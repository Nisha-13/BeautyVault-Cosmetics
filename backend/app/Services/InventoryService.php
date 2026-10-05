<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\OrderBatchAllocation;
use App\Models\OrderItem;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Pure FIFO Batch Allocation
     * Scans unexpired active batches ordered by earliest expiry date and allocates quantities.
     */
    public function allocateBatchesFifo(ProductVariant $variant, int $qtyRequested): array
    {
        return DB::transaction(function () use ($variant, $qtyRequested) {
            $batches = ProductBatch::where('product_variant_id', $variant->id)
                ->where('status', 'active')
                ->where('current_quantity', '>', 0)
                ->where('expires_at', '>', now()->toDateString())
                ->orderBy('expires_at', 'asc')
                ->lockForUpdate()
                ->get();

            $totalBatchAvailable = $batches->sum('current_quantity');
            if ($totalBatchAvailable < $qtyRequested) {
                throw new Exception("Batch allocation failed: unexpired viable batches have {$totalBatchAvailable} units, requested {$qtyRequested}.");
            }

            $remainingQtyToDeduct = $qtyRequested;
            $allocations = [];

            foreach ($batches as $batch) {
                if ($remainingQtyToDeduct <= 0) {
                    break;
                }

                $deductFromThisBatch = min($batch->current_quantity, $remainingQtyToDeduct);
                $batch->current_quantity -= $deductFromThisBatch;
                if ($batch->current_quantity === 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();

                $allocations[] = [
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'quantity' => $deductFromThisBatch,
                    'quantity_allocated' => $deductFromThisBatch,
                ];

                $remainingQtyToDeduct -= $deductFromThisBatch;
            }

            return $allocations;
        });
    }

    /**
     * FIFO Batch Deduction with Database Pessimistic Locking
     * Ensures strict concurrency safety and prevents overselling
     */
    public function allocateAndDeductStock(
        ProductVariant $variant,
        int $qtyRequested,
        ?OrderItem $orderItem = null,
        ?User $user = null,
        string $reason = 'Order Fulfillment Checkout',
        string $referenceType = 'Order',
        ?int $referenceId = null
    ): array {
        // Enforce within existing transaction or create one
        return DB::transaction(function () use (
            $variant,
            $qtyRequested,
            $orderItem,
            $user,
            $reason,
            $referenceType,
            $referenceId
        ) {
            // 1. Lock the ProductVariant row for update
            /** @var ProductVariant $lockedVariant */
            $lockedVariant = ProductVariant::where('id', $variant->id)->lockForUpdate()->firstOrFail();

            if ($lockedVariant->stock_quantity < $qtyRequested) {
                throw new Exception("Insufficient stock for SKU [{$lockedVariant->sku}]. Available: {$lockedVariant->stock_quantity}, requested: {$qtyRequested}");
            }

            // 2. Fetch active, unexpired batches ordered by earliest expiry date (FIFO) with row-level locks
            $batches = ProductBatch::where('product_variant_id', $lockedVariant->id)
                ->where('status', 'active')
                ->where('current_quantity', '>', 0)
                ->where('expires_at', '>', now()->toDateString())
                ->orderBy('expires_at', 'asc')
                ->lockForUpdate()
                ->get();

            $totalBatchAvailable = $batches->sum('current_quantity');
            if ($totalBatchAvailable < $qtyRequested) {
                throw new Exception("Batch allocation failed: unexpired viable batches have {$totalBatchAvailable} units, requested {$qtyRequested}.");
            }

            $remainingQtyToDeduct = $qtyRequested;
            $allocations = [];

            // 3. Deduct according to FIFO lot hierarchy
            foreach ($batches as $batch) {
                if ($remainingQtyToDeduct <= 0) {
                    break;
                }

                $deductFromThisBatch = min($batch->current_quantity, $remainingQtyToDeduct);
                $batchPrevQty = $batch->current_quantity;
                $batchNewQty = $batchPrevQty - $deductFromThisBatch;

                $batch->current_quantity = $batchNewQty;
                if ($batchNewQty === 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();

                // Record Order Batch link if item present
                if ($orderItem) {
                    OrderBatchAllocation::create([
                        'order_item_id' => $orderItem->id,
                        'product_batch_id' => $batch->id,
                        'allocated_quantity' => $deductFromThisBatch,
                    ]);
                }

                $allocations[] = [
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'expires_at' => $batch->expires_at->toDateString(),
                    'quantity_allocated' => $deductFromThisBatch,
                ];

                $remainingQtyToDeduct -= $deductFromThisBatch;
            }

            // 4. Update Variant overall stock cache
            $prevStock = $lockedVariant->stock_quantity;
            $newStock = $prevStock - $qtyRequested;
            $lockedVariant->stock_quantity = $newStock;
            $lockedVariant->save();

            // 5. Audit Log Entry
            InventoryLog::create([
                'product_variant_id' => $lockedVariant->id,
                'product_batch_id' => $allocations[0]['batch_id'] ?? null,
                'user_id' => $user?->id,
                'change_type' => 'order_deduction',
                'quantity_change' => -$qtyRequested,
                'previous_quantity' => $prevStock,
                'new_quantity' => $newStock,
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            return [
                'sku' => $lockedVariant->sku,
                'quantity_deducted' => $qtyRequested,
                'remaining_stock' => $newStock,
                'allocations' => $allocations,
            ];
        });
    }

    /**
     * Restock a variant with a new batch lot
     */
    public function restockBatch(ProductVariant $variant, array $data, ?User $user = null): ProductBatch
    {
        return DB::transaction(function () use ($variant, $data, $user) {
            $batch = ProductBatch::create([
                'product_variant_id' => $variant->id,
                'batch_number' => $data['batch_number'],
                'supplier_name' => $data['supplier_name'] ?? 'Authorized Supplier',
                'manufactured_at' => $data['manufactured_at'] ?? now()->toDateString(),
                'expires_at' => $data['expires_at'],
                'initial_quantity' => $data['quantity'],
                'current_quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'] ?? 0,
                'status' => 'active',
                'warehouse_location' => $data['warehouse_location'] ?? 'General Warehouse',
            ]);

            $lockedVariant = ProductVariant::where('id', $variant->id)->lockForUpdate()->firstOrFail();
            $prevStock = $lockedVariant->stock_quantity;
            $newStock = $prevStock + (int) $data['quantity'];
            $lockedVariant->stock_quantity = $newStock;
            $lockedVariant->save();

            InventoryLog::create([
                'product_variant_id' => $lockedVariant->id,
                'product_batch_id' => $batch->id,
                'user_id' => $user?->id,
                'change_type' => 'restock',
                'quantity_change' => (int) $data['quantity'],
                'previous_quantity' => $prevStock,
                'new_quantity' => $newStock,
                'reason' => 'Inventory restock: Batch ' . $batch->batch_number,
                'reference_type' => 'ProductBatch',
                'reference_id' => $batch->id,
            ]);

            return $batch;
        });
    }

    /**
     * Get batches approaching expiration for pro-active alerts
     */
    public function getExpiringBatches(int $withinDays = 60): Collection
    {
        return ProductBatch::with(['variant.product.brand'])
            ->expiringSoon($withinDays)
            ->orderBy('expires_at', 'asc')
            ->get();
    }

    /**
     * Get variants at or below low-stock threshold
     */
    public function getLowStockVariants(): Collection
    {
        return ProductVariant::with(['product.brand'])
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->get();
    }
}

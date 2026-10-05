<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductBatch;
use App\Models\Category;
use App\Models\Brand;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $inventoryService;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = app(InventoryService::class);

        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare']);
        $brand    = Brand::create(['name' => 'The Ordinary', 'slug' => 'the-ordinary']);
        $product  = Product::create([
            'category_id' => $category->id,
            'brand_id'    => $brand->id,
            'name'        => 'Niacinamide 10% + Zinc 1%',
            'slug'        => 'niacinamide-10-zinc-1',
            'base_price'  => 12.00,
            'is_active'   => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id'     => $product->id,
            'sku'            => 'NMD-30ML',
            'title'          => '30ml Bottle',
            'shade_name'     => '30ml',
            'price'          => 12.00,
            'stock_quantity' => 0,
        ]);
    }

    /** @test */
    public function fifo_batch_allocation_uses_oldest_batch_first()
    {
        // Create two batches — oldest first
        $oldBatch = ProductBatch::create([
            'product_variant_id'  => $this->variant->id,
            'batch_number'        => 'BATCH-2024-01',
            'initial_quantity'    => 30,
            'current_quantity'    => 30,
            'manufactured_at'     => now()->subMonths(6),
            'expires_at'          => now()->addMonths(6),
            'unit_cost'           => 4.00,
            'status'              => 'active',
        ]);

        $newBatch = ProductBatch::create([
            'product_variant_id'  => $this->variant->id,
            'batch_number'        => 'BATCH-2024-02',
            'initial_quantity'    => 50,
            'current_quantity'    => 50,
            'manufactured_at'     => now()->subMonths(3),
            'expires_at'          => now()->addMonths(18),
            'unit_cost'           => 4.00,
            'status'              => 'active',
        ]);

        // Allocate 25 units — should come entirely from old batch
        $allocations = $this->inventoryService->allocateBatchesFifo($this->variant, 25);

        $this->assertCount(1, $allocations);
        $this->assertEquals($oldBatch->id, $allocations[0]['batch_id']);
        $this->assertEquals(25, $allocations[0]['quantity']);
    }

    /** @test */
    public function fifo_spans_multiple_batches_when_needed()
    {
        $batch1 = ProductBatch::create([
            'product_variant_id' => $this->variant->id,
            'batch_number'       => 'BATCH-A',
            'initial_quantity'   => 10,
            'current_quantity'   => 10,
            'manufactured_at'    => now()->subMonths(8),
            'expires_at'         => now()->addMonths(4),
            'unit_cost'          => 4.00,
            'status'             => 'active',
        ]);

        $batch2 = ProductBatch::create([
            'product_variant_id' => $this->variant->id,
            'batch_number'       => 'BATCH-B',
            'initial_quantity'   => 20,
            'current_quantity'   => 20,
            'manufactured_at'    => now()->subMonths(4),
            'expires_at'         => now()->addMonths(10),
            'unit_cost'          => 4.00,
            'status'             => 'active',
        ]);

        // Need 15 units — 10 from batch1 (FIFO), 5 from batch2
        $allocations = $this->inventoryService->allocateBatchesFifo($this->variant, 15);

        $this->assertCount(2, $allocations);
        $this->assertEquals($batch1->id, $allocations[0]['batch_id']);
        $this->assertEquals(10, $allocations[0]['quantity']);
        $this->assertEquals($batch2->id, $allocations[1]['batch_id']);
        $this->assertEquals(5, $allocations[1]['quantity']);
    }

    /** @test */
    public function insufficient_stock_throws_exception()
    {
        // No batches created — should throw
        $this->expectException(\Exception::class);

        $this->inventoryService->allocateBatchesFifo($this->variant, 10);
    }

    /** @test */
    public function expired_batches_are_excluded_from_allocation()
    {
        // Expired batch
        ProductBatch::create([
            'product_variant_id' => $this->variant->id,
            'batch_number'       => 'BATCH-EXPIRED',
            'initial_quantity'   => 100,
            'current_quantity'   => 100,
            'manufactured_at'    => now()->subYears(2),
            'expires_at'         => now()->subDay(), // EXPIRED
            'unit_cost'          => 4.00,
            'status'             => 'active',
        ]);

        $this->expectException(\Exception::class);

        $this->inventoryService->allocateBatchesFifo($this->variant, 5);
    }
}

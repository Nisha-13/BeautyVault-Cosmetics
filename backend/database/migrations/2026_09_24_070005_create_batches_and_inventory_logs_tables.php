<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->index();
            $table->string('supplier_name')->nullable();
            $table->date('manufactured_at')->nullable();
            $table->date('expires_at')->index();
            $table->unsignedInteger('initial_quantity');
            $table->unsignedInteger('current_quantity')->default(0)->index();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->enum('status', ['active', 'quarantined', 'expired', 'depleted'])->default('active')->index();
            $table->string('warehouse_location')->nullable();
            $table->timestamps();

            // Compound index for high-speed FIFO batch allocation: finding oldest unexpired active batch
            $table->index(['product_variant_id', 'status', 'expires_at']);
        });

        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('change_type', [
                'restock',
                'order_deduction',
                'adjustment',
                'return_restock',
                'damage_writeoff',
                'expired_writeoff'
            ])->index();
            $table->integer('quantity_change');
            $table->unsignedInteger('previous_quantity');
            $table->unsignedInteger('new_quantity');
            $table->string('reason')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index(['product_variant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('product_batches');
    }
};

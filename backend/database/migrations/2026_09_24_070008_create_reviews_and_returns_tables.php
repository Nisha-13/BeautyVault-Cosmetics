<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('review_text');
            
            // Cosmetics-specific social proof
            $table->boolean('verified_purchase')->default(false)->index();
            $table->string('reviewer_skin_type')->nullable();
            $table->string('reviewer_undertone')->nullable();
            $table->string('purchased_shade')->nullable();
            
            $table->json('photos')->nullable();
            $table->unsignedInteger('helpful_votes')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->index();
            $table->timestamps();

            $table->index(['product_id', 'status', 'rating']);
        });

        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->enum('status', [
                'requested',
                'approved',
                'rejected',
                'in_transit',
                'received',
                'refunded'
            ])->default('requested')->index();

            $table->enum('reason_code', [
                'wrong_shade',
                'skin_reaction',
                'damaged_in_transit',
                'defective_packaging',
                'changed_mind'
            ])->index();

            $table->text('explanation')->nullable();
            $table->decimal('refund_amount', 10, 2);
            $table->enum('refund_method', ['original_payment', 'store_credit'])->default('original_payment');
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('condition', ['unopened', 'opened_tested', 'damaged'])->default('unopened');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('reviews');
    }
};

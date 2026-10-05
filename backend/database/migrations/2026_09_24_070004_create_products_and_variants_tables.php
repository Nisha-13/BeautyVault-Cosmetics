<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->text('how_to_use')->nullable();
            $table->longText('full_ingredients_text')->nullable();
            
            // Beauty specific diagnostic filters
            $table->json('target_skin_types')->nullable()->comment('dry, oily, combination, sensitive, normal');
            $table->json('target_concerns')->nullable()->comment('acne, wrinkles, dullness, hyperpigmentation');
            $table->string('finish_type')->nullable()->comment('matte, dewy, satin, radiant, natural');
            $table->string('coverage_level')->nullable()->comment('sheer, light, medium, full, buildable');
            $table->unsignedSmallInteger('spf_rating')->nullable();
            $table->unsignedSmallInteger('pao_months')->default(12)->comment('Period After Opening in months');
            
            // Financial & Status
            $table->decimal('base_price', 10, 2)->index();
            $table->decimal('discount_price', 10, 2)->nullable()->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_bestseller')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            
            // Rating aggregates
            $table->decimal('rating_avg', 3, 2)->default(0.00)->index();
            $table->unsignedInteger('reviews_count')->default(0);

            // Media
            $table->string('thumbnail_url')->nullable();
            $table->json('gallery_images')->nullable();

            $table->timestamps();
        });

        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('concentration_percentage', 5, 2)->nullable()->comment('e.g. 10.00 for 10% Niacinamide');
            $table->boolean('is_key_active')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'ingredient_id']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->index();
            $table->string('title');
            
            // Shade management
            $table->string('shade_name')->nullable()->index();
            $table->string('shade_hex', 7)->nullable()->comment('#RRGGBB');
            $table->unsignedTinyInteger('depth_level')->nullable()->comment('1: Fair to 10: Deep')->index();
            $table->enum('undertone', ['cool', 'warm', 'neutral', 'olive', 'peach'])->nullable()->index();
            
            $table->string('volume_or_weight')->nullable()->comment('e.g. 30 ml or 50g');
            $table->decimal('price', 10, 2)->index();
            $table->decimal('compare_at_price', 10, 2)->nullable();
            
            $table->unsignedInteger('stock_quantity')->default(0)->index();
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->boolean('is_active')->default(true)->index();
            $table->string('image_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_ingredients');
        Schema::dropIfExists('products');
    }
};

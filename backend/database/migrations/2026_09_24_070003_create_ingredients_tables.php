<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('inci_name')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('comedogenic_rating')->default(0)->comment('0: Non-comedogenic to 5: Highly pore-clogging');
            $table->unsignedTinyInteger('ewg_safety_score')->default(1)->comment('1: Low hazard to 10: High hazard');
            $table->string('function_type')->nullable()->comment('Antioxidant, Humectant, Exfoliant, Peptide, etc.');
            $table->json('benefits')->nullable()->comment('array of benefits: soothing, brightening, etc.');
            $table->boolean('is_active_ingredient')->default(false)->index();
            $table->boolean('is_potential_irritant')->default(false);
            $table->boolean('safe_for_fungal_acne')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};

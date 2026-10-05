<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['admin', 'manager', 'customer'])->default('customer')->after('phone')->index();
            $table->string('avatar_url')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('avatar_url');
        });

        Schema::create('user_skin_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('skin_type', ['normal', 'dry', 'oily', 'combination', 'sensitive'])->nullable();
            $table->enum('undertone', ['cool', 'warm', 'neutral', 'olive'])->nullable();
            $table->unsignedTinyInteger('shade_depth')->nullable()->comment('1: Fair to 10: Deep');
            $table->json('concerns')->nullable()->comment('acne, wrinkles, hyperpigmentation, redness, etc.');
            $table->json('allergies')->nullable()->comment('fragrance, parabens, sulfates, etc.');
            $table->string('hair_type')->nullable()->comment('straight, wavy, curly, coily');
            $table->string('hair_porosity')->nullable()->comment('low, medium, high');
            $table->timestamps();
        });

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Home');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('street_address');
            $table->string('apartment_or_suite')->nullable();
            $table->string('city');
            $table->string('state_or_province');
            $table->string('postal_code');
            $table->string('country')->default('US');
            $table->boolean('is_default_shipping')->default(false);
            $table->boolean('is_default_billing')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('user_skin_profiles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'avatar_url', 'is_active']);
        });
    }
};

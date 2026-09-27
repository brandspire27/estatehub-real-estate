<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cities table
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('state')->nullable();
            $table->text('image_url')->nullable();
            $table->timestamps();
        });

        // 2. Properties table
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 15, 2)->index();
            $table->string('property_type', 50)->index(); // Flat, Apartment, Villa, Plot, Building, Commercial Space, Land
            $table->unsignedTinyInteger('bhk')->nullable()->index();
            $table->unsignedInteger('area_sqft');
            $table->string('city', 100)->index();
            $table->string('area', 150); // Locality / Suburb
            $table->string('possession_status', 50)->default('Ready to Move'); // Ready to Move, Under Construction
            $table->string('status', 30)->default('DRAFT')->index(); // DRAFT, PENDING_VERIFICATION, PUBLISHED, SOLD, EXPIRED, REMOVED
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->index(['status', 'city']);
            $table->index(['status', 'property_type']);
        });

        // 3. Property Images table
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->text('image_url');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['property_id', 'sort_order']);
        });

        // 4. Buyer Requirements table
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('property_type', 50)->index();
            $table->unsignedTinyInteger('bhk')->nullable();
            $table->decimal('budget_min', 15, 2)->index();
            $table->decimal('budget_max', 15, 2)->index();
            $table->string('possession_status', 50)->nullable();
            $table->string('city', 100)->index();
            $table->text('description')->nullable();
            $table->string('status', 30)->default('ACTIVE')->index(); // ACTIVE, MATCHED, CLOSED, EXPIRED
            $table->timestamps();

            $table->index(['status', 'city']);
        });

        // 5. Matches table
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_id')->constrained('requirements')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->index(); // 0 to 100
            $table->timestamps();

            $table->unique(['requirement_id', 'property_id']);
        });

        // 6. Activity Logs table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100)->index();
            $table->string('entity_type', 100)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('requirements');
        Schema::dropIfExists('property_images');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('cities');
    }
};

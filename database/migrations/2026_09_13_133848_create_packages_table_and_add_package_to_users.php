<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('billing_period')->default('monthly'); // monthly, quarterly, yearly, lifetime
            $table->integer('duration_days')->default(30);
            $table->integer('listing_limit')->default(3); // -1 for unlimited
            $table->integer('featured_limit')->default(1);
            $table->integer('contact_unlock_limit')->default(10);
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->string('badge')->nullable(); // e.g. Popular, Recommended, Best Value
            $table->string('target_role')->default('SELLER'); // SELLER, BUYER, ALL
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('avatar')->constrained('packages')->nullOnDelete();
            $table->dateTime('package_expires_at')->nullable()->after('package_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn(['package_id', 'package_expires_at']);
        });

        Schema::dropIfExists('packages');
    }
};

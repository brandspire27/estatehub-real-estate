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
        Schema::table('cities', function (Blueprint $table) {
            $table->boolean('is_popular')->default(true)->index()->after('image_url');
            $table->unsignedInteger('sort_order')->default(0)->after('is_popular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex(['is_popular']);
            $table->dropColumn(['is_popular', 'sort_order']);
        });
    }
};

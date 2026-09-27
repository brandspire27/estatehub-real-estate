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
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status', 30)->default('UNVERIFIED')->after('is_active');
            $table->string('kyc_document_type', 50)->nullable()->after('kyc_status');
            $table->string('kyc_document_number', 100)->nullable()->after('kyc_document_type');
            $table->text('kyc_document_url')->nullable()->after('kyc_document_number');
            $table->text('address')->nullable()->after('kyc_document_url');
            $table->string('emergency_contact', 50)->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_status',
                'kyc_document_type',
                'kyc_document_number',
                'kyc_document_url',
                'address',
                'emergency_contact',
            ]);
        });
    }
};

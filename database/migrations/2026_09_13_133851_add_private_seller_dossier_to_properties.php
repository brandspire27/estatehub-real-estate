<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('kyc_document_type')->nullable()->after('youtube_url');
            $table->string('kyc_document_number')->nullable()->after('kyc_document_type');
            $table->text('kyc_document_url')->nullable()->after('kyc_document_number');
            $table->text('private_remarks')->nullable()->after('kyc_document_url');
            $table->json('private_photos')->nullable()->after('private_remarks');
            $table->json('private_videos')->nullable()->after('private_photos');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_document_type',
                'kyc_document_number',
                'kyc_document_url',
                'private_remarks',
                'private_photos',
                'private_videos',
            ]);
        });
    }
};

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
        Schema::create('about_page_settings', function (Blueprint $table) {
            $table->id();

            // Hero Section
            $table->string('hero_badge')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->json('hero_stats')->nullable();

            // Section 1: The Problem vs Solution Story
            $table->string('challenge_tagline')->nullable();
            $table->string('challenge_title')->nullable();
            $table->text('challenge_content_1')->nullable();
            $table->text('challenge_content_2')->nullable();
            $table->text('challenge_content_3')->nullable();
            $table->string('traditional_title')->nullable();
            $table->json('traditional_points')->nullable();
            $table->string('solution_title')->nullable();
            $table->json('solution_points')->nullable();

            // Section 2: 100-Point Match Algorithm Engine
            $table->string('algorithm_tagline')->nullable();
            $table->string('algorithm_title')->nullable();
            $table->text('algorithm_subtitle')->nullable();
            $table->json('algorithm_vectors')->nullable();

            // Section 3: Verified Standards
            $table->json('standards_cards')->nullable();

            // Section 4: Buyer & Seller Advantages
            $table->string('buyer_title')->nullable();
            $table->json('buyer_points')->nullable();
            $table->string('seller_title')->nullable();
            $table->json('seller_points')->nullable();

            // Section 5: 6-Step Verification Protocol
            $table->string('verification_tagline')->nullable();
            $table->string('verification_title')->nullable();
            $table->text('verification_subtitle')->nullable();
            $table->json('verification_steps')->nullable();

            // Section 6: FAQs
            $table->string('faq_tagline')->nullable();
            $table->string('faq_title')->nullable();
            $table->text('faq_subtitle')->nullable();
            $table->json('faqs')->nullable();

            // Section 7: Call to Action Banner
            $table->string('cta_title')->nullable();
            $table->text('cta_subtitle')->nullable();
            $table->string('cta_btn1_text')->nullable();
            $table->string('cta_btn1_url')->nullable();
            $table->string('cta_btn2_text')->nullable();
            $table->string('cta_btn2_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_page_settings');
    }
};

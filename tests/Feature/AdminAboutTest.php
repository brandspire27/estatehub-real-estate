<?php

namespace Tests\Feature;

use App\Models\AboutPageSetting;
use App\Models\User;
use Tests\TestCase;

class AdminAboutTest extends TestCase
{
    /**
     * Test authorization for Admin About Page editor.
     */
    public function test_admin_about_editor_requires_admin_role(): void
    {
        // 1. Guest redirected to login
        $this->get('/admin/about')->assertRedirect('/login');

        // 2. Buyer gets 403 Forbidden
        $buyer = User::where('role', 'BUYER')->first() ?? User::create([
            'name' => 'Buyer Tester',
            'email' => 'buyer_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '98' . rand(10000000, 99999999),
            'role' => 'BUYER',
            'is_active' => true,
        ]);
        $this->actingAs($buyer)->get('/admin/about')->assertStatus(403);

        // 3. Admin gets 200 OK
        $admin = User::where('role', 'ADMIN')->first() ?? User::create([
            'name' => 'Admin Tester',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '98' . rand(10000000, 99999999),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);
        $response = $this->actingAs($admin)->get('/admin/about');
        $response->assertStatus(200);
        $response->assertSee('Public About Page Editor');
        $response->assertSee('hero_badge');
        $response->assertSee('Save All Changes');
    }

    /**
     * Test admin can update About page and verify updates on public page.
     */
    public function test_admin_can_update_about_page_and_see_changes_on_public_page(): void
    {
        $admin = User::where('role', 'ADMIN')->first() ?? User::create([
            'name' => 'Admin Tester',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '98' . rand(10000000, 99999999),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        $customHeroTitle = 'Custom Architectural Matchmaking in India ' . rand(100, 999);
        $customFaqQuestion = 'Is EstateHub available in Tier-2 cities? ' . rand(100, 999);
        $customFaqAnswer = 'Yes, we have expanded coverage to Guwahati, Kochi, and Indore.';
        $customCtaTitle = 'Find Your Dream Luxury Home Today ' . rand(100, 999);

        $payload = [
            'hero_badge' => 'India\'s Top Real Estate Tech',
            'hero_title' => $customHeroTitle,
            'hero_subtitle' => 'Zero spam, zero ghost listings, direct verified matchmaking.',
            'hero_stats' => [
                ['value' => '15,000+', 'label' => 'Verified Homes', 'color' => 'text-brand-400'],
                ['value' => '99.1%', 'label' => 'AI Accuracy', 'color' => 'text-emerald-400'],
                ['value' => '60,000+', 'label' => 'Happy Buyers', 'color' => 'text-indigo-400'],
                ['value' => '₹0', 'label' => 'Brokerage Overhead', 'color' => 'text-amber-400'],
            ],
            'challenge_tagline' => 'Disrupting Outdated Classifieds',
            'challenge_title' => 'Why Real Estate Portals Have Failed',
            'challenge_content_1' => 'Old portals monetize broker spam and leak numbers.',
            'challenge_content_2' => 'Sellers get hundreds of unqualified calls.',
            'challenge_content_3' => 'EstateHub fixes this with automated algorithm scoring.',
            'traditional_title' => 'The Broken Model',
            'traditional_points' => [
                'Unverified spam',
                'Aggressive telemarketing',
            ],
            'solution_title' => 'The EstateHub Fix',
            'solution_points' => [
                'Admin verified listings',
                'Two-way phone privacy mask',
            ],
            'algorithm_tagline' => 'Smart Matching',
            'algorithm_title' => 'The 100-Point Formula',
            'algorithm_subtitle' => 'Our proprietary formula evaluates compatibility instantly.',
            'algorithm_vectors' => [
                [
                    'icon' => 'fa-solid fa-location-dot',
                    'color' => 'brand',
                    'points' => '25 Pts',
                    'title' => 'Micro-Location',
                    'desc' => 'Neighborhood accuracy.',
                    'badge' => 'Location Strict',
                ],
            ],
            'standards_cards' => [
                [
                    'icon' => 'fa-solid fa-shield-halved',
                    'color' => 'brand',
                    'title' => '100% Title Checked',
                    'desc' => 'Verified ownership documents.',
                ],
            ],
            'buyer_title' => 'For Buyers',
            'buyer_points' => [
                ['title' => 'Direct Calling', 'desc' => 'No middleman.'],
            ],
            'seller_title' => 'For Sellers',
            'seller_points' => [
                ['title' => 'Zero Spam', 'desc' => 'Only qualified leads.'],
            ],
            'verification_tagline' => 'Our Standards',
            'verification_title' => '6-Step Editorial Review',
            'verification_subtitle' => 'Every property undergoes manual review.',
            'verification_steps' => [
                ['step' => 1, 'title' => 'KYC Check', 'desc' => 'Identity validated.'],
            ],
            'faq_tagline' => 'Clarifications',
            'faq_title' => 'Frequently Asked Questions',
            'faq_subtitle' => 'Everything you need to know.',
            'faqs' => [
                [
                    'question' => $customFaqQuestion,
                    'answer' => $customFaqAnswer,
                ],
            ],
            'cta_title' => $customCtaTitle,
            'cta_subtitle' => 'Join thousands of satisfied property owners and buyers.',
            'cta_btn1_text' => 'Browse Villas',
            'cta_btn1_url' => '/properties',
            'cta_btn2_text' => 'Post Requirement',
            'cta_btn2_url' => '/requirements/create',
        ];

        $updateResponse = $this->actingAs($admin)->put('/admin/about', $payload);

        $updateResponse->assertRedirect('/admin/about');
        $updateResponse->assertSessionHas('success');

        // Verify the database has the new values
        $setting = AboutPageSetting::first();
        $this->assertEquals($customHeroTitle, $setting->hero_title);
        $this->assertEquals('15,000+', $setting->hero_stats[0]['value']);

        // Verify public /about reflects the new content
        $publicResponse = $this->get('/about');
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee($customHeroTitle);
        $publicResponse->assertSee('15,000+');
        $publicResponse->assertSee('Verified Homes');
        $publicResponse->assertSee($customFaqQuestion);
        $publicResponse->assertSee($customFaqAnswer);
        $publicResponse->assertSee($customCtaTitle);
    }
}

<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Property;
use App\Models\SeoMeta;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoEngineTest extends TestCase
{
    /**
     * Test dynamic XML sitemap endpoint.
     */
    public function test_sitemap_xml_renders_valid_xml_with_pages_and_properties(): void
    {
        $seller = User::where('role', 'SELLER')->first() ?? User::create([
            'name' => 'Sitemap Seller',
            'email' => 'seller_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '99' . rand(10000000, 99999999),
            'role' => 'SELLER',
            'is_active' => true,
        ]);

        // Seed an approved/published property
        $property = Property::create([
            'user_id' => $seller->id,
            'created_by_user_id' => $seller->id,
            'title' => 'Sitemap Luxury Villa ' . rand(1000, 9999),
            'description' => 'Spectacular skyline views with private pool and premium fittings.',
            'price' => 12500000,
            'property_type' => 'Flat',
            'bhk' => 3,
            'area_sqft' => 2100,
            'city' => 'Mumbai',
            'area' => 'Bandra West',
            'possession_status' => 'Ready to Move',
            'status' => 'PUBLISHED',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        
        $content = $response->getContent();
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString(url('/'), $content);
        $this->assertStringContainsString(url('/properties'), $content);
        $this->assertStringContainsString(url('/about'), $content);
        $this->assertStringContainsString(url('/contact'), $content);
        $this->assertStringContainsString($property->title, $content);
    }

    /**
     * Test /llms.txt AI discovery endpoint.
     */
    public function test_llms_txt_endpoint_returns_structured_markdown(): void
    {
        $response = $this->get('/llms.txt');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));
        
        $content = $response->getContent();
        $this->assertStringContainsString('# EstateHub — Verified Real Estate Marketplace', $content);
        $this->assertStringContainsString('## Core Public Endpoints', $content);
        $this->assertStringContainsString('/sitemap.xml', $content);
        $this->assertStringContainsString('/llms-full.txt', $content);
    }

    /**
     * Test /llms-full.txt comprehensive AI discovery endpoint.
     */
    public function test_llms_full_txt_endpoint_returns_detailed_context(): void
    {
        $response = $this->get('/llms-full.txt');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));

        $content = $response->getContent();
        $this->assertStringContainsString('# EstateHub — Full Published Property Inventory & Detailed Knowledge Base', $content);
        $this->assertStringContainsString('## Active Property Listings', $content);
        $this->assertStringContainsString('API Integration Note', $content);
    }

    /**
     * Test robots.txt contains sitemap and llms references.
     */
    public function test_robots_txt_contains_sitemap_and_llms_references(): void
    {
        $robotsPath = public_path('robots.txt');
        $this->assertFileExists($robotsPath);

        $content = file_get_contents($robotsPath);
        $this->assertStringContainsString('sitemap.xml', $content);
        $this->assertStringContainsString('llms.txt', $content);
        $this->assertStringContainsString('Disallow: /admin/', $content);
    }

    /**
     * Test frontend page renders dynamic SEO meta tags and JSON-LD schema.
     */
    public function test_homepage_renders_dynamic_seo_tags_and_json_ld(): void
    {
        SeoMeta::updateOrCreate(
            ['page_key' => 'home'],
            [
                'page_name' => 'Home Page',
                'meta_title' => 'Custom Automated Home Title | EstateHub',
                'meta_description' => 'Custom test meta description for the home page search snippet.',
                'meta_keywords' => 'custom, test, keywords',
                'canonical_url' => url('/'),
                'is_indexed' => true,
                'is_followed' => true,
                'og_title' => 'Custom OG Home Title',
                'og_description' => 'Custom OG Description',
                'twitter_card' => 'summary_large_image',
            ]
        );

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Custom Automated Home Title | EstateHub');
        $response->assertSee('Custom test meta description for the home page search snippet.');
        $response->assertSee('custom, test, keywords');
        $response->assertSee('Custom OG Home Title');
        $response->assertSee('summary_large_image');
        $response->assertSee('application/ld+json', false);
    }

    /**
     * Test admin SEO suite security and authorization.
     */
    public function test_admin_seo_suite_requires_admin_authorization(): void
    {
        // Guest
        $this->get('/admin/seo')->assertRedirect('/login');

        // Normal Buyer User
        $buyer = User::where('role', 'BUYER')->first() ?? User::create([
            'name' => 'Buyer Tester',
            'email' => 'buyer_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '99' . rand(10000000, 99999999),
            'role' => 'BUYER',
            'is_active' => true,
        ]);
        $this->actingAs($buyer)->get('/admin/seo')->assertStatus(403);

        // Admin User
        $admin = User::where('role', 'ADMIN')->first() ?? User::create([
            'name' => 'Admin Tester',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '99' . rand(10000000, 99999999),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);
        $response = $this->actingAs($admin)->get('/admin/seo');
        $response->assertStatus(200);
        $response->assertSee('Search Engine Optimization &amp; Metadata Suite', false);
    }

    /**
     * Test admin can view and update an SEO record with SERP parameters.
     */
    public function test_admin_can_update_seo_record(): void
    {
        $admin = User::where('role', 'ADMIN')->first() ?? User::create([
            'name' => 'Admin Tester',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '99' . rand(10000000, 99999999),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        $seoRecord = SeoMeta::firstOrCreate(
            ['page_key' => 'about'],
            [
                'page_name' => 'About Us',
                'meta_title' => 'Old About Title',
                'meta_description' => 'Old About Description',
                'is_indexed' => true,
                'is_followed' => true,
            ]
        );

        // View Edit Page
        $editResponse = $this->actingAs($admin)->get("/admin/seo/{$seoRecord->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Google SERP Live Preview');

        // Update Record
        $updateResponse = $this->actingAs($admin)->put("/admin/seo/{$seoRecord->id}", [
            'page_name' => 'About EstateHub Platform',
            'meta_title' => 'About EstateHub — 100-Point Real Estate Matchmaking',
            'meta_description' => 'Learn how EstateHub pairs certified sellers with active property buyers.',
            'meta_keywords' => 'about estatehub, matchmaking, verified sellers',
            'canonical_url' => url('/about'),
            'is_indexed' => '1',
            'is_followed' => '1',
            'og_title' => 'About EstateHub Marketplace',
            'og_description' => 'Learn how EstateHub pairs certified sellers with active property buyers.',
            'twitter_card' => 'summary_large_image',
            'json_ld' => '{"@context":"https://schema.org","@type":"AboutPage"}',
        ]);

        $updateResponse->assertRedirect(route('admin.seo.index'));

        $this->assertDatabaseHas('seo_metas', [
            'id' => $seoRecord->id,
            'meta_title' => 'About EstateHub — 100-Point Real Estate Matchmaking',
            'is_indexed' => true,
        ]);
    }

    /**
     * Test admin can create a new custom page SEO record.
     */
    public function test_admin_can_create_custom_page_seo(): void
    {
        $admin = User::where('role', 'ADMIN')->first() ?? User::create([
            'name' => 'Admin Tester',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '99' . rand(10000000, 99999999),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        // Clean up if careers exists
        SeoMeta::where('page_key', 'careers')->delete();

        $response = $this->actingAs($admin)->post('/admin/seo', [
            'page_key' => 'careers',
            'page_name' => 'Careers & Jobs',
            'meta_title' => 'Join the EstateHub Team | Careers & Openings',
            'meta_description' => 'Explore careers at EstateHub. We are hiring engineers, matchmakers, and real estate associates.',
            'canonical_url' => url('/careers'),
            'is_indexed' => '1',
            'is_followed' => '1',
            'twitter_card' => 'summary_large_image',
        ]);

        $response->assertRedirect(route('admin.seo.index'));

        $this->assertDatabaseHas('seo_metas', [
            'page_key' => 'careers',
            'meta_title' => 'Join the EstateHub Team | Careers & Openings',
        ]);
    }

    /**
     * Test properties page locality filtering by city and ensure no JS attribute leakage.
     */
    public function test_properties_page_renders_cleanly_without_js_leakage_and_filters_localities_by_city(): void
    {
        $response = $this->get('/properties');
        $response->assertStatus(200);

        $content = $response->getContent();

        // 1. Verify clean Alpine component binding
        $this->assertStringContainsString('x-data="propertiesExplorer()"', $content);

        // 2. Verify NO raw JS text leakage in HTML outside <script>
        $htmlWithoutScripts = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        $this->assertStringNotContainsString('setAreaRange(min, max)', $htmlWithoutScripts);
        $this->assertStringNotContainsString('updateLocalities()', $htmlWithoutScripts);
        $this->assertStringNotContainsString('sel.innerHTML', $htmlWithoutScripts);

        // 3. Verify server-side rendered locality dropdown when city is selected (Mumbai)
        $mumbaiResponse = $this->get('/properties?city=Mumbai');
        $mumbaiResponse->assertStatus(200);
        $mumbaiContent = $mumbaiResponse->getContent();
        $this->assertStringContainsString('All Localities in Mumbai (Without Locality)', $mumbaiContent);
        $this->assertStringContainsString('Bandra West', $mumbaiContent);

        // 4. Verify server-side rendered locality dropdown when city is selected (Guwahati)
        $guwahatiResponse = $this->get('/properties?city=Guwahati');
        $guwahatiResponse->assertStatus(200);
        $guwahatiContent = $guwahatiResponse->getContent();
        $this->assertStringContainsString('All Localities in Guwahati (Without Locality)', $guwahatiContent);
        $this->assertStringContainsString('Beltola', $guwahatiContent);
    }

    /**
     * Test admin can upload social share (OG) image for SEO configuration.
     */
    public function test_admin_can_upload_og_image_for_seo_record(): void
    {
        Storage::fake('public');

        $admin = User::where('role', 'ADMIN')->first() ?? User::create([
            'name' => 'Admin Tester',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'phone' => '99' . rand(10000000, 99999999),
            'role' => 'ADMIN',
            'is_active' => true,
        ]);

        $seoRecord = SeoMeta::firstOrCreate(
            ['page_key' => 'home'],
            [
                'page_name' => 'Home Page',
                'meta_title' => 'Home Title',
            ]
        );

        $fakeImage = UploadedFile::fake()->image('custom_og_banner.jpg', 1200, 630);

        $response = $this->actingAs($admin)->put("/admin/seo/{$seoRecord->id}", [
            'page_name' => 'Home Page Updated',
            'meta_title' => 'Home Title Updated',
            'og_image_file' => $fakeImage,
            'is_indexed' => '1',
            'is_followed' => '1',
        ]);

        $response->assertRedirect(route('admin.seo.index'));

        $seoRecord->refresh();
        $this->assertNotNull($seoRecord->og_image);
        $this->assertStringContainsString('/storage/seo/', $seoRecord->og_image);

        // Verify file actually exists in fake public storage
        $storedFilename = basename($seoRecord->og_image);
        Storage::disk('public')->assertExists('seo/' . $storedFilename);
    }
}

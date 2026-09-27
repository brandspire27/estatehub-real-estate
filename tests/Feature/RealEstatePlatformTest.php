<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Locality;
use App\Models\MatchRecord;
use App\Models\Package;
use App\Models\Property;
use App\Models\Requirement;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealEstatePlatformTest extends TestCase
{
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('EstateHub');
        $response->assertSee('Find a place');
    }

    public function test_properties_catalog_and_filtering_works(): void
    {
        $response = $this->get('/properties?city=Guwahati');
        $response->assertStatus(200);
        $response->assertSee('Properties for Sale');
    }

    public function test_property_details_page_increments_views(): void
    {
        $property = Property::where('status', 'PUBLISHED')->first();
        $initialViews = $property->views;

        // Verify SEO 301 redirect from plain ID
        $redirectResponse = $this->get('/properties/' . $property->id);
        $redirectResponse->assertStatus(301);
        $redirectResponse->assertRedirect('/properties/' . $property->id . '/' . $property->slug);

        // Access SEO URL
        $response = $this->get('/properties/' . $property->id . '/' . $property->slug);
        $response->assertStatus(200);
        $response->assertSee($property->title);

        $property->refresh();
        $this->assertEquals($initialViews + 1, $property->views);
    }

    public function test_buyer_can_submit_inquiry_and_unlock_seller_phone(): void
    {
        $property = Property::where('status', 'PUBLISHED')->first();

        $response = $this->postJson('/properties/' . $property->id . '/inquiry', [
            'buyer_name' => 'Anil Sharma',
            'buyer_phone' => '9876500000',
            'buyer_email' => 'anil@example.com',
            'message' => 'I would like to visit this property tomorrow.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'phone' => $property->user->phone,
        ]);

        $this->assertDatabaseHas('property_inquiries', [
            'property_id' => $property->id,
            'buyer_name' => 'Anil Sharma',
            'buyer_phone' => '9876500000',
        ]);
    }

    public function test_login_with_email_works(): void
    {
        $response = $this->post('/login', [
            'login' => 'buyer@realestate.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_with_phone_number_works(): void
    {
        $response = $this->post('/login', [
            'login' => '9876543210', // Admin phone
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
    }

    public function test_buyer_cannot_access_admin_panel(): void
    {
        $buyer = User::where('role', 'BUYER')->first();

        $response = $this->actingAs($buyer)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = User::where('role', 'ADMIN')->first();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Platform Administration');
    }

    public function test_matching_service_calculates_100_percent_match(): void
    {
        $matchingService = app(MatchingService::class);

        $property = Property::where('city', 'Guwahati')
            ->where('property_type', 'Apartment')
            ->where('bhk', 3)
            ->where('status', 'PUBLISHED')
            ->first();

        $requirement = Requirement::where('city', 'Guwahati')
            ->where('property_type', 'Apartment')
            ->where('bhk', 3)
            ->first();

        $score = $matchingService->calculateScore($requirement, $property);

        // City (25) + Type (20) + BHK (15) + Budget (25) + Possession (15) = 100
        $this->assertEquals(100, $score);
    }

    public function test_buyer_posting_requirement_triggers_matches(): void
    {
        $buyer = User::where('role', 'BUYER')->first();

        $response = $this->actingAs($buyer)->post('/requirements', [
            'property_type' => 'Apartment',
            'bhk' => 3,
            'budget_min' => 6000000,
            'budget_max' => 8000000,
            'city' => 'Guwahati',
            'possession_status' => 'Ready to Move',
            'description' => 'Test requirement for 3 BHK apartment in Guwahati',
        ]);

        $newReq = Requirement::where('description', 'Test requirement for 3 BHK apartment in Guwahati')->latest('id')->first();
        $this->assertNotNull($newReq);
        $response->assertRedirect(route('requirements.matches', $newReq));

        // Ensure matches were generated automatically
        $matchesCount = MatchRecord::where('requirement_id', $newReq->id)->count();
        $this->assertGreaterThan(0, $matchesCount);
    }

    public function test_seller_can_create_property_listing(): void
    {
        $seller = User::where('role', 'SELLER')->first();

        $response = $this->actingAs($seller)->post('/listings', [
            'title' => 'Brand New 3 BHK Skyline Flat',
            'description' => 'A prime property with scenic views and full modern amenities.',
            'price' => 5500000,
            'property_type' => 'Flat',
            'bhk' => 3,
            'area_sqft' => 1400,
            'city' => 'Guwahati',
            'area' => 'Zoo Road',
            'possession_status' => 'Ready to Move',
            'status' => 'PENDING_VERIFICATION',
        ]);

        $response->assertRedirect(route('listings.index'));
        $this->assertDatabaseHas('properties', [
            'title' => 'Brand New 3 BHK Skyline Flat',
            'user_id' => $seller->id,
            'status' => 'PENDING_VERIFICATION',
        ]);
    }

    public function test_staff_can_register_new_client(): void
    {
        $staff = User::where('role', 'STAFF')->first();

        $uniqueEmail = 'client_' . uniqid() . '@example.com';
        $uniquePhone = '99' . rand(10000000, 99999999);

        $response = $this->actingAs($staff)->post('/staff/users', [
            'name' => 'New Client Created By Staff',
            'email' => $uniqueEmail,
            'phone' => $uniquePhone,
            'password' => 'password',
            'role' => 'BUYER',
        ]);

        $response->assertRedirect(route('staff.users'));
        $this->assertDatabaseHas('users', [
            'email' => $uniqueEmail,
            'created_by_user_id' => $staff->id,
            'created_via_staff' => 1,
        ]);
    }

    public function test_requirement_records_locality(): void
    {
        $buyer = User::where('role', 'BUYER')->first();

        $response = $this->actingAs($buyer)->post('/requirements', [
            'property_type' => 'Flat',
            'bhk' => 2,
            'budget_min' => 4000000,
            'budget_max' => 6000000,
            'city' => 'Guwahati',
            'locality' => 'Zoo Road / Ambikagiri Nagar',
            'possession_status' => 'Ready to Move',
            'description' => 'Requirement with explicit locality test',
        ]);

        $this->assertDatabaseHas('requirements', [
            'city' => 'Guwahati',
            'locality' => 'Zoo Road / Ambikagiri Nagar',
            'description' => 'Requirement with explicit locality test',
        ]);
    }

    public function test_requirement_matches_page_has_whatsapp_sharing(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $requirement = Requirement::find(9) ?: Requirement::first();

        $response = $this->actingAs($admin)->get("/requirements/{$requirement->id}/matches");
        $response->assertStatus(200);
        $response->assertSee('Share on WhatsApp');
        $response->assertSee('web.whatsapp.com');
        $response->assertSee('Here are your matched properties');
        $response->assertSee('openWhatsApp');
    }

    public function test_admin_can_manage_localities(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $buyer = User::where('role', 'BUYER')->first();

        // Buyer cannot access admin localities
        $this->actingAs($buyer)->get('/admin/localities')->assertStatus(403);

        // Admin can access
        $this->actingAs($admin)->get('/admin/localities')->assertStatus(200)->assertSee('Locality Management');

        // Admin can add a locality
        $uniqueLocName = 'Lachit Nagar ' . rand(1000, 9999);
        $storeResponse = $this->actingAs($admin)->post('/admin/localities', [
            'city' => 'Guwahati',
            'name' => $uniqueLocName,
            'is_active' => 1,
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('localities', [
            'city' => 'Guwahati',
            'name' => $uniqueLocName,
        ]);
    }

    public function test_staff_can_search_clients_via_ajax(): void
    {
        $staff = User::where('role', 'STAFF')->first();
        $buyer = User::where('role', 'BUYER')->first();

        // Buyer cannot search staff users
        $this->actingAs($buyer)->getJson('/staff/users/search?q=test')->assertStatus(403);

        // Staff can search by phone or name
        $response = $this->actingAs($staff)->getJson("/staff/users/search?q={$buyer->name}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $buyer->name]);
    }

    public function test_sixmile_and_beltola_properties_exist_and_match(): void
    {
        $sixmileCount = Property::where('area', 'Sixmile')->where('status', 'PUBLISHED')->count();
        $beltolaCount = Property::where('area', 'Beltola')->where('status', 'PUBLISHED')->count();

        $this->assertGreaterThanOrEqual(5, $sixmileCount);
        $this->assertGreaterThanOrEqual(5, $beltolaCount);
        $this->assertGreaterThanOrEqual(10, $sixmileCount + $beltolaCount);

        // Verify public API returns Sixmile and Beltola for Guwahati
        $response = $this->getJson('/api/cities/Guwahati/localities');
        $response->assertStatus(200);
        $response->assertJsonFragment(['Sixmile']);
        $response->assertJsonFragment(['Beltola']);
    }

    public function test_homepage_mumbai_image_is_valid(): void
    {
        $mumbai = City::where('name', 'Mumbai')->first();
        $this->assertNotNull($mumbai);
        $this->assertStringContainsString('photo-1570168007204-dfb528c6958f', $mumbai->image_url);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($mumbai->image_url);
    }

    public function test_properties_catalog_has_mobile_filter_and_dynamic_locality(): void
    {
        $response = $this->get('/properties');
        $response->assertStatus(200);
        $response->assertSeeText('Filter & Refine');
        $response->assertSee('mobileFiltersOpen');
        $response->assertSeeText('City (Select First)');
        $response->assertSee('All Localities in');
        $response->assertSeeText('Plot & Land Size');
        $response->assertSeeText('Commercial Space Size');
    }

    public function test_property_cards_display_relevant_specs_for_plots_and_commercial(): void
    {
        $commercial = Property::where('property_type', 'Commercial Space')->first();
        $this->assertNotNull($commercial);

        $response = $this->get('/properties?property_type=Commercial+Space');
        $response->assertStatus(200);
        $response->assertDontSee('N/A BHK');
    }

    public function test_buyer_profile_page_displays_professional_metrics_and_settings(): void
    {
        $buyer = User::where('role', 'BUYER')->first();
        $this->actingAs($buyer);

        $response = $this->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Verified Buyer');
        $response->assertSee('Active Requirements');
        $response->assertSee('Qualified Matches');
        $response->assertSeeText('Personal & Contact Information');
        $response->assertSeeText('Communication & Privacy Shield');
    }

    public function test_every_city_has_at_least_ten_localities_in_database(): void
    {
        $cities = City::all();
        $this->assertNotEmpty($cities);

        foreach ($cities as $city) {
            $count = Locality::where('city', $city->name)->where('is_active', true)->count();
            $this->assertGreaterThanOrEqual(10, $count, "City {$city->name} has fewer than 10 localities.");
        }
    }

    public function test_matching_engine_rejects_commercial_and_plots_for_residential_flat_requirement(): void
    {
        $flatReq = Requirement::where('property_type', 'Flat')->where('status', 'ACTIVE')->first();
        $this->assertNotNull($flatReq);

        $matcher = app(\App\Services\MatchingService::class);

        $commercialProp = Property::where('property_type', 'Commercial Space')->where('status', 'PUBLISHED')->first();
        if ($commercialProp) {
            $commercialScore = $matcher->calculateScore($flatReq, $commercialProp);
            $this->assertEquals(0, $commercialScore, "Commercial property should score 0 against a residential flat requirement.");
        }

        $plotProp = Property::where('property_type', 'Plot')->where('status', 'PUBLISHED')->first();
        if ($plotProp) {
            $plotScore = $matcher->calculateScore($flatReq, $plotProp);
            $this->assertEquals(0, $plotScore, "Plot property should score 0 against a residential flat requirement.");
        }
    }

    public function test_admin_can_manage_packages_crud(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $buyer = User::where('role', 'BUYER')->first();

        // Non-admin blocked
        $this->actingAs($buyer)->get('/admin/packages')->assertStatus(403);

        // Admin can list packages
        $response = $this->actingAs($admin)->get('/admin/packages');
        $response->assertStatus(200);
        $response->assertSee('Subscription Packages');
        $response->assertSee('Starter Free');

        // Admin can create a new package
        $pkgName = 'Developer Elite ' . rand(100, 999);
        $storeResponse = $this->actingAs($admin)->post('/admin/packages', [
            'name' => $pkgName,
            'price' => 7999,
            'billing_period' => 'yearly',
            'duration_days' => 365,
            'listing_limit' => 50,
            'featured_limit' => 10,
            'contact_unlock_limit' => 100,
            'description' => 'For top builders',
            'features' => "Top search placement\nDedicated manager",
            'target_role' => 'SELLER',
            'is_active' => 1,
        ]);
        $storeResponse->assertRedirect(route('admin.packages.index'));
        $this->assertDatabaseHas('packages', ['name' => $pkgName]);

        $newPkg = Package::where('name', $pkgName)->first();

        // Admin can toggle status
        $this->actingAs($admin)->post("/admin/packages/{$newPkg->id}/toggle")->assertRedirect();
        $this->assertEquals(false, $newPkg->fresh()->is_active);

        // Admin can delete package
        $this->actingAs($admin)->delete("/admin/packages/{$newPkg->id}")->assertRedirect();
        $this->assertDatabaseMissing('packages', ['id' => $newPkg->id]);
    }

    public function test_seller_can_select_package_from_dashboard(): void
    {
        $seller = User::where('role', 'SELLER')->first();
        $package = Package::where('slug', 'pro-seller')->first() ?: Package::first();

        $response = $this->actingAs($seller)->post('/dashboard/select-package', [
            'package_id' => $package->id,
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $seller->refresh();
        $this->assertEquals($package->id, $seller->package_id);
        $this->assertNotNull($seller->package_expires_at);
        $this->assertTrue($seller->hasActivePackage());
    }

    public function test_seller_can_create_property_with_dynamic_types_youtube_url_and_private_dossier(): void
    {
        $seller = User::where('role', 'SELLER')->first();

        $uniqueTitle = 'Prime Commercial Land ' . rand(1000, 9999);
        $uniqueDeed = 'PATTA-' . rand(100000, 999999);
        $privateNote = 'Private negotiation floor is strictly 85 Lakhs.';

        $response = $this->actingAs($seller)->post('/listings', [
            'title' => $uniqueTitle,
            'description' => 'Exceptional commercial plot located directly on highway with clear title deed.',
            'price' => 9500000,
            'property_type' => 'Land',
            'bhk' => null, // No BHK for Land
            'area_sqft' => 4500,
            'city' => 'Guwahati',
            'area' => 'Sixmile',
            'possession_status' => 'Ready to Move',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'kyc_document_type' => 'Title Deed / Patta',
            'kyc_document_number' => $uniqueDeed,
            'kyc_document_url' => 'https://drive.google.com/file/d/patta-deed/view',
            'private_remarks' => $privateNote,
            'private_photos' => ['https://images.unsplash.com/blueprint1.jpg'],
            'private_videos' => ['https://youtube.com/private_inspection_1'],
            'status' => 'PUBLISHED', // Non-staff seller should be forced to PENDING_VERIFICATION
        ]);

        $response->assertRedirect(route('listings.index'));
        $response->assertSessionHas('success');

        $property = Property::where('title', $uniqueTitle)->first();
        $this->assertNotNull($property);

        // Seller submitted property MUST be PENDING_VERIFICATION (not live PUBLISHED)
        $this->assertEquals('PENDING_VERIFICATION', $property->status);
        $this->assertEquals('Land', $property->property_type);
        $this->assertNull($property->bhk);
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $property->youtube_url);
        $this->assertEquals('Title Deed / Patta', $property->kyc_document_type);
        $this->assertEquals($uniqueDeed, $property->kyc_document_number);
        $this->assertEquals($privateNote, $property->private_remarks);
        $this->assertIsArray($property->private_photos);
        $this->assertContains('https://images.unsplash.com/blueprint1.jpg', $property->private_photos);
    }

    public function test_private_seller_dossier_is_hidden_from_public_and_visible_to_seller(): void
    {
        $seller = User::where('role', 'SELLER')->first();
        $buyer = User::where('role', 'BUYER')->first();

        // Create a property with confidential remarks
        $property = Property::create([
            'user_id' => $seller->id,
            'created_by_user_id' => $seller->id,
            'title' => 'Executive Villa With Confidential Dossier ' . rand(100, 999),
            'description' => 'Beautiful villa in quiet locality with all amenities included.',
            'price' => 7500000,
            'property_type' => 'Villa',
            'bhk' => 4,
            'area_sqft' => 2400,
            'city' => 'Guwahati',
            'area' => 'Beltola',
            'possession_status' => 'Ready to Move',
            'status' => 'PUBLISHED',
            'kyc_document_type' => 'Registered Sale Deed',
            'kyc_document_number' => 'SECRET-REG-DEED-777',
            'private_remarks' => 'CONFIDENTIAL: Minimum reserve price is 68 Lakhs. Tenant leaves next month.',
        ]);

        // Seller viewing their own property DOES see the private dossier
        $sellerResponse = $this->actingAs($seller)->get($property->url);
        $sellerResponse->assertStatus(200);
        $sellerResponse->assertSee('Private Seller Dossier');
        $sellerResponse->assertSee('SECRET-REG-DEED-777');
        $sellerResponse->assertSee('CONFIDENTIAL: Minimum reserve price is 68 Lakhs');

        // Public / Buyer viewing the property DOES NOT see the private dossier
        $buyerResponse = $this->actingAs($buyer)->get($property->url);
        $buyerResponse->assertStatus(200);
        $buyerResponse->assertDontSee('Private Seller Dossier');
        $buyerResponse->assertDontSee('SECRET-REG-DEED-777');
        $buyerResponse->assertDontSee('CONFIDENTIAL: Minimum reserve price is 68 Lakhs');
    }

    public function test_seller_submitted_property_is_hidden_from_live_site_until_admin_approves(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $seller = User::where('role', 'SELLER')->first();

        // Seller creates listing
        $title = 'Unapproved Property ' . rand(1000, 9999);
        $this->actingAs($seller)->post('/listings', [
            'title' => $title,
            'description' => 'A prime test property awaiting admin verification.',
            'price' => 5000000,
            'property_type' => 'Flat',
            'bhk' => 2,
            'area_sqft' => 1100,
            'city' => 'Guwahati',
            'area' => 'Sixmile',
            'possession_status' => 'Ready to Move',
        ]);

        $prop = Property::where('title', $title)->first();
        $this->assertNotNull($prop);
        $this->assertEquals('PENDING_VERIFICATION', $prop->status);

        // Not in public catalogue
        $catalogResponse = $this->get('/properties?city=Guwahati');
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertDontSee($title);

        // Admin approves property
        $approveResponse = $this->actingAs($admin)->post("/admin/properties/{$prop->id}/approve");
        $approveResponse->assertRedirect();
        $approveResponse->assertSessionHas('success');

        $prop->refresh();
        $this->assertEquals('PUBLISHED', $prop->status);

        // Now visible in public catalogue!
        $catalogAfterApproval = $this->get('/properties?city=Guwahati');
        $catalogAfterApproval->assertStatus(200);
        $catalogAfterApproval->assertSee($title);
    }

    public function test_admin_and_staff_can_approve_property_from_dashboard(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $staff = User::where('role', 'STAFF')->first();
        $seller = User::where('role', 'SELLER')->first();

        // Create pending property
        $prop = Property::create([
            'user_id' => $seller->id,
            'title' => 'Moderation Test ' . rand(1000, 9999),
            'status' => 'PENDING_VERIFICATION',
            'price' => 4500000,
            'city' => 'Bangalore',
            'area' => 'Indiranagar',
            'property_type' => 'Apartment',
            'description' => 'A wonderful apartment for verification test.',
            'bhk' => 2,
            'area_sqft' => 1200,
            'possession_status' => 'Ready to Move',
        ]);

        // Staff can approve via staff route
        $staffApprove = $this->actingAs($staff)->post("/staff/properties/{$prop->id}/approve");
        $staffApprove->assertRedirect();
        $prop->refresh();
        $this->assertEquals('PUBLISHED', $prop->status);
    }

    public function test_admin_can_create_new_user_with_role_and_package(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $pkg = \App\Models\Package::first();

        $createPage = $this->actingAs($admin)->get('/admin/users/create');
        $createPage->assertStatus(200);
        $createPage->assertSee('Register New User');

        $email = 'newuser' . rand(1000, 9999) . '@example.com';
        $phone = '98765' . rand(10000, 99999);

        $storeResponse = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Aditi Sen',
            'email' => $email,
            'phone' => $phone,
            'password' => 'secret123',
            'role' => 'BUYER',
            'package_id' => $pkg?->id,
            'is_active' => 1,
        ]);

        $storeResponse->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'BUYER',
            'created_by_user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_create_staff_with_kyc(): void
    {
        $admin = User::where('role', 'ADMIN')->first();

        $createStaffPage = $this->actingAs($admin)->get('/admin/staff/create');
        $createStaffPage->assertStatus(200);
        $createStaffPage->assertSee('Staff KYC', false);

        $email = 'staffkyc' . rand(1000, 9999) . '@example.com';
        $phone = '98764' . rand(10000, 99999);

        $storeStaff = $this->actingAs($admin)->post('/admin/staff', [
            'name' => 'Vikram Singhania',
            'email' => $email,
            'phone' => $phone,
            'password' => 'password123',
            'kyc_document_type' => 'Aadhaar Card',
            'kyc_document_number' => '9999-8888-7777',
            'kyc_document_url' => 'https://storage.example.com/kyc/vikram.pdf',
            'address' => 'GS Road, Guwahati, Assam',
            'emergency_contact' => 'Anand Singhania - 9876500001',
        ]);

        $storeStaff->assertRedirect('/admin/staff');
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'STAFF',
            'kyc_status' => 'VERIFIED',
            'kyc_document_type' => 'Aadhaar Card',
            'kyc_document_number' => '9999-8888-7777',
        ]);
    }

    public function test_admin_properties_table_contains_edit_option(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $response = $this->actingAs($admin)->get('/admin/properties');
        $response->assertStatus(200);
        $response->assertSee('Edit Property Details');
    }

    public function test_properties_filter_form_contains_localities(): void
    {
        $response = $this->get('/properties');
        $response->assertStatus(200);
        $response->assertSee('locality-select-dropdown');
        $response->assertSee('Beltola');
        $response->assertSee('Sixmile');
    }

    public function test_profile_page_supports_all_user_roles_and_kyc_updates(): void
    {
        $roles = ['ADMIN', 'STAFF', 'SELLER', 'BUYER'];

        foreach ($roles as $role) {
            $user = User::where('role', $role)->first();
            $response = $this->actingAs($user)->get('/profile');
            $response->assertStatus(200);
            $response->assertSee('Regulatory KYC', false);
        }

        // Test updating profile with KYC
        $buyer = User::where('role', 'BUYER')->first();
        $updateResponse = $this->actingAs($buyer)->put('/profile', [
            'name' => $buyer->name,
            'email' => $buyer->email,
            'phone' => $buyer->phone,
            'address' => 'Apartment 504, Riverside Greens, Guwahati',
            'emergency_contact' => 'Sunil Sharma - 9876541111',
            'kyc_document_type' => 'PAN Card',
            'kyc_document_number' => 'ABCDE1234F',
            'kyc_document_url' => 'https://example.com/kyc-pan.jpg',
        ]);

        $updateResponse->assertRedirect();
        $buyer->refresh();
        $this->assertEquals('VERIFIED', $buyer->kyc_status);
        $this->assertEquals('PAN Card', $buyer->kyc_document_type);
        $this->assertEquals('ABCDE1234F', $buyer->kyc_document_number);
    }

    public function test_property_photo_and_private_seller_dossier_uploads_and_deletion(): void
    {
        Storage::fake('public');
        $seller = User::where('role', 'SELLER')->first();

        // 1. Create property with uploaded public photos, KYC file, private photo files, and private video files
        $photo1 = UploadedFile::fake()->image('livingroom.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('bedroom.png', 800, 600);
        $kycDoc = UploadedFile::fake()->create('aadhaar_deed.pdf', 500, 'application/pdf');
        $privDoc = UploadedFile::fake()->image('blueprint.jpg', 1200, 900);
        $privVideo = UploadedFile::fake()->create('inspection.mp4', 2048, 'video/mp4');

        $storeResponse = $this->actingAs($seller)->post('/listings', [
            'title' => 'Dossier Upload Test ' . rand(1000, 9999),
            'city' => 'Guwahati',
            'area' => 'Zoo Road',
            'property_type' => 'Flat',
            'bhk' => 3,
            'area_sqft' => 1500,
            'price' => 6500000,
            'possession_status' => 'Ready to Move',
            'description' => 'A wonderful listing testing photo upload and dossier file capabilities.',
            'property_photos' => [$photo1, $photo2],
            'kyc_document_type' => 'Title Deed / Patta',
            'kyc_document_number' => 'PATTA-998822',
            'kyc_document_file' => $kycDoc,
            'private_photo_files' => [$privDoc],
            'private_video_files' => [$privVideo],
            'private_remarks' => 'Internal seller confidential negotiation floor is 62 Lakhs.',
            'status' => 'PENDING_VERIFICATION',
        ]);

        $storeResponse->assertRedirect('/listings');
        $prop = Property::where('user_id', $seller->id)->latest('id')->first();
        $this->assertNotNull($prop);
        $this->assertCount(2, $prop->images);

        // Verify KYC and private files stored in public disk
        $this->assertNotEmpty($prop->kyc_document_url);
        $this->assertStringContainsString('/storage/properties/dossier/kyc/', $prop->kyc_document_url);

        $this->assertIsArray($prop->private_photos);
        $this->assertCount(1, $prop->private_photos);
        $this->assertStringContainsString('/storage/properties/dossier/photos/', $prop->private_photos[0]);

        $this->assertIsArray($prop->private_videos);
        $this->assertCount(1, $prop->private_videos);
        $this->assertStringContainsString('/storage/properties/dossier/videos/', $prop->private_videos[0]);

        // 2. Test edit page renders upload fields & current files
        $editPage = $this->actingAs($seller)->get("/listings/{$prop->id}/edit");
        $editPage->assertStatus(200);
        $editPage->assertSee('Public Property Photography');
        $editPage->assertSee('Upload New Property Photos');
        $editPage->assertSee('Private Seller Dossier');
        $editPage->assertSee('Upload New Document File');
        $editPage->assertSee('Upload Private Document');
        $editPage->assertSee('Upload Video Files from Device');

        // 3. Test update: delete one image and upload another
        $imgToDelete = $prop->images->first();
        $newPhoto = UploadedFile::fake()->image('balcony.jpg', 600, 400);

        $updateResponse = $this->actingAs($seller)->put("/listings/{$prop->id}", [
            'title' => $prop->title . ' (Updated)',
            'city' => $prop->city,
            'area' => $prop->area,
            'property_type' => $prop->property_type,
            'bhk' => $prop->bhk,
            'area_sqft' => $prop->area_sqft,
            'price' => $prop->price,
            'possession_status' => $prop->possession_status,
            'description' => $prop->description,
            'delete_image_ids' => [$imgToDelete->id],
            'property_photos' => [$newPhoto],
            'status' => 'PENDING_VERIFICATION',
        ]);

        $updateResponse->assertRedirect('/listings');
        $prop->refresh();

        // One deleted, one retained, one newly uploaded -> count should still be 2
        $this->assertCount(2, $prop->images);
        $this->assertFalse($prop->images->contains('id', $imgToDelete->id));
        $this->assertEquals($prop->title, $prop->fresh()->title);
    }
}





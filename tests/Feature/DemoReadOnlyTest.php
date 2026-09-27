<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoReadOnlyTest extends TestCase
{
    public function test_public_demo_allows_browsing_and_login(): void
    {
        config(['app.demo_read_only' => true]);

        $this->get('/login')->assertOk();
        $this->post('/login', [])->assertRedirect('/login');
    }

    public function test_public_demo_rejects_state_changing_requests(): void
    {
        config(['app.demo_read_only' => true]);

        $this->post('/contact', [])->assertForbidden();
    }

    public function test_public_demo_login_does_not_repair_the_default_admin_account(): void
    {
        config(['app.demo_read_only' => true]);

        $admin = User::where('email', 'admin@estatehub.com')->firstOrFail();
        $admin->password = Hash::make('different-password');
        $admin->save();
        $activityCount = ActivityLog::count();

        $this->post('/login', [
            'login' => 'admin@estatehub.com',
            'password' => 'password',
        ])->assertSessionHasErrors(['login']);

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertTrue(Hash::check('different-password', $admin->fresh()->password));
        $this->assertSame($activityCount, ActivityLog::count());
    }

    public function test_public_demo_property_views_do_not_change_property_records(): void
    {
        config(['app.demo_read_only' => true]);

        $seller = User::factory()->create(['role' => 'SELLER']);
        $property = Property::create([
            'user_id' => $seller->id,
            'title' => 'Read-only Demo Property',
            'description' => 'A published property for testing read-only demo behavior.',
            'price' => 5000000,
            'property_type' => 'Apartment',
            'bhk' => 2,
            'area_sqft' => 1200,
            'city' => 'Guwahati',
            'area' => 'Zoo Road',
            'status' => 'PUBLISHED',
            'views' => 7,
        ]);

        $this->get("/properties/{$property->id}")->assertRedirect(route('properties.show', [
            'property' => $property->id,
            'slug' => 'read-only-demo-property',
        ]));

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'slug' => null,
            'views' => 7,
        ]);
    }

    public function test_regular_app_mode_keeps_existing_writes_enabled(): void
    {
        config(['app.demo_read_only' => false]);

        $this->post('/contact', [])->assertSessionHasErrors(['name', 'email', 'message']);
    }
}

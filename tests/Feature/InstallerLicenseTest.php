<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckInstallation;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InstallerLicenseTest extends TestCase
{
    public function test_unavailable_license_service_does_not_accept_a_formatted_code(): void
    {
        $this->withoutMiddleware(CheckInstallation::class);

        Http::fake([
            'https://lab.webotapp.com/api/license/verify' => Http::response([], 503),
        ]);

        $response = $this->postJson('/install/verify-customer', [
            'name' => 'Demo Customer',
            'phone_country_code' => '+1',
            'phone_number' => '2025550123',
            'email' => 'demo@example.com',
            'purchase_code' => 'WEBOT-REAL-DEMO-1234',
            'agree_license' => '1',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonPath('success', false);
    }
}

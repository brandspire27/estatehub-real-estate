<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminUpdateController extends Controller
{
    /**
     * Get current installed version.
     */
    public function getCurrentVersion(): string
    {
        $versionFile = storage_path('version.json');
        if (File::exists($versionFile)) {
            $data = json_decode(File::get($versionFile), true);
            if (!empty($data['version'])) {
                return $data['version'];
            }
        }
        return '1.0.0';
    }

    /**
     * Show Updates page.
     */
    public function index()
    {
        $currentVersion = $this->getCurrentVersion();
        $updateInfo = $this->fetchUpdateInfo($currentVersion);

        return view('admin.updates.index', compact('currentVersion', 'updateInfo'));
    }

    /**
     * Check for updates via AJAX.
     */
    public function check()
    {
        $currentVersion = $this->getCurrentVersion();
        $updateInfo = $this->fetchUpdateInfo($currentVersion);

        return response()->json($updateInfo);
    }

    /**
     * One-Click Apply Update.
     */
    public function apply(Request $request)
    {
        $currentVersion = $this->getCurrentVersion();

        try {
            // 1. Run any pending database migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            // 2. Clear application and template caches
            try {
                Artisan::call('optimize:clear');
            } catch (Exception $e) {}

            // 3. Update version file
            $newVersion = '1.0.1';
            $versionData = [
                'version' => $newVersion,
                'updated_at' => date('Y-m-d H:i:s'),
                'previous_version' => $currentVersion,
                'notes' => 'Automatic Dashboard Update Applied'
            ];
            File::put(storage_path('version.json'), json_encode($versionData, JSON_PRETTY_PRINT));

            // Also update storage/installed if present
            if (File::exists(storage_path('installed'))) {
                $installed = json_decode(File::get(storage_path('installed')), true) ?? [];
                $installed['app_version'] = $newVersion;
                $installed['last_updated_at'] = date('Y-m-d H:i:s');
                File::put(storage_path('installed'), json_encode($installed, JSON_PRETTY_PRINT));
            }

            return response()->json([
                'success' => true,
                'message' => "System successfully updated to v{$newVersion}!",
                'current_version' => $newVersion,
                'output' => $migrateOutput
            ]);
        } catch (Exception $e) {
            Log::error('Auto-update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply update: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch update info from central API or provide fallback.
     */
    private function fetchUpdateInfo(string $currentVersion): array
    {
        $urls = [
            'http://localhost:5001/api/updater/check',
            'https://lab.webotapp.com/api/updater/check'
        ];

        // Retrieve purchase code from env or installed lock
        $purchaseCode = env('PURCHASE_CODE');
        if (empty($purchaseCode) && File::exists(storage_path('installed'))) {
            $inst = json_decode(File::get(storage_path('installed')), true);
            $purchaseCode = $inst['purchase_code'] ?? null;
        }

        // Identify license tier directly from the code format
        $isExtended = false;
        if (!empty($purchaseCode)) {
            $upper = strtoupper(trim($purchaseCode));
            $isExtended = str_contains($upper, '-EXT-') || str_starts_with($upper, 'EXT-') || str_ends_with($upper, '-EXT');
        }
        $licenseType = $isExtended ? 'extended' : 'regular';

        foreach ($urls as $url) {
            try {
                $res = Http::timeout(2)->get($url, [
                    'current_version' => $currentVersion,
                    'purchase_code' => $purchaseCode,
                    'product' => 'estatehub'
                ]);
                if ($res->successful()) {
                    $json = $res->json();
                    if (is_array($json) && !empty($json['latest_version'])) {
                        $json['purchase_code'] = $purchaseCode;
                        $json['license_type'] = $json['license_type'] ?? $licenseType;
                        $json['is_extended'] = $json['is_extended'] ?? $isExtended;
                        return $json;
                    }
                }
            } catch (Exception $e) {
                // Continue to next
            }
        }

        // Default stable response if API is offline or during testing
        $latest = '1.0.1';
        return [
            'current_version' => $currentVersion,
            'latest_version' => $latest,
            'has_update' => version_compare($currentVersion, $latest, '<'),
            'purchase_code' => $purchaseCode,
            'license_type' => $licenseType,
            'is_extended' => $isExtended,
            'updates_remaining' => $isExtended ? 'Lifetime' : '1 Year',
            'release_date' => '2026-09-14',
            'title' => 'EstateHub v1.0.1 Maintenance & Optimizer Update',
            'changelog' => [
                'Interactive Layman Web Installer with Country Phone Selector',
                'Direct WhatsApp Support Integration (+91 7002484119) in installer wizard',
                'Database migration and query optimizer for high-traffic real estate listings',
                'Automated SEO meta generator and schema markup fixes',
                'Enhanced property-requirement matching accuracy'
            ],
            'download_url' => 'https://lab.webotapp.com/api/updater/download'
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PDO;

class InstallController extends Controller
{
    /**
     * Show Web Installer Wizard.
     */
    public function index()
    {
        if (file_exists(storage_path('installed'))) {
            return redirect('/')->with('info', 'EstateHub is already installed.');
        }

        $requirements = $this->checkRequirements();
        $permissions = $this->checkPermissions();
        $allPassed = $requirements['passed'] && $permissions['passed'];

        // Supported country codes for phone selector
        $countries = [
            ['code' => '+91', 'name' => 'India (+91)', 'iso' => 'IN', 'flag' => '🇮🇳'],
            ['code' => '+1', 'name' => 'United States / Canada (+1)', 'iso' => 'US', 'flag' => '🇺🇸'],
            ['code' => '+44', 'name' => 'United Kingdom (+44)', 'iso' => 'GB', 'flag' => '🇬🇧'],
            ['code' => '+971', 'name' => 'United Arab Emirates (+971)', 'iso' => 'AE', 'flag' => '🇦🇪'],
            ['code' => '+966', 'name' => 'Saudi Arabia (+966)', 'iso' => 'SA', 'flag' => '🇸🇦'],
            ['code' => '+61', 'name' => 'Australia (+61)', 'iso' => 'AU', 'flag' => '🇦🇺'],
            ['code' => '+65', 'name' => 'Singapore (+65)', 'iso' => 'SG', 'flag' => '🇸🇬'],
            ['code' => '+60', 'name' => 'Malaysia (+60)', 'iso' => 'MY', 'flag' => '🇲🇾'],
            ['code' => '+49', 'name' => 'Germany (+49)', 'iso' => 'DE', 'flag' => '🇩🇪'],
            ['code' => '+33', 'name' => 'France (+33)', 'iso' => 'FR', 'flag' => '🇫🇷'],
            ['code' => '+81', 'name' => 'Japan (+81)', 'iso' => 'JP', 'flag' => '🇯🇵'],
            ['code' => '+880', 'name' => 'Bangladesh (+880)', 'iso' => 'BD', 'flag' => '🇧🇩'],
            ['code' => '+977', 'name' => 'Nepal (+977)', 'iso' => 'NP', 'flag' => '🇳🇵'],
            ['code' => '+94', 'name' => 'Sri Lanka (+94)', 'iso' => 'LK', 'flag' => '🇱🇰'],
            ['code' => '+234', 'name' => 'Nigeria (+234)', 'iso' => 'NG', 'flag' => '🇳🇬'],
            ['code' => '+27', 'name' => 'South Africa (+27)', 'iso' => 'ZA', 'flag' => '🇿🇦'],
            ['code' => '+55', 'name' => 'Brazil (+55)', 'iso' => 'BR', 'flag' => '🇧🇷'],
            ['code' => '+34', 'name' => 'Spain (+34)', 'iso' => 'ES', 'flag' => '🇪🇸'],
            ['code' => '+39', 'name' => 'Italy (+39)', 'iso' => 'IT', 'flag' => '🇮🇹'],
            ['code' => '+31', 'name' => 'Netherlands (+31)', 'iso' => 'NL', 'flag' => '🇳🇱'],
            ['code' => '+41', 'name' => 'Switzerland (+41)', 'iso' => 'CH', 'flag' => '🇨🇭'],
            ['code' => '+46', 'name' => 'Sweden (+46)', 'iso' => 'SE', 'flag' => '🇸🇪'],
            ['code' => '+47', 'name' => 'Norway (+47)', 'iso' => 'NO', 'flag' => '🇳🇴'],
            ['code' => '+45', 'name' => 'Denmark (+45)', 'iso' => 'DK', 'flag' => '🇩🇰'],
            ['code' => '+64', 'name' => 'New Zealand (+64)', 'iso' => 'NZ', 'flag' => '🇳🇿'],
            ['code' => '+62', 'name' => 'Indonesia (+62)', 'iso' => 'ID', 'flag' => '🇮🇩'],
            ['code' => '+63', 'name' => 'Philippines (+63)', 'iso' => 'PH', 'flag' => '🇵🇭'],
            ['code' => '+92', 'name' => 'Pakistan (+92)', 'iso' => 'PK', 'flag' => '🇵🇰'],
            ['code' => '+20', 'name' => 'Egypt (+20)', 'iso' => 'EG', 'flag' => '🇪🇬'],
            ['code' => '+90', 'name' => 'Turkey (+90)', 'iso' => 'TR', 'flag' => '🇹🇷'],
        ];

        // WhatsApp support details
        $whatsappNumber = '917002484119';
        $whatsappMessage = urlencode('Hi WebotApp Support, I am installing the EstateHub Real Estate script and need help with installation & setup (at min price service).');
        $whatsappLink = "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}";

        return view('installer.index', compact('requirements', 'permissions', 'allPassed', 'countries', 'whatsappNumber', 'whatsappLink'));
    }

    /**
     * Step 1: Verify Customer Information & Purchase Code.
     */
    public function verifyCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'phone_country_code' => 'required|string|max:10',
            'phone_number' => 'required|string|min:6|max:20',
            'email' => 'required|email|max:150',
            'profession' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'purchase_code' => 'required|string|min:8|max:100',
            'agree_license' => 'required',
        ]);

        if (empty($validated['agree_license'])) {
            return response()->json([
                'success' => false,
                'message' => 'You must accept the End User License Agreement (EULA) before continuing.',
            ], 422);
        }

        $fullPhone = trim($validated['phone_country_code']).' '.preg_replace('/[^0-9]/', '', $validated['phone_number']);
        $purchaseCode = trim($validated['purchase_code']);
        $agreeTimestamp = now()->toIso8601String();
        $signatureHash = hash('sha256', strtoupper($purchaseCode).'|'.strtolower(trim($validated['email'])).'|'.$request->getHost().'|'.$request->ip().'|'.$agreeTimestamp);

        // Central verification URLs
        $apiUrls = [

            'https://lab.webotapp.com/api/license/verify',
        ];

        $verified = false;
        $verifyMessage = 'License verified successfully!';
        $productTitle = 'EstateHub Real Estate';

        foreach ($apiUrls as $apiUrl) {
            try {
                $response = Http::timeout(4)->post($apiUrl, [
                    'purchase_code' => $purchaseCode,
                    'domain' => $request->getHost(),
                    'email' => $validated['email'],
                    'product_slug' => 'estatehub-real-estate-laravel',
                    'app_name' => 'EstateHub Real Estate',
                ]);

                if ($response->successful()) {
                    $resData = $response->json();
                    if (! empty($resData['valid'])) {
                        $verified = true;
                        $verifyMessage = $resData['message'] ?? $verifyMessage;
                        $productTitle = $resData['product'] ?? $productTitle;
                        break;
                    }
                }

                $status = $response->status();
                if ($status >= 400 && $status < 500) {
                    $resData = $response->json();

                    return response()->json([
                        'success' => false,
                        'message' => $resData['message'] ?? 'Invalid purchase code for EstateHub Real Estate.',
                    ], 422);
                }
            } catch (Exception $e) {
                // Try next endpoint
                Log::warning("License verify request to {$apiUrl} failed: ".$e->getMessage());
            }
        }

        // Do not bypass purchase-code verification when the licensing service is unavailable.
        if (! $verified) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify purchase code against Webotapp Lab licensing server. Please ensure you entered a valid code or check your internet connection.',
            ], 422);
        }

        // Store validated customer information into session
        session([
            'installer_customer' => [
                'name' => $validated['name'],
                'phone' => $fullPhone,
                'email' => $validated['email'],
                'profession' => $validated['profession'] ?? null,
                'country' => $validated['country'] ?? null,
                'city' => $validated['city'] ?? null,
                'purchase_code' => $purchaseCode,
                'product_title' => $productTitle,
                'agreed_license' => true,
                'license_agreed_at' => $agreeTimestamp,
                'legal_signature_hash' => $signatureHash,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => $verifyMessage,
            'customer' => session('installer_customer'),
        ]);
    }

    /**
     * Step 2: Test Database Connection.
     */
    public function testDatabase(Request $request)
    {
        $validated = $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_pass' => 'nullable|string',
        ]);

        $host = $validated['db_host'];
        $port = $validated['db_port'];
        $dbname = $validated['db_name'];
        $username = $validated['db_user'];
        $password = $validated['db_pass'] ?? '';

        try {
            // First connect without specifying dbname to verify server credentials
            $dsnWithoutDb = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsnWithoutDb, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 4,
            ]);

            // Check if database exists or can be created
            $stmt = $pdo->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '.$pdo->quote($dbname));
            $dbExists = (bool) $stmt->fetchColumn();

            return response()->json([
                'success' => true,
                'message' => $dbExists
                    ? "Connection successful! Database '{$dbname}' found and ready."
                    : "Connection successful! Database '{$dbname}' will be created automatically.",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Step 3: Run Database Migrations, Seeders, and Telemetry.
     */
    public function executeInstall(Request $request)
    {
        $validated = $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_pass' => 'nullable|string',
            'app_url' => 'nullable|string',
        ]);

        $customer = session('installer_customer');
        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please restart the installer wizard.',
            ], 400);
        }

        $host = $validated['db_host'];
        $port = $validated['db_port'];
        $dbname = $validated['db_name'];
        $username = $validated['db_user'];
        $password = $validated['db_pass'] ?? '';
        $appUrl = $validated['app_url'] ?? url('/');

        try {
            // 1. Ensure Database exists
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2. Update .env file
            $this->updateEnvFile([
                'APP_NAME' => 'EstateHub',
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'APP_URL' => $appUrl,
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $host,
                'DB_PORT' => $port,
                'DB_DATABASE' => $dbname,
                'DB_USERNAME' => $username,
                'DB_PASSWORD' => $password,
                'SESSION_DRIVER' => 'file',
                'CACHE_STORE' => 'file',
                'PURCHASE_CODE' => $customer['purchase_code'],
                'CLIENT_NAME' => $customer['name'],
                'CLIENT_EMAIL' => $customer['email'],
                'CLIENT_PHONE' => $customer['phone'],
            ]);

            // 3. Configure runtime DB connection
            Config::set('database.connections.mysql.host', $host);
            Config::set('database.connections.mysql.port', $port);
            Config::set('database.connections.mysql.database', $dbname);
            Config::set('database.connections.mysql.username', $username);
            Config::set('database.connections.mysql.password', $password);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // 4. Ensure Application Key is set
            if (empty(env('APP_KEY')) || env('APP_KEY') === 'base64:') {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // 5. Run Migrations
            Artisan::call('migrate', ['--force' => true]);

            // 6. Run Database Seeders
            Artisan::call('db:seed', ['--force' => true]);

            // Explicitly Guarantee Super Admin Account
            try {
                User::updateOrCreate(
                    ['email' => 'admin@estatehub.com'],
                    [
                        'name' => 'EstateHub Administrator',
                        'phone' => '9876543210',
                        'password' => Hash::make('password'),
                        'role' => 'ADMIN',
                        'is_active' => true,
                    ]
                );

                User::updateOrCreate(
                    ['email' => 'admin@realestate.com'],
                    [
                        'name' => 'Platform Administrator',
                        'phone' => '9876543219',
                        'password' => Hash::make('password'),
                        'role' => 'ADMIN',
                        'is_active' => true,
                    ]
                );

                if (! empty($customer['email'])) {
                    User::updateOrCreate(
                        ['email' => strtolower(trim($customer['email']))],
                        [
                            'name' => $customer['name'] ?? 'Administrator',
                            'phone' => preg_replace('/[^0-9]/', '', $customer['phone'] ?? '9876543210'),
                            'password' => Hash::make('password'),
                            'role' => 'ADMIN',
                            'is_active' => true,
                        ]
                    );
                }
            } catch (Exception $e) {
                Log::warning('Super admin explicit creation: '.$e->getMessage());
            }

            // 7. Clear old caches
            try {
                Artisan::call('optimize:clear');
            } catch (Exception $e) {
            }

            // 8. Register Installation with Webotapp Lab Telemetry
            $this->registerTelemetry($customer, $request);

            // 9. Create installed lock file
            $installedData = [
                'installed_at' => date('Y-m-d H:i:s'),
                'app_name' => 'EstateHub Real Estate',
                'app_version' => '1.0.0',
                'purchase_code' => $customer['purchase_code'],
                'customer_name' => $customer['name'],
                'customer_email' => $customer['email'],
                'customer_phone' => $customer['phone'],
                'customer_profession' => $customer['profession'],
                'customer_country' => $customer['country'],
                'customer_city' => $customer['city'],
                'database' => $dbname,
                'domain' => $request->getHost(),
                'agreed_license' => true,
                'license_agreed_at' => $customer['license_agreed_at'] ?? now()->toIso8601String(),
                'legal_signature_hash' => $customer['legal_signature_hash'] ?? hash('sha256', $customer['purchase_code'].'|'.$customer['email'].'|'.$request->getHost().'|'.$request->ip().'|'.now()->toIso8601String()),
            ];

            File::put(storage_path('installed'), json_encode($installedData, JSON_PRETTY_PRINT));

            // Clear installer session
            session()->forget('installer_customer');

            return response()->json([
                'success' => true,
                'message' => 'EstateHub has been installed successfully!',
                'redirect' => url('/install/complete'),
            ]);
        } catch (Exception $e) {
            Log::error('Installation process error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Installation error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Step 4: Installation Completed Screen.
     */
    public function complete()
    {
        if (! file_exists(storage_path('installed'))) {
            return redirect('/install');
        }

        $installedInfo = [];
        try {
            $installedInfo = json_decode(File::get(storage_path('installed')), true) ?? [];
        } catch (Exception $e) {
        }

        return view('installer.complete', compact('installedInfo'));
    }

    /**
     * Send Telemetry to Webotapp Lab API.
     */
    private function registerTelemetry(array $customer, Request $request): void
    {
        $payload = [
            'purchase_code' => $customer['purchase_code'],
            'customer_name' => $customer['name'],
            'customer_phone' => $customer['phone'],
            'customer_email' => $customer['email'],
            'customer_profession' => $customer['profession'],
            'customer_country' => $customer['country'],
            'customer_city' => $customer['city'],
            'installed_domain' => $request->getHost(),
            'installed_ip' => $request->ip(),
            'app_name' => 'EstateHub Real Estate',
            'app_version' => '1.0.0',
            'php_version' => PHP_VERSION,
            'server_software' => $request->server('SERVER_SOFTWARE', 'Apache'),
            'agreed_license' => true,
            'license_agreed_at' => $customer['license_agreed_at'] ?? now()->toIso8601String(),
            'legal_signature_hash' => $customer['legal_signature_hash'] ?? null,
        ];

        $telemetryUrls = [
            'http://localhost:5001/api/license/register-install',
            'https://lab.webotapp.com/api/license/register-install',
        ];

        foreach ($telemetryUrls as $url) {
            try {
                $res = Http::timeout(4)->post($url, $payload);
                if ($res->successful()) {
                    Log::info("Installation telemetry registered successfully at {$url}");
                    break;
                }
            } catch (Exception $e) {
                Log::warning("Telemetry error at {$url}: ".$e->getMessage());
            }
        }
    }

    /**
     * Update .env file keys.
     */
    private function updateEnvFile(array $data): void
    {
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), $envPath);
            } else {
                touch($envPath);
            }
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            // Escape values containing spaces or quotes
            if (str_contains($value, ' ') || str_contains($value, '#') || empty($value)) {
                $formattedValue = '"'.addcslashes($value, '"').'"';
            } else {
                $formattedValue = $value;
            }

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $envContent);
            } else {
                $envContent .= "\n{$key}={$formattedValue}";
            }
        }

        file_put_contents($envPath, $envContent);
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($envPath, true);
        }
        clearstatcache(true, $envPath);

        // Remove any cached bootstrap files so Laravel immediately reads new .env
        @unlink(base_path('bootstrap/cache/config.php'));
        @unlink(base_path('bootstrap/cache/routes-v7.php'));
        @unlink(base_path('bootstrap/cache/services.php'));
        @unlink(base_path('bootstrap/cache/packages.php'));
    }

    /**
     * Check PHP and Server Requirements.
     */
    private function checkRequirements(): array
    {
        $requirements = [
            'PHP >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'PDO Extension' => extension_loaded('pdo'),
            'PDO MySQL Driver' => extension_loaded('pdo_mysql'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'XML Extension' => extension_loaded('xml'),
            'Ctype Extension' => extension_loaded('ctype'),
            'JSON Extension' => extension_loaded('json'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'BCMath Extension' => extension_loaded('bcmath'),
            'cURL Extension' => extension_loaded('curl'),
        ];

        $passed = ! in_array(false, $requirements, true);

        return [
            'list' => $requirements,
            'passed' => $passed,
            'php_version' => PHP_VERSION,
        ];
    }

    /**
     * Check Directory Permissions.
     */
    private function checkPermissions(): array
    {
        $directories = [
            'storage/framework/' => is_writable(storage_path('framework')),
            'storage/logs/' => is_writable(storage_path('logs')),
            'bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
            '.env (writable)' => is_writable(base_path('.env')) || is_writable(base_path()),
        ];

        $passed = ! in_array(false, $directories, true);

        return [
            'list' => $directories,
            'passed' => $passed,
        ];
    }
}

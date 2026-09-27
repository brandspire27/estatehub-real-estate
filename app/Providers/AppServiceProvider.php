<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Before installation, force session & cache to file driver so web installer never tries to connect to an unconfigured database
        if (!file_exists(storage_path('installed'))) {
            config([
                'session.driver' => 'file',
                'cache.default' => 'file',
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

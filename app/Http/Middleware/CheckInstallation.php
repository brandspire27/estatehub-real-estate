<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installedFile = storage_path('installed');
        $isInstalled = file_exists($installedFile);
        $isInstallRoute = $request->is('install*') || $request->is('install');

        // If not installed and not on install route or assets, redirect to install wizard
        if (!$isInstalled && !$isInstallRoute && !$request->is('build*') && !$request->is('assets*')) {
            return redirect('/install');
        }

        // If already installed and trying to access install wizard, redirect to home
        if ($isInstalled && $isInstallRoute) {
            return redirect('/')->with('info', 'EstateHub is already installed.');
        }

        return $next($request);
    }
}

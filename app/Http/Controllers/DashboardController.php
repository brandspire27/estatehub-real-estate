<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\MatchRecord;
use App\Models\Package;
use App\Models\Property;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isStaff()) {
            return $this->staffDashboard();
        }

        if ($user->isSeller()) {
            return $this->sellerDashboard();
        }

        return $this->buyerDashboard();
    }

    public function selectPackage(Request $request): RedirectResponse
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $user = Auth::user();
        $package = Package::where('is_active', true)->findOrFail($request->package_id);

        $user->package_id = $package->id;
        $user->package_expires_at = $package->duration_days ? now()->addDays($package->duration_days) : null;
        $user->save();

        ActivityLog::record($user->id, 'PACKAGE_SUBSCRIBED', 'Package', $package->id, [
            'package_name' => $package->name,
            'price' => $package->price,
            'expires_at' => $user->package_expires_at?->toDateTimeString(),
        ]);

        return redirect()->route('dashboard')->with('success', "You have successfully activated the {$package->name} package!");
    }

    private function buyerDashboard(): View
    {
        $user = Auth::user();
        $user->load('package');

        $stats = [
            'active_requirements' => Requirement::where('user_id', $user->id)->where('status', 'ACTIVE')->count(),
            'total_requirements' => Requirement::where('user_id', $user->id)->count(),
            'total_matches' => MatchRecord::whereHas('requirement', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'high_matches' => MatchRecord::whereHas('requirement', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('score', '>=', 80)->count(),
        ];

        $recentRequirements = Requirement::where('user_id', $user->id)
            ->withCount('matches')
            ->latest()
            ->take(5)
            ->get();

        $topMatches = MatchRecord::whereHas('requirement', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with(['property.images', 'requirement'])
            ->orderByDesc('score')
            ->take(6)
            ->get();

        $availablePackages = Package::active()->whereIn('target_role', ['BUYER', 'ALL'])->orderBy('price')->get();

        return view('dashboards.buyer', compact('stats', 'recentRequirements', 'topMatches', 'availablePackages', 'user'));
    }

    private function sellerDashboard(): View
    {
        $user = Auth::user();
        $user->load('package');

        $stats = [
            'total_listings' => Property::where('user_id', $user->id)->count(),
            'published' => Property::where('user_id', $user->id)->where('status', 'PUBLISHED')->count(),
            'pending' => Property::where('user_id', $user->id)->where('status', 'PENDING_VERIFICATION')->count(),
            'sold' => Property::where('user_id', $user->id)->where('status', 'SOLD')->count(),
            'total_views' => Property::where('user_id', $user->id)->sum('views'),
            'matched_buyers' => MatchRecord::whereHas('property', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
        ];

        $recentListings = Property::where('user_id', $user->id)
            ->with(['images'])
            ->withCount('matches')
            ->latest()
            ->take(5)
            ->get();

        $buyerMatches = MatchRecord::whereHas('property', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with(['requirement.user', 'property'])
            ->orderByDesc('score')
            ->take(6)
            ->get();

        $availablePackages = Package::active()->whereIn('target_role', ['SELLER', 'ALL'])->orderBy('price')->get();

        return view('dashboards.seller', compact('stats', 'recentListings', 'buyerMatches', 'availablePackages', 'user'));
    }

    private function staffDashboard(): View
    {
        $user = Auth::user();

        $stats = [
            'clients_created' => User::where('created_by_user_id', $user->id)->count(),
            'properties_created' => Property::where('created_by_user_id', $user->id)->count(),
            'requirements_created' => Requirement::where('created_by_user_id', $user->id)->count(),
            'total_platform_matches' => MatchRecord::count(),
        ];

        $recentClients = User::where('created_by_user_id', $user->id)->latest()->take(5)->get();
        $recentProperties = Property::with(['user', 'images'])->where('created_by_user_id', $user->id)->latest()->take(5)->get();
        $pendingProperties = Property::with(['user', 'images'])
            ->where('status', 'PENDING_VERIFICATION')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboards.staff', compact('stats', 'recentClients', 'recentProperties', 'pendingProperties'));
    }

    private function adminDashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_buyers' => User::where('role', 'BUYER')->count(),
            'total_sellers' => User::where('role', 'SELLER')->count(),
            'total_staff' => User::where('role', 'STAFF')->count(),
            'total_listings' => Property::count(),
            'published_listings' => Property::where('status', 'PUBLISHED')->count(),
            'pending_listings' => Property::where('status', 'PENDING_VERIFICATION')->count(),
            'active_requirements' => Requirement::where('status', 'ACTIVE')->count(),
            'total_matches' => MatchRecord::count(),
        ];

        $recentUsers = User::latest()->take(6)->get();
        $pendingProperties = Property::with(['user', 'images'])
            ->where('status', 'PENDING_VERIFICATION')
            ->latest()
            ->take(6)
            ->get();
        $recentLogs = ActivityLog::with('user')->latest()->take(8)->get();

        return view('dashboards.admin', compact('stats', 'recentUsers', 'pendingProperties', 'recentLogs'));
    }
}

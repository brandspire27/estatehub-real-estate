<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPackageController extends Controller
{
    public function index(): View
    {
        $packages = Package::withCount('users')->latest()->paginate(10);
        $totalPackages = Package::count();
        $activePackages = Package::where('is_active', true)->count();
        $totalSubscribedUsers = Package::withCount('users')->get()->sum('users_count');

        return view('admin.packages.index', compact('packages', 'totalPackages', 'activePackages', 'totalSubscribedUsers'));
    }

    public function create(): View
    {
        return view('admin.packages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|string|in:monthly,quarterly,yearly,lifetime',
            'duration_days' => 'required|integer|min:1',
            'listing_limit' => 'required|integer', // -1 for unlimited
            'featured_limit' => 'required|integer|min:0',
            'contact_unlock_limit' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|string', // newline separated
            'badge' => 'nullable|string|max:50',
            'target_role' => 'required|string|in:SELLER,BUYER,ALL',
            'is_active' => 'boolean',
        ]);

        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', explode("\n", $validated['features']))));
        }

        $package = Package::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . rand(100, 999),
            'price' => $validated['price'],
            'billing_period' => $validated['billing_period'],
            'duration_days' => $validated['duration_days'],
            'listing_limit' => $validated['listing_limit'],
            'featured_limit' => $validated['featured_limit'],
            'contact_unlock_limit' => $validated['contact_unlock_limit'],
            'description' => $validated['description'] ?? null,
            'features' => $featuresArray,
            'badge' => $validated['badge'] ?? null,
            'target_role' => $validated['target_role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(Auth::id(), 'ADMIN_CREATED_PACKAGE', 'Package', $package->id, [
            'name' => $package->name,
            'price' => $package->price,
        ]);

        return redirect()->route('admin.packages.index')->with('success', "Package '{$package->name}' created successfully.");
    }

    public function edit(Package $package): View
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|string|in:monthly,quarterly,yearly,lifetime',
            'duration_days' => 'required|integer|min:1',
            'listing_limit' => 'required|integer',
            'featured_limit' => 'required|integer|min:0',
            'contact_unlock_limit' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'target_role' => 'required|string|in:SELLER,BUYER,ALL',
            'is_active' => 'boolean',
        ]);

        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', explode("\n", $validated['features']))));
        }

        $package->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'billing_period' => $validated['billing_period'],
            'duration_days' => $validated['duration_days'],
            'listing_limit' => $validated['listing_limit'],
            'featured_limit' => $validated['featured_limit'],
            'contact_unlock_limit' => $validated['contact_unlock_limit'],
            'description' => $validated['description'] ?? null,
            'features' => $featuresArray,
            'badge' => $validated['badge'] ?? null,
            'target_role' => $validated['target_role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(Auth::id(), 'ADMIN_UPDATED_PACKAGE', 'Package', $package->id, [
            'name' => $package->name,
            'price' => $package->price,
        ]);

        return redirect()->route('admin.packages.index')->with('success', "Package '{$package->name}' updated successfully.");
    }

    public function destroy(Package $package): RedirectResponse
    {
        $name = $package->name;
        $package->delete();

        ActivityLog::record(Auth::id(), 'ADMIN_DELETED_PACKAGE', 'Package', $package->id, [
            'name' => $name,
        ]);

        return redirect()->route('admin.packages.index')->with('success', "Package '{$name}' deleted successfully.");
    }

    public function toggleStatus(Package $package): RedirectResponse
    {
        $package->is_active = !$package->is_active;
        $package->save();

        ActivityLog::record(Auth::id(), 'ADMIN_TOGGLED_PACKAGE_STATUS', 'Package', $package->id, [
            'name' => $package->name,
            'is_active' => $package->is_active,
        ]);

        $state = $package->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.packages.index')->with('success', "Package '{$package->name}' has been {$state}.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('creator')->latest();

        if ($request->filled('role')) {
            $query->where('role', strtoupper($request->role));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'buyers' => User::where('role', 'BUYER')->count(),
            'sellers' => User::where('role', 'SELLER')->count(),
            'staff' => User::where('role', 'STAFF')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create(): View
    {
        $packages = \App\Models\Package::active()->orderBy('price')->get();
        $roles = ['BUYER', 'SELLER', 'STAFF', 'ADMIN'];
        return view('admin.users.create', compact('packages', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|min:10|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
            'role' => 'required|in:BUYER,SELLER,STAFF,ADMIN',
            'package_id' => 'nullable|exists:packages,id',
            'is_active' => 'nullable|boolean',
        ]);

        $package = null;
        if ($request->filled('package_id')) {
            $package = \App\Models\Package::find($request->package_id);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'package_id' => $package?->id,
            'package_expires_at' => $package && $package->duration_days ? now()->addDays($package->duration_days) : null,
            'created_by_user_id' => Auth::id(),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            'email_verified_at' => now(),
        ]);

        ActivityLog::record(Auth::id(), 'ADMIN_CREATED_USER', 'User', $user->id, [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' ({$user->role}) created successfully.");
    }

    public function show(User $user): View
    {
        $user->load(['properties.images', 'requirements.matches', 'creator', 'activityLogs']);
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        ActivityLog::record(Auth::id(), 'ADMIN_USER_STATUS_TOGGLED', 'User', $user->id, [
            'new_status' => $user->is_active ? 'active' : 'inactive',
        ]);

        $statusStr = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$user->name} has been {$statusStr}.");
    }
}

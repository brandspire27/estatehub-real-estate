<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\MatchRecord;
use App\Models\Property;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function users(Request $request): View
    {
        $query = User::whereIn('role', ['BUYER', 'SELLER'])->latest();

        if ($request->filled('role')) {
            $query->where('role', strtoupper($request->role));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('staff.users', compact('users'));
    }

    public function createUser(): View
    {
        return view('staff.create_user');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|min:10|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
            'role' => 'required|in:BUYER,SELLER',
        ]);

        $staffUser = Auth::user();

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_by_user_id' => $staffUser->id,
            'created_via_staff' => true,
            'is_active' => true,
        ]);

        ActivityLog::record($staffUser->id, 'STAFF_CREATED_' . $request->role, 'User', $user->id, [
            'staff_name' => $staffUser->name,
            'client_email' => $user->email,
        ]);

        return redirect()->route('staff.users')->with('success', "Client account for {$user->name} ({$user->role}) created successfully.");
    }

    public function matches(Request $request): View
    {
        $query = MatchRecord::with(['requirement.user', 'property.images', 'property.user'])
            ->orderByDesc('score');

        if ($request->filled('city')) {
            $query->whereHas('property', fn($q) => $q->where('city', $request->city));
        }

        if ($request->filled('min_score')) {
            $query->where('score', '>=', (int)$request->min_score);
        }

        $matches = $query->paginate(15)->withQueryString();

        return view('staff.matches', compact('matches'));
    }

    public function searchUsers(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $cleanDigits = preg_replace('/[^0-9]/', '', $q);

        $users = User::whereIn('role', ['BUYER', 'SELLER'])
            ->where('is_active', true)
            ->where(function ($query) use ($q, $cleanDigits) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
                if (strlen($cleanDigits) >= 2) {
                    $query->orWhere('phone', 'like', "%{$cleanDigits}%");
                }
            })
            ->limit(15)
            ->get(['id', 'name', 'phone', 'email', 'role']);

        return response()->json($users);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        // Clean and normalize phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => $cleanPhone,
            'password' => Hash::make($request->password),
            'role' => strtoupper($request->role),
            'is_active' => true,
            'created_via_staff' => false,
        ]);

        ActivityLog::record($user->id, 'USER_REGISTERED', 'User', $user->id, [
            'role' => $user->role,
            'email' => $user->email,
        ]);

        Auth::login($user);

        $roleName = ucfirst(strtolower($user->role));
        return redirect()->route('dashboard')->with('success', "Welcome to EstateHub! Your {$roleName} account is ready.");
    }
}

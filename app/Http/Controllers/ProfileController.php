<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();
        $user->load('package');

        $roleStats = [];
        $recentItems = [];

        if ($user->isSeller()) {
            $roleStats = [
                'primary' => [
                    'label' => 'Total Listings',
                    'value' => \App\Models\Property::where('user_id', $user->id)->count(),
                    'icon' => 'fa-building',
                    'color' => 'text-brand-600',
                ],
                'secondary' => [
                    'label' => 'Live on Market',
                    'value' => \App\Models\Property::where('user_id', $user->id)->where('status', 'PUBLISHED')->count(),
                    'icon' => 'fa-circle-check',
                    'color' => 'text-emerald-600',
                ],
                'tertiary' => [
                    'label' => 'Pending Review',
                    'value' => \App\Models\Property::where('user_id', $user->id)->where('status', 'PENDING_VERIFICATION')->count(),
                    'icon' => 'fa-clock',
                    'color' => 'text-amber-600',
                ],
                'quaternary' => [
                    'label' => 'Matched Buyers',
                    'value' => \App\Models\MatchRecord::whereHas('property', fn($q) => $q->where('user_id', $user->id))->count(),
                    'icon' => 'fa-users',
                    'color' => 'text-indigo-600',
                ],
            ];
            $recentItems = \App\Models\Property::where('user_id', $user->id)->latest()->take(3)->get();
        } elseif ($user->isStaff()) {
            $roleStats = [
                'primary' => [
                    'label' => 'Clients Onboarded',
                    'value' => \App\Models\User::where('created_by_user_id', $user->id)->count(),
                    'icon' => 'fa-users',
                    'color' => 'text-emerald-600',
                ],
                'secondary' => [
                    'label' => 'Properties Posted',
                    'value' => \App\Models\Property::where('created_by_user_id', $user->id)->count(),
                    'icon' => 'fa-building',
                    'color' => 'text-brand-600',
                ],
                'tertiary' => [
                    'label' => 'Requirements Filed',
                    'value' => \App\Models\Requirement::where('created_by_user_id', $user->id)->count(),
                    'icon' => 'fa-list-check',
                    'color' => 'text-indigo-600',
                ],
                'quaternary' => [
                    'label' => 'Staff KYC Status',
                    'value' => $user->kyc_status ?? 'VERIFIED',
                    'icon' => 'fa-id-badge',
                    'color' => 'text-purple-600',
                ],
            ];
            $recentItems = \App\Models\User::where('created_by_user_id', $user->id)->latest()->take(3)->get();
        } elseif ($user->isAdmin()) {
            $roleStats = [
                'primary' => [
                    'label' => 'Platform Users',
                    'value' => \App\Models\User::count(),
                    'icon' => 'fa-users-gear',
                    'color' => 'text-rose-600',
                ],
                'secondary' => [
                    'label' => 'Total Properties',
                    'value' => \App\Models\Property::count(),
                    'icon' => 'fa-building',
                    'color' => 'text-brand-600',
                ],
                'tertiary' => [
                    'label' => 'Pending Verification',
                    'value' => \App\Models\Property::where('status', 'PENDING_VERIFICATION')->count(),
                    'icon' => 'fa-clock',
                    'color' => 'text-amber-600',
                ],
                'quaternary' => [
                    'label' => 'Platform Matches',
                    'value' => \App\Models\MatchRecord::count(),
                    'icon' => 'fa-handshake',
                    'color' => 'text-emerald-600',
                ],
            ];
            $recentItems = \App\Models\ActivityLog::with('user')->latest()->take(3)->get();
        } else {
            // Buyer
            $roleStats = [
                'primary' => [
                    'label' => 'Active Requirements',
                    'value' => $user->requirements()->where('status', 'ACTIVE')->count(),
                    'icon' => 'fa-list-check',
                    'color' => 'text-brand-600',
                ],
                'secondary' => [
                    'label' => 'Qualified Matches',
                    'value' => \App\Models\MatchRecord::whereHas('requirement', fn($q) => $q->where('user_id', $user->id))->count(),
                    'icon' => 'fa-handshake',
                    'color' => 'text-emerald-600',
                ],
                'tertiary' => [
                    'label' => 'Inquiries Sent',
                    'value' => \App\Models\PropertyInquiry::where('buyer_id', $user->id)->orWhere('buyer_phone', $user->phone)->count(),
                    'icon' => 'fa-comments',
                    'color' => 'text-indigo-600',
                ],
                'quaternary' => [
                    'label' => 'KYC Status',
                    'value' => $user->kyc_status ?? 'UNVERIFIED',
                    'icon' => 'fa-shield-halved',
                    'color' => 'text-purple-600',
                ],
            ];
            $recentItems = $user->requirements()->latest()->take(3)->get();
        }

        return view('profile.edit', compact('user', 'roleStats', 'recentItems'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $data = [
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'avatar' => $request->avatar,
            'address' => $request->address,
            'emergency_contact' => $request->emergency_contact,
            'kyc_document_type' => $request->kyc_document_type,
            'kyc_document_number' => $request->kyc_document_number,
            'kyc_document_url' => $request->kyc_document_url,
        ];

        if ($request->filled('kyc_document_number') && $user->kyc_status === 'UNVERIFIED') {
            $data['kyc_status'] = 'VERIFIED';
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::record($user->id, 'PROFILE_UPDATED', 'User', $user->id, [
            'updated_by' => $user->name,
            'kyc_status' => $user->kyc_status,
        ]);

        return back()->with('success', 'Profile and account settings updated successfully.');
    }
}

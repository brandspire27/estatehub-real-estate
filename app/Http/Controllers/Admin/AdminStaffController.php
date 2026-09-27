<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function index(): View
    {
        $staffMembers = User::where('role', 'STAFF')
            ->withCount(['createdUsers'])
            ->latest()
            ->paginate(15);

        return view('admin.staff.index', compact('staffMembers'));
    }

    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|min:10|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
            'kyc_document_type' => 'nullable|string|max:50',
            'kyc_document_number' => 'nullable|string|max:100',
            'kyc_document_url' => 'nullable|string|max:2000',
            'address' => 'nullable|string|max:1000',
            'emergency_contact' => 'nullable|string|max:50',
        ]);

        $staff = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'password' => Hash::make($request->password),
            'role' => 'STAFF',
            'created_by_user_id' => Auth::id(),
            'is_active' => true,
            'kyc_status' => $request->filled('kyc_document_number') ? 'VERIFIED' : 'UNVERIFIED',
            'kyc_document_type' => $request->kyc_document_type,
            'kyc_document_number' => $request->kyc_document_number,
            'kyc_document_url' => $request->kyc_document_url,
            'address' => $request->address,
            'emergency_contact' => $request->emergency_contact,
            'email_verified_at' => now(),
        ]);

        ActivityLog::record(Auth::id(), 'ADMIN_CREATED_STAFF', 'User', $staff->id, [
            'staff_name' => $staff->name,
            'staff_email' => $staff->email,
            'kyc_status' => $staff->kyc_status,
            'kyc_doc_type' => $staff->kyc_document_type,
        ]);

        return redirect()->route('admin.staff.index')->with('success', "Staff member {$staff->name} created with KYC status: {$staff->kyc_status}.");
    }

    public function activity(User $staff): View
    {
        $logs = ActivityLog::where('user_id', $staff->id)->latest()->paginate(20);
        return view('admin.staff.activity', compact('staff', 'logs'));
    }
}

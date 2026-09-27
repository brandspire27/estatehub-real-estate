@extends('layouts.dashboard')

@section('title', 'Admin - Staff Directory')
@section('page-title', 'Staff Management')
@section('page-subtitle', 'Staff members onboard clients and post listings/requirements on their behalf')

@section('content')
<div class="space-y-6">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between">
        <div class="text-xs font-semibold text-slate-500">
            Total Staff Members: <span class="font-bold text-slate-900">{{ $staffMembers->total() }}</span>
        </div>

        <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition">
            <i class="fa-solid fa-user-plus"></i>
            <span>Add Staff Member</span>
        </a>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($staffMembers->isEmpty())
            <div class="text-center py-20 p-6 text-slate-400">
                <i class="fa-solid fa-id-badge text-4xl mb-3 block"></i>
                <p class="text-sm font-bold text-slate-700">No staff members created yet</p>
                <a href="{{ route('admin.staff.create') }}" class="mt-2 inline-block text-xs font-bold text-brand-600 hover:underline">+ Create Staff Account</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Staff Member</th>
                            <th class="py-3.5 px-4 font-bold">Email</th>
                            <th class="py-3.5 px-4 font-bold">Phone</th>
                            <th class="py-3.5 px-4 font-bold">Clients Onboarded</th>
                            <th class="py-3.5 px-4 font-bold">KYC Status</th>
                            <th class="py-3.5 px-4 font-bold">Status</th>
                            <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($staffMembers as $staff)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-purple-700 text-white flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($staff->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $staff->name }}</div>
                                        @if($staff->emergency_contact)
                                            <div class="text-[10px] text-slate-400">Emerg: {{ $staff->emergency_contact }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-slate-600">{{ $staff->email }}</td>
                                <td class="py-4 px-4 font-semibold text-slate-900">{{ $staff->phone }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                        {{ $staff->created_users_count }} Clients
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    @if($staff->kyc_status === 'VERIFIED')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 flex items-center gap-1 w-fit">
                                            <i class="fa-solid fa-shield-check text-[9px]"></i>
                                            <span>{{ $staff->kyc_document_type ?? 'Verified' }}</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($staff->is_active)
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Active</span>
                                    @else
                                        <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full">Suspended</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.staff.activity', $staff) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                            Activity Trail
                                        </a>
                                        <form action="{{ route('admin.users.toggle', $staff) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition {{ $staff->is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-600' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}">
                                                {{ $staff->is_active ? 'Suspend' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

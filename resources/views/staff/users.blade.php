@extends('layouts.dashboard')

@section('title', 'Client Accounts')
@section('page-title', 'Client Directory')
@section('page-subtitle', 'Manage Buyer and Seller accounts registered by staff')

@section('content')
<div class="space-y-6">
    
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search & Filter -->
        <form action="{{ route('staff.users') }}" method="GET" class="flex items-center gap-2 text-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client name, email, phone..." 
                   class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs w-64 focus:outline-none focus:ring-2 focus:ring-brand-500">
            
            <select name="role" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                <option value="">All Roles</option>
                <option value="BUYER" {{ request('role') === 'BUYER' ? 'selected' : '' }}>Buyers</option>
                <option value="SELLER" {{ request('role') === 'SELLER' ? 'selected' : '' }}>Sellers</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-brand-600 transition">
                Search
            </button>
        </form>

        <a href="{{ route('staff.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition flex-shrink-0">
            <i class="fa-solid fa-user-plus"></i>
            <span>Register New Client</span>
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($users->isEmpty())
            <div class="text-center py-20 p-6 text-slate-400">
                <i class="fa-solid fa-users text-4xl mb-3 block"></i>
                <p class="text-sm font-bold text-slate-700">No clients found</p>
                <a href="{{ route('staff.users.create') }}" class="mt-2 inline-block text-xs font-bold text-brand-600 hover:underline">+ Register Client</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Client Name</th>
                            <th class="py-3.5 px-4 font-bold">Role</th>
                            <th class="py-3.5 px-4 font-bold">Email</th>
                            <th class="py-3.5 px-4 font-bold">Phone Number</th>
                            <th class="py-3.5 px-4 font-bold">Registration Source</th>
                            <th class="py-3.5 px-6 font-bold text-right">Date Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($users as $u)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $u->role === 'BUYER' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-600">{{ $u->email }}</td>
                                <td class="py-4 px-4 font-semibold text-slate-900">
                                    <a href="tel:{{ $u->phone }}" class="hover:text-brand-600 flex items-center gap-1">
                                        <i class="fa-solid fa-phone text-[10px] text-slate-400"></i>
                                        <span>{{ $u->phone }}</span>
                                    </a>
                                </td>
                                <td class="py-4 px-4">
                                    @if($u->created_via_staff)
                                        <span class="text-[10px] font-bold text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded-full">
                                            Staff Registered
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400">Self Registered</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right text-slate-400">
                                    {{ $u->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

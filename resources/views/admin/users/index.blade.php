@extends('layouts.dashboard')

@section('title', 'Admin - User Directory')
@section('page-title', 'User Management')
@section('page-subtitle', 'Manage buyers, sellers, staff, and account permissions')

@section('content')
<div class="space-y-6">
    
    <!-- Header Action Ribbon -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-semibold">
            <span>Total Users: <strong class="text-slate-900">{{ $stats['total'] }}</strong></span>
            <span class="text-slate-300">&bull;</span>
            <span>Buyers: <strong class="text-indigo-600">{{ $stats['buyers'] }}</strong></span>
            <span class="text-slate-300">&bull;</span>
            <span>Sellers: <strong class="text-emerald-600">{{ $stats['sellers'] }}</strong></span>
            <span class="text-slate-300">&bull;</span>
            <span>Staff: <strong class="text-purple-600">{{ $stats['staff'] }}</strong></span>
        </div>

        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-xs shadow-md shadow-brand-500/20 transition flex-shrink-0">
            <i class="fa-solid fa-user-plus"></i>
            <span>Create New User</span>
        </a>
    </div>

    <!-- Top Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-end gap-4 text-xs font-semibold">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Search User</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or phone..." 
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Filter by Role</label>
                <select name="role" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">All Roles</option>
                    <option value="BUYER" {{ request('role') === 'BUYER' ? 'selected' : '' }}>Buyer</option>
                    <option value="SELLER" {{ request('role') === 'SELLER' ? 'selected' : '' }}>Seller</option>
                    <option value="STAFF" {{ request('role') === 'STAFF' ? 'selected' : '' }}>Staff</option>
                    <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Status</label>
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-brand-600 transition">
                Apply Filters
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                        <th class="py-3.5 px-6 font-bold">User</th>
                        <th class="py-3.5 px-4 font-bold">Role</th>
                        <th class="py-3.5 px-4 font-bold">Phone Number</th>
                        <th class="py-3.5 px-4 font-bold">Registered Via</th>
                        <th class="py-3.5 px-4 font-bold">Status</th>
                        <th class="py-3.5 px-4 font-bold">Joined</th>
                        <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($u->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $u) }}" class="font-bold text-slate-900 hover:text-brand-600">
                                        {{ $u->name }}
                                    </a>
                                    <div class="text-[11px] text-slate-500">{{ $u->email }}</div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black 
                                    {{ $u->role === 'ADMIN' ? 'bg-rose-100 text-rose-800' : '' }}
                                    {{ $u->role === 'STAFF' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $u->role === 'SELLER' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $u->role === 'BUYER' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-900">{{ $u->phone }}</td>
                            <td class="py-4 px-4 text-slate-500">
                                @if($u->creator)
                                    <span class="text-[10px] text-purple-700 font-semibold">Staff ({{ $u->creator->name }})</span>
                                @else
                                    <span class="text-[10px] text-slate-400">Direct Self-Registration</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Active</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Suspended</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-400">{{ $u->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.show', $u) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="View Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.toggle', $u) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition {{ $u->is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-600' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}">
                                                {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection

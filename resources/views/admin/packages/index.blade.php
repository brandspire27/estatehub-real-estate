@extends('layouts.dashboard')

@section('title', 'Admin - Package Management')
@section('page-title', 'Subscription Packages')
@section('page-subtitle', 'Manage pricing tiers, listing quotas, contact unlock limits, and package perks')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Packages</div>
                <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalPackages }}</div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Packages</div>
                <div class="text-2xl font-black text-emerald-600 mt-0.5">{{ $activePackages }}</div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Subscribed Members</div>
                <div class="text-2xl font-black text-indigo-600 mt-0.5">{{ $totalSubscribedUsers }}</div>
            </div>
        </div>
    </div>

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm">
        <div>
            <h3 class="font-bold text-slate-900 text-base">All Platform Packages</h3>
            <p class="text-xs text-slate-500 mt-0.5">Sellers and buyers choose from these packages upon signup and from their dashboard</p>
        </div>
        <a href="{{ route('admin.packages.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Create New Package</span>
        </a>
    </div>

    <!-- Packages Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Package Name & Badge</th>
                        <th class="py-4 px-6">Price / Period</th>
                        <th class="py-4 px-6">Listing Quota</th>
                        <th class="py-4 px-6">Perks & Limits</th>
                        <th class="py-4 px-6">Target Role</th>
                        <th class="py-4 px-6">Subscribers</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($packages as $package)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Name -->
                            <td class="py-4 px-6">
                                <div class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                    <span>{{ $package->name }}</span>
                                    @if($package->badge)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                            {{ $package->badge }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 max-w-xs">
                                    {{ $package->description }}
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="py-4 px-6">
                                <div class="font-black text-slate-900 text-sm">
                                    {{ $package->formatted_price }}
                                </div>
                                <div class="text-[10px] text-slate-400 capitalize font-medium">
                                    {{ $package->billing_period }} ({{ $package->duration_days }} days)
                                </div>
                            </td>

                            <!-- Listing Quota -->
                            <td class="py-4 px-6">
                                @if($package->isUnlimitedListings())
                                    <span class="inline-flex items-center gap-1 font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100 text-[11px]">
                                        <i class="fa-solid fa-infinity"></i> Unlimited
                                    </span>
                                @else
                                    <span class="font-extrabold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg text-xs">
                                        {{ $package->listing_limit }} Listings
                                    </span>
                                @endif
                            </td>

                            <!-- Perks -->
                            <td class="py-4 px-6">
                                <div class="space-y-1">
                                    <div class="text-[11px] text-slate-600 font-medium">
                                        <i class="fa-solid fa-star text-amber-500 mr-1"></i> {{ $package->featured_limit }} Featured Listings
                                    </div>
                                    <div class="text-[11px] text-slate-600 font-medium">
                                        <i class="fa-solid fa-address-book text-brand-600 mr-1"></i> {{ $package->contact_unlock_limit }} Contact Unlocks
                                    </div>
                                </div>
                            </td>

                            <!-- Target Role -->
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $package->target_role === 'SELLER' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '' }}
                                    {{ $package->target_role === 'BUYER' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                    {{ $package->target_role === 'ALL' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}">
                                    {{ $package->target_role }}
                                </span>
                            </td>

                            <!-- Subscribers -->
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-700">{{ $package->users_count }} members</span>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6">
                                <form action="{{ route('admin.packages.toggle', $package) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition
                                        {{ $package->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}"
                                        title="Click to toggle status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $package->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $package->is_active ? 'Active' : 'Disabled' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="p-2 rounded-xl text-slate-600 hover:text-brand-600 hover:bg-brand-50 transition" title="Edit Package">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete Package">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2"></i>
                                <p class="text-sm font-semibold">No subscription packages found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($packages->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $packages->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

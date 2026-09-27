@extends('layouts.dashboard')

@section('title', 'Seller Dashboard')
@section('page-title', 'Seller Dashboard')
@section('page-subtitle', 'Manage your property listings and see matched buyer demand')

@section('content')
<div class="space-y-8" x-data="{ packageModalOpen: {{ !$user->package_id ? 'true' : 'false' }} }">
    
    <!-- Membership Plan Status Banner -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 text-white flex items-center justify-center text-xl shadow-md shadow-brand-500/20">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Current Membership</span>
                        @if($user->package)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                {{ $user->package->name }}
                            </span>
                            @if($user->package->badge)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                                    {{ $user->package->badge }}
                                </span>
                            @endif
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                No Package Selected
                            </span>
                        @endif
                    </div>
                    <div class="text-slate-600 text-xs sm:text-sm mt-0.5 flex flex-wrap items-center gap-3">
                        @if($user->package)
                            <span>Listing Limit: <strong>{{ $user->package->isUnlimitedListings() ? 'Unlimited' : $user->package->listing_limit }}</strong></span>
                            <span class="text-slate-300">&bull;</span>
                            <span>Featured Slots: <strong>{{ $user->package->featured_limit }}</strong></span>
                            <span class="text-slate-300">&bull;</span>
                            <span>Valid Until: <strong>{{ $user->package_expires_at ? $user->package_expires_at->format('d M, Y') : 'Lifetime' }}</strong></span>
                        @else
                            <span class="text-rose-600 font-semibold">Select a package to start listing properties and connecting with buyers.</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-shrink-0">
                <button type="button" @click="packageModalOpen = true" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>{{ $user->package ? 'Change / Upgrade Package' : 'Select Package Now' }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Listings</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_listings'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Published</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['published'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Pending Review</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sold</div>
            <div class="text-2xl font-black text-slate-700 mt-1">{{ $stats['sold'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-brand-600 uppercase tracking-wider">Total Views</div>
            <div class="text-2xl font-black text-brand-600 mt-1">{{ number_format($stats['total_views']) }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Matched Buyers</div>
            <div class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['matched_buyers'] }}</div>
        </div>
    </div>

    <!-- Call to action: Create Listing -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <h3 class="text-xl font-black tracking-tight">List a new property for sale</h3>
            <p class="text-slate-300 text-xs sm:text-sm mt-1">Publish specifications, photos, and price to immediately trigger buyer matches across India.</p>
        </div>
        <a href="{{ route('listings.create') }}" class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs shadow-md transition flex items-center gap-2 flex-shrink-0">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>Add New Listing</span>
        </a>
    </div>

    <!-- 2 Column Layout: Recent Listings & Matched Buyers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Recent Listings -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-house-chimney text-brand-600"></i>
                    <h3 class="font-bold text-slate-900 text-base">Recent Listings</h3>
                </div>
                <a href="{{ route('listings.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            @if($recentListings->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fa-regular fa-building text-3xl mb-2"></i>
                    <p class="text-xs font-medium">You have not created any listings yet.</p>
                    <a href="{{ route('listings.create') }}" class="mt-3 inline-block text-xs font-bold text-brand-600 hover:underline">
                        + Add Your First Listing
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentListings as $prop)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition flex items-center gap-3.5">
                            <img src="{{ $prop->primary_image_url }}" alt="{{ $prop->title }}" class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $prop->title }}</h4>
                                </div>
                                <div class="text-xs font-extrabold text-slate-800 mt-0.5">
                                    {{ $prop->formatted_price }} &bull; {{ $prop->city }}
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                        {{ $prop->status === 'PUBLISHED' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $prop->status === 'PENDING_VERIFICATION' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $prop->status === 'DRAFT' ? 'bg-slate-200 text-slate-700' : '' }}
                                        {{ $prop->status === 'SOLD' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                        {{ $prop->status }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $prop->matches_count }} buyer matches
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1 text-right flex-shrink-0">
                                <a href="{{ route('listings.edit', $prop) }}" class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-brand-600 text-xs">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Matched Buyers Demands -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-500"></i>
                    <h3 class="font-bold text-slate-900 text-base">Matched Buyers</h3>
                </div>
                <a href="{{ route('matches.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            @if($buyerMatches->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fa-solid fa-handshake-simple text-3xl mb-2 text-indigo-400"></i>
                    <p class="text-xs font-medium">No active buyer matches found yet.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">As buyers post matching criteria, they will appear here.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($buyerMatches as $bm)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">
                                        {{ substr($bm->requirement->user->name ?? 'B', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-900 truncate">{{ $bm->requirement->user->name ?? 'Buyer' }}</span>
                                </div>
                                <div class="text-xs text-slate-600 mt-1">
                                    Seeking: {{ $bm->requirement->bhk ? $bm->requirement->bhk . ' BHK ' : '' }}{{ $bm->requirement->property_type }} in {{ $bm->requirement->city }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Budget: {{ $bm->requirement->formatted_budget }} &bull; For: <span class="font-medium text-slate-700">{{ $bm->property->title }}</span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-800 mb-1">
                                    {{ $bm->score }}% Match
                                </span>
                                <div>
                                    <a href="tel:{{ $bm->requirement->user->phone ?? '' }}" class="text-[11px] font-bold text-brand-600 hover:underline">
                                        Call Buyer &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Package Selector Modal -->
    @include('dashboards.partials.package_selector_modal', ['availablePackages' => $availablePackages, 'user' => $user])

</div>
@endsection

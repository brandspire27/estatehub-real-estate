@extends('layouts.dashboard')

@section('title', 'Buyer Dashboard')
@section('page-title', 'Buyer Dashboard')
@section('page-subtitle', 'Track your requirements and real-time property matches')

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
                            <span>Buyer Inquiries / Phone Unlocks: <strong>{{ $user->package->contact_unlock_limit }}</strong></span>
                            <span class="text-slate-300">&bull;</span>
                            <span>Valid Until: <strong>{{ $user->package_expires_at ? $user->package_expires_at->format('d M, Y') : 'Lifetime' }}</strong></span>
                        @else
                            <span class="text-rose-600 font-semibold">Select a package to unlock verified seller contact details and priority matching.</span>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['active_requirements'] }}</div>
                <div class="text-xs font-semibold text-slate-500">Active Requirements</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_matches'] }}</div>
                <div class="text-xs font-semibold text-slate-500">Total Matches</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['high_matches'] }}</div>
                <div class="text-xs font-semibold text-slate-500">High Matches (80%+)</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_requirements'] }}</div>
                <div class="text-xs font-semibold text-slate-500">All Requirements</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Banner -->
    <div class="bg-gradient-to-r from-brand-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-1 text-center sm:text-left">
            <h3 class="text-xl font-black tracking-tight">Need to find your ideal property faster?</h3>
            <p class="text-slate-300 text-xs sm:text-sm">Post your exact budget and location criteria to let our match engine notify sellers.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('requirements.create') }}" class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Post Requirement</span>
            </a>
            <a href="{{ route('properties.index') }}" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Browse All</span>
            </a>
        </div>
    </div>

    <!-- 2 Column Layout: Recent Requirements & Top Matches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Recent Requirements -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-brand-600"></i>
                    <h3 class="font-bold text-slate-900 text-base">My Recent Requirements</h3>
                </div>
                <a href="{{ route('requirements.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            @if($recentRequirements->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fa-regular fa-folder-open text-3xl mb-2"></i>
                    <p class="text-xs font-medium">You have not posted any requirements yet.</p>
                    <a href="{{ route('requirements.create') }}" class="mt-3 inline-block text-xs font-bold text-brand-600 hover:underline">
                        + Post Your First Requirement
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentRequirements as $req)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-extrabold text-slate-900">
                                        {{ $req->bhk ? $req->bhk . ' BHK ' : '' }}{{ $req->property_type }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $req->status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $req->status }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500">
                                    <i class="fa-solid fa-location-dot text-brand-600 mr-1"></i>{{ $req->city }} &bull; Budget: {{ $req->formatted_budget }}
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <a href="{{ route('requirements.matches', $req) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs transition">
                                    <i class="fa-solid fa-bolt text-amber-500"></i>
                                    <span>{{ $req->matches_count }} Matches</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Top Property Matches -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-amber-500"></i>
                    <h3 class="font-bold text-slate-900 text-base">Top Property Matches</h3>
                </div>
                <a href="{{ route('matches.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            @if($topMatches->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fa-solid fa-wand-magic-sparkles text-3xl mb-2 text-amber-400"></i>
                    <p class="text-xs font-medium">No matching properties found yet.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Post an active requirement to trigger automated matching.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($topMatches as $match)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition flex items-center gap-3.5">
                            <img src="{{ $match->property->primary_image_url }}" alt="{{ $match->property->title }}" class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">
                                        <a href="{{ route('properties.show', $match->property) }}" class="hover:text-brand-600">
                                            {{ $match->property->title }}
                                        </a>
                                    </h4>
                                </div>
                                <div class="text-xs font-extrabold text-slate-800 mt-0.5">
                                    {{ $match->property->formatted_price }} &bull; {{ $match->property->city }}
                                </div>
                                <div class="text-[10px] text-slate-400 truncate mt-0.5">
                                    Matched with: {{ $match->requirement->property_type }} in {{ $match->requirement->city }}
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-black {{ $match->score >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $match->score }}% Match
                                </span>
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

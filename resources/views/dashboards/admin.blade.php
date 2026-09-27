@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('page-title', 'Platform Administration')
@section('page-subtitle', 'Real-time overview of users, property moderation, and match activity')

@section('content')
<div class="space-y-8">
    
        <!-- System Version & Auto-Update Notification Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 border border-slate-800 p-4 sm:p-5 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-neon/10 border border-neon/30 flex items-center justify-center text-neon font-black text-lg">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-black text-sm text-white">EstateHub Platform Status</span>
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">v1.0.0</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Auto-Update Engine Active
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Check official releases, run database schema patches, and execute 1-click system updates.</p>
            </div>
        </div>
        <a href="{{ route('admin.updates.index') }}" class="px-4 py-2.5 rounded-xl bg-neon hover:bg-neon/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-arrows-rotate"></i>
            Check Updates
        </a>
    </div>

<!-- Stats Row -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Users</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_users'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">
                {{ $stats['total_buyers'] }} Buyers &bull; {{ $stats['total_sellers'] }} Sellers &bull; {{ $stats['total_staff'] }} Staff
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-brand-600 uppercase tracking-wider">Listings</div>
            <div class="text-2xl font-black text-brand-700 mt-1">{{ $stats['total_listings'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">
                <span class="text-emerald-600 font-bold">{{ $stats['published_listings'] }} Published</span> &bull; 
                <span class="text-amber-600 font-bold">{{ $stats['pending_listings'] }} Pending</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Active Requirements</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $stats['active_requirements'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Participating in live matching</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Platform Matches</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['total_matches'] }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Algorithmic connections</div>
        </div>
    </div>

    <!-- Pending Verification Moderation Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
                <h3 class="font-bold text-slate-900 text-base">Pending Verification Listings ({{ $pendingProperties->count() }})</h3>
            </div>
            <a href="{{ route('admin.properties.index', ['status' => 'PENDING_VERIFICATION']) }}" class="text-xs font-bold text-brand-600 hover:underline">
                All Pending &rarr;
            </a>
        </div>

        @if($pendingProperties->isEmpty())
            <div class="text-center py-10 text-slate-400 text-xs">
                <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-1.5 block"></i>
                All submitted listings are reviewed! No pending verifications.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-3 font-bold">Property</th>
                            <th class="pb-3 font-bold">Seller</th>
                            <th class="pb-3 font-bold">City</th>
                            <th class="pb-3 font-bold">Price</th>
                            <th class="pb-3 font-bold">Submitted</th>
                            <th class="pb-3 font-bold text-right">Moderation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($pendingProperties as $prop)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 font-bold text-slate-900 flex items-center gap-2">
                                    <img src="{{ $prop->primary_image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                    <a href="{{ route('properties.show', $prop) }}" target="_blank" class="hover:text-brand-600 truncate max-w-xs">
                                        {{ $prop->title }}
                                    </a>
                                </td>
                                <td class="py-3 text-slate-600">{{ $prop->user->name ?? 'Unknown' }}</td>
                                <td class="py-3 text-slate-600">{{ $prop->city }}</td>
                                <td class="py-3 font-bold text-slate-900">{{ $prop->formatted_price }}</td>
                                <td class="py-3 text-slate-400">{{ $prop->created_at->diffForHumans() }}</td>
                                <td class="py-3 text-right">
                                    <form action="{{ route('admin.properties.approve', $prop) }}" method="POST" class="inline-flex gap-1.5">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-sm flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i>
                                            <span>Approve & Publish</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 2 Column: Recent Users & Recent Audit Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Recent Users -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-base">Recent Users</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-brand-600 hover:underline">User Directory &rarr;</a>
            </div>

            <div class="space-y-3">
                @foreach($recentUsers as $u)
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs uppercase">
                                {{ substr($u->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                <div class="text-slate-500 text-[11px]">{{ $u->email }} &bull; {{ $u->phone }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded-full font-bold text-[10px] 
                                {{ $u->role === 'ADMIN' ? 'bg-rose-100 text-rose-800' : '' }}
                                {{ $u->role === 'STAFF' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $u->role === 'SELLER' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $u->role === 'BUYER' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                {{ $u->role }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $u->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-base">System Audit Trail</h3>
                <a href="{{ route('admin.logs.index') }}" class="text-xs font-bold text-brand-600 hover:underline">Full Log &rarr;</a>
            </div>

            <div class="space-y-3">
                @foreach($recentLogs as $log)
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-brand-700 font-mono">{{ $log->action }}</span>
                            <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-slate-600 text-[11px] mt-1">
                            By <span class="font-semibold text-slate-800">{{ $log->user->name ?? 'System' }}</span>
                            @if($log->entity_type)
                                &bull; {{ $log->entity_type }} #{{ $log->entity_id }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

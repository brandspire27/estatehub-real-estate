@extends('layouts.dashboard')

@section('title', 'Admin - Platform Matches')
@section('page-title', 'Platform-Wide Matches')
@section('page-subtitle', 'Complete database of automated matches computed between buyer demand and seller listings')

@section('content')
<div class="space-y-6">
    
    <!-- Top Bar: Filters + Recalculate Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        
        <!-- Recalculate All Button -->
        <form action="{{ route('admin.matches.recalculate') }}" method="POST" onsubmit="return confirm('Run multi-parameter matching algorithm across all active requirements and published properties?');">
            @csrf
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Recalculate All Platform Matches</span>
            </button>
        </form>

        <!-- Filter Form -->
        <form action="{{ route('admin.matches.index') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
            <select name="city" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                <option value="">All Cities</option>
                @foreach($cities as $c)
                    <option value="{{ $c }}" {{ request('city') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>

            <select name="min_score" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                <option value="">Any Score</option>
                <option value="60" {{ request('min_score') == '60' ? 'selected' : '' }}>60%+</option>
                <option value="75" {{ request('min_score') == '75' ? 'selected' : '' }}>75%+ (Good)</option>
                <option value="85" {{ request('min_score') == '85' ? 'selected' : '' }}>85%+ (High)</option>
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Buyer or Seller..." 
                   class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-brand-600 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Matches Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($matches->isEmpty())
            <div class="text-center py-20 p-6 text-slate-400">
                <i class="fa-solid fa-handshake-simple text-4xl mb-3 block"></i>
                <p class="text-sm font-bold text-slate-700">No match records found</p>
                <p class="text-xs text-slate-400 mt-1">Click "Recalculate All Platform Matches" above to generate matches.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Buyer & Requirement</th>
                            <th class="py-3.5 px-4 font-bold">Seller & Listing</th>
                            <th class="py-3.5 px-4 font-bold">City</th>
                            <th class="py-3.5 px-4 font-bold">Property Type</th>
                            <th class="py-3.5 px-4 font-bold">Price vs Budget</th>
                            <th class="py-3.5 px-4 font-bold">Match Score</th>
                            <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($matches as $m)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ $m->requirement->user->name ?? 'Buyer' }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $m->requirement->bhk ? $m->requirement->bhk . ' BHK ' : '' }}{{ $m->requirement->property_type }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $m->requirement->user->phone ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $m->property->user->name ?? 'Seller' }}</div>
                                    <a href="{{ route('properties.show', $m->property) }}" target="_blank" class="text-[11px] text-brand-600 hover:underline block truncate max-w-[180px]">
                                        {{ $m->property->title }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">{{ $m->property->user->phone ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4 font-semibold text-slate-800">
                                    {{ $m->property->city }}
                                </td>
                                <td class="py-4 px-4 text-slate-700">
                                    {{ $m->property->property_type }}
                                    @if($m->property->bhk)
                                        <div class="text-[10px] text-slate-400">{{ $m->property->bhk }} BHK</div>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-black text-slate-900">{{ $m->property->formatted_price }}</div>
                                    <div class="text-[10px] text-slate-400">Req: {{ $m->requirement->formatted_budget }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-black 
                                        {{ $m->score >= 85 ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $m->score >= 70 && $m->score < 85 ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $m->score < 70 ? 'bg-slate-200 text-slate-800' : '' }}">
                                        {{ $m->score }}%
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('properties.show', $m->property) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $matches->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

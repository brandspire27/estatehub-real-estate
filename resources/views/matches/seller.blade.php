@extends('layouts.dashboard')

@section('title', 'Buyer Matches')
@section('page-title', 'Matched Buyer Requirements')
@section('page-subtitle', 'Qualified buyers searching for properties matching your listings')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form action="{{ route('matches.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4 text-xs font-semibold">
            <!-- Filter by Property -->
            <div class="w-full sm:w-1/3">
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Filter by My Property</label>
                <select name="property_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">All My Properties</option>
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>
                            {{ $prop->title }} ({{ $prop->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Min Score -->
            <div class="w-full sm:w-1/4">
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Minimum Match Score</label>
                <select name="min_score" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">Any Score (35%+)</option>
                    <option value="60" {{ request('min_score') == '60' ? 'selected' : '' }}>60% & Above</option>
                    <option value="75" {{ request('min_score') == '75' ? 'selected' : '' }}>75% & Above (High)</option>
                    <option value="85" {{ request('min_score') == '85' ? 'selected' : '' }}>85% & Above (Exact)</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-brand-600 transition flex-shrink-0">
                Filter Demands
            </button>
        </form>
    </div>

    <!-- Matches Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($matches->isEmpty())
            <div class="text-center py-20 p-6">
                <i class="fa-solid fa-users text-4xl text-slate-300 mb-3 block"></i>
                <h4 class="text-base font-bold text-slate-800">No buyer matches found for the selected filter</h4>
                <p class="text-xs text-slate-500 mt-1">When buyers post criteria matching your property, their direct contact details appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Buyer Details</th>
                            <th class="py-3.5 px-4 font-bold">Buyer Seeking</th>
                            <th class="py-3.5 px-4 font-bold">Budget Range</th>
                            <th class="py-3.5 px-4 font-bold">Matched Property</th>
                            <th class="py-3.5 px-4 font-bold">Score</th>
                            <th class="py-3.5 px-6 font-bold text-right">Direct Contact</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($matches as $m)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900 text-sm">{{ $m->requirement->user->name ?? 'Buyer' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $m->requirement->user->email ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-slate-800">
                                        {{ $m->requirement->bhk ? $m->requirement->bhk . ' BHK ' : '' }}{{ $m->requirement->property_type }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        <i class="fa-solid fa-location-dot text-brand-600 mr-0.5"></i>{{ $m->requirement->city }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-black text-slate-900 text-xs">
                                    {{ $m->requirement->formatted_budget }}
                                </td>
                                <td class="py-4 px-4">
                                    <a href="{{ route('properties.show', $m->property) }}" target="_blank" class="font-semibold text-brand-600 hover:underline block truncate max-w-[180px]">
                                        {{ $m->property->title }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">{{ $m->property->formatted_price }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-black 
                                        {{ $m->score >= 85 ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $m->score >= 70 && $m->score < 85 ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $m->score < 70 ? 'bg-slate-200 text-slate-800' : '' }}">
                                        {{ $m->score }}% Match
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="tel:{{ $m->requirement->user->phone ?? '' }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-sm hover:bg-emerald-700 transition">
                                            <i class="fa-solid fa-phone"></i>
                                            <span>Call: {{ $m->requirement->user->phone ?? 'Call' }}</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($matches->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $matches->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

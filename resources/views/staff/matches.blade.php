@extends('layouts.dashboard')

@section('title', 'Match Operations Center')
@section('page-title', 'Match Operations Center')
@section('page-subtitle', 'Monitor algorithmic pairings between active buyers and sellers')

@section('content')
<div class="space-y-6">
    
    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form action="{{ route('staff.matches') }}" method="GET" class="flex flex-wrap items-end gap-4 text-xs font-semibold">
            <div>
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">City Filter</label>
                <input type="text" name="city" value="{{ request('city') }}" placeholder="e.g. Guwahati, Mumbai..." 
                       class="px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Min Score</label>
                <select name="min_score" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">Any (35%+)</option>
                    <option value="60" {{ request('min_score') == '60' ? 'selected' : '' }}>60%+</option>
                    <option value="80" {{ request('min_score') == '80' ? 'selected' : '' }}>80%+ (High)</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-brand-600 transition">
                Filter Matches
            </button>
        </form>
    </div>

    <!-- Matches Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($matches->isEmpty())
            <div class="text-center py-20 p-6 text-slate-400">
                <i class="fa-solid fa-handshake-angle text-4xl mb-3 block"></i>
                <p class="text-sm font-bold text-slate-700">No matches found</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Buyer Demands</th>
                            <th class="py-3.5 px-4 font-bold">Matched Property</th>
                            <th class="py-3.5 px-4 font-bold">Seller</th>
                            <th class="py-3.5 px-4 font-bold">Compatibility</th>
                            <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($matches as $m)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900 text-xs">{{ $m->requirement->user->name ?? 'Buyer' }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $m->requirement->bhk ? $m->requirement->bhk . ' BHK ' : '' }}{{ $m->requirement->property_type }} &bull; {{ $m->requirement->city }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">Budget: {{ $m->requirement->formatted_budget }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <a href="{{ route('properties.show', $m->property) }}" target="_blank" class="font-bold text-slate-900 hover:text-brand-600 truncate block max-w-[200px]">
                                        {{ $m->property->title }}
                                    </a>
                                    <div class="text-[11px] text-slate-500">{{ $m->property->formatted_price }} &bull; {{ $m->property->city }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-slate-800">{{ $m->property->user->name ?? 'Seller' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $m->property->user->phone ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-black 
                                        {{ $m->score >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $m->score }}% Match
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('properties.show', $m->property) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                        Open Property
                                    </a>
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

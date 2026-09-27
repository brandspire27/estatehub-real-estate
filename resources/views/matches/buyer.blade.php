@extends('layouts.dashboard')

@section('title', 'Property Matches')
@section('page-title', 'Smart Property Matches')
@section('page-subtitle', 'Real-time properties aligned with your active budget and housing preferences')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form action="{{ route('matches.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4 text-xs font-semibold">
            <!-- Filter by Requirement -->
            <div class="w-full sm:w-1/3">
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Filter by Requirement</label>
                <select name="requirement_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">All My Requirements</option>
                    @foreach($requirements as $req)
                        <option value="{{ $req->id }}" {{ request('requirement_id') == $req->id ? 'selected' : '' }}>
                            {{ $req->bhk ? $req->bhk . ' BHK ' : '' }}{{ $req->property_type }} in {{ $req->city }} ({{ $req->formatted_budget }})
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
                Filter Matches
            </button>
        </form>
    </div>

    <!-- Match Grid -->
    @if($matches->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300 p-6">
            <i class="fa-solid fa-wand-magic-sparkles text-4xl text-slate-300 mb-3 block"></i>
            <h4 class="text-base font-bold text-slate-800">No matches found for the selected criteria</h4>
            <p class="text-xs text-slate-500 mt-1 mb-4">Try posting a new requirement or adjusting your budget parameters.</p>
            <a href="{{ route('requirements.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white font-bold text-xs">
                Post New Requirement
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($matches as $m)
                @php $property = $m->property; @endphp
                <div class="group bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Image -->
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                            <img src="{{ $property->primary_image_url }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Match Score Pill -->
                            <div class="absolute top-3 left-3 z-10">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black shadow-lg 
                                    {{ $m->score >= 85 ? 'bg-emerald-600 text-white' : '' }}
                                    {{ $m->score >= 70 && $m->score < 85 ? 'bg-amber-500 text-white' : '' }}
                                    {{ $m->score < 70 ? 'bg-slate-800 text-white' : '' }}">
                                    <i class="fa-solid fa-bolt text-[10px]"></i>
                                    <span>{{ $m->score }}% Match</span>
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-3 right-3 flex items-end justify-between text-white z-10">
                                <div>
                                    <div class="text-lg font-black text-white drop-shadow-sm">{{ $property->formatted_price }}</div>
                                </div>
                                <div class="text-xs font-semibold text-slate-200">
                                    {{ $property->bhk ? $property->bhk . ' BHK' : $property->property_type }}
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="text-xs font-semibold text-brand-600 mb-1">
                                <i class="fa-solid fa-location-dot"></i> {{ $property->area }}, {{ $property->city }}
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm leading-snug line-clamp-1 mb-2">
                                <a href="{{ route('properties.show', $property) }}" target="_blank" class="hover:text-brand-600">
                                    {{ $property->title }}
                                </a>
                            </h4>
                            <div class="text-[11px] text-slate-500 mb-3">
                                Matched for: <span class="font-semibold text-slate-700">{{ $m->requirement->bhk ? $m->requirement->bhk . ' BHK ' : '' }}{{ $m->requirement->property_type }} in {{ $m->requirement->city }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-5 pt-0 flex items-center justify-between gap-2 border-t border-slate-100/80 mt-2">
                        <a href="tel:{{ $property->user->phone ?? '' }}" class="px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-phone"></i>
                            <span>Call Seller</span>
                        </a>
                        <a href="{{ route('properties.show', $property) }}" target="_blank" class="px-3 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition flex items-center gap-1.5">
                            <span>Details</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $matches->links() }}
        </div>
    @endif

</div>
@endsection

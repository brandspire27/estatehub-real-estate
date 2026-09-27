@extends('layouts.app')

@section('title', 'Explore Properties — EstateHub')

@section('content')
<div class="bg-slate-100 border-b border-slate-200 py-6 sm:py-8" x-data="propertiesExplorer()">

    <!-- Page Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Properties for Sale</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Browse verified properties with instant buyer matching</p>
            </div>

            <!-- Header Actions: Upload Property CTA & Mobile Filters -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-xs shadow-md shadow-brand-500/20 transition flex-shrink-0">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Upload Property</span>
                </a>

                <!-- Mobile Filter Trigger Button (prominent on mobile above the fold) -->
                <div class="flex items-center gap-2 lg:hidden">
                    <button type="button" @click="mobileFiltersOpen = true"
                            class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Filter & Refine</span>
                        @php
                            $activeCount = count(array_filter($filters ?? [], fn($v, $k) => !empty($v) && $k !== 'sort', ARRAY_FILTER_USE_BOTH));
                        @endphp
                        @if($activeCount > 0)
                            <span class="w-4 h-4 rounded-full bg-brand-500 text-white text-[9px] font-black flex items-center justify-center">
                                {{ $activeCount }}
                            </span>
                        @endif
                    </button>

                    @if(!empty(array_filter($filters ?? [])))
                        <a href="{{ route('properties.index') }}" class="px-2.5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mobile Active Filter Pills Bar (instant visibility on mobile) -->
        @if(!empty(array_filter($filters ?? [])))
            <div class="flex items-center gap-2 overflow-x-auto pt-3 pb-1 no-scrollbar lg:hidden text-xs">
                <span class="text-slate-400 font-semibold text-[11px] flex-shrink-0">Active:</span>
                @if(!empty($filters['city']))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-slate-200 text-slate-700 font-medium flex-shrink-0">
                        <i class="fa-solid fa-city text-brand-600 text-[10px]"></i>
                        <span>{{ $filters['city'] }}</span>
                    </span>
                @endif
                @if(!empty($filters['locality']))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-800 font-medium flex-shrink-0">
                        <i class="fa-solid fa-location-dot text-[10px]"></i>
                        <span>{{ $filters['locality'] }}</span>
                    </span>
                @endif
                @if(!empty($filters['property_type']))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-slate-200 text-slate-700 font-medium flex-shrink-0">
                        <span>{{ $filters['property_type'] }}</span>
                    </span>
                @endif
                @if(!empty($filters['bhk']))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-slate-200 text-slate-700 font-medium flex-shrink-0">
                        <span>{{ $filters['bhk'] }} BHK</span>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Desktop Filter Sidebar (hidden on mobile, visible on desktop) -->
            <div class="hidden lg:block lg:col-span-1">
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm sticky top-28">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-brand-600"></i>
                            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Filters</h3>
                        </div>
                        @if(!empty(array_filter($filters ?? [])))
                            <a href="{{ route('properties.index') }}" class="text-xs font-semibold text-rose-500 hover:text-rose-600">
                                Reset All
                            </a>
                        @endif
                    </div>

                    @include('properties.partials.filter_form', ['isMobile' => false])
                </div>
            </div>

            <!-- Property Grid & Results (IMMEDIATELY ABOVE THE FOLD ON MOBILE) -->
            <div class="lg:col-span-3">
                
                <!-- Seller Promotion Card: Upload Property -->
                <div class="mb-6 p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-slate-900 to-indigo-950 text-white shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-brand-500/20 text-brand-400 border border-brand-400/30 flex items-center justify-center text-xl flex-shrink-0">
                            <i class="fa-solid fa-house-circle-check"></i>
                        </div>
                        <div>
                            <div class="text-sm sm:text-base font-black text-white">Selling or Renting Your Property?</div>
                            <p class="text-xs text-slate-300">Upload your listing with photos & YouTube video tour for instant qualified buyer matches.</p>
                        </div>
                    </div>
                    <a href="{{ route('listings.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-black shadow transition flex-shrink-0">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Property Free</span>
                    </a>
                </div>

                <!-- Top Bar: Result Count & Sorting -->
                <div class="bg-white rounded-2xl border border-slate-200/80 px-4 sm:px-5 py-3.5 mb-6 shadow-sm flex items-center justify-between gap-3">
                    <div class="text-xs sm:text-sm text-slate-600 font-medium">
                        Showing <span class="font-bold text-slate-900">{{ $properties->total() }}</span> verified properties
                    </div>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="hidden sm:inline text-slate-500 font-bold uppercase tracking-wider">Sort:</span>
                        <form action="{{ route('properties.index') }}" method="GET" id="sortForm">
                            @foreach($filters as $k => $v)
                                @if($k !== 'sort' && !empty($v))
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach
                            <select name="sort" onchange="document.getElementById('sortForm').submit()" class="px-2.5 sm:px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-500 bg-slate-50">
                                <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>Latest Listings</option>
                                <option value="price_low" {{ ($filters['sort'] ?? '') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ ($filters['sort'] ?? '') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="views" {{ ($filters['sort'] ?? '') === 'views' ? 'selected' : '' }}>Most Viewed</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Properties Cards -->
                @if($properties->isEmpty())
                    <div class="text-center py-16 sm:py-20 bg-white rounded-3xl border border-dashed border-slate-300 p-8">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">No properties matched your criteria</h3>
                        <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto mb-6">
                            Try clearing some filters or searching in a different city or price range.
                        </p>
                        <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-bold text-xs shadow-md">
                            Reset All Filters
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($properties as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-8">
                        {{ $properties->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>

    <!-- Mobile Slide-out Filter Drawer (Modal) -->
    <div x-show="mobileFiltersOpen" x-cloak class="fixed inset-0 z-50 lg:hidden flex justify-end"
         role="dialog" aria-modal="true">
        
        <!-- Backdrop -->
        <div @click="mobileFiltersOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs"></div>

        <!-- Drawer Content -->
        <div x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="relative w-full max-w-md bg-white h-full shadow-2xl overflow-y-auto flex flex-col justify-between p-6 z-10 text-slate-800">
            
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-brand-600"></i>
                        <h3 class="font-bold text-slate-900 text-base">Filter Properties</h3>
                    </div>
                    <button type="button" @click="mobileFiltersOpen = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                @include('properties.partials.filter_form', ['isMobile' => true])
            </div>

            <div class="pt-4 border-t border-slate-100 mt-6 flex items-center gap-3">
                <a href="{{ route('properties.index') }}" class="w-1/3 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs text-center hover:bg-slate-50 transition">
                    Reset
                </a>
                <button type="button" @click="document.getElementById('mobileFilterForm').submit()" class="flex-1 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function propertiesExplorer() {
    return {
        mobileFiltersOpen: false,
        selectedCity: @json($filters['city'] ?? ''),
        selectedLocality: @json($filters['locality'] ?? ''),
        propertyType: @json($filters['property_type'] ?? ''),
        localitiesByCity: @json($localitiesByCity ?? []),

        init() {
            this.updateLocalities();

            // Sync locality select change with Alpine state
            document.querySelectorAll('.locality-select-dropdown').forEach(sel => {
                sel.addEventListener('change', (e) => {
                    this.selectedLocality = e.target.value;
                });
            });
        },

        getLocalities() {
            return this.selectedCity ? (this.localitiesByCity[this.selectedCity] || []) : [];
        },

        onCityChange() {
            this.selectedLocality = '';
            this.updateLocalities();
        },

        updateLocalities() {
            const city = this.selectedCity;
            const currentVal = this.selectedLocality;
            const localities = (city && this.localitiesByCity[city]) ? this.localitiesByCity[city] : [];

            document.querySelectorAll('.locality-select-dropdown').forEach(sel => {
                sel.innerHTML = '';

                // Default option: With or without locality
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = city 
                    ? ('All Localities in ' + city + ' (Without Locality)')
                    : 'All Localities (Without Locality)';
                sel.appendChild(defaultOpt);

                if (city) {
                    // When a city is selected, populate ONLY that city's localities
                    if (localities.length > 0) {
                        localities.forEach(loc => {
                            const opt = document.createElement('option');
                            opt.value = loc.name;
                            opt.textContent = loc.name;
                            if (loc.name === currentVal) {
                                opt.selected = true;
                            }
                            sel.appendChild(opt);
                        });
                    }
                } else {
                    // If no city selected, display all localities grouped by city
                    Object.keys(this.localitiesByCity).forEach(cName => {
                        const list = this.localitiesByCity[cName] || [];
                        if (list.length > 0) {
                            const grp = document.createElement('optgroup');
                            grp.label = cName;
                            list.forEach(loc => {
                                const opt = document.createElement('option');
                                opt.value = loc.name;
                                opt.textContent = loc.name + ' (' + cName + ')';
                                if (loc.name === currentVal) {
                                    opt.selected = true;
                                }
                                grp.appendChild(opt);
                            });
                            sel.appendChild(grp);
                        }
                    });
                }

                sel.value = currentVal || '';
            });
        },

        isResidential() {
            return !this.propertyType || ['Flat', 'Apartment', 'Villa'].includes(this.propertyType);
        },

        isPlot() {
            return ['Plot', 'Land'].includes(this.propertyType);
        },

        isCommercial() {
            return ['Commercial Space', 'Building'].includes(this.propertyType);
        },

        setAreaRange(min, max) {
            const minEl = document.querySelector('input[name=min_area]');
            const maxEl = document.querySelector('input[name=max_area]');
            if (minEl) minEl.value = min || '';
            if (maxEl) maxEl.value = max || '';
        }
    };
}
</script>
@endsection

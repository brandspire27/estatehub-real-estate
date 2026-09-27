@props(['isMobile' => false])

<form action="{{ route('properties.index') }}" method="GET" id="{{ $isMobile ? 'mobileFilterForm' : 'desktopFilterForm' }}" class="space-y-4">

    <!-- Keyword -->
    <div>
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Keyword</label>
        <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="Title, amenities..." 
               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
    </div>

    <!-- 1. City (First) -->
    <div>
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
            City <span class="text-brand-600 font-semibold">(Select First)</span>
        </label>
        <select name="city" x-model="selectedCity" @change="onCityChange()"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
            <option value="">All Cities</option>
            @foreach($cities as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>
    </div>

    <!-- 2. Locality (Second - with or without locality) -->
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">
                Locality / Area
            </label>
            <span class="text-[10px] text-brand-600 font-bold" x-text="selectedCity ? (getLocalities().length + ' areas in ' + selectedCity) : 'All Metros'"></span>
        </div>

        <div>
            <select name="locality" x-model="selectedLocality" id="{{ $isMobile ? 'mobileLocalitySelect' : 'desktopLocalitySelect' }}"
                    class="locality-select-dropdown w-full px-3 py-2 rounded-xl border border-brand-200 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                <option value="">{{ !empty($filters['city']) ? 'All Localities in ' . $filters['city'] . ' (Without Locality)' : 'All Localities (Without Locality)' }}</option>
                @if(!empty($filters['city']) && isset($localitiesByCity[$filters['city']]))
                    @foreach($localitiesByCity[$filters['city']] as $loc)
                        <option value="{{ $loc->name }}" {{ ($filters['locality'] ?? '') === $loc->name ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                @else
                    @foreach($localitiesByCity as $cityName => $locList)
                        <optgroup label="{{ $cityName }}">
                            @foreach($locList as $loc)
                                <option value="{{ $loc->name }}" {{ ($filters['locality'] ?? '') === $loc->name ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $cityName }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                @endif
            </select>
            <p class="text-[10px] text-slate-400 mt-1" x-text="selectedCity ? ('Showing localities in ' + selectedCity + ' only. Select an area or search whole city.') : 'Select a city first to filter localities, or choose from all cities.'"></p>
        </div>
    </div>

    <!-- 3. Property Type -->
    <div>
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Property Type</label>
        <select name="property_type" x-model="propertyType"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
            <option value="">All Types (Residential & Commercial)</option>
            @foreach($propertyTypes as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <!-- 4. Dynamic Options: BHK for Residential (Flat/Apartment/Villa) -->
    <div x-show="isResidential()" x-cloak class="pt-1">
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">BHK Configuration</label>
        <div class="grid grid-cols-5 gap-1.5">
            @foreach([1, 2, 3, 4, 5] as $b)
                <label class="cursor-pointer">
                    <input type="radio" name="bhk" value="{{ $b }}" class="sr-only peer" {{ (string)($filters['bhk'] ?? '') === (string)$b ? 'checked' : '' }}>
                    <div class="py-1.5 text-center text-xs font-semibold rounded-lg border border-slate-200 peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 hover:bg-slate-50 transition">
                        {{ $b }} BHK
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <!-- 5. Dynamic Options: Plot / Land Area Size -->
    <div x-show="isPlot()" x-cloak class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-2.5">
        <div class="flex items-center gap-1.5 text-emerald-800 text-xs font-bold uppercase tracking-wider">
            <i class="fa-solid fa-vector-square text-emerald-600"></i>
            <span>Plot & Land Size</span>
        </div>

        <div class="grid grid-cols-2 gap-1.5 text-[11px]">
            <button type="button" @click="setAreaRange(0, 1500)" class="p-1.5 text-center rounded-lg border border-emerald-200 bg-white hover:bg-emerald-100/50 font-medium text-slate-700 transition">
                &lt; 1,500 sq.ft
            </button>
            <button type="button" @click="setAreaRange(1500, 3000)" class="p-1.5 text-center rounded-lg border border-emerald-200 bg-white hover:bg-emerald-100/50 font-medium text-slate-700 transition">
                1,500 – 3,000 sq.ft
            </button>
            <button type="button" @click="setAreaRange(3000, 6000)" class="p-1.5 text-center rounded-lg border border-emerald-200 bg-white hover:bg-emerald-100/50 font-medium text-slate-700 transition">
                3,000 – 6,000 sq.ft
            </button>
            <button type="button" @click="setAreaRange(6000, '')" class="p-1.5 text-center rounded-lg border border-emerald-200 bg-white hover:bg-emerald-100/50 font-medium text-slate-700 transition">
                6,000+ sq.ft
            </button>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-1">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase">Min Area (Sq.Ft)</label>
                <input type="number" name="min_area" value="{{ $filters['min_area'] ?? '' }}" placeholder="Min Sq.Ft"
                       class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-emerald-500 bg-white">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase">Max Area (Sq.Ft)</label>
                <input type="number" name="max_area" value="{{ $filters['max_area'] ?? '' }}" placeholder="Max Sq.Ft"
                       class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-emerald-500 bg-white">
            </div>
        </div>
    </div>

    <!-- 6. Dynamic Options: Commercial Space Area -->
    <div x-show="isCommercial()" x-cloak class="p-3.5 bg-indigo-50/70 border border-indigo-200 rounded-2xl space-y-2.5">
        <div class="flex items-center gap-1.5 text-indigo-900 text-xs font-bold uppercase tracking-wider">
            <i class="fa-solid fa-briefcase text-indigo-600"></i>
            <span>Commercial Space Size</span>
        </div>

        <div class="grid grid-cols-2 gap-1.5 text-[11px]">
            <button type="button" @click="setAreaRange(0, 1000)" class="p-1.5 text-center rounded-lg border border-indigo-200 bg-white hover:bg-indigo-100/50 font-medium text-slate-700 transition">
                &lt; 1,000 sq.ft (Shop/Clinic)
            </button>
            <button type="button" @click="setAreaRange(1000, 2500)" class="p-1.5 text-center rounded-lg border border-indigo-200 bg-white hover:bg-indigo-100/50 font-medium text-slate-700 transition">
                1,000 – 2,500 sq.ft (Office)
            </button>
            <button type="button" @click="setAreaRange(2500, 5000)" class="p-1.5 text-center rounded-lg border border-indigo-200 bg-white hover:bg-indigo-100/50 font-medium text-slate-700 transition">
                2,500 – 5,000 sq.ft (Showroom)
            </button>
            <button type="button" @click="setAreaRange(5000, '')" class="p-1.5 text-center rounded-lg border border-indigo-200 bg-white hover:bg-indigo-100/50 font-medium text-slate-700 transition">
                5,000+ sq.ft (Building)
            </button>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-1">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase">Min Area (Sq.Ft)</label>
                <input type="number" name="min_area" value="{{ $filters['min_area'] ?? '' }}" placeholder="Min Sq.Ft"
                       class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500 bg-white">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase">Max Area (Sq.Ft)</label>
                <input type="number" name="max_area" value="{{ $filters['max_area'] ?? '' }}" placeholder="Max Sq.Ft"
                       class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500 bg-white">
            </div>
        </div>
    </div>

    <!-- 7. Price Range (Budget) -->
    <div class="pt-1">
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Budget in INR (₹)</label>
        <div class="grid grid-cols-2 gap-2">
            <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min Price" 
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
            <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max Price" 
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
        </div>
    </div>

    <!-- 8. Possession Status -->
    <div>
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Possession Timeline</label>
        <select name="possession_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
            <option value="">Any Status</option>
            <option value="Ready to Move" {{ ($filters['possession_status'] ?? '') === 'Ready to Move' ? 'selected' : '' }}>Ready to Move</option>
            <option value="Under Construction" {{ ($filters['possession_status'] ?? '') === 'Under Construction' ? 'selected' : '' }}>Under Construction</option>
        </select>
    </div>

    <!-- Keep current sort parameter -->
    <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'latest' }}">

    @if(!$isMobile)
        <div class="pt-2">
            <button type="submit" class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition">
                Apply Filters
            </button>
        </div>
    @endif

</form>

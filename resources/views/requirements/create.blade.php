@extends('layouts.dashboard')

@section('title', 'Post Buyer Requirement')
@section('page-title', 'Post Buyer Requirement')
@section('page-subtitle', 'Our matching engine compares your criteria against published properties instantly')

@section('content')
<div class="max-w-3xl bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm"
     x-data="requirementCreateForm()">
    
    <form action="{{ route('requirements.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- If staff/admin, allow selecting buyer via AJAX search -->
        @if(Auth::user()->canAccessStaff())
            <div class="p-5 rounded-2xl bg-indigo-50/70 border border-indigo-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase tracking-wider text-indigo-950">
                        <i class="fa-solid fa-user-tag text-indigo-600 mr-1"></i> Target Buyer (Client)
                    </label>
                    <span class="text-[11px] text-indigo-600 font-medium">Search by client name or phone</span>
                </div>

                <!-- Hidden input for form submission -->
                <input type="hidden" name="target_user_id" :value="selectedClient ? selectedClient.id : '{{ Auth::id() }}'">

                <!-- Selected Client Banner -->
                <template x-if="selectedClient">
                    <div class="flex items-center justify-between p-3.5 bg-white rounded-xl border border-indigo-200 shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm uppercase">
                                <span x-text="selectedClient.name.charAt(0)"></span>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span x-text="selectedClient.name"></span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700" x-text="selectedClient.role"></span>
                                </div>
                                <div class="text-xs text-slate-500 flex items-center gap-3 mt-0.5">
                                    <span><i class="fa-solid fa-phone text-xs mr-1 text-slate-400"></i><span x-text="selectedClient.phone"></span></span>
                                    <span><i class="fa-solid fa-envelope text-xs mr-1 text-slate-400"></i><span x-text="selectedClient.email"></span></span>
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="clearClient()" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-600 transition">
                            Change Client
                        </button>
                    </div>
                </template>

                <!-- Search Input when no client is selected -->
                <template x-if="!selectedClient">
                    <div class="relative">
                        <div class="relative">
                            <input type="text"
                                   x-model="clientQuery"
                                   @input="searchClients()"
                                   @focus="searchClients()"
                                   placeholder="Type client name or 10-digit phone number to search..."
                                   class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-indigo-300 text-sm font-semibold bg-white focus:ring-2 focus:ring-brand-500">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-indigo-400 text-sm"></i>
                            
                            <template x-if="isSearchingClient">
                                <i class="fa-solid fa-spinner fa-spin absolute right-3.5 top-1/2 -translate-y-1/2 text-indigo-400 text-sm"></i>
                            </template>
                        </div>

                        <div class="mt-1.5 flex items-center justify-between text-[11px] text-indigo-700">
                            <span>Default: <strong>Self ({{ Auth::user()->name }})</strong> if no client selected</span>
                            <button type="button" @click="clearClient()" class="hover:underline font-semibold">Keep as Self</button>
                        </div>

                        <!-- AJAX Search Dropdown Results -->
                        <div x-show="clientResults.length > 0"
                             @click.outside="clientResults = []"
                             class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl border border-slate-200 shadow-xl max-h-64 overflow-y-auto z-30 divide-y divide-slate-100">
                            <template x-for="c in clientResults" :key="c.id">
                                <div @click="selectClient(c)" class="p-3 hover:bg-indigo-50 cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                            <span x-text="c.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-900" x-text="c.name"></div>
                                            <div class="text-[11px] text-slate-500">
                                                <span x-text="c.phone"></span> &bull; <span x-text="c.email"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700" x-text="c.role"></span>
                                </div>
                            </template>
                        </div>

                        <div x-show="clientQuery.trim().length > 1 && clientResults.length === 0 && !isSearchingClient"
                             class="text-xs text-slate-400 p-2 italic">
                            No clients found matching "<span x-text="clientQuery"></span>"
                        </div>
                    </div>
                </template>
            </div>
        @endif

        <!-- Property Type & Dynamic Category Options -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Property Type Selector -->
            <div>
                <label for="property_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Property Type <span class="text-rose-500">*</span>
                </label>
                <select id="property_type" 
                        name="property_type" 
                        x-model="propertyType" 
                        @change="handleTypeChange()"
                        required 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500">
                    @foreach($propertyTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                @error('property_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Dynamic Options Section based on Property Type -->
            <div>
                <!-- 1. Residential: Show BHK Selector -->
                <div x-show="isResidential()">
                    <label for="bhk" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Preferred BHK <span class="text-rose-500">*</span>
                    </label>
                    <select id="bhk" 
                            name="bhk" 
                            x-model="selectedBhk"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500">
                        <option value="1">1 BHK</option>
                        <option value="2">2 BHK</option>
                        <option value="3">3 BHK</option>
                        <option value="4">4 BHK</option>
                        <option value="5">5+ BHK / Penthouse</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Select your preferred bedroom layout</p>
                </div>

                <!-- 2. Plot / Land: Show Plot Category Options -->
                <div x-show="isPlotOrLand()" x-cloak>
                    <label for="plot_category" class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1">
                        <i class="fa-solid fa-vector-square mr-1"></i> Plot & Land Category
                    </label>
                    <select id="plot_category" 
                            name="sub_category" 
                            x-model="plotCategory"
                            class="w-full px-3 py-2.5 rounded-xl border border-emerald-300 text-sm font-semibold bg-emerald-50/50 focus:ring-2 focus:ring-emerald-500">
                        <option value="Residential Freehold Plot">Residential Freehold Plot</option>
                        <option value="Gated Enclave Villa Plot">Gated Enclave Villa Plot</option>
                        <option value="Commercial Roadside Plot">Commercial Roadside Plot</option>
                        <option value="Agricultural / Farm Land">Agricultural / Farm Land</option>
                        <option value="Industrial / Warehouse Land">Industrial / Warehouse Land</option>
                    </select>
                    <p class="text-[11px] text-emerald-600 mt-1">BHK is not applicable for plots and land parcels</p>
                </div>

                <!-- 3. Commercial: Show Commercial Space Usage Options -->
                <div x-show="isCommercial()" x-cloak>
                    <label for="commercial_usage" class="block text-xs font-bold uppercase tracking-wider text-indigo-700 mb-1">
                        <i class="fa-solid fa-briefcase mr-1"></i> Commercial Space Usage
                    </label>
                    <select id="commercial_usage" 
                            name="sub_category" 
                            x-model="commercialUsage"
                            class="w-full px-3 py-2.5 rounded-xl border border-indigo-300 text-sm font-semibold bg-indigo-50/50 focus:ring-2 focus:ring-indigo-500">
                        <option value="Corporate Office Space">Corporate Office Space</option>
                        <option value="Retail Store / Frontage Showroom">Retail Store / Frontage Showroom</option>
                        <option value="Commercial Warehouse / Godown">Commercial Warehouse / Godown</option>
                        <option value="Coworking / Managed Floor">Coworking / Managed Floor</option>
                        <option value="Full Independent Building">Full Independent Commercial Building</option>
                    </select>
                    <p class="text-[11px] text-indigo-600 mt-1">Tailored for business, retail, and office search</p>
                </div>
            </div>
        </div>

        <!-- City, Locality & Possession Status -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- City Dropdown -->
            <div>
                <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Preferred City <span class="text-rose-500">*</span>
                </label>
                <select id="city" 
                        name="city" 
                        x-model="selectedCity" 
                        required 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                    @foreach($cities as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select>
                @error('city') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Locality Dropdown (Dynamic based on selectedCity) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="locality" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Locality / Preferred Area
                    </label>
                    <span class="text-[10px] text-brand-600 font-semibold" x-text="getLocalities().length + ' available'"></span>
                </div>
                
                <select id="locality" 
                        name="locality" 
                        x-model="selectedLocality"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                    <option value="">All Localities / Flexible</option>
                    <template x-for="loc in getLocalities()" :key="loc.id">
                        <option :value="loc.name" x-text="loc.name" :selected="loc.name === selectedLocality"></option>
                    </template>
                </select>
                @error('locality') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Possession Status -->
            <div>
                <label for="possession_status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Possession Timeline
                </label>
                <select id="possession_status" name="possession_status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                    <option value="Any">Any / Flexible</option>
                    <option value="Ready to Move" {{ old('possession_status') === 'Ready to Move' ? 'selected' : '' }}>Ready to Move</option>
                    <option value="Under Construction" {{ old('possession_status') === 'Under Construction' ? 'selected' : '' }}>Under Construction</option>
                </select>
            </div>
        </div>

        <!-- Budget Range -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Budget Range (₹ INR) <span class="text-rose-500">*</span>
                </label>
                <span class="text-xs font-bold text-brand-600" x-text="formatBudgetDisplay()"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="budget_min" class="block text-[11px] text-slate-500 font-medium mb-1">Min Budget (₹)</label>
                    <input type="number" 
                           id="budget_min" 
                           name="budget_min" 
                           x-model.number="budgetMin" 
                           required 
                           step="50000" 
                           min="0"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500">
                    @error('budget_min') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="budget_max" class="block text-[11px] text-slate-500 font-medium mb-1">Max Budget (₹)</label>
                    <input type="number" 
                           id="budget_max" 
                           name="budget_max" 
                           x-model.number="budgetMax" 
                           required 
                           step="50000" 
                           min="0"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500">
                    @error('budget_max') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Quick Budget Presets -->
            <div class="flex flex-wrap gap-2 mt-3">
                <span class="text-xs text-slate-400 self-center mr-1">Quick Presets:</span>
                <button type="button" @click="setBudget(2500000, 5000000)" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600">₹25L – ₹50L</button>
                <button type="button" @click="setBudget(5000000, 8000000)" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600">₹50L – ₹80L</button>
                <button type="button" @click="setBudget(8000000, 15000000)" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600">₹80L – ₹1.5 Cr</button>
                <button type="button" @click="setBudget(15000000, 30000000)" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600">₹1.5 Cr – ₹3 Cr</button>
            </div>
        </div>

        <!-- Additional Specifications & Preferences -->
        <div>
            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                Specific Notes & Amenities Desired
            </label>
            <textarea id="description" 
                      name="description" 
                      rows="3" 
                      placeholder="e.g. Prefer gated society with covered parking, gym, near top schools, or corner commercial unit."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">{{ old('description') }}</textarea>
            <p class="text-xs text-slate-400 mt-1">Our matching algorithm will search these keywords for higher precision scoring.</p>
        </div>

        <!-- Submit & Actions -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('requirements.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-500/20 transition flex items-center gap-2">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                <span>Publish Requirement & Run Matches</span>
            </button>
        </div>
    </form>
</div>

<!-- Inline Safe Alpine Component Script (Zero Attribute Leakage) -->
<script>
function requirementCreateForm() {
    return {
        selectedCity: '{{ old('city', $cities->first() ?? 'Guwahati') }}',
        selectedLocality: '{{ old('locality', '') }}',
        localitiesByCity: {!! json_encode($localitiesByCity ?? []) !!},
        
        propertyType: '{{ old('property_type', 'Flat') }}',
        selectedBhk: '{{ old('bhk', 2) }}',
        plotCategory: 'Residential Freehold Plot',
        commercialUsage: 'Corporate Office Space',

        budgetMin: {{ (float)old('budget_min', 4000000) }},
        budgetMax: {{ (float)old('budget_max', 7500000) }},

        // Client AJAX Search State (for staff)
        clientQuery: '',
        clientResults: [],
        isSearchingClient: false,
        selectedClient: {!! json_encode(old('target_user_id') ? \App\Models\User::find(old('target_user_id')) : (request('user_id') ? \App\Models\User::find(request('user_id')) : null)) !!},
        searchTimeout: null,

        isResidential() {
            return ['Flat', 'Apartment', 'Villa'].includes(this.propertyType);
        },

        isPlotOrLand() {
            return ['Plot', 'Land'].includes(this.propertyType);
        },

        isCommercial() {
            return ['Commercial Space', 'Building'].includes(this.propertyType);
        },

        handleTypeChange() {
            if (this.isPlotOrLand() || this.isCommercial()) {
                // Clear BHK for non-residential
                this.selectedBhk = '';
            } else if (!this.selectedBhk) {
                this.selectedBhk = '2';
            }
        },

        getLocalities() {
            return this.localitiesByCity[this.selectedCity] || [];
        },

        setBudget(min, max) {
            this.budgetMin = min;
            this.budgetMax = max;
        },

        formatBudgetDisplay() {
            const fmt = (v) => {
                if (v >= 10000000) return '₹' + (v / 10000000).toFixed(2).replace(/\.00$/, '') + ' Cr';
                if (v >= 100000) return '₹' + (v / 100000).toFixed(2).replace(/\.00$/, '') + ' Lakh';
                return '₹' + v.toLocaleString();
            };
            return fmt(this.budgetMin) + ' – ' + fmt(this.budgetMax);
        },

        searchClients() {
            if (this.clientQuery.trim().length < 1) {
                this.clientResults = [];
                return;
            }
            clearTimeout(this.searchTimeout);
            this.isSearchingClient = true;
            this.searchTimeout = setTimeout(() => {
                fetch('{{ route('staff.users.search') }}?q=' + encodeURIComponent(this.clientQuery))
                    .then(res => res.json())
                    .then(data => {
                        this.clientResults = data;
                        this.isSearchingClient = false;
                    })
                    .catch(() => {
                        this.isSearchingClient = false;
                    });
            }, 250);
        },

        selectClient(client) {
            this.selectedClient = client;
            this.clientQuery = '';
            this.clientResults = [];
        },

        clearClient() {
            this.selectedClient = null;
            this.clientQuery = '';
            this.clientResults = [];
        }
    };
}
</script>
@endsection

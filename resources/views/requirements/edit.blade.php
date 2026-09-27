@extends('layouts.dashboard')

@section('title', 'Edit Buyer Requirement')
@section('page-title', 'Edit Requirement')
@section('page-subtitle', 'Updating your criteria will automatically re-run matching algorithms')

@section('content')
<div class="max-w-3xl bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm"
     x-data="requirementEditForm()">
    <form action="{{ route('requirements.update', $requirement) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Property Type & Dynamic BHK / Category Options -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        <option value="{{ $type }}" {{ old('property_type', $requirement->property_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('property_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <!-- 1. Residential: Preferred BHK -->
                <div x-show="isResidential()">
                    <label for="bhk" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Preferred BHK <span class="text-rose-500">*</span>
                    </label>
                    <select id="bhk" 
                            name="bhk" 
                            x-model="selectedBhk"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500">
                        <option value="">Select BHK</option>
                        @foreach([1, 2, 3, 4, 5] as $b)
                            <option value="{{ $b }}" {{ old('bhk', $requirement->bhk) == $b ? 'selected' : '' }}>{{ $b }} BHK</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Plot / Land: Category -->
                <div x-show="isPlotOrLand()" x-cloak>
                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1">
                        <i class="fa-solid fa-vector-square mr-1"></i> Plot & Land Category
                    </label>
                    <div class="px-3.5 py-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200 text-xs font-bold text-emerald-800">
                        Freehold Plot / Land Parcel
                    </div>
                </div>

                <!-- 3. Commercial Space: Usage -->
                <div x-show="isCommercial()" x-cloak>
                    <label class="block text-xs font-bold uppercase tracking-wider text-indigo-700 mb-1">
                        <i class="fa-solid fa-briefcase mr-1"></i> Commercial Space
                    </label>
                    <div class="px-3.5 py-2.5 rounded-xl bg-indigo-50/70 border border-indigo-200 text-xs font-bold text-indigo-800">
                        Office / Retail / Commercial
                    </div>
                </div>
            </div>
        </div>

        <!-- City, Locality & Possession Status -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Preferred City <span class="text-rose-500">*</span>
                </label>
                <select id="city" name="city" x-model="selectedCity" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                    @foreach($cities as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="locality" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Locality / Preferred Area
                    </label>
                    <span class="text-[10px] text-brand-600 font-semibold" x-text="getLocalities().length + ' available'"></span>
                </div>
                <select id="locality" name="locality" x-model="selectedLocality"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                    <option value="">All Localities / Flexible</option>
                    <template x-for="loc in getLocalities()" :key="loc.id">
                        <option :value="loc.name" x-text="loc.name" :selected="loc.name === selectedLocality"></option>
                    </template>
                </select>
            </div>

            <div>
                <label for="possession_status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Possession Timeline
                </label>
                <select id="possession_status" name="possession_status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                    <option value="Any" {{ $requirement->possession_status === 'Any' ? 'selected' : '' }}>Any / Flexible</option>
                    <option value="Ready to Move" {{ $requirement->possession_status === 'Ready to Move' ? 'selected' : '' }}>Ready to Move</option>
                    <option value="Under Construction" {{ $requirement->possession_status === 'Under Construction' ? 'selected' : '' }}>Under Construction</option>
                </select>
            </div>
        </div>

        <!-- Budget Range (Min & Max) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="budget_min" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Minimum Budget (₹) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₹</span>
                    <input type="number" id="budget_min" name="budget_min" value="{{ old('budget_min', (int)$requirement->budget_min) }}" required min="0" step="50000"
                           class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label for="budget_max" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Maximum Budget (₹) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₹</span>
                    <input type="number" id="budget_max" name="budget_max" value="{{ old('budget_max', (int)$requirement->budget_max) }}" required min="0" step="50000"
                           class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <!-- Status -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Requirement Status</label>
            <div class="flex flex-wrap gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="ACTIVE" class="text-brand-600" {{ $requirement->status === 'ACTIVE' ? 'checked' : '' }}>
                    <span class="ml-2 text-xs font-bold text-emerald-600">Active (Participate in Matching)</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="MATCHED" class="text-brand-600" {{ $requirement->status === 'MATCHED' ? 'checked' : '' }}>
                    <span class="ml-2 text-xs font-medium text-slate-700">Matched / Deal Ongoing</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="CLOSED" class="text-brand-600" {{ $requirement->status === 'CLOSED' ? 'checked' : '' }}>
                    <span class="ml-2 text-xs font-medium text-slate-500">Closed</span>
                </label>
            </div>
        </div>

        <!-- Description / Specific Notes -->
        <div>
            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                Specific Preferences / Notes
            </label>
            <textarea id="description" name="description" rows="4" 
                      class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">{{ old('description', $requirement->description) }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md transition">
                Update Requirement
            </button>
        </div>
    </form>
</div>

<script>
function requirementEditForm() {
    return {
        selectedCity: '{{ old('city', $requirement->city) }}',
        selectedLocality: '{{ old('locality', $requirement->locality) }}',
        localitiesByCity: {!! json_encode($localitiesByCity ?? []) !!},
        
        propertyType: '{{ old('property_type', $requirement->property_type) }}',
        selectedBhk: '{{ old('bhk', $requirement->bhk) }}',

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
                this.selectedBhk = '';
            } else if (!this.selectedBhk) {
                this.selectedBhk = '2';
            }
        },

        getLocalities() {
            return this.localitiesByCity[this.selectedCity] || [];
        }
    };
}
</script>
@endsection

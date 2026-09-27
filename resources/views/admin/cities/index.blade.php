@extends('layouts.dashboard')

@section('title', 'Admin - Popular Cities Showcase & Image Manager')
@section('page-title', 'Popular Cities & Location Showcase')
@section('page-subtitle', 'Manage cities, upload high-resolution showcase imagery, and curate the "Explore Popular Cities" section on the homepage')

@section('content')
<div class="space-y-7" x-data="adminCityManager()">

    <!-- Top Navigation Tabs (Between Cities & Localities) -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 overflow-x-auto">
        <a href="{{ route('admin.cities.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-brand-600 text-white shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-city"></i>
            <span>Popular Cities & Showcase ({{ $cities->count() }})</span>
        </a>
        <a href="{{ route('admin.localities.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition flex items-center gap-2">
            <i class="fa-solid fa-map-location-dot text-slate-400"></i>
            <span>Locality Manager</span>
        </a>
        <a href="{{ url('/#popular-cities') }}" target="_blank"
           class="ml-auto px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-brand-600"></i>
            <span>View Public "Explore Popular Cities" Section</span>
        </a>
    </div>

    <!-- Overview Stats Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-city"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $cities->count() }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Cities</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $popularCount }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Featured on Homepage</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $totalProperties }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Listings</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-image"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $cities->whereNotNull('image_url')->count() }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Custom Covers Uploaded</div>
            </div>
        </div>
    </div>

    <!-- Quick Add City Section (Collapsible Accordion / Card) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-2xs" x-data="{ openAdd: false }">
        <div class="flex items-center justify-between cursor-pointer select-none" @click="openAdd = !openAdd">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900">Add New City & Showcase Photo</h3>
                    <p class="text-xs text-slate-500">Register a new metropolitan hub and upload its showcase visual for the marketplace</p>
                </div>
            </div>
            <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition flex items-center gap-1.5">
                <span x-text="openAdd ? 'Close Form' : 'Add New City'"></span>
                <i class="fa-solid text-[10px]" :class="openAdd ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>

        <form x-show="openAdd" x-cloak action="{{ route('admin.cities.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 pt-6 border-t border-slate-100 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="new_city_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        City Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="new_city_name" name="name" required placeholder="e.g. Chandigarh, Ahmedabad, Goa..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="new_city_state" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        State / Region
                    </label>
                    <input type="text" id="new_city_state" name="state" placeholder="e.g. Punjab, Gujarat, Goa..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="new_city_sort" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Display Priority Order
                    </label>
                    <input type="number" id="new_city_sort" name="sort_order" value="0" min="0"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <!-- Upload Dropzone for New City -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                        <i class="fa-solid fa-cloud-arrow-up text-brand-600 mr-1"></i> Upload City Showcase Photo
                    </label>
                    <span class="text-[10px] text-slate-500 font-bold bg-white px-2 py-0.5 rounded-md border border-slate-200">
                        JPG, PNG, WEBP &bull; Max 5MB &bull; Recommended 600x800 px (3:4 aspect)
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp,image/jpg"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer bg-white rounded-xl border border-slate-300 py-1.5 px-2">
                    </div>
                    <div>
                        <input type="url" name="image_url" placeholder="Or paste external high-res photo URL..."
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-brand-500 bg-white">
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 cursor-pointer pt-1">
                    <input type="checkbox" name="is_popular" value="1" checked class="rounded text-brand-600 focus:ring-brand-500">
                    <span class="text-xs font-bold text-slate-800">Feature immediately in "Explore Popular Cities" on homepage</span>
                </label>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-black shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Create City Record</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Cities Showcase Grid -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-black text-slate-900 tracking-tight">Explore Popular Cities Showcase Directory</h3>
                <p class="text-xs text-slate-500">Click "Upload / Edit Showcase" on any city to replace its cover photo or adjust its status</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($cities as $city)
                <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-2xs hover:shadow-md transition-all flex flex-col group">
                    <!-- City Card Visual Thumbnail (Matches 3:4 aspect ratio of homepage) -->
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-900">
                        <img src="{{ $city->display_image_url }}" alt="{{ $city->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent"></div>

                        <!-- Top Badges -->
                        <div class="absolute top-3 inset-x-3 flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $city->is_popular ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-800/90 text-slate-300' }}">
                                <i class="fa-solid {{ $city->is_popular ? 'fa-circle-check' : 'fa-eye-slash' }} mr-1"></i>
                                {{ $city->is_popular ? 'Featured Popular' : 'Hidden from Section' }}
                            </span>

                            @if($city->image_url)
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-brand-600 text-white uppercase tracking-wider">
                                    Custom Image
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-700 text-slate-200 uppercase tracking-wider">
                                    Default Stock
                                </span>
                            @endif
                        </div>

                        <!-- Bottom Title on Image -->
                        <div class="absolute bottom-3 inset-x-3">
                            <h4 class="text-lg font-black text-white tracking-tight leading-tight">{{ $city->name }}</h4>
                            <p class="text-xs text-slate-300 font-medium">{{ $city->state ?: 'India' }}</p>
                        </div>
                    </div>

                    <!-- Card Body & Metrics -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <div class="grid grid-cols-2 gap-2 text-center text-xs">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="text-base font-black text-slate-900">{{ $city->published_properties_count }}</div>
                                <div class="text-[10px] font-bold uppercase text-slate-500">Published Listings</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="text-base font-black text-slate-900">{{ $city->localities_count }}</div>
                                <div class="text-[10px] font-bold uppercase text-slate-500">Localities</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2 pt-1 border-t border-slate-100">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    data-city="{{ $city->toJson() }}"
                                    @click="openEditModal(JSON.parse($el.dataset.city))"
                                        class="flex-1 py-2 px-3 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-black transition flex items-center justify-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-cloud-arrow-up text-brand-600"></i>
                                    <span>Upload / Edit Photo</span>
                                </button>

                                <form action="{{ route('admin.cities.toggle-popular', $city) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            title="{{ $city->is_popular ? 'Hide from Popular Cities on homepage' : 'Show in Popular Cities on homepage' }}"
                                            class="p-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs transition">
                                        <i class="fa-solid {{ $city->is_popular ? 'fa-eye text-emerald-600' : 'fa-eye-slash text-slate-400' }}"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                                <a href="{{ route('properties.index', ['city' => $city->name]) }}" target="_blank" class="hover:text-brand-600 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i> View Public Listings
                                </a>

                                @if($city->properties_count === 0)
                                    <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" onsubmit="return confirm('Delete city \'{{ addslashes($city->name) }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Edit & Upload Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="closeEditModal()"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden" @click.away="closeEditModal()">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center text-sm shadow-xs">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900" x-text="'Update City & Photo: ' + editCity.name"></h3>
                            <p class="text-[11px] text-slate-500">Upload new showcase visual for the "Explore Popular Cities" section</p>
                        </div>
                    </div>
                    <button type="button" @click="closeEditModal()" class="w-8 h-8 rounded-xl hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Modal Body: Form & Live Preview -->
                <form :action="'/admin/cities/' + editCity.id" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                        <!-- Left: Inputs & Upload Dropzone -->
                        <div class="md:col-span-7 space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        City Name <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="editCity.name" required
                                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        State / Region
                                    </label>
                                    <input type="text" name="state" x-model="editCity.state"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        Sort Priority
                                    </label>
                                    <input type="number" name="sort_order" x-model="editCity.sort_order" min="0"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div class="flex items-end pb-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_popular" value="1" x-model="editCity.is_popular" class="rounded text-brand-600 focus:ring-brand-500">
                                        <span class="text-xs font-bold text-slate-800">Featured in Section</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Image Upload Zone -->
                            <div class="p-4 rounded-2xl bg-brand-50/40 border border-brand-200/80 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-brand-950 flex items-center gap-1.5">
                                        <i class="fa-solid fa-camera text-brand-600"></i>
                                        <span>Upload City Photo</span>
                                    </label>
                                    <span class="text-[9px] font-bold text-brand-800 bg-white px-2 py-0.5 rounded-full border border-brand-200">
                                        JPG, PNG, WEBP &bull; Max 5MB
                                    </span>
                                </div>

                                <input type="file" name="image_file" id="image_file" accept="image/jpeg,image/png,image/webp,image/jpg"
                                       @change="handleImageFile($event)"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer bg-white rounded-xl border border-brand-200 py-1.5 px-2">

                                <p class="text-[11px] text-slate-500 font-medium">Selecting a file immediately updates the live card preview on the right.</p>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                        Or External Image URL
                                    </label>
                                    <input type="url" name="image_url" x-model="editCity.image_url" placeholder="https://images.unsplash.com/..."
                                           @input="previewUrl = editCity.image_url"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-brand-500 bg-white">
                                </div>

                                <template x-if="editCity.image_url">
                                    <label class="inline-flex items-center gap-2 cursor-pointer pt-1 text-rose-600 hover:text-rose-700">
                                        <input type="checkbox" name="remove_image" value="1" @change="if($event.target.checked) previewUrl = fallbackImage" class="rounded text-rose-600 focus:ring-rose-500">
                                        <span class="text-xs font-semibold">Remove custom photo (Revert to default stock)</span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Right: Live Card Preview Matching Homepage Aspect 3:4 -->
                        <div class="md:col-span-5 space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center justify-between">
                                <span>Live Homepage Preview</span>
                                <span class="text-[10px] text-brand-600 font-extrabold uppercase">aspect [3/4]</span>
                            </label>

                            <!-- The exact card markup from home.blade.php -->
                            <div class="relative rounded-2xl overflow-hidden shadow-md aspect-[3/4] flex flex-col justify-end p-4 border border-slate-200 bg-slate-900 group select-none">
                                <img :src="previewUrl || fallbackImage" 
                                     :alt="editCity.name" 
                                     class="absolute inset-0 w-full h-full object-cover">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>

                                <div class="relative z-10">
                                    <h3 class="font-black text-white text-lg tracking-tight" x-text="editCity.name || 'City Name'"></h3>
                                    <span class="text-xs text-slate-300 font-medium" x-text="(editCity.published_properties_count || '0') + ' Listings'"></span>
                                </div>
                            </div>
                            <p class="text-[10px] text-center text-slate-400 font-medium">This is exactly how visitors see this city in "Explore Popular Cities".</p>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                        <button type="button" @click="closeEditModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-black shadow-md shadow-brand-500/20 transition flex items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Save Showcase Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function adminCityManager() {
    return {
        showEditModal: false,
        fallbackImage: 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=600&q=80',
        previewUrl: '',
        editCity: {
            id: null,
            name: '',
            state: '',
            sort_order: 0,
            is_popular: true,
            image_url: '',
            published_properties_count: 0
        },

        openEditModal: function(city) {
            this.editCity = Object.assign({}, city);
            this.previewUrl = city.image_url || city.display_image_url || this.fallbackImage;
            this.showEditModal = true;
        },

        closeEditModal: function() {
            this.showEditModal = false;
            this.previewUrl = '';
        },

        handleImageFile: function(e) {
            var files = e.target.files;
            if (!files || files.length === 0) return;
            var self = this;
            var reader = new FileReader();
            reader.onload = function(evt) {
                self.previewUrl = evt.target.result;
            };
            reader.readAsDataURL(files[0]);
        }
    };
}
</script>
@endpush

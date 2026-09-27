@extends('layouts.dashboard')

@section('title', 'Edit Property Listing')
@section('page-title', 'Edit Property Listing')
@section('page-subtitle', 'Update specifications, pricing, and status for ID #PROP-' . str_pad($property->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-4xl bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm"
     x-data="propertyEditForm()">
    <form action="{{ route('listings.update', $property) }}" method="POST" enctype="multipart/form-data" class="space-y-7">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                Property Title <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="title" name="title" value="{{ old('title', $property->title) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('title') border-rose-500 @enderror">
            @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- City & Locality (Matching /properties Filter) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    City <span class="text-brand-600 font-semibold">(Select First)</span> <span class="text-rose-500">*</span>
                </label>
                <select id="city" name="city" x-model="selectedCity" @change="onCityChange()" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500 bg-white">
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ old('city', $property->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
                @error('city') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="area" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Locality / Suburb <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[10px] text-brand-600 font-semibold" x-text="getLocalities().length + ' areas in ' + selectedCity"></span>
                </div>
                <select id="area" name="area" x-model="selectedArea" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 bg-white @error('area') border-rose-500 @enderror">
                    <option value="">Select Locality...</option>
                    @if(isset($localitiesByCity))
                        @foreach($localitiesByCity as $cityName => $locList)
                            @foreach($locList as $loc)
                                <option value="{{ $loc->name }}" {{ old('area', $property->area) === $loc->name ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $cityName }})
                                </option>
                            @endforeach
                        @endforeach
                    @endif
                </select>
                @error('area') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Property Type & Dynamic Fields (Matching /properties Filter) -->
        <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4">
            <div>
                <label for="property_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Property Type <span class="text-rose-500">*</span>
                </label>
                <select id="property_type" name="property_type" x-model="propertyType" required 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-brand-500 bg-white">
                    @foreach($propertyTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Dynamic Section A: Residential (Flat, Apartment, Villa) -> BHK Config -->
            <div x-show="isResidential()" x-cloak class="pt-2 border-t border-slate-200">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    BHK Configuration <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-6 gap-2">
                    @foreach([1, 2, 3, 4, 5, 6] as $b)
                        <label class="cursor-pointer">
                            <input type="radio" name="bhk" value="{{ $b }}" x-model="bhk" class="sr-only peer">
                            <div class="py-2.5 text-center text-xs font-bold rounded-xl border border-slate-300 peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 bg-white hover:bg-slate-100 transition shadow-sm">
                                {{ $b }} BHK
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Dynamic Section B: Plot / Land Area Presets -->
            <div x-show="isPlot()" x-cloak class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-vector-square text-emerald-600"></i> Plot / Land Size Presets
                    </span>
                    <span class="text-[11px] text-emerald-700">Click to autofill Area (Sq.Ft)</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-semibold">
                    <button type="button" @click="setArea(1200)" class="py-2 px-3 rounded-xl bg-white border border-emerald-300 text-slate-700 hover:bg-emerald-100 transition text-center shadow-xs">
                        &lt; 1,500 sq.ft (1,200)
                    </button>
                    <button type="button" @click="setArea(2400)" class="py-2 px-3 rounded-xl bg-white border border-emerald-300 text-slate-700 hover:bg-emerald-100 transition text-center shadow-xs">
                        1,500 – 3,000 sq.ft (2,400)
                    </button>
                    <button type="button" @click="setArea(4500)" class="py-2 px-3 rounded-xl bg-white border border-emerald-300 text-slate-700 hover:bg-emerald-100 transition text-center shadow-xs">
                        3,000 – 6,000 sq.ft (4,500)
                    </button>
                    <button type="button" @click="setArea(8000)" class="py-2 px-3 rounded-xl bg-white border border-emerald-300 text-slate-700 hover:bg-emerald-100 transition text-center shadow-xs">
                        6,000+ sq.ft (8,000)
                    </button>
                </div>
            </div>

            <!-- Dynamic Section C: Commercial Space Area Presets -->
            <div x-show="isCommercial()" x-cloak class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-900 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-briefcase text-indigo-600"></i> Commercial Space Size Presets
                    </span>
                    <span class="text-[11px] text-indigo-700">Click to autofill Area (Sq.Ft)</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-semibold">
                    <button type="button" @click="setArea(850)" class="py-2 px-3 rounded-xl bg-white border border-indigo-300 text-slate-700 hover:bg-indigo-100 transition text-center shadow-xs">
                        Shop/Clinic (850 sq.ft)
                    </button>
                    <button type="button" @click="setArea(1800)" class="py-2 px-3 rounded-xl bg-white border border-indigo-300 text-slate-700 hover:bg-indigo-100 transition text-center shadow-xs">
                        Office Space (1,800 sq.ft)
                    </button>
                    <button type="button" @click="setArea(3500)" class="py-2 px-3 rounded-xl bg-white border border-indigo-300 text-slate-700 hover:bg-indigo-100 transition text-center shadow-xs">
                        Showroom (3,500 sq.ft)
                    </button>
                    <button type="button" @click="setArea(7500)" class="py-2 px-3 rounded-xl bg-white border border-indigo-300 text-slate-700 hover:bg-indigo-100 transition text-center shadow-xs">
                        Building/Floor (7,500 sq.ft)
                    </button>
                </div>
            </div>

            <!-- Area Input (Sq.Ft) -->
            <div>
                <label for="area_sqft" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Super Built-up / Plot Area in Sq.Ft <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" id="area_sqft" name="area_sqft" x-model="areaSqft" required min="50"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500 @error('area_sqft') border-rose-500 @enderror">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">sq.ft</span>
                </div>
                @error('area_sqft') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Price & Possession -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="price" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Price in INR (₹) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₹</span>
                    <input type="number" id="price" name="price" value="{{ old('price', (int)$property->price) }}" required min="10000" step="1000"
                           class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label for="possession_status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Possession Status <span class="text-rose-500">*</span>
                </label>
                <select id="possession_status" name="possession_status" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 bg-white">
                    <option value="Ready to Move" {{ old('possession_status', $property->possession_status) === 'Ready to Move' ? 'selected' : '' }}>Ready to Move</option>
                    <option value="Under Construction" {{ old('possession_status', $property->possession_status) === 'Under Construction' ? 'selected' : '' }}>Under Construction</option>
                </select>
            </div>
        </div>

        <!-- YouTube Video URL for Slideshow -->
        <div class="p-5 rounded-3xl bg-red-50/60 border border-red-200 space-y-2">
            <div class="flex items-center justify-between">
                <label for="youtube_url" class="block text-xs font-bold uppercase tracking-wider text-red-900">
                    <i class="fa-brands fa-youtube text-red-600 mr-1 text-sm"></i>
                    YouTube Video URL (For Slideshow & Video Tour)
                </label>
                <span class="text-[10px] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">HD Slideshow Tour</span>
            </div>
            <input type="url" id="youtube_url" name="youtube_url" x-model="youtubeUrl"
                   placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..."
                   class="w-full px-4 py-2.5 rounded-xl border border-red-300 text-xs font-mono focus:ring-2 focus:ring-red-500 bg-white">
        </div>

        <!-- Public Photography Management -->
        <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-camera text-brand-600"></i>
                        <span>Public Property Photography</span>
                    </label>
                    <p class="text-[11px] text-slate-500">Upload photos directly from your device or specify external image URLs.</p>
                </div>
                <span class="text-[10px] font-bold text-slate-600 bg-white border border-slate-200 px-2.5 py-1 rounded-full shadow-2xs">
                    JPG, PNG, WEBP &bull; Max 10MB each
                </span>
            </div>

            <!-- Existing Photos Gallery (with Delete checkboxes) -->
            @if($property->images && $property->images->count() > 0)
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Current Photos ({{ $property->images->count() }})
                        </span>
                        <span class="text-[11px] font-semibold text-rose-600 flex items-center gap-1">
                            <i class="fa-solid fa-trash-can"></i> Check any image below to delete it upon saving
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3 pt-1">
                        @foreach($property->images as $img)
                            <div class="relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-2xs">
                                <img src="{{ $img->image_url }}" alt="Property Photo" class="w-full h-24 object-cover">
                                <label class="absolute bottom-0 inset-x-0 bg-slate-900/85 hover:bg-rose-700 text-white text-[10px] font-bold py-1.5 px-2 flex items-center justify-between cursor-pointer transition">
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-trash"></i> Delete</span>
                                    <input type="checkbox" name="delete_image_ids[]" value="{{ $img->id }}" class="rounded text-rose-600 focus:ring-rose-500 border-white/40">
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Direct Multi-File Upload Dropzone -->
            <div class="border-2 border-dashed border-brand-300 hover:border-brand-500 rounded-2xl p-6 bg-brand-50/20 text-center transition cursor-pointer relative group">
                <input type="file" name="property_photos[]" id="property_photos" multiple accept="image/jpeg,image/png,image/webp,image/jpg"
                       @change="handlePropertyPhotos($event)"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                <div class="flex flex-col items-center justify-center pointer-events-none">
                    <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-600 group-hover:scale-110 flex items-center justify-center text-xl mb-2 transition-transform shadow-xs">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <span class="text-sm font-black text-slate-800">Upload New Property Photos</span>
                    <span class="text-xs text-slate-500 mt-0.5">Click or drag & drop image files from your computer</span>
                </div>
            </div>
            @error('property_photos.*') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror

            <!-- Live Client-side Thumbnail Previews -->
            <div x-show="propertyPhotosPreviews.length > 0" x-cloak class="pt-2">
                <p class="text-xs font-bold text-emerald-700 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check"></i>
                    <span x-text="propertyPhotosPreviews.length + ' photo(s) selected for upload:'"></span>
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2.5">
                    <template x-for="(prev, idx) in propertyPhotosPreviews" :key="idx">
                        <div class="relative rounded-xl overflow-hidden border border-emerald-300 bg-white shadow-2xs">
                            <img :src="prev.url" class="w-full h-20 object-cover">
                            <div class="p-1 text-[9px] font-mono text-slate-600 truncate bg-slate-50 border-t border-slate-200" x-text="prev.name"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Optional External Image URLs -->
            <details class="group pt-2 border-t border-slate-200">
                <summary class="text-xs font-bold text-slate-600 cursor-pointer hover:text-brand-600 flex items-center gap-1.5 select-none py-1">
                    <i class="fa-solid fa-link text-slate-400"></i>
                    <span>Add external image URLs (Optional)</span>
                    <i class="fa-solid fa-chevron-down text-[10px] ml-auto group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="space-y-2 mt-3 pl-2 border-l-2 border-slate-200">
                    @for($i = 0; $i < 3; $i++)
                        <input type="url" name="image_urls[]" placeholder="Photo URL https://..."
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-brand-500 bg-white">
                    @endfor
                </div>
            </details>
        </div>

        <!-- Public Description -->
        <div>
            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                Detailed Property Description <span class="text-rose-500">*</span>
            </label>
            <textarea id="description" name="description" rows="4" required
                      class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">{{ old('description', $property->description) }}</textarea>
        </div>

        <!-- PRIVATE SELLER DOSSIER (CONFIDENTIAL - NOT PUBLIC) -->
        <div class="rounded-3xl border-2 border-indigo-200 bg-indigo-50/30 overflow-hidden shadow-xs">
            <button type="button" @click="dossierOpen = !dossierOpen" 
                    class="w-full px-6 py-4 bg-indigo-100/70 hover:bg-indigo-100 flex items-center justify-between transition text-left">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-sm">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <div class="text-sm font-black text-indigo-950 flex items-center gap-2">
                            <span>Private Seller Dossier</span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-indigo-200 text-indigo-900">
                                Confidential &bull; Not Public
                            </span>
                        </div>
                        <p class="text-[11px] text-indigo-700">KYC verification, title deeds, private inspection videos, internal photos, and negotiation remarks (Seller & Admin only)</p>
                    </div>
                </div>
                <div class="text-indigo-700">
                    <i class="fa-solid" :class="dossierOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </div>
            </button>

            <div x-show="dossierOpen" class="p-6 space-y-5 bg-white border-t border-indigo-200/80">
                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2">
                    <i class="fa-solid fa-shield-halved text-amber-600 mt-0.5"></i>
                    <span><strong>Privacy Guaranteed:</strong> The fields in this dossier will never be displayed on the public marketplace or shared with buyers.</span>
                </div>

                <!-- KYC Document Type & Number -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kyc_document_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            KYC / Title Document Type
                        </label>
                        <select id="kyc_document_type" name="kyc_document_type" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">Select Document Type...</option>
                            <option value="Aadhaar Card" {{ old('kyc_document_type', $property->kyc_document_type) === 'Aadhaar Card' ? 'selected' : '' }}>Aadhaar Card</option>
                            <option value="PAN Card" {{ old('kyc_document_type', $property->kyc_document_type) === 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                            <option value="Title Deed / Patta" {{ old('kyc_document_type', $property->kyc_document_type) === 'Title Deed / Patta' ? 'selected' : '' }}>Title Deed / Patta</option>
                            <option value="Registered Sale Deed" {{ old('kyc_document_type', $property->kyc_document_type) === 'Registered Sale Deed' ? 'selected' : '' }}>Registered Sale Deed</option>
                            <option value="Municipal Tax Receipt" {{ old('kyc_document_type', $property->kyc_document_type) === 'Municipal Tax Receipt' ? 'selected' : '' }}>Municipal Tax Receipt</option>
                            <option value="Khata Certificate" {{ old('kyc_document_type', $property->kyc_document_type) === 'Khata Certificate' ? 'selected' : '' }}>Khata Certificate</option>
                        </select>
                    </div>

                    <div>
                        <label for="kyc_document_number" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Document Number / ID
                        </label>
                        <input type="text" id="kyc_document_number" name="kyc_document_number" value="{{ old('kyc_document_number', $property->kyc_document_number) }}"
                               placeholder="e.g. DEED-2024-88492"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- KYC Document File Upload & Link -->
                <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-200/70 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-indigo-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-file-shield text-indigo-600"></i>
                            <span>Title Deed / KYC Document</span>
                        </label>
                        <span class="text-[10px] font-bold text-indigo-800 bg-white border border-indigo-200 px-2 py-0.5 rounded-full">
                            PDF, JPG, PNG, WEBP &bull; Max 10MB
                        </span>
                    </div>

                    @if($property->kyc_document_url)
                        <div class="p-3 rounded-xl bg-white border border-indigo-200 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2 text-indigo-900 truncate">
                                <i class="fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                                <span class="font-bold shrink-0">Attached Document:</span>
                                <span class="font-mono text-[11px] truncate text-slate-600">{{ $property->kyc_document_url }}</span>
                            </div>
                            <a href="{{ $property->kyc_document_url }}" target="_blank" class="shrink-0 px-3 py-1 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-bold text-xs transition flex items-center gap-1">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> View File
                            </a>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="kyc_document_file" class="block text-[11px] font-bold text-slate-700 mb-1">
                                Upload New Document File
                            </label>
                            <input type="file" id="kyc_document_file" name="kyc_document_file" accept=".pdf,image/jpeg,image/png,image/webp,image/jpg"
                                   @change="handleKycFile($event)"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-white rounded-xl border border-indigo-200 py-1.5 px-2">
                            <p x-show="kycFileName" x-cloak class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-check"></i> <span x-text="kycFileName"></span>
                            </p>
                            @error('kyc_document_file') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="kyc_document_url" class="block text-[11px] font-bold text-slate-700 mb-1">
                                Or Cloud Storage / Google Drive Link
                            </label>
                            <input type="url" id="kyc_document_url" name="kyc_document_url" value="{{ old('kyc_document_url', $property->kyc_document_url) }}"
                                   placeholder="https://drive.google.com/... or https://..."
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-indigo-500 bg-white">
                        </div>
                    </div>
                </div>

                <!-- Private Remarks -->
                <div>
                    <label for="private_remarks" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Private Seller Remarks & Internal Notes
                    </label>
                    <textarea id="private_remarks" name="private_remarks" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">{{ old('private_remarks', $property->private_remarks) }}</textarea>
                </div>

                <!-- Private Photos / Blueprints -->
                <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-200/70 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-indigo-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-images text-indigo-600"></i>
                            <span>Private Photos & Blueprints</span>
                        </label>
                        <span class="text-[10px] font-bold text-indigo-800 bg-white border border-indigo-200 px-2 py-0.5 rounded-full">
                            JPG, PNG, WEBP, PDF &bull; Max 10MB each
                        </span>
                    </div>

                    @php
                        $privPhotos = is_array($property->private_photos) ? $property->private_photos : [];
                    @endphp
                    @if(count($privPhotos) > 0)
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-600">Currently Attached Dossier Files/Links:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($privPhotos as $pIdx => $pUrl)
                                    @if(!empty($pUrl))
                                        <a href="{{ $pUrl }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-indigo-200 text-indigo-900 text-[11px] font-mono hover:bg-indigo-100 transition flex items-center gap-1.5">
                                            <i class="fa-solid fa-file text-indigo-500"></i>
                                            <span>Doc {{ $pIdx + 1 }}</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Upload Private Document & Photo Files from Device
                            </label>
                            <input type="file" name="private_photo_files[]" multiple accept="image/jpeg,image/png,image/webp,image/jpg,.pdf"
                                   @change="handlePrivatePhotos($event)"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-white rounded-xl border border-indigo-200 py-1.5 px-2">
                            <p x-show="privatePhotosCount > 0" x-cloak class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-check"></i> <span x-text="privatePhotosCount + ' file(s) selected for private dossier'"></span>
                            </p>
                            @error('private_photo_files.*') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Or Private Document URLs / Cloud Drive Links
                            </label>
                            <div class="space-y-2">
                                @for($p = 0; $p < max(2, count($privPhotos)); $p++)
                                    <input type="url" name="private_photos[]" value="{{ old('private_photos.' . $p, $privPhotos[$p] ?? '') }}"
                                           placeholder="Private document / blueprint URL {{ $p + 1 }}" 
                                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-indigo-500 bg-white">
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Private Inspection Videos -->
                <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-200/70 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-indigo-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-video text-indigo-600"></i>
                            <span>Private Inspection & Raw Walkthrough Videos</span>
                        </label>
                        <span class="text-[10px] font-bold text-indigo-800 bg-white border border-indigo-200 px-2 py-0.5 rounded-full">
                            MP4, WebM, MOV &bull; Max 50MB each
                        </span>
                    </div>

                    @php
                        $privVideos = is_array($property->private_videos) ? $property->private_videos : [];
                    @endphp
                    @if(count($privVideos) > 0)
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-600">Currently Attached Private Videos:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($privVideos as $vIdx => $vUrl)
                                    @if(!empty($vUrl))
                                        <a href="{{ $vUrl }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-indigo-200 text-indigo-900 text-[11px] font-mono hover:bg-indigo-100 transition flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-play text-red-500"></i>
                                            <span>Video {{ $vIdx + 1 }}</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Upload Video Files from Device
                            </label>
                            <input type="file" name="private_video_files[]" multiple accept="video/mp4,video/webm,video/quicktime,video/x-msvideo"
                                   @change="handlePrivateVideos($event)"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-white rounded-xl border border-indigo-200 py-1.5 px-2">
                            <p x-show="privateVideosCount > 0" x-cloak class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-check"></i> <span x-text="privateVideosCount + ' video file(s) selected for private dossier'"></span>
                            </p>
                            @error('private_video_files.*') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Or Private Video Links (YouTube Unlisted, Vimeo, Cloud Drive)
                            </label>
                            <div class="space-y-2">
                                @for($v = 0; $v < max(2, count($privVideos)); $v++)
                                    <input type="url" name="private_videos[]" value="{{ old('private_videos.' . $v, $privVideos[$v] ?? '') }}"
                                           placeholder="Private raw inspection video link {{ $v + 1 }}" 
                                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-indigo-500 bg-white">
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between flex-wrap gap-4">
            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                Listing Status
            </label>
            <div class="flex items-center gap-3">
                <select name="status" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-brand-500">
                    <option value="DRAFT" {{ old('status', $property->status) === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                    <option value="PENDING_VERIFICATION" {{ old('status', $property->status) === 'PENDING_VERIFICATION' ? 'selected' : '' }}>Pending Verification</option>
                    @if(Auth::user()->canAccessStaff())
                        <option value="PUBLISHED" {{ old('status', $property->status) === 'PUBLISHED' ? 'selected' : '' }}>Published (Live)</option>
                    @endif
                    <option value="SOLD" {{ old('status', $property->status) === 'SOLD' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('listings.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">
                Cancel
            </a>

            <button type="submit" class="px-8 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black text-sm shadow-md shadow-brand-500/20 transition">
                Update Property Listing
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function propertyEditForm() {
    return {
        selectedCity: @json(old('city', $property->city)),
        selectedArea: @json(old('area', $property->area)),
        propertyType: @json(old('property_type', $property->property_type)),
        bhk: @json(old('bhk', $property->bhk)),
        areaSqft: @json(old('area_sqft', $property->area_sqft)),
        youtubeUrl: @json(old('youtube_url', $property->youtube_url)),
        localitiesByCity: @json($localitiesByCity ?? []),
        dossierOpen: true,

        getLocalities: function() {
            return this.localitiesByCity[this.selectedCity] || [];
        },

        init: function() {
            this.updateAreaOptions();
        },

        onCityChange: function() {
            this.selectedArea = '';
            this.updateAreaOptions();
        },

        updateAreaOptions: function() {
            var areaSelect = document.getElementById('area');
            if (!areaSelect) return;
            var currentVal = this.selectedArea;
            areaSelect.innerHTML = '<option value="">Select Locality...</option>';
            var list = this.localitiesByCity[this.selectedCity] || [];
            list.forEach(function(loc) {
                var opt = document.createElement('option');
                opt.value = loc.name;
                opt.textContent = loc.name;
                if (loc.name === currentVal) opt.selected = true;
                areaSelect.appendChild(opt);
            });
            areaSelect.value = currentVal || '';
        },

        isResidential: function() {
            return ['Flat', 'Apartment', 'Villa'].indexOf(this.propertyType) !== -1;
        },

        isPlot: function() {
            return ['Plot', 'Land'].indexOf(this.propertyType) !== -1;
        },

        isCommercial: function() {
            return ['Commercial Space', 'Building'].indexOf(this.propertyType) !== -1;
        },

        setArea: function(val) {
            this.areaSqft = val;
        },

        propertyPhotosPreviews: [],
        handlePropertyPhotos: function(e) {
            var files = e.target.files;
            this.propertyPhotosPreviews = [];
            if (!files || files.length === 0) return;
            var self = this;
            for (var i = 0; i < files.length; i++) {
                (function(file) {
                    var reader = new FileReader();
                    reader.onload = function(evt) {
                        self.propertyPhotosPreviews.push({
                            name: file.name,
                            size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                            url: evt.target.result
                        });
                    };
                    reader.readAsDataURL(file);
                })(files[i]);
            }
        },

        kycFileName: '',
        handleKycFile: function(e) {
            if (e.target.files && e.target.files.length > 0) {
                this.kycFileName = e.target.files[0].name + ' (' + (e.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
            } else {
                this.kycFileName = '';
            }
        },

        privatePhotosCount: 0,
        handlePrivatePhotos: function(e) {
            this.privatePhotosCount = e.target.files ? e.target.files.length : 0;
        },

        privateVideosCount: 0,
        handlePrivateVideos: function(e) {
            this.privateVideosCount = e.target.files ? e.target.files.length : 0;
        }
    };
}
</script>
@endpush

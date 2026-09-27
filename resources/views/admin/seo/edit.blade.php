@extends('layouts.dashboard')

@section('title', 'Edit SEO: ' . $seoMeta->page_name)
@section('page-title', 'SEO Configuration: ' . $seoMeta->page_name)
@section('page-subtitle', 'Optimize search engine rankings, OpenGraph previews, and indexing directives')

@section('content')
<div class="max-w-6xl mx-auto pb-12" x-data="{
    title: '{{ addslashes($seoMeta->meta_title ?? '') }}',
    description: '{{ addslashes($seoMeta->meta_description ?? '') }}',
    canonical: '{{ addslashes($seoMeta->canonical_url ?? url('/')) }}',
    ogTitle: '{{ addslashes($seoMeta->og_title ?? '') }}',
    ogDescription: '{{ addslashes($seoMeta->og_description ?? '') }}',
    ogImage: '{{ addslashes($seoMeta->og_image ?? '') }}',
    imageMode: 'upload',
    serpMode: 'desktop',
    jsonLd: `{{ addslashes($seoMeta->json_ld ?? '') }}`,
    jsonValid: true,
    validateJson() {
        if (!this.jsonLd.trim()) { this.jsonValid = true; return; }
        try { JSON.parse(this.jsonLd); this.jsonValid = true; } catch(e) { this.jsonValid = false; }
    },
    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.ogImage = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },
    clearImage() {
        this.ogImage = '';
        const fileInput = document.getElementById('og_image_file');
        if (fileInput) fileInput.value = '';
    }
}">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.seo.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-brand-600 hover:border-brand-300 transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $seoMeta->page_key }}
                    </span>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="text-xs font-medium text-slate-500">Last updated: {{ $seoMeta->updated_at->diffForHumans() }}</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 mt-0.5">{{ $seoMeta->page_name }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ $seoMeta->canonical_url ?? url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square text-slate-400"></i>
                <span>View Live Page</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <div class="font-bold mb-1 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                <span>Please fix the following validation errors:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.seo.update', $seoMeta) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Google SERP Live Simulator -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                        <i class="fa-brands fa-google"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Google SERP Live Preview</h3>
                        <p class="text-[11px] text-slate-500">Real-time simulation of how this page renders in organic Google search results</p>
                    </div>
                </div>

                <!-- Desktop / Mobile Toggle -->
                <div class="inline-flex rounded-xl p-1 bg-slate-200/70 text-xs font-semibold text-slate-600">
                    <button type="button" x-on:click="serpMode = 'desktop'" :class="serpMode === 'desktop' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1 rounded-lg transition flex items-center gap-1.5">
                        <i class="fa-solid fa-desktop text-[11px]"></i>
                        <span>Desktop</span>
                    </button>
                    <button type="button" x-on:click="serpMode = 'mobile'" :class="serpMode === 'mobile' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1 rounded-lg transition flex items-center gap-1.5">
                        <i class="fa-solid fa-mobile-screen text-[11px]"></i>
                        <span>Mobile</span>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <!-- Desktop SERP Preview Container -->
                <div x-show="serpMode === 'desktop'" class="max-w-2xl bg-white p-4 rounded-xl border border-slate-100 transition">
                    <div class="flex items-center gap-3 mb-1.5">
                        <div class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-[10px] text-slate-700 font-bold">
                            RE
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-slate-800 leading-none">RealEstate Pro</span>
                            <span class="text-[11px] text-slate-500 truncate" x-text="canonical || '{{ url('/') }}'"></span>
                        </div>
                    </div>
                    <div class="cursor-pointer group">
                        <h4 class="text-lg font-medium text-[#1a0dab] group-hover:underline leading-snug line-clamp-1" x-text="title || 'Page Title Will Appear Here'"></h4>
                    </div>
                    <p class="text-xs text-[#4d5156] mt-1 leading-relaxed line-clamp-2" x-text="description || 'Page meta description snippet will appear here to summarize search intent and attract high organic click-through rates.'"></p>
                </div>

                <!-- Mobile SERP Preview Container -->
                <div x-show="serpMode === 'mobile'" class="max-w-sm bg-white p-4 rounded-2xl border-2 border-slate-200 shadow-sm mx-auto">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center text-[9px] text-slate-700 font-bold">
                            RE
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-semibold text-slate-800 leading-none">RealEstate Pro</span>
                            <span class="text-[10px] text-slate-500 truncate max-w-[200px]" x-text="canonical || '{{ url('/') }}'"></span>
                        </div>
                    </div>
                    <h4 class="text-base font-medium text-[#1a0dab] leading-tight line-clamp-2" x-text="title || 'Page Title Will Appear Here'"></h4>
                    <p class="text-[11px] text-[#4d5156] mt-1 leading-normal line-clamp-3" x-text="description || 'Page meta description snippet will appear here to summarize search intent and attract high organic click-through rates.'"></p>
                </div>
            </div>
        </div>

        <!-- Primary Core Meta Information -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass text-brand-600"></i>
                <span>Core Search Engine Metadata</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Page Key (Readonly) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Page Key (Identifier)</label>
                    <input type="text" name="page_key" value="{{ old('page_key', $seoMeta->page_key) }}" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 font-mono text-xs cursor-not-allowed">
                    <p class="text-[11px] text-slate-400 mt-1">Internal unique key matching the route or page name</p>
                </div>

                <!-- Page Display Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Page Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="page_name" value="{{ old('page_name', $seoMeta->page_name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <p class="text-[11px] text-slate-400 mt-1">Friendly internal display title</p>
                </div>
            </div>

            <!-- Meta Title -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-slate-700">
                        Meta Title <span class="text-rose-500">*</span>
                    </label>
                    <div class="text-[11px] font-mono">
                        <span :class="title.length >= 45 && title.length <= 65 ? 'text-emerald-600 font-bold' : (title.length > 65 ? 'text-rose-500 font-bold' : 'text-slate-500')">
                            <span x-text="title.length"></span> / 60 chars
                        </span>
                        <span class="text-slate-400 text-[10px] ml-1">(Recommended: 50-60)</span>
                    </div>
                </div>
                <input type="text" name="meta_title" x-model="title" value="{{ old('meta_title', $seoMeta->meta_title) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="e.g. Luxury Homes & Flats for Sale | RealEstate Pro">
                <!-- Progress bar -->
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="h-full transition-all duration-200" :style="'width: ' + Math.min((title.length / 60) * 100, 100) + '%'" :class="title.length >= 45 && title.length <= 65 ? 'bg-emerald-500' : (title.length > 65 ? 'bg-rose-500' : 'bg-amber-400')"></div>
                </div>
            </div>

            <!-- Meta Description -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-slate-700">
                        Meta Description <span class="text-rose-500">*</span>
                    </label>
                    <div class="text-[11px] font-mono">
                        <span :class="description.length >= 130 && description.length <= 165 ? 'text-emerald-600 font-bold' : (description.length > 165 ? 'text-rose-500 font-bold' : 'text-slate-500')">
                            <span x-text="description.length"></span> / 160 chars
                        </span>
                        <span class="text-slate-400 text-[10px] ml-1">(Recommended: 140-160)</span>
                    </div>
                </div>
                <textarea name="meta_description" x-model="description" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="Brief, compelling synopsis of this page to drive high organic search click rates...">{{ old('meta_description', $seoMeta->meta_description) }}</textarea>
                <!-- Progress bar -->
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="h-full transition-all duration-200" :style="'width: ' + Math.min((description.length / 160) * 100, 100) + '%'" :class="description.length >= 130 && description.length <= 165 ? 'bg-emerald-500' : (description.length > 165 ? 'bg-rose-500' : 'bg-amber-400')"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Meta Keywords -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Meta Keywords (Comma separated)</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seoMeta->meta_keywords) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="luxury flats, villas, buy property, rent apartment">
                    <p class="text-[11px] text-slate-400 mt-1">Secondary keywords for search engines</p>
                </div>

                <!-- Canonical URL -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Canonical URL</label>
                    <input type="url" name="canonical_url" x-model="canonical" value="{{ old('canonical_url', $seoMeta->canonical_url) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="{{ url('/') }}">
                    <p class="text-[11px] text-slate-400 mt-1">Prevents duplicate content penalties by declaring master page URL</p>
                </div>
            </div>
        </div>

        <!-- Robots Indexing Directives -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-robot text-purple-600"></i>
                <span>Robots & Crawler Directives</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="pt-0.5">
                        <input type="checkbox" id="is_indexed" name="is_indexed" value="1" {{ old('is_indexed', $seoMeta->is_indexed) ? 'checked' : '' }} class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                    </div>
                    <div>
                        <label for="is_indexed" class="text-xs font-bold text-slate-800 cursor-pointer">Allow Search Engine Indexing (INDEX)</label>
                        <p class="text-[11px] text-slate-500 mt-0.5">If unchecked, generates <code class="text-[10px] bg-slate-200/70 px-1 py-0.5 rounded text-slate-700 font-mono">noindex</code> to hide page from Google, Bing & AI crawlers.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="pt-0.5">
                        <input type="checkbox" id="is_followed" name="is_followed" value="1" {{ old('is_followed', $seoMeta->is_followed) ? 'checked' : '' }} class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                    </div>
                    <div>
                        <label for="is_followed" class="text-xs font-bold text-slate-800 cursor-pointer">Follow Links on this Page (FOLLOW)</label>
                        <p class="text-[11px] text-slate-500 mt-0.5">If unchecked, generates <code class="text-[10px] bg-slate-200/70 px-1 py-0.5 rounded text-slate-700 font-mono">nofollow</code> to stop PageRank flow to linked destinations.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media & OpenGraph Sharing -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-share-nodes text-indigo-600"></i>
                <span>OpenGraph & Social Media Sharing</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- OG Title -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Social Share Title (og:title)</label>
                    <input type="text" name="og_title" x-model="ogTitle" value="{{ old('og_title', $seoMeta->og_title) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="Defaults to Meta Title if blank">
                </div>

                <!-- Twitter Card -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Twitter Card Format</label>
                    <select name="twitter_card" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white">
                        <option value="summary_large_image" {{ old('twitter_card', $seoMeta->twitter_card) == 'summary_large_image' ? 'selected' : '' }}>summary_large_image (Recommended - Wide photo preview)</option>
                        <option value="summary" {{ old('twitter_card', $seoMeta->twitter_card) == 'summary' ? 'selected' : '' }}>summary (Standard square thumbnail preview)</option>
                    </select>
                </div>
            </div>

            <!-- OG Description -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Social Share Description (og:description)</label>
                <textarea name="og_description" x-model="ogDescription" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="Defaults to Meta Description if blank">{{ old('og_description', $seoMeta->og_description) }}</textarea>
            </div>

            <!-- OG Image Upload & URL Options -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-slate-700">Social Share Image (og:image / twitter:image)</label>
                    
                    <!-- Toggle between Upload & URL -->
                    <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-[11px] font-semibold text-slate-600">
                        <button type="button" 
                                @click="imageMode = 'upload'" 
                                :class="imageMode === 'upload' ? 'bg-white text-brand-600 shadow-xs' : 'text-slate-500 hover:text-slate-900'" 
                                class="px-2.5 py-1 rounded-md transition flex items-center gap-1.5">
                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                            <span>Upload File</span>
                        </button>
                        <button type="button" 
                                @click="imageMode = 'url'" 
                                :class="imageMode === 'url' ? 'bg-white text-brand-600 shadow-xs' : 'text-slate-500 hover:text-slate-900'" 
                                class="px-2.5 py-1 rounded-md transition flex items-center gap-1.5">
                            <i class="fa-solid fa-link text-xs"></i>
                            <span>Image URL</span>
                        </button>
                    </div>
                </div>

                <!-- Option A: File Upload Dropzone -->
                <div x-show="imageMode === 'upload'" class="space-y-2">
                    <div class="relative border-2 border-dashed border-slate-200 hover:border-brand-400 transition rounded-2xl p-5 text-center bg-slate-50/60 hover:bg-slate-50 group">
                        <input type="file" 
                               id="og_image_file" 
                               name="og_image_file" 
                               accept="image/jpeg,image/png,image/jpg,image/webp" 
                               @change="previewImage($event)" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="space-y-1.5 pointer-events-none">
                            <div class="w-10 h-10 mx-auto rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg group-hover:scale-105 transition-transform shadow-xs">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-brand-600 transition">Click to browse or drag & drop image</span>
                                <p class="text-[11px] text-slate-400 mt-0.5">PNG, JPG, WEBP up to 5MB (1200 x 630 px recommended for Facebook & Twitter)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Option B: Remote Image URL -->
                <div x-show="imageMode === 'url'" class="space-y-2">
                    <input type="url" 
                           name="og_image" 
                           x-model="ogImage" 
                           value="{{ old('og_image', $seoMeta->og_image) }}" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500" 
                           placeholder="https://yourdomain.com/og-banner.jpg (1200x630 recommended)">
                </div>
                
                <input type="hidden" name="remove_og_image" :value="ogImage ? '0' : '1'">

                <!-- Image Live Preview -->
                <div x-show="ogImage" class="mt-3 p-3 bg-slate-50 rounded-xl border border-slate-200 max-w-md">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-eye text-slate-400"></i>
                            <span>Social Card Preview</span>
                        </span>
                        <button type="button" @click="clearImage()" class="text-[11px] text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                            <span>Remove Image</span>
                        </button>
                    </div>
                    <img :src="ogImage" alt="OG Preview" class="w-full h-44 object-cover rounded-lg border border-slate-200 shadow-xs" x-on:error="$el.style.display='none'">
                </div>
            </div>
        </div>

        <!-- Schema.org JSON-LD Structured Data -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-code text-amber-600"></i>
                    <span>Structured Data (JSON-LD Schema)</span>
                </h3>
                <span class="text-[11px] font-mono px-2 py-0.5 rounded-full" :class="jsonValid ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800 font-bold'">
                    <span x-text="jsonValid ? 'Valid JSON' : 'Invalid JSON Syntax'"></span>
                </span>
            </div>

            <p class="text-xs text-slate-500">Provide Schema.org JSON-LD structured data to trigger Google Rich Snippets, Knowledge Panels, and AI entity grounding.</p>

            <div>
                <textarea name="json_ld" x-model="jsonLd" x-on:input="validateJson()" rows="8" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-mono text-xs bg-slate-900 text-emerald-400 focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder='{ "&#64;context": "https://schema.org", "&#64;type": "WebPage", "name": "Page Title" }'>{{ old('json_ld', $seoMeta->json_ld) }}</textarea>
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="sticky bottom-4 z-20 bg-white/95 backdrop-blur border border-slate-200/80 rounded-2xl p-4 shadow-lg flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <i class="fa-solid fa-cloud-arrow-up text-brand-600"></i>
                <span>Changes apply immediately to frontend meta tags & search crawlers</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.seo.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save SEO Settings</span>
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

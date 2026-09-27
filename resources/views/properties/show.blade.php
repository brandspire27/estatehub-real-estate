@extends('layouts.app')

@section('title', $property->title . ' in ' . $property->area . ', ' . $property->city . ' — EstateHub')

@push('meta')
    <meta name="description" content="{{ Str::limit(strip_tags($property->description), 155) }}">
    <link rel="canonical" href="{{ $property->url }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $property->title }} | EstateHub" />
    <meta property="og:description" content="{{ Str::limit(strip_tags($property->description), 155) }}" />
    <meta property="og:image" content="{{ $property->primary_image_url }}" />
    <meta property="og:url" content="{{ $property->url }}" />

    <!-- Real Estate Listing Schema (JSON-LD) -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'SingleFamilyResidence',
        'name' => $property->title,
        'description' => Str::limit(strip_tags($property->description), 200),
        'image' => $property->images->pluck('image_url')->toArray(),
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $property->city,
            'addressRegion' => $property->area,
            'addressCountry' => 'IN',
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => (string)$property->price,
            'priceCurrency' => 'INR',
            'availability' => 'https://schema.org/InStock',
            'seller' => [
                '@type' => 'Person',
                'name' => $property->user->name ?? 'Verified Seller',
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
@php
    $mediaList = [];
    if (!empty($property->youtube_embed_url)) {
        $mediaList[] = [
            'type' => 'video',
            'embed_url' => $property->youtube_embed_url,
            'thumb_url' => $property->youtube_thumbnail_url ?: ('https://img.youtube.com/vi/' . $property->youtube_video_id . '/hqdefault.jpg'),
            'label' => 'HD Video Walkthrough Tour',
        ];
    }
    foreach ($property->images as $img) {
        $mediaList[] = [
            'type' => 'image',
            'url' => $img->image_url,
            'thumb_url' => $img->image_url,
            'label' => $property->title . ' Photo',
        ];
    }
    if (empty($mediaList)) {
        $mediaList[] = [
            'type' => 'image',
            'url' => $property->primary_image_url,
            'thumb_url' => $property->primary_image_url,
            'label' => $property->title . ' Photo',
        ];
    }

    $pricePerSqft = round((float)$property->price / max(1, $property->area_sqft));
    $defaultLoan = round((float)$property->price * 0.8);
@endphp

<!-- Breadcrumb Strip -->
<div class="bg-white border-b border-slate-200/80 py-3.5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <nav class="flex text-xs font-medium text-slate-500 gap-2 items-center flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-brand-600 transition flex items-center gap-1">
                    <i class="fa-solid fa-house text-[10px]"></i>
                    <span>Home</span>
                </a>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <a href="{{ route('properties.index') }}" class="hover:text-brand-600 transition">Properties</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <a href="{{ route('properties.index', ['city' => $property->city]) }}" class="hover:text-brand-600 transition font-semibold text-slate-700">{{ $property->city }}</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <a href="{{ route('properties.index', ['city' => $property->city, 'locality' => $property->area]) }}" class="hover:text-brand-600 transition">{{ $property->area }}</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                <span class="text-slate-900 font-bold truncate max-w-xs sm:max-w-sm">{{ $property->title }}</span>
            </nav>

            <div class="hidden sm:flex items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Verified Listing
                </span>
                <span class="text-slate-400 font-mono text-[11px]">#PROP-{{ str_pad($property->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10"
     x-data="propertyDetailPage({
         mediaList: {{ Js::from($mediaList) }},
         propertyId: {{ $property->id }},
         propertyTitle: '{{ addslashes($property->title) }}',
         propertyPrice: {{ (float)$property->price }},
         defaultLoan: {{ $defaultLoan }},
         sellerPhone: '{{ $property->user->phone ?? '9876543212' }}',
         maskedPhone: '{{ $property->masked_phone }}',
         sellerName: '{{ addslashes($property->user->name ?? 'Rajesh Agarwal') }}',
         buyerName: '{{ addslashes(Auth::user()->name ?? '') }}',
         buyerPhone: '{{ addslashes(Auth::user()->phone ?? '') }}',
         buyerEmail: '{{ addslashes(Auth::user()->email ?? '') }}',
         city: '{{ addslashes($property->city) }}',
         area: '{{ addslashes($property->area) }}',
         inquiryUrl: '{{ route('properties.inquiry', $property) }}',
         csrfToken: '{{ csrf_token() }}'
     })">

    <!-- Top Headline Banner: Badges, Title, Wishlist & Actions -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2.5 max-w-3xl">
                <!-- Badges Row -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-gradient-to-r from-brand-600 to-indigo-600 text-white shadow-sm shadow-brand-500/20">
                        {{ $property->property_type }}
                    </span>
                    @if($property->bhk)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-900 text-white">
                            {{ $property->bhk }} BHK Executive
                        </span>
                    @endif
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-certificate text-emerald-600"></i>
                        <span>RERA & Title Clear</span>
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        <span>{{ $property->possession_status }}</span>
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                        Zero Brokerage
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    {{ $property->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-slate-600">
                    <div class="flex items-center gap-1.5 font-semibold text-brand-600">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>{{ $property->area }}, {{ $property->city }}</span>
                    </div>
                    <span class="text-slate-300">•</span>
                    <div class="flex items-center gap-1.5 text-slate-500">
                        <i class="fa-solid fa-ruler-combined text-slate-400"></i>
                        <span>{{ number_format($property->area_sqft) }} sq.ft. Carpet Area</span>
                    </div>
                    <span class="text-slate-300">•</span>
                    <div class="flex items-center gap-1.5 text-slate-500">
                        <i class="fa-solid fa-eye text-slate-400"></i>
                        <span>{{ number_format($property->views) }} views</span>
                    </div>
                </div>
            </div>

            <!-- Price Card & Top Action Buttons -->
            <div class="flex flex-col sm:flex-row lg:flex-col sm:items-center lg:items-end justify-between gap-4 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                <div class="text-left lg:text-right">
                    <div class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">All-Inclusive Asking Price</div>
                    <div class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-0.5 bg-gradient-to-r from-slate-900 to-brand-700 bg-clip-text text-transparent">
                        {{ $property->formatted_price }}
                    </div>
                    <div class="flex items-center lg:justify-end gap-2 text-xs text-slate-500 mt-1">
                        <span class="font-bold text-slate-700">₹{{ number_format($pricePerSqft) }}</span>
                        <span>/ sq.ft</span>
                        <span class="text-slate-300">•</span>
                        <a href="#emi-calculator" class="text-brand-600 hover:text-brand-700 font-semibold underline decoration-brand-300">
                            Est. EMI ₹<span x-text="monthlyEmi.toLocaleString('en-IN')"></span>/mo
                        </a>
                    </div>
                </div>

                <!-- Action Button Toolbar -->
                <div class="flex items-center gap-2">
                    <button type="button" @click="toggleWishlist()" :class="wishlisted ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'" class="px-3.5 py-2 rounded-xl border text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-heart" :class="wishlisted ? 'fa-solid text-rose-600' : 'fa-regular text-slate-500'"></i>
                        <span x-text="wishlisted ? 'Saved' : 'Save'"></span>
                    </button>

                    <button type="button" @click="shareListing()" class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-share-nodes text-slate-500"></i>
                        <span x-text="copied ? 'Link Copied!' : 'Share'"></span>
                    </button>

                    <button type="button" @click="window.print()" class="hidden sm:inline-flex px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold transition items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-print text-slate-500"></i>
                        <span>Print</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid: Left (Gallery, Specifications, Overview, Amenities, EMI, Neighborhood) | Right (Seller Passport, Visit Scheduler) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10 items-start">
        
        <!-- Left 2 Cols -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- 1. Cinematic Gallery & High-Res Viewport -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-4 sm:p-6 shadow-sm overflow-hidden">
                
                <!-- Main Screen -->
                <div class="relative aspect-[16/10] sm:aspect-[16/9] rounded-2xl overflow-hidden bg-slate-950 mb-3 shadow-xl group select-none">
                    
                    <!-- Slide Viewport: Video or Image -->
                    <template x-if="media[currentIndex].type === 'video'">
                        <div class="w-full h-full relative bg-black flex items-center justify-center">
                            <iframe :src="media[currentIndex].embed_url" 
                                    title="Property Video Tour" 
                                    class="w-full h-full border-0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    </template>

                    <template x-if="media[currentIndex].type === 'image'">
                        <div class="w-full h-full relative cursor-pointer" @click="lightboxOpen = true">
                            <img :src="media[currentIndex].url" 
                                 :alt="media[currentIndex].label" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="px-4 py-2 rounded-xl bg-black/70 backdrop-blur-md text-white text-xs font-bold flex items-center gap-2 shadow-lg">
                                    <i class="fa-solid fa-expand"></i>
                                    <span>Click to View Fullscreen</span>
                                </span>
                            </div>
                        </div>
                    </template>

                    <!-- Top Left Badges -->
                    <div class="absolute top-4 left-4 flex flex-wrap gap-2 pointer-events-none z-10">
                        <template x-if="media[currentIndex].type === 'video'">
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-red-600 text-white shadow-lg flex items-center gap-1.5 animate-pulse">
                                <i class="fa-brands fa-youtube text-sm"></i>
                                <span>HD Video Tour</span>
                            </span>
                        </template>
                        <template x-if="media[currentIndex].type === 'image'">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-950/75 text-white backdrop-blur-md border border-white/10">
                                Photo <span x-text="(currentIndex + 1) + ' of ' + media.length"></span>
                            </span>
                        </template>
                    </div>

                    <!-- Top Right Quick Fullscreen -->
                    <button type="button" @click="lightboxOpen = true" class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-slate-950/70 hover:bg-slate-900 text-white flex items-center justify-center backdrop-blur-md transition shadow-md z-10">
                        <i class="fa-solid fa-up-right-and-down-left-from-center text-xs"></i>
                    </button>

                    <!-- Slider Arrow Controls -->
                    <template x-if="media.length > 1">
                        <div>
                            <button @click="prevSlide()" 
                                    type="button" 
                                    aria-label="Previous Slide"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-slate-950/70 hover:bg-brand-600 text-white flex items-center justify-center backdrop-blur-md transition shadow-lg opacity-80 hover:opacity-100 focus:outline-none z-10">
                                <i class="fa-solid fa-chevron-left text-sm"></i>
                            </button>

                            <button @click="nextSlide()" 
                                    type="button" 
                                    aria-label="Next Slide"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-slate-950/70 hover:bg-brand-600 text-white flex items-center justify-center backdrop-blur-md transition shadow-lg opacity-80 hover:opacity-100 focus:outline-none z-10">
                                <i class="fa-solid fa-chevron-right text-sm"></i>
                            </button>
                        </div>
                    </template>

                    <!-- Bottom Bar Overlay -->
                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between pointer-events-none z-10 text-white">
                        <span class="text-xs font-bold bg-slate-950/75 backdrop-blur-md px-3 py-1 rounded-full border border-white/10" x-text="media[currentIndex].label"></span>
                        <div class="bg-slate-950/75 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold border border-white/10">
                            <span x-text="(currentIndex + 1) + ' / ' + media.length"></span>
                        </div>
                    </div>

                </div>

                <!-- Thumbnails Strip -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-2 px-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                            <i class="fa-solid fa-images text-brand-600"></i>
                            <span>Verified Property Gallery (<span x-text="media.length"></span> items)</span>
                        </span>
                        <button type="button" @click="lightboxOpen = true" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                            View All Fullscreen &rarr;
                        </button>
                    </div>

                    <div class="flex gap-2.5 overflow-x-auto pb-2 scrollbar-thin">
                        <template x-for="(item, idx) in media" :key="idx">
                            <button @click="currentIndex = idx" 
                                    type="button" 
                                    class="w-24 h-16 sm:w-28 sm:h-18 rounded-xl overflow-hidden border-2 relative flex-shrink-0 transition cursor-pointer group focus:outline-none"
                                    :class="currentIndex === idx ? 'border-brand-600 ring-2 ring-brand-500/40 shadow-md scale-[1.02]' : 'border-transparent opacity-70 hover:opacity-100 hover:border-slate-300'">
                                
                                <img :src="item.thumb_url" :alt="item.label" class="w-full h-full object-cover">

                                <!-- Video Overlay Indicator -->
                                <template x-if="item.type === 'video'">
                                    <div class="absolute inset-0 bg-slate-950/40 group-hover:bg-slate-950/20 flex flex-col items-center justify-center text-white transition">
                                        <div class="w-6 h-6 rounded-full bg-red-600 flex items-center justify-center shadow-lg">
                                            <i class="fa-solid fa-play text-[9px] text-white ml-0.5"></i>
                                        </div>
                                        <span class="text-[9px] font-bold mt-1 bg-slate-900/90 px-1 rounded text-white tracking-tight">Video</span>
                                    </div>
                                </template>

                                <!-- Active Check Badge -->
                                <div x-show="currentIndex === idx" class="absolute top-1 right-1 w-4 h-4 rounded-full bg-brand-600 text-white flex items-center justify-center text-[9px]">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

            </div>

            <!-- 2. Bento Grid: Key Architectural Specifications -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-cube text-brand-600"></i>
                        <span>Architectural & Structural Highlights</span>
                    </h3>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        100% On-Site Verified
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <!-- Bedrooms -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/70 hover:border-brand-300 transition group">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-sm mb-2 group-hover:scale-110 transition">
                            <i class="fa-solid fa-bed"></i>
                        </div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Bedrooms</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            {{ $property->bhk ? $property->bhk . ' BHK Luxury' : 'Studio / Open' }}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">All En-Suite Baths</div>
                    </div>

                    <!-- Super Built-Up Area -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/70 hover:border-brand-300 transition group">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm mb-2 group-hover:scale-110 transition">
                            <i class="fa-solid fa-ruler-combined"></i>
                        </div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Super Area</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            {{ number_format($property->area_sqft) }} sq.ft.
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">Carpet: ~{{ number_format(round($property->area_sqft * 0.85)) }} sq.ft.</div>
                    </div>

                    <!-- Facing / Vaastu -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/70 hover:border-brand-300 transition group">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm mb-2 group-hover:scale-110 transition">
                            <i class="fa-solid fa-compass"></i>
                        </div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Facing & Orientation</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            North-East
                        </div>
                        <div class="text-[10px] text-emerald-600 font-bold mt-0.5">100% Vaastu Compliant</div>
                    </div>

                    <!-- Possession -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/70 hover:border-brand-300 transition group">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm mb-2 group-hover:scale-110 transition">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Possession</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            {{ $property->possession_status }}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">Immediate Handover</div>
                    </div>

                    <!-- Parking -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/70 hover:border-brand-300 transition group">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm mb-2 group-hover:scale-110 transition">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Reserved Parking</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            2 Covered Bays
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">EV Charging Ready</div>
                    </div>

                    <!-- Floor & Structure -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/70 hover:border-brand-300 transition group">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm mb-2 group-hover:scale-110 transition">
                            <i class="fa-solid fa-stairs"></i>
                        </div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Structure</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            G + 1 Duplex
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">Private Rooftop Deck</div>
                    </div>

                    <!-- Furnishing -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/70 hover:border-brand-300 transition group">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm mb-2 group-hover:scale-110 transition">
                            <i class="fa-solid fa-couch"></i>
                        </div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Furnishing</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            Semi-Furnished
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">Italian Modular Kitchen</div>
                    </div>

                    <!-- Title / Ownership -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/70 hover:border-brand-300 transition group">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm mb-2 group-hover:scale-110 transition">
                            <i class="fa-solid fa-stamp"></i>
                        </div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Title & Deed</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            Freehold Land
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">Clear Municipal Sanction</div>
                    </div>
                </div>
            </div>

            <!-- 3. Property Overview & Architectural Description -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                <h3 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-brand-600"></i>
                    <span>Property Overview & Architectural Narrative</span>
                </h3>

                <div class="text-slate-700 text-sm sm:text-base leading-relaxed space-y-4">
                    <p class="text-slate-800 font-medium text-base sm:text-lg leading-snug border-l-4 border-brand-600 pl-4 py-1 bg-brand-50/40 rounded-r-xl">
                        {{ $property->description }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-xs font-bold text-slate-900 block">Grand Living & Double-Height Ceiling</strong>
                                <span class="text-xs text-slate-500">18-foot soaring ceilings with floor-to-ceiling French windows flooding natural ambient daylight.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-xs font-bold text-slate-900 block">Chef-Grade Italian Modular Kitchen</strong>
                                <span class="text-xs text-slate-500">Equipped with quartz waterfall countertops, built-in chimney, pantry and soft-closing Blum cabinetry.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-xs font-bold text-slate-900 block">Private Landscaped Lawn & Garden</strong>
                                <span class="text-xs text-slate-500">Exclusive private green perimeter with stone-paved walkways and outdoor barbecue setup.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-xs font-bold text-slate-900 block">Rooftop Sunset Entertainment Deck</strong>
                                <span class="text-xs text-slate-500">Weather-proof tile lounge area offering unhindered vistas of the lush green Assam hill ranges.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Curated Luxury Amenities Bento Grid -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-sparkles text-amber-500"></i>
                            <span>Curated Lifestyle Amenities & Features</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Modern comforts, eco-sustainability, and 24/7 security features</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5 text-xs sm:text-sm font-semibold text-slate-700">
                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <span>24/7 Gated Security</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <span>CCTV Surveillance</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <span>100% Power Backup</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-solar-panel"></i>
                        </div>
                        <span>Rooftop Solar Plant</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-faucet-drip"></i>
                        </div>
                        <span>24hr Water Supply</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-green-100 text-green-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-seedling"></i>
                        </div>
                        <span>Landscaped Lawn</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-charging-station"></i>
                        </div>
                        <span>EV Fast Charging Point</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-cloud-sun-rain"></i>
                        </div>
                        <span>Rainwater Harvesting</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-brand-50/50 hover:border-brand-200 transition">
                        <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center text-sm flex-shrink-0">
                            <i class="fa-solid fa-compass"></i>
                        </div>
                        <span>100% Vaastu Planned</span>
                    </div>
                </div>
            </div>

            <!-- 5. Interactive Home Loan & EMI Financial Simulator Widget -->
            <div id="emi-calculator" class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5 mb-6">
                    <div>
                        <span class="text-[11px] font-black uppercase tracking-widest text-brand-400">Financial Suite</span>
                        <h3 class="text-xl sm:text-2xl font-black tracking-tight text-white mt-0.5">Home Loan EMI Calculator</h3>
                        <p class="text-xs text-slate-400">Simulate monthly installments based on prevailing lending rates</p>
                    </div>

                    <div class="text-left sm:text-right bg-white/10 px-4 py-2.5 rounded-2xl backdrop-blur-md">
                        <div class="text-[10px] uppercase font-bold text-slate-300">Estimated Monthly EMI</div>
                        <div class="text-2xl sm:text-3xl font-black text-emerald-400 tracking-tight">
                            ₹<span x-text="monthlyEmi.toLocaleString('en-IN')"></span>
                            <span class="text-xs font-normal text-slate-300">/mo</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Loan Amount Slider -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-slate-400">Loan Amount</span>
                            <span class="text-white font-mono font-bold">₹<span x-text="Number(loanAmount).toLocaleString('en-IN')"></span></span>
                        </div>
                        <input type="range" x-model="loanAmount" min="1000000" :max="propertyPrice" step="100000" class="w-full accent-brand-500 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-500">
                            <span>₹10 Lakh</span>
                            <span>₹{{ number_format($property->price / 100000) }} Lakh</span>
                        </div>
                    </div>

                    <!-- Interest Rate Slider -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-slate-400">Interest Rate (p.a.)</span>
                            <span class="text-white font-mono font-bold"><span x-text="interestRate"></span>%</span>
                        </div>
                        <input type="range" x-model="interestRate" min="7.0" max="12.0" step="0.1" class="w-full accent-brand-500 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-500">
                            <span>7.0%</span>
                            <span>12.0%</span>
                        </div>
                    </div>

                    <!-- Loan Tenure Slider -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-slate-400">Tenure</span>
                            <span class="text-white font-mono font-bold"><span x-text="loanTenure"></span> Years</span>
                        </div>
                        <input type="range" x-model="loanTenure" min="5" max="30" step="1" class="w-full accent-brand-500 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-500">
                            <span>5 Yrs</span>
                            <span>30 Yrs</span>
                        </div>
                    </div>
                </div>

                <!-- Financial Breakdown Bar -->
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                    <div class="flex flex-wrap items-center justify-between text-xs text-slate-300 gap-2">
                        <div>Principal: <strong class="text-white">₹<span x-text="Number(loanAmount).toLocaleString('en-IN')"></span></strong></div>
                        <div>Total Interest: <strong class="text-amber-400">₹<span x-text="totalInterest.toLocaleString('en-IN')"></span></strong></div>
                        <div>Total Payable: <strong class="text-emerald-400">₹<span x-text="totalPayable.toLocaleString('en-IN')"></span></strong></div>
                    </div>

                    <!-- Visual Progress Bar -->
                    <div class="w-full h-2.5 rounded-full bg-slate-800 overflow-hidden flex">
                        <div class="bg-brand-500 transition-all duration-300" :style="'width: ' + principalRatio + '%'" title="Principal"></div>
                        <div class="bg-amber-400 transition-all duration-300" :style="'width: ' + (100 - principalRatio) + '%'" title="Interest"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-brand-500"></span> Principal (<span x-text="principalRatio"></span>%)</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Total Interest (<span x-text="100 - principalRatio"></span>%)</span>
                    </div>
                </div>
            </div>

            <!-- 6. Locality & Neighborhood Connectivity Intelligence -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-brand-600"></i>
                            <span>Locality & Neighborhood Intelligence: {{ $property->area }}, {{ $property->city }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Strategic location with immediate highway connectivity and premium social infrastructure</p>
                    </div>
                    <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-black border border-emerald-200">
                        <i class="fa-solid fa-star text-amber-500"></i>
                        <span>9.4 / 10 Liveability Score</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Transit -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-car-side text-brand-600"></i>
                            <span>Transit & Roads</span>
                        </div>
                        <ul class="text-xs text-slate-600 space-y-1.5">
                            <li class="flex justify-between"><span>Beltola Tiniali:</span> <strong class="text-slate-900">500 m (2 min)</strong></li>
                            <li class="flex justify-between"><span>GS Road Highway:</span> <strong class="text-slate-900">1.2 km (4 min)</strong></li>
                            <li class="flex justify-between"><span>Guwahati Station:</span> <strong class="text-slate-900">9.5 km</strong></li>
                            <li class="flex justify-between"><span>LGBI Airport:</span> <strong class="text-slate-900">28 km</strong></li>
                        </ul>
                    </div>

                    <!-- Education -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
                            <span>Schools & Colleges</span>
                        </div>
                        <ul class="text-xs text-slate-600 space-y-1.5">
                            <li class="flex justify-between"><span>DPS Guwahati:</span> <strong class="text-slate-900">1.8 km</strong></li>
                            <li class="flex justify-between"><span>Royal Global Univ:</span> <strong class="text-slate-900">4.2 km</strong></li>
                            <li class="flex justify-between"><span>Sanskriti Gurukul:</span> <strong class="text-slate-900">6.0 km</strong></li>
                            <li class="flex justify-between"><span>Cotton University:</span> <strong class="text-slate-900">10 km</strong></li>
                        </ul>
                    </div>

                    <!-- Healthcare -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-hospital text-rose-600"></i>
                            <span>Medical & Health</span>
                        </div>
                        <ul class="text-xs text-slate-600 space-y-1.5">
                            <li class="flex justify-between"><span>GNRC Sixmile:</span> <strong class="text-slate-900">3.1 km</strong></li>
                            <li class="flex justify-between"><span>Health City Hospital:</span> <strong class="text-slate-900">2.8 km</strong></li>
                            <li class="flex justify-between"><span>Dispur Poly Clinic:</span> <strong class="text-slate-900">2.2 km</strong></li>
                            <li class="flex justify-between"><span>Apollo Clinic:</span> <strong class="text-slate-900">1.5 km</strong></li>
                        </ul>
                    </div>

                    <!-- Shopping & Lifestyle -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-basket-shopping text-emerald-600"></i>
                            <span>Retail & Dining</span>
                        </div>
                        <ul class="text-xs text-slate-600 space-y-1.5">
                            <li class="flex justify-between"><span>Beltola Organic Mkt:</span> <strong class="text-slate-900">400 m</strong></li>
                            <li class="flex justify-between"><span>City Centre Mall:</span> <strong class="text-slate-900">4.5 km</strong></li>
                            <li class="flex justify-between"><span>Novotel Luxury Hotel:</span> <strong class="text-slate-900">3.5 km</strong></li>
                            <li class="flex justify-between"><span>Pantaloons Retail:</span> <strong class="text-slate-900">2.0 km</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            @if(Auth::check() && (Auth::id() === $property->user_id || Auth::user()->isAdmin() || Auth::user()->isStaff()))
                <!-- 🔒 PRIVATE SELLER DOSSIER (CONFIDENTIAL - ONLY VISIBLE TO SELLER & ADMIN) -->
                <div class="bg-gradient-to-br from-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-indigo-800/60">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-indigo-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600/40 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-xl shadow-inner">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Restricted Access</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                                        Confidential &bull; Not Public
                                    </span>
                                </div>
                                <h3 class="text-xl font-black text-white tracking-tight mt-0.5">Private Seller Dossier</h3>
                                <p class="text-xs text-indigo-300">Visible only to you (Owner) and platform staff/admin. Never shown to buyers.</p>
                            </div>
                        </div>

                        <a href="{{ route('listings.edit', $property) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow transition flex-shrink-0">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit Dossier</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 text-xs">
                        <!-- KYC Details -->
                        <div class="p-4 rounded-2xl bg-indigo-900/40 border border-indigo-700/50 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold uppercase tracking-wider text-indigo-300 text-[11px] flex items-center gap-1.5">
                                    <i class="fa-solid fa-id-card text-indigo-400"></i>
                                    <span>KYC / Title Documents</span>
                                </span>
                                @if($property->kyc_document_type)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-[10px]">Document Added</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 font-bold text-[10px]">Pending Upload</span>
                                @endif
                            </div>

                            <div class="text-slate-200">
                                <div class="text-indigo-300 text-[11px]">Document Type:</div>
                                <div class="font-bold text-white text-sm">{{ $property->kyc_document_type ?: 'Not specified' }}</div>
                            </div>

                            <div class="text-slate-200">
                                <div class="text-indigo-300 text-[11px]">Document Number / Ref:</div>
                                <div class="font-mono font-bold text-white">{{ $property->kyc_document_number ?: 'N/A' }}</div>
                            </div>

                            @if($property->kyc_document_url)
                                <div class="pt-1">
                                    <a href="{{ $property->kyc_document_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow transition">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        <span>View KYC Document</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Private Remarks -->
                        <div class="p-4 rounded-2xl bg-indigo-900/40 border border-indigo-700/50 space-y-2">
                            <div class="font-extrabold uppercase tracking-wider text-indigo-300 text-[11px] flex items-center gap-1.5">
                                <i class="fa-solid fa-comment-dots text-indigo-400"></i>
                                <span>Internal Notes & Remarks</span>
                            </div>
                            <div class="text-slate-300 text-xs leading-relaxed whitespace-pre-line bg-indigo-950/60 p-3 rounded-xl border border-indigo-800/50 min-h-[90px]">
                                {{ $property->private_remarks ?: 'No private internal notes recorded yet. Use Edit Dossier to maintain negotiation minimums, key locations, or tenancy notes.' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Col: Verified Seller Concierge & Direct Contact -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Seller Profile & Inquiry Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-lg sticky top-28 space-y-6">
                
                <!-- Card Badge Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="fa-solid fa-shield-check text-emerald-600"></i>
                        <span>Verified Direct Seller</span>
                    </span>
                    <span class="text-[11px] font-bold text-slate-400 flex items-center gap-1">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        <span>Fast Response (&lt;15m)</span>
                    </span>
                </div>

                <!-- Seller Info -->
                <div class="flex items-center gap-3.5 pb-2">
                    <div class="relative flex-shrink-0">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 via-brand-700 to-indigo-600 text-white flex items-center justify-center font-black text-2xl uppercase shadow-md shadow-brand-500/20">
                            {{ substr($property->user->name ?? 'R', 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs ring-2 ring-white shadow" title="Verified Member">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-lg leading-tight">{{ $property->user->name ?? 'Rajesh Agarwal' }}</h4>
                        <p class="text-xs font-semibold text-slate-500 mt-0.5">Certified Marketplace Seller</p>
                        <div class="flex items-center gap-1 text-amber-500 text-xs mt-1.5 font-semibold">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>
                            <span class="text-slate-900 font-black ml-1">4.8</span>
                            <span class="text-slate-400 text-[11px] font-normal">(18 verified deals)</span>
                        </div>
                    </div>
                </div>

                <!-- Price Ribbon in Sidebar -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Demand Price</div>
                        <div class="text-2xl font-black text-slate-900">{{ $property->formatted_price }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Rate</div>
                        <div class="text-xs font-bold text-slate-700">₹{{ number_format($pricePerSqft) }}/sq.ft</div>
                    </div>
                </div>

                <!-- Contact Details with Phone Masking Shield -->
                <div class="space-y-3">
                    <!-- Phone Block -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 transition"
                         :class="unlocked ? 'bg-emerald-50/70 border-emerald-300' : 'hover:border-brand-300'">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-xs text-brand-600"></i>
                                <span>Direct Seller Phone</span>
                            </span>
                            <template x-if="!unlocked">
                                <span class="text-[10px] font-extrabold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <i class="fa-solid fa-lock text-[9px]"></i> Protected
                                </span>
                            </template>
                            <template x-if="unlocked">
                                <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <i class="fa-solid fa-lock-open text-[9px]"></i> Unlocked
                                </span>
                            </template>
                        </div>

                        <!-- Clickable Phone Trigger -->
                        <button type="button" 
                                @click="openPhoneFlow()" 
                                class="w-full text-left font-black text-lg text-slate-900 hover:text-brand-600 transition flex items-center justify-between group">
                            <span x-text="unlocked ? fullPhone : maskedPhone" class="tracking-wider font-mono"></span>
                            <template x-if="!unlocked">
                                <span class="text-xs font-extrabold text-brand-600 group-hover:underline flex items-center gap-1 bg-brand-50 px-2.5 py-1 rounded-lg">
                                    <span>Reveal</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </span>
                            </template>
                            <template x-if="unlocked">
                                <span class="text-xs font-extrabold text-emerald-600 flex items-center gap-1 bg-emerald-100 px-2.5 py-1 rounded-lg">
                                    <i class="fa-solid fa-phone-volume"></i> Call Now
                                </span>
                            </template>
                        </button>
                    </div>

                    <!-- Email Block -->
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs">
                        <span class="text-slate-500 font-semibold flex items-center gap-1.5">
                            <i class="fa-solid fa-envelope text-brand-600"></i>
                            <span>Direct Email</span>
                        </span>
                        <a href="mailto:{{ $property->user->email ?? 'seller@realestate.com' }}" class="font-bold text-slate-900 hover:text-brand-600 transition truncate max-w-[160px]">
                            {{ $property->user->email ?? 'seller@realestate.com' }}
                        </a>
                    </div>
                </div>

                <!-- Call to Action Buttons -->
                <div class="space-y-2.5 pt-2">
                    
                    <!-- Call / Unlock Button -->
                    <button type="button" 
                            @click="openPhoneFlow()" 
                            class="w-full py-3.5 px-4 rounded-xl font-extrabold text-sm shadow-md shadow-brand-500/20 flex items-center justify-center gap-2 transition"
                            :class="unlocked ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-brand-600 hover:bg-brand-700 text-white'">
                        <i class="fa-solid" :class="unlocked ? 'fa-phone-volume' : 'fa-lock-open'"></i>
                        <span x-text="unlocked ? 'Call ' + sellerName + ' (' + fullPhone + ')' : 'Click to Reveal Seller Phone'"></span>
                    </button>

                    <!-- WhatsApp Web Button -->
                    <template x-if="unlocked">
                        <a :href="whatsappUrl" 
                           target="_blank" 
                           class="w-full py-3.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-sm shadow flex items-center justify-center gap-2 transition">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>Chat on WhatsApp Web</span>
                        </a>
                    </template>

                    <template x-if="!unlocked">
                        <button type="button" 
                                @click="modalOpen = true" 
                                class="w-full py-3 px-4 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm flex items-center justify-center gap-2 transition">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
                            <span>Connect via WhatsApp</span>
                        </button>
                    </template>

                    <!-- Schedule Site Visit Trigger -->
                    <button type="button" 
                            @click="visitModalOpen = true" 
                            class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs shadow flex items-center justify-center gap-2 transition">
                        <i class="fa-solid fa-calendar-check text-amber-400"></i>
                        <span>Schedule Private Site Inspection</span>
                    </button>
                </div>

                <!-- Trust Guarantee Box -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-around text-center text-[10px] text-slate-500 font-semibold">
                    <div class="flex flex-col items-center gap-1">
                        <i class="fa-solid fa-shield-halved text-emerald-600 text-sm"></i>
                        <span>Zero Spam</span>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <i class="fa-solid fa-handshake-angle text-brand-600 text-sm"></i>
                        <span>Direct Owner</span>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <i class="fa-solid fa-award text-amber-500 text-sm"></i>
                        <span>RERA Verified</span>
                    </div>
                </div>

            </div>

            <!-- Buyer Requirement Match Box -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/80 text-amber-950 text-xs leading-relaxed shadow-sm">
                <div class="font-extrabold text-sm flex items-center gap-2 text-amber-900 mb-1.5">
                    <i class="fa-solid fa-lightbulb text-amber-600"></i>
                    <span>Have Specific Buyer Requirements?</span>
                </div>
                <p class="text-amber-800">
                    Post your exact budget, preferred BHK, and locality to be algorithmically paired with private off-market villas and apartments in {{ $property->city }}.
                </p>
                <a href="{{ route('requirements.create') }}" class="mt-3 inline-flex items-center gap-1.5 font-black text-amber-900 hover:text-black underline">
                    <span>Post Buyer Requirement (100% Free)</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

        </div>

    </div>

    <!-- Related Properties in Guwahati / Same Category -->
    @if($relatedProperties->isNotEmpty())
        <div class="mt-16 sm:mt-20 pt-10 border-t border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Similar Premium Properties in {{ $property->city }}</h2>
                    <p class="text-xs text-slate-500 mt-1">Curated recommendations matched on price tier, configuration, and locality</p>
                </div>
                <a href="{{ route('properties.index', ['city' => $property->city]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:text-brand-700 transition">
                    <span>Explore All {{ $property->city }} Listings</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProperties as $rel)
                    <x-property-card :property="$rel" />
                @endforeach
            </div>
        </div>
    @endif

    <!-- Fullscreen Lightbox Photo Viewer Modal -->
    <div x-show="lightboxOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-8 bg-black/95 backdrop-blur-md"
         role="dialog"
         aria-modal="true"
         @keydown.escape.window="lightboxOpen = false"
         @keydown.arrow-left.window="prevSlide()"
         @keydown.arrow-right.window="nextSlide()">
        
        <!-- Lightbox Close Button -->
        <button type="button" @click="lightboxOpen = false" class="absolute top-6 right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl transition z-20">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Lightbox Prev Button -->
        <button type="button" @click="prevSlide()" class="absolute left-4 sm:left-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-brand-600 text-white flex items-center justify-center text-lg transition z-20">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <!-- Lightbox Next Button -->
        <button type="button" @click="nextSlide()" class="absolute right-4 sm:right-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-brand-600 text-white flex items-center justify-center text-lg transition z-20">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <!-- Main Media Stage -->
        <div class="max-w-5xl max-h-[85vh] w-full flex flex-col items-center justify-center">
            <template x-if="media[currentIndex].type === 'video'">
                <div class="w-full aspect-[16/9] max-h-[75vh]">
                    <iframe :src="media[currentIndex].embed_url" title="Video Walkthrough" class="w-full h-full rounded-2xl border-0" allowfullscreen></iframe>
                </div>
            </template>
            <template x-if="media[currentIndex].type === 'image'">
                <img :src="media[currentIndex].url" :alt="media[currentIndex].label" class="max-w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl">
            </template>

            <div class="mt-4 text-center text-white">
                <h4 class="text-sm font-bold" x-text="media[currentIndex].label"></h4>
                <p class="text-xs text-slate-400 mt-0.5"><span x-text="(currentIndex + 1) + ' of ' + media.length"></span> &bull; Use Left/Right keys or arrows to navigate</p>
            </div>
        </div>
    </div>

    <!-- Phone Unlock / Buyer Details Modal -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        
        <div x-show="modalOpen" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="modalOpen = false" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

        <div x-show="modalOpen" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95" 
             class="relative bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-lg w-full p-6 sm:p-8 z-10 overflow-hidden">
            
            <button @click="modalOpen = false" type="button" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-slate-100 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <!-- Pre-Submission Form State -->
            <template x-if="!unlocked">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xl">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-brand-600">Direct Owner Connection</span>
                            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Unlock Verified Phone</h3>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed mb-5 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        Connect with <strong class="text-slate-900" x-text="sellerName"></strong> directly without broker intervention. Your phone number is verified to prevent automated commercial spam.
                    </p>

                    <form @submit.prevent="submitBuyerForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   x-model="formData.buyer_name" 
                                   required 
                                   placeholder="e.g. Rahul Sharma" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Phone Number <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">+91</span>
                                <input type="tel" 
                                       x-model="formData.buyer_phone" 
                                       required 
                                       maxlength="14"
                                       placeholder="9876543210" 
                                       class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Email Address <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input type="email" 
                                   x-model="formData.buyer_email" 
                                   placeholder="rahul@example.com" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Message to Seller
                            </label>
                            <textarea x-model="formData.message" 
                                      rows="2" 
                                      class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none text-slate-700"></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" 
                                    :disabled="submitting" 
                                    class="w-full py-3.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm shadow-md flex items-center justify-center gap-2 transition disabled:opacity-50">
                                <span x-show="!submitting" class="flex items-center gap-2">
                                    <i class="fa-solid fa-lock-open"></i>
                                    <span>Unlock & Show Phone Number</span>
                                </span>
                                <span x-show="submitting" class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                    <span>Verifying & Unlocking...</span>
                                </span>
                            </button>
                        </div>

                        <div class="text-center">
                            <span class="text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-shield-check text-emerald-500"></i>
                                <span>100% Privacy Protected &bull; Zero spam guarantee</span>
                            </span>
                        </div>
                    </form>
                </div>
            </template>

            <!-- Post-Submission Unlocked State -->
            <template x-if="unlocked">
                <div class="text-center py-4">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-4 animate-bounce">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Verification Successful</span>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mt-1 mb-2">Seller Phone Unlocked!</h3>
                    
                    <p class="text-xs text-slate-500 mb-6 max-w-sm mx-auto">
                        You can now call or WhatsApp <strong class="text-slate-900" x-text="sellerName"></strong> directly regarding {{ $property->title }}.
                    </p>

                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 mb-6">
                        <div class="text-xs font-semibold text-emerald-700 mb-1">Direct Phone Number</div>
                        <div class="text-3xl font-black text-slate-900 tracking-wider font-mono select-all" x-text="fullPhone"></div>
                        <div class="text-xs text-slate-500 mt-1">Verified Seller &bull; Available 9 AM – 8 PM</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a :href="telUrl" 
                           class="py-3.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm shadow-md flex items-center justify-center gap-2 transition">
                            <i class="fa-solid fa-phone-volume"></i>
                            <span>Call Now</span>
                        </a>

                        <a :href="whatsappUrl" 
                           target="_blank" 
                           class="py-3.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-sm shadow flex items-center justify-center gap-2 transition">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>WhatsApp Web</span>
                        </a>
                    </div>

                    <button @click="modalOpen = false" type="button" class="mt-6 text-xs text-slate-400 hover:text-slate-600 font-semibold underline">
                        Back to Property Details
                    </button>
                </div>
            </template>

        </div>
    </div>

    <!-- Private Inspection / Site Visit Booking Modal -->
    <div x-show="visitModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        
        <div x-show="visitModalOpen" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="visitModalOpen = false" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

        <div x-show="visitModalOpen" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95" 
             class="relative bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-md w-full p-6 sm:p-8 z-10 overflow-hidden">
            
            <button @click="visitModalOpen = false" type="button" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-slate-100 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600">VIP Concierge Service</span>
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Schedule Site Inspection</h3>
                </div>
            </div>

            <p class="text-xs text-slate-600 mb-4">
                Pick your preferred date & time for a guided on-site walkthrough of <strong class="text-slate-900">{{ $property->title }}</strong>.
            </p>

            <form @submit.prevent="scheduleVisit()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Preferred Date</label>
                    <input type="date" x-model="visitDate" required min="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Preferred Time Slot</label>
                    <select x-model="visitSlot" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none bg-white">
                        <option value="Morning (10:00 AM - 12:00 PM)">Morning (10:00 AM - 12:00 PM)</option>
                        <option value="Afternoon (1:00 PM - 3:00 PM)">Afternoon (1:00 PM - 3:00 PM)</option>
                        <option value="Evening (4:00 PM - 6:00 PM)">Evening (4:00 PM - 6:00 PM)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Your Phone Number</label>
                    <input type="tel" x-model="formData.buyer_phone" required placeholder="9876543210" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Confirm Appointment Request</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function propertyDetailPage(cfg) {
    return {
        media: cfg.mediaList || [],
        currentIndex: 0,
        lightboxOpen: false,
        modalOpen: false,
        visitModalOpen: false,
        unlocked: false,
        submitting: false,
        wishlisted: false,
        copied: false,
        fullPhone: cfg.sellerPhone,
        maskedPhone: cfg.maskedPhone,
        sellerName: cfg.sellerName,
        propertyPrice: cfg.propertyPrice,
        loanAmount: cfg.defaultLoan,
        interestRate: 8.5,
        loanTenure: 20,
        visitDate: new Date(Date.now() + 86400000).toISOString().split('T')[0],
        visitSlot: 'Morning (10:00 AM - 12:00 PM)',
        whatsappUrl: '',
        telUrl: '',
        formData: {
            buyer_name: cfg.buyerName,
            buyer_phone: cfg.buyerPhone,
            buyer_email: cfg.buyerEmail,
            message: 'Hi ' + cfg.sellerName + ', I am interested in your property \'' + cfg.propertyTitle + '\' in ' + cfg.area + ', ' + cfg.city + '. Please share more details.'
        },

        init() {
            this.buildLinks(this.fullPhone);
            const cached = localStorage.getItem('estatehub_unlocked_prop_' + cfg.propertyId);
            if (cached === 'true') {
                this.unlocked = true;
            }
            this.wishlisted = localStorage.getItem('estatehub_wishlist_' + cfg.propertyId) === 'true';
        },

        prevSlide() {
            this.currentIndex = (this.currentIndex > 0) ? (this.currentIndex - 1) : (this.media.length - 1);
        },

        nextSlide() {
            this.currentIndex = (this.currentIndex < this.media.length - 1) ? (this.currentIndex + 1) : 0;
        },

        toggleWishlist() {
            this.wishlisted = !this.wishlisted;
            localStorage.setItem('estatehub_wishlist_' + cfg.propertyId, this.wishlisted ? 'true' : 'false');
        },

        shareListing() {
            if (navigator.share) {
                navigator.share({
                    title: cfg.propertyTitle,
                    url: window.location.href
                }).catch(() => {});
            } else {
                navigator.clipboard.writeText(window.location.href);
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2500);
            }
        },

        buildLinks(phone) {
            let clean = phone.replace(/[^0-9]/g, '');
            if (clean.length === 10) clean = '91' + clean;
            this.whatsappUrl = 'https://wa.me/' + clean + '?text=' + encodeURIComponent('Hello ' + this.sellerName + ', I am interested in your property \'' + cfg.propertyTitle + '\' listed on EstateHub.');
            this.telUrl = 'tel:' + phone;
        },

        openPhoneFlow() {
            if (this.unlocked) {
                window.location.href = this.telUrl;
            } else {
                this.modalOpen = true;
            }
        },

        scheduleVisit() {
            alert('Your site visit request for ' + this.visitDate + ' (' + this.visitSlot + ') has been registered! ' + this.sellerName + ' will coordinate with you.');
            this.visitModalOpen = false;
        },

        async submitBuyerForm() {
            if (!this.formData.buyer_name.trim()) {
                alert('Please enter your full name.');
                return;
            }
            if (!this.formData.buyer_phone.trim() || this.formData.buyer_phone.trim().length < 8) {
                alert('Please enter a valid phone number.');
                return;
            }

            this.submitting = true;
            try {
                const response = await fetch(cfg.inquiryUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrfToken
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();
                if (data.success) {
                    this.unlocked = true;
                    this.fullPhone = data.phone;
                    this.whatsappUrl = data.whatsapp_url;
                    this.telUrl = data.tel_url;
                    localStorage.setItem('estatehub_unlocked_prop_' + cfg.propertyId, 'true');
                } else {
                    alert(data.message || 'Unable to submit inquiry.');
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred. Please try again.');
            } finally {
                this.submitting = false;
            }
        },

        get monthlyEmi() {
            const P = Number(this.loanAmount);
            const r = (Number(this.interestRate) / 12) / 100;
            const n = Number(this.loanTenure) * 12;
            if (r === 0) return Math.round(P / n);
            const emi = (P * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
            return Math.round(emi);
        },

        get totalPayable() {
            return this.monthlyEmi * (Number(this.loanTenure) * 12);
        },

        get totalInterest() {
            return Math.max(0, this.totalPayable - Number(this.loanAmount));
        },

        get principalRatio() {
            if (this.totalPayable === 0) return 100;
            return Math.round((Number(this.loanAmount) / this.totalPayable) * 100);
        }
    };
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'EstateHub — Find Properties & Match Requirements')

@section('content')
<!-- Hero Section with Search Bar -->
<div class="relative bg-slate-950 text-white overflow-hidden">
    <!-- Unsplash Hero Background with Cinematic Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=2000&q=85" 
             alt="Luxury Home Architecture" 
             class="w-full h-full object-cover object-center opacity-40 mix-blend-luminosity scale-105 animate-pulse duration-1000">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-slate-900/60"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-28 sm:pb-36 lg:pt-28">
        <div class="max-w-3xl">
            <!-- Pill Tag -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-500/10 border border-brand-400/30 text-brand-300 text-xs font-semibold mb-6 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-brand-400 animate-ping"></span>
                <span>India's 1st Algorithmic Buyer-Seller Match Engine</span>
            </div>

            <!-- Main Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.1] mb-6">
                Find a place <br class="hidden sm:inline">
                <span class="bg-gradient-to-r from-white via-slate-100 to-brand-300 bg-clip-text text-transparent">you'll genuinely love.</span>
            </h1>

            <p class="text-lg sm:text-xl text-slate-300 font-normal leading-relaxed mb-10 max-w-2xl">
                Explore thousands of verified homes, apartments, plots, and commercial properties across major Indian cities.
            </p>
        </div>

        <!-- Property Search Bar Component -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-2xl text-slate-800 max-w-5xl border border-slate-100">
            <form action="{{ route('properties.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Keyword / Locality -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Search Area / Keyword</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="keyword" placeholder="e.g. Whitefield, GS Road..." 
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-slate-50 hover:bg-white transition">
                    </div>
                </div>

                <!-- City -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">City</label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <select name="city" class="w-full pl-10 pr-8 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-slate-50 hover:bg-white transition appearance-none">
                            <option value="">All Indian Cities</option>
                            @foreach($cities as $c)
                                <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <!-- Property Type -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Property Type</label>
                    <div class="relative">
                        <i class="fa-solid fa-building absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <select name="property_type" class="w-full pl-10 pr-8 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-slate-50 hover:bg-white transition appearance-none">
                            <option value="">All Property Types</option>
                            <option value="Apartment">Apartment</option>
                            <option value="Flat">Flat</option>
                            <option value="Villa">Villa</option>
                            <option value="Plot">Plot</option>
                            <option value="Building">Independent Building</option>
                            <option value="Commercial Space">Commercial Space</option>
                            <option value="Land">Land</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-lg shadow-brand-500/25 flex items-center justify-center gap-2 transition duration-200">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Search Properties</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Platform Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-12 max-w-4xl text-slate-300">
            <div class="border-l-2 border-brand-500 pl-4">
                <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ number_format($stats['properties_count']) }}+</div>
                <div class="text-xs sm:text-sm text-slate-400 font-medium">Published Listings</div>
            </div>
            <div class="border-l-2 border-brand-500 pl-4">
                <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ number_format($stats['buyers_count']) }}+</div>
                <div class="text-xs sm:text-sm text-slate-400 font-medium">Registered Buyers</div>
            </div>
            <div class="border-l-2 border-brand-500 pl-4">
                <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ number_format($stats['sellers_count']) }}+</div>
                <div class="text-xs sm:text-sm text-slate-400 font-medium">Active Sellers</div>
            </div>
            <div class="border-l-2 border-brand-500 pl-4">
                <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ number_format($stats['cities_count']) }}+</div>
                <div class="text-xs sm:text-sm text-slate-400 font-medium">Major Indian Cities</div>
            </div>
        </div>
    </div>
</div>

<!-- Popular Cities Section (Section 18) -->
<section id="popular-cities" class="py-20 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600">Top Locations</span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Explore Popular Cities</h2>
                <p class="text-slate-500 text-sm mt-1">Discover residential and commercial properties in premier growth hubs</p>
            </div>
            <a href="{{ route('properties.index') }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 text-sm font-bold text-brand-600 hover:text-brand-700 transition">
                <span>View All Properties</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
            @foreach($cities as $city)
                <a href="{{ route('properties.index', ['city' => $city->name]) }}" class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 aspect-[3/4] flex flex-col justify-end p-4 border border-slate-200/80">
                    <img src="{{ $city->display_image_url }}" 
                         alt="{{ $city->name }}" 
                         loading="lazy"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>

                    <div class="relative z-10">
                        <h3 class="font-black text-white text-lg tracking-tight group-hover:text-brand-300 transition">{{ $city->name }}</h3>
                        <span class="text-xs text-slate-300 font-medium">
                            {{ $city->properties_count }} {{ Str::plural('Listing', $city->properties_count) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Properties Grid (Section 19) -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600">Fresh on the Market</span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Featured Properties</h2>
                <p class="text-slate-500 text-sm mt-1">Verified hand-picked properties with direct seller contact</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <a href="{{ route('properties.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    View Complete Catalog
                </a>
            </div>
        </div>

        @if($featuredProperties->isEmpty())
            <div class="text-center py-16 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                <i class="fa-solid fa-building-circle-exclamation text-4xl text-slate-400 mb-3"></i>
                <h4 class="text-lg font-bold text-slate-700">No properties published yet</h4>
                <p class="text-slate-500 text-sm mt-1">Check back shortly or run database seeds.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProperties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- How It Works Section (Section 20) -->
<section class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-wider text-brand-600">Simple 4-Step Process</span>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">How EstateHub Works</h2>
            <p class="text-slate-500 text-sm mt-2">Whether you are purchasing your dream home or liquidating an asset, our platform automates the match.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Step 1 -->
            <div class="relative bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="text-4xl font-black text-slate-200 mb-4">01</div>
                <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-magnifying-glass-location"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Search Properties</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Filter thousands of published listings by city, property type, budget, and BHK with zero friction.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="relative bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="text-4xl font-black text-slate-200 mb-4">02</div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-clipboard-question"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Post Requirement</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Buyers post their budget range, BHK preference, possession status, and preferred city.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="relative bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="text-4xl font-black text-slate-200 mb-4">03</div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Get Smart Matches</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Our multi-parameter engine computes compatibility scores from 0-100% instantly on every listing.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="relative bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <div class="text-4xl font-black text-slate-200 mb-4">04</div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Connect & Transact</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Direct phone and email contact between matched buyers and verified property owners.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Dual Call To Action Section (Section 21) -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Buyer CTA -->
            <div class="relative rounded-3xl p-8 sm:p-10 bg-gradient-to-br from-brand-900 via-brand-800 to-slate-900 text-white overflow-hidden shadow-xl">
                <div class="relative z-10 max-w-md">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md mb-4 uppercase tracking-wider">
                        For Home Buyers
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black tracking-tight mb-3">Looking to Buy a Property?</h3>
                    <p class="text-slate-200 text-sm mb-6 leading-relaxed">
                        Don't waste time scrolling through irrelevant listings. Post your requirement and let our matching engine notify you when compatible properties appear.
                    </p>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-slate-900 font-bold text-sm shadow-md hover:bg-slate-100 transition">
                        <span>Post Your Requirement</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <i class="fa-solid fa-house-chimney absolute right-4 bottom-4 text-9xl text-white/5 pointer-events-none"></i>
            </div>

            <!-- Seller CTA -->
            <div class="relative rounded-3xl p-8 sm:p-10 bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white overflow-hidden shadow-xl">
                <div class="relative z-10 max-w-md">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/30 backdrop-blur-md mb-4 uppercase tracking-wider">
                        For Property Sellers
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black tracking-tight mb-3">Have a Property to Sell?</h3>
                    <p class="text-slate-200 text-sm mb-6 leading-relaxed">
                        Publish your listing to thousands of active, pre-screened buyers looking in your locality with verified budgets.
                    </p>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm shadow-md transition">
                        <span>List Your Property Free</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <i class="fa-solid fa-building-wheat absolute right-4 bottom-4 text-9xl text-white/5 pointer-events-none"></i>
            </div>

        </div>
    </div>
</section>
@endsection

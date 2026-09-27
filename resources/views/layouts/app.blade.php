<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $routeName = Route::currentRouteName();
        $seoMeta = \App\Models\SeoMeta::getForPage($routeName ?? 'home');
        $resolvedTitle = $seoMeta?->meta_title ?: (View::hasSection('title') ? View::getSection('title') : 'EstateHub — Modern Real Estate Marketplace');
        $resolvedDescription = $seoMeta?->meta_description ?? 'Discover verified luxury homes, apartments, commercial offices and prime investment plots with EstateHub.';
        $resolvedKeywords = $seoMeta?->meta_keywords ?? 'real estate, buy property, luxury apartments, villas, commercial plots, verified sellers';
        $resolvedCanonical = $seoMeta?->canonical_url ?: url()->current();
        $robotsDirective = $seoMeta ? $seoMeta->robots_directive : 'index, follow';
        $ogTitle = $seoMeta?->og_title ?: $resolvedTitle;
        $ogDesc = $seoMeta?->og_description ?: $resolvedDescription;
        $ogImage = $seoMeta?->og_image ?: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80';
        $twitterCard = $seoMeta?->twitter_card ?: 'summary_large_image';
    @endphp

    <title>{{ $resolvedTitle }}</title>

    @if($routeName !== 'properties.show')
        <meta name="description" content="{{ $resolvedDescription }}">
        @if($resolvedKeywords)
        <meta name="keywords" content="{{ $resolvedKeywords }}">
        @endif
        <meta name="robots" content="{{ $robotsDirective }}">
        <link rel="canonical" href="{{ $resolvedCanonical }}">

        <!-- OpenGraph Metadata -->
        <meta property="og:site_name" content="EstateHub">
        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $ogDesc }}">
        <meta property="og:url" content="{{ $resolvedCanonical }}">
        <meta property="og:type" content="website">
        @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        @endif

        <!-- Twitter Card -->
        <meta name="twitter:card" content="{{ $twitterCard }}">
        <meta name="twitter:title" content="{{ $ogTitle }}">
        <meta name="twitter:description" content="{{ $ogDesc }}">
        @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
        @endif

        <!-- JSON-LD Structured Data Schema -->
        <script type="application/ld+json">
        {!! ($seoMeta && !empty($seoMeta->json_ld)) ? $seoMeta->json_ld : json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateAgent',
            'name' => 'EstateHub Marketplace',
            'url' => url('/'),
            'description' => 'Verified residential and commercial real estate platform connecting buyers, sellers, and certified agents.',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/properties') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string'
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @else
        <meta name="robots" content="index, follow">
    @endif

    @stack('meta')
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS (Vite + CDN for 100% reliable standalone & XAMPP Apache rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all duration-200" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-brand-700 via-brand-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/25 group-hover:scale-105 transition-transform duration-200">
                            <i class="fa-solid fa-city text-xl"></i>
                        </div>
                        <div>
                            <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-slate-800 to-brand-700 bg-clip-text text-transparent">EstateHub</span>
                            <span class="hidden sm:inline-block text-[10px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded bg-brand-50 text-brand-700 border border-brand-200 ml-1.5">Marketplace</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1 font-medium text-slate-600">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg hover:text-brand-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('home') ? 'text-brand-600 font-semibold bg-brand-50/60' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('properties.index') }}" class="px-4 py-2 rounded-lg hover:text-brand-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('properties.*') ? 'text-brand-600 font-semibold bg-brand-50/60' : '' }}">
                        Explore Properties
                    </a>
                    <a href="{{ route('about') }}" class="px-4 py-2 rounded-lg hover:text-brand-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('about') ? 'text-brand-600 font-semibold bg-brand-50/60' : '' }}">
                        About
                    </a>
                    <a href="{{ route('contact') }}" class="px-4 py-2 rounded-lg hover:text-brand-600 hover:bg-slate-50 transition-colors {{ request()->routeIs('contact') ? 'text-brand-600 font-semibold bg-brand-50/60' : '' }}">
                        Contact
                    </a>
                </nav>

                <!-- Auth Action Buttons -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <!-- Role Badge & Dashboard -->
                        <div class="flex items-center gap-3" x-data="{ userMenu: false }">
                            <div class="relative" @click.outside="userMenu = false">
                                <button @click="userMenu = !userMenu" type="button" class="flex items-center gap-2.5 px-3 py-1.5 rounded-full border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition">
                                    <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div class="text-left hidden lg:block">
                                        <div class="text-xs font-bold text-slate-900 leading-tight">{{ Auth::user()->name }}</div>
                                        <div class="text-[10px] uppercase font-semibold text-brand-600">{{ Auth::user()->role }}</div>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="userMenu" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                                    <div class="px-4 py-2 border-b border-slate-100">
                                        <p class="text-xs text-slate-500">Signed in as</p>
                                        <p class="text-sm font-bold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                                        <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded bg-brand-100 text-brand-700 uppercase">
                                            {{ Auth::user()->role }}
                                        </span>
                                    </div>
                                    
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                        <i class="fa-solid fa-gauge-high text-brand-600 w-4"></i> Dashboard
                                    </a>

                                    @if(Auth::user()->isBuyer())
                                        <a href="{{ route('requirements.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                            <i class="fa-solid fa-clipboard-list text-brand-600 w-4"></i> My Requirements
                                        </a>
                                    @endif

                                    @if(Auth::user()->isSeller())
                                        <a href="{{ route('listings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                            <i class="fa-solid fa-house-chimney text-brand-600 w-4"></i> My Listings
                                        </a>
                                        <a href="{{ route('matches.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                            <i class="fa-solid fa-users text-indigo-500 w-4"></i> Buyer Matches
                                        </a>
                                    @endif

                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                            <i class="fa-solid fa-shield-halved text-red-500 w-4"></i> Admin Panel
                                        </a>
                                    @endif

                                    @if(Auth::user()->isStaff())
                                        <a href="{{ route('staff.users') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                            <i class="fa-solid fa-user-plus text-emerald-600 w-4"></i> Staff Desk
                                        </a>
                                    @endif

                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                        <i class="fa-solid fa-user-gear text-slate-400 w-4"></i> Profile Settings
                                    </a>

                                    <div class="border-t border-slate-100 my-1"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 font-medium text-left">
                                            <i class="fa-solid fa-arrow-right-from-bracket w-4"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-600 text-white font-semibold text-sm shadow-md shadow-brand-500/20 hover:bg-brand-700 transition">
                                <i class="fa-solid fa-table-columns"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600 px-3 py-2 transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold text-sm shadow-sm hover:bg-brand-600 transition duration-200">
                            <span>Register Free</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileOpen = !mobileOpen" type="button" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100">
                        <i :class="mobileOpen ? 'fa-solid fa-xmark text-xl' : 'fa-solid fa-bars text-xl'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div x-show="mobileOpen" x-cloak class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Home</a>
            <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Explore Properties</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">About</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Contact</a>
            
            <div class="border-t border-slate-100 pt-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-2.5 rounded-xl bg-brand-600 text-white font-semibold mb-2">
                        Go to Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-center px-4 py-2 text-sm font-semibold text-red-600">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-700 mb-2">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2.5 rounded-xl bg-brand-600 text-white font-semibold">
                        Register Free
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Alert Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-brand-50 border border-brand-200 text-brand-800 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-info text-brand-600 text-lg"></i>
                <div class="text-sm font-medium">{{ session('info') }}</div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Trust Highlights Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pb-12 mb-12 border-b border-slate-800/80">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <div class="text-white font-bold text-sm">100% Verified Listings</div>
                        <div class="text-xs text-slate-400">Direct owner & RERA verified</div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-brand-500/10 border border-brand-500/20 text-brand-400 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <div class="text-white font-bold text-sm">AI Match Engine</div>
                        <div class="text-xs text-slate-400">100-Point precise compatibility</div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <div class="text-white font-bold text-sm">Instant Connections</div>
                        <div class="text-xs text-slate-400">Direct call & WhatsApp tours</div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <div class="text-white font-bold text-sm">Zero Spam Policy</div>
                        <div class="text-xs text-slate-400">Masked phone privacy shield</div>
                    </div>
                </div>
            </div>

            <!-- Main Footer Links Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-10 pb-14 border-b border-slate-800/80">
                
                <!-- Brand Col (2 cols wide) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white shadow-lg">
                            <i class="fa-solid fa-city text-lg"></i>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-white">EstateHub</span>
                    </div>

                    <p class="text-slate-400 text-sm leading-relaxed pr-4">
                        India's premier algorithmic real estate marketplace connecting qualified property buyers with genuine sellers, builders, and verified owners through intelligent multi-parametric matching.
                    </p>

                    <div class="pt-2 space-y-2 text-xs text-slate-400">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-location-dot text-brand-400 w-4 text-center"></i>
                            <span>Central Tech Hub, MG Road, Bangalore &bull; Guwahati Desk</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-phone text-brand-400 w-4 text-center"></i>
                            <span>National Hotline: +91 (080) 4567-8900</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-envelope text-brand-400 w-4 text-center"></i>
                            <span>Support: help@estatehub.in</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-3">
                        <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:border-brand-600 hover:text-white transition">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                        <a href="#" aria-label="Twitter X" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:border-brand-600 hover:text-white transition">
                            <i class="fa-brands fa-x-twitter text-xs"></i>
                        </a>
                        <a href="#" aria-label="LinkedIn" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:border-brand-600 hover:text-white transition">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                        <a href="#" aria-label="Instagram" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:border-brand-600 hover:text-white transition">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="#" aria-label="YouTube" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-red-600 hover:border-red-600 hover:text-white transition">
                            <i class="fa-brands fa-youtube text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Popular Metros & Cities -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 pb-1 border-b border-slate-800 inline-block">
                        Top Cities
                    </h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        <li><a href="{{ route('properties.index', ['city' => 'Guwahati']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Guwahati Properties</a></li>
                        <li><a href="{{ route('properties.index', ['city' => 'Guwahati', 'locality' => 'Sixmile']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Sixmile, Guwahati</a></li>
                        <li><a href="{{ route('properties.index', ['city' => 'Guwahati', 'locality' => 'Beltola']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Beltola, Guwahati</a></li>
                        <li><a href="{{ route('properties.index', ['city' => 'Mumbai']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Mumbai Luxury Flats</a></li>
                        <li><a href="{{ route('properties.index', ['city' => 'Bangalore']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Bangalore Tech Hub</a></li>
                        <li><a href="{{ route('properties.index', ['city' => 'Delhi NCR']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Delhi NCR Residences</a></li>
                        <li><a href="{{ route('properties.index', ['city' => 'Pune']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Pune Modern Homes</a></li>
                    </ul>
                </div>

                <!-- Property Types & Categories -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 pb-1 border-b border-slate-800 inline-block">
                        Categories
                    </h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        <li><a href="{{ route('properties.index', ['property_type' => 'Apartment']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Apartments & Flats</a></li>
                        <li><a href="{{ route('properties.index', ['property_type' => 'Villa']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Gated Luxury Villas</a></li>
                        <li><a href="{{ route('properties.index', ['property_type' => 'Plot']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Freehold Residential Plots</a></li>
                        <li><a href="{{ route('properties.index', ['property_type' => 'Commercial Space']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Commercial Spaces</a></li>
                        <li><a href="{{ route('properties.index', ['property_type' => 'Building']) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Independent Buildings</a></li>
                        <li><a href="{{ route('properties.index', ['bhk' => 2]) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">2 BHK Starter Homes</a></li>
                        <li><a href="{{ route('properties.index', ['bhk' => 3]) }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">3 BHK Premium Suites</a></li>
                    </ul>
                </div>

                <!-- Solutions & Tools -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 pb-1 border-b border-slate-800 inline-block">
                        Solutions
                    </h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        <li><a href="{{ route('about') }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">100-Point Match Engine</a></li>
                        <li><a href="{{ route('requirements.create') }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Post Buyer Requirement</a></li>
                        <li><a href="{{ route('listings.create') }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">List Property for Sale</a></li>
                        <li><a href="{{ route('properties.index') }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Browse All Properties</a></li>
                        <li><a href="{{ route('matches.index') }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Matchmaking Portal</a></li>
                        <li><a href="{{ route('about') }}#faq" class="hover:text-white hover:translate-x-0.5 inline-block transition">Frequently Asked Questions</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">Contact Real Estate Desk</a></li>
                    </ul>
                </div>

                <!-- Trust, Legal & RERA -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 pb-1 border-b border-slate-800 inline-block">
                        Trust & Legal
                    </h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        <li><a href="{{ route('about') }}" class="hover:text-white hover:translate-x-0.5 inline-block transition">About EstateHub</a></li>
                        <li><a href="{{ route('about') }}#rera" class="hover:text-white hover:translate-x-0.5 inline-block transition">RERA Compliance Guide</a></li>
                        <li><a href="{{ route('about') }}#verification" class="hover:text-white hover:translate-x-0.5 inline-block transition">6-Step Seller Verification</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-0.5 inline-block transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-0.5 inline-block transition">Terms & Conditions</a></li>
                        <li><a href="{{ url('/sitemap.xml') }}" target="_blank" class="hover:text-white hover:translate-x-0.5 inline-block transition flex items-center gap-1.5"><i class="fa-solid fa-sitemap text-[10px] text-brand-400"></i><span>XML Sitemap</span></a></li>
                        <li><a href="{{ url('/llms.txt') }}" target="_blank" class="hover:text-white hover:translate-x-0.5 inline-block transition flex items-center gap-1.5"><i class="fa-solid fa-robot text-[10px] text-purple-400"></i><span>AI & LLM Directory</span></a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-0.5 inline-block transition">Anti-Fraud Disclaimer</a></li>
                        <li><span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-semibold mt-1"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Network Active</span></li>
                    </ul>
                </div>

            </div>

            <!-- RERA & Regulatory Advisory Notice -->
            <div class="py-6 border-b border-slate-800/80 text-[11px] text-slate-400 leading-relaxed space-y-2">
                <p>
                    <strong class="text-slate-300">RERA & Statutory Disclaimer:</strong> EstateHub is an online technology matching platform connecting prospective purchasers and tenants with verified real estate sellers and promoters. We do not act as an unregistered real estate broker. All project details, floor plans, configurations, and pricing displayed are for informational matching purposes based on data supplied by sellers. Buyers are strongly encouraged to independently verify RERA registration numbers, land titles, and municipal sanctions directly with respective State Real Estate Regulatory Authorities (e.g. MahaRERA, RERA Assam, Karnataka RERA) prior to executing any financial agreement.
                </p>
            </div>

            <!-- Bottom Row: Badges, Copyright & Security -->
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div>
                    <p>&copy; {{ date('Y') }} EstateHub Technologies Pvt. Ltd. All rights reserved. • Developed By <a href="https://www.webotapp.com" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline font-semibold">Webotapp</a></p>
                </div>

                <!-- Trust Badges -->
                <div class="flex items-center flex-wrap gap-4 text-[11px] text-slate-400">
                    <span class="flex items-center gap-1 bg-slate-900 border border-slate-800 px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-lock text-emerald-400"></i>
                        <span>256-Bit SSL Secured</span>
                    </span>
                    <span class="flex items-center gap-1 bg-slate-900 border border-slate-800 px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-shield-check text-brand-400"></i>
                        <span>RERA Compliant Network</span>
                    </span>
                    <span class="flex items-center gap-1 bg-slate-900 border border-slate-800 px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-award text-amber-400"></i>
                        <span>Verified Marketplace</span>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

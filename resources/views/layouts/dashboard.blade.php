<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — EstateHub</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                            950: '#0b1936',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col flex-shrink-0 shadow-2xl lg:shadow-none">
            
            <!-- Sidebar Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/80">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white shadow-lg">
                        <i class="fa-solid fa-city text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">EstateHub</span>
                        <div class="text-[10px] uppercase font-bold tracking-wider text-brand-400">
                            {{ Auth::user()->role }} PORTAL
                        </div>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Current User Card -->
            <div class="p-4 mx-4 my-4 rounded-xl bg-slate-800/60 border border-slate-700/60 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm uppercase shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</h4>
                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->phone }}</p>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dashboard') ? 'bg-brand-600 text-white font-semibold shadow-md shadow-brand-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                    <span>Overview</span>
                </a>

                <!-- BUYER LINKS -->
                @if(Auth::user()->isBuyer())
                    <div class="pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500 px-3">Buyer Actions</div>
                    
                    <a href="{{ route('requirements.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('requirements.*') && !request()->routeIs('requirements.create') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                        <span>My Requirements</span>
                    </a>

                    <a href="{{ route('requirements.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('requirements.create') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-plus-circle w-5 text-center"></i>
                        <span>Post Requirement</span>
                    </a>

                    <a href="{{ route('properties.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition">
                        <i class="fa-solid fa-magnifying-glass w-5 text-center"></i>
                        <span>Search Properties</span>
                    </a>
                @endif

                <!-- SELLER LINKS -->
                @if(Auth::user()->isSeller())
                    <div class="pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500 px-3">Seller Actions</div>
                    
                    <a href="{{ route('listings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('listings.index') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-house-chimney w-5 text-center"></i>
                        <span>My Listings</span>
                    </a>

                    <a href="{{ route('listings.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('listings.create') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-cloud-arrow-up w-5 text-center"></i>
                        <span>Add New Listing</span>
                    </a>

                    <a href="{{ route('matches.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('matches.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-user-tag text-indigo-400 w-5 text-center"></i>
                        <span>Matched Buyers</span>
                    </a>
                @endif

                <!-- STAFF LINKS -->
                @if(Auth::user()->isStaff())
                    <div class="pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500 px-3">Staff Operations</div>
                    
                    <a href="{{ route('staff.users') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('staff.users*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        <span>Client Accounts</span>
                    </a>

                    <a href="{{ route('staff.users.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('staff.users.create') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-user-plus w-5 text-center"></i>
                        <span>Create Client</span>
                    </a>

                    <a href="{{ route('listings.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('listings.create') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-house-medical w-5 text-center"></i>
                        <span>Post Property</span>
                    </a>

                    <a href="{{ route('requirements.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('requirements.create') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-file-circle-plus w-5 text-center"></i>
                        <span>Post Requirement</span>
                    </a>

                    <a href="{{ route('staff.matches') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('staff.matches') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-arrows-split-up-and-left text-amber-400 w-5 text-center"></i>
                        <span>Match Center</span>
                    </a>
                @endif

                <!-- ADMIN LINKS -->
                @if(Auth::user()->isAdmin())
                    <div class="pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500 px-3">Administration</div>
                    
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.users.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-users-gear w-5 text-center"></i>
                        <span>User Management</span>
                    </a>

                    <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.staff.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-id-badge w-5 text-center"></i>
                        <span>Staff Directory</span>
                    </a>

                    <a href="{{ route('admin.properties.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.properties.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-building-circle-check w-5 text-center"></i>
                        <span>Property Moderation</span>
                    </a>

                    <a href="{{ route('admin.requirements.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.requirements.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-list-check w-5 text-center"></i>
                        <span>Requirements</span>
                    </a>

                    <a href="{{ route('admin.cities.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.cities.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-city w-5 text-center text-amber-400"></i>
                        <span>Popular Cities</span>
                    </a>

                    <a href="{{ route('admin.localities.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.localities.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-map-location-dot w-5 text-center"></i>
                        <span>Locality Manager</span>
                    </a>

                    <a href="{{ route('admin.packages.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.packages.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-boxes-stacked w-5 text-center"></i>
                        <span>Packages & Plans</span>
                    </a>

                    <a href="{{ route('admin.matches.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.matches.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-handshake w-5 text-center"></i>
                        <span>Platform Matches</span>
                    </a>

                    <a href="{{ route('admin.seo.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.seo.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-magnifying-glass-chart text-emerald-400 w-5 text-center"></i>
                        <span>SEO & Metadata</span>
                    </a>

                    <a href="{{ route('admin.about.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.about.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-circle-info text-cyan-400 w-5 text-center"></i>
                        <span>About Page Editor</span>
                    </a>

                    <a href="{{ route('admin.updates.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.updates.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-arrows-rotate text-emerald-400 w-5 text-center"></i>
                        <span>System Updates</span>
                    </a>

                    <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.logs.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-shield-halved w-5 text-center"></i>
                        <span>Audit Logs</span>
                    </a>
                @endif

                <div class="pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500 px-3">Account</div>
                
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('profile.*') ? 'bg-brand-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-user-gear w-5 text-center"></i>
                    <span>Profile Settings</span>
                </a>

                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i>
                    <span>Public Website</span>
                </a>
            </nav>

            <!-- Logout Bottom -->
            <div class="p-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm text-rose-400 hover:bg-rose-500/10 transition">
                        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Dashboard View Container -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            
            <!-- Top Dashboard Bar -->
            <header class="h-20 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                        <p class="text-xs text-slate-500 hidden sm:block">@yield('page-subtitle', 'Welcome to your EstateHub control panel')</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        <span>Live Site</span>
                    </a>
                    
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>{{ Auth::user()->role }}</span>
                    </span>
                </div>
            </header>

            <!-- Flash alerts -->
            <div class="px-6 pt-6">
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
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                @yield('content')
            </main>
            <footer class="py-4 px-6 border-t border-slate-200 text-center text-xs text-slate-500 bg-white">
                &copy; {{ date('Y') }} EstateHub. Developed By <a href="https://www.webotapp.com" target="_blank" rel="noopener noreferrer" class="font-bold text-slate-800 hover:text-emerald-600 underline">Webotapp</a>
            </footer>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

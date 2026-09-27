@extends('layouts.app')

@section('title', 'About EstateHub — Precision Real Estate Matchmaking & Verified Marketplace')

@section('content')
<!-- Hero Section -->
<div class="relative bg-slate-950 text-white py-20 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-brand-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-indigo-600 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl">
        @if(filled($about->hero_badge ?? null))
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-brand-500/10 text-brand-400 border border-brand-500/20 mb-4">
                <i class="fa-solid fa-sparkles"></i>
                {{ $about->hero_badge }}
            </span>
        @endif

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight mt-2 leading-tight">
            {!! $about->hero_title ?? 'Precision Property Discovery with <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-indigo-400">Zero Spam</span>' !!}
        </h1>

        <p class="text-slate-300 text-base sm:text-lg mt-6 leading-relaxed">
            {{ $about->hero_subtitle ?? 'EstateHub eliminates fake listings, duplicate broker spam, and endless cold calls through an intelligent 100-point algorithmic engine connecting qualified buyers directly with verified sellers.' }}
        </p>

        <!-- Stats Bar -->
        @php
            $stats = $about->hero_stats ?? [
                ['value' => '10,000+', 'label' => 'Verified Properties', 'color' => 'text-brand-400'],
                ['value' => '98.4%', 'label' => 'Match Precision', 'color' => 'text-emerald-400'],
                ['value' => '45,000+', 'label' => 'Direct Buyer Calls', 'color' => 'text-indigo-400'],
                ['value' => '₹0', 'label' => 'Hidden Broker Fees', 'color' => 'text-amber-400'],
            ];
        @endphp
        @if(!empty($stats))
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mt-12 pt-10 border-t border-slate-800">
                @foreach($stats as $st)
                    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div class="text-2xl sm:text-3xl font-black {{ $st['color'] ?? 'text-brand-400' }}">{{ $st['value'] ?? '' }}</div>
                        <div class="text-xs text-slate-400 font-medium mt-1">{{ $st['label'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-20">
    
    <!-- Section 1: The Problem vs The EstateHub Solution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div>
            <span class="text-xs font-extrabold uppercase tracking-wider text-brand-600">
                {{ $about->challenge_tagline ?? 'The Modern Realty Challenge' }}
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1 mb-6">
                {{ $about->challenge_title ?? 'Why Traditional Property Portals Fail Indian Buyers' }}
            </h2>
            <div class="space-y-4 text-slate-600 text-sm sm:text-base leading-relaxed">
                @if(filled($about->challenge_content_1 ?? null))
                    <p>{{ $about->challenge_content_1 }}</p>
                @endif
                @if(filled($about->challenge_content_2 ?? null))
                    <p>{{ $about->challenge_content_2 }}</p>
                @endif
                @if(filled($about->challenge_content_3 ?? null))
                    <p>{{ $about->challenge_content_3 }}</p>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <!-- Traditional Classifieds Model -->
            <div class="p-6 rounded-3xl bg-rose-50/70 border border-rose-100">
                <div class="flex items-center gap-3 text-rose-800 font-bold mb-2">
                    <i class="fa-solid fa-circle-xmark text-rose-500 text-lg"></i>
                    <span>{{ $about->traditional_title ?? 'Traditional Classifieds Model' }}</span>
                </div>
                <ul class="text-xs sm:text-sm text-rose-900/80 space-y-2 pl-7 list-disc">
                    @foreach($about->traditional_points ?? [] as $pt)
                        <li>{{ $pt }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- The EstateHub Precision Engine -->
            <div class="p-6 rounded-3xl bg-emerald-50/70 border border-emerald-100">
                <div class="flex items-center gap-3 text-emerald-800 font-bold mb-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span>{{ $about->solution_title ?? 'The EstateHub Precision Engine' }}</span>
                </div>
                <ul class="text-xs sm:text-sm text-emerald-900/80 space-y-2 pl-7 list-disc">
                    @foreach($about->solution_points ?? [] as $pt)
                        <li>{{ $pt }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Section 2: How Our 100-Point Match Algorithm Works -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 shadow-sm">
        <div class="max-w-3xl mb-10">
            <span class="text-xs font-extrabold uppercase tracking-wider text-brand-600">
                {{ $about->algorithm_tagline ?? 'The Technology Architecture' }}
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1 mb-4">
                {{ $about->algorithm_title ?? 'How Our 100-Point Match Engine Works' }}
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                {{ $about->algorithm_subtitle ?? 'When a buyer posts their requirements or a seller lists a property, our background engine computes pairwise compatibility across 5 weighted vectors, producing a match score between 0 and 100:' }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            @php
                $colorStyles = [
                    'brand' => ['bg' => 'bg-brand-100', 'text' => 'text-brand-600', 'badge' => 'text-brand-700'],
                    'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'badge' => 'text-emerald-700'],
                    'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'badge' => 'text-indigo-700'],
                    'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'badge' => 'text-purple-700'],
                    'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'badge' => 'text-amber-700'],
                ];
            @endphp
            @foreach($about->algorithm_vectors ?? [] as $vec)
                @php
                    $cKey = $vec['color'] ?? 'brand';
                    $style = $colorStyles[$cKey] ?? $colorStyles['brand'];
                @endphp
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/70 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl {{ $style['bg'] }} {{ $style['text'] }} flex items-center justify-center font-bold text-base mb-4">
                            <i class="{{ $vec['icon'] ?? 'fa-solid fa-check' }}"></i>
                        </div>
                        <div class="text-2xl font-black {{ $style['text'] }}">{{ $vec['points'] ?? '' }}</div>
                        <div class="font-bold text-slate-900 text-base mt-1">{{ $vec['title'] ?? '' }}</div>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            {{ $vec['desc'] ?? '' }}
                        </p>
                    </div>
                    @if(filled($vec['badge'] ?? null))
                        <div class="mt-4 pt-3 border-t border-slate-200 text-[11px] font-semibold {{ $style['badge'] }}">
                            {{ $vec['badge'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section 3: Verified Marketplace & RERA Compliance Standards -->
    @if(!empty($about->standards_cards))
        <div id="rera" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @php
                $stdStyles = [
                    'brand' => ['bg' => 'bg-brand-50', 'text' => 'text-brand-600'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                    'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                ];
            @endphp
            @foreach($about->standards_cards as $sc)
                @php
                    $sStyle = $stdStyles[$sc['color'] ?? 'brand'] ?? $stdStyles['brand'];
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-sm">
                    <div class="w-12 h-12 rounded-2xl {{ $sStyle['bg'] }} {{ $sStyle['text'] }} flex items-center justify-center text-xl mb-4">
                        <i class="{{ $sc['icon'] ?? 'fa-solid fa-shield' }}"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-xl mb-2">{{ $sc['title'] ?? '' }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        {{ $sc['desc'] ?? '' }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Section 4: Dual-Perspective Advantages (Buyers & Sellers) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- For Serious Buyers -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-bold text-xl">
                    <i class="fa-solid fa-house-chimney-user"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-600">Tailored For Purchasers</span>
                    <h3 class="text-2xl font-black text-slate-900">{{ $about->buyer_title ?? 'Buyer Advantages' }}</h3>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($about->buyer_points ?? [] as $bp)
                    <div class="flex items-start gap-3.5">
                        <i class="fa-solid fa-check text-brand-600 text-base mt-1"></i>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $bp['title'] ?? '' }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $bp['desc'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('requirements.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-md">
                    <span>Post Free Buyer Requirement</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- For Verified Sellers -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl">
                    <i class="fa-solid fa-building-circle-check"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Tailored For Property Owners</span>
                    <h3 class="text-2xl font-black text-slate-900">{{ $about->seller_title ?? 'Seller & Builder Advantages' }}</h3>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($about->seller_points ?? [] as $sp)
                    <div class="flex items-start gap-3.5">
                        <i class="fa-solid fa-check text-emerald-600 text-base mt-1"></i>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $sp['title'] ?? '' }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $sp['desc'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-md">
                    <span>List Your Property Free</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Section 5: 6-Step Verification Process (#verification) -->
    <div id="verification" class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 shadow-sm">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-extrabold uppercase tracking-wider text-brand-600">
                {{ $about->verification_tagline ?? 'Integrity Framework' }}
            </span>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-1">
                {{ $about->verification_title ?? 'Our 6-Step Verification Protocol' }}
            </h2>
            <p class="text-slate-500 text-sm mt-2">
                {{ $about->verification_subtitle ?? 'Every listing on EstateHub must pass our stringent editorial and administrative checklist before publishing.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($about->verification_steps ?? [] as $vStep)
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 flex gap-4">
                    <div class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ $vStep['step'] ?? $loop->iteration }}
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $vStep['title'] ?? '' }}</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $vStep['desc'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section 6: Interactive FAQ Accordion (#faq) -->
    @if(!empty($about->faqs))
        <div id="faq" class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 shadow-sm" x-data="{ openFaq: 0 }">
            <div class="max-w-3xl mx-auto text-center mb-10">
                <span class="text-xs font-extrabold uppercase tracking-wider text-brand-600">
                    {{ $about->faq_tagline ?? 'Got Questions?' }}
                </span>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-1">
                    {{ $about->faq_title ?? 'Frequently Asked Questions' }}
                </h2>
                <p class="text-slate-500 text-sm mt-2">
                    {{ $about->faq_subtitle ?? 'Everything you need to know about the EstateHub marketplace and matching engine.' }}
                </p>
            </div>

            <div class="max-w-3xl mx-auto space-y-4">
                @foreach($about->faqs as $fIdx => $faq)
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button type="button" 
                                @click="openFaq = (openFaq === {{ $fIdx }} ? null : {{ $fIdx }})" 
                                class="w-full p-5 text-left font-bold text-slate-900 hover:text-brand-600 flex items-center justify-between transition focus:outline-none">
                            <span class="text-base sm:text-lg">{{ $faq['question'] ?? '' }}</span>
                            <i class="fa-solid text-sm text-slate-400 transition transform" :class="openFaq === {{ $fIdx }} ? 'fa-chevron-up text-brand-600' : 'fa-chevron-down'"></i>
                        </button>
                        <div x-show="openFaq === {{ $fIdx }}" x-cloak class="px-5 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                            {{ $faq['answer'] ?? '' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Final Call to Action -->
    <div class="bg-gradient-to-r from-brand-600 to-indigo-600 rounded-3xl p-10 sm:p-14 text-white text-center shadow-xl">
        <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-4">
            {{ $about->cta_title ?? 'Ready to Experience Transparent Real Estate?' }}
        </h2>
        <p class="text-white/90 text-sm sm:text-base max-w-2xl mx-auto mb-8 leading-relaxed">
            {{ $about->cta_subtitle ?? 'Join thousands of smart property buyers and verified owners across India who save weeks of wasted time using our precision matching engine.' }}
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            @if(filled($about->cta_btn1_text ?? null))
                <a href="{{ $about->cta_btn1_url ?? route('properties.index') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white text-slate-900 font-extrabold text-sm hover:bg-slate-100 transition shadow-lg">
                    {{ $about->cta_btn1_text }}
                </a>
            @endif
            @if(filled($about->cta_btn2_text ?? null))
                <a href="{{ $about->cta_btn2_url ?? route('requirements.create') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-slate-950/40 hover:bg-slate-950/60 border border-white/20 text-white font-extrabold text-sm transition">
                    {{ $about->cta_btn2_text }}
                </a>
            @endif
        </div>
    </div>

</div>
@endsection

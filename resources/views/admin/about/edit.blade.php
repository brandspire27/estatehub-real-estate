@extends('layouts.dashboard')

@section('title', 'About Page Content Editor — EstateHub Admin')
@section('page-title', 'About Page Management')
@section('page-subtitle', 'Customize all public copy, stat counters, matching engine parameters, FAQs, and verification standards')

@section('content')
<div class="max-w-6xl mx-auto pb-16" x-data="{
    activeTab: 'hero',
    stats: {{ json_encode($setting->hero_stats ?? []) }},
    traditionalPoints: {{ json_encode($setting->traditional_points ?? []) }},
    solutionPoints: {{ json_encode($setting->solution_points ?? []) }},
    algorithmVectors: {{ json_encode($setting->algorithm_vectors ?? []) }},
    standardsCards: {{ json_encode($setting->standards_cards ?? []) }},
    buyerPoints: {{ json_encode($setting->buyer_points ?? []) }},
    sellerPoints: {{ json_encode($setting->seller_points ?? []) }},
    verificationSteps: {{ json_encode($setting->verification_steps ?? []) }},
    faqs: {{ json_encode($setting->faqs ?? []) }},

    addTraditionalPoint() {
        this.traditionalPoints.push('');
    },
    removeTraditionalPoint(idx) {
        this.traditionalPoints.splice(idx, 1);
    },

    addSolutionPoint() {
        this.solutionPoints.push('');
    },
    removeSolutionPoint(idx) {
        this.solutionPoints.splice(idx, 1);
    },

    addFaq() {
        this.faqs.push({ question: '', answer: '' });
    },
    removeFaq(idx) {
        this.faqs.splice(idx, 1);
    },

    addVerificationStep() {
        const nextStep = this.verificationSteps.length + 1;
        this.verificationSteps.push({ step: nextStep, title: '', desc: '' });
    },
    removeVerificationStep(idx) {
        this.verificationSteps.splice(idx, 1);
        this.verificationSteps.forEach((s, i) => s.step = i + 1);
    }
}">

    <!-- Top Header & Live Preview Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-600 text-white flex items-center justify-center text-lg shadow-md shadow-brand-500/20">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-brand-50 text-brand-700 border border-brand-200">
                        /about
                    </span>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="text-xs font-medium text-slate-500">Live Editorial System</span>
                </div>
                <h1 class="text-xl font-black text-slate-900 mt-0.5">Public About Page Editor</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('about') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-brand-600 hover:border-brand-300 transition shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square text-slate-400"></i>
                <span>Preview Live Page</span>
            </a>
            <button type="submit" form="aboutPageForm" class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-extrabold rounded-xl bg-brand-600 hover:bg-brand-700 text-white transition shadow-md shadow-brand-500/20">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Save All Changes</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
            <div>
                <div class="font-bold">Content Updated Successfully!</div>
                <div class="text-xs text-emerald-700 mt-0.5">{{ session('success') }} Check the live page to view your changes.</div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
            <div class="font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc pl-5 mt-2 text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1.5 mb-6 flex overflow-x-auto scrollbar-none gap-1 shadow-sm">
        <button type="button" @click="activeTab = 'hero'" :class="activeTab === 'hero' ? 'bg-brand-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 whitespace-nowrap transition">
            <i class="fa-solid fa-sparkles"></i>
            <span>1. Hero & Stats</span>
        </button>

        <button type="button" @click="activeTab = 'challenge'" :class="activeTab === 'challenge' ? 'bg-brand-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 whitespace-nowrap transition">
            <i class="fa-solid fa-scale-balanced"></i>
            <span>2. Problem vs Solution</span>
        </button>

        <button type="button" @click="activeTab = 'algorithm'" :class="activeTab === 'algorithm' ? 'bg-brand-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 whitespace-nowrap transition">
            <i class="fa-solid fa-microchip"></i>
            <span>3. 100-Pt Algorithm</span>
        </button>

        <button type="button" @click="activeTab = 'standards'" :class="activeTab === 'standards' ? 'bg-brand-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 whitespace-nowrap transition">
            <i class="fa-solid fa-shield-halved"></i>
            <span>4. Standards & Verification</span>
        </button>

        <button type="button" @click="activeTab = 'advantages'" :class="activeTab === 'advantages' ? 'bg-brand-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 whitespace-nowrap transition">
            <i class="fa-solid fa-user-check"></i>
            <span>5. Buyers & Sellers</span>
        </button>

        <button type="button" @click="activeTab = 'faqs'" :class="activeTab === 'faqs' ? 'bg-brand-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 whitespace-nowrap transition">
            <i class="fa-solid fa-circle-question"></i>
            <span>6. FAQs</span>
        </button>

        <button type="button" @click="activeTab = 'cta'" :class="activeTab === 'cta' ? 'bg-brand-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 whitespace-nowrap transition">
            <i class="fa-solid fa-bullhorn"></i>
            <span>7. Call to Action</span>
        </button>
    </div>

    <!-- Main Edit Form -->
    <form id="aboutPageForm" action="{{ route('admin.about.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- TAB 1: HERO & STAT COUNTERS -->
        <div x-show="activeTab === 'hero'" class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-sparkles"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Hero Header & Narrative</h2>
                        <p class="text-xs text-slate-500">The introduction banner viewed above the fold on /about</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hero Pill Badge</label>
                        <input type="text" name="hero_badge" value="{{ old('hero_badge', $setting->hero_badge) }}" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition" placeholder="e.g. Redefining Indian Real Estate">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hero Main Title (HTML supported for gradient styling)</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}" required class="w-full px-4 py-2.5 text-sm font-semibold rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition" placeholder="e.g. Precision Property Discovery with Zero Spam">
                        <p class="text-[11px] text-slate-400 mt-1">Tip: Use <code>&lt;span class=&quot;text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-indigo-400&quot;&gt;Zero Spam&lt;/span&gt;</code> for gradient highlight.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hero Subtitle / Value Proposition</label>
                        <textarea name="hero_subtitle" rows="3" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition" placeholder="EstateHub eliminates fake listings...">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Hero 4 Stat Counters -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-chart-simple"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">4 Platform Key Metrics</h2>
                            <p class="text-xs text-slate-500">Displayed in the hero banner stats grid</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <template x-for="(stat, idx) in stats" :key="idx">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400" x-text="'Metric #' + (idx + 1)"></span>
                                <input type="hidden" :name="'hero_stats[' + idx + '][color]'" x-model="stat.color">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Value / Figure</label>
                                <input type="text" :name="'hero_stats[' + idx + '][value]'" x-model="stat.value" class="w-full px-3 py-1.5 text-sm font-black rounded-lg border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Metric Label</label>
                                <input type="text" :name="'hero_stats[' + idx + '][label]'" x-model="stat.label" class="w-full px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- TAB 2: PROBLEM VS SOLUTION -->
        <div x-show="activeTab === 'challenge'" class="space-y-6" x-cloak>
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">The Modern Realty Challenge Narrative</h2>
                        <p class="text-xs text-slate-500">The story and background behind founding EstateHub</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Section Tagline</label>
                        <input type="text" name="challenge_tagline" value="{{ old('challenge_tagline', $setting->challenge_tagline) }}" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Section Headline</label>
                        <input type="text" name="challenge_title" value="{{ old('challenge_title', $setting->challenge_title) }}" class="w-full px-4 py-2.5 text-sm font-semibold rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Story Paragraph 1 (Buyer Pain Point)</label>
                        <textarea name="challenge_content_1" rows="3" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">{{ old('challenge_content_1', $setting->challenge_content_1) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Story Paragraph 2 (Seller Frustration)</label>
                        <textarea name="challenge_content_2" rows="3" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">{{ old('challenge_content_2', $setting->challenge_content_2) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Story Paragraph 3 (EstateHub Mathematical Solution)</label>
                        <textarea name="challenge_content_3" rows="3" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">{{ old('challenge_content_3', $setting->challenge_content_3) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Comparison Cards: Traditional vs EstateHub -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Traditional Pitfalls -->
                <div class="bg-white rounded-3xl border border-rose-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-rose-100">
                        <div class="flex items-center gap-2 text-rose-800 font-bold text-base">
                            <i class="fa-solid fa-circle-xmark text-rose-500"></i>
                            <span>Traditional Model Pitfalls</span>
                        </div>
                        <button type="button" @click="addTraditionalPoint()" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 transition">
                            + Add Bullet
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Card Title</label>
                        <input type="text" name="traditional_title" value="{{ old('traditional_title', $setting->traditional_title) }}" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200 focus:border-rose-400">
                    </div>

                    <div class="space-y-2">
                        <template x-for="(point, idx) in traditionalPoints" :key="idx">
                            <div class="flex items-center gap-2">
                                <input type="text" :name="'traditional_points[' + idx + ']'" x-model="traditionalPoints[idx]" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-rose-400" placeholder="Point description...">
                                <button type="button" @click="removeTraditionalPoint(idx)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 flex items-center justify-center transition flex-shrink-0">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- EstateHub Solution -->
                <div class="bg-white rounded-3xl border border-emerald-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-emerald-100">
                        <div class="flex items-center gap-2 text-emerald-800 font-bold text-base">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>EstateHub Precision Advantages</span>
                        </div>
                        <button type="button" @click="addSolutionPoint()" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
                            + Add Bullet
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Card Title</label>
                        <input type="text" name="solution_title" value="{{ old('solution_title', $setting->solution_title) }}" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200 focus:border-emerald-400">
                    </div>

                    <div class="space-y-2">
                        <template x-for="(point, idx) in solutionPoints" :key="idx">
                            <div class="flex items-center gap-2">
                                <input type="text" :name="'solution_points[' + idx + ']'" x-model="solutionPoints[idx]" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-emerald-400" placeholder="Point description...">
                                <button type="button" @click="removeSolutionPoint(idx)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 flex items-center justify-center transition flex-shrink-0">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: 100-POINT ALGORITHM ENGINE -->
        <div x-show="activeTab === 'algorithm'" class="space-y-6" x-cloak>
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">100-Point Match Algorithm Presentation</h2>
                        <p class="text-xs text-slate-500">Define the 5 evaluation criteria that power the proprietary match score</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tagline</label>
                        <input type="text" name="algorithm_tagline" value="{{ old('algorithm_tagline', $setting->algorithm_tagline) }}" class="w-full px-4 py-2 text-sm rounded-xl border border-slate-200 focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Headline</label>
                        <input type="text" name="algorithm_title" value="{{ old('algorithm_title', $setting->algorithm_title) }}" class="w-full px-4 py-2 text-sm font-semibold rounded-xl border border-slate-200 focus:border-brand-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description Text</label>
                    <textarea name="algorithm_subtitle" rows="2" class="w-full px-4 py-2 text-sm rounded-xl border border-slate-200 focus:border-brand-500">{{ old('algorithm_subtitle', $setting->algorithm_subtitle) }}</textarea>
                </div>

                <!-- 5 Vector Cards -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">The 5 Compatibility Vectors</h3>
                    <template x-for="(vec, idx) in algorithmVectors" :key="idx">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Points Weight</label>
                                <input type="text" :name="'algorithm_vectors[' + idx + '][points]'" x-model="vec.points" class="w-full px-3 py-1.5 text-xs font-black rounded-lg border border-slate-200">
                                <input type="hidden" :name="'algorithm_vectors[' + idx + '][color]'" x-model="vec.color">
                                <input type="hidden" :name="'algorithm_vectors[' + idx + '][icon]'" x-model="vec.icon">
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Vector Name</label>
                                <input type="text" :name="'algorithm_vectors[' + idx + '][title]'" x-model="vec.title" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200">
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Evaluation Description</label>
                                <input type="text" :name="'algorithm_vectors[' + idx + '][desc]'" x-model="vec.desc" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200">
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Footer Badge</label>
                                <input type="text" :name="'algorithm_vectors[' + idx + '][badge]'" x-model="vec.badge" class="w-full px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- TAB 4: STANDARDS & VERIFICATION -->
        <div x-show="activeTab === 'standards'" class="space-y-6" x-cloak>
            <!-- 3 Standards Cards -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Marketplace Trust Pillars</h2>
                        <p class="text-xs text-slate-500">The 3 core assurance pillars (Verified Sellers, RERA Alignment, Phone Masking)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <template x-for="(card, idx) in standardsCards" :key="idx">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Pillar Title</label>
                                <input type="text" :name="'standards_cards[' + idx + '][title]'" x-model="card.title" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200">
                                <input type="hidden" :name="'standards_cards[' + idx + '][icon]'" x-model="card.icon">
                                <input type="hidden" :name="'standards_cards[' + idx + '][color]'" x-model="card.color">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Description</label>
                                <textarea :name="'standards_cards[' + idx + '][desc]'" x-model="card.desc" rows="3" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200"></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 6-Step Verification Protocol -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-list-ol"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Verification Protocol Steps</h2>
                            <p class="text-xs text-slate-500">The editorial & legal checkpoints required for each listing</p>
                        </div>
                    </div>
                    <button type="button" @click="addVerificationStep()" class="px-3 py-1.5 text-xs font-bold rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Step</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tagline</label>
                        <input type="text" name="verification_tagline" value="{{ old('verification_tagline', $setting->verification_tagline) }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Section Title</label>
                        <input type="text" name="verification_title" value="{{ old('verification_title', $setting->verification_title) }}" class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Subtitle</label>
                    <input type="text" name="verification_subtitle" value="{{ old('verification_subtitle', $setting->verification_subtitle) }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="(step, idx) in verificationSteps" :key="idx">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 relative">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-6 h-6 rounded-lg bg-brand-600 text-white flex items-center justify-center text-[10px] font-bold" x-text="step.step || (idx + 1)"></div>
                                <input type="hidden" :name="'verification_steps[' + idx + '][step]'" :value="idx + 1">
                                <button type="button" @click="removeVerificationStep(idx)" class="text-slate-400 hover:text-rose-600 text-xs">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>

                            <div class="space-y-2">
                                <input type="text" :name="'verification_steps[' + idx + '][title]'" x-model="step.title" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200" placeholder="Step title...">
                                <textarea :name="'verification_steps[' + idx + '][desc]'" x-model="step.desc" rows="2" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200" placeholder="Step explanation..."></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- TAB 5: BUYER & SELLER PERKS -->
        <div x-show="activeTab === 'advantages'" class="space-y-6" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Buyer Perks -->
                <div class="bg-white rounded-3xl border border-brand-100 p-6 shadow-sm">
                    <div class="flex items-center gap-2 pb-3 mb-4 border-b border-brand-100 text-brand-800 font-bold text-base">
                        <i class="fa-solid fa-house-chimney-user text-brand-600"></i>
                        <span>Buyer Advantages</span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Column Title</label>
                        <input type="text" name="buyer_title" value="{{ old('buyer_title', $setting->buyer_title) }}" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200">
                    </div>

                    <div class="space-y-3">
                        <template x-for="(pt, idx) in buyerPoints" :key="idx">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                                <input type="text" :name="'buyer_points[' + idx + '][title]'" x-model="pt.title" class="w-full px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-200" placeholder="Feature title...">
                                <textarea :name="'buyer_points[' + idx + '][desc]'" x-model="pt.desc" rows="2" class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-200" placeholder="Feature detail..."></textarea>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Seller Perks -->
                <div class="bg-white rounded-3xl border border-emerald-100 p-6 shadow-sm">
                    <div class="flex items-center gap-2 pb-3 mb-4 border-b border-emerald-100 text-emerald-800 font-bold text-base">
                        <i class="fa-solid fa-building-circle-check text-emerald-600"></i>
                        <span>Seller & Builder Advantages</span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Column Title</label>
                        <input type="text" name="seller_title" value="{{ old('seller_title', $setting->seller_title) }}" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200">
                    </div>

                    <div class="space-y-3">
                        <template x-for="(pt, idx) in sellerPoints" :key="idx">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                                <input type="text" :name="'seller_points[' + idx + '][title]'" x-model="pt.title" class="w-full px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-200" placeholder="Feature title...">
                                <textarea :name="'seller_points[' + idx + '][desc]'" x-model="pt.desc" rows="2" class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-200" placeholder="Feature detail..."></textarea>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 6: FAQ MANAGER -->
        <div x-show="activeTab === 'faqs'" class="space-y-6" x-cloak>
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-circle-question"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Frequently Asked Questions</h2>
                            <p class="text-xs text-slate-500">Manage accordion questions, answers, and clarity points</p>
                        </div>
                    </div>
                    <button type="button" @click="addFaq()" class="px-4 py-2 text-xs font-bold rounded-xl bg-brand-600 hover:bg-brand-700 text-white transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add New FAQ</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tagline</label>
                        <input type="text" name="faq_tagline" value="{{ old('faq_tagline', $setting->faq_tagline) }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Section Title</label>
                        <input type="text" name="faq_title" value="{{ old('faq_title', $setting->faq_title) }}" class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200">
                    </div>
                </div>

                <div class="space-y-4">
                    <template x-for="(faq, idx) in faqs" :key="idx">
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 transition">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px]" x-text="idx + 1"></span>
                                    <span>FAQ Item</span>
                                </span>
                                <button type="button" @click="removeFaq(idx)" class="px-2.5 py-1 text-xs text-rose-600 hover:bg-rose-50 rounded-lg transition font-semibold">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Remove
                                </button>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Question</label>
                                <input type="text" :name="'faqs[' + idx + '][question]'" x-model="faq.question" class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 focus:border-brand-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Answer</label>
                                <textarea :name="'faqs[' + idx + '][answer]'" x-model="faq.answer" rows="3" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-brand-500"></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- TAB 7: CALL TO ACTION BANNER -->
        <div x-show="activeTab === 'cta'" class="space-y-6" x-cloak>
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Bottom Call to Action Banner</h2>
                        <p class="text-xs text-slate-500">Conversion section displayed at the bottom of the page</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Headline</label>
                        <input type="text" name="cta_title" value="{{ old('cta_title', $setting->cta_title) }}" class="w-full px-4 py-2.5 text-sm font-bold rounded-xl border border-slate-200 focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subtext</label>
                        <textarea name="cta_subtitle" rows="3" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-brand-500">{{ old('cta_subtitle', $setting->cta_subtitle) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                        <div class="space-y-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-xs font-bold text-brand-700 uppercase tracking-wider">Button 1 (Primary)</span>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Button Label</label>
                                <input type="text" name="cta_btn1_text" value="{{ old('cta_btn1_text', $setting->cta_btn1_text) }}" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Target URL / Route</label>
                                <input type="text" name="cta_btn1_url" value="{{ old('cta_btn1_url', $setting->cta_btn1_url) }}" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200" placeholder="/properties">
                            </div>
                        </div>

                        <div class="space-y-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Button 2 (Secondary)</span>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Button Label</label>
                                <input type="text" name="cta_btn2_text" value="{{ old('cta_btn2_text', $setting->cta_btn2_text) }}" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Target URL / Route</label>
                                <input type="text" name="cta_btn2_url" value="{{ old('cta_btn2_url', $setting->cta_btn2_url) }}" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200" placeholder="/requirements/create">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Save Bar -->
        <div class="sticky bottom-4 z-20 bg-slate-900/90 backdrop-blur-md rounded-2xl p-4 text-white flex items-center justify-between shadow-2xl border border-white/10">
            <div class="flex items-center gap-2 text-xs text-slate-300">
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                <span>Changes will be applied immediately across the live website.</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('about') }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white transition">
                    View Live
                </a>
                <button type="submit" class="px-6 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-xs font-extrabold text-white transition shadow-lg shadow-brand-500/25">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

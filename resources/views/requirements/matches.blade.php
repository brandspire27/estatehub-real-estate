@extends('layouts.dashboard')

@section('title', 'Matched Properties for Requirement')
@section('page-title', 'Matched Properties')
@section('page-subtitle', 'Select properties to share directly with the buyer via WhatsApp')

@section('content')
<div class="space-y-8" x-data="{
    selectedProperties: [],
    showModal: false,
    copied: false,
    recipientName: '{{ addslashes($requirement->user->name ?? 'Buyer') }}',
    recipientPhone: '{{ addslashes($requirement->user->phone ?? '') }}',
    propertiesData: [
        @foreach($matches as $m)
        {
            id: {{ $m->property->id }},
            title: '{{ addslashes($m->property->title) }}',
            locality: '{{ addslashes($m->property->area ?: $m->property->city) }}',
            budget: '{{ addslashes($m->property->formatted_price) }}',
            type: '{{ addslashes($m->property->bhk ? $m->property->bhk . ' BHK ' . $m->property->property_type : $m->property->property_type) }}',
            link: '{{ url('/properties/' . $m->property->id) }}',
            score: {{ $m->score }}
        },
        @endforeach
    ],
    toggleSelectAll() {
        if (this.selectedProperties.length === this.propertiesData.length) {
            this.selectedProperties = [];
        } else {
            this.selectedProperties = this.propertiesData.map(p => p.id);
        }
    },
    isSelected(id) {
        return this.selectedProperties.includes(id);
    },
    toggleProperty(id) {
        if (this.isSelected(id)) {
            this.selectedProperties = this.selectedProperties.filter(item => item !== id);
        } else {
            this.selectedProperties.push(id);
        }
    },
    getSelectedList() {
        return this.propertiesData.filter(p => this.selectedProperties.includes(p.id));
    },
    generateWhatsAppMessage() {
        const selected = this.getSelectedList();
        if (selected.length === 0) return '';
        
        let msg = `Hello ${this.recipientName},\n\nHere are your matched properties \n\n`;
        selected.forEach((p, idx) => {
            msg += `${idx + 1}. ${p.title} - ${p.locality} - ${p.budget} - ${p.type} - ${p.link}\n\n`;
        });
        return msg.trim();
    },
    getCleanPhone() {
        let clean = this.recipientPhone.replace(/[^0-9]/g, '');
        if (clean.length === 10) {
            clean = '91' + clean;
        }
        return clean;
    },
    openWhatsApp(type = 'web') {
        if (this.selectedProperties.length === 0) {
            alert('Please select at least one property to share.');
            return;
        }
        const phone = this.getCleanPhone();
        const text = encodeURIComponent(this.generateWhatsAppMessage());
        
        const url = type === 'web' 
            ? `https://web.whatsapp.com/send?phone=${phone}&text=${text}`
            : `https://api.whatsapp.com/send?phone=${phone}&text=${text}`;
            
        window.open(url, '_blank');
    },
    copyToClipboard() {
        const text = this.generateWhatsAppMessage();
        navigator.clipboard.writeText(text).then(() => {
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 2500);
        });
    }
}">
    
    <!-- Requirement Context Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 uppercase tracking-wider">
                        Requirement #{{ $requirement->id }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">
                        Buyer: <strong class="text-slate-900">{{ $requirement->user->name ?? 'Buyer' }}</strong>
                        @if(!empty($requirement->user->phone))
                            (<a href="tel:{{ $requirement->user->phone }}" class="text-brand-600 hover:underline">{{ $requirement->user->phone }}</a>)
                        @endif
                    </span>
                </div>

                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                    {{ $requirement->bhk ? $requirement->bhk . ' BHK ' : '' }}{{ $requirement->property_type }}
                    in 
                    @if($requirement->locality && strcasecmp(trim($requirement->locality), trim($requirement->city)) !== 0)
                        <span class="text-brand-600">{{ $requirement->locality }}</span>,
                    @endif
                    {{ $requirement->city }}
                </h2>

                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 mt-2">
                    <span><i class="fa-solid fa-wallet text-brand-600 mr-1"></i>Budget: <strong class="text-slate-800">{{ $requirement->formatted_budget }}</strong></span>
                    <span><i class="fa-solid fa-key text-brand-600 mr-1"></i>Possession: <strong class="text-slate-800">{{ $requirement->possession_status ?: 'Any' }}</strong></span>
                    <span>Status: <strong class="text-emerald-700 font-bold">{{ $requirement->status }}</strong></span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('requirements.edit', $requirement) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Criteria
                </a>
            </div>
        </div>
    </div>

    <!-- Match Results & WhatsApp Action Header -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span>Found {{ $matches->total() }} Matched {{ Str::plural('Property', $matches->total()) }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium">
                        <span x-text="selectedProperties.length">0</span> of <span x-text="propertiesData.length">0</span> selected
                    </span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Select one or more properties below to send the pre-formatted summary directly to WhatsApp.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5">
                <button type="button" @click="toggleSelectAll()" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <span x-text="selectedProperties.length === propertiesData.length ? 'Deselect All' : 'Select All'">Select All</span>
                </button>

                <!-- WhatsApp Share Button -->
                <button type="button" 
                        @click="if (selectedProperties.length === 0) { alert('Please select at least one property to share via WhatsApp.'); } else { showModal = true; }"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Share via WhatsApp (<span x-text="selectedProperties.length">0</span>)</span>
                </button>
            </div>
        </div>

        @if($matches->isEmpty())
            <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-300 p-6 mt-6">
                <i class="fa-solid fa-magnifying-glass-chart text-4xl text-slate-300 mb-3 block"></i>
                <h4 class="text-base font-bold text-slate-800">No matching properties right now</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Properties will automatically link to this requirement whenever matching listings are published by sellers or approved by admin.
                </p>
                <div class="mt-4">
                    <a href="{{ route('properties.index', ['city' => $requirement->city]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white font-bold text-xs">
                        Browse All in {{ $requirement->city }}
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6">
                @foreach($matches as $m)
                    @php $property = $m->property; @endphp
                    <div class="group bg-white rounded-3xl border transition-all duration-300 overflow-hidden flex flex-col justify-between relative shadow-sm hover:shadow-xl"
                         :class="isSelected({{ $property->id }}) ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-slate-200/90'">
                        
                        <div>
                            <!-- Image with Match Score Ribbon & Checkbox -->
                            <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                                <img src="{{ $property->primary_image_url }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>

                                <!-- Match Score Pill -->
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black shadow-lg 
                                        {{ $m->score >= 85 ? 'bg-emerald-600 text-white' : '' }}
                                        {{ $m->score >= 70 && $m->score < 85 ? 'bg-amber-500 text-white' : '' }}
                                        {{ $m->score < 70 ? 'bg-slate-800 text-white' : '' }}">
                                        <i class="fa-solid fa-bolt text-[10px]"></i>
                                        <span>{{ $m->score }}% Match</span>
                                    </span>
                                </div>

                                <!-- Checkbox to Select Property -->
                                <div class="absolute top-3 right-3 z-10">
                                    <label class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-full shadow-md border-2 transition backdrop-blur-md"
                                           :class="isSelected({{ $property->id }}) ? 'bg-emerald-600 border-white text-white' : 'bg-white/90 border-slate-300 hover:border-emerald-500 text-transparent'"
                                           title="Select property to share on WhatsApp">
                                        <input type="checkbox" :value="{{ $property->id }}" 
                                               :checked="isSelected({{ $property->id }})"
                                               @change="toggleProperty({{ $property->id }})" 
                                               class="sr-only">
                                        <i class="fa-solid fa-check text-xs" :class="isSelected({{ $property->id }}) ? 'text-white' : 'opacity-0'"></i>
                                    </label>
                                </div>

                                <div class="absolute bottom-3 left-3 right-3 flex items-end justify-between text-white z-10">
                                    <div>
                                        <div class="text-lg font-black text-white drop-shadow-sm">{{ $property->formatted_price }}</div>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-200">
                                        {{ $property->bhk ? $property->bhk . ' BHK' : $property->property_type }}
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-5">
                                <div class="text-xs font-semibold text-brand-600 mb-1">
                                    <i class="fa-solid fa-location-dot"></i> {{ $property->area }}, {{ $property->city }}
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm leading-snug line-clamp-1 mb-3">
                                    <a href="{{ route('properties.show', $property) }}" target="_blank" class="hover:text-brand-600">
                                        {{ $property->title }}
                                    </a>
                                </h4>

                                <!-- Match Criteria Checklist -->
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1 text-[11px] text-slate-600">
                                    <div class="flex items-center justify-between">
                                        <span>City ({{ $property->city }}):</span>
                                        <span class="font-bold {{ strcasecmp($property->city, $requirement->city) === 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                            {{ strcasecmp($property->city, $requirement->city) === 0 ? '✓ Matched (+25)' : '–' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>Type ({{ $property->property_type }}):</span>
                                        <span class="font-bold {{ strcasecmp($property->property_type, $requirement->property_type) === 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                            {{ strcasecmp($property->property_type, $requirement->property_type) === 0 ? '✓ Matched (+20)' : '–' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>Locality ({{ $property->area }}):</span>
                                        <span class="font-bold text-slate-700">
                                            {{ $property->area }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="p-5 pt-0 flex items-center justify-between gap-2 border-t border-slate-100/80 mt-2">
                            <!-- Toggle Select Button -->
                            <button type="button" @click="toggleProperty({{ $property->id }})" 
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5"
                                    :class="isSelected({{ $property->id }}) ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                <i :class="isSelected({{ $property->id }}) ? 'fa-solid fa-check-circle text-emerald-600' : 'fa-regular fa-circle text-slate-400'"></i>
                                <span x-text="isSelected({{ $property->id }}) ? 'Selected' : 'Select'">Select</span>
                            </button>

                            <a href="{{ route('properties.show', $property) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition flex items-center gap-1.5">
                                <span>View Property</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $matches->links() }}
            </div>
        @endif
    </div>

    <!-- Sticky Floating Action Bar when properties are selected -->
    <div x-show="selectedProperties.length > 0" x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-6 inset-x-0 z-30 flex justify-center px-4 pointer-events-none">
        <div class="pointer-events-auto bg-slate-900 text-white px-6 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-5">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-bold">
                    <span x-text="selectedProperties.length">0</span> properties selected
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="selectedProperties = []" class="text-xs text-slate-400 hover:text-white px-2 py-1 transition">
                    Clear
                </button>
                <button type="button" @click="showModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-lg transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Share on WhatsApp &rarr;</span>
                </button>
            </div>
        </div>
    </div>

    <!-- WhatsApp Share Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div @click="showModal = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

        <!-- Dialog Box -->
        <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 overflow-hidden z-10 text-slate-800">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Share Matched Properties</h3>
                        <p class="text-xs text-slate-500">Send WhatsApp summary with links directly to buyer</p>
                    </div>
                </div>
                <button @click="showModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="mt-5 space-y-4">
                <!-- Recipient Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Recipient Name (@{{FULL NAME}})
                        </label>
                        <input type="text" x-model="recipientName" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Recipient WhatsApp Phone
                        </label>
                        <div class="relative">
                            <input type="tel" x-model="recipientPhone" placeholder="9876543210" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5">Will format with +91 country code automatically.</p>
                    </div>
                </div>

                <!-- Message Preview Box -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                            WhatsApp Message Preview (<span x-text="selectedProperties.length"></span> properties)
                        </label>
                        <button type="button" @click="copyToClipboard()" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                            <i class="fa-regular fa-copy"></i>
                            <span x-text="copied ? 'Copied!' : 'Copy Text'">Copy Text</span>
                        </button>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 font-mono text-xs text-slate-800 leading-relaxed whitespace-pre-wrap max-h-56 overflow-y-auto"
                         x-text="generateWhatsAppMessage()">
                    </div>
                </div>

                <!-- Sending Actions -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <button type="button" @click="copyToClipboard()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition flex items-center justify-center gap-2">
                        <i class="fa-regular fa-copy"></i>
                        <span x-text="copied ? 'Message Copied!' : 'Copy Message Text'">Copy Message Text</span>
                    </button>

                    <div class="flex items-center gap-2.5 w-full sm:w-auto">
                        <!-- WhatsApp Web button -->
                        <button type="button" @click="openWhatsApp('web')" class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/25 transition flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-base"></i>
                            <span>Open WhatsApp Web &rarr;</span>
                        </button>

                        <!-- WhatsApp Universal / Mobile App button -->
                        <button type="button" @click="openWhatsApp('api')" class="px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition" title="Open via WhatsApp App">
                            <i class="fa-solid fa-mobile-screen-button"></i>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'System Updates — EstateHub Admin')
@section('page-title', 'System Updates')
@section('page-subtitle', 'Manage EstateHub platform releases, database patches, and one-click updates')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-16" x-data="{
    loading: false,
    updating: false,
    hasUpdate: {{ $updateInfo['has_update'] ? 'true' : 'false' }},
    currentVersion: '{{ $currentVersion }}',
    latestVersion: '{{ $updateInfo['latest_version'] ?? '1.0.1' }}',
    alertMessage: '',
    alertType: '',

    async checkForUpdates() {
        this.loading = true;
        this.alertMessage = '';
        try {
            const res = await fetch('{{ route('admin.updates.check') }}');
            const data = await res.json();
            this.hasUpdate = data.has_update;
            this.currentVersion = data.current_version;
            this.latestVersion = data.latest_version;
            if (this.hasUpdate) {
                this.alertType = 'success';
                this.alertMessage = 'New update available: v' + this.latestVersion;
            } else {
                this.alertType = 'info';
                this.alertMessage = 'Your system is running the latest version (' + this.currentVersion + ')';
            }
        } catch (e) {
            this.alertType = 'error';
            this.alertMessage = 'Unable to connect to updates server.';
        } finally {
            this.loading = false;
        }
    },

    async applyUpdate() {
        if (!confirm('Are you sure you want to apply this update now? It will run database migrations and refresh cache.')) return;
        this.updating = true;
        this.alertMessage = '';
        try {
            const res = await fetch('{{ route('admin.updates.apply') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.alertType = 'success';
                this.alertMessage = data.message;
                this.currentVersion = data.current_version;
                this.hasUpdate = false;
            } else {
                this.alertType = 'error';
                this.alertMessage = data.message || 'Update failed.';
            }
        } catch (e) {
            this.alertType = 'error';
            this.alertMessage = 'Error applying update: ' + e.message;
        } finally {
            this.updating = false;
        }
    }
}">

    <!-- Notification Alert -->
    <div x-show="alertMessage" x-cloak 
         :class="{
            'bg-emerald-900/50 border-emerald-500/50 text-emerald-200': alertType === 'success',
            'bg-blue-900/50 border-blue-500/50 text-blue-200': alertType === 'info',
            'bg-red-900/50 border-red-500/50 text-red-200': alertType === 'error'
         }"
         class="p-4 rounded-2xl border text-sm font-semibold flex items-center justify-between transition-all">
        <span x-text="alertMessage"></span>
        <button @click="alertMessage = ''" class="text-xs opacity-70 hover:opacity-100">&times;</button>
    </div>

    <!-- Status Overview Card -->
    <div class="bg-slate-900/80 rounded-3xl border border-slate-800/80 p-6 sm:p-8 shadow-xl">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-neon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-white">Platform Version</h2>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            <span class="text-sm text-slate-400 font-mono">Current: <strong class="text-white font-bold" x-text="'v' + currentVersion">v{{ $currentVersion }}</strong></span>
                            <template x-if="hasUpdate">
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[10px] font-black uppercase tracking-wider">
                                    Update Available: <span x-text="'v' + latestVersion">v{{ $updateInfo['latest_version'] ?? '1.0.1' }}</span>
                                </span>
                            </template>
                            <template x-if="!hasUpdate">
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[10px] font-black uppercase tracking-wider">
                                    Up to Date
                                </span>
                            </template>
                            @if(!empty($updateInfo['is_extended']))
                                <span class="px-2.5 py-0.5 rounded-full bg-neon/15 text-neon border border-neon/40 text-[10px] font-black uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                    Extended License (Lifetime Updates)
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-sky-500/10 text-sky-400 border border-sky-500/30 text-[10px] font-bold uppercase tracking-wider">
                                    Regular License ({{ $updateInfo['updates_remaining'] ?? '1 Year Updates' }})
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button @click="checkForUpdates" :disabled="loading || updating" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs uppercase tracking-wider transition-all border border-slate-700 flex items-center gap-2">
                    <span x-show="loading" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    <span x-text="loading ? 'Checking...' : 'Check for Updates'">Check for Updates</span>
                </button>

                <template x-if="hasUpdate">
                    <button @click="applyUpdate" :disabled="updating" class="px-5 py-2.5 rounded-xl bg-neon hover:bg-neon/90 text-black font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-neon/20 flex items-center gap-2">
                        <span x-show="updating" class="w-3.5 h-3.5 border-2 border-black border-t-transparent rounded-full animate-spin"></span>
                        <span x-text="updating ? 'Updating System...' : 'One-Click Update Now'">One-Click Update Now</span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Update Details & Changelog -->
    <div class="bg-slate-900/80 rounded-3xl border border-slate-800/80 p-6 sm:p-8 shadow-xl space-y-6">
        <div class="border-b border-slate-800 pb-4">
            <h3 class="text-base font-black text-white flex items-center justify-between">
                <span>Release Information & Changelog</span>
                <span class="text-xs text-slate-400 font-mono">Released: {{ $updateInfo['release_date'] ?? date('Y-m-d') }}</span>
            </h3>
            <p class="text-xs text-slate-400 mt-1">{{ $updateInfo['title'] ?? 'EstateHub Updates' }}</p>
        </div>

        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Included Changes:</h4>
            <ul class="space-y-2 text-sm text-slate-300">
                @foreach($updateInfo['changelog'] ?? [] as $change)
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-lg bg-neon/10 text-neon flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">✓</span>
                        <span class="leading-relaxed">{{ $change }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Safe Update Guarantee Notice -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 text-xs text-slate-400 space-y-1">
            <strong class="text-slate-200 block font-bold">Automatic Update Process</strong>
            <p>The auto-updater safely checks dependencies, runs pending database schema migrations, and flushes compiled views and route caches without affecting your active listings, users, or media.</p>
        </div>
    </div>

    <!-- Support Assistance Box -->
    <div class="rounded-2xl bg-emerald-950/40 border border-emerald-500/30 p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-xs text-slate-300">
            <strong class="text-white block text-sm mb-0.5">Need customized updates or custom module development?</strong>
            Our core engineering team can assist you with seamless upgrades.
        </div>
        <a href="https://wa.me/917002484119?text=Hi%20WebotApp%2C%20I%20need%20custom%20update%20support%20for%20EstateHub" target="_blank" class="shrink-0 px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow">
            WhatsApp Core Team
        </a>
    </div>

</div>
@endsection

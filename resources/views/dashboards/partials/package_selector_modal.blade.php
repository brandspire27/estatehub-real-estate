@props(['availablePackages', 'user'])

<!-- Package Selector Modal -->
<div x-show="packageModalOpen" 
     x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="packageModalOpen" 
         x-transition:enter="ease-out duration-200" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-150" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         @click="packageModalOpen = false" 
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

    <!-- Modal Dialog -->
    <div x-show="packageModalOpen" 
         x-transition:enter="ease-out duration-200" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100" 
         x-transition:leave="ease-in duration-150" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95" 
         class="relative bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-4xl w-full p-6 sm:p-8 z-10 overflow-hidden text-slate-800">
        
        <!-- Close Button -->
        <button @click="packageModalOpen = false" type="button" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-slate-100 transition">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center max-w-xl mx-auto mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-brand-50 text-brand-600 border border-brand-200 mb-2">
                <i class="fa-solid fa-gem"></i> Select Membership Package
            </span>
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Choose Your Platform Plan</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Select a package to unlock property listing limits, direct buyer inquiries, and priority auto-matching</p>
        </div>

        <!-- Package Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($availablePackages as $pkg)
                @php
                    $isCurrent = $user->package_id === $pkg->id;
                @endphp
                <div class="rounded-3xl p-6 border-2 transition relative flex flex-col justify-between
                    {{ $isCurrent ? 'border-brand-600 bg-brand-50/20 shadow-lg ring-2 ring-brand-500/20' : 'border-slate-200 hover:border-slate-300 bg-white hover:shadow-md' }}">
                    
                    @if($pkg->badge)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                            {{ $isCurrent ? 'bg-brand-600 text-white shadow-sm' : 'bg-amber-400 text-amber-950 shadow-sm' }}">
                            {{ $isCurrent ? 'Current Plan' : $pkg->badge }}
                        </div>
                    @endif

                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <h4 class="font-extrabold text-slate-900 text-lg">{{ $pkg->name }}</h4>
                        </div>

                        <p class="text-xs text-slate-500 line-clamp-2 mb-4">{{ $pkg->description }}</p>

                        <!-- Price -->
                        <div class="mb-5 pb-4 border-b border-slate-100">
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-black text-slate-900">{{ $pkg->formatted_price }}</span>
                                <span class="text-xs font-semibold text-slate-400">/ {{ $pkg->duration_days }} days</span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5 font-medium">Billed {{ $pkg->billing_period }}</div>
                        </div>

                        <!-- Highlights List -->
                        <ul class="space-y-2.5 text-xs text-slate-600 mb-6">
                            <li class="flex items-start gap-2 font-medium">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <span>
                                    @if($pkg->isUnlimitedListings())
                                        <strong>Unlimited</strong> listings
                                    @else
                                        Up to <strong>{{ $pkg->listing_limit }}</strong> listings
                                    @endif
                                </span>
                            </li>
                            <li class="flex items-start gap-2 font-medium">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <span><strong>{{ $pkg->featured_limit }}</strong> featured ranking slots</span>
                            </li>
                            <li class="flex items-start gap-2 font-medium">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <span><strong>{{ $pkg->contact_unlock_limit }}</strong> buyer phone unlocks</span>
                            </li>
                            @if(!empty($pkg->features) && is_array($pkg->features))
                                @foreach(array_slice($pkg->features, 0, 3) as $f)
                                    <li class="flex items-start gap-2 text-slate-500">
                                        <i class="fa-solid fa-check text-slate-400 mt-0.5 flex-shrink-0"></i>
                                        <span>{{ $f }}</span>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <!-- Action Button -->
                    <div>
                        @if($isCurrent)
                            <div class="w-full py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs text-center flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Active Plan</span>
                            </div>
                        @else
                            <form action="{{ route('dashboard.select_package') }}" method="POST">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-1.5">
                                    <span>{{ $pkg->isFree() ? 'Activate Free Plan' : 'Select ' . $pkg->name }}</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        <div class="text-center mt-6 text-[11px] text-slate-400 flex items-center justify-center gap-2">
            <i class="fa-solid fa-shield-check text-emerald-500"></i>
            <span>No long term contracts. Upgrade or switch plans at any time from your dashboard.</span>
        </div>

    </div>
</div>

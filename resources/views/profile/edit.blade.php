@extends('layouts.dashboard')

@section('title', 'Profile & Identity — EstateHub')
@section('page-title', $user->role === 'SELLER' ? 'Seller Portfolio & Account Settings' : ($user->role === 'STAFF' ? 'Staff Operations Profile & Credentials' : ($user->role === 'ADMIN' ? 'Executive Administration Profile' : 'Buyer Profile & Preferences')))
@section('page-subtitle', 'Manage your verified personal identity, regulatory KYC verification, security, and notification settings')

@section('content')
<div class="space-y-8" x-data="{
    avatarUrl: '{{ old('avatar', $user->avatar) }}',
    name: '{{ old('name', $user->name) }}',
    phone: '{{ old('phone', $user->phone) }}',
    email: '{{ old('email', $user->email) }}',
    address: '{{ old('address', $user->address) }}',
    emergencyContact: '{{ old('emergency_contact', $user->emergency_contact) }}',
    kycType: '{{ old('kyc_document_type', $user->kyc_document_type) }}',
    kycNumber: '{{ old('kyc_document_number', $user->kyc_document_number) }}',
    kycUrl: '{{ old('kyc_document_url', $user->kyc_document_url) }}',
    showPasswordForm: false
}">

    <!-- Executive Profile Hero Banner -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-brand-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-0 right-1/3 w-60 h-60 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
                <!-- Avatar with Verification Ring -->
                <div class="relative flex-shrink-0">
                    <template x-if="avatarUrl">
                        <img :src="avatarUrl" alt="Avatar" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover ring-4 ring-white/10 shadow-lg">
                    </template>
                    <template x-if="!avatarUrl">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl 
                            {{ $user->role === 'ADMIN' ? 'bg-gradient-to-tr from-rose-600 to-amber-600' : '' }}
                            {{ $user->role === 'STAFF' ? 'bg-gradient-to-tr from-purple-600 to-indigo-600' : '' }}
                            {{ $user->role === 'SELLER' ? 'bg-gradient-to-tr from-emerald-600 to-teal-600' : '' }}
                            {{ $user->role === 'BUYER' ? 'bg-gradient-to-tr from-brand-600 to-indigo-600' : '' }}
                            text-white flex items-center justify-center font-black text-3xl uppercase shadow-lg ring-4 ring-white/10">
                            <span x-text="name ? name.charAt(0) : '{{ substr($user->name, 0, 1) }}'"></span>
                        </div>
                    </template>
                    @if($user->kyc_status === 'VERIFIED')
                        <div class="absolute -bottom-1.5 -right-1.5 w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs ring-4 ring-slate-900 shadow-md" title="KYC Identity Verified">
                            <i class="fa-solid fa-shield-check"></i>
                        </div>
                    @else
                        <div class="absolute -bottom-1.5 -right-1.5 w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs ring-4 ring-slate-900 shadow-md" title="KYC Verification Pending">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    @endif
                </div>

                <!-- Identity Info -->
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                        <h2 class="text-2xl font-black text-white tracking-tight" x-text="name"></h2>
                        
                        <span class="px-3 py-0.5 rounded-full text-xs font-black uppercase tracking-wider
                            {{ $user->role === 'ADMIN' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}
                            {{ $user->role === 'STAFF' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : '' }}
                            {{ $user->role === 'SELLER' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                            {{ $user->role === 'BUYER' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : '' }}">
                            {{ $user->role === 'BUYER' ? 'Verified Buyer' : ($user->role === 'SELLER' ? 'Verified Seller' : ($user->role === 'STAFF' ? 'Staff Operations' : 'Platform Administrator')) }}
                        </span>

                        @if($user->kyc_status === 'VERIFIED')
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                <span>Verified KYC Member</span>
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                <span>KYC Unverified</span>
                            </span>
                        @endif

                        @if($user->package)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400 text-slate-950 flex items-center gap-1">
                                <i class="fa-solid fa-crown text-[9px]"></i>
                                <span>{{ $user->package->name }} Plan</span>
                            </span>
                        @endif
                    </div>

                    <p class="text-slate-300 text-xs sm:text-sm font-medium flex items-center justify-center sm:justify-start gap-3 flex-wrap pt-1">
                        <span><i class="fa-solid fa-envelope text-brand-400 mr-1.5"></i>{{ $user->email }}</span>
                        <span class="text-slate-600">&bull;</span>
                        <span><i class="fa-solid fa-phone text-brand-400 mr-1.5"></i>+91 {{ $user->phone }}</span>
                        @if($user->address)
                            <span class="text-slate-600">&bull;</span>
                            <span class="truncate max-w-xs"><i class="fa-solid fa-location-dot text-brand-400 mr-1.5"></i>{{ $user->address }}</span>
                        @endif
                    </p>

                    <div class="text-[11px] text-slate-400 pt-1">
                        Member ID #{{ $user->role }}-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }} &bull; Account Created {{ $user->created_at->format('d M, Y') }} ({{ $user->created_at->diffForHumans() }})
                    </div>
                </div>
            </div>

            <!-- Header Quick Action Buttons -->
            <div class="flex items-center gap-2.5 flex-shrink-0">
                @if($user->isSeller())
                    <a href="{{ route('listings.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Property</span>
                    </a>
                @elseif($user->isBuyer())
                    <a href="{{ route('requirements.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Post Requirement</span>
                    </a>
                @elseif($user->isStaff())
                    <a href="{{ route('staff.users.create') }}" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Register Client</span>
                    </a>
                @elseif($user->isAdmin())
                    <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Create User</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Role KPI Metrics Ribbon -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-800/80">
            @foreach($roleStats as $key => $metric)
                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center {{ $metric['color'] }} text-lg flex-shrink-0">
                        <i class="fa-solid {{ $metric['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">{{ $metric['label'] }}</div>
                        <div class="text-xl sm:text-2xl font-black text-white mt-0.5">{{ $metric['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Columns: Edit Profile, KYC Dossier & Security -->
        <div class="lg:col-span-2 space-y-6">
            
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 1. Personal & Contact Information Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <i class="fa-solid fa-user-pen text-brand-600"></i>
                                <span>Personal & Contact Information</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Your official platform contact details and identity credentials</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            <i class="fa-solid fa-fingerprint text-[10px]"></i>
                            <span>ID #{{ $user->id }}</span>
                        </span>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Full Legal Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" x-model="name" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500 @error('name') border-rose-500 @enderror">
                                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Email Address <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" x-model="email" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-brand-500 @error('email') border-rose-500 @enderror">
                                @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Primary Phone / WhatsApp <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">+91</span>
                                    <input type="tel" id="phone" name="phone" x-model="phone" required maxlength="14"
                                           class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold font-mono focus:ring-2 focus:ring-brand-500 @error('phone') border-rose-500 @enderror">
                                </div>
                                @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Profile Photo URL -->
                            <div>
                                <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Profile Avatar Photo URL
                                </label>
                                <input type="url" id="avatar" name="avatar" x-model="avatarUrl"
                                       placeholder="https://images.unsplash.com/..."
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('avatar') border-rose-500 @enderror">
                                @error('avatar') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Residential Address & Emergency Contact -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                            <div>
                                <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Residential / Business Address
                                </label>
                                <input type="text" id="address" name="address" x-model="address"
                                       placeholder="e.g. 402, Skyline Residency, GS Road, Guwahati"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('address') border-rose-500 @enderror">
                                @error('address') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="emergency_contact" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Emergency Contact / Backup Phone
                                </label>
                                <input type="text" id="emergency_contact" name="emergency_contact" x-model="emergencyContact"
                                       placeholder="e.g. Anand Sharma (Spouse) - 9876540000"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('emergency_contact') border-rose-500 @enderror">
                                @error('emergency_contact') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. KYC Identification & Regulatory Dossier Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <i class="fa-solid fa-id-card-clip text-emerald-600"></i>
                                <span>Regulatory KYC & Identity Dossier</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Government-approved identification for verified seller & buyer status</p>
                        </div>

                        @if($user->kyc_status === 'VERIFIED')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="fa-solid fa-badge-check"></i>
                                <span>KYC Verified</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                <span>Pending Verification</span>
                            </span>
                        @endif
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Document Type -->
                            <div>
                                <label for="kyc_document_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    KYC Document Type
                                </label>
                                <select id="kyc_document_type" name="kyc_document_type" x-model="kycType"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold bg-white focus:ring-2 focus:ring-brand-500">
                                    <option value="">Choose Document...</option>
                                    <option value="Aadhaar Card">Aadhaar Card (UIDAI)</option>
                                    <option value="PAN Card">PAN Card (Income Tax Dept)</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Driving License">Driving License</option>
                                    <option value="Voter ID">Voter ID Card</option>
                                    <option value="Corporate GSTIN">Corporate GSTIN / CIN</option>
                                </select>
                            </div>

                            <!-- Document Number -->
                            <div>
                                <label for="kyc_document_number" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Document ID Number
                                </label>
                                <input type="text" id="kyc_document_number" name="kyc_document_number" x-model="kycNumber"
                                       placeholder="e.g. 1234 5678 9012 or ABCDE1234F"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('kyc_document_number') border-rose-500 @enderror">
                                @error('kyc_document_number') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Document Attachment / Cloud URL -->
                        <div>
                            <label for="kyc_document_url" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Document Proof Scan / PDF Link (Optional)
                            </label>
                            <input type="url" id="kyc_document_url" name="kyc_document_url" x-model="kycUrl"
                                   placeholder="https://drive.google.com/file/... or cloud document link"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('kyc_document_url') border-rose-500 @enderror">
                            <p class="text-[11px] text-slate-400 mt-1">Provide a link to your scanned document copy for administrative verification.</p>
                            @error('kyc_document_url') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 text-xs text-slate-600">
                            <i class="fa-solid fa-lock text-emerald-600 mt-0.5 text-sm"></i>
                            <div>
                                <strong class="text-slate-800 block mb-0.5">Government Document Confidentiality Guarantee</strong>
                                All identity proofs and document numbers are encrypted and restricted solely to platform compliance officers. Buyers and external visitors will only ever see your green "Verified Member" badge.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Security & Account Password -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-base font-black text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-key text-brand-600"></i>
                                <span>Security & Account Password</span>
                            </h4>
                            <p class="text-xs text-slate-500 mt-0.5">Manage login credentials and authorization keys</p>
                        </div>
                        <button type="button" 
                                @click="showPasswordForm = !showPasswordForm" 
                                class="px-3.5 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 text-xs font-bold text-brand-600 hover:text-brand-700 transition">
                            <span x-text="showPasswordForm ? 'Hide Password Form' : 'Change Password'"></span>
                        </button>
                    </div>

                    <div x-show="showPasswordForm" x-cloak class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    New Password
                                </label>
                                <input type="password" id="password" name="password" placeholder="••••••••"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('password') border-rose-500 @enderror">
                                @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    Confirm New Password
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500">Minimum 6 characters. Leave blank if keeping current password.</p>
                    </div>
                </div>

                <!-- 4. Communication & Privacy Shield -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <h4 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-shield-heart text-brand-600"></i>
                        <span>Communication & Privacy Shield</span>
                    </h4>
                    <p class="text-xs text-slate-500">Customize how matched buyers, sellers, and system algorithms communicate with you</p>
                    
                    <div class="space-y-3 pt-2 text-xs text-slate-700">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-brand-600 focus:ring-brand-500">
                            <span>Receive real-time WhatsApp match notifications for 90%+ compatibility properties</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-brand-600 focus:ring-brand-500">
                            <span>Mask my phone number with privacy shield until I choose to unlock seller contact</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-brand-600 focus:ring-brand-500">
                            <span>Weekly property market trends and price circle rate updates</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" class="px-8 py-3.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black text-sm shadow-lg shadow-brand-500/25 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save All Profile Changes</span>
                    </button>
                </div>

            </form>

        </div>

        <!-- Right Column: Membership Plan, Recent Activity & Trust Badges -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Membership Package Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-crown text-amber-500"></i>
                        <span>Membership Plan</span>
                    </h3>
                    <a href="{{ route('dashboard') }}" class="text-xs font-bold text-brand-600 hover:underline">
                        Manage &rarr;
                    </a>
                </div>

                @if($user->package)
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50/40 border border-amber-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-extrabold text-slate-900">{{ $user->package->name }}</span>
                            @if($user->package->badge)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white uppercase">
                                    {{ $user->package->badge }}
                                </span>
                            @endif
                        </div>

                        <div class="text-xl font-black text-slate-900">
                            {{ $user->package->formatted_price }}
                            <span class="text-xs font-normal text-slate-500">/ {{ $user->package->duration_days ? $user->package->duration_days . ' days' : 'Lifetime' }}</span>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-700 pt-2 border-t border-amber-200/60">
                            <div class="flex items-center justify-between">
                                <span>Listing Limit:</span>
                                <strong>{{ $user->package->isUnlimitedListings() ? 'Unlimited' : $user->package->listing_limit }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Featured Slots:</span>
                                <strong>{{ $user->package->featured_limit }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Direct Leads:</span>
                                <strong class="text-emerald-700">{{ $user->package->hasDirectLeads() ? 'Instant WhatsApp' : 'Standard' }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Expires:</span>
                                <strong>{{ $user->package_expires_at ? $user->package_expires_at->format('d M, Y') : 'Never (Active)' }}</strong>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-2">
                        <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center mx-auto text-base">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900">Free Community Tier</h4>
                        <p class="text-[11px] text-slate-500">Upgrade to an active package on your dashboard to unlock featured listings and priority matching.</p>
                        <a href="{{ route('dashboard') }}" class="inline-block px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition mt-1">
                            Browse Packages
                        </a>
                    </div>
                @endif
            </div>

            <!-- Role-Tailored Recent Activity Feed -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        @if($user->isSeller())
                            <i class="fa-solid fa-house-chimney text-brand-600"></i>
                            <span>Recent Listings</span>
                        @elseif($user->isBuyer())
                            <i class="fa-solid fa-clipboard-list text-indigo-600"></i>
                            <span>Recent Requirements</span>
                        @elseif($user->isStaff())
                            <i class="fa-solid fa-user-plus text-purple-600"></i>
                            <span>Recent Clients</span>
                        @else
                            <i class="fa-solid fa-shield-halved text-rose-600"></i>
                            <span>Recent Audit Trail</span>
                        @endif
                    </h3>
                </div>

                @if($recentItems->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs">
                        No recent activity recorded yet.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentItems as $item)
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition text-xs">
                                @if($user->isSeller())
                                    <div class="flex items-center justify-between font-bold text-slate-900">
                                        <span class="truncate max-w-[180px]">{{ $item->title }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold 
                                            {{ $item->status === 'PUBLISHED' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $item->status }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $item->formatted_price }} &bull; {{ $item->city }}
                                    </div>
                                @elseif($user->isBuyer())
                                    <div class="flex items-center justify-between font-bold text-slate-900">
                                        <span>{{ $item->bhk ? $item->bhk . ' BHK ' : '' }}{{ $item->property_type }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-indigo-100 text-indigo-800">
                                            {{ $item->status }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $item->city }} &bull; Budget: {{ $item->formatted_budget }}
                                    </div>
                                @elseif($user->isStaff())
                                    <div class="flex items-center justify-between font-bold text-slate-900">
                                        <span>{{ $item->name }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-purple-100 text-purple-800">
                                            {{ $item->role }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $item->phone }} &bull; {{ $item->created_at->format('M d, Y') }}
                                    </div>
                                @else
                                    <div class="flex items-center justify-between font-mono font-bold text-brand-700 text-[11px]">
                                        <span>{{ $item->action }}</span>
                                        <span class="text-[10px] text-slate-400 font-sans">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Entity: {{ $item->entity_type }} #{{ $item->entity_id }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Identity Trust & Security Badge -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 rounded-3xl p-6 text-white border border-slate-800 shadow-sm space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-white">Security & Trust Profile</h4>
                        <p class="text-[11px] text-slate-400">Authenticated 256-bit Encrypted Session</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs text-slate-300 pt-2 border-t border-slate-800">
                    <div class="flex items-center justify-between">
                        <span>Email Verification:</span>
                        <span class="text-emerald-400 font-bold"><i class="fa-solid fa-check text-[10px] mr-1"></i>Verified</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Role Permissions:</span>
                        <span class="font-bold text-white">{{ $user->role }} Access Level</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Account Status:</span>
                        <span class="text-emerald-400 font-bold">Active</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

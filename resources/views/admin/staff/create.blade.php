@extends('layouts.dashboard')

@section('title', 'Admin - Create Staff')
@section('page-title', 'Create Staff Account')
@section('page-subtitle', 'Staff members receive access to client onboarding and listing creation privileges')

@section('content')
<div class="max-w-2xl bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm">
    <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Staff Member Full Name <span class="text-rose-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   placeholder="e.g. Vikram Singhania"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('name') border-rose-500 @enderror">
            @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address <span class="text-rose-500">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   placeholder="staff.vikram@realestate.com"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('email') border-rose-500 @enderror">
            @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Staff Phone Number <span class="text-rose-500">*</span></label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                   placeholder="9876543211"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('phone') border-rose-500 @enderror">
            @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Initial Password <span class="text-rose-500">*</span></label>
            <input type="password" id="password" name="password" value="password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('password') border-rose-500 @enderror">
            <p class="text-[11px] text-slate-400 mt-1">Default set to 'password'.</p>
        </div>

        <!-- Staff KYC Section -->
        <div class="pt-4 border-t border-slate-200">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900">Staff KYC & Identity Verification</h4>
                    <p class="text-[11px] text-slate-500">Record regulatory background check & identification</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- KYC Document Type -->
                <div>
                    <label for="kyc_document_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Document Type <span class="text-emerald-600 font-bold">(KYC)</span>
                    </label>
                    <select id="kyc_document_type" name="kyc_document_type"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold bg-white focus:ring-2 focus:ring-brand-500">
                        <option value="">Select Document...</option>
                        <option value="Aadhaar Card" {{ old('kyc_document_type') === 'Aadhaar Card' ? 'selected' : '' }}>Aadhaar Card</option>
                        <option value="PAN Card" {{ old('kyc_document_type') === 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                        <option value="Passport" {{ old('kyc_document_type') === 'Passport' ? 'selected' : '' }}>Passport</option>
                        <option value="Driving License" {{ old('kyc_document_type') === 'Driving License' ? 'selected' : '' }}>Driving License</option>
                        <option value="Voter ID" {{ old('kyc_document_type') === 'Voter ID' ? 'selected' : '' }}>Voter ID Card</option>
                    </select>
                </div>

                <!-- KYC Document Number -->
                <div>
                    <label for="kyc_document_number" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        ID / Document Number
                    </label>
                    <input type="text" id="kyc_document_number" name="kyc_document_number" value="{{ old('kyc_document_number') }}"
                           placeholder="e.g. 1234-5678-9012 or ABCDE1234F"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <!-- KYC Document URL / Attachment -->
            <div class="mt-4">
                <label for="kyc_document_url" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    KYC Document Scan / Cloud Link (Optional)
                </label>
                <input type="url" id="kyc_document_url" name="kyc_document_url" value="{{ old('kyc_document_url') }}"
                       placeholder="https://storage.example.com/kyc/vikram-aadhaar.pdf"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
            </div>

            <!-- Residential Address & Emergency Contact -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Residential Address
                    </label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}"
                           placeholder="House No, Street, City, State, PIN"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="emergency_contact" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Emergency Contact (Name & Phone)
                    </label>
                    <input type="text" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}"
                           placeholder="Suresh Singhania (Brother) - 9876599999"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-id-card"></i>
                <span>Create Staff Account & KYC</span>
            </button>
        </div>
    </form>
</div>
@endsection

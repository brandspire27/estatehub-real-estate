@extends('layouts.dashboard')

@section('title', 'Register New Client')
@section('page-title', 'Register Client Account')
@section('page-subtitle', 'Onboard a new Buyer or Seller into the EstateHub marketplace')

@section('content')
<div class="max-w-2xl bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm">
    <form action="{{ route('staff.users.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Client Role -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Account Role <span class="text-rose-500">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="BUYER" class="sr-only peer" checked>
                    <div class="p-3 text-center rounded-xl border-2 border-slate-200 peer-checked:border-brand-600 peer-checked:bg-brand-50/50 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-cart-shopping text-brand-600 text-lg mb-1 block"></i>
                        <span class="text-xs font-bold text-slate-800">Buyer</span>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="SELLER" class="sr-only peer">
                    <div class="p-3 text-center rounded-xl border-2 border-slate-200 peer-checked:border-brand-600 peer-checked:bg-brand-50/50 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-house-circle-check text-brand-600 text-lg mb-1 block"></i>
                        <span class="text-xs font-bold text-slate-800">Seller</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Client Full Name <span class="text-rose-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   placeholder="e.g. Anand Roy"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('name') border-rose-500 @enderror">
            @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address <span class="text-rose-500">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   placeholder="client@example.com"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('email') border-rose-500 @enderror">
            @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Phone Number (10 Digits) <span class="text-rose-500">*</span></label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                   placeholder="9876543210"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('phone') border-rose-500 @enderror">
            @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Temporary Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Initial Password <span class="text-rose-500">*</span></label>
            <input type="password" id="password" name="password" value="password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('password') border-rose-500 @enderror">
            <p class="text-[11px] text-slate-400 mt-1">Default set to 'password'. The client can reset it later.</p>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md transition">
                Create Client Account
            </button>
        </div>
    </form>
</div>
@endsection

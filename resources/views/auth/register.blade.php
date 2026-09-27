@extends('layouts.app')

@section('title', 'Create Your Account — EstateHub')

@section('content')
<div class="min-h-[85vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-brand-600 text-white items-center justify-center text-2xl shadow-lg shadow-brand-500/25 mb-3">
                <i class="fa-solid fa-city"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Join EstateHub</h2>
            <p class="mt-2 text-sm text-slate-500">
                Register as a Property Buyer or Seller to start matching
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl rounded-3xl border border-slate-200/80">
            
            <form class="space-y-4" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Role Selection -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        I am registering as:
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="BUYER" class="sr-only peer" {{ old('role', 'BUYER') === 'BUYER' ? 'checked' : '' }}>
                            <div class="p-3 text-center rounded-xl border-2 border-slate-200 peer-checked:border-brand-600 peer-checked:bg-brand-50/50 hover:bg-slate-50 transition">
                                <i class="fa-solid fa-cart-shopping text-brand-600 text-lg mb-1 block"></i>
                                <span class="text-xs font-bold text-slate-800">Property Buyer</span>
                                <span class="text-[10px] text-slate-500 block">Looking for home</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="SELLER" class="sr-only peer" {{ old('role') === 'SELLER' ? 'checked' : '' }}>
                            <div class="p-3 text-center rounded-xl border-2 border-slate-200 peer-checked:border-brand-600 peer-checked:bg-brand-50/50 hover:bg-slate-50 transition">
                                <i class="fa-solid fa-house-circle-check text-brand-600 text-lg mb-1 block"></i>
                                <span class="text-xs font-bold text-slate-800">Property Seller</span>
                                <span class="text-[10px] text-slate-500 block">List properties</span>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Full Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           placeholder="e.g. Rahul Sharma"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 @error('name') border-rose-500 @enderror">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           placeholder="rahul@example.com"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone (min 10 digits) -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Phone Number (10 Digits)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500">+91</span>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required
                               placeholder="9876543210"
                               class="w-full pl-12 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 @error('phone') border-rose-500 @enderror">
                    </div>
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Password</label>
                    <input id="password" name="password" type="password" required
                           placeholder="At least 6 characters"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 @error('password') border-rose-500 @enderror">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           placeholder="Repeat your password"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-500/25 transition duration-200">
                        Complete Free Registration
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500">
                Already registered?
                <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-700 ml-1">
                    Sign In
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

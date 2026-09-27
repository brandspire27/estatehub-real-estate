@extends('layouts.dashboard')

@section('title', 'Admin - Create User')
@section('page-title', 'Register New User')
@section('page-subtitle', 'Add buyers, sellers, staff members, or administrators with customized role permissions')

@section('content')
<div class="max-w-3xl bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm" x-data="{
    selectedRole: '{{ old('role', 'BUYER') }}'
}">
    <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
        <div>
            <h3 class="font-bold text-slate-900 text-lg">User Account Details</h3>
            <p class="text-xs text-slate-500 mt-0.5">Fill in credentials and assign an operational role</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Users</span>
        </a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Role Selection -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Account Role <span class="text-rose-500">*</span>
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <label :class="selectedRole === 'BUYER' ? 'border-indigo-600 bg-indigo-50/40 text-indigo-900 ring-2 ring-indigo-500/20' : 'border-slate-200 hover:border-slate-300 text-slate-700'"
                       class="p-4 rounded-2xl border cursor-pointer transition flex flex-col items-center text-center">
                    <input type="radio" name="role" value="BUYER" x-model="selectedRole" class="sr-only">
                    <i class="fa-solid fa-house-user text-xl mb-1.5" :class="selectedRole === 'BUYER' ? 'text-indigo-600' : 'text-slate-400'"></i>
                    <span class="text-xs font-extrabold">Buyer</span>
                    <span class="text-[10px] text-slate-500 mt-0.5">Post requirements</span>
                </label>

                <label :class="selectedRole === 'SELLER' ? 'border-emerald-600 bg-emerald-50/40 text-emerald-900 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300 text-slate-700'"
                       class="p-4 rounded-2xl border cursor-pointer transition flex flex-col items-center text-center">
                    <input type="radio" name="role" value="SELLER" x-model="selectedRole" class="sr-only">
                    <i class="fa-solid fa-building text-xl mb-1.5" :class="selectedRole === 'SELLER' ? 'text-emerald-600' : 'text-slate-400'"></i>
                    <span class="text-xs font-extrabold">Seller</span>
                    <span class="text-[10px] text-slate-500 mt-0.5">List properties</span>
                </label>

                <label :class="selectedRole === 'STAFF' ? 'border-purple-600 bg-purple-50/40 text-purple-900 ring-2 ring-purple-500/20' : 'border-slate-200 hover:border-slate-300 text-slate-700'"
                       class="p-4 rounded-2xl border cursor-pointer transition flex flex-col items-center text-center">
                    <input type="radio" name="role" value="STAFF" x-model="selectedRole" class="sr-only">
                    <i class="fa-solid fa-id-badge text-xl mb-1.5" :class="selectedRole === 'STAFF' ? 'text-purple-600' : 'text-slate-400'"></i>
                    <span class="text-xs font-extrabold">Staff</span>
                    <span class="text-[10px] text-slate-500 mt-0.5">Operations desk</span>
                </label>

                <label :class="selectedRole === 'ADMIN' ? 'border-rose-600 bg-rose-50/40 text-rose-900 ring-2 ring-rose-500/20' : 'border-slate-200 hover:border-slate-300 text-slate-700'"
                       class="p-4 rounded-2xl border cursor-pointer transition flex flex-col items-center text-center">
                    <input type="radio" name="role" value="ADMIN" x-model="selectedRole" class="sr-only">
                    <i class="fa-solid fa-shield-halved text-xl mb-1.5" :class="selectedRole === 'ADMIN' ? 'text-rose-600' : 'text-slate-400'"></i>
                    <span class="text-xs font-extrabold">Admin</span>
                    <span class="text-[10px] text-slate-500 mt-0.5">Full control</span>
                </label>
            </div>
            @error('role') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- 2. Personal Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Full Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Ramesh Chandra Sharma"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('name') border-rose-500 @enderror">
                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Mobile Phone Number <span class="text-rose-500">*</span>
                </label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                       placeholder="9876543210"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('phone') border-rose-500 @enderror">
                @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- 3. Email & Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Email Address <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       placeholder="user@example.com"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('email') border-rose-500 @enderror">
                @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Account Password <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="password" name="password" value="{{ old('password', 'password') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('password') border-rose-500 @enderror">
                <p class="text-[11px] text-slate-400 mt-1">Default set to 'password' for convenience.</p>
                @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- 4. Optional Package Assignment (Buyer / Seller) -->
        <div x-show="selectedRole === 'BUYER' || selectedRole === 'SELLER'" class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
            <label for="package_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Assign Membership Package (Optional)
            </label>
            <select id="package_id" name="package_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold bg-white focus:ring-2 focus:ring-brand-500">
                <option value="">None (Free / Default Access)</option>
                @foreach($packages as $pkg)
                    <option value="{{ $pkg->id }}" {{ old('package_id') == $pkg->id ? 'selected' : '' }}>
                        {{ $pkg->name }} — {{ $pkg->formatted_price }} ({{ $pkg->duration_days ? $pkg->duration_days . ' Days' : 'Lifetime' }}) &bull; Target: {{ $pkg->target_role }}
                    </option>
                @endforeach
            </select>
            <p class="text-[11px] text-slate-500 mt-1.5">Pre-assign an active tier plan to grant listing/search limits immediately.</p>
        </div>

        <!-- 5. Active Status Checkbox -->
        <div class="flex items-center gap-2 pt-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                   class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
            <label for="is_active" class="text-xs font-bold text-slate-700">
                Active Account Status (User can sign in immediately)
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black text-xs shadow-md shadow-brand-500/20 transition flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Create User Account</span>
            </button>
        </div>
    </form>
</div>
@endsection

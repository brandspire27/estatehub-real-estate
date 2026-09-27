@extends('layouts.dashboard')

@section('title', 'Admin - Edit Package')
@section('page-title', 'Edit Subscription Package')
@section('page-subtitle', 'Update pricing tier details and listing quotas for ' . $package->name)

@section('content')
<div class="max-w-4xl bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm">
    <form action="{{ route('admin.packages.update', $package) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Package Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $package->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('name') border-rose-500 @enderror">
                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Badge -->
            <div>
                <label for="badge" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Badge Tag (Optional)
                </label>
                <input type="text" id="badge" name="badge" value="{{ old('badge', $package->badge) }}"
                       placeholder="e.g. Most Popular, Recommended"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Price -->
            <div>
                <label for="price" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Price in INR (₹) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₹</span>
                    <input type="number" id="price" name="price" value="{{ old('price', $package->price) }}" required min="0" step="0.01"
                           class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500 @error('price') border-rose-500 @enderror">
                </div>
                @error('price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Billing Period -->
            <div>
                <label for="billing_period" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Billing Cycle <span class="text-rose-500">*</span>
                </label>
                <select id="billing_period" name="billing_period" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                    <option value="monthly" {{ old('billing_period', $package->billing_period) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ old('billing_period', $package->billing_period) === 'quarterly' ? 'selected' : '' }}>Quarterly (3 Months)</option>
                    <option value="yearly" {{ old('billing_period', $package->billing_period) === 'yearly' ? 'selected' : '' }}>Yearly (12 Months)</option>
                    <option value="lifetime" {{ old('billing_period', $package->billing_period) === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                </select>
            </div>

            <!-- Duration in Days -->
            <div>
                <label for="duration_days" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Duration (Days) <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" required min="1"
                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Listing Limit -->
            <div>
                <label for="listing_limit" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Listing Limit <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="listing_limit" name="listing_limit" value="{{ old('listing_limit', $package->listing_limit) }}" required
                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                <p class="text-[11px] text-slate-400 mt-1">Enter -1 for unlimited listings.</p>
            </div>

            <!-- Featured Listings Limit -->
            <div>
                <label for="featured_limit" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Featured Listings <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="featured_limit" name="featured_limit" value="{{ old('featured_limit', $package->featured_limit) }}" required min="0"
                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
            </div>

            <!-- Contact Unlock Limit -->
            <div>
                <label for="contact_unlock_limit" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Buyer Unlocks / Leads <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="contact_unlock_limit" name="contact_unlock_limit" value="{{ old('contact_unlock_limit', $package->contact_unlock_limit) }}" required min="0"
                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Target Role -->
            <div>
                <label for="target_role" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Target Member Role <span class="text-rose-500">*</span>
                </label>
                <select id="target_role" name="target_role" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
                    <option value="SELLER" {{ old('target_role', $package->target_role) === 'SELLER' ? 'selected' : '' }}>Sellers / Property Owners</option>
                    <option value="BUYER" {{ old('target_role', $package->target_role) === 'BUYER' ? 'selected' : '' }}>Buyers / Investors</option>
                    <option value="ALL" {{ old('target_role', $package->target_role) === 'ALL' ? 'selected' : '' }}>All Members</option>
                </select>
            </div>

            <!-- Active Checkbox -->
            <div class="flex items-center pt-6">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="rounded text-brand-600 focus:ring-brand-500" {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm font-bold text-slate-700">Package is Active</span>
                </label>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                Short Description
            </label>
            <input type="text" id="description" name="description" value="{{ old('description', $package->description) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">
        </div>

        <!-- Features -->
        <div>
            <label for="features" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                Included Features & Perks (One per line)
            </label>
            <textarea id="features" name="features" rows="4"
                      class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-brand-500">{{ old('features', is_array($package->features) ? implode("\n", $package->features) : '') }}</textarea>
            <p class="text-[11px] text-slate-400 mt-1">Enter each highlighted feature on a new line to display as bullet points.</p>
        </div>

        <!-- Buttons -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('admin.packages.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">
                Cancel
            </a>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-500/20 transition">
                Update Package
            </button>
        </div>
    </form>
</div>
@endsection

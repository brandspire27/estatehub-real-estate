@extends('layouts.dashboard')

@section('title', 'My Properties')
@section('page-title', 'Property Listings')
@section('page-subtitle', 'Manage and track your property portfolio status')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action & Status Filter Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        
        <!-- Status Tabs -->
        <div class="flex flex-wrap gap-2 text-xs font-bold">
            <a href="{{ route('listings.index') }}" class="px-3.5 py-2 rounded-xl border transition {{ !request()->filled('status') ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                All ({{ $counts['all'] }})
            </a>
            <a href="{{ route('listings.index', ['status' => 'PUBLISHED']) }}" class="px-3.5 py-2 rounded-xl border transition {{ request('status') === 'PUBLISHED' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Published ({{ $counts['published'] }})
            </a>
            <a href="{{ route('listings.index', ['status' => 'PENDING_VERIFICATION']) }}" class="px-3.5 py-2 rounded-xl border transition {{ request('status') === 'PENDING_VERIFICATION' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Pending ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('listings.index', ['status' => 'DRAFT']) }}" class="px-3.5 py-2 rounded-xl border transition {{ request('status') === 'DRAFT' ? 'bg-slate-700 text-white border-slate-700' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Draft ({{ $counts['draft'] }})
            </a>
            <a href="{{ route('listings.index', ['status' => 'SOLD']) }}" class="px-3.5 py-2 rounded-xl border transition {{ request('status') === 'SOLD' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Sold ({{ $counts['sold'] }})
            </a>
        </div>

        <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition flex-shrink-0">
            <i class="fa-solid fa-plus"></i>
            <span>Add New Property</span>
        </a>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($properties->isEmpty())
            <div class="text-center py-20 p-6">
                <i class="fa-solid fa-house-chimney text-4xl text-slate-300 mb-3 block"></i>
                <h4 class="text-base font-bold text-slate-800">No listings found in this category</h4>
                <p class="text-xs text-slate-500 mt-1 mb-4">Add your properties to connect with verified buyers looking in your area.</p>
                <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white font-bold text-xs">
                    <i class="fa-solid fa-plus"></i>
                    <span>Create Listing</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Property</th>
                            <th class="py-3.5 px-4 font-bold">Type & Specs</th>
                            <th class="py-3.5 px-4 font-bold">Location</th>
                            <th class="py-3.5 px-4 font-bold">Price</th>
                            <th class="py-3.5 px-4 font-bold">Status</th>
                            <th class="py-3.5 px-4 font-bold">Stats</th>
                            <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($properties as $prop)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6 flex items-center gap-3">
                                    <img src="{{ $prop->primary_image_url }}" alt="" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                                    <div>
                                        <a href="{{ route('properties.show', $prop) }}" target="_blank" class="font-bold text-slate-900 hover:text-brand-600 text-xs block truncate max-w-[220px]">
                                            {{ $prop->title }}
                                        </a>
                                        <div class="text-[10px] text-slate-400 mt-0.5">ID: #PROP-{{ str_pad($prop->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-slate-800">{{ $prop->property_type }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $prop->bhk ? $prop->bhk . ' BHK &bull; ' : '' }}{{ number_format($prop->area_sqft) }} sq.ft</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-slate-800">{{ $prop->city }}</div>
                                    <div class="text-[11px] text-slate-500 truncate max-w-[120px]">{{ $prop->area }}</div>
                                </td>
                                <td class="py-4 px-4 font-black text-slate-900 text-sm">
                                    {{ $prop->formatted_price }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black 
                                        {{ $prop->status === 'PUBLISHED' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $prop->status === 'PENDING_VERIFICATION' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $prop->status === 'DRAFT' ? 'bg-slate-200 text-slate-700' : '' }}
                                        {{ $prop->status === 'SOLD' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                        {{ $prop->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    <div class="flex items-center gap-1.5 text-[11px]">
                                        <i class="fa-solid fa-eye text-slate-400"></i>
                                        <span>{{ number_format($prop->views) }} views</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] text-indigo-600 font-bold mt-0.5">
                                        <i class="fa-solid fa-bolt"></i>
                                        <span>{{ $prop->matches->count() }} matches</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('properties.show', $prop) }}" target="_blank" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Preview Live">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                        </a>
                                        <a href="{{ route('listings.edit', $prop) }}" class="p-2 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 transition" title="Edit Listing">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('listings.destroy', $prop) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Delete">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($properties->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $properties->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Admin - Property Moderation')
@section('page-title', 'Property Moderation & Catalog')
@section('page-subtitle', 'Review, verify, and approve property listings across all cities')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2 text-xs font-bold">
            <a href="{{ route('admin.properties.index') }}" class="px-3.5 py-2 rounded-xl border transition {{ !request()->filled('status') ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                All ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.properties.index', ['status' => 'PENDING_VERIFICATION']) }}" class="px-3.5 py-2 rounded-xl border transition {{ request('status') === 'PENDING_VERIFICATION' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Pending Review ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.properties.index', ['status' => 'PUBLISHED']) }}" class="px-3.5 py-2 rounded-xl border transition {{ request('status') === 'PUBLISHED' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Published ({{ $counts['published'] }})
            </a>
            <a href="{{ route('admin.properties.index', ['status' => 'DRAFT']) }}" class="px-3.5 py-2 rounded-xl border transition {{ request('status') === 'DRAFT' ? 'bg-slate-700 text-white border-slate-700' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Draft ({{ $counts['draft'] }})
            </a>
            <a href="{{ route('admin.properties.index', ['status' => 'SOLD']) }}" class="px-3.5 py-2 rounded-xl border transition {{ request('status') === 'SOLD' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Sold ({{ $counts['sold'] }})
            </a>
        </div>
    </div>

    <!-- Properties Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                        <th class="py-3.5 px-6 font-bold">Listing</th>
                        <th class="py-3.5 px-4 font-bold">Seller</th>
                        <th class="py-3.5 px-4 font-bold">City & Locality</th>
                        <th class="py-3.5 px-4 font-bold">Price</th>
                        <th class="py-3.5 px-4 font-bold">Status</th>
                        <th class="py-3.5 px-4 font-bold">Update Status</th>
                        <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($properties as $prop)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 flex items-center gap-3">
                                <img src="{{ $prop->primary_image_url }}" alt="" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                                <div>
                                    <a href="{{ route('properties.show', $prop) }}" target="_blank" class="font-bold text-slate-900 hover:text-brand-600 text-xs block truncate max-w-[200px]">
                                        {{ $prop->title }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">{{ $prop->property_type }} &bull; {{ $prop->bhk ? $prop->bhk . ' BHK' : '' }}</div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-900">{{ $prop->user->name ?? 'Unknown' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $prop->user->phone ?? '' }}</div>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-800">
                                {{ $prop->city }}
                                <div class="text-[10px] text-slate-400 truncate max-w-[120px]">{{ $prop->area }}</div>
                            </td>
                            <td class="py-4 px-4 font-black text-slate-900">
                                {{ $prop->formatted_price }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black 
                                    {{ $prop->status === 'PUBLISHED' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $prop->status === 'PENDING_VERIFICATION' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $prop->status === 'DRAFT' ? 'bg-slate-200 text-slate-700' : '' }}
                                    {{ $prop->status === 'SOLD' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                    {{ $prop->status === 'REMOVED' ? 'bg-rose-100 text-rose-800' : '' }}">
                                    {{ $prop->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($prop->status === 'PENDING_VERIFICATION')
                                        <form action="{{ route('admin.properties.approve', $prop) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] shadow-sm flex items-center gap-1 transition" title="Approve & Publish Live Immediately">
                                                <i class="fa-solid fa-check"></i>
                                                <span>Approve Live</span>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.properties.status', $prop) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf
                                        <select name="status" class="px-2 py-1 rounded-lg border border-slate-200 text-[11px] font-semibold bg-white">
                                            <option value="PUBLISHED" {{ $prop->status === 'PUBLISHED' ? 'selected' : '' }}>Published</option>
                                            <option value="PENDING_VERIFICATION" {{ $prop->status === 'PENDING_VERIFICATION' ? 'selected' : '' }}>Pending</option>
                                            <option value="DRAFT" {{ $prop->status === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                            <option value="SOLD" {{ $prop->status === 'SOLD' ? 'selected' : '' }}>Sold</option>
                                            <option value="REMOVED" {{ $prop->status === 'REMOVED' ? 'selected' : '' }}>Removed</option>
                                        </select>
                                        <button type="submit" class="p-1 px-2 rounded-lg bg-slate-900 text-white font-bold text-[10px] hover:bg-brand-600 transition">
                                            Set
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('listings.edit', $prop) }}" class="p-2 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-600 transition" title="Edit Property Details">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <a href="{{ route('properties.show', $prop) }}" target="_blank" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Preview">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.properties.destroy', $prop) }}" method="POST" onsubmit="return confirm('Permanently delete this property?');" class="inline">
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

        <div class="p-4 border-t border-slate-100">
            {{ $properties->links() }}
        </div>
    </div>

</div>
@endsection

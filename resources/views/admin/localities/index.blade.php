@extends('layouts.dashboard')

@section('title', 'Admin - Locality Management')
@section('page-title', 'Locality Management')
@section('page-subtitle', 'Manage geographic localities and preferred areas for property listings and buyer requirements')

@section('content')
<div class="space-y-6" x-data="{
    showEditModal: false,
    editId: null,
    editCity: '',
    editName: '',
    editIsActive: true,
    openEdit(id, city, name, isActive) {
        this.editId = id;
        this.editCity = city;
        this.editName = name;
        this.editIsActive = isActive;
        this.showEditModal = true;
    }
}">

    <!-- Top Navigation Tabs (Between Cities & Localities) -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 overflow-x-auto">
        <a href="{{ route('admin.cities.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition flex items-center gap-2">
            <i class="fa-solid fa-city text-slate-400"></i>
            <span>Popular Cities & Showcase ({{ $cities->count() }})</span>
        </a>
        <a href="{{ route('admin.localities.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-brand-600 text-white shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-map-location-dot"></i>
            <span>Locality Manager</span>
        </a>
        <a href="{{ route('admin.cities.index') }}"
           class="ml-auto px-3.5 py-1.5 rounded-xl text-xs font-bold text-brand-600 bg-brand-50 hover:bg-brand-100 transition flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
            <span>Upload City Showcase Images</span>
        </a>
    </div>

    <!-- Top Grid: Quick Add Card & Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Add Locality Card -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Add New Locality</h3>
                    <p class="text-[11px] text-slate-500">Add a new area under any city to populate dropdowns across the platform</p>
                </div>
            </div>

            <form action="{{ route('admin.localities.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                @csrf
                <div class="sm:col-span-4">
                    <label for="new_city" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                        Select City <span class="text-rose-500">*</span>
                    </label>
                    <select id="new_city" name="city" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                        <option value="">Select City...</option>
                        @foreach($cities as $c)
                            <option value="{{ $c->name }}" {{ old('city', $selectedCity) === $c->name ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-5">
                    <label for="new_name" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                        Locality Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="new_name" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Sixmile, Beltola, Indiranagar..."
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                    @error('name') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-3">
                    <button type="submit" class="w-full py-2 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Add Locality</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Info / Tip Card (Automatic Dropdown Sync) -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white rounded-3xl p-6 shadow-lg border border-slate-700/80 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 mb-3 shadow-xs">
                    <i class="fa-solid fa-sparkles text-emerald-400"></i>
                    <span>Automatic Dropdown Sync</span>
                </div>
                <h4 class="text-base font-black text-white tracking-tight mb-2">Standardized Geographies</h4>
                <p class="text-xs text-slate-200 leading-relaxed font-normal">
                    Active localities automatically populate the <strong class="text-white font-bold underline decoration-brand-400">Locality / Preferred Area</strong> dropdowns when posting requirements or property listings.
                </p>
            </div>
            <div class="pt-4 mt-4 border-t border-slate-700/80 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-300 font-medium">Total Localities:</span>
                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-black text-xs">{{ $localities->total() }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-300 font-medium">Cities:</span>
                    <span class="px-2.5 py-0.5 rounded-lg bg-brand-500/20 text-brand-300 border border-brand-500/40 font-black text-xs">{{ $cities->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.localities.index') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <div class="w-full sm:w-48">
                <select name="city" onchange="this.form.submit()" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                    <option value="">All Cities ({{ $cities->count() }})</option>
                    @foreach($cities as $c)
                        <option value="{{ $c->name }}" {{ $selectedCity === $c->name ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="relative flex-1 sm:w-56">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search locality name..."
                       class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition">
                Filter
            </button>

            @if($selectedCity || $search)
                <a href="{{ route('admin.localities.index') }}" class="px-2.5 py-1.5 rounded-xl text-xs text-rose-600 hover:bg-rose-50 font-semibold transition">
                    Clear
                </a>
            @endif
        </form>

        <div class="text-xs text-slate-500 font-medium">
            Showing <strong class="text-slate-900">{{ $localities->firstItem() ?? 0 }}-{{ $localities->lastItem() ?? 0 }}</strong> of <strong class="text-slate-900">{{ $localities->total() }}</strong> localities
        </div>
    </div>

    <!-- Localities Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($localities->isEmpty())
            <div class="text-center py-16 p-6 text-slate-400">
                <i class="fa-solid fa-map-location-dot text-4xl mb-3 block text-slate-300"></i>
                <p class="text-sm font-bold text-slate-700">No localities found</p>
                <p class="text-xs text-slate-400 mt-1">Try changing your filters or add a new locality above.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">City</th>
                            <th class="py-3.5 px-6 font-bold">Locality / Area Name</th>
                            <th class="py-3.5 px-4 font-bold text-center">Active Properties</th>
                            <th class="py-3.5 px-4 font-bold">Status</th>
                            <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($localities as $loc)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-city text-brand-600 text-xs"></i>
                                        <span>{{ $loc->city }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 font-semibold text-slate-800">
                                    <span class="text-sm font-bold text-slate-900">{{ $loc->name }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($loc->properties_count > 0)
                                        <a href="{{ route('admin.properties.index', ['city' => $loc->city]) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 hover:underline">
                                            <i class="fa-solid fa-house-circle-check text-[10px]"></i>
                                            <span>{{ $loc->properties_count }} listings</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[11px]">0 listings</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($loc->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" 
                                                @click="openEdit({{ $loc->id }}, '{{ addslashes($loc->city) }}', '{{ addslashes($loc->name) }}', {{ $loc->is_active ? 'true' : 'false' }})"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-[11px] transition">
                                            <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="{{ route('admin.localities.destroy', $loc) }}" method="POST" onsubmit="return confirm('Delete locality \'{{ addslashes($loc->name) }}\'?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 text-[11px] transition" title="Delete Locality">
                                                <i class="fa-regular fa-trash-can"></i>
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
                {{ $localities->links() }}
            </div>
        @endif
    </div>

    <!-- Edit Locality Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div @click="showEditModal = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

        <!-- Dialog Box -->
        <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 overflow-hidden z-10 text-slate-800">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Edit Locality</h3>
                </div>
                <button @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="'{{ url('admin/localities') }}/' + editId" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">City</label>
                    <select name="city" x-model="editCity" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                        @foreach($cities as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Locality Name</label>
                    <input type="text" name="name" x-model="editName" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" :checked="editIsActive"
                           @change="editIsActive = $event.target.checked"
                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <label for="edit_is_active" class="text-xs font-semibold text-slate-700">Active (visible in dropdowns)</label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

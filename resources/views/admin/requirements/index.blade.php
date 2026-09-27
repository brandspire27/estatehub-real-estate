@extends('layouts.dashboard')

@section('title', 'Admin - Requirements Management')
@section('page-title', 'Buyer Requirements Oversight')
@section('page-subtitle', 'Monitor buyer criteria across cities and trigger automated rematching')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form action="{{ route('admin.requirements.index') }}" method="GET" class="flex flex-wrap items-end gap-4 text-xs font-semibold">
            <div>
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Status</label>
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">All Statuses</option>
                    <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                    <option value="MATCHED" {{ request('status') === 'MATCHED' ? 'selected' : '' }}>Matched</option>
                    <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">City</label>
                <input type="text" name="city" value="{{ request('city') }}" placeholder="City name..." 
                       class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
            </div>

            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-brand-600 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Requirements Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                        <th class="py-3.5 px-6 font-bold">Buyer</th>
                        <th class="py-3.5 px-4 font-bold">Target Criteria</th>
                        <th class="py-3.5 px-4 font-bold">City</th>
                        <th class="py-3.5 px-4 font-bold">Budget</th>
                        <th class="py-3.5 px-4 font-bold">Status</th>
                        <th class="py-3.5 px-4 font-bold">Matches</th>
                        <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($requirements as $req)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $req->user->name ?? 'Buyer' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $req->user->phone ?? '' }} &bull; {{ $req->user->email ?? '' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-900">{{ $req->bhk ? $req->bhk . ' BHK ' : '' }}{{ $req->property_type }}</div>
                                <div class="text-[10px] text-slate-400">Possession: {{ $req->possession_status ?: 'Any' }}</div>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-800">{{ $req->city }}</td>
                            <td class="py-4 px-4 font-black text-slate-900">{{ $req->formatted_budget }}</td>
                            <td class="py-4 px-4">
                                <form action="{{ route('admin.requirements.status', $req) }}" method="POST" class="inline-flex items-center gap-1">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 rounded-lg border border-slate-200 text-[11px] font-bold">
                                        <option value="ACTIVE" {{ $req->status === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                        <option value="MATCHED" {{ $req->status === 'MATCHED' ? 'selected' : '' }}>Matched</option>
                                        <option value="CLOSED" {{ $req->status === 'CLOSED' ? 'selected' : '' }}>Closed</option>
                                        <option value="EXPIRED" {{ $req->status === 'EXPIRED' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-4 px-4 font-bold text-indigo-600">
                                <a href="{{ route('requirements.matches', $req) }}" target="_blank" class="hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-bolt text-amber-500"></i>
                                    <span>{{ $req->matches->count() }} matches</span>
                                </a>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <form action="{{ route('admin.requirements.rematch', $req) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs transition" title="Re-calculate Matches">
                                        <i class="fa-solid fa-rotate mr-1"></i> Re-Match
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $requirements->links() }}
        </div>
    </div>

</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'My Requirements')
@section('page-title', 'Buyer Requirements')
@section('page-subtitle', 'Active requirements automatically generate real-time matches against published properties')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Row -->
    <div class="flex items-center justify-between">
        <div class="text-xs font-semibold text-slate-500">
            Total Requirements: <span class="font-bold text-slate-900">{{ $requirements->total() }}</span>
        </div>

        <a href="{{ route('requirements.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Post New Requirement</span>
        </a>
    </div>

    <!-- Requirements List -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($requirements->isEmpty())
            <div class="text-center py-20 p-6">
                <i class="fa-solid fa-clipboard-list text-4xl text-slate-300 mb-3 block"></i>
                <h4 class="text-base font-bold text-slate-800">No buyer requirements found</h4>
                <p class="text-xs text-slate-500 mt-1 mb-4">Post what you are looking for (city, budget, BHK) and our algorithm will find matched properties.</p>
                <a href="{{ route('requirements.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white font-bold text-xs">
                    <i class="fa-solid fa-plus"></i>
                    <span>Post Requirement</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Requirement</th>
                            <th class="py-3.5 px-4 font-bold">Location</th>
                            <th class="py-3.5 px-4 font-bold">Budget Range</th>
                            <th class="py-3.5 px-4 font-bold">Possession</th>
                            <th class="py-3.5 px-4 font-bold">Status</th>
                            <th class="py-3.5 px-4 font-bold">Matches Found</th>
                            <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($requirements as $req)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900 text-sm">
                                        {{ $req->bhk ? $req->bhk . ' BHK ' : '' }}{{ $req->property_type }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Posted {{ $req->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="py-4 px-4 font-semibold text-slate-800">
                                    <i class="fa-solid fa-location-dot text-brand-600 mr-1"></i>
                                    @if($req->locality)
                                        <span class="text-slate-900 font-bold">{{ $req->locality }}</span>, 
                                    @endif
                                    {{ $req->city }}
                                </td>
                                <td class="py-4 px-4 font-black text-slate-900 text-sm">
                                    {{ $req->formatted_budget }}
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    {{ $req->possession_status ?: 'Any' }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black 
                                        {{ $req->status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $req->status === 'MATCHED' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                        {{ $req->status === 'CLOSED' ? 'bg-slate-200 text-slate-700' : '' }}
                                        {{ $req->status === 'EXPIRED' ? 'bg-rose-100 text-rose-800' : '' }}">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <a href="{{ route('requirements.matches', $req) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold transition">
                                        <i class="fa-solid fa-bolt text-amber-500"></i>
                                        <span>{{ $req->matches_count }} Matches</span>
                                        <i class="fa-solid fa-chevron-right text-[10px] text-amber-600"></i>
                                    </a>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('requirements.matches', $req) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="View Matches">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('requirements.edit', $req) }}" class="p-2 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 transition" title="Edit Requirement">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('requirements.destroy', $req) }}" method="POST" onsubmit="return confirm('Delete this requirement?');" class="inline">
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

            @if($requirements->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $requirements->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

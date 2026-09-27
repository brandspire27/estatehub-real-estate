@extends('layouts.dashboard')

@section('title', 'Admin - Audit Trail')
@section('page-title', 'System Audit Trail')
@section('page-subtitle', 'Immutable chronological log of administrative, staff, and user operations')

@section('content')
<div class="space-y-6">
    
    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form action="{{ route('admin.logs.index') }}" method="GET" class="flex flex-wrap items-end gap-4 text-xs font-semibold">
            <div>
                <label class="block text-slate-500 uppercase tracking-wider mb-1.5 font-bold">Action Pattern</label>
                <input type="text" name="action" value="{{ request('action') }}" placeholder="e.g. PROPERTY, STAFF, USER..." 
                       class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
            </div>

            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-brand-600 transition">
                Filter Logs
            </button>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($logs->isEmpty())
            <div class="text-center py-20 p-6 text-slate-400 text-xs">
                No activity records found.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Timestamp</th>
                            <th class="py-3.5 px-4 font-bold">Action</th>
                            <th class="py-3.5 px-4 font-bold">Actor</th>
                            <th class="py-3.5 px-4 font-bold">Target Entity</th>
                            <th class="py-3.5 px-6 font-bold">Details / Payload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($logs as $l)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-6 text-slate-400 font-mono">{{ $l->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="py-3.5 px-4 font-bold font-mono text-brand-700">{{ $l->action }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900">{{ $l->user->name ?? 'System' }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $l->user->role ?? '' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $l->entity_type ?: '–' }} {{ $l->entity_id ? '#' . $l->entity_id : '' }}
                                </td>
                                <td class="py-3.5 px-6 font-mono text-[11px] text-slate-500 max-w-xs truncate">
                                    {{ $l->metadata ? json_encode($l->metadata) : '–' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

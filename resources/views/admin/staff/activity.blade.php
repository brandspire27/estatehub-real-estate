@extends('layouts.dashboard')

@section('title', 'Staff Activity — ' . $staff->name)
@section('page-title', 'Staff Activity Audit: ' . $staff->name)
@section('page-subtitle', 'Chronological log of client onboarding, listings, and requirements posted by this staff member')

@section('content')
<div class="space-y-6">
    
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs text-slate-500 font-bold">Staff Member</span>
            <div class="text-base font-extrabold text-slate-900">{{ $staff->name }} ({{ $staff->email }})</div>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">
            &larr; Back to Staff Directory
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($logs->isEmpty())
            <div class="text-center py-16 text-slate-400 text-xs">
                No recorded activity for this staff member yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3.5 px-6 font-bold">Timestamp</th>
                            <th class="py-3.5 px-4 font-bold">Action</th>
                            <th class="py-3.5 px-4 font-bold">Entity Type</th>
                            <th class="py-3.5 px-4 font-bold">Entity ID</th>
                            <th class="py-3.5 px-6 font-bold">Metadata Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($logs as $log)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-6 text-slate-400 font-mono">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="py-3.5 px-4 font-bold text-brand-700 font-mono">{{ $log->action }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $log->entity_type ?: '–' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">#{{ $log->entity_id ?: '–' }}</td>
                                <td class="py-3.5 px-6 text-slate-500 font-mono text-[11px]">
                                    {{ $log->metadata ? json_encode($log->metadata) : '–' }}
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

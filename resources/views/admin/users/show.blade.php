@extends('layouts.dashboard')

@section('title', 'User Profile — ' . $user->name)
@section('page-title', 'User Profile: ' . $user->name)
@section('page-subtitle', 'Member since ' . $user->created_at->format('F d, Y'))

@section('content')
<div class="space-y-8">
    
    <!-- User Overview Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-bold text-2xl uppercase shadow-md">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-slate-900">{{ $user->name }}</h3>
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mt-1">
                    <span>{{ $user->email }}</span>
                    &bull;
                    <span>{{ $user->phone }}</span>
                    &bull;
                    <span class="px-2 py-0.5 rounded-full font-bold uppercase text-[10px] bg-brand-100 text-brand-800">{{ $user->role }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($user->id !== Auth::id())
                <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $user->is_active ? 'bg-rose-50 text-rose-600 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        {{ $user->is_active ? 'Deactivate Account' : 'Reactivate Account' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Properties or Requirements depending on role -->
    @if($user->isSeller())
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Properties Listed by {{ $user->name }} ({{ $user->properties->count() }})</h3>
            @if($user->properties->isEmpty())
                <p class="text-xs text-slate-400">No properties listed yet.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($user->properties as $prop)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                            <img src="{{ $prop->primary_image_url }}" alt="" class="w-12 h-12 rounded-xl object-cover">
                            <div class="min-w-0">
                                <a href="{{ route('properties.show', $prop) }}" target="_blank" class="font-bold text-slate-900 text-xs truncate hover:text-brand-600 block">
                                    {{ $prop->title }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $prop->formatted_price }} &bull; {{ $prop->status }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if($user->isBuyer())
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Requirements Posted by {{ $user->name }} ({{ $user->requirements->count() }})</h3>
            @if($user->requirements->isEmpty())
                <p class="text-xs text-slate-400">No requirements posted yet.</p>
            @else
                <div class="space-y-2">
                    @foreach($user->requirements as $req)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900">{{ $req->bhk ? $req->bhk . ' BHK ' : '' }}{{ $req->property_type }}</span> in <span class="font-semibold text-brand-600">{{ $req->city }}</span>
                                <div class="text-[11px] text-slate-500">Budget: {{ $req->formatted_budget }} &bull; Status: {{ $req->status }}</div>
                            </div>
                            <span class="font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">
                                {{ $req->matches->count() }} Matches
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Staff Desk')
@section('page-title', 'Staff Operations Desk')
@section('page-subtitle', 'Manage client onboarding, property listings, and requirements')

@section('content')
<div class="space-y-8">
    
    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['clients_created'] }}</div>
                <div class="text-xs font-semibold text-slate-500">Clients Registered by You</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['properties_created'] }}</div>
                <div class="text-xs font-semibold text-slate-500">Properties Created</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['requirements_created'] }}</div>
                <div class="text-xs font-semibold text-slate-500">Requirements Posted</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_platform_matches'] }}</div>
                <div class="text-xs font-semibold text-slate-500">Platform Matches</div>
            </div>
        </div>
    </div>

    <!-- Quick Operations Action Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <a href="{{ route('staff.users.create') }}" class="group bg-white rounded-2xl p-6 border border-slate-200 hover:border-brand-500 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-brand-600 group-hover:text-white flex items-center justify-center text-xl transition">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 text-sm">Register New Client</h4>
                <p class="text-xs text-slate-500 mt-0.5">Add Buyer or Seller account</p>
            </div>
        </a>

        <a href="{{ route('listings.create') }}" class="group bg-white rounded-2xl p-6 border border-slate-200 hover:border-brand-500 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white flex items-center justify-center text-xl transition">
                <i class="fa-solid fa-house-medical"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 text-sm">Post Property for Seller</h4>
                <p class="text-xs text-slate-500 mt-0.5">Publish listing on client's behalf</p>
            </div>
        </a>

        <a href="{{ route('requirements.create') }}" class="group bg-white rounded-2xl p-6 border border-slate-200 hover:border-brand-500 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-brand-600 group-hover:text-white flex items-center justify-center text-xl transition">
                <i class="fa-solid fa-file-circle-plus"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 text-sm">Post Buyer Requirement</h4>
                <p class="text-xs text-slate-500 mt-0.5">Submit buyer demand criteria</p>
            </div>
        </a>
    <!-- Pending Verification Properties Moderation -->
    @if(isset($pendingProperties) && $pendingProperties->isNotEmpty())
        <div class="bg-white rounded-3xl border border-amber-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-amber-100 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="font-bold text-slate-900 text-base">Pending Verification Listings</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                        {{ $pendingProperties->count() }} Listings Awaiting Approval
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-3 font-bold">Property</th>
                            <th class="pb-3 font-bold">Seller</th>
                            <th class="pb-3 font-bold">City</th>
                            <th class="pb-3 font-bold">Price</th>
                            <th class="pb-3 font-bold">Submitted</th>
                            <th class="pb-3 font-bold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($pendingProperties as $prop)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 font-bold text-slate-900 flex items-center gap-2">
                                    <img src="{{ $prop->primary_image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                    <a href="{{ route('properties.show', $prop) }}" target="_blank" class="hover:text-brand-600 truncate max-w-xs">
                                        {{ $prop->title }}
                                    </a>
                                </td>
                                <td class="py-3 text-slate-600">{{ $prop->user->name ?? 'Unknown' }}</td>
                                <td class="py-3 text-slate-600">{{ $prop->city }}</td>
                                <td class="py-3 font-bold text-slate-900">{{ $prop->formatted_price }}</td>
                                <td class="py-3 text-slate-400">{{ $prop->created_at->diffForHumans() }}</td>
                                <td class="py-3 text-right">
                                    <form action="{{ route('staff.properties.approve', $prop) }}" method="POST" class="inline-flex gap-1.5">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-sm flex items-center gap-1.5 transition">
                                            <i class="fa-solid fa-check"></i>
                                            <span>Approve & Publish</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Recent Clients Created -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-base">Recently Added Clients</h3>
            <a href="{{ route('staff.users') }}" class="text-xs font-bold text-brand-600 hover:underline">View All &rarr;</a>
        </div>

        @if($recentClients->isEmpty())
            <div class="text-center py-10 text-slate-400 text-xs">
                No clients created through your staff account yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-3 font-bold">Client Name</th>
                            <th class="pb-3 font-bold">Email</th>
                            <th class="pb-3 font-bold">Phone</th>
                            <th class="pb-3 font-bold">Role</th>
                            <th class="pb-3 font-bold">Date Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($recentClients as $client)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 font-bold text-slate-900">{{ $client->name }}</td>
                                <td class="py-3 text-slate-600">{{ $client->email }}</td>
                                <td class="py-3 text-slate-600">{{ $client->phone }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full font-bold {{ $client->role === 'BUYER' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $client->role }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-500">{{ $client->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

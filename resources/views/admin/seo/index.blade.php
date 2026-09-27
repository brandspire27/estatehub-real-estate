@extends('layouts.dashboard')

@section('title', 'Admin - SEO & Metadata Manager')
@section('page-title', 'Search Engine Optimization & Metadata Suite')
@section('page-subtitle', 'Manage SERP titles, meta descriptions, OpenGraph tags, robots directives, and dynamic sitemaps')

@section('content')
<div class="space-y-6">

    <!-- KPI Statistics Ribbon -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Managed Pages</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_managed_pages'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Indexed (Search Ready)</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['indexed_pages'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">NoIndex Directive</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['noindex_pages'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-[11px] font-semibold text-brand-600 uppercase tracking-wider">Listings in Sitemap</div>
            <div class="text-2xl font-black text-brand-600 mt-1">{{ $stats['published_properties_in_sitemap'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <div class="text-[11px] font-semibold text-purple-600 uppercase tracking-wider">Metros in Sitemap</div>
            <div class="text-2xl font-black text-purple-600 mt-1">{{ $stats['cities_in_sitemap'] }}</div>
        </div>
    </div>

    <!-- Quick Tool Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                <i class="fa-solid fa-sitemap text-brand-600"></i>
                <span>Live Sitemap.xml</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
            </a>

            <a href="{{ url('/llms.txt') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                <i class="fa-solid fa-robot text-purple-600"></i>
                <span>AI /llms.txt</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
            </a>

            <a href="{{ url('/llms-full.txt') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                <i class="fa-solid fa-brain text-indigo-600"></i>
                <span>AI /llms-full.txt</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
            </a>

            <a href="{{ url('/robots.txt') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                <i class="fa-solid fa-file-lines text-emerald-600"></i>
                <span>Robots.txt</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
            </a>
        </div>

        <a href="{{ route('admin.seo.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-xs shadow-md shadow-brand-500/20 transition flex-shrink-0">
            <i class="fa-solid fa-plus"></i>
            <span>Add Custom Page SEO</span>
        </a>
    </div>

    <!-- SEO Managed Pages Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                        <th class="py-3.5 px-6 font-bold">Page & Route Key</th>
                        <th class="py-3.5 px-4 font-bold">Google SERP Snippet Preview</th>
                        <th class="py-3.5 px-4 font-bold">Robots Directive</th>
                        <th class="py-3.5 px-4 font-bold">Social Card</th>
                        <th class="py-3.5 px-4 font-bold">Schema (JSON-LD)</th>
                        <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($seoPages as $page)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 text-sm">{{ $page->page_name }}</div>
                                <div class="font-mono text-[10px] text-brand-600 mt-0.5">{{ $page->page_key }}</div>
                                @if($page->canonical_url)
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5" title="{{ $page->canonical_url }}">
                                        {{ $page->canonical_url }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-4 px-4 max-w-md">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1">
                                    <div class="text-xs font-bold text-indigo-700 truncate" title="{{ $page->meta_title }}">
                                        {{ $page->meta_title ?? 'Default Platform Title' }}
                                    </div>
                                    <div class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed" title="{{ $page->meta_description }}">
                                        {{ $page->meta_description ?? 'No custom meta description configured.' }}
                                    </div>
                                    <div class="flex items-center gap-3 text-[10px] text-slate-400 pt-0.5">
                                        <span>Title: {{ strlen($page->meta_title ?? '') }} chars</span>
                                        <span>&bull;</span>
                                        <span>Desc: {{ strlen($page->meta_description ?? '') }} chars</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-4">
                                @if($page->is_indexed)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                        <span>Index, Follow</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-ban text-[9px]"></i>
                                        <span>NoIndex</span>
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-4">
                                @if($page->og_image)
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $page->og_image }}" alt="" class="w-10 h-7 rounded object-cover border border-slate-200 flex-shrink-0">
                                        <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $page->twitter_card }}</span>
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400">Default Meta</span>
                                @endif
                            </td>

                            <td class="py-4 px-4">
                                @if(!empty($page->json_ld))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="fa-solid fa-code text-[9px] mr-0.5"></i> Custom JSON-LD
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400">Auto Generated</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.seo.edit', $page) }}" class="px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit SEO</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $seoPages->links() }}
        </div>
    </div>

</div>
@endsection

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\City;
use App\Models\Property;
use App\Models\SeoMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSeoController extends Controller
{
    public function index(): View
    {
        $seoPages = SeoMeta::latest()->paginate(15);

        $stats = [
            'total_managed_pages' => SeoMeta::count(),
            'indexed_pages' => SeoMeta::where('is_indexed', true)->count(),
            'noindex_pages' => SeoMeta::where('is_indexed', false)->count(),
            'published_properties_in_sitemap' => Property::published()->count(),
            'cities_in_sitemap' => City::count(),
        ];

        return view('admin.seo.index', compact('seoPages', 'stats'));
    }

    public function create(): View
    {
        return view('admin.seo.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_key' => 'required|string|max:80|unique:seo_metas,page_key',
            'page_name' => 'required|string|max:120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'is_indexed' => 'nullable|boolean',
            'is_followed' => 'nullable|boolean',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:1000',
            'og_image' => 'nullable|string|max:500',
            'og_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'twitter_card' => 'nullable|in:summary,summary_large_image',
            'json_ld' => 'nullable|string',
        ]);

        $validated['page_key'] = Str::slug($validated['page_key']);
        $validated['is_indexed'] = $request->boolean('is_indexed', true);
        $validated['is_followed'] = $request->boolean('is_followed', true);

        if ($request->hasFile('og_image_file')) {
            $path = $request->file('og_image_file')->store('seo', 'public');
            $validated['og_image'] = asset('storage/' . $path);
        }
        unset($validated['og_image_file']);

        // Validate JSON if provided
        if (!empty($validated['json_ld'])) {
            json_decode($validated['json_ld']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['json_ld' => 'The structured data must be a valid JSON-LD string.']);
            }
        }

        $seo = SeoMeta::create($validated);

        ActivityLog::record(Auth::id(), 'ADMIN_CREATED_SEO_PAGE', 'SeoMeta', $seo->id, [
            'page_key' => $seo->page_key,
            'page_name' => $seo->page_name,
        ]);

        return redirect()->route('admin.seo.index')->with('success', "SEO metadata configured for '{$seo->page_name}'.");
    }

    public function edit($seo): View
    {
        $seoMeta = $seo instanceof SeoMeta ? $seo : SeoMeta::findOrFail($seo);
        return view('admin.seo.edit', compact('seoMeta'));
    }

    public function update(Request $request, $seo): RedirectResponse
    {
        $seoMeta = $seo instanceof SeoMeta ? $seo : SeoMeta::findOrFail($seo);

        $validated = $request->validate([
            'page_name' => 'required|string|max:120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'is_indexed' => 'nullable|boolean',
            'is_followed' => 'nullable|boolean',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:1000',
            'og_image' => 'nullable|string|max:500',
            'og_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'twitter_card' => 'nullable|in:summary,summary_large_image',
            'json_ld' => 'nullable|string',
        ]);

        $validated['is_indexed'] = $request->boolean('is_indexed');
        $validated['is_followed'] = $request->boolean('is_followed');

        if ($request->hasFile('og_image_file')) {
            $path = $request->file('og_image_file')->store('seo', 'public');
            $validated['og_image'] = asset('storage/' . $path);
        } elseif ($request->boolean('remove_og_image')) {
            $validated['og_image'] = null;
        }
        unset($validated['og_image_file']);

        // Validate JSON if provided
        if (!empty($validated['json_ld'])) {
            json_decode($validated['json_ld']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['json_ld' => 'The structured data must be a valid JSON-LD string.']);
            }
        }

        $seoMeta->update($validated);

        ActivityLog::record(Auth::id(), 'ADMIN_UPDATED_SEO_PAGE', 'SeoMeta', $seoMeta->id, [
            'page_key' => $seoMeta->page_key,
            'page_name' => $seoMeta->page_name,
        ]);

        return redirect()->route('admin.seo.index')->with('success', "SEO settings updated for '{$seoMeta->page_name}'.");
    }

    public function destroy($seo): RedirectResponse
    {
        $seoMeta = $seo instanceof SeoMeta ? $seo : SeoMeta::findOrFail($seo);
        $pageName = $seoMeta->page_name;
        $seoMeta->delete();

        ActivityLog::record(Auth::id(), 'ADMIN_DELETED_SEO_PAGE', 'SeoMeta', null, [
            'page_name' => $pageName,
        ]);

        return redirect()->route('admin.seo.index')->with('success', "SEO record for '{$pageName}' deleted.");
    }
}

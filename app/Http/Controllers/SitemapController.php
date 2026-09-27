<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Property;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap conforming to sitemaps.org standard.
     */
    public function index(): Response
    {
        $baseUrl = url('/');

        // Core Static Pages
        $staticPages = [
            [
                'loc' => $baseUrl . '/',
                'lastmod' => now()->toIso8601String(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => $baseUrl . '/properties',
                'lastmod' => now()->toIso8601String(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => $baseUrl . '/about',
                'lastmod' => now()->subDays(5)->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => $baseUrl . '/contact',
                'lastmod' => now()->subDays(5)->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
        ];

        // Active Cities Filter Pages
        $cityPages = City::orderBy('name')->get()->map(function ($city) use ($baseUrl) {
            return [
                'loc' => $baseUrl . '/properties?city=' . urlencode($city->name),
                'lastmod' => now()->toIso8601String(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];
        })->toArray();

        // Published Properties
        $properties = Property::published()
            ->with(['images'])
            ->latest('updated_at')
            ->get()
            ->map(function ($prop) use ($baseUrl) {
                return [
                    'loc' => $prop->url,
                    'lastmod' => $prop->updated_at->toIso8601String(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                    'image' => $prop->primary_image_url,
                    'title' => $prop->title,
                ];
            })->toArray();

        $content = view('sitemap.xml', compact('staticPages', 'cityPages', 'properties'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex', // Don't index the sitemap XML itself in SERP
        ]);
    }
}

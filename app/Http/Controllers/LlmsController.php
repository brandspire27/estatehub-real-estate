<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Locality;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Response;

class LlmsController extends Controller
{
    /**
     * Standard /llms.txt markdown document for AI agents and LLM search systems.
     */
    public function index(): Response
    {
        $baseUrl = url('/');
        $totalListings = Property::published()->count();
        $totalCities = City::count();
        $topCities = City::withCount(['properties' => fn($q) => $q->published()])
            ->orderByDesc('properties_count')
            ->take(8)
            ->get();

        $content = view('llms.index', compact('baseUrl', 'totalListings', 'totalCities', 'topCities'))->render();

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    /**
     * Extended /llms-full.txt markdown document with complete published property catalog for LLM deep context.
     */
    public function full(): Response
    {
        $baseUrl = url('/');
        $properties = Property::published()
            ->with(['user', 'images'])
            ->latest()
            ->get();

        $content = view('llms.full', compact('baseUrl', 'properties'))->render();

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AboutPageSetting;
use App\Models\City;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Platform statistics
        $stats = [
            'properties_count' => Property::where('status', 'PUBLISHED')->count(),
            'buyers_count' => User::where('role', 'BUYER')->count(),
            'sellers_count' => User::where('role', 'SELLER')->count(),
            'cities_count' => City::count(),
        ];

        // Popular cities with property count (Respect is_popular & sort_order configured by admin)
        $cities = City::where('is_popular', true)
            ->withCount(['properties' => function ($query) {
                $query->where('status', 'PUBLISHED');
            }])
            ->orderBy('sort_order')
            ->orderByDesc('properties_count')
            ->take(12)
            ->get();

        if ($cities->isEmpty()) {
            $cities = City::withCount(['properties' => function ($query) {
                $query->where('status', 'PUBLISHED');
            }])->orderByDesc('properties_count')->take(12)->get();
        }

        // Featured published properties
        $featuredProperties = Property::with(['images', 'user'])
            ->where('status', 'PUBLISHED')
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('stats', 'cities', 'featuredProperties'));
    }

    public function about(): View
    {
        $about = AboutPageSetting::getSettings();

        return view('pages.about', compact('about'));
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'message' => 'required|string|min:10',
        ]);

        return back()->with('success', 'Thank you for reaching out! Our real estate advisory team will contact you shortly.');
    }

    public function getLocalities(Request $request, string $city): \Illuminate\Http\JsonResponse
    {
        $localities = \App\Models\Locality::where('city', $city)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name');

        return response()->json($localities);
    }
}

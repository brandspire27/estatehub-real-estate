<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\City;
use App\Models\MatchRecord;
use App\Services\MatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminMatchController extends Controller
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index(Request $request): View
    {
        $query = MatchRecord::with([
            'requirement.user',
            'property.user',
            'property.images',
        ])->orderByDesc('score');

        if ($request->filled('city')) {
            $city = $request->city;
            $query->whereHas('property', fn($q) => $q->where('city', $city));
        }

        if ($request->filled('property_type')) {
            $type = $request->property_type;
            $query->whereHas('property', fn($q) => $q->where('property_type', $type));
        }

        if ($request->filled('min_score')) {
            $query->where('score', '>=', (int)$request->min_score);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('requirement.user', fn($sub) => $sub->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('property.user', fn($sub) => $sub->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('property', fn($sub) => $sub->where('title', 'like', "%{$s}%"));
            });
        }

        $matches = $query->paginate(15)->withQueryString();
        $cities = City::orderBy('name')->pluck('name');

        return view('admin.matches.index', compact('matches', 'cities'));
    }

    public function recalculateAll(): RedirectResponse
    {
        $count = $this->matchingService->recalculateAll();

        ActivityLog::record(Auth::id(), 'ADMIN_RECALCULATED_ALL_MATCHES', null, null, [
            'total_matches_generated' => $count,
        ]);

        return back()->with('success', "Full system re-matching complete! {$count} total matches generated/updated.");
    }
}

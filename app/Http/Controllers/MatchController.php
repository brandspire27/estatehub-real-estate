<?php

namespace App\Http\Controllers;

use App\Models\MatchRecord;
use App\Models\Property;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        if ($user->isSeller()) {
            return $this->sellerMatches($request);
        }

        return $this->buyerMatches($request);
    }

    private function buyerMatches(Request $request): View
    {
        $user = Auth::user();

        $query = MatchRecord::whereHas('requirement', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with(['property.images', 'property.user', 'requirement'])
            ->orderByDesc('score');

        if ($request->filled('requirement_id')) {
            $query->where('requirement_id', $request->requirement_id);
        }

        if ($request->filled('min_score')) {
            $query->where('score', '>=', (int)$request->min_score);
        }

        $matches = $query->paginate(12)->withQueryString();
        $requirements = Requirement::where('user_id', $user->id)->get();

        return view('matches.buyer', compact('matches', 'requirements'));
    }

    private function sellerMatches(Request $request): View
    {
        $user = Auth::user();

        $query = MatchRecord::whereHas('property', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with(['requirement.user', 'property.images'])
            ->orderByDesc('score');

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('min_score')) {
            $query->where('score', '>=', (int)$request->min_score);
        }

        $matches = $query->paginate(12)->withQueryString();
        $properties = Property::where('user_id', $user->id)->get();

        return view('matches.seller', compact('matches', 'properties'));
    }
}

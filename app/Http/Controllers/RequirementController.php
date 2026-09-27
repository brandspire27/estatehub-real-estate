<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequirementRequest;
use App\Models\ActivityLog;
use App\Models\City;
use App\Models\Locality;
use App\Models\Requirement;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RequirementController extends Controller
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Requirement::with(['user', 'matches.property'])->latest();

        if ($user->canAccessStaff()) {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requirements = $query->paginate(10)->withQueryString();

        return view('requirements.index', compact('requirements'));
    }

    public function create(): View
    {
        $cities = City::orderBy('name')->pluck('name');
        $propertyTypes = ['Flat', 'Apartment', 'Villa', 'Plot', 'Building', 'Commercial Space', 'Land'];
        $buyers = [];

        if (Auth::user()->canAccessStaff()) {
            $buyers = User::where('role', 'BUYER')->orderBy('name')->get();
        }

        $localitiesByCity = Locality::active()->orderBy('name')->get()->groupBy('city');

        return view('requirements.create', compact('cities', 'propertyTypes', 'buyers', 'localitiesByCity'));
    }

    public function store(StoreRequirementRequest $request): RedirectResponse
    {
        $authUser = Auth::user();
        $ownerId = $authUser->id;

        if ($authUser->canAccessStaff() && $request->filled('target_user_id')) {
            $ownerId = (int)$request->target_user_id;
        }

        $locality = $request->filled('locality') ? trim($request->locality) : null;
        if ($locality && strcasecmp($locality, trim($request->city)) === 0) {
            $locality = null;
        }

        $requirement = Requirement::create([
            'user_id' => $ownerId,
            'created_by_user_id' => $authUser->id,
            'property_type' => $request->property_type,
            'bhk' => $request->bhk,
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'possession_status' => $request->possession_status ?: 'Any',
            'city' => $request->city,
            'locality' => $locality,
            'description' => $request->description,
            'status' => $request->input('status', 'ACTIVE'),
        ]);

        // Run matching algorithm
        $matches = $this->matchingService->matchRequirement($requirement);
        $matchCount = $matches->count();

        ActivityLog::record($authUser->id, 'REQUIREMENT_CREATED', 'Requirement', $requirement->id, [
            'matches_found' => $matchCount,
            'city' => $requirement->city,
            'locality' => $requirement->locality,
            'type' => $requirement->property_type,
        ]);

        $message = "Requirement posted! We found {$matchCount} matching properties for you.";
        return redirect()->route('requirements.matches', $requirement)->with('success', $message);
    }

    public function edit(Requirement $requirement): View
    {
        $this->authorizeOwnerOrStaff($requirement);

        $cities = City::orderBy('name')->pluck('name');
        $propertyTypes = ['Flat', 'Apartment', 'Villa', 'Plot', 'Building', 'Commercial Space', 'Land'];
        $localitiesByCity = Locality::active()->orderBy('name')->get()->groupBy('city');

        return view('requirements.edit', compact('requirement', 'cities', 'propertyTypes', 'localitiesByCity'));
    }

    public function update(StoreRequirementRequest $request, Requirement $requirement): RedirectResponse
    {
        $this->authorizeOwnerOrStaff($requirement);
        $authUser = Auth::user();

        $locality = $request->filled('locality') ? trim($request->locality) : null;
        if ($locality && strcasecmp($locality, trim($request->city)) === 0) {
            $locality = null;
        }

        $requirement->update([
            'property_type' => $request->property_type,
            'bhk' => $request->bhk,
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'possession_status' => $request->possession_status ?: 'Any',
            'city' => $request->city,
            'locality' => $locality,
            'description' => $request->description,
            'status' => $request->input('status', $requirement->status),
        ]);

        // Re-run matching
        $matches = $this->matchingService->matchRequirement($requirement);

        ActivityLog::record($authUser->id, 'REQUIREMENT_UPDATED', 'Requirement', $requirement->id, [
            'matches_found' => $matches->count(),
        ]);

        return redirect()->route('requirements.index')->with('success', 'Requirement updated successfully.');
    }

    public function destroy(Requirement $requirement): RedirectResponse
    {
        $this->authorizeOwnerOrStaff($requirement);

        $requirement->delete();

        ActivityLog::record(Auth::id(), 'REQUIREMENT_DELETED', 'Requirement', $requirement->id);

        return redirect()->route('requirements.index')->with('success', 'Requirement deleted.');
    }

    public function matches(Requirement $requirement): View
    {
        $this->authorizeOwnerOrStaff($requirement);

        // Always re-sync matches to ensure latest scores and newly published properties are evaluated
        $this->matchingService->matchRequirement($requirement);

        $matches = $requirement->matches()
            ->with(['property.images', 'property.user'])
            ->orderByDesc('score')
            ->paginate(12);

        return view('requirements.matches', compact('requirement', 'matches'));
    }

    private function authorizeOwnerOrStaff(Requirement $requirement): void
    {
        $user = Auth::user();
        if (!$user->canAccessStaff() && $user->id !== $requirement->user_id) {
            abort(403, 'Unauthorized access to this requirement.');
        }
    }
}

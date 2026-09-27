<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Requirement;
use App\Services\MatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminRequirementController extends Controller
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index(Request $request): View
    {
        $query = Requirement::with(['user', 'matches'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        $requirements = $query->paginate(15)->withQueryString();

        return view('admin.requirements.index', compact('requirements'));
    }

    public function updateStatus(Request $request, Requirement $requirement): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:ACTIVE,MATCHED,CLOSED,EXPIRED',
        ]);

        $oldStatus = $requirement->status;
        $requirement->status = $request->status;
        $requirement->save();

        if ($requirement->status === 'ACTIVE') {
            $this->matchingService->matchRequirement($requirement);
        }

        ActivityLog::record(Auth::id(), 'ADMIN_UPDATED_REQUIREMENT_STATUS', 'Requirement', $requirement->id, [
            'old_status' => $oldStatus,
            'new_status' => $requirement->status,
        ]);

        return back()->with('success', "Requirement status updated to {$requirement->status}.");
    }

    public function reMatch(Requirement $requirement): RedirectResponse
    {
        $matches = $this->matchingService->matchRequirement($requirement);
        return back()->with('success', "Re-matched requirement. Found {$matches->count()} matching properties.");
    }
}

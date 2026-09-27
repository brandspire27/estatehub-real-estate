<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Property;
use App\Services\MatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminPropertyController extends Controller
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index(Request $request): View
    {
        $query = Property::with(['user', 'images'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('city', 'like', "%{$s}%")
                    ->orWhere('area', 'like', "%{$s}%");
            });
        }

        $properties = $query->paginate(15)->withQueryString();

        $counts = [
            'total' => Property::count(),
            'published' => Property::where('status', 'PUBLISHED')->count(),
            'pending' => Property::where('status', 'PENDING_VERIFICATION')->count(),
            'draft' => Property::where('status', 'DRAFT')->count(),
            'sold' => Property::where('status', 'SOLD')->count(),
        ];

        return view('admin.properties.index', compact('properties', 'counts'));
    }

    public function updateStatus(Request $request, Property $property): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:DRAFT,PENDING_VERIFICATION,PUBLISHED,SOLD,EXPIRED,REMOVED',
        ]);

        $oldStatus = $property->status;
        $property->status = $request->status;
        $property->save();

        // Run matching if transitioned to PUBLISHED
        if ($property->status === 'PUBLISHED') {
            $this->matchingService->matchProperty($property);
        }

        ActivityLog::record(Auth::id(), 'ADMIN_UPDATED_PROPERTY_STATUS', 'Property', $property->id, [
            'property_title' => $property->title,
            'old_status' => $oldStatus,
            'new_status' => $property->status,
        ]);

        return back()->with('success', "Listing status updated to {$property->status}.");
    }

    public function approve(Request $request, Property $property): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $property->status = 'PUBLISHED';
        $property->save();

        $this->matchingService->matchProperty($property);

        ActivityLog::record(Auth::id(), 'ADMIN_APPROVED_PROPERTY', 'Property', $property->id, [
            'property_title' => $property->title,
            'owner_id' => $property->user_id,
        ]);

        $message = "Listing '{$property->title}' has been approved and published to live marketplace.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'property_id' => $property->id,
                'status' => 'PUBLISHED',
            ]);
        }

        return redirect()->back(fallback: route('dashboard'))->with('success', $message);
    }

    public function destroy(Property $property): RedirectResponse
    {
        $title = $property->title;
        $property->delete();

        ActivityLog::record(Auth::id(), 'ADMIN_DELETED_PROPERTY', 'Property', $property->id, [
            'property_title' => $title,
        ]);

        return back()->with('success', "Property '{$title}' deleted.");
    }
}

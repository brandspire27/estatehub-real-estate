<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\City;
use App\Models\Locality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminLocalityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Locality::with('cityRecord')->withCount('properties');

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $localities = $query->orderBy('city')->orderBy('name')->paginate(20)->withQueryString();
        $cities = City::orderBy('name')->get();
        $selectedCity = $request->city;
        $search = $request->search;

        return view('admin.localities.index', compact('localities', 'cities', 'selectedCity', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:100'],
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('localities')->where(function ($query) use ($request) {
                    return $query->where('city', $request->city);
                }),
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.unique' => 'This locality already exists for the selected city.',
        ]);

        $cityRecord = City::where('name', $validated['city'])->first();

        $locality = Locality::create([
            'city_id' => $cityRecord ? $cityRecord->id : null,
            'city' => $validated['city'],
            'name' => trim($validated['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(Auth::id(), 'LOCALITY_CREATED', 'Locality', $locality->id, [
            'city' => $locality->city,
            'name' => $locality->name,
        ]);

        return redirect()->route('admin.localities.index', ['city' => $locality->city])
            ->with('success', "Locality '{$locality->name}' added successfully under {$locality->city}.");
    }

    public function update(Request $request, Locality $locality): RedirectResponse
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:100'],
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('localities')->where(function ($query) use ($request) {
                    return $query->where('city', $request->city);
                })->ignore($locality->id),
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $cityRecord = City::where('name', $validated['city'])->first();

        $locality->update([
            'city_id' => $cityRecord ? $cityRecord->id : null,
            'city' => $validated['city'],
            'name' => trim($validated['name']),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $locality->is_active,
        ]);

        ActivityLog::record(Auth::id(), 'LOCALITY_UPDATED', 'Locality', $locality->id, [
            'city' => $locality->city,
            'name' => $locality->name,
            'is_active' => $locality->is_active,
        ]);

        return redirect()->back()->with('success', "Locality '{$locality->name}' updated successfully.");
    }

    public function destroy(Locality $locality): RedirectResponse
    {
        $name = $locality->name;
        $city = $locality->city;

        $locality->delete();

        ActivityLog::record(Auth::id(), 'LOCALITY_DELETED', 'Locality', $locality->id, [
            'name' => $name,
            'city' => $city,
        ]);

        return redirect()->back()->with('success', "Locality '{$name}' deleted successfully.");
    }
}

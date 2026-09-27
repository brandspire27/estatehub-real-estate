<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\City;
use App\Models\Locality;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCityController extends Controller
{
    public function index(): View
    {
        $cities = City::withCount(['properties', 'publishedProperties', 'localities'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $totalProperties = $cities->sum('properties_count');
        $popularCount = $cities->where('is_popular', true)->count();

        return view('admin.cities.index', compact('cities', 'totalProperties', 'popularCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:cities,name'],
            'state' => ['nullable', 'string', 'max:100'],
            'is_popular' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ]);

        $imageUrl = null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('cities', 'public');
            $imageUrl = asset('storage/' . $path);
        } elseif (!empty($validated['image_url'])) {
            $imageUrl = $validated['image_url'];
        }

        $city = City::create([
            'name' => trim($validated['name']),
            'state' => !empty($validated['state']) ? trim($validated['state']) : null,
            'image_url' => $imageUrl,
            'is_popular' => $request->boolean('is_popular', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        ActivityLog::record(Auth::id(), 'CITY_CREATED', 'City', $city->id, [
            'name' => $city->name,
            'state' => $city->state,
            'has_custom_image' => !empty($imageUrl),
        ]);

        return redirect()->route('admin.cities.index')
            ->with('success', "City '{$city->name}' has been created successfully.");
    }

    public function update(Request $request, City $city): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('cities', 'name')->ignore($city->id)],
            'state' => ['nullable', 'string', 'max:100'],
            'is_popular' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $imageUrl = $city->image_url;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('cities', 'public');
            $imageUrl = asset('storage/' . $path);
        } elseif ($request->boolean('remove_image')) {
            $imageUrl = null;
        } elseif ($request->filled('image_url')) {
            $imageUrl = $validated['image_url'];
        }

        $oldName = $city->name;
        $newName = trim($validated['name']);

        $city->update([
            'name' => $newName,
            'state' => !empty($validated['state']) ? trim($validated['state']) : null,
            'image_url' => $imageUrl,
            'is_popular' => $request->boolean('is_popular', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        if ($oldName !== $newName) {
            Property::where('city', $oldName)->update(['city' => $newName]);
            Locality::where('city', $oldName)->update(['city' => $newName]);
        }

        ActivityLog::record(Auth::id(), 'CITY_UPDATED', 'City', $city->id, [
            'name' => $city->name,
            'state' => $city->state,
            'image_url' => $imageUrl,
            'is_popular' => $city->is_popular,
        ]);

        return redirect()->route('admin.cities.index')
            ->with('success', "Showcase and settings for '{$city->name}' updated successfully.");
    }

    public function togglePopular(City $city): RedirectResponse
    {
        $city->update([
            'is_popular' => !$city->is_popular,
        ]);

        ActivityLog::record(Auth::id(), 'CITY_POPULAR_TOGGLED', 'City', $city->id, [
            'name' => $city->name,
            'is_popular' => $city->is_popular,
        ]);

        $status = $city->is_popular ? 'featured in' : 'removed from';
        return redirect()->route('admin.cities.index')
            ->with('success', "'{$city->name}' is now {$status} Explore Popular Cities.");
    }

    public function destroy(City $city): RedirectResponse
    {
        $propertiesCount = $city->properties()->count();
        if ($propertiesCount > 0) {
            return redirect()->route('admin.cities.index')->with('error', "Cannot delete '{$city->name}' because {$propertiesCount} property listings are currently associated with it.");
        }

        $name = $city->name;
        $city->delete();

        ActivityLog::record(Auth::id(), 'CITY_DELETED', 'City', $city->id, [
            'name' => $name,
        ]);

        return redirect()->route('admin.cities.index')
            ->with('success', "City '{$name}' deleted successfully.");
    }
}

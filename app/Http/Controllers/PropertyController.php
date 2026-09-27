<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Models\ActivityLog;
use App\Models\City;
use App\Models\Locality;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyInquiry;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PropertyController extends Controller
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Public search & listing page.
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'keyword', 'city', 'locality', 'property_type', 'bhk',
            'min_price', 'max_price', 'min_area', 'max_area', 'possession_status', 'sort',
        ]);

        $properties = Property::with(['images', 'user'])
            ->published()
            ->filter($filters)
            ->paginate(12)
            ->withQueryString();

        $cities = City::orderBy('name')->pluck('name');
        $propertyTypes = ['Flat', 'Apartment', 'Villa', 'Plot', 'Building', 'Commercial Space', 'Land'];
        $localitiesByCity = Locality::active()->orderBy('name')->get()->groupBy('city');

        return view('properties.index', compact('properties', 'filters', 'cities', 'propertyTypes', 'localitiesByCity'));
    }

    /**
     * Public / detail view. Increments views. Supports SEO friendly slug with 301 redirect.
     */
    public function show(Request $request, Property $property, ?string $slug = null): View|RedirectResponse
    {
        // Auto-generate slug if missing
        if (empty($property->slug) && ! empty($property->title)) {
            $property->slug = Str::slug($property->title);
            if (! config('app.demo_read_only')) {
                $property->saveQuietly();
            }
        }

        // Canonical 301 redirect if slug is missing or doesn't match
        if ($property->slug && $slug !== $property->slug) {
            return redirect()->route('properties.show', [
                'property' => $property->id,
                'slug' => $property->slug,
            ], 301);
        }

        // Check authorization if not published
        if ($property->status !== 'PUBLISHED') {
            if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->isStaff() && Auth::id() !== $property->user_id)) {
                abort(404, 'Property not found or not published.');
            }
        }

        // Increment view count
        if (! config('app.demo_read_only')) {
            $property->increment('views');
        }

        $property->load(['images', 'user']);

        // Similar/related properties
        $relatedProperties = Property::with('images')
            ->published()
            ->where('id', '!=', $property->id)
            ->where(function ($q) use ($property) {
                $q->where('city', $property->city)
                    ->orWhere('property_type', $property->property_type);
            })
            ->take(4)
            ->get();

        return view('properties.show', compact('property', 'relatedProperties'));
    }

    /**
     * Store buyer inquiry and unlock seller contact.
     */
    public function storeInquiry(Request $request, Property $property): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_phone' => 'required|string|max:20',
            'buyer_email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $inquiry = PropertyInquiry::create([
            'property_id' => $property->id,
            'seller_id' => $property->user_id,
            'buyer_id' => Auth::id(),
            'buyer_name' => $validated['buyer_name'],
            'buyer_phone' => $validated['buyer_phone'],
            'buyer_email' => $validated['buyer_email'] ?? (Auth::user()?->email),
            'message' => $validated['message'] ?? null,
        ]);

        // Log inquiry activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'BUYER_INQUIRY',
            'description' => "Buyer {$validated['buyer_name']} ({$validated['buyer_phone']}) inquired about property #{$property->id} ({$property->title})",
            'subject_type' => Property::class,
            'subject_id' => $property->id,
            'ip_address' => $request->ip(),
        ]);

        $sellerPhone = $property->user->phone ?? '9876543212';
        $sellerName = $property->user->name ?? 'Property Owner';
        $cleanPhone = preg_replace('/[^0-9]/', '', $sellerPhone);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91'.$cleanPhone;
        }

        $waMsg = urlencode("Hello {$sellerName}, I am interested in your property '{$property->title}' on EstateHub.");
        $whatsappUrl = "https://wa.me/{$cleanPhone}?text={$waMsg}";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Seller contact details unlocked!',
                'phone' => $sellerPhone,
                'seller_name' => $sellerName,
                'whatsapp_url' => $whatsappUrl,
                'tel_url' => 'tel:'.$sellerPhone,
            ]);
        }

        return back()->with('success', "Seller contact unlocked! Call {$sellerName} at {$sellerPhone}");
    }

    /**
     * Seller / Staff listing management.
     */
    public function myListings(Request $request): View
    {
        $user = Auth::user();
        $query = Property::with(['images', 'matches'])
            ->latest();

        if ($user->isAdmin() || $user->isStaff()) {
            // Staff / Admin can view all or filtered by user
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $properties = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => Property::where('user_id', $user->id)->count(),
            'published' => Property::where('user_id', $user->id)->where('status', 'PUBLISHED')->count(),
            'pending' => Property::where('user_id', $user->id)->where('status', 'PENDING_VERIFICATION')->count(),
            'draft' => Property::where('user_id', $user->id)->where('status', 'DRAFT')->count(),
            'sold' => Property::where('user_id', $user->id)->where('status', 'SOLD')->count(),
        ];

        return view('properties.my_listings', compact('properties', 'counts'));
    }

    /**
     * Create property form.
     */
    public function create(): View
    {
        $cities = City::orderBy('name')->pluck('name');
        $propertyTypes = ['Flat', 'Apartment', 'Villa', 'Plot', 'Building', 'Commercial Space', 'Land'];
        $sellers = [];

        if (Auth::user()->canAccessStaff()) {
            $sellers = User::where('role', 'SELLER')->orderBy('name')->get();
        }

        $localitiesByCity = Locality::active()->orderBy('name')->get()->groupBy('city');

        return view('properties.create', compact('cities', 'propertyTypes', 'sellers', 'localitiesByCity'));
    }

    /**
     * Store property.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $authUser = Auth::user();

        // Determine owner
        $ownerId = $authUser->id;
        if ($authUser->canAccessStaff() && $request->filled('target_user_id')) {
            $ownerId = (int) $request->target_user_id;
        }

        // Sellers submit as DRAFT or PENDING_VERIFICATION; Admin/Staff can directly publish
        $status = $request->input('status', 'PENDING_VERIFICATION');
        if (! $authUser->canAccessStaff()) {
            if ($status !== 'DRAFT') {
                $status = 'PENDING_VERIFICATION';
            }
        }

        // KYC Document handling
        $kycDocumentUrl = $request->kyc_document_url;
        if ($request->hasFile('kyc_document_file')) {
            $path = $request->file('kyc_document_file')->store('properties/dossier/kyc', 'public');
            $kycDocumentUrl = asset('storage/'.$path);
        }

        // Private Photos handling (URLs + Files)
        $privatePhotos = [];
        if ($request->has('private_photos') && is_array($request->private_photos)) {
            $privatePhotos = array_values(array_filter(array_map('trim', $request->private_photos)));
        }
        if ($request->hasFile('private_photo_files')) {
            foreach ($request->file('private_photo_files') as $file) {
                $path = $file->store('properties/dossier/photos', 'public');
                $privatePhotos[] = asset('storage/'.$path);
            }
        }
        if (empty($privatePhotos)) {
            $privatePhotos = null;
        }

        // Private Videos handling (URLs + Files)
        $privateVideos = [];
        if ($request->has('private_videos') && is_array($request->private_videos)) {
            $privateVideos = array_values(array_filter(array_map('trim', $request->private_videos)));
        }
        if ($request->hasFile('private_video_files')) {
            foreach ($request->file('private_video_files') as $file) {
                $path = $file->store('properties/dossier/videos', 'public');
                $privateVideos[] = asset('storage/'.$path);
            }
        }
        if (empty($privateVideos)) {
            $privateVideos = null;
        }

        $property = Property::create([
            'user_id' => $ownerId,
            'created_by_user_id' => $authUser->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'property_type' => $request->property_type,
            'bhk' => in_array($request->property_type, ['Plot', 'Land', 'Commercial Space', 'Building']) ? null : $request->bhk,
            'area_sqft' => $request->area_sqft,
            'city' => $request->city,
            'area' => $request->area,
            'youtube_url' => $request->youtube_url,
            'kyc_document_type' => $request->kyc_document_type,
            'kyc_document_number' => $request->kyc_document_number,
            'kyc_document_url' => $kycDocumentUrl,
            'private_remarks' => $request->private_remarks,
            'private_photos' => $privatePhotos,
            'private_videos' => $privateVideos,
            'possession_status' => $request->possession_status,
            'status' => $status,
            'views' => 0,
        ]);

        $sort = 0;
        // Save uploaded public photos
        if ($request->hasFile('property_photos')) {
            foreach ($request->file('property_photos') as $photoFile) {
                $path = $photoFile->store('properties/photos', 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_url' => asset('storage/'.$path),
                    'sort_order' => $sort++,
                ]);
            }
        }

        // Save image URLs
        if ($request->has('image_urls') && is_array($request->image_urls)) {
            foreach ($request->image_urls as $url) {
                if (! empty(trim($url))) {
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_url' => trim($url),
                        'sort_order' => $sort++,
                    ]);
                }
            }
        }

        // If no images supplied, assign a high-res default architecture photo
        if ($property->images()->count() === 0) {
            $defaults = [
                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
            ];
            PropertyImage::create([
                'property_id' => $property->id,
                'image_url' => $defaults[array_rand($defaults)],
                'sort_order' => 0,
            ]);
        }

        // Perform instant matchmaking
        $this->matchingService->matchProperty($property);

        ActivityLog::record($authUser->id, 'PROPERTY_CREATED', 'Property', $property->id, [
            'title' => $property->title,
            'status' => $property->status,
        ]);

        return redirect()->route('listings.index')->with('success', 'Property listing created successfully.');
    }

    /**
     * Edit property form.
     */
    public function edit(Property $property): View
    {
        $this->authorizeOwnerOrStaff($property);

        $cities = City::orderBy('name')->pluck('name');
        $propertyTypes = ['Flat', 'Apartment', 'Villa', 'Plot', 'Building', 'Commercial Space', 'Land'];
        $property->load('images');
        $localitiesByCity = Locality::active()->orderBy('name')->get()->groupBy('city');

        return view('properties.edit', compact('property', 'cities', 'propertyTypes', 'localitiesByCity'));
    }

    /**
     * Update property.
     */
    public function update(StorePropertyRequest $request, Property $property): RedirectResponse
    {
        $this->authorizeOwnerOrStaff($property);
        $authUser = Auth::user();

        $status = $request->input('status', $property->status);
        if (! $authUser->canAccessStaff() && $status === 'PUBLISHED' && $property->status !== 'PUBLISHED') {
            $status = 'PENDING_VERIFICATION';
        }

        // KYC Document handling (file upload or URL)
        $kycDocumentUrl = $property->kyc_document_url;
        if ($request->filled('kyc_document_url')) {
            $kycDocumentUrl = trim($request->kyc_document_url);
        }
        if ($request->hasFile('kyc_document_file')) {
            $path = $request->file('kyc_document_file')->store('properties/dossier/kyc', 'public');
            $kycDocumentUrl = asset('storage/'.$path);
        }

        // Private Photos handling (file uploads + existing URLs)
        $privatePhotos = is_array($property->private_photos) ? $property->private_photos : [];
        if ($request->has('private_photos') && is_array($request->private_photos)) {
            $privatePhotos = array_values(array_filter(array_map('trim', $request->private_photos)));
        }
        if ($request->hasFile('private_photo_files')) {
            foreach ($request->file('private_photo_files') as $file) {
                $path = $file->store('properties/dossier/photos', 'public');
                $privatePhotos[] = asset('storage/'.$path);
            }
        }
        if (empty($privatePhotos)) {
            $privatePhotos = null;
        }

        // Private Videos handling (file uploads + existing URLs)
        $privateVideos = is_array($property->private_videos) ? $property->private_videos : [];
        if ($request->has('private_videos') && is_array($request->private_videos)) {
            $privateVideos = array_values(array_filter(array_map('trim', $request->private_videos)));
        }
        if ($request->hasFile('private_video_files')) {
            foreach ($request->file('private_video_files') as $file) {
                $path = $file->store('properties/dossier/videos', 'public');
                $privateVideos[] = asset('storage/'.$path);
            }
        }
        if (empty($privateVideos)) {
            $privateVideos = null;
        }

        $property->update([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'property_type' => $request->property_type,
            'bhk' => in_array($request->property_type, ['Plot', 'Land', 'Commercial Space', 'Building']) ? null : $request->bhk,
            'area_sqft' => $request->area_sqft,
            'city' => $request->city,
            'area' => $request->area,
            'youtube_url' => $request->youtube_url,
            'kyc_document_type' => $request->kyc_document_type,
            'kyc_document_number' => $request->kyc_document_number,
            'kyc_document_url' => $kycDocumentUrl,
            'private_remarks' => $request->private_remarks,
            'private_photos' => $privatePhotos,
            'private_videos' => $privateVideos,
            'possession_status' => $request->possession_status,
            'status' => $status,
        ]);

        // Delete any images selected for removal
        if ($request->has('delete_image_ids') && is_array($request->delete_image_ids)) {
            $property->images()->whereIn('id', $request->delete_image_ids)->delete();
        }

        // Upload new public property photos
        if ($request->hasFile('property_photos')) {
            $sort = $property->images()->count();
            foreach ($request->file('property_photos') as $photoFile) {
                $path = $photoFile->store('properties/photos', 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_url' => asset('storage/'.$path),
                    'sort_order' => $sort++,
                ]);
            }
        }

        // Handle URL inputs if provided
        if ($request->has('image_urls') && is_array($request->image_urls)) {
            $validUrls = array_filter(array_map('trim', $request->image_urls));
            if (! empty($validUrls)) {
                $existingUrls = $property->images()->pluck('image_url')->toArray();
                $sort = $property->images()->count();
                foreach ($validUrls as $url) {
                    if (! in_array($url, $existingUrls)) {
                        PropertyImage::create([
                            'property_id' => $property->id,
                            'image_url' => $url,
                            'sort_order' => $sort++,
                        ]);
                    }
                }
            }
        }

        // Re-match
        $this->matchingService->matchProperty($property);

        ActivityLog::record($authUser->id, 'PROPERTY_UPDATED', 'Property', $property->id, [
            'title' => $property->title,
            'status' => $property->status,
        ]);

        return redirect()->route('listings.index')->with('success', 'Property listing updated successfully.');
    }

    /**
     * Delete property.
     */
    public function destroy(Property $property): RedirectResponse
    {
        $this->authorizeOwnerOrStaff($property);

        $title = $property->title;
        $property->delete();

        ActivityLog::record(Auth::id(), 'PROPERTY_DELETED', 'Property', $property->id, [
            'title' => $title,
        ]);

        return redirect()->route('listings.index')->with('success', 'Property listing deleted.');
    }

    private function authorizeOwnerOrStaff(Property $property): void
    {
        $user = Auth::user();
        if (! $user->canAccessStaff() && $user->id !== $property->user_id) {
            abort(403, 'Unauthorized access to this property.');
        }
    }
}

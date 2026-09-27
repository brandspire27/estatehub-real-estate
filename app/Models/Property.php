<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by_user_id',
        'title',
        'slug',
        'youtube_url',
        'kyc_document_type',
        'kyc_document_number',
        'kyc_document_url',
        'private_remarks',
        'private_photos',
        'private_videos',
        'description',
        'price',
        'property_type',
        'bhk',
        'area_sqft',
        'city',
        'area',
        'possession_status',
        'status',
        'views',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'bhk' => 'integer',
        'area_sqft' => 'integer',
        'views' => 'integer',
        'private_photos' => 'array',
        'private_videos' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Property $property) {
            if (empty($property->slug) && !empty($property->title)) {
                $property->slug = \Illuminate\Support\Str::slug($property->title);
            }
        });
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchRecord::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'PUBLISHED');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['keyword'] ?? null, function ($q, $keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('area', 'like', "%{$keyword}%")
                        ->orWhere('city', 'like', "%{$keyword}%");
                });
            })
            ->when($filters['city'] ?? null, fn($q, $city) => $q->where('city', $city))
            ->when($filters['locality'] ?? null, fn($q, $loc) => $q->where('area', $loc))
            ->when($filters['property_type'] ?? null, fn($q, $type) => $q->where('property_type', $type))
            ->when($filters['bhk'] ?? null, fn($q, $bhk) => $q->where('bhk', (int)$bhk))
            ->when($filters['possession_status'] ?? null, fn($q, $status) => $q->where('possession_status', $status))
            ->when($filters['min_price'] ?? null, fn($q, $min) => $q->where('price', '>=', (float)$min))
            ->when($filters['max_price'] ?? null, fn($q, $max) => $q->where('price', '<=', (float)$max))
            ->when($filters['min_area'] ?? null, fn($q, $minArea) => $q->where('area_sqft', '>=', (float)$minArea))
            ->when($filters['max_area'] ?? null, fn($q, $maxArea) => $q->where('area_sqft', '<=', (float)$maxArea))
            ->when($filters['sort'] ?? 'latest', function ($q, $sort) {
                match ($sort) {
                    'price_low' => $q->orderBy('price', 'asc'),
                    'price_high' => $q->orderBy('price', 'desc'),
                    'views' => $q->orderBy('views', 'desc'),
                    default => $q->latest(),
                };
            });
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = (float)$this->price;
        if ($price >= 10000000) {
            return '₹' . rtrim(rtrim(number_format($price / 10000000, 2), '0'), '.') . ' Cr';
        }
        if ($price >= 100000) {
            return '₹' . rtrim(rtrim(number_format($price / 100000, 2), '0'), '.') . ' Lakh';
        }
        return '₹' . number_format($price);
    }

    public function getLocalityAttribute(): string
    {
        return $this->area ?? '';
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $first = $this->images->first();
        if ($first && !empty($first->image_url)) {
            return $first->image_url;
        }
        return 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80';
    }

    public function getSlugAttribute(): string
    {
        if (!empty($this->attributes['slug'])) {
            return $this->attributes['slug'];
        }
        return \Illuminate\Support\Str::slug($this->title ?? 'property');
    }

    public function getUrlAttribute(): string
    {
        return route('properties.show', ['property' => $this->id, 'slug' => $this->slug]);
    }

    public function getYoutubeVideoIdAttribute(): ?string
    {
        if (empty($this->youtube_url)) {
            return null;
        }

        // Match watch?v=, youtu.be/, embed/, shorts/
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_video_id;
        return $id ? "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&rel=0" : null;
    }

    public function getYoutubeThumbnailUrlAttribute(): ?string
    {
        $id = $this->youtube_video_id;
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }

    public function getMaskedPhoneAttribute(): string
    {
        $phone = $this->user->phone ?? '9876543212';
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        $len = strlen($cleaned);

        if ($len >= 4) {
            $firstTwo = substr($cleaned, 0, 2);
            $lastTwo = substr($cleaned, -2);
            $mask = str_repeat('*', max(4, $len - 4));
            return $firstTwo . $mask . $lastTwo;
        }

        return '98******12';
    }
}

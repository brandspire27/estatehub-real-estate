<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state',
        'image_url',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getDisplayImageUrlAttribute(): string
    {
        if (!empty($this->image_url)) {
            return $this->image_url;
        }

        return 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=800&q=80';
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'city', 'name');
    }

    public function publishedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'city', 'name')->where('status', 'PUBLISHED');
    }

    public function localities(): HasMany
    {
        return $this->hasMany(Locality::class, 'city_id');
    }
}

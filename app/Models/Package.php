<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_period',
        'duration_days',
        'listing_limit',
        'featured_limit',
        'contact_unlock_limit',
        'description',
        'features',
        'badge',
        'target_role',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'listing_limit' => 'integer',
        'featured_limit' => 'integer',
        'contact_unlock_limit' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Package $package) {
            if (empty($package->slug) && !empty($package->name)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isFree(): bool
    {
        return (float)$this->price == 0;
    }

    public function isUnlimitedListings(): bool
    {
        return $this->listing_limit < 0;
    }

    public function hasDirectLeads(): bool
    {
        return $this->contact_unlock_limit !== 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->isFree()) {
            return 'Free';
        }
        return '₹' . number_format($this->price, 0);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by_user_id',
        'property_type',
        'bhk',
        'budget_min',
        'budget_max',
        'possession_status',
        'city',
        'locality',
        'description',
        'status',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'bhk' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchRecord::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'ACTIVE');
    }

    public function getFormattedBudgetAttribute(): string
    {
        $min = (float)$this->budget_min;
        $max = (float)$this->budget_max;

        $format = function (float $val) {
            if ($val >= 10000000) {
                return '₹' . rtrim(rtrim(number_format($val / 10000000, 2), '0'), '.') . ' Cr';
            }
            if ($val >= 100000) {
                return '₹' . rtrim(rtrim(number_format($val / 100000, 2), '0'), '.') . 'L';
            }
            return '₹' . number_format($val);
        };

        return $format($min) . ' - ' . $format($max);
    }
}

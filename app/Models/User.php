<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'created_by_user_id',
        'created_via_staff',
        'is_active',
        'avatar',
        'package_id',
        'package_expires_at',
        'kyc_status',
        'kyc_document_type',
        'kyc_document_number',
        'kyc_document_url',
        'address',
        'emergency_contact',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'package_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'created_via_staff' => 'boolean',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function hasActivePackage(): bool
    {
        return $this->package_id !== null && (!$this->package_expires_at || $this->package_expires_at->isFuture());
    }

    public function hasReachedListingLimit(): bool
    {
        if (!$this->package) {
            return false;
        }

        if ($this->package->isUnlimitedListings()) {
            return false;
        }

        $activeCount = $this->properties()->whereIn('status', ['PUBLISHED', 'PENDING_VERIFICATION'])->count();
        return $activeCount >= $this->package->listing_limit;
    }

    public function isAdmin(): bool
    {
        return strtoupper($this->role) === 'ADMIN';
    }

    public function isStaff(): bool
    {
        return strtoupper($this->role) === 'STAFF';
    }

    public function isSeller(): bool
    {
        return strtoupper($this->role) === 'SELLER';
    }

    public function isBuyer(): bool
    {
        return strtoupper($this->role) === 'BUYER';
    }

    public function canAccessAdmin(): bool
    {
        return $this->isAdmin();
    }

    public function canAccessStaff(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by_user_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}

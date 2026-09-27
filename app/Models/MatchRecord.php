<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchRecord extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'requirement_id',
        'property_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(Requirement::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

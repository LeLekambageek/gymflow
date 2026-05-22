<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'name',
        'description',
        'coach_id',
        'category',
        'capacity',
        'duration_minutes',
        'price',
        'room',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CourseSession::class);
    }
}

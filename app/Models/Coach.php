<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'bio',
        'speciality',
        'photo',
        'status',
        'hourly_rate',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}

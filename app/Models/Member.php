<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'address',
        'emergency_contact',
        'emergency_phone',
        'status',
        'photo',
        'qr_code',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('end_date', '>=', date('Y-m-d'))
            ->latestOfMany('end_date');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'bookings', 'member_id', 'course_session_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}

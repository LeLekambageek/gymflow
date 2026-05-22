<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'member_id',
        'course_session_id',
        'status',
        'booked_at',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }
}

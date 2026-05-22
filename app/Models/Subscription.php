<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{
    protected $fillable = [
        'member_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
        'status',
        'amount_paid',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->whereDate('end_date', '>=', today());
    }

    public function getDaysRemainingAttribute()
    {
        $endDate = $this->end_date instanceof \DateTime ? $this->end_date : \Carbon\Carbon::parse($this->end_date);
        $today = \Carbon\Carbon::today();
        $days = $today->diffInDays($endDate, false);
        return (int) max(0, $days);
    }
}

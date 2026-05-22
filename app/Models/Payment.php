<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    
    protected $fillable = [
        'member_id',        
        'subscription_id',  
        'amount',           
        'type',             
        'method',           
        'status',           
        'reference',        
        'notes',            
        'payment_date',   
    ];


    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date', 
    ];


    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }


    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}

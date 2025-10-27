<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'months',
        'monthly_fee',
        'total_amount',
        'payment_reference',
        'pesapal_tracking_id',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'monthly_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
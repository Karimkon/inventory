<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'business_type',
        'subscription_status',
        'activated_at'
    ];

    protected $casts = [
        'activated_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('is_active', true);
    }

    // Check if shop has active subscription
    public function getHasActiveSubscriptionAttribute()
    {
        return $this->subscription_status === 'active' && $this->activeSubscription;
    }
}
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
        'activated_at',
        'pos_pin'
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

     /**
     * Generate a random 4-digit PIN
     */
    public static function generateRandomPin()
    {
        return sprintf('%04d', random_int(0, 9999));
    }
    
    /**
     * Check if POS PIN is valid
     */
    public function verifyPosPin($pin)
    {
        return $this->pos_pin === $pin; // Simple comparison for now
    }
}
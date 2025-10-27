<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // ADD THIS IMPORT

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'plan_type',
        'activation_fee',
        'monthly_fee',
        'months',
        'total_amount', // ADD THIS - it's in casts but not fillable
        'is_active',
        'payment_status',
        'pesapal_tracking_id',
        'payment_reference',
        'activated_at',
        'expires_at',
        'admin_notes',
    ];

    protected $casts = [
        'activation_fee' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if subscription is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if subscription is active
     */
    public function getIsActiveAttribute($value)
    {
        return $value && !$this->is_expired;
    }

    /**
     * Get remaining days
     */
    public function getRemainingDaysAttribute()
    {
        if (!$this->expires_at) return 0;
        return max(0, Carbon::now()->diffInDays($this->expires_at, false));
    }

    /**
     * Calculate total amount for multiple months
     */
    public static function calculateTotalAmount($monthlyFee, $months, $activationFee = 0)
    {
        return ($monthlyFee * $months) + $activationFee;
    }
    
    /**
     * Get the shop that owns the subscription.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get plan details
     */
    public function getPlanDetailsAttribute()
    {
        $plans = [
            'retail' => [
                'name' => 'Retail Shop',
                'activation_fee' => 50000,
                'monthly_fee' => 15000,
                'features' => ['Basic Inventory', 'Sales Tracking', 'Basic Reports']
            ],
            'wholesale' => [
                'name' => 'Wholesale Business',
                'activation_fee' => 120000,
                'monthly_fee' => 15000,
                'features' => ['Advanced Inventory', 'Bulk Operations', 'Supplier Management']
            ],
            'hardware' => [
                'name' => 'Hardware Store',
                'activation_fee' => 200000,
                'monthly_fee' => 15000,
                'features' => ['Hardware Specific Features', 'Contractor Accounts', 'Project Tracking']
            ],
            'supermarket' => [
                'name' => 'Supermarket',
                'activation_fee' => 500000,
                'monthly_fee' => 15000,
                'features' => ['Multi-department', 'Barcode Support', 'Advanced Analytics']
            ]
        ];

        return $plans[$this->plan_type] ?? null;
    }
}
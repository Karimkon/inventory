<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'plan_type',
        'activation_fee',
        'monthly_fee',
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
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

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
                'activation_fee' => 2000,
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
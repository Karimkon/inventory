<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'business_name',
        'owner_name',
        'email',
        'phone',
        'business_type',
        'location',
        'activation_fee',
        'monthly_fee',
        'months_paid',
        'total_amount',
        'status',
        'pesapal_tracking_id',
        'payment_reference',
        'paid_at',
        'admin_notes',
    ];

    protected $casts = [
        'activation_fee' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

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
                'monthly_fee' => 20000,
                'features' => ['Advanced Inventory', 'Bulk Operations', 'Supplier Management']
            ],
            'hardware' => [
                'name' => 'Hardware Store',
                'activation_fee' => 200000,
                'monthly_fee' => 20000,
                'features' => ['Hardware Specific Features', 'Contractor Accounts', 'Project Tracking']
            ],
            'supermarket' => [
                'name' => 'Supermarket',
                'activation_fee' => 500000,
                'monthly_fee' => 30000,
                'features' => ['Multi-department', 'Barcode Support', 'Advanced Analytics']
            ]
        ];

        return $plans[$this->business_type] ?? null;
    }

    /**
     * Check if application is paid
     */
    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if application is approved
     */
    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }
}
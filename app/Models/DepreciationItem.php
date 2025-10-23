<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DepreciationItem extends Model
{
    use HasFactory;

    protected $table = 'depreciation_items';

    protected $fillable = [
        'shop_id',
        'asset_name',
        'purchase_cost',
        'current_value',
        'depreciation_rate',
        'purchase_date',
        'useful_life_years',
        'description',
    ];

    protected $casts = [
        'purchase_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    /**
     * Get the shop that owns the asset.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Calculate accumulated depreciation
     */
    public function getAccumulatedDepreciationAttribute(): float
    {
        return $this->purchase_cost - $this->current_value;
    }

    /**
     * Calculate monthly depreciation expense
     */
    public function getMonthlyDepreciationAttribute(): float
    {
        $annualDepreciation = ($this->purchase_cost * $this->depreciation_rate) / 100;
        return $annualDepreciation / 12;
    }

    /**
     * Calculate remaining useful life in years
     */
    public function getRemainingLifeYearsAttribute(): float
    {
        if ($this->current_value <= 0 || $this->monthly_depreciation <= 0) {
            return 0;
        }
        return $this->current_value / ($this->monthly_depreciation * 12);
    }

    /**
     * Calculate age of asset in months
     */
    public function getAssetAgeMonthsAttribute(): int
    {
        return Carbon::parse($this->purchase_date)->diffInMonths(Carbon::now());
    }
}
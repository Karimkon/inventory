<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'category',
        'description',
        'amount',
        'expense_date',
        'notes',
        'receipt_image'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    // Expense categories
    const CATEGORIES = [
        'rent' => 'Rent',
        'utilities' => 'Utilities',
        'salaries' => 'Salaries',
        'supplies' => 'Supplies',
        'marketing' => 'Marketing',
        'transport' => 'Transport',
        'maintenance' => 'Maintenance',
        'other' => 'Other',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function scopeForShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year);
    }

    // Add to your Expense model (app/Models/Expense.php)
public function getCategoryIconAttribute()
{
    $icons = [
        'rent' => 'house',
        'utilities' => 'lightning',
        'salaries' => 'people',
        'supplies' => 'box',
        'marketing' => 'megaphone',
        'transport' => 'truck',
        'maintenance' => 'tools',
        'other' => 'tag'
    ];
    
    return $icons[$this->category] ?? 'receipt';
}
}
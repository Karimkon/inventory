<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'loan_name',
        'lender_name',
        'principal_amount',
        'interest_rate',
        'term_months',
        'monthly_payment',
        'start_date',
        'remaining_balance',
        'purpose',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'start_date' => 'date',
    ];

    /**
     * Get the shop that owns the loan.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Calculate the progress percentage of loan repayment
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->principal_amount == 0) {
            return 0;
        }
        
        $paid = $this->principal_amount - $this->remaining_balance;
        return ($paid / $this->principal_amount) * 100;
    }

    /**
     * Check if loan is fully paid
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_balance <= 0;
    }

    /**
     * Calculate total interest paid so far
     */
    public function getTotalInterestPaidAttribute(): float
    {
        $monthsPaid = $this->getMonthsPaid();
        $totalPayments = $this->monthly_payment * $monthsPaid;
        $principalPaid = $this->principal_amount - $this->remaining_balance;
        return max($totalPayments - $principalPaid, 0);
    }

    /**
     * Get number of months paid so far
     */
    public function getMonthsPaid(): int
    {
        $startDate = Carbon::parse($this->start_date);
        $monthsPaid = $startDate->diffInMonths(Carbon::now());
        return min($monthsPaid, $this->term_months);
    }
}
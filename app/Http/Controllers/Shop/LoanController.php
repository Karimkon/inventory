<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    /**
     * Display a listing of the loans.
     */
    public function index()
    {
        $shopId = Auth::user()->shop_id;
        $loans = Loan::where('shop_id', $shopId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        // Calculate total loan metrics
        $totalPrincipal = $loans->sum('principal_amount');
        $totalRemaining = $loans->sum('remaining_balance');
        $totalInterestPaid = $this->calculateTotalInterestPaid($shopId);

        return view('shop.loans.index', compact('loans', 'totalPrincipal', 'totalRemaining', 'totalInterestPaid'));
    }

    /**
     * Show the form for creating a new loan.
     */
    public function create()
    {
        return view('shop.loans.create');
    }

    /**
     * Store a newly created loan in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_name' => 'required|string|max:255',
            'lender_name' => 'required|string|max:255',
            'principal_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_months' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'purpose' => 'nullable|string',
        ]);

        // Calculate monthly payment and remaining balance
        $principal = $request->principal_amount;
        $monthlyInterestRate = ($request->interest_rate / 100) / 12;
        $termMonths = $request->term_months;

        // Monthly payment calculation using amortization formula
        if ($monthlyInterestRate > 0) {
            $monthlyPayment = ($principal * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $termMonths)) 
                            / (pow(1 + $monthlyInterestRate, $termMonths) - 1);
        } else {
            $monthlyPayment = $principal / $termMonths;
        }

        Loan::create([
            'shop_id' => Auth::user()->shop_id,
            'loan_name' => $request->loan_name,
            'lender_name' => $request->lender_name,
            'principal_amount' => $principal,
            'interest_rate' => $request->interest_rate,
            'term_months' => $termMonths,
            'monthly_payment' => round($monthlyPayment, 2),
            'start_date' => $request->start_date,
            'remaining_balance' => $principal,
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('shop.loans.index')
            ->with('success', 'Loan recorded successfully!');
    }

    /**
     * Display the specified loan.
     */
    public function show(Loan $loan)
    {
        // Verify loan belongs to user's shop
        if ($loan->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        // Calculate payment schedule
        $paymentSchedule = $this->generatePaymentSchedule($loan);

        return view('shop.loans.show', compact('loan', 'paymentSchedule'));
    }

    /**
     * Show the form for editing the specified loan.
     */
    public function edit(Loan $loan)
    {
        // Verify loan belongs to user's shop
        if ($loan->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        return view('shop.loans.edit', compact('loan'));
    }

    /**
     * Update the specified loan in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        // Verify loan belongs to user's shop
        if ($loan->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        $request->validate([
            'loan_name' => 'required|string|max:255',
            'lender_name' => 'required|string|max:255',
            'remaining_balance' => 'required|numeric|min:0',
            'purpose' => 'nullable|string',
        ]);

        $loan->update($request->only(['loan_name', 'lender_name', 'remaining_balance', 'purpose']));

        return redirect()->route('shop.loans.index')
            ->with('success', 'Loan updated successfully!');
    }

    /**
     * Remove the specified loan from storage.
     */
    public function destroy(Loan $loan)
    {
        // Verify loan belongs to user's shop
        if ($loan->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        $loan->delete();

        return redirect()->route('shop.loans.index')
            ->with('success', 'Loan deleted successfully!');
    }

    /**
     * Record a loan payment
     */
    public function recordPayment(Request $request, Loan $loan)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $newBalance = $loan->remaining_balance - $request->payment_amount;
        
        if ($newBalance < 0) {
            return back()->with('error', 'Payment amount exceeds remaining balance.');
        }

        $loan->update([
            'remaining_balance' => max($newBalance, 0)
        ]);

        return redirect()->route('shop.loans.show', $loan)
            ->with('success', 'Payment recorded successfully!');
    }

    /**
     * Calculate total interest paid across all loans
     */
    private function calculateTotalInterestPaid($shopId)
    {
        $loans = Loan::where('shop_id', $shopId)->get();
        $totalInterest = 0;

        foreach ($loans as $loan) {
            $monthsPaid = $loan->start_date->diffInMonths(Carbon::now());
            $monthsPaid = min($monthsPaid, $loan->term_months);
            
            $totalPayments = $loan->monthly_payment * $monthsPaid;
            $principalPaid = $loan->principal_amount - $loan->remaining_balance;
            $interestPaid = $totalPayments - $principalPaid;
            
            $totalInterest += max($interestPaid, 0);
        }

        return $totalInterest;
    }

    /**
     * Generate payment schedule for a loan
     */
    private function generatePaymentSchedule(Loan $loan)
    {
        $schedule = [];
        $balance = $loan->principal_amount;
        $monthlyRate = ($loan->interest_rate / 100) / 12;
        $payment = $loan->monthly_payment;
        $date = Carbon::parse($loan->start_date);

        for ($month = 1; $month <= $loan->term_months && $balance > 0; $month++) {
            $interest = $balance * $monthlyRate;
            $principal = $payment - $interest;
            
            // Adjust last payment
            if ($principal > $balance) {
                $principal = $balance;
                $payment = $principal + $interest;
            }

            $balance -= $principal;

            $schedule[] = [
                'month' => $month,
                'date' => $date->format('M Y'),
                'payment' => $payment,
                'principal' => $principal,
                'interest' => $interest,
                'balance' => max($balance, 0)
            ];

            $date->addMonth();
        }

        return $schedule;
    }
}
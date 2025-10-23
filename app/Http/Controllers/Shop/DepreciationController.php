<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepreciationItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DepreciationController extends Controller
{
    /**
     * Display a listing of depreciable assets.
     */
    public function index()
    {
        $shopId = Auth::user()->shop_id;
        $assets = DepreciationItem::where('shop_id', $shopId)
                    ->orderBy('purchase_date', 'desc')
                    ->paginate(10);

        // Calculate totals
        $totalOriginalCost = $assets->sum('purchase_cost');
        $totalCurrentValue = $assets->sum('current_value');
        $totalDepreciationExpense = $totalOriginalCost - $totalCurrentValue;

        return view('shop.depreciation.index', compact(
            'assets', 'totalOriginalCost', 'totalCurrentValue', 'totalDepreciationExpense'
        ));
    }

    /**
     * Show the form for creating a new depreciable asset.
     */
    public function create()
    {
        return view('shop.depreciation.create');
    }

    /**
     * Store a newly created depreciable asset.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'useful_life_years' => 'required|integer|min:1|max:50',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
        ]);

        // Calculate annual depreciation amount
        $annualDepreciation = ($request->purchase_cost * $request->depreciation_rate) / 100;
        
        // Calculate current value based on time elapsed
        $purchaseDate = Carbon::parse($request->purchase_date);
        $monthsOwned = $purchaseDate->diffInMonths(Carbon::now());
        $totalDepreciation = min(($annualDepreciation / 12) * $monthsOwned, $request->purchase_cost);
        $currentValue = max($request->purchase_cost - $totalDepreciation, 0);

        DepreciationItem::create([
            'shop_id' => Auth::user()->shop_id,
            'asset_name' => $request->asset_name,
            'purchase_cost' => $request->purchase_cost,
            'current_value' => $currentValue,
            'depreciation_rate' => $request->depreciation_rate,
            'purchase_date' => $request->purchase_date,
            'useful_life_years' => $request->useful_life_years,
            'description' => $request->description,
        ]);

        return redirect()->route('shop.depreciation.index')
            ->with('success', 'Depreciable asset added successfully!');
    }

    /**
     * Display comprehensive financial analysis.
     */
    public function financialAnalysis()
    {
        $shopId = Auth::user()->shop_id;
        
        // Get current month for calculations
        $currentMonth = Carbon::now()->format('Y-m');
        $startDate = Carbon::parse($currentMonth)->startOfMonth();
        $endDate = Carbon::parse($currentMonth)->endOfMonth();

        // Revenue and Gross Profit
        $revenueData = \App\Models\Sale::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(sold_price * quantity) as revenue, SUM(cost_price * quantity) as cogs')
            ->first();

        $grossProfit = ($revenueData->revenue ?? 0) - ($revenueData->cogs ?? 0);

        // Operating Expenses (from expenses table)
        $operatingExpenses = \App\Models\Expense::where('shop_id', $shopId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Depreciation Expense (monthly)
        $depreciationExpense = DepreciationItem::where('shop_id', $shopId)
            ->get()
            ->sum(function($asset) {
                $annualDepreciation = ($asset->purchase_cost * $asset->depreciation_rate) / 100;
                return $annualDepreciation / 12;
            });

        // Loan Interest Expense
        $loanInterest = \App\Models\Loan::where('shop_id', $shopId)
            ->where('remaining_balance', '>', 0)
            ->get()
            ->sum(function($loan) {
                return $loan->remaining_balance * ($loan->interest_rate / 100) / 12;
            });

        // EBITDA Calculation
        $ebitda = $grossProfit - $operatingExpenses;

        // EBIT (Earnings Before Interest and Taxes)
        $ebit = $ebitda - $depreciationExpense;

        // EBT (Earnings Before Taxes)
        $ebt = $ebit - $loanInterest;

        // Taxes (30% of EBT)
        $taxes = max($ebt * 0.30, 0);

        // Net Income
        $netIncome = $ebt - $taxes;

        // Get assets and loans for display
        $assets = DepreciationItem::where('shop_id', $shopId)->get();
        $loans = \App\Models\Loan::where('shop_id', $shopId)->where('remaining_balance', '>', 0)->get();

        return view('shop.depreciation.financial-analysis', compact(
            'grossProfit',
            'operatingExpenses',
            'depreciationExpense',
            'loanInterest',
            'ebitda',
            'ebit',
            'ebt',
            'taxes',
            'netIncome',
            'assets',
            'loans',
            'currentMonth'
        ));
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(DepreciationItem $depreciation)
    {
        // Verify asset belongs to user's shop
        if ($depreciation->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this asset.');
        }

        return view('shop.depreciation.edit', compact('depreciation'));
    }

    /**
     * Update the specified asset.
     */
    public function update(Request $request, DepreciationItem $depreciation)
    {
        // Verify asset belongs to user's shop
        if ($depreciation->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this asset.');
        }

        $request->validate([
            'asset_name' => 'required|string|max:255',
            'current_value' => 'required|numeric|min:0',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
        ]);

        $depreciation->update($request->only([
            'asset_name', 
            'current_value', 
            'depreciation_rate',
            'description'
        ]));

        return redirect()->route('shop.depreciation.index')
            ->with('success', 'Asset updated successfully!');
    }

    /**
     * Remove the specified asset.
     */
    public function destroy(DepreciationItem $depreciation)
    {
        // Verify asset belongs to user's shop
        if ($depreciation->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this asset.');
        }

        $depreciation->delete();

        return redirect()->route('shop.depreciation.index')
            ->with('success', 'Asset deleted successfully!');
    }

    /**
     * Recalculate all asset values (useful for end of month)
     */
    public function recalculateValues()
    {
        $shopId = Auth::user()->shop_id;
        $assets = DepreciationItem::where('shop_id', $shopId)->get();

        foreach ($assets as $asset) {
            $purchaseDate = Carbon::parse($asset->purchase_date);
            $monthsOwned = $purchaseDate->diffInMonths(Carbon::now());
            
            $annualDepreciation = ($asset->purchase_cost * $asset->depreciation_rate) / 100;
            $totalDepreciation = min(($annualDepreciation / 12) * $monthsOwned, $asset->purchase_cost);
            $currentValue = max($asset->purchase_cost - $totalDepreciation, 0);
            
            $asset->update(['current_value' => $currentValue]);
        }

        return redirect()->route('shop.depreciation.index')
            ->with('success', 'All asset values recalculated successfully!');
    }
}
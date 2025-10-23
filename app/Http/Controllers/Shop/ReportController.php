<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\DepreciationItem;
use App\Models\Loan;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $period = $request->get('period', 'today'); // today, week, month, custom
        
        // Set date range based on period
        switch($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                break;
            default: // today
                $startDate = Carbon::today();
                $endDate = Carbon::today();
        }

        // Sales Data
        $salesData = Sale::where('sales.shop_id', $shopId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select([
                DB::raw('SUM(quantity) as total_units_sold'),
                DB::raw('SUM(sold_price * quantity) as total_revenue'),
                DB::raw('SUM(cost_price * quantity) as total_cost'),
                DB::raw('SUM((sold_price - cost_price) * quantity) as total_profit')
            ])->first();

        // Top Selling Products
        $topProducts = Sale::where('sales.shop_id', $shopId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select([
                'products.name',
                DB::raw('SUM(sales.quantity) as total_sold'),
                DB::raw('SUM((sales.sold_price - sales.cost_price) * sales.quantity) as total_profit')
            ])
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();

        // Inventory Value
        $inventoryValue = Product::where('shop_id', $shopId)
            ->select([
                DB::raw('SUM(stock * cost_price) as total_inventory_value'),
                DB::raw('SUM(stock * price) as potential_revenue'),
                DB::raw('COUNT(*) as total_products')
            ])->first();

        // Daily Sales Trend
        $dailySales = Sale::where('sales.shop_id', $shopId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(quantity) as units_sold'),
                DB::raw('SUM((sold_price - cost_price) * quantity) as daily_profit')
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('shop.reports.index', compact(
            'salesData',
            'topProducts',
            'inventoryValue',
            'dailySales',
            'period',
            'startDate',
            'endDate'
        ));
    }

    // Enhanced Profit & Loss with Advanced Financial Metrics
    public function profitLoss(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        // REVENUE
        $revenue = Sale::where('sales.shop_id', $shopId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->sum(DB::raw('sold_price * quantity'));

        // COST OF GOODS SOLD
        $cogs = Sale::where('sales.shop_id', $shopId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->sum(DB::raw('cost_price * quantity'));

        // GROSS PROFIT
        $grossProfit = $revenue - $cogs;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        // OPERATING EXPENSES
        $operatingExpenses = Expense::where('shop_id', $shopId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // SUBSCRIPTION FEES
        $subscriptionFees = Subscription::where('shop_id', $shopId)
            ->where('is_active', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('monthly_fee');

        // EBITDA (Earnings Before Interest, Taxes, Depreciation, Amortization)
        $ebitda = $grossProfit - $operatingExpenses - $subscriptionFees;

        // DEPRECIATION & AMORTIZATION
        $depreciation = $this->calculateDepreciation($shopId, $startDate, $endDate);

        // EBIT (Earnings Before Interest and Taxes)
        $ebit = $ebitda - $depreciation;

        // INTEREST EXPENSE (from loans)
        $interestExpense = $this->calculateInterestExpense($shopId, $startDate, $endDate);

        // EBT (Earnings Before Taxes)
        $ebt = $ebit - $interestExpense;

        // TAXES (30% of EBT)
        $taxRate = 0.30;
        $taxes = max($ebt * $taxRate, 0); // Only tax if profitable

        // NET INCOME
        $netIncome = $ebt - $taxes;

        // Financial Ratios
        $netMargin = $revenue > 0 ? ($netIncome / $revenue) * 100 : 0;
        $operatingMargin = $revenue > 0 ? ($ebit / $revenue) * 100 : 0;
        $ebitdaMargin = $revenue > 0 ? ($ebitda / $revenue) * 100 : 0;

        // Monthly comparison
        $previousMonth = Carbon::parse($month)->subMonth()->format('Y-m');
        $previousMonthRevenue = Sale::where('sales.shop_id', $shopId)
            ->whereBetween('sales.created_at', [
                Carbon::parse($previousMonth)->startOfMonth(),
                Carbon::parse($previousMonth)->endOfMonth()
            ])
            ->sum(DB::raw('sold_price * quantity'));

        $revenueGrowth = $previousMonthRevenue > 0 ? 
            (($revenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 : 0;

        return view('shop.reports.profit-loss', compact(
            'revenue',
            'cogs',
            'grossProfit',
            'grossMargin',
            'operatingExpenses',
            'subscriptionFees',
            'ebitda',
            'depreciation',
            'ebit',
            'interestExpense',
            'ebt',
            'taxes',
            'taxRate',
            'netIncome',
            'netMargin',
            'operatingMargin',
            'ebitdaMargin',
            'month',
            'revenueGrowth',
            'startDate',
            'endDate'
        ));
    }

    // Enhanced Balance Sheet with Liabilities
    public function balanceSheet()
    {
        $shopId = Auth::user()->shop_id;

        // ASSETS
        $inventoryValue = Product::where('shop_id', $shopId)
            ->sum(DB::raw('stock * cost_price'));

        // Fixed Assets (Depreciated Value)
        $fixedAssets = DepreciationItem::where('shop_id', $shopId)
            ->sum('current_value');

        // Cash Balance (Net Profit from all periods)
        $totalRevenue = Sale::where('sales.shop_id', $shopId)
            ->sum(DB::raw('sold_price * quantity'));
        $totalCostOfGoods = Sale::where('sales.shop_id', $shopId)
            ->sum(DB::raw('cost_price * quantity'));
        $totalExpenses = Expense::where('shop_id', $shopId)->sum('amount');
        $totalInterest = $this->calculateTotalInterestExpense($shopId);
        $totalTaxes = $this->calculateTotalTaxes($shopId);
        
        $cashBalance = ($totalRevenue - $totalCostOfGoods - $totalExpenses - $totalInterest - $totalTaxes);

        // Total Assets
        $totalAssets = $inventoryValue + $fixedAssets + max($cashBalance, 0);

        // LIABILITIES
        $totalLoans = Loan::where('shop_id', $shopId)->sum('remaining_balance');
        $totalLiabilities = $totalLoans;

        // EQUITY
        $ownersEquity = $totalAssets - $totalLiabilities;

        // Financial Health Metrics
        $cashRatio = $totalAssets > 0 ? (max($cashBalance, 0) / $totalAssets) * 100 : 0;
        $inventoryRatio = $totalAssets > 0 ? ($inventoryValue / $totalAssets) * 100 : 0;
        $debtToEquity = $ownersEquity > 0 ? ($totalLiabilities / $ownersEquity) * 100 : 0;
        $currentRatio = $totalLiabilities > 0 ? ($totalAssets / $totalLiabilities) : 0;

        return view('shop.reports.balance-sheet', compact(
            'inventoryValue',
            'fixedAssets',
            'cashBalance',
            'totalAssets',
            'totalLiabilities',
            'totalLoans',
            'ownersEquity',
            'totalRevenue',
            'totalCostOfGoods',
            'totalExpenses',
            'totalInterest',
            'totalTaxes',
            'cashRatio',
            'inventoryRatio',
            'debtToEquity',
            'currentRatio'
        ));
    }

    // Helper Methods for Financial Calculations
    private function calculateDepreciation($shopId, $startDate, $endDate)
    {
        $depreciationItems = DepreciationItem::where('shop_id', $shopId)->get();
        $totalDepreciation = 0;

        foreach ($depreciationItems as $item) {
            $monthsInPeriod = $startDate->diffInMonths($endDate) + 1;
            $annualDepreciation = $item->purchase_cost * ($item->depreciation_rate / 100);
            $monthlyDepreciation = $annualDepreciation / 12;
            $periodDepreciation = $monthlyDepreciation * $monthsInPeriod;
            
            $totalDepreciation += $periodDepreciation;
        }

        return $totalDepreciation;
    }

    private function calculateInterestExpense($shopId, $startDate, $endDate)
    {
        $loans = Loan::where('shop_id', $shopId)->get();
        $totalInterest = 0;

        foreach ($loans as $loan) {
            $annualInterest = $loan->remaining_balance * ($loan->interest_rate / 100);
            $monthlyInterest = $annualInterest / 12;
            $monthsInPeriod = $startDate->diffInMonths($endDate) + 1;
            $periodInterest = $monthlyInterest * $monthsInPeriod;
            
            $totalInterest += $periodInterest;
        }

        return $totalInterest;
    }

    private function calculateTotalInterestExpense($shopId)
    {
        $loans = Loan::where('shop_id', $shopId)->get();
        $totalInterest = 0;

        foreach ($loans as $loan) {
            // Calculate total interest paid so far
            $monthsActive = $loan->start_date->diffInMonths(Carbon::now());
            $annualInterest = $loan->principal_amount * ($loan->interest_rate / 100);
            $monthlyInterest = $annualInterest / 12;
            $totalInterest += $monthlyInterest * min($monthsActive, $loan->term_months);
        }

        return $totalInterest;
    }

    private function calculateTotalTaxes($shopId)
    {
        // Calculate total taxes paid based on historical profitability
        $currentYear = Carbon::now()->year;
        $totalTaxes = 0;

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($currentYear, $month)->startOfMonth();
            $endDate = Carbon::create($currentYear, $month)->endOfMonth();

            $revenue = Sale::where('sales.shop_id', $shopId)
                ->whereBetween('sales.created_at', [$startDate, $endDate])
                ->sum(DB::raw('sold_price * quantity'));

            $cogs = Sale::where('sales.shop_id', $shopId)
                ->whereBetween('sales.created_at', [$startDate, $endDate])
                ->sum(DB::raw('cost_price * quantity'));

            $expenses = Expense::where('shop_id', $shopId)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');

            $ebt = ($revenue - $cogs - $expenses);
            $taxes = max($ebt * 0.30, 0);
            
            $totalTaxes += $taxes;
        }

        return $totalTaxes;
    }
}
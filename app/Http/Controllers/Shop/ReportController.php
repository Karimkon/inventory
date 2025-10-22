<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
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

        // Sales Data - FIXED: Specify table for shop_id
        $salesData = Sale::where('sales.shop_id', $shopId) // Specify table
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select([
                DB::raw('SUM(quantity) as total_units_sold'),
                DB::raw('SUM(sold_price * quantity) as total_revenue'),
                DB::raw('SUM(cost_price * quantity) as total_cost'),
                DB::raw('SUM((sold_price - cost_price) * quantity) as total_profit')
            ])->first();

        // Top Selling Products - FIXED: Specify table for shop_id in JOIN
        $topProducts = Sale::where('sales.shop_id', $shopId) // Specify table
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

        // Inventory Value - FIXED: Already correct
        $inventoryValue = Product::where('shop_id', $shopId)
            ->select([
                DB::raw('SUM(stock * cost_price) as total_inventory_value'),
                DB::raw('SUM(stock * price) as potential_revenue'),
                DB::raw('COUNT(*) as total_products')
            ])->first();

        // Daily Sales Trend - FIXED: Specify table for shop_id
        $dailySales = Sale::where('sales.shop_id', $shopId) // Specify table
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

    // Profit & Loss Statement - FIXED: Specify table for shop_id
    public function profitLoss(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        // Revenue from sales - FIXED: Specify table
        $revenue = Sale::where('sales.shop_id', $shopId) // Specify table
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->sum(DB::raw('sold_price * quantity'));

        // Cost of Goods Sold - FIXED: Specify table
        $cogs = Sale::where('sales.shop_id', $shopId) // Specify table
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->sum(DB::raw('cost_price * quantity'));

       // Gross Profit
$grossProfit = $revenue - $cogs;

// Gross Profit Margin
$grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

// ADD EXPENSES CALCULATION HERE
$totalExpenses = \App\Models\Expense::where('shop_id', $shopId)
    ->whereBetween('expense_date', [$startDate, $endDate])
    ->sum('amount');

// Net Profit (Gross Profit - Expenses)
$netProfit = $grossProfit - $totalExpenses;

// Net Profit Margin
$netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

// Monthly comparison - FIXED: Specify table
$previousMonth = Carbon::parse($month)->subMonth()->format('Y-m');
$previousMonthRevenue = Sale::where('sales.shop_id', $shopId) // Specify table
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
            'month',
            'revenueGrowth',
            'startDate',
            'endDate',
            'totalExpenses',
            'netProfit',
            'netMargin'
        ));
    }

    // Basic Balance Sheet - FIXED: Already correct
    public function balanceSheet()
{
    $shopId = Auth::user()->shop_id;

    // ASSETS
    // Inventory Value (at cost)
    $inventoryValue = Product::where('shop_id', $shopId)
        ->sum(DB::raw('stock * cost_price'));

    // Cash Balance (Total Profit from Sales MINUS Total Expenses)
    $totalRevenue = Sale::where('sales.shop_id', $shopId)
        ->sum(DB::raw('sold_price * quantity'));
    $totalCostOfGoods = Sale::where('sales.shop_id', $shopId)
        ->sum(DB::raw('cost_price * quantity'));
    $totalExpenses = \App\Models\Expense::where('shop_id', $shopId)->sum('amount');
    
    $cashBalance = ($totalRevenue - $totalCostOfGoods) - $totalExpenses;

    // Total Assets
    $totalAssets = $inventoryValue + max($cashBalance, 0); // Ensure cash doesn't go negative

    // LIABILITIES & EQUITY
    // For simplicity, we'll assume no liabilities in this basic version
    // In a real system, you'd track loans, accounts payable, etc.
    $totalLiabilities = 0;

    // Owner's Equity (Assets - Liabilities)
    $ownersEquity = $totalAssets - $totalLiabilities;

    // Financial Health Metrics
    $cashRatio = $totalAssets > 0 ? ($cashBalance / $totalAssets) * 100 : 0;
    $inventoryRatio = $totalAssets > 0 ? ($inventoryValue / $totalAssets) * 100 : 0;
    $debtToEquity = $ownersEquity > 0 ? ($totalLiabilities / $ownersEquity) * 100 : 0;

    return view('shop.reports.balance-sheet', compact(
        'inventoryValue',
        'cashBalance',
        'totalAssets',
        'totalLiabilities',
        'ownersEquity',
        'totalRevenue',
        'totalCostOfGoods',
        'totalExpenses',
        'cashRatio',
        'inventoryRatio',
        'debtToEquity'
    ));
}
}
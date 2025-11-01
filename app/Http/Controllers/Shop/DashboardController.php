<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;

        // Products stats - ONLY CURRENT SHOP
        $totalProducts = Product::where('shop_id', $shopId)->count();
        $recentProducts = Product::where('shop_id', $shopId)->latest()->take(5)->get();

        // Sales & profit - ONLY CURRENT SHOP
        $salesToday = Sale::where('shop_id', $shopId)
            ->whereDate('created_at', Carbon::today())->sum('quantity');
            
        $profitToday = Sale::where('shop_id', $shopId)
            ->whereDate('created_at', Carbon::today())
            ->sum(DB::raw('(sold_price - cost_price) * quantity')); // FIXED

        // Add weekly and monthly stats
        $salesWeek = Sale::where('shop_id', $shopId)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('quantity');
            
        $profitWeek = Sale::where('shop_id', $shopId)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum(DB::raw('(sold_price - cost_price) * quantity'));

            // ADD: Stock Alerts
    $lowStockProducts = Product::where('shop_id', $shopId)
        ->where('stock', '<=', 5)
        ->where('stock', '>', 0)
        ->count();

    $outOfStockProducts = Product::where('shop_id', $shopId)
        ->where('stock', 0)
        ->count();

    $lowStockItems = Product::where('shop_id', $shopId)
        ->where('stock', '<=', 5)
        ->orderBy('stock', 'asc')
        ->take(10)
        ->get();

        // Calculate expected revenue from all stock
$expectedRevenue = Product::where('shop_id', $shopId)
    ->sum(DB::raw('price * stock'));

// Calculate total investment in stock
$totalInvestment = Product::where('shop_id', $shopId)
    ->sum(DB::raw('cost_price * stock'));

// Calculate potential profit
$potentialProfit = $expectedRevenue - $totalInvestment;

        return view('shop.dashboard', compact(
            'totalProducts', 'recentProducts', 'salesToday', 'profitToday', 'salesWeek', 'profitWeek',
            'lowStockProducts', 'outOfStockProducts', 'lowStockItems',  'expectedRevenue', 'totalInvestment', 'potentialProfit'
        ));
    }
}
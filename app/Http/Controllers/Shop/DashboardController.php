<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $shop = Auth::user()->shop;
        $shopId = $shop->id;

        // Get dashboard statistics using existing logic
        $totalProducts = Product::where('shop_id', $shopId)->count();
        
        // Use the same logic as your existing dashboard
        $lowStockProducts = Product::where('shop_id', $shopId)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->count();
            
        $outOfStockProducts = Product::where('shop_id', $shopId)
            ->where('stock', 0)
            ->count();
            
        $criticalStockProducts = Product::where('shop_id', $shopId)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 2)
            ->count();

        // Get recent low stock items (stock <= 5)
        $lowStockItems = Product::where('shop_id', $shopId)
            ->where(function($query) {
                $query->where('stock', 0)
                      ->orWhere('stock', '<=', 5);
            })
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        // Calculate inventory value and potential profit (same as existing)
        $inventoryData = Product::where('shop_id', $shopId)
            ->select(
                DB::raw('SUM(stock * cost_price) as inventory_value'),
                DB::raw('SUM(stock * (price - cost_price)) as potential_profit')
            )->first();

        $expectedRevenue = $inventoryData->inventory_value ?? 0;
        $potentialProfit = $inventoryData->potential_profit ?? 0;

        // Sales data (same as existing)
        $salesToday = Sale::where('shop_id', $shopId)
            ->whereDate('created_at', today())
            ->sum('quantity');

        $profitToday = Sale::where('shop_id', $shopId)
            ->whereDate('created_at', today())
            ->sum(DB::raw('(sold_price - cost_price) * quantity'));

        $salesWeek = Sale::where('shop_id', $shopId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('quantity');

        $profitWeek = Sale::where('shop_id', $shopId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw('(sold_price - cost_price) * quantity'));

        // Recent products (same as existing)
        $recentProducts = Product::where('shop_id', $shopId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('shop.dashboard', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'criticalStockProducts',
            'lowStockItems',
            'expectedRevenue',
            'potentialProfit',
            'salesToday',
            'profitToday',
            'salesWeek',
            'profitWeek',
            'recentProducts',
            'shop'
        ));
    }

    /**
     * Detailed Low Stock Report Page
     */
    public function lowStockReport(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $type = $request->get('type', 'all'); // all, low, critical, out
        
        $query = Product::where('shop_id', $shopId);
        
        // Apply filters
        switch ($type) {
            case 'low':
                $query->where('stock', '>', 0)
                      ->where('stock', '<=', 5);
                $title = 'Low Stock Products (1-5 items)';
                break;
            case 'critical':
                $query->where('stock', '>', 0)
                      ->where('stock', '<=', 2);
                $title = 'Critical Stock (1-2 items)';
                break;
            case 'out':
                $query->where('stock', 0);
                $title = 'Out of Stock Products';
                break;
            case 'all':
            default:
                $query->where(function($q) {
                    $q->where('stock', 0)
                      ->orWhere('stock', '<=', 5);
                });
                $title = 'All Stock Alerts';
                break;
        }
        
        $products = $query->orderBy('stock', 'asc')
                         ->orderBy('name', 'asc')
                         ->paginate(20);
        
        // Get counts for filter badges
        $counts = [
            'all' => Product::where('shop_id', $shopId)
                ->where(function($q) {
                    $q->where('stock', 0)->orWhere('stock', '<=', 5);
                })->count(),
            'low' => Product::where('shop_id', $shopId)
                ->where('stock', '>', 0)
                ->where('stock', '<=', 5)->count(),
            'critical' => Product::where('shop_id', $shopId)
                ->where('stock', '>', 0)
                ->where('stock', '<=', 2)->count(),
            'out' => Product::where('shop_id', $shopId)
                ->where('stock', 0)->count(),
        ];
        
        return view('shop.products.low-stock-report', compact(
            'products', 'type', 'title', 'counts'
        ));
    }
}
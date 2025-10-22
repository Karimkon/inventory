<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // SaaS Overview Stats
        $totalShops = Shop::count();
        $totalUsers = User::where('role', 'shop')->count();
        $totalProducts = Product::count();
        
        // Shop performance metrics
        $topShops = Shop::withCount(['products', 'sales'])
            ->withSum('sales', 'quantity')
            ->orderBy('sales_sum_quantity', 'desc')
            ->take(5)
            ->get();

        // System-wide sales & profit
        $salesToday = Sale::whereDate('created_at', Carbon::today())->sum('quantity');
        $profitToday = Sale::whereDate('created_at', Carbon::today())
            ->sum(DB::raw('(sold_price - cost_price) * quantity'));

        $salesWeek = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('quantity');
        $profitWeek = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum(DB::raw('(sold_price - cost_price) * quantity'));

        $salesMonth = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('quantity');
        $profitMonth = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum(DB::raw('(sold_price - cost_price) * quantity'));

        // Latest sales across all shops
        $filter = $request->query('filter', 'today');
        $salesQuery = Sale::with(['product', 'shop'])->latest();

        switch ($filter) {
            case 'week':
                $salesQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $salesQuery->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;
            case 'custom':
                if ($request->filled('start') && $request->filled('end')) {
                    $salesQuery->whereBetween('created_at', [
                        Carbon::parse($request->start)->startOfDay(),
                        Carbon::parse($request->end)->endOfDay()
                    ]);
                }
                break;
            default: // today
                $salesQuery->whereDate('created_at', Carbon::today());
                break;
        }

        $latestSales = $salesQuery->paginate(10);
        $recentShops = Shop::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalShops',
            'totalUsers',
            'totalProducts',
            'topShops',
            'salesToday',
            'profitToday',
            'salesWeek',
            'profitWeek',
            'salesMonth',
            'profitMonth',
            'latestSales',
            'recentShops',
            'filter'
        ));
    }
}
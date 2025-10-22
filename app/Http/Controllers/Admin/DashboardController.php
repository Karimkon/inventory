<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Products stats
        $totalProducts = Product::count();
        $recentProducts = Product::latest()->take(5)->get();

        // Sales & profit calculations
        $salesToday = Sale::whereDate('created_at', Carbon::today())->sum('quantity');
        $profitToday = Sale::whereDate('created_at', Carbon::today())
            ->sum(\DB::raw('(sold_price - cost_price) * quantity'));

        $salesWeek = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('quantity');
        $profitWeek = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum(\DB::raw('(sold_price - cost_price) * quantity'));

        $salesMonth = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('quantity');
        $profitMonth = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum(\DB::raw('(sold_price - cost_price) * quantity'));

        // Filter for Latest Sold Items table
        $filter = $request->query('filter', 'today');
        $salesQuery = Sale::with('product')->latest();

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

        return view('admin.dashboard', compact(
            'totalProducts',
            'recentProducts',
            'salesToday',
            'profitToday',
            'salesWeek',
            'profitWeek',
            'salesMonth',
            'profitMonth',
            'latestSales',
            'filter'
        ));
    }
}

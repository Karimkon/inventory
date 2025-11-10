<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PosController extends Controller
{
    /**
     * Show POS login form
     */
    public function showLogin()
    {
        return view('pos.login');
    }

    /**
     * Handle POS login with PIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'shop_identifier' => 'required|string|max:255',
            'pos_pin' => 'required|string|size:4',
        ]);

        // Find shop by ID, slug, or name
        $shop = Shop::where('id', $request->shop_identifier)
                    ->orWhere('slug', $request->shop_identifier)
                    ->orWhere('name', 'like', '%' . $request->shop_identifier . '%')
                    ->first();

        if (!$shop || !$shop->verifyPosPin($request->pos_pin)) {
            return back()->with('error', 'Invalid Shop ID or PIN');
        }

        // Store session
        session([
            'pos_shop_id' => $shop->id,
            'pos_shop_name' => $shop->name,
            'pos_access' => true,
            'pos_login_time' => now(),
        ]);

        return redirect()->route('pos.dashboard')
                         ->with('success', "POS access granted for {$shop->name}");
    }

     /**
     * POS Dashboard with enhanced data
     */
    public function dashboard(Request $request)
    {
        if (!session('pos_access') || !session('pos_shop_id')) {
            return redirect()->route('pos.login')->with('error', 'POS session expired. Please login again.');
        }

        $shop = Shop::find(session('pos_shop_id'));
        if (!$shop) {
            session()->forget(['pos_access', 'pos_shop_id', 'pos_shop_name']);
            return redirect()->route('pos.login')->with('error', 'Shop no longer exists.');
        }

        $search = $request->query('search');

        // Get products
        $products = Product::where('shop_id', $shop->id)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(12);

        // Dashboard statistics
        $totalProducts = Product::where('shop_id', $shop->id)->count();
        $lowStockProducts = Product::where('shop_id', $shop->id)
            ->where('stock', '<=', 5)
            ->where('stock', '>', 0)
            ->count();
        $outOfStockProducts = Product::where('shop_id', $shop->id)
            ->where('stock', 0)
            ->count();
        
        // Today's sales
        $todaySales = Sale::where('shop_id', $shop->id)
            ->whereDate('created_at', Carbon::today())
            ->get();
        
        $todayRevenue = $todaySales->sum(function($sale) {
            return $sale->sold_price * $sale->quantity;
        });
        
        $todayProfit = $todaySales->sum(function($sale) {
            return ($sale->sold_price - $sale->cost_price) * $sale->quantity;
        });

        // Stock value
        $stockValue = Product::where('shop_id', $shop->id)
            ->sum(\DB::raw('stock * cost_price'));

        return view('pos.dashboard', compact(
            'products', 
            'search',
            'totalProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'todayRevenue',
            'todayProfit',
            'stockValue',
            'shop'
        ));
    }


    /**
     * POS Sales History
     */
    public function salesHistory(Request $request)
    {
        $this->checkPosAccess();
        $shopId = session('pos_shop_id');

        // Get filter parameters
        $timeRange = $request->get('time_range', 'today');
        $date = $request->get('date', now()->format('Y-m-d'));
        $productId = $request->get('product_id');

        // Base query
        $salesQuery = Sale::with('product')
            ->where('shop_id', $shopId);

        // Apply time filters
        if ($timeRange === 'today') {
            $salesQuery->whereDate('created_at', Carbon::today());
        } elseif ($timeRange === 'week') {
            $salesQuery->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        } elseif ($timeRange === 'month') {
            $salesQuery->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
        } elseif ($timeRange === 'custom' && $date) {
            $salesQuery->whereDate('created_at', $date);
        }

        // Apply product filter
        if ($productId) {
            $salesQuery->where('product_id', $productId);
        }

        // Get sales data
        $sales = $salesQuery->orderBy('created_at', 'desc')->paginate(20);

        // Summary statistics
        $totalSales = $salesQuery->count();
        $totalQuantity = $salesQuery->sum('quantity');
        $totalRevenue = $salesQuery->get()->sum(function($sale) {
            return $sale->sold_price * $sale->quantity;
        });
        $totalProfit = $salesQuery->get()->sum(function($sale) {
            return ($sale->sold_price - $sale->cost_price) * $sale->quantity;
        });

        // Sales by product
        $salesByProduct = Sale::where('shop_id', $shopId)
            ->when($timeRange === 'today', fn($q) => $q->whereDate('created_at', Carbon::today()))
            ->when($timeRange === 'week', fn($q) => $q->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]))
            ->when($timeRange === 'month', fn($q) => $q->whereMonth('created_at', Carbon::now()->month))
            ->when($timeRange === 'custom' && $date, fn($q) => $q->whereDate('created_at', $date))
            ->with('product')
            ->get()
            ->groupBy('product_id')
            ->map(function($productSales) {
                $firstSale = $productSales->first();
                return [
                    'product_name' => $firstSale->product->name,
                    'total_quantity' => $productSales->sum('quantity'),
                    'total_revenue' => $productSales->sum(function($sale) {
                        return $sale->sold_price * $sale->quantity;
                    }),
                    'total_profit' => $productSales->sum(function($sale) {
                        return ($sale->sold_price - $sale->cost_price) * $sale->quantity;
                    }),
                    'sales_count' => $productSales->count(),
                ];
            })
            ->values();

        // Get products for filter dropdown
        $products = Product::where('shop_id', $shopId)->get();

        return view('pos.sales-history', compact(
            'sales',
            'totalSales',
            'totalQuantity',
            'totalRevenue',
            'totalProfit',
            'salesByProduct',
            'products',
            'timeRange',
            'date',
            'productId'
        ));
    }
    
    /**
     * Quick Sell product
     */
  public function sell(Request $request, Product $product)
{
    $this->checkShopAccess($product->shop_id);

    $request->validate([
        'quantity' => "required|integer|min:1|max:{$product->stock}",
    ]);

    $quantity = $request->quantity;

    try {
        // Generate receipt number
        $shop = Shop::find(session('pos_shop_id'));
        $receiptNumber = $this->generateReceiptNumber($shop->name);
        
        $sale = Sale::create([
            'product_id' => $product->id,
            'shop_id' => $product->shop_id,
            'quantity' => $quantity,
            'sold_price' => $product->price,
            'cost_price' => $product->cost_price,
        ]);

        $product->decrement('stock', $quantity);

        // Save session for receipt WITH RECEIPT NUMBER
        Session::put('pos_last_sale', [
            'receipt_number' => $receiptNumber,
            'shop_name' => $shop->name,
            'items' => [[
                'product_name' => $product->name,
                'quantity' => $quantity,
                'price' => $product->price,
                'total' => $product->price * $quantity,
            ]],
            'total_amount' => $product->price * $quantity,
            'sold_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sold {$quantity} × {$product->name}",
            'receipt_number' => $receiptNumber,
            'receipt_url' => route('pos.cart.receipt')
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 422);
    }
}



    /**
     * POS Receipt
     */
    public function receipt()
{
    $lastSale = Session::get('pos_last_sale');

    if (!$lastSale || !isset($lastSale['items'])) {
        return back()->with('error', 'No sale data found.');
    }

    $soldItems = $lastSale['items'];

    $total = array_reduce($soldItems, function ($sum, $item) {
        return $sum + ($item['total'] ?? 0);
    }, 0);

    return view('pos.receipt', [
        'cart' => $soldItems,
        'total' => $total,
    ]);
}


    /**
     * Store POS Expense
     */
    public function storeExpense(Request $request)
    {
        $this->checkPosAccess();

        $shopId = session('pos_shop_id');

        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'shop_id' => $shopId,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'notes' => 'Recorded via POS by attendant',
        ]);

        return redirect()->route('pos.dashboard')
            ->with('success', "Expense of UGX " . number_format($request->amount) . " recorded successfully!");
    }

    /**
     * POS Logout
     */
    public function logout()
    {
        session()->forget(['pos_access', 'pos_shop_id', 'pos_shop_name']);
        return redirect('/')->with('info', 'POS session ended.');
    }

    // ========================
    // Cart Methods (Session-based)
    // ========================
    public function addToCart(Request $request, $productId)
    {
        $this->checkPosAccess();

        $product = Product::findOrFail($productId);
        $this->checkShopAccess($product->shop_id);

        $cart = Session::get('pos_cart', []);
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity > $product->stock) $quantity = $product->stock;

        $cart[$productId] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => ($cart[$productId]['quantity'] ?? 0) + $quantity,
            'max_stock' => $product->stock
        ];

        Session::put('pos_cart', $cart);

        return response()->json(['success' => true]);
    }

    public function updateCart(Request $request, $productId)
    {
        $cart = Session::get('pos_cart', []);
        if (!isset($cart[$productId])) return response()->json(['success' => false, 'error' => 'Product not in cart']);

        $product = Product::findOrFail($productId);
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity > $product->stock) $quantity = $product->stock;

        if ($quantity < 1) unset($cart[$productId]);
        else $cart[$productId]['quantity'] = $quantity;

        Session::put('pos_cart', $cart);
        return response()->json(['success' => true]);
    }

    public function removeFromCart($productId)
    {
        $cart = Session::get('pos_cart', []);
        unset($cart[$productId]);
        Session::put('pos_cart', $cart);
        return response()->json(['success' => true]);
    }

    public function clearCart()
    {
        Session::forget('pos_cart');
        return response()->json(['success' => true]);
    }

    public function getCartData()
    {
        $cart = Session::get('pos_cart', []);
        $cartTotal = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
        return response()->json([
            'cartItems' => $cart,
            'cartTotal' => $cartTotal,
            'cartCount' => count($cart)
        ]);
    }

    public function checkout()
{
    $cart = Session::get('pos_cart', []);
    if (!$cart) {
        return response()->json(['success' => false, 'error' => 'Cart is empty']);
    }

    $soldItems = [];
    $totalAmount = 0;
    $totalProfit = 0;

     // Generate receipt number
    $shop = Shop::find(session('pos_shop_id'));
    $receiptNumber = $this->generateReceiptNumber($shop->name);

    \DB::beginTransaction();

    try {
        foreach ($cart as $item) {
            $product = Product::lockForUpdate()->find($item['product_id']);

            if (!$product || $product->shop_id != session('pos_shop_id')) {
                throw new \Exception("Product '{$item['name']}' not found or unauthorized.");
            }

            if ($product->stock < $item['quantity']) {
                throw new \Exception("Insufficient stock for '{$item['name']}'. Available: {$product->stock}");
            }

            $quantity = $item['quantity'];
            $itemTotal = $product->price * $quantity;
            $itemProfit = ($product->price - $product->cost_price) * $quantity;

            // Record sale
            $sale = Sale::create([
                'product_id' => $product->id,
                'shop_id' => $product->shop_id,
                'quantity' => $quantity,
                'sold_price' => $product->price,
                'cost_price' => $product->cost_price,
            ]);

            // Decrement stock
            $product->decrement('stock', $quantity);

            // Save for receipt
            $soldItems[] = [
                'sale_id' => $sale->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'price' => $product->price,
                'total' => $itemTotal,
                'profit' => $itemProfit,
            ];

            $totalAmount += $itemTotal;
            $totalProfit += $itemProfit;
        }

        \DB::commit();

        // Save sold items in session for receipt
        Session::put('pos_last_sale', [
             'receipt_number' => $receiptNumber,
            'shop_name' => $shop->name,
            'items' => $soldItems,
            'total_amount' => $totalAmount,
            'total_profit' => $totalProfit,
            'sold_at' => now()->format('Y-m-d H:i:s'),
        ]);

        // Clear the cart
        Session::forget('pos_cart');

        return response()->json([
            'success' => true,
            'message' => 'Sale completed successfully!',
            'receipt_number' => $receiptNumber,
            'total_amount' => $totalAmount,
            'total_profit' => $totalProfit
        ]);

    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Checkout error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 422);
    }
}


   public function unifiedReceipt()
{
    $lastSale = Session::get('pos_last_sale');

    if (!$lastSale || !isset($lastSale['items'])) {
        return back()->with('error', 'No sale data found.');
    }

    return view('pos.receipt', [
        'receipt_number' => $lastSale['receipt_number'],
        'shop_name' => $lastSale['shop_name'],
        'cart' => $lastSale['items'],
        'total' => $lastSale['total_amount'],
        'sold_at' => $lastSale['sold_at'],
    ]);
}



    // ========================
    // Helper Methods
    // ========================
    private function checkPosAccess()
    {
        if (!session('pos_access') || !session('pos_shop_id')) {
            abort(403, 'POS access required.');
        }
    }

    private function checkShopAccess($shopId)
    {
        $this->checkPosAccess();
        if ($shopId != session('pos_shop_id')) {
            abort(403, 'Unauthorized product access.');
        }
    }

    // Add this method to your existing PosController
private function generateReceiptNumber($shopName)
{
    // Get shop prefix from name (first 3 letters uppercase)
    $prefix = strtoupper(substr($shopName, 0, 3));
    
    // Get today's sales count for this shop + 1
    $todaySalesCount = Sale::where('shop_id', session('pos_shop_id'))
        ->whereDate('created_at', today())
        ->count();
    
    $nextNumber = str_pad($todaySalesCount + 1, 3, '0', STR_PAD_LEFT);
    
    return $prefix . '-' . date('Ymd') . '-' . $nextNumber;
}
}

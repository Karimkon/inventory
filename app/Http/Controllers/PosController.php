<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use App\Models\Expense;

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
     * Handle POS login with PIN security
     */
    public function login(Request $request)
    {
        $request->validate([
            'shop_identifier' => 'required|string|max:255',
            'pos_pin' => 'required|string|size:4' // PIN must be exactly 4 digits
        ]);

        // Try to find shop by various identifiers
        $shop = Shop::where('id', $request->shop_identifier)
                    ->orWhere('slug', $request->shop_identifier)
                    ->orWhere('name', 'like', '%' . $request->shop_identifier . '%')
                    ->first();

        if (!$shop) {
            return back()->with('error', 'Shop not found. Please check the Shop ID/Name.');
        }

        // Verify POS PIN
        if (!$shop->verifyPosPin($request->pos_pin)) {
            \Log::warning('Failed POS login attempt', [
                'shop_id' => $shop->id,
                'identifier' => $request->shop_identifier
            ]);
            return back()->with('error', 'Invalid POS PIN. Please check with your manager.');
        }

        // Store shop in session for POS access
        session([
            'pos_shop_id' => $shop->id,
            'pos_shop_name' => $shop->name,
            'pos_access' => true,
            'pos_login_time' => now()
        ]);

        \Log::info('POS login successful', ['shop_id' => $shop->id]);

        return redirect()->route('pos.dashboard')
            ->with('success', "POS access granted for {$shop->name}");
    }

    /**
     * POS Dashboard with security check
     */
    public function dashboard(Request $request)
    {
        // Enhanced security check
        if (!session('pos_access') || !session('pos_shop_id')) {
            return redirect()->route('pos.login')->with('error', 'POS session expired. Please login again.');
        }

        // Verify shop still exists
        $shop = Shop::find(session('pos_shop_id'));
        if (!$shop) {
            session()->forget(['pos_access', 'pos_shop_id', 'pos_shop_name']);
            return redirect()->route('pos.login')->with('error', 'Shop no longer exists.');
        }

        $shopId = session('pos_shop_id');
        $search = $request->query('search');

        // Get products for this shop
        $products = Product::where('shop_id', $shopId)
            ->when($search, function($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(12);

        return view('pos.dashboard', compact('products', 'search'));
    }
    
    /**
     * Sell product from POS
     */
    public function sell(Request $request, Product $product)
    {
        // Verify product belongs to the POS shop
        if ($product->shop_id != session('pos_shop_id')) {
            return back()->with('error', 'Invalid product access.');
        }

        $request->validate([
            'quantity' => "required|integer|min:1|max:{$product->stock}",
        ]);

        $quantity = $request->quantity;

        // Calculate profit
        $profit = ($product->price - $product->cost_price) * $quantity;

        // Record sale
        \App\Models\Sale::create([
            'product_id' => $product->id,
            'shop_id' => $product->shop_id,
            'quantity' => $quantity,
            'sold_price' => $product->price,
            'cost_price' => $product->cost_price,
        ]);

        // Update product stock
        $product->decrement('stock', $quantity);

        // Store sale info for success message
        return back()->with('success', 
            "Sold {$quantity} × {$product->name} for UGX " . number_format($product->price * $quantity)
        );
    }

    /**
     * POS Receipt
     */
    public function receipt(Product $product, $qty)
    {
        if ($product->shop_id != session('pos_shop_id')) {
            abort(403, 'Unauthorized access.');
        }

        $total = $product->price * $qty;
        return view('pos.receipt', compact('product', 'qty', 'total'));
    }

    /**
     * Store expense from POS
     */
    public function storeExpense(Request $request)
    {
        // Check if POS access is granted
        if (!session('pos_access')) {
            return redirect('/pos/login')->with('error', 'POS access required.');
        }

        $shopId = session('pos_shop_id');

        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        // Create the expense
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
     * Logout from POS
     */
    public function logout()
    {
        session()->forget(['pos_access', 'pos_shop_id', 'pos_shop_name']);
        return redirect('/')->with('info', 'POS session ended.');
    }
}
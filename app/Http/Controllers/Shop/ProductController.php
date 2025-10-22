<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $search = $request->query('search');

        $products = Product::where('shop_id', $shopId)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(10);

        return view('shop.products.index', compact('products', 'search'));
    }

    // ADD: Show create form for shop users
    public function create()
    {
        return view('shop.products.create');
    }

    // ADD: Store product for shop users
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create([
            'name' => $request->name,
            'stock' => $request->stock,
            'cost_price' => $request->cost_price,
            'price' => $request->price,
            'shop_id' => Auth::user()->shop_id, // Auto-assign to shop
        ]);

        return redirect()->route('shop.products.index')
            ->with('success', 'Product added successfully!');
    }

    // COMPLETE: Sell product functionality
    public function sell(Request $request, Product $product)
    {
        // Verify product belongs to user's shop
        if ($product->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this product.');
        }

        $request->validate([
            'quantity' => "required|integer|min:1|max:{$product->stock}",
        ]);

        $quantity = $request->quantity;

        // Calculate profit
        $profit = ($product->price - $product->cost_price) * $quantity;

        // Record sale
        Sale::create([
            'product_id' => $product->id,
            'shop_id' => $product->shop_id,
            'quantity' => $quantity,
            'sold_price' => $product->price,
            'cost_price' => $product->cost_price,
        ]);

        // Update product stock
        $product->decrement('stock', $quantity);

        // Store sale info for success message
        session()->flash('last_sale', [
            'product_name' => $product->name,
            'quantity' => $quantity,
            'total' => $product->price * $quantity,
            'profit' => $profit,
            'sold_at' => now()->format('Y-m-d H:i'),
        ]);

        return redirect()->route('shop.products.index')
            ->with('success', "Sold {$quantity} of {$product->name} for UGX " . number_format($product->price * $quantity));
    }

    // ADD: Receipt generation
    public function receipt(Product $product, $qty)
    {
        if ($product->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this product.');
        }

        $total = $product->price * $qty;
        
        return view('shop.products.receipt', compact('product', 'qty', 'total'));
    }
}
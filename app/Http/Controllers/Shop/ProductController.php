<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $search = $request->query('search');

        $products = Product::where('shop_id', $shopId)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(10);

        // Get cart items count for badge
        $cartCount = $this->getCartItemsCount();

        return view('shop.products.index', compact('products', 'search', 'cartCount'));
    }

    // ADD TO CART functionality
    public function addToCart(Request $request, $productId)
    {
        try {
            // Find the product
            $product = Product::find($productId);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found'
                ], 404);
            }

            // Verify shop ownership
            if ($product->shop_id !== Auth::user()->shop_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 403);
            }

            // Validate quantity
            $validated = $request->validate([
                'quantity' => "required|integer|min:1|max:{$product->stock}",
            ], [
                'quantity.max' => "Only {$product->stock} items available in stock",
                'quantity.min' => 'Quantity must be at least 1',
                'quantity.required' => 'Quantity is required',
            ]);

            $quantity = $validated['quantity'];
            $cart = session()->get('cart', []);

            // Check if product already in cart
            if (isset($cart[$product->id])) {
                $newQuantity = $cart[$product->id]['quantity'] + $quantity;
                if ($newQuantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'error' => "Cannot add more than available stock. You have {$cart[$product->id]['quantity']} in cart. Available: {$product->stock}"
                    ], 422);
                }
                $cart[$product->id]['quantity'] = $newQuantity;
            } else {
                $cart[$product->id] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'cost_price' => $product->cost_price,
                    'quantity' => $quantity,
                    'max_stock' => $product->stock
                ];
            }

            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'cartCount' => $this->getCartItemsCount(),
                'cartTotal' => $this->getCartTotal()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Add to cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while adding to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    // UPDATE CART item quantity
    public function updateCart(Request $request, $productId)
    {
        try {
            $cart = session()->get('cart', []);
            
            if (!isset($cart[$productId])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found in cart'
                ], 404);
            }

            $product = Product::find($productId);
            if (!$product || $product->shop_id !== Auth::user()->shop_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found'
                ], 404);
            }

            $quantity = (int)$request->quantity;
            
            if ($quantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'error' => "Cannot add more than available stock. Available: {$product->stock}"
                ], 422);
            }

            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
                $cart[$productId]['max_stock'] = $product->stock; // Update max stock
            }

            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'cartCount' => $this->getCartItemsCount(),
                'cartTotal' => $this->getCartTotal(),
                'itemTotal' => isset($cart[$productId]) ? $cart[$productId]['price'] * $cart[$productId]['quantity'] : 0
            ]);

        } catch (\Exception $e) {
            Log::error('Update cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating cart'
            ], 500);
        }
    }

    // REMOVE FROM CART
    public function removeFromCart($productId)
    {
        try {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
                session()->put('cart', $cart);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart',
                'cartCount' => $this->getCartItemsCount(),
                'cartTotal' => $this->getCartTotal()
            ]);

        } catch (\Exception $e) {
            Log::error('Remove from cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while removing from cart'
            ], 500);
        }
    }

    // CLEAR CART
    public function clearCart()
    {
        try {
            session()->forget('cart');
            
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Clear cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while clearing cart'
            ], 500);
        }
    }

    // CHECKOUT - Sell all items in cart
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'error' => 'Cart is empty'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $saleItems = [];
            $totalAmount = 0;
            $totalProfit = 0;

            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                
                // Verify product exists and has sufficient stock
                if (!$product) {
                    throw new \Exception("Product '{$item['name']}' not found");
                }
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for '{$item['name']}'. Available: {$product->stock}, Requested: {$item['quantity']}");
                }

                // Calculate item totals
                $itemTotal = $item['price'] * $item['quantity'];
                $itemProfit = ($item['price'] - $item['cost_price']) * $item['quantity'];
                
                // Record sale
                $sale = Sale::create([
                    'product_id' => $product->id,
                    'shop_id' => Auth::user()->shop_id,
                    'quantity' => $item['quantity'],
                    'sold_price' => $item['price'],
                    'cost_price' => $item['cost_price'],
                ]);

                // Update product stock
                $product->decrement('stock', $item['quantity']);

                // Store sale item for receipt
                $saleItems[] = [
                    'sale_id' => $sale->id,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $itemTotal,
                    'profit' => $itemProfit,
                ];

                $totalAmount += $itemTotal;
                $totalProfit += $itemProfit;
            }

            DB::commit();

            // Store sale info for success message and receipt
            session()->flash('last_sale', [
                'type' => 'multi',
                'items' => $saleItems,
                'total_amount' => $totalAmount,
                'total_profit' => $totalProfit,
                'sold_at' => now()->format('Y-m-d H:i:s'),
            ]);

            // Clear cart after successful checkout
            session()->forget('cart');

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'total_amount' => $totalAmount,
                'total_profit' => $totalProfit
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }

    // UNIFIED RECEIPT for multiple items
    public function unifiedReceipt()
    {
        $lastSale = session('last_sale');
        
        if (!$lastSale) {
            abort(404, 'No recent sale found. Please complete a sale first.');
        }

        // Handle both single and multi-item sales
        if ($lastSale['type'] === 'multi') {
            return view('shop.products.unified-receipt', [
                'saleItems' => $lastSale['items'],
                'totalAmount' => $lastSale['total_amount'],
                'totalProfit' => $lastSale['total_profit'],
                'soldAt' => $lastSale['sold_at']
            ]);
        } else {
            // Convert single sale to multi format for unified receipt
            return view('shop.products.unified-receipt', [
                'saleItems' => [[
                    'product_name' => $lastSale['product_name'],
                    'quantity' => $lastSale['quantity'],
                    'price' => $lastSale['total'] / $lastSale['quantity'],
                    'total' => $lastSale['total'],
                    'profit' => $lastSale['profit'],
                ]],
                'totalAmount' => $lastSale['total'],
                'totalProfit' => $lastSale['profit'],
                'soldAt' => $lastSale['sold_at']
            ]);
        }
    }

    // Helper methods
    private function getCartItemsCount()
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }

    private function getCartTotal()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    // Get cart data for AJAX requests
    public function getCartData()
    {
        return response()->json([
            'cartCount' => $this->getCartItemsCount(),
            'cartTotal' => $this->getCartTotal(),
            'cartItems' => session()->get('cart', [])
        ]);
    }

    // Your existing methods remain the same...
    public function create()
    {
        return view('shop.products.create');
    }

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
            'shop_id' => Auth::user()->shop_id,
        ]);

        return redirect()->route('shop.products.index')
            ->with('success', 'Product added successfully!');
    }

    public function edit(Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this product.');
        }

        return view('shop.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this product.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update([
            'name' => $request->name,
            'stock' => $request->stock,
            'cost_price' => $request->cost_price,
            'price' => $request->price,
        ]);

        return redirect()->route('shop.products.index')
            ->with('success', 'Product updated successfully!');
    }

   public function sell(Request $request, Product $product)
{
    if ($product->shop_id !== Auth::user()->shop_id) {
        return response()->json([
            'success' => false,
            'error' => 'Unauthorized access'
        ], 403);
    }

    try {
        $validated = $request->validate([
            'quantity' => "required|integer|min:1|max:{$product->stock}",
        ]);

        $quantity = $validated['quantity'];
        $profit = ($product->price - $product->cost_price) * $quantity;

        Sale::create([
            'product_id' => $product->id,
            'shop_id' => $product->shop_id,
            'quantity' => $quantity,
            'sold_price' => $product->price,
            'cost_price' => $product->cost_price,
        ]);

        $product->decrement('stock', $quantity);

        session()->flash('last_sale', [
            'type' => 'single',
            'product_name' => $product->name,
            'quantity' => $quantity,
            'total' => $product->price * $quantity,
            'profit' => $profit,
            'sold_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sold {$quantity} of {$product->name}"
        ]);

    } catch (\Exception $e) {
        Log::error('Quick sell error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 422);
    }
}   

    public function receipt(Product $product, $qty)
    {
        if ($product->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this product.');
        }

        $total = $product->price * $qty;
        
        return view('shop.products.receipt', compact('product', 'qty', 'total'));
    }

    
}
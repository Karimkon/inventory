<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf; // Make sure you installed barryvdh/laravel-dompdf
use App\Models\Sale;


class ProductController extends Controller
{
    // Display products list
    public function index(Request $request)
    {
        $search = $request->query('search');

        $products = Product::when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                           ->paginate(10);

        return view('admin.products.index', compact('products', 'search'));
    }

    // Show create form
    public function create()
    {
        return view('admin.products.create');
    }

    // Store new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
        ]);

        Product::create($request->only(['name', 'stock', 'price', 'cost_price']));

        return redirect()->route('admin.products.index')->with('success','Product added!');
    }

    // Sell product
    public function sell(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        $qty = $request->quantity;

        // Reduce stock
        $product->decrement('stock', $qty);

        // Record the sale
        Sale::create([
            'product_id' => $product->id,
            'quantity' => $qty,
            'sold_price' => $product->price,
            'cost_price' => $product->cost_price,
        ]);

        // Calculate profit
        $profit = ($product->price - $product->cost_price) * $qty;

        // Store sale info for dashboard/index
        session()->flash('last_sale', [
            'product_name' => $product->name,
            'qty'          => $qty,
            'profit'       => $profit,
            'sold_at'      => now()->format('Y-m-d H:i:s'),
        ]);

        // If print is selected, redirect to PDF receipt
        if ($request->print === 'yes') {
            return redirect()->route('admin.products.receipt', [
                'product' => $product->id,
                'qty' => $qty
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Sale recorded! Profit: UGX " . number_format($profit));
    }

    // Generate PDF receipt
    public function receipt(Product $product, $qty)
    {
        $total = $product->price * $qty;

        $pdf = Pdf::loadView('admin.products.receipt', [
            'product' => $product,
            'qty' => $qty,
            'total' => $total,
        ]);

        return $pdf->download('receipt.pdf'); // triggers download
    }

    // Show single product
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    // Edit product
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
        ]);

        $product->update($request->only(['name', 'stock', 'price', 'cost_price']));

        return redirect()->route('admin.products.index')->with('success', 'Product updated!');
    }

    // Delete product
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted!');
    }

    public function printReceipt($saleId)
    {
        $sale = Sale::with('product')->findOrFail($saleId);

        try {
            $connector = new WindowsPrintConnector("XP-58"); // USB printer name
            $printer = new Printer($connector);

            $printer->text("SN Hardware\n");
            $printer->text("Sale Receipt\n");
            $printer->text("----------------------------\n");
            $printer->text("Product: " . $sale->product->name . "\n");
            $printer->text("Qty: " . $sale->quantity . "\n");
            $printer->text("Price: UGX " . number_format($sale->sold_price,2) . "\n");
            $printer->text("Cost: UGX " . number_format($sale->cost_price,2) . "\n");
            $printer->text("Profit: UGX " . number_format(($sale->sold_price - $sale->cost_price) * $sale->quantity,2) . "\n");
            $printer->text("Sold At: " . $sale->created_at->format('d M Y H:i') . "\n");
            $printer->text("----------------------------\n");
            $printer->text("Thank you!\n");

            $printer->cut();
            $printer->close();

            return redirect()->back()->with('success', 'Receipt printed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Printer error: ' . $e->getMessage());
        }
    }
}

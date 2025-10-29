{{-- resources/views/pos/receipt.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ session('pos_shop_name') ?? 'POS' }}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 320px; margin: auto; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px; text-align: left; border-bottom: 1px solid #ccc; }
        tfoot td { font-weight: bold; }
        .total { font-size: 1.2em; margin-top: 10px; }
        .center { text-align: center; }
        .small { font-size: 0.85em; color: #555; }
        @media print { body { width: auto; } }
    </style>
</head>
<body>
    <h2>{{ session('pos_shop_name') ?? 'Shop Receipt' }}</h2>
    <p class="center small">Date: {{ now()->format('d/m/Y H:i') }}</p>

    @if(isset($product) && isset($qty))
        {{-- Single product receipt --}}
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $qty }}</td>
                    <td>UGX {{ number_format($product->price) }}</td>
                    <td>UGX {{ number_format($product->price * $qty) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">TOTAL</td>
                    <td>UGX {{ number_format($total) }}</td>
                </tr>
            </tfoot>
        </table>
    @elseif(isset($cart))
        {{-- Unified cart receipt --}}
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $item)
    <tr>
        <td>{{ $item['product_name'] }}</td>
        <td>{{ $item['quantity'] }}</td>
        <td>UGX {{ number_format($item['price']) }}</td>
        <td>UGX {{ number_format($item['total']) }}</td>
    </tr>
@endforeach

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">TOTAL</td>
                    <td>UGX {{ number_format($total) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p>No items to display.</p>
    @endif

    <p class="center small">Thank you for shopping with us!</p>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shop Receipt</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 280px; /* thermal printer width */
            margin: 0;
            padding: 0;
        }
        .header, .footer {
            text-align: center;
        }
        .header h2 {
            margin: 0;
            font-size: 14px;
        }
        .header p {
            margin: 2px 0 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            padding: 2px 0;
            text-align: left;
        }
        .totals td {
            border-top: 1px dashed #000;
            font-weight: bold;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .shop-info {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ Auth::user()->shop->name ?? 'My Shop' }}</h2>
        <p>Sales Receipt</p>
        <p>{{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <div class="separator"></div>

    <table>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $qty }}</td>
            <td>{{ number_format($product->price, 2) }}</td>
            <td>{{ number_format($total, 2) }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <table class="totals">
        <tr>
            <td>Total Amount:</td>
            <td>UGX {{ number_format($total, 2) }}</td>
        </tr>
        @if(($product->price - $product->cost_price) > 0)
        <tr>
            <td>Profit:</td>
            <td>UGX {{ number_format(($product->price - $product->cost_price) * $qty, 2) }}</td>
        </tr>
        @endif
    </table>

    <div class="separator"></div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p class="shop-info">{{ Auth::user()->shop->name ?? 'My Shop' }}</p>
    </div>

    <script>
        // Auto-print if needed
        window.onload = function() {
            // Uncomment below to auto-print
            // window.print();
        };
    </script>
</body>
</html>
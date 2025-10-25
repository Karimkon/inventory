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
        .item-row td {
            border-bottom: 1px dotted #ccc;
            padding: 3px 0;
        }
        .totals td {
            border-top: 1px dashed #000;
            font-weight: bold;
            padding: 4px 0;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .shop-info {
            font-size: 10px;
            color: #666;
        }
        .item-name {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
        @foreach($saleItems as $item)
        <tr class="item-row">
            <td class="item-name" title="{{ $item['product_name'] }}">{{ $item['product_name'] }}</td>
            <td>{{ $item['quantity'] }}</td>
            <td>{{ number_format($item['unit_price'], 2) }}</td>
            <td>{{ number_format($item['total'], 2) }}</td>
        </tr>
        @endforeach
    </table>

    <div class="separator"></div>

    <table class="totals">
        <tr>
            <td>Subtotal:</td>
            <td>UGX {{ number_format($totalAmount, 2) }}</td>
        </tr>
        <tr>
            <td>Total Items:</td>
            <td>{{ count($saleItems) }}</td>
        </tr>
        <tr>
            <td>Total Profit:</td>
            <td>UGX {{ number_format($totalProfit, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Grand Total:</strong></td>
            <td><strong>UGX {{ number_format($totalAmount, 2) }}</strong></td>
        </tr>
    </table>

    <div class="separator"></div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p class="shop-info">{{ Auth::user()->shop->name ?? 'My Shop' }}</p>
        <p class="shop-info">Receipt ID: {{ strtoupper(uniqid()) }}</p>
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
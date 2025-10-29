<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shop Receipt</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 280px;
            margin: 0 auto;
            padding: 10px;
        }
        .header, .footer {
            text-align: center;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th {
            text-align: left;
            padding: 3px 0;
            border-bottom: 1px solid #000;
            font-size: 11px;
        }
        td {
            padding: 2px 0;
            font-size: 11px;
        }
        .totals td {
            border-top: 1px dashed #000;
            font-weight: bold;
            padding: 5px 0;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .shop-info {
            font-size: 10px;
            color: #666;
        }
        .item-row td {
            padding: 3px 0;
        }
        .text-right {
            text-align: right;
        }
        .grand-total {
            font-size: 13px;
            font-weight: bold;
        }
        @media print {
            body {
                width: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ Auth::user()->shop->name ?? 'MY SHOP' }}</h2>
        <p>SALES RECEIPT</p>
        <p>{{ $soldAt }}</p>
        <p style="font-size: 10px;">Receipt #{{ substr(md5($soldAt), 0, 8) }}</p>
    </div>

    <div class="separator"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 45%;">Item</th>
                <th style="width: 15%;">Qty</th>
                <th style="width: 20%;" class="text-right">Price</th>
                <th style="width: 20%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($saleItems as $item)
            <tr class="item-row">
                <td>{{ $item['product_name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td class="text-right">{{ number_format($item['price'], 0) }}</td>
                <td class="text-right">{{ number_format($item['total'], 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <table class="totals">
        <tr>
            <td>SUBTOTAL:</td>
            <td class="text-right">UGX {{ number_format($totalAmount, 0) }}</td>
        </tr>
        <tr class="grand-total">
            <td>TOTAL:</td>
            <td class="text-right">UGX {{ number_format($totalAmount, 0) }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <div class="footer">
        <p style="margin: 10px 0;">Thank you for your purchase!</p>
        <p class="shop-info">{{ Auth::user()->shop->name ?? 'My Shop' }}</p>
        @if(Auth::user()->shop->phone ?? false)
        <p class="shop-info">Tel: {{ Auth::user()->shop->phone }}</p>
        @endif
        <p class="shop-info" style="margin-top: 10px; font-size: 9px;">
            Served by: {{ Auth::user()->name }}
        </p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
            
            // Close window after printing (optional)
            // window.onafterprint = function() {
            //     window.close();
            // };
        };
    </script>
</body>
</html>
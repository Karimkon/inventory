{{-- resources/views/pos/receipt.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ session('pos_shop_name') ?? 'POS' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 10px auto;
            padding: 15px;
            background: white;
            color: #000;
            line-height: 1.4;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #333;
        }
        
        .shop-name {
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .receipt-info {
            font-size: 12px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .receipt-number {
            font-weight: bold;
            font-size: 14px;
            color: #e74c3c;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
        }
        
        thead {
            border-bottom: 2px solid #333;
        }
        
        th {
            text-align: left;
            padding: 8px 4px;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 11px;
        }
        
        td {
            padding: 6px 4px;
            border-bottom: 1px dotted #ddd;
        }
        
        .item-name {
            font-weight: 600;
        }
        
        .quantity, .price, .total {
            text-align: right;
        }
        
        tfoot tr {
            border-top: 2px solid #333;
        }
        
        tfoot td {
            padding: 10px 4px;
            font-weight: 900;
            font-size: 14px;
        }
        
        .grand-total {
            background: #f8f9fa;
            font-size: 16px !important;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #333;
            font-size: 11px;
        }
        
        .thank-you {
            font-weight: 900;
            font-size: 12px;
            margin-top: 10px;
            color: #2c3e50;
        }
        
        .contact {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
        
        .barcode {
            text-align: center;
            margin: 10px 0;
            font-family: 'Libre Barcode 39', monospace;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <div class="shop-name">
            {{ session('pos_shop_name') ?? 'SHOP' }}
        </div>
    </div>
    
    <div class="receipt-info">
        <div class="receipt-number">
            RECEIPT #: {{ $receipt_number ?? Session::get('pos_last_sale.receipt_number') }}
        </div>
        <div>
            DATE: {{ $sold_at ?? Session::get('pos_last_sale.sold_at') }}
        </div>
    </div>


    @if(isset($cart))
        {{-- Unified cart receipt --}}
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="quantity">Qty</th>
                    <th class="price">Price</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $item)
                <tr>
                    <td class="item-name">{{ $item['product_name'] }}</td>
                    <td class="quantity">{{ $item['quantity'] }}</td>
                    <td class="price">UGX {{ number_format($item['price']) }}</td>
                    <td class="total">UGX {{ number_format($item['total']) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>TOTAL AMOUNT</strong></td>
                    <td class="total grand-total">UGX {{ number_format($total) }}</td>
                </tr>
            </tfoot>
        </table>
    @elseif(isset($product) && isset($qty))
        {{-- Single product receipt --}}
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="quantity">Qty</th>
                    <th class="price">Price</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="item-name">{{ $product->name }}</td>
                    <td class="quantity">{{ $qty }}</td>
                    <td class="price">UGX {{ number_format($product->price) }}</td>
                    <td class="total">UGX {{ number_format($product->price * $qty) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>TOTAL AMOUNT</strong></td>
                    <td class="total grand-total">UGX {{ number_format($total) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p style="text-align: center; color: #999;">No items to display.</p>
    @endif

    <div class="footer">
        <div class="thank-you">
            THANK YOU FOR YOUR BUSINESS!
        </div>
        
        <div style="margin-top: 8px; font-size: 9px; color: #888;">
            {{ date('Y') }} &copy; {{ session('pos_shop_name') ?? 'Redvers E Mobility' }}
        </div>
    </div>

    {{-- Print button for non-print view --}}
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="
            background: #2c3e50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        ">
            🖨️ Print Receipt
        </button>
        <button onclick="window.close()" style="
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        ">
            ❌ Close
        </button>
    </div>

    <script>
        // Auto-print when page loads (optional)
        window.onload = function() {
            // Uncomment the line below if you want auto-print
            // window.print();
        };
    </script>
</body>
</html>
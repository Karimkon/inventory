<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <style>
        body { font-family: monospace; font-size: 14px; padding: 20px; }
        .receipt { max-width: 300px; margin: 0 auto; }
        .center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="center">
            <h3>{{ session('pos_shop_name') }}</h3>
            <p>POS Receipt</p>
        </div>
        
        <div class="divider"></div>
        
        <p><strong>Product:</strong> {{ $product->name }}</p>
        <p><strong>Quantity:</strong> {{ $qty }}</p>
        <p><strong>Price:</strong> UGX {{ number_format($product->price) }}</p>
        
        <div class="divider"></div>
        
        <p><strong>TOTAL: UGX {{ number_format($total) }}</strong></p>
        
        <div class="divider"></div>
        
        <p class="center">Thank you for your purchase!</p>
        <p class="center">{{ now()->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>
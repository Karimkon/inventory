<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SN General Hardware Receipt</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 280px; /* typical thermal printer width */
            margin: 0;
            padding: 0;
        }
        .header, .footer {
            text-align: center;
        }
        .header h2 {
            margin: 0;
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
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SN General Hardware</h2>
        <p>Nakirebe Branch</p>
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
            <td>{{ number_format($product->price,2) }}</td>
            <td>{{ number_format($total,2) }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <table class="totals">
        <tr>
            <td>Total</td>
            <td>{{ number_format($total,2) }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>SN General Hardware - Nakirebe</p>
    </div>
</body>
</html>

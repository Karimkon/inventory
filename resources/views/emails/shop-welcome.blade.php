<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Redvers Shopflow</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .credentials { background: #fff; border: 2px dashed #667eea; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .login-btn { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to Redvers Shopflow!</h1>
            <p>Your shop has been successfully activated</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->name }},</h2>
            
            <p>Congratulations! Your shop <strong>{{ $shop->name }}</strong> has been approved and is now active on our platform.</p>
            
            <div class="credentials">
                <h3>🔐 Your Login Credentials:</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
                
                <p><strong>Shop Login URL:</strong> <a href="{{ url('/shop/login') }}">{{ url('/shop/login') }}</a></p>

                <p><strong>POS Login URL:</strong> <a href="{{ url('https://redversshopflow.shop/') }}">{{ url('/shop/login') }}</a></p>

                <p><strong>POS Access PIN:</strong> <code>{{ $pin }}</code></p>
            </div>

            <p><strong>Important Security Notes:</strong></p>
            <ul>
                <li>Keep your login credentials secure</li>
                <li>Change your password after first login</li>
                <li>Never share your password with anyone</li>
            </ul>

            <a href="{{ url('/shop/login') }}" class="login-btn">Login to Your Shop</a>

            <h3>📋 What You Can Do:</h3>
            <ul>
                <li>Manage your products and inventory</li>
                <li>Track sales and generate reports</li>
                <li>Manage expenses and loans</li>
                <li>View analytics and insights</li>
            </ul>

            <p>If you have any questions or need assistance, please contact our support team:</p>
            <p>📞 Phone: +256-707208954 or +256-741613506<br>
               ✉️ Email: redversemobility@gmail.com</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Redvers Shopflow. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
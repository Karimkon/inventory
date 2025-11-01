<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Redvers ShopFlow Uganda - Admin Portal</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            background: linear-gradient(-45deg, #0f172a, #1e293b, #334155, #0f172a);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            color: #f1f5f9;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
        }
        
        .hero-section {
            text-align: center;
            max-width: 1200px;
            width: 100%;
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-container {
            margin-bottom: 2rem;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .main-title {
            font-size: clamp(2rem, 5vw, 4rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #60a5fa, #a78bfa, #60a5fa);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3s linear infinite;
        }
        
        @keyframes shimmer {
            to { background-position: 200% center; }
        }
        
        .subtitle {
            font-size: clamp(1.25rem, 3vw, 2rem);
            font-weight: 600;
            color: #bfdbfe;
            margin-bottom: 1rem;
        }
        
        .description {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: #cbd5e1;
            margin-bottom: 0.5rem;
            line-height: 1.6;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .highlight-text {
            color: #67e8f9;
            font-weight: 600;
            display: block;
            margin-top: 0.5rem;
        }
        
        .section-title {
            font-size: clamp(1.25rem, 2.5vw, 1.75rem);
            font-weight: 700;
            color: #bfdbfe;
            margin: 3rem 0 2rem;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 900px;
            margin: 0 auto 3rem;
            padding: 0 1rem;
        }
        
        .role-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }
        
        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border-color: rgba(148, 163, 184, 0.4);
        }
        
        .role-card:hover::before {
            opacity: 1;
        }
        
        .role-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .role-card:hover .role-icon {
            transform: scale(1.1);
        }
        
        .role-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .role-description {
            font-size: 0.95rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .role-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .admin-card .role-title { color: #c4b5fd; }
        .admin-card .role-button {
            background: #7c3aed;
            color: white;
        }
        .admin-card:hover .role-button {
            background: #6d28d9;
            transform: scale(1.05);
        }
        
        .shop-card .role-title { color: #86efac; }
        .shop-card .role-button {
            background: #10b981;
            color: white;
        }
        .shop-card:hover .role-button {
            background: #059669;
            transform: scale(1.05);
        }
        
        .onboard-card .role-title { color: #fcd34d; }
        .onboard-card .role-button {
            background: #f59e0b;
            color: white;
        }
        .onboard-card:hover .role-button {
            background: #d97706;
            transform: scale(1.05);
        }
        
        .onboard-section {
            max-width: 500px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .status-link {
            display: inline-block;
            margin-top: 1rem;
            color: #67e8f9;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }
        
        .status-link:hover {
            color: #a5f3fc;
        }
        
        .footer {
            text-align: center;
            margin-top: 4rem;
            animation: fadeInUp 1s ease-out 0.3s backwards;
        }
        
        .footer-text {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        
        .footer-subtext {
            font-size: 0.8rem;
            color: #64748b;
        }
        
        @media (max-width: 640px) {
            .logo-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
            
            .role-card {
                padding: 1.5rem;
            }
        }
        
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(96, 165, 250, 0.3);
            border-radius: 50%;
            animation: floatParticle 20s infinite ease-in-out;
        }


        .pos-card .role-title { color: #7dd3fc; }
.pos-card .role-button {
    background: #0ea5e9;
    color: white;
}
.pos-card:hover .role-button {
    background: #0284c7;
    transform: scale(1.05);
}

.form-control {
    transition: all 0.3s ease;
}
.form-control:focus {
    outline: none;
    border-color: #0ea5e9 !important;
    box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
}
        
        @keyframes floatParticle {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translate(100px, -100vh) scale(0);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="particles" id="particles"></div>
    
    <div class="container">
        <div class="hero-section">
            <div class="logo-container">
                <div class="logo-icon">📊</div>
                <h1 class="main-title">Redvers ShopFlow Uganda</h1>
                <h2 class="subtitle">Smart Inventory & POS Management</h2>
            </div>
            
            <div>
                <p class="description">
                    Complete business management solution for retail, wholesale & hardware shops 🏪📈
                </p>
                <span class="highlight-text">Streamline operations, maximize profits ✨</span>
            </div>
            
            <div>
                <h3 class="section-title">Admin Access</h3>
                
                <div class="cards-grid">
                    <a href="{{ url('admin/login') }}" class="role-card admin-card">
                        <div class="role-icon">👑</div>
                        <h4 class="role-title">System Admin</h4>
                        <p class="role-description">Manage all shops, users & system configuration</p>
                        <div class="role-button">Admin Login</div>
                    </a>
                    
                    <a href="{{ url('shop/login') }}" class="role-card shop-card">
                        <div class="role-icon">🏪</div>
                        <h4 class="role-title">Shop Manager</h4>
                        <p class="role-description">POS, inventory, sales & financial reports</p>
                        <div class="role-button">Shop Login</div>
                    </a>
                </div>
            </div>
            
            <div class="onboard-section">
                <h3 class="section-title">Want to Join Our Platform?</h3>
                
                <a href="{{ route('onboarding.show') }}" class="role-card onboard-card">
                    <div class="role-icon">🚀</div>
                    <h4 class="role-title">Start Your Business</h4>
                    <p class="role-description">Join ShopFlow & digitize your shop operations today</p>
                    <div class="role-button">Get Started</div>
                </a>
                
                <a href="{{ route('onboarding.status') }}" class="status-link">
                    Check Application Status →
                </a>
            </div>

           <!-- Add this after the existing cards grid, around line 180 -->
<div class="onboard-section">
    <h3 class="section-title">Employee POS Access</h3>
    
    <div class="role-card pos-card">
        <div class="role-icon">💳</div>
        <h4 class="role-title">POS Terminal</h4>
        <p class="role-description">Secure access for shop employees</p>
        
    <form action="{{ route('pos.login.submit') }}" method="POST" class="mt-3" autocomplete="off">
        @csrf
        <!-- Hidden dummy fields to block autofill -->
        <input type="text" style="display:none">
        <input type="password" style="display:none">

        <!-- Real input fields with unique names -->
        <input type="text" name="shop_identifier" placeholder="Enter Shop ID or Name" 
       class="form-control" autocomplete="new-password" required>

<input type="password" name="pos_pin" placeholder="Enter POS PIN" maxlength="4" 
       class="form-control" autocomplete="new-password" required>


        <button type="submit" class="role-button">🔐 Access POS Terminal</button>
    </form>

    </div>
</div> 
            <div class="footer">
                <div class="footer-text">© 2025 ShopFlow Uganda. All rights reserved.</div>
                <div class="footer-subtext">Empowering businesses with smart management tools 📊</div>
            </div>
        </div>
    </div>
    
    <script>
        // Create floating particles
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }
    </script>
</body>
</html>
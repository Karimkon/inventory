<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'SN Shop Dashboard')</title>

<!-- Bootstrap + Icons + Fonts -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

<style>
:root {
    --shop-primary: #10b981;  /* Green theme for shop */
    --shop-primary-hover: #059669;
    --sidebar-bg: #1f2937;
    --sidebar-text: #e5e7eb;
    --sidebar-hover: #fff;
    --bg: #f8f9fa;
    --card-bg: #ffffff;
    --card-shadow: rgba(0,0,0,0.05);
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--bg);
    margin: 0;
    color: #212529;
}

/* Sidebar */
.sidebar {
    width: 260px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: var(--sidebar-bg);
    color: var(--sidebar-text);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    z-index: 1000;
    overflow-y: auto;
    box-shadow: 2px 0 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    padding-top: 1rem;
}

.sidebar-logo {
    text-align: center;
    font-weight: 700;
    font-size: 1.2rem;
    padding: 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    color: var(--shop-primary);
}

.shop-badge {
    background: var(--shop-primary);
    color: white;
    font-size: 0.7rem;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    margin-left: 0.5rem;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    color: var(--sidebar-text);
    padding: 0.9rem 1.2rem;
    border-radius: 8px;
    margin: 0.4rem 0;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.3s ease;
}
.sidebar a:hover, .sidebar a.active {
    background: var(--shop-primary);
    color: var(--sidebar-hover);
    transform: translateX(4px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Shop Info Section */
.shop-info {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 8px;
    padding: 1rem;
    margin: 1rem;
    text-align: center;
}

.shop-name {
    font-weight: 600;
    color: var(--shop-primary);
    margin-bottom: 0.5rem;
}

.shop-role {
    font-size: 0.8rem;
    color: #9ca3af;
}

/* Sidebar Footer */
.sidebar-footer {
    margin-top: auto;
    padding: 1rem;
}
.logout-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem;
    border-radius: 8px;
    background: #6b7280;
    color: #fff;
    border: none;
    font-weight: 500;
    transition: all 0.3s ease;
}
.logout-button:hover { 
    background: #4b5563; 
}

/* Main Content */
.content {
    margin-left: 260px;
    padding: 2rem;
    transition: margin-left 0.3s ease;
}

/* Cards */
.card {
    background: var(--card-bg);
    box-shadow: 0 4px 12px var(--card-shadow);
    border-radius: 12px;
    padding: 1rem;
}

/* Mobile */
@media(max-width:1024px){ 
    .sidebar { width:240px; } 
    .content{margin-left:240px;} 
}
@media(max-width:768px){
    .sidebar { left:-260px; }
    .sidebar.active { left:0; box-shadow:4px 0 20px rgba(0,0,0,0.3); }
    .content { margin-left:0; padding:1rem; }
    .mobile-toggle {
        position: fixed;
        top: 10px;
        left: 10px;
        font-size: 1.8rem;
        color: var(--shop-primary);
        z-index: 1100;
        cursor: pointer;
    }
}

/* Quick Stats in Sidebar */
.quick-stats {
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
    padding: 0.8rem;
    margin: 1rem;
    font-size: 0.85rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.3rem;
}

.stat-value {
    color: var(--shop-primary);
    font-weight: 600;
}
</style>
</head>
<body>

<!-- Mobile toggle -->
<div class="mobile-toggle d-md-none"><i class="bi bi-list"></i></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div>Inventory MGT <span class="shop-badge">SHOP</span></div>
    </div>

    <!-- Shop Info -->
    <div class="shop-info">
        <div class="shop-name">{{ Auth::user()->shop->name ?? 'My Shop' }}</div>
        <div class="shop-role">Shop User</div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="stat-item">
            <span>Products:</span>
            <span class="stat-value">{{ Auth::user()->shop->products->count() ?? 0 }}</span>
        </div>
        <div class="stat-item">
            <span>Today's Sales:</span>
            <span class="stat-value">UGX 0</span>
        </div>
    </div>

    <div class="sidebar-content">
        <a href="{{ route('shop.dashboard') }}" class="{{ request()->routeIs('shop.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="{{ route('shop.products.index') }}" class="{{ request()->routeIs('shop.products.index') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>Sell Products
        </a>

        <a href="{{ route('shop.expenses.index') }}" class="{{ request()->routeIs('shop.expenses.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Expenses
        </a>

        <a href="{{ route('shop.reports.index') }}" class="{{ request()->routeIs('shop.reports.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i> Financial Reports
        </a>

        <a href="#" class="">
            <i class="bi bi-receipt"></i> Today's Receipts
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="text-center text-muted small mb-2">
            {{ Auth::user()->name }}<br>
            <small>{{ Auth::user()->email }}</small>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Main Content -->
<main class="content">
    @yield('content')
</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggle = document.querySelector('.mobile-toggle');
    const sidebar = document.getElementById('sidebar');
    toggle.addEventListener('click', () => sidebar.classList.toggle('active'));
</script>
</body>
</html>
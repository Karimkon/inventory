@extends('shop.layouts.app')

@section('title', 'Shop Dashboard - Redvers Shopflow')

@section('content')
<div class="dashboard-container">
    <!-- Header Section -->
    <div class="dashboard-header mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="dashboard-title">
                    <i class="bi bi-shop-window"></i> Shop Dashboard
                    <span class="badge bg-success align-middle ms-3 fs-6 pulse-badge">
                        <i class="bi bi-activity me-1"></i>Live
                    </span>
                </h1>
                <p class="dashboard-subtitle text-muted mt-2">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Real-time inventory management and sales analytics
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="date-card">
                    <i class="bi bi-calendar-event me-2"></i>
                    <strong>{{ now()->format('l, M d, Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="stats-grid mb-5">
        <div class="row g-4">
            <!-- Total Products Card -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stat-card stat-card-purple">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="bi bi-box-seam-fill"></i>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ number_format($totalProducts) }}</div>
                            <div class="stat-label">Total Products</div>
                            <div class="stat-badge success-badge">
                                <i class="bi bi-arrow-up"></i> Active Inventory
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Value Card -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stat-card stat-card-green">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="bi bi-coin"></i>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ number_format($expectedRevenue) }}</div>
                            <div class="stat-label">Inventory Value (UGX)</div>
                            <div class="stat-badge profit-badge">
                                <i class="bi bi-graph-up"></i> Profit: {{ number_format($potentialProfit) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Sales Card -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stat-card stat-card-blue">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="bi bi-cart-check-fill"></i>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ number_format($salesToday) }}</div>
                            <div class="stat-label">Today's Sales</div>
                            <div class="stat-badge info-badge">
                                <i class="bi bi-cash-stack"></i> {{ number_format($profitToday) }} UGX
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Sales Card -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stat-card stat-card-orange">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="bi bi-calendar-week-fill"></i>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ number_format($salesWeek) }}</div>
                            <div class="stat-label">This Week's Sales</div>
                            <div class="stat-badge warning-badge">
                                <i class="bi bi-trophy-fill"></i> {{ number_format($profitWeek) }} UGX
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Left Column: Charts & Recent Products -->
        <div class="col-lg-8">
            <!-- Sales Chart Section -->
            <div class="content-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="bi bi-bar-chart-line-fill text-primary"></i>
                        Sales Performance
                    </h5>
                    <select class="form-select form-select-sm chart-select" id="chartPeriod" onchange="updateChart()">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 3 months</option>
                    </select>
                </div>
                <div class="card-body-custom">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Recent Products Table -->
            <div class="content-card">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="bi bi-clock-history text-primary"></i>
                        Recent Products
                    </h5>
                    <a href="{{ route('shop.products.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-right-circle me-1"></i> View All
                    </a>
                </div>
                <div class="card-body-custom p-0">
                    <div class="table-responsive">
                        <table class="table table-hover products-table mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-end">Cost Price</th>
                                    <th class="text-end">Selling Price</th>
                                    <th class="text-end">Profit/Unit</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProducts as $product)
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <div class="product-icon-box">
                                                <i class="bi bi-box"></i>
                                            </div>
                                            <div>
                                                <div class="product-name">{{ $product->name }}</div>
                                                <small class="product-sku">SKU: #{{ $product->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="stock-pill stock-{{ $product->stock > 10 ? 'high' : ($product->stock > 0 ? 'low' : 'out') }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="text-end price-text">{{ number_format($product->cost_price) }}</td>
                                    <td class="text-end price-text fw-bold">{{ number_format($product->price) }}</td>
                                    <td class="text-end">
                                        <span class="profit-pill">
                                            +{{ number_format($product->price - $product->cost_price) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($product->stock == 0)
                                            <span class="status-badge status-danger">
                                                <i class="bi bi-x-circle"></i> Out
                                            </span>
                                        @elseif($product->stock <= 5)
                                            <span class="status-badge status-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Low
                                            </span>
                                        @else
                                            <span class="status-badge status-success">
                                                <i class="bi bi-check-circle"></i> Available
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox display-4 text-muted"></i>
                                            <p class="mt-3 text-muted">No products added yet</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Alerts & Quick Actions -->
        <div class="col-lg-4">
          <!-- Stock Alerts -->
@if($lowStockProducts > 0 || $outOfStockProducts > 0)
<div class="content-card mb-4">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="card-title-custom">
            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
            Stock Alerts
        </h5>
        <a href="{{ route('shop.products.low-stock-report') }}" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-clipboard-data me-1"></i> View Detailed Report
        </a>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <!-- Critical Stock Alert -->
            @if($criticalStockProducts > 0)
            <div class="col-md-6">
                <div class="alert-box alert-danger">
                    <div class="alert-icon">
                        <i class="bi bi-fire"></i>
                    </div>
                    <div class="alert-content">
                        <h6>Critical Stock Alert! 🚨</h6>
                        <p>{{ $criticalStockProducts }} products with 2 or less items left</p>
                        <a href="{{ route('shop.products.low-stock-report', ['type' => 'critical']) }}" class="alert-link">
                            View critical items <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Out of Stock Alert -->
            @if($outOfStockProducts > 0)
            <div class="col-md-6">
                <div class="alert-box alert-dark">
                    <div class="alert-icon">
                        <i class="bi bi-x-octagon-fill"></i>
                    </div>
                    <div class="alert-content">
                        <h6>Out of Stock</h6>
                        <p>{{ $outOfStockProducts }} products need immediate restocking</p>
                        <a href="{{ route('shop.products.low-stock-report', ['type' => 'out']) }}" class="alert-link">
                            View out of stock <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Low Stock Alert -->
            @if($lowStockProducts > 0)
            <div class="col-md-6">
                <div class="alert-box alert-warning">
                    <div class="alert-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="alert-content">
                        <h6>Low Stock Warning</h6>
                        <p>{{ $lowStockProducts }} products running low on inventory</p>
                        <a href="{{ route('shop.products.low-stock-report', ['type' => 'low']) }}" class="alert-link">
                            View low stock <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Summary -->
            <div class="col-md-6">
                <div class="stock-summary-card">
                    <h6 class="summary-title">Quick Stock Summary</h6>
                    <div class="summary-stats">
                        <div class="stat-item">
                            <span class="stat-label">Total Products:</span>
                            <span class="stat-value">{{ $totalProducts }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label text-warning">Low Stock:</span>
                            <span class="stat-value">{{ $lowStockProducts }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label text-danger">Critical:</span>
                            <span class="stat-value">{{ $criticalStockProducts }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label text-dark">Out of Stock:</span>
                            <span class="stat-value">{{ $outOfStockProducts }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Low Stock Items -->
        @if($lowStockItems->count() > 0)
        <div class="mt-4">
            <h6 class="list-title mb-3">
                <i class="bi bi-clock-history me-2"></i>Items Needing Immediate Attention
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Current Stock</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockItems as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-box-seam me-2 text-muted"></i>
                                    <span>{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $product->stock == 0 ? 'dark' : ($product->stock <= 2 ? 'danger' : 'warning') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($product->stock == 0)
                                    <span class="badge bg-dark">Out of Stock</span>
                                @elseif($product->stock <= 2)
                                    <span class="badge bg-danger">Critical</span>
                                @else
                                    <span class="badge bg-warning">Low Stock</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('shop.products.edit', $product) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-up-circle"></i> Restock
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endif
            <!-- Quick Actions -->
            <div class="content-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="bi bi-lightning-charge-fill text-warning"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="action-buttons">
                        <a href="{{ route('shop.products.create') }}" class="action-btn action-btn-primary">
                            <i class="bi bi-plus-circle-fill"></i>
                            <div>
                                <div class="action-title">Add Product</div>
                                <small>Create new item</small>
                            </div>
                        </a>
                        <a href="{{ route('shop.products.index') }}" class="action-btn action-btn-outline">
                            <i class="bi bi-box-seam"></i>
                            <div>
                                <div class="action-title">All Products</div>
                                <small>Manage inventory</small>
                            </div>
                        </a>
                        <a href="{{ route('shop.expenses.create') }}" class="action-btn action-btn-outline">
                            <i class="bi bi-cash-coin"></i>
                            <div>
                                <div class="action-title">Record Expense</div>
                                <small>Track spending</small>
                            </div>
                        </a>
                        <a href="{{ route('shop.reports.index') }}" class="action-btn action-btn-outline">
                            <i class="bi bi-graph-up-arrow"></i>
                            <div>
                                <div class="action-title">View Reports</div>
                                <small>Analytics & insights</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="content-card financial-summary">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="bi bi-calculator-fill text-success"></i>
                        Financial Summary
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="summary-item">
                        <div class="summary-icon bg-primary">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Inventory Value</div>
                            <div class="summary-value">UGX {{ number_format($expectedRevenue) }}</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon bg-success">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Potential Profit</div>
                            <div class="summary-value">UGX {{ number_format($potentialProfit) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
/* ==================== Global Variables ==================== */
:root {
    --primary: #6366f1;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --info: #3b82f6;
    --dark: #1e293b;
    --light: #f8fafc;
    --purple: #8b5cf6;
    --orange: #f97316;
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --radius: 12px;
    --radius-lg: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ==================== Dashboard Container ==================== */
.dashboard-container {
    padding: 2rem 0;
    max-width: 1400px;
    margin: 0 auto;
}

/* ==================== Header Styles ==================== */
.dashboard-title {
    font-size: 2.25rem;
    font-weight: 800;
    color: var(--dark);
    letter-spacing: -0.025em;
    margin-bottom: 0.5rem;
}

.dashboard-subtitle {
    font-size: 1rem;
    color: #64748b;
    font-weight: 500;
}

.pulse-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.date-card {
    background: white;
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    border: 1px solid #e2e8f0;
    display: inline-block;
    font-size: 0.95rem;
}

/* ==================== Stat Cards ==================== */
.stat-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    height: 100%;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    box-shadow: var(--shadow);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    transition: var(--transition);
}

.stat-card-purple::before { background: linear-gradient(90deg, var(--purple), #a78bfa); }
.stat-card-green::before { background: linear-gradient(90deg, var(--success), #34d399); }
.stat-card-blue::before { background: linear-gradient(90deg, var(--info), #60a5fa); }
.stat-card-orange::before { background: linear-gradient(90deg, var(--orange), #fb923c); }

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.stat-card:hover::before {
    height: 6px;
}

.stat-card-body {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.stat-icon-wrapper {
    position: relative;
    flex-shrink: 0;
}

.stat-icon {
    width: 70px;
    height: 70px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    position: relative;
    z-index: 1;
}

.stat-card-purple .stat-icon { background: linear-gradient(135deg, var(--purple), #a78bfa); }
.stat-card-green .stat-icon { background: linear-gradient(135deg, var(--success), #34d399); }
.stat-card-blue .stat-icon { background: linear-gradient(135deg, var(--info), #60a5fa); }
.stat-card-orange .stat-icon { background: linear-gradient(135deg, var(--orange), #fb923c); }

.stat-glow {
    position: absolute;
    inset: -10px;
    border-radius: var(--radius);
    opacity: 0;
    transition: var(--transition);
    z-index: 0;
}

.stat-card-purple .stat-glow { background: radial-gradient(circle, rgba(139, 92, 246, 0.3), transparent); }
.stat-card-green .stat-glow { background: radial-gradient(circle, rgba(16, 185, 129, 0.3), transparent); }
.stat-card-blue .stat-glow { background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent); }
.stat-card-orange .stat-glow { background: radial-gradient(circle, rgba(249, 115, 22, 0.3), transparent); }

.stat-card:hover .stat-glow {
    opacity: 1;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1rem;
    font-weight: 800;
    color: var(--dark);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.success-badge {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.profit-badge {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.info-badge {
    background: rgba(59, 130, 246, 0.1);
    color: var(--info);
}

.warning-badge {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

/* ==================== Content Cards ==================== */
.content-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.card-title-custom {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-body-custom {
    padding: 1.5rem;
}

.chart-select {
    width: auto;
    min-width: 150px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

/* ==================== Products Table ==================== */
.products-table {
    font-size: 0.875rem;
}

.products-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.products-table thead th {
    font-weight: 700;
    color: #64748b;
    padding: 1rem;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}

.products-table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.products-table tbody tr:hover {
    background: #f8fafc;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.product-icon-box {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 1.25rem;
}

.product-name {
    font-weight: 600;
    color: var(--dark);
}

.product-sku {
    color: #94a3b8;
    font-size: 0.75rem;
}

.stock-pill {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.75rem;
}

.stock-high {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.stock-low {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.stock-out {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.price-text {
    font-size: 0.875rem;
    color: #64748b;
}

.profit-pill {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    font-weight: 600;
    font-size: 0.75rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.status-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.status-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

/* ==================== Alert Boxes ==================== */
.alert-box {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: var(--radius);
    border: 1px solid;
}

.alert-danger {
    background: rgba(239, 68, 68, 0.05);
    border-color: rgba(239, 68, 68, 0.2);
}

.alert-warning {
    background: rgba(245, 158, 11, 0.05);
    border-color: rgba(245, 158, 11, 0.2);
}

.alert-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.alert-danger .alert-icon {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.alert-warning .alert-icon {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.alert-content h6 {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: var(--dark);
}

.alert-content p {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.alert-link {
    color: var(--primary);
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: var(--transition);
}

.alert-link:hover {
    gap: 0.5rem;
}

.stock-items-list {
    background: #f8fafc;
    border-radius: var(--radius);
    padding: 1rem;
    margin-top: 1rem;
}

.list-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.75rem;
}

.stock-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: white;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: var(--transition);
}

.stock-item:hover {
    background: #f1f5f9;
}

.stock-item:last-child {
    margin-bottom: 0;
}

.item-info {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--dark);
}

.item-stock {
    padding: 0.25rem 0.625rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.75rem;
}

/* ==================== Action Buttons ==================== */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-radius: var(--radius);
    text-decoration: none;
    transition: var(--transition);
    border: 2px solid;
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    transition: var(--transition);
}

.action-btn:hover::before {
    left: 0;
}

.action-btn i {
    font-size: 1.5rem;
    flex-shrink: 0;
    z-index: 1;
}

.action-btn > div {
    flex: 1;
    z-index: 1;
}

.action-title {
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 0.125rem;
}

.action-btn small {
    font-size: 0.75rem;
    opacity: 0.8;
}

.action-btn-primary {
    background: linear-gradient(135deg, var(--primary), #818cf8);
    color: white;
    border-color: transparent;
}

.action-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
}

.action-btn-outline {
    background: white;
    color: var(--dark);
    border-color: #e2e8f0;
}

.action-btn-outline::before {
    background: #f8fafc;
}

.action-btn-outline:hover {
    transform: translateY(-2px);
    border-color: var(--primary);
    color: var(--primary);
}

/* ==================== Financial Summary ==================== */
.financial-summary {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: var(--radius);
    margin-bottom: 1rem;
    box-shadow: var(--shadow-sm);
}

.summary-item:last-child {
    margin-bottom: 0;
}

.summary-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.summary-content {
    flex: 1;
}

.summary-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.summary-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--dark);
}

/* Enhanced Stock Alert Styles */
.alert-dark {
    background: rgba(30, 41, 59, 0.05);
    border: 1px solid rgba(30, 41, 59, 0.2);
    color: #1e293b;
}

.alert-dark .alert-icon {
    background: rgba(30, 41, 59, 0.1);
    color: #1e293b;
}

.stock-summary-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    height: 100%;
}

.summary-title {
    font-weight: 600;
    color: #475569;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
}

.stat-value {
    font-weight: 700;
    font-size: 1.1rem;
}

/* ==================== Empty State ==================== */
.empty-state {
    padding: 2rem;
}

.empty-state i {
    opacity: 0.2;
}

.empty-state p {
    margin: 0;
    font-size: 0.95rem;
}

/* ==================== Chart Styles ==================== */
#salesChart {
    max-height: 350px;
}

/* ==================== Responsive Design ==================== */
@media (max-width: 1200px) {
    .dashboard-title {
        font-size: 2rem;
    }
}

@media (max-width: 992px) {
    .stat-card-body {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        font-size: 1.75rem;
    }
    
    .stat-value {
        color: var(--shop-primary);
        font-weight: 600;
        font-size: 0.9rem;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem 0;
    }
    
    .dashboard-title {
        font-size: 1.75rem;
    }
    
    .date-card {
        margin-top: 1rem;
    }
    
    .card-header-custom {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .chart-select {
        width: 100%;
    }
}

/* ==================== Animations ==================== */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card {
    animation: slideInUp 0.5s ease-out backwards;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

.content-card {
    animation: slideInUp 0.5s ease-out backwards;
    animation-delay: 0.5s;
}

/* ==================== Print Styles ==================== */
@media print {
    .action-buttons,
    .chart-select,
    .btn {
        display: none !important;
    }
    
    .stat-card,
    .content-card {
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
        page-break-inside: avoid;
    }
}
</style>

<script>
// ==================== Chart Configuration ====================
document.addEventListener('DOMContentLoaded', function() {
    initSalesChart();
    animateStatValues();
});

function initSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient1.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
    gradient1.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient2.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
    gradient2.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    window.salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales (Units)',
                data: [12, 19, 15, 25, 22, 30, 28],
                borderColor: '#6366f1',
                backgroundColor: gradient1,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#6366f1',
                pointHoverBorderWidth: 3
            }, {
                label: 'Profit (UGX)',
                data: [50000, 85000, 62000, 95000, 88000, 120000, 110000],
                borderColor: '#10b981',
                backgroundColor: gradient2,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#10b981',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#e2e8f0',
                    padding: 16,
                    cornerRadius: 12,
                    titleFont: {
                        size: 14,
                        weight: '700'
                    },
                    bodyFont: {
                        size: 13,
                        weight: '500'
                    },
                    displayColors: true,
                    borderColor: 'rgba(226, 232, 240, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.dataset.label.includes('Profit')) {
                                    label += 'UGX ' + context.parsed.y.toLocaleString();
                                } else {
                                    label += context.parsed.y + ' units';
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        color: '#64748b'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(226, 232, 240, 0.5)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        color: '#64748b',
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function animateStatValues() {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach((el, index) => {
        const text = el.textContent;
        const numbers = text.match(/[\d,]+/);
        
        if (numbers) {
            const finalValue = parseInt(numbers[0].replace(/,/g, ''));
            let currentValue = 0;
            const increment = finalValue / 50;
            const duration = 1000;
            const stepTime = duration / 50;
            
            el.textContent = '0';
            
            setTimeout(() => {
                const counter = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        el.textContent = text;
                        clearInterval(counter);
                    } else {
                        el.textContent = Math.floor(currentValue).toLocaleString();
                    }
                }, stepTime);
            }, index * 100);
        }
    });
}

function updateChart() {
    const period = document.getElementById('chartPeriod').value;
    showNotification(`Chart updated for last ${period} days`, 'info');
    // Add your AJAX call here to fetch new data
}

function showNotification(message, type = 'info') {
    const colors = {
        info: '#3b82f6',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444'
    };
    
    const icons = {
        info: 'info-circle-fill',
        success: 'check-circle-fill',
        warning: 'exclamation-triangle-fill',
        danger: 'x-circle-fill'
    };
    
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border-left: 4px solid ${colors[type]};
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 350px;
        animation: slideInRight 0.3s ease;
    `;
    
    notification.innerHTML = `
        <i class="bi bi-${icons[type]}" style="font-size: 1.5rem; color: ${colors[type]};"></i>
        <span style="flex: 1; font-weight: 600; color: #1e293b;">${message}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.25rem;">
            <i class="bi bi-x"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Add slide animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection
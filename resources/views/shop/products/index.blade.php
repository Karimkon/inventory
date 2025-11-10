@extends('shop.layouts.app')

@section('title', 'Point of Sale - Quick Selling')

@section('content')
<div class="pos-container">
    <!-- Quick Stats Header -->
    <div class="stats-header mb-4">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="stat-box stat-primary">
                    <div class="stat-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $products->total() }}</div>
                        <div class="stat-label">Total Products</div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-arrow-up"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-box stat-success">
                    <div class="stat-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $products->where('stock', '>', 0)->count() }}</div>
                        <div class="stat-label">In Stock</div>
                    </div>
                    <div class="stat-trend success">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-box stat-warning">
                    <div class="stat-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $products->where('stock', '<=', 5)->where('stock', '>', 0)->count() }}</div>
                        <div class="stat-label">Low Stock</div>
                    </div>
                    <div class="stat-trend warning">
                        <i class="bi bi-dash"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-box stat-danger">
                    <div class="stat-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $products->where('stock', 0)->count() }}</div>
                        <div class="stat-label">Out of Stock</div>
                    </div>
                    <div class="stat-trend danger">
                        <i class="bi bi-arrow-down"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="custom-alert custom-alert-success">
            <div class="alert-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="alert-content">
                <strong>Success!</strong>
                <p>{{ session('success') }}</p>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif

    @if(session('last_sale'))
        @php $sale = session('last_sale'); @endphp
        <div class="custom-alert custom-alert-info">
            <div class="alert-icon">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="alert-content">
                <strong>Sale Completed!</strong>
                <p>
                    @if($sale['type'] === 'multi')
                        {{ count($sale['items']) }} items sold
                    @else
                        {{ $sale['quantity'] }}x <b>{{ $sale['product_name'] }}</b>
                    @endif
                    • Total: <b>UGX {{ number_format($sale['type'] === 'multi' ? $sale['total_amount'] : $sale['total']) }}</b> 
                    • Profit: <b class="text-success">UGX {{ number_format($sale['type'] === 'multi' ? $sale['total_profit'] : $sale['profit']) }}</b>
                </p>
            </div>
            <a href="{{ route('shop.receipt.unified') }}" 
               class="btn btn-sm btn-light" target="_blank">
                <i class="bi bi-printer"></i> Print
            </a>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="sidebar-sticky">
                <!-- Quick Actions Card -->
                <div class="quick-actions-card mb-4">
                    <div class="card-header-pos">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <span>Quick Actions</span>
                    </div>
                    <div class="card-body-pos">
                        <div class="action-buttons-grid">
                            <a href="{{ route('shop.products.create') }}" class="action-btn-pos primary">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span>Add Product</span>
                            </a>
                            <a href="{{ route('shop.expenses.create') }}" class="action-btn-pos warning">
                                <i class="bi bi-cash-coin"></i>
                                <span>Add Expense</span>
                            </a>
                            <a href="{{ route('shop.reports.index') }}" class="action-btn-pos info">
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Reports</span>
                            </a>
                            <button onclick="clearCart()" class="action-btn-pos danger">
                                <i class="bi bi-trash3"></i>
                                <span>Clear Cart</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Today's Summary -->
                <div class="summary-card">
                    <div class="card-header-pos">
                        <i class="bi bi-calendar-check"></i>
                        <span>Today's Summary</span>
                    </div>
                    <div class="card-body-pos">
                        <div class="summary-item">
                            <span class="summary-label">Products Sold</span>
                            <span class="summary-value">0</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Revenue</span>
                            <span class="summary-value text-primary">UGX 0</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Profit</span>
                            <span class="summary-value text-success">UGX 0</span>
                        </div>
                    </div>
                </div>

                <!-- Shopping Cart -->
        <div class="cart-card mb-4" id="cartSection" style="display: none;">
                <div class="cart-header">
                    <div class="cart-title">
                        <i class="bi bi-cart3"></i>
                        <span>Shopping Cart</span>
                    </div>
                    <span class="cart-badge" id="cartCountBadge">0 items</span>
                </div>
                <div class="cart-body">
                    <div id="cartItems" class="cart-items-list">
                        <!-- Cart items will be loaded here -->
                    </div>
                    <div class="cart-footer">
                        <div class="cart-total">
                            <span>Total Amount:</span>
                            <span id="cartTotalAmount" class="total-value">UGX 0</span>
                        </div>
                        <div class="cart-actions">
                            <button class="btn-cart-action clear" onclick="clearCart()">
                                <i class="bi bi-trash3"></i> Clear Cart
                            </button>
                            <button class="btn-cart-action checkout" onclick="checkout()">
                                <i class="bi bi-credit-card"></i> Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
        
            <!-- Search & Filters -->
            <div class="search-card mb-4">
                <form class="search-form" method="GET" action="{{ route('shop.products.index') }}">
                    @csrf
                    <div class="search-input-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               class="search-input" 
                               placeholder="Search products by name..."
                               id="searchInput">
                        @if(request('search'))
                            <a href="{{ route('shop.products.index') }}" class="search-clear">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="search-submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
                
                @if(request('search'))
                <div class="active-filters">
                    <span class="filter-label">Active:</span>
                    <span class="filter-tag">
                        <i class="bi bi-search"></i> "{{ request('search') }}"
                        <a href="{{ route('shop.products.index') }}" class="filter-remove">×</a>
                    </span>
                </div>
                @endif
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                @foreach($products as $product)
                <div class="product-card-pos">
                    <!-- Stock Badge -->
                    <div class="product-badge badge-{{ $product->stock > 5 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                        <i class="bi bi-box"></i> {{ $product->stock }}
                    </div>
                    
                    <!-- Product Header -->
                    <div class="product-header-pos">
                        <h3 class="product-name-pos" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h3>
                    </div>

                    <!-- Product Pricing -->
                    <div class="product-pricing">
                        <div class="price-row">
                            <span class="price-label">Cost:</span>
                            <span class="price-value cost">{{ number_format($product->cost_price) }}</span>
                        </div>
                        <div class="price-row selling">
                            <span class="price-label">Price:</span>
                            <span class="price-value">{{ number_format($product->price) }}</span>
                        </div>
                        <div class="price-row profit">
                            <span class="price-label">Profit:</span>
                            <span class="price-value profit">{{ number_format($product->price - $product->cost_price) }}</span>
                        </div>
                    </div>

                    <!-- Product Actions -->
                    <div class="product-actions">
                        <form onsubmit="event.preventDefault(); addToCart({{ $product->id }}, this.quantity.value)" 
                              class="add-to-cart-form">
                            @csrf
                            <div class="quantity-control">
                                <input type="number" 
                                       name="quantity" 
                                       min="1" 
                                       max="{{ $product->stock }}"
                                       value="1"
                                       class="quantity-input"
                                       {{ $product->stock == 0 ? 'disabled' : '' }}>
                                <button type="submit" 
                                        class="btn-add-cart"
                                        {{ $product->stock == 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-cart-plus"></i> Add
                                </button>
                            </div>
                        </form>

                        @if($product->stock > 0)
                        <div class="quick-actions-row">
                            <button class="btn-quick-sell" 
                                    onclick="quickSell({{ $product->id }}, 1)">
                                <i class="bi bi-lightning-charge-fill"></i> Quick Sell
                            </button>
                            <a href="{{ route('shop.products.edit', $product) }}" 
                               class="btn-edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                        @else
                        <div class="out-of-stock-notice">
                            <i class="bi bi-exclamation-octagon"></i> Out of Stock
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="pagination-wrapper">
                {{ $products->links() }}
            </div>
            @endif

            <!-- Empty State -->
            @if($products->count() == 0)
            <div class="empty-state-pos">
                <div class="empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>No Products Found</h3>
                <p>
                    @if(request()->has('search'))
                        No products match your search criteria.
                    @else
                        You haven't added any products yet.
                    @endif
                </p>
                <a href="{{ route('shop.products.create') }}" class="btn-empty-action">
                    <i class="bi bi-plus-circle-fill"></i> Add Your First Product
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* ==================== Variables ==================== */
:root {
    --primary: #6366f1;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --dark: #1e293b;
    --light: #f8fafc;
    --gray: #64748b;
    --border: #e2e8f0;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ==================== Container ==================== */
.pos-container {
    padding: 1.5rem;
    max-width: 1600px;
    margin: 0 auto;
}

/* ==================== Stats Header ==================== */
.stat-box {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow);
    border: 2px solid transparent;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.stat-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    transition: var(--transition);
}

.stat-box:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-primary::before { background: linear-gradient(90deg, var(--primary), #818cf8); }
.stat-success::before { background: linear-gradient(90deg, var(--success), #34d399); }
.stat-warning::before { background: linear-gradient(90deg, var(--warning), #fbbf24); }
.stat-danger::before { background: linear-gradient(90deg, var(--danger), #f87171); }

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
    flex-shrink: 0;
}

.stat-primary .stat-icon { background: linear-gradient(135deg, var(--primary), #818cf8); }
.stat-success .stat-icon { background: linear-gradient(135deg, var(--success), #34d399); }
.stat-warning .stat-icon { background: linear-gradient(135deg, var(--warning), #fbbf24); }
.stat-danger .stat-icon { background: linear-gradient(135deg, var(--danger), #f87171); }

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1rem;
    font-weight: 800;
    color: var(--dark);
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray);
    font-weight: 600;
}

.stat-trend {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.05);
    color: var(--gray);
}

.stat-trend.success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
.stat-trend.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
.stat-trend.danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

/* ==================== Custom Alerts ==================== */
.custom-alert {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.custom-alert-success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
    border: 2px solid rgba(16, 185, 129, 0.3);
}

.custom-alert-info {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
    border: 2px solid rgba(59, 130, 246, 0.3);
}

.custom-alert .alert-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.custom-alert-success .alert-icon {
    background: rgba(16, 185, 129, 0.15);
    color: var(--success);
}

.custom-alert-info .alert-icon {
    background: rgba(59, 130, 246, 0.15);
    color: var(--info);
}

.custom-alert .alert-content {
    flex: 1;
}

.custom-alert strong {
    display: block;
    font-size: 1rem;
    margin-bottom: 0.25rem;
    color: var(--dark);
}

.custom-alert p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray);
}

.custom-alert .alert-close {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(0, 0, 0, 0.05);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray);
    transition: var(--transition);
}

.custom-alert .alert-close:hover {
    background: rgba(0, 0, 0, 0.1);
}

/* ==================== Sidebar ==================== */
.sidebar-sticky {
    position: sticky;
    top: 20px;
}

.quick-actions-card,
.summary-card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.card-header-pos {
    padding: 1.25rem;
    background: linear-gradient(135deg, var(--dark), #334155);
    color: white;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
}

.card-body-pos {
    padding: 1.25rem;
}

.action-buttons-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.action-btn-pos {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--transition);
    border: 2px solid;
}

.action-btn-pos i {
    font-size: 1.5rem;
}

.action-btn-pos.primary {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(99, 102, 241, 0.05));
    color: var(--primary);
    border-color: rgba(99, 102, 241, 0.2);
}

.action-btn-pos.warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
    color: var(--warning);
    border-color: rgba(245, 158, 11, 0.2);
}

.action-btn-pos.info {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
    color: var(--info);
    border-color: rgba(59, 130, 246, 0.2);
}

.action-btn-pos.danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
    color: var(--danger);
    border-color: rgba(239, 68, 68, 0.2);
}

.action-btn-pos:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.875rem 0;
    border-bottom: 1px solid var(--border);
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-label {
    font-size: 0.875rem;
    color: var(--gray);
    font-weight: 500;
}

.summary-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--dark);
}

/* ==================== Cart Card ==================== */
.cart-card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    border: 2px solid var(--primary);
    animation: slideDown 0.3s ease;
}

.cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, var(--primary), #818cf8);
    color: white;
}

.cart-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    font-size: 1.125rem;
}

.cart-badge {
    padding: 0.375rem 0.875rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.cart-body {
    padding: 1.5rem;
}

.cart-items-list {
    max-height: 400px;
    overflow-y: auto;
}

.cart-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: var(--light);
    border-radius: 10px;
    margin-bottom: 1rem;
    transition: var(--transition);
}

.cart-item:hover {
    background: #f1f5f9;
}

.cart-footer {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--border);
}

.cart-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 1.125rem;
    font-weight: 700;
}

.total-value {
    font-size: 1.5rem;
    color: var(--primary);
}

.cart-actions {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 1rem;
}

.btn-cart-action {
    padding: 0.875rem 1.5rem;
    border-radius: 10px;
    border: 2px solid;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-cart-action.clear {
    background: white;
    color: var(--danger);
    border-color: var(--danger);
}

.btn-cart-action.clear:hover {
    background: var(--danger);
    color: white;
}

.btn-cart-action.checkout {
    background: linear-gradient(135deg, var(--success), #34d399);
    color: white;
    border-color: transparent;
}

.btn-cart-action.checkout:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* ==================== Search Card ==================== */
.search-card {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
}

.search-form {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.search-input-wrapper {
    flex: 1;
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 1.25rem;
}

.search-input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--border);
    border-radius: 10px;
    font-size: 1rem;
    transition: var(--transition);
}

.search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.search-clear {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 1.25rem;
    cursor: pointer;
    transition: var(--transition);
}

.search-clear:hover {
    color: var(--danger);
}

.search-submit {
    padding: 0.875rem 2rem;
    background: linear-gradient(135deg, var(--primary), #818cf8);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.search-submit:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.active-filters {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

.filter-label {
    font-size: 0.875rem;
    color: var(--gray);
    font-weight: 600;
}

.filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.875rem;
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary);
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.filter-remove {
    color: var(--primary);
    text-decoration: none;
    font-size: 1.25rem;
    line-height: 1;
    cursor: pointer;
    transition: var(--transition);
}

.filter-remove:hover {
    color: var(--danger);
}

/* ==================== Products Grid ==================== */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.product-card-pos {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 2px solid transparent;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.product-card-pos:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
}

.product-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.badge-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.product-header-pos {
    margin-bottom: 1rem;
    padding-right: 80px;
}

.product-name-pos {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-pricing {
    margin-bottom: 1.5rem;
}

.price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.375rem 0;
    border-bottom: 1px solid var(--border);
}

.price-row:last-child {
    border-bottom: none;
}

.price-row.selling {
    background: rgba(99, 102, 241, 0.05);
    margin: 0 -0.5rem;
    padding: 0.5rem;
    border-radius: 6px;
    border: none;
}

.price-row.profit {
    background: rgba(16, 185, 129, 0.05);
    margin: 0 -0.5rem;
    padding: 0.5rem;
    border-radius: 6px;
    border: none;
}

.price-label {
    font-size: 0.875rem;
    color: var(--gray);
    font-weight: 500;
}

.price-value {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--dark);
}

.price-value.cost {
    color: var(--danger);
}

.price-row.selling .price-value {
    color: var(--primary);
    font-size: 1rem;
}

.price-row.profit .price-value {
    color: var(--success);
    font-size: 1rem;
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.add-to-cart-form {
    width: 100%;
}

.quantity-control {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.quantity-input {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    text-align: center;
    transition: var(--transition);
}

.quantity-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.quantity-input:disabled {
    background-color: #f8fafc;
    color: #94a3b8;
    cursor: not-allowed;
}

.btn-add-cart {
    flex: 2;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, var(--success), #34d399);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-add-cart:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-add-cart:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
}

.quick-actions-row {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.btn-quick-sell {
    flex: 1;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, var(--warning), #fbbf24);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.btn-quick-sell:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-edit {
    padding: 0.75rem;
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary);
    border: 2px solid rgba(99, 102, 241, 0.2);
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.btn-edit:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.out-of-stock-notice {
    padding: 1rem;
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

/* ==================== Pagination ==================== */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.pagination {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.page-item {
    list-style: none;
}

.page-link {
    padding: 0.75rem 1rem;
    border: 2px solid var(--border);
    border-radius: 8px;
    color: var(--dark);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
}

.page-link:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.page-item.active .page-link {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.page-item.disabled .page-link {
    background: #f8fafc;
    color: #94a3b8;
    cursor: not-allowed;
}

/* ==================== Empty State ==================== */
.empty-state-pos {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.empty-icon {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
}

.empty-state-pos h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 1rem;
}

.empty-state-pos p {
    font-size: 1rem;
    color: var(--gray);
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.btn-empty-action {
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--primary), #818cf8);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
}

.btn-empty-action:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
}

/* ==================== Responsive Design ==================== */
@media (max-width: 1200px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 992px) {
    .pos-container {
        padding: 1rem;
    }
    
    .search-form {
        flex-direction: column;
        gap: 1rem;
    }
    
    .search-input-wrapper {
        width: 100%;
    }
    
    .cart-actions {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stats-header .row {
        gap: 1rem;
    }
    
    .stat-box {
        padding: 1rem;
    }
    
    .stat-value {
        color: var(--shop-primary);
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
        gap: 1rem;
    }
    
    .action-buttons-grid {
        grid-template-columns: 1fr;
    }
    
    .custom-alert {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .custom-alert .alert-content {
        text-align: center;
    }
}

@media (max-width: 576px) {
    .pos-container {
        padding: 0.5rem;
    }
    
    .stat-box {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .quick-actions-row {
        flex-direction: column;
    }
    
    .cart-item {
        flex-direction: column;
        text-align: center;
    }
    
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
}

/* ==================== Utility Classes ==================== */
.text-primary { color: var(--primary) !important; }
.text-success { color: var(--success) !important; }
.text-warning { color: var(--warning) !important; }
.text-danger { color: var(--danger) !important; }
.text-info { color: var(--info) !important; }
.text-dark { color: var(--dark) !important; }
.text-gray { color: var(--gray) !important; }

.bg-primary { background-color: var(--primary) !important; }
.bg-success { background-color: var(--success) !important; }
.bg-warning { background-color: var(--warning) !important; }
.bg-danger { background-color: var(--danger) !important; }

/* ==================== Loading States ==================== */
.loading {
    opacity: 0.7;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ==================== Print Styles ==================== */
@media print {
    .sidebar-sticky,
    .search-card,
    .action-buttons-grid,
    .btn-edit,
    .btn-quick-sell {
        display: none !important;
    }
    
    .pos-container {
        padding: 0;
    }
    
    .product-card-pos {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<script>
// ==================== Cart Management Functions ====================
function addToCart(productId, quantity = 1) {
    if (quantity < 1) {
        showToast('Quantity must be at least 1', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('quantity', quantity);

    fetch(`{{ route('shop.cart.add', ':id') }}`.replace(':id', productId), {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.error || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
    if (data.success) {
        updateCartUI();
        showToast('Product added to cart!', 'success');
        
        // Add visual feedback - SAFER VERSION
        try {
            // Try multiple selectors to find the product card
            const selectors = [
                `[data-product-id="${productId}"]`,
                `[onclick*="quickSell(${productId}"]`,
                `[onsubmit*="addToCart(${productId}"]`,
                `.product-card-pos:has([onclick*="${productId}"])`
            ];
            
            let productCard = null;
            for (const selector of selectors) {
                productCard = document.querySelector(selector);
                if (productCard) {
                    // If we found a direct element, get the card
                    if (!productCard.classList.contains('product-card-pos')) {
                        productCard = productCard.closest('.product-card-pos');
                    }
                    break;
                }
            }
            
            // Only apply animation if we found the card
            if (productCard) {
                productCard.style.animation = 'pulse 0.5s ease';
                setTimeout(() => {
                    if (productCard) {
                        productCard.style.animation = '';
                    }
                }, 500);
            }
        } catch (error) {
            console.warn('Could not find product card for animation:', error);
            // Don't show error to user, just continue
        }
    } else {
        showToast(data.error || 'Error adding to cart', 'danger');
    }

    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error: ' + error.message, 'danger');
    });
}

function updateCartUI() {
    fetch('{{ route("shop.cart.data") }}')
        .then(response => response.json())
        .then(data => {
            const cartSection = document.getElementById('cartSection');
            const cartCount = data.cartCount || 0;
            
            if (cartCount > 0) {
                cartSection.style.display = 'block';
                document.getElementById('cartCountBadge').textContent = `${cartCount} ${cartCount === 1 ? 'item' : 'items'}`;
                document.getElementById('cartTotalAmount').textContent = `UGX ${data.cartTotal?.toLocaleString() || '0'}`;
                loadCartItems(data.cartItems);
                updateTodaySummary(data);
            } else {
                cartSection.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching cart data:', error);
        });
}

function loadCartItems(cartItems) {
    const cartItemsContainer = document.getElementById('cartItems');
    
    if (!cartItems || Object.keys(cartItems).length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart-state">
                <i class="bi bi-cart-x" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                <p class="text-muted">Your cart is empty</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    Object.values(cartItems).forEach(item => {
        const itemTotal = item.price * item.quantity;
        const profit = (item.price - item.cost_price) * item.quantity;
        
        html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-details">
                        <span class="cart-item-price">UGX ${item.price.toLocaleString()} each</span>
                        <span class="cart-item-profit">Profit: UGX ${profit.toLocaleString()}</span>
                    </div>
                </div>
                <div class="cart-item-controls">
                    <div class="quantity-control-group">
                        <button class="btn-quantity" onclick="updateCartItem(${item.product_id}, ${Math.max(1, item.quantity - 1)})">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" 
                               class="cart-quantity-input" 
                               value="${item.quantity}" 
                               min="1" 
                               max="${item.max_stock}"
                               onchange="updateCartItem(${item.product_id}, this.value)"
                               onblur="validateQuantity(${item.product_id}, this)">
                        <button class="btn-quantity" onclick="updateCartItem(${item.product_id}, ${Math.min(item.max_stock, item.quantity + 1)})">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <div class="cart-item-total">
                        <div class="total-amount">UGX ${itemTotal.toLocaleString()}</div>
                    </div>
                    <button class="btn-remove-item" onclick="removeFromCart(${item.product_id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    cartItemsContainer.innerHTML = html;
}

function validateQuantity(productId, input) {
    const quantity = parseInt(input.value);
    if (isNaN(quantity) || quantity < 1) {
        input.value = 1;
        updateCartItem(productId, 1);
    }
}

function updateCartItem(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }

    fetch(`{{ route('shop.cart.update', ':id') }}`.replace(':id', productId), {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateCartUI();
            showToast('Cart updated', 'success');
        } else {
            showToast(data.error || 'Error updating cart', 'danger');
            updateCartUI(); // Refresh to get correct state
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error: ' + error.message, 'danger');
        updateCartUI(); // Refresh to get correct state
    });
}

function removeFromCart(productId) {
    if (!confirm('Remove this item from cart?')) return;

    fetch(`{{ route('shop.cart.remove', ':id') }}`.replace(':id', productId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartUI();
            showToast('Item removed from cart', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error: ' + error.message, 'danger');
    });
}

function clearCart() {
    if (!confirm('Clear all items from cart? This action cannot be undone.')) return;

    fetch('{{ route("shop.cart.clear") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartUI();
            showToast('Cart cleared successfully', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error: ' + error.message, 'danger');
    });
}

function checkout() {
    const cartSection = document.getElementById('cartSection');
    const checkoutBtn = cartSection.querySelector('.btn-cart-action.checkout');
    
    if (!confirm('Complete sale and generate receipt?')) return;

    // Add loading state
    checkoutBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Processing...';
    checkoutBtn.disabled = true;

    fetch('{{ route("shop.cart.checkout") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Sale completed successfully!', 'success');
            // Open receipt in new tab
            window.open('{{ route("shop.receipt.unified") }}', '_blank');
            // Reload page after short delay
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.error || 'Error completing sale', 'danger');
            checkoutBtn.innerHTML = '<i class="bi bi-credit-card"></i> Checkout';
            checkoutBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error: ' + error.message, 'danger');
        checkoutBtn.innerHTML = '<i class="bi bi-credit-card"></i> Checkout';
        checkoutBtn.disabled = false;
    });
}

function quickSell(productId, quantity) {
    if (!confirm(`Quick sell ${quantity} item(s)? This will immediately complete the sale.`)) return;
    
    fetch(`{{ route('shop.products.sell', ':id') }}`.replace(':id', productId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Quick sale completed!', 'success');
            // Open receipt in new tab
            window.open('{{ route("shop.receipt.unified") }}', '_blank');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Error completing sale', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error: ' + error.message, 'danger');
    });
}

function updateTodaySummary(cartData) {
    // This would typically fetch today's sales data from the server
    // For now, we'll just update with cart data
    const todaySold = Object.values(cartData.cartItems || {}).reduce((sum, item) => sum + item.quantity, 0);
    const todayRevenue = cartData.cartTotal || 0;
    const todayProfit = Object.values(cartData.cartItems || {}).reduce((sum, item) => {
        return sum + ((item.price - item.cost_price) * item.quantity);
    }, 0);

    // Update the summary cards
    document.querySelectorAll('.summary-item')[0].querySelector('.summary-value').textContent = todaySold;
    document.querySelectorAll('.summary-item')[1].querySelector('.summary-value').textContent = `UGX ${todayRevenue.toLocaleString()}`;
    document.querySelectorAll('.summary-item')[2].querySelector('.summary-value').textContent = `UGX ${todayProfit.toLocaleString()}`;
}

function showToast(message, type = 'info') {
    // Remove existing toasts
    document.querySelectorAll('.custom-toast').forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `custom-toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        padding: 1rem 1.5rem;
        border-radius: var(--radius);
        color: white;
        font-weight: 600;
        box-shadow: var(--shadow-lg);
        animation: slideInRight 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    `;
    
    // Set background based on type
    const backgrounds = {
        success: 'linear-gradient(135deg, var(--success), #34d399)',
        danger: 'linear-gradient(135deg, var(--danger), #f87171)',
        warning: 'linear-gradient(135deg, var(--warning), #fbbf24)',
        info: 'linear-gradient(135deg, var(--info), #60a5fa)'
    };
    
    toast.style.background = backgrounds[type] || backgrounds.info;
    
    const icon = type === 'success' ? 'bi-check-circle-fill' :
                 type === 'danger' ? 'bi-x-circle-fill' :
                 type === 'warning' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill';
    
    toast.innerHTML = `
        <i class="bi ${icon}" style="font-size: 1.25rem;"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; color: white; cursor: pointer;">
            <i class="bi bi-x"></i>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartUI();
    
    // Add pulse animation for CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
        .spin {
            animation: spin 1s linear infinite;
        }
        .empty-cart-state {
            text-align: center;
            padding: 2rem;
            color: #64748b;
        }
        .cart-item-info {
            flex: 1;
        }
        .cart-item-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .cart-item-details {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
        }
        .cart-item-price {
            color: var(--primary);
            font-weight: 600;
        }
        .cart-item-profit {
            color: var(--success);
        }
        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .quantity-control-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-quantity {
            width: 32px;
            height: 32px;
            border: 1px solid var(--border);
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-quantity:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .cart-quantity-input {
            width: 60px;
            padding: 0.375rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
        }
        .cart-item-total {
            min-width: 100px;
            text-align: right;
        }
        .total-amount {
            font-weight: 700;
            color: var(--primary);
        }
        .btn-remove-item {
            width: 32px;
            height: 32px;
            border: 1px solid var(--danger);
            background: white;
            color: var(--danger);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-remove-item:hover {
            background: var(--danger);
            color: white;
        }
    `;
    document.head.appendChild(style);

    // Quick search functionality
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 800);
        });
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput?.focus();
        }
        // Escape to clear search
        if (e.key === 'Escape' && searchInput?.value) {
            searchInput.value = '';
            searchInput.form?.submit();
        }
    });
});

// Export functions for global access
window.addToCart = addToCart;
window.updateCartItem = updateCartItem;
window.removeFromCart = removeFromCart;
window.clearCart = clearCart;
window.checkout = checkout;
window.quickSell = quickSell;
window.showToast = showToast;
</script>
@endsection
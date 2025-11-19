@extends('pos.layouts.app')
@section('title', 'POS Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Dashboard Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-box-seam display-6 opacity-75"></i>
                    </div>
                    <h4 class="mb-1">{{ $totalProducts }}</h4>
                    <small class="opacity-75">Total Products</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-exclamation-triangle display-6 opacity-75"></i>
                    </div>
                    <h4 class="mb-1">{{ $lowStockProducts }}</h4>
                    <small class="opacity-75">Low Stock</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card bg-danger text-white shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-x-circle display-6 opacity-75"></i>
                    </div>
                    <h4 class="mb-1">{{ $outOfStockProducts }}</h4>
                    <small class="opacity-75">Out of Stock</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-currency-dollar display-6 opacity-75"></i>
                    </div>
                    <h4 class="mb-1">UGX {{ number_format($todayRevenue) }}</h4>
                    <small class="opacity-75">Today's Revenue</small>
                </div>
            </div>
        </div>
        
    
        
       
    </div>

    <!-- Quick Actions Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('pos.dashboard') }}" class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <input type="text" name="search" value="{{ $search }}" 
                                           class="form-control form-control-lg" 
                                           placeholder="🔍 Search products by name...">
                                </div>
                                <button class="btn btn-primary btn-lg px-4" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                                @if($search)
                                <a href="{{ route('pos.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('pos.sales-history') }}" class="btn btn-info btn-lg">
                                    <i class="bi bi-clock-history"></i> Sales History
                                </a>
                                <button class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                    <i class="bi bi-cash-coin"></i> Expense
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alerts Row -->
@if($lowStockProducts > 0 || $outOfStockProducts > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle-fill"></i> Stock Alerts
                </h5>
                    <a href="{{ route('pos.low-stock-report') }}" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-clipboard-data"></i> View Report
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($outOfStockProducts > 0)
                    <div class="col-md-4">
                        <div class="alert alert-danger h-100 m-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Out of Stock</h6>
                                    <p class="mb-1">{{ $outOfStockProducts }} products</p>
                                    <small>Immediate restocking needed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($lowStockProducts > 0)
                    <div class="col-md-4">
                        <div class="alert alert-warning h-100 m-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Low Stock</h6>
                                    <p class="mb-1">{{ $lowStockProducts }} products</p>
                                    <small>Running low on inventory</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-4">
                        <div class="bg-light rounded p-3 h-100">
                            <h6 class="mb-2">Stock Summary</h6>
                            <div class="small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Total Products:</span>
                                    <strong>{{ $totalProducts }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-warning">Low Stock:</span>
                                    <strong>{{ $lowStockProducts }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-danger">Out of Stock:</span>
                                    <strong>{{ $outOfStockProducts }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

    <div class="row">
        <!-- Quick Actions & Cart Sidebar -->
        <div class="col-xl-3 col-lg-4 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 mb-4">
                        <button class="btn btn-success btn-lg" onclick="clearCart()">
                            <i class="bi bi-trash"></i> Clear Cart
                        </button>
                        <button class="btn btn-primary btn-lg" onclick="checkout()">
                            <i class="bi bi-credit-card"></i> Checkout Sale
                        </button>
                    </div>
                    
                    <!-- Cart Section -->
                    <div id="cartSection" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Shopping Cart</h6>
                            <span id="cartCountBadge" class="badge bg-primary">0 items</span>
                        </div>
                        <div id="cartItems" class="mb-3" style="max-height: 300px; overflow-y: auto;"></div>
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="fs-5">Total:</strong>
                                <strong class="fs-5 text-success" id="cartTotalAmount">UGX 0</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="border-bottom pb-2">Today's Summary</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Revenue:</span>
                                <strong class="text-success">UGX {{ number_format($todayRevenue) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Products:</span>
                                <strong>{{ $totalProducts }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-xl-9 col-lg-8">
            @if($products->count() > 0)
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6">
                    <div class="card product-card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column p-3">
                            <!-- Product Header -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0 text-truncate flex-grow-1" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h6>
                                <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }} ms-2">
                                    {{ $product->stock }}
                                </span>
                            </div>

                            <!-- Pricing -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Price:</small>
                                    <strong class="text-success">UGX {{ number_format($product->price) }}</strong>
                                </div>
                            </div>

                            <!-- Actions -->
                            @if($product->stock > 0)
                            <div class="mt-auto">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button class="btn btn-success btn-sm w-100" 
                                                onclick="addToCart({{ $product->id }}, 1)"
                                                title="Add to Cart">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-warning btn-sm w-100" 
                                                onclick="quickSell({{ $product->id }}, 1)"
                                                title="Quick Sell">
                                            <i class="bi bi-lightning"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- Quantity Selector -->
                                <div class="mt-2">
                                    <div class="input-group input-group-sm">
                                        <input type="number" 
                                               id="qty-{{ $product->id }}"
                                               class="form-control text-center" 
                                               value="1" 
                                               min="1" 
                                               max="{{ $product->stock }}"
                                               onchange="updateQuantity({{ $product->id }}, this.value)">
                                        
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-center text-danger mt-auto py-2">
                                <i class="bi bi-x-circle"></i>
                                <small>Out of Stock</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="mt-3 d-flex justify-content-end">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>  
            @endif

            @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-box-seam display-1 text-muted opacity-50"></i>
                    <h4 class="mt-3 text-muted">No products found</h4>
                    <p class="text-muted">
                        @if($search)
                            No products match your search criteria.
                        @else
                            No products available in inventory.
                        @endif
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('pos.expenses.store') }}" method="POST">
            @csrf
            <div class="modal-header bg-warning">Add Expense<button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label>Category</label><select name="category" class="form-select form-select-lg" required>
                    <option value="">Select...</option>
                    <option value="supplies">Supplies</option>
                    <option value="transport">Transport</option>
                    <option value="utilities">Utilities</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="other">Other</option>
                </select></div>
                <div class="mb-3"><label>Description</label><input name="description" class="form-control" required></div>
                <div class="mb-3"><label>Amount</label><input name="amount" type="number" class="form-control" min="0" required></div>
                <div class="mb-3"><label>Date</label><input name="expense_date" type="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-warning">Record Expense</button></div>
        </form>
    </div>
</div>

<script>
/* =========================
   POS JS: Cart & Quick Sell
   Compatible with PosController
========================= */

/**
 * Show toast notifications
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

/* =========================
   QUICK SELL (single item)
========================= */
function quickSell(productId, quantity = 1) {
    if (!confirm(`Sell ${quantity} item(s)?`)) return;

    fetch(`{{ route('pos.sell', ':id') }}`.replace(':id', productId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Sale completed!', 'success');

            // Open unified receipt in new tab
            window.open(data.receipt_url, '_blank');

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

/* =========================
   OPTIONAL: Multi-item POS Cart
========================= */
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('quantity', quantity);

    fetch(`{{ route('pos.cart.add', ':id') }}`.replace(':id', productId), {
        method: 'POST',
        body: formData
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.success) {
            updateCartUI();
            showToast('Product added to cart!', 'success');
            // Reset quantity input
            const qtyInput = document.getElementById('qty-' + productId);
            if (qtyInput) qtyInput.value = 1;
        } else {
            showToast(data.error || 'Error adding to cart', 'danger');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error: ' + err.message, 'danger');
    });
}

function updateCartUI() {
    fetch('{{ route("pos.cart.data") }}')
        .then(resp => resp.json())
        .then(data => {
            const cartSection = document.getElementById('cartSection');
            const cartCount = data.cartCount || 0;
            if (cartCount > 0) {
                cartSection.style.display = 'block';
                document.getElementById('cartCountBadge').textContent = `${cartCount} items`;
                document.getElementById('cartTotalAmount').textContent = `UGX ${data.cartTotal.toLocaleString()}`;
                loadCartItems(data.cartItems);
            } else {
                cartSection.style.display = 'none';
            }
        })
        .catch(err => console.error('Error fetching cart data:', err));
}

function loadCartItems(cartItems) {
    const container = document.getElementById('cartItems');
    if (!cartItems || Object.keys(cartItems).length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No items in cart</p>';
        return;
    }

    let html = '';
    Object.values(cartItems).forEach(item => {
        const total = item.price * item.quantity;
        html += `
        <div class="cart-item d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <div class="flex-grow-1">
                <div class="fw-bold">${item.name}</div>
                <small class="text-muted">UGX ${item.price.toLocaleString()} each</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 120px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartItem(${item.product_id}, ${Math.max(1, item.quantity - 1)})">-</button>
                    <input type="number" class="form-control text-center" value="${item.quantity}" min="1" max="${item.max_stock}" 
                           onchange="updateCartItem(${item.product_id}, this.value)">
                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartItem(${item.product_id}, ${Math.min(item.max_stock, item.quantity + 1)})">+</button>
                </div>
                <div class="text-end" style="min-width: 80px;">
                    <div class="fw-bold">UGX ${total.toLocaleString()}</div>
                </div>
                <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(${item.product_id})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>`;
    });

    container.innerHTML = html;
}

function updateCartItem(productId, quantity) {
    if (quantity < 1) return removeFromCart(productId);

    fetch(`{{ route('pos.cart.update', ':id') }}`.replace(':id', productId), {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(resp => resp.json())
    .then(data => {
        if (!data.success) showToast(data.error || 'Error updating cart', 'danger');
        updateCartUI();
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error: ' + err.message, 'danger');
        updateCartUI();
    });
}

function removeFromCart(productId) {
    if (!confirm('Remove this item from cart?')) return;

    fetch(`{{ route('pos.cart.remove', ':id') }}`.replace(':id', productId), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.success) {
            showToast('Item removed from cart', 'success');
            updateCartUI();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error: ' + err.message, 'danger');
    });
}

function clearCart() {
    if (!confirm('Clear all items from cart?')) return;

    fetch('{{ route("pos.cart.clear") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.success) {
            updateCartUI();
            showToast('Cart cleared', 'success');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error: ' + err.message, 'danger');
    });
}

function checkout() {
    if (!confirm('Complete sale and generate receipt?')) return;

    fetch('{{ route("pos.cart.checkout") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.success) {
            showToast('Sale completed successfully!', 'success');
            window.open('{{ route("pos.cart.receipt") }}', '_blank');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Error completing sale', 'danger');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error: ' + err.message, 'danger');
    });
}

// Enhanced JavaScript with quantity updates
function updateQuantity(productId, quantity) {
    const input = document.getElementById('qty-' + productId);
    const maxStock = parseInt(input.max);
    
    if (quantity > maxStock) {
        input.value = maxStock;
        showToast(`Maximum stock is ${maxStock}`, 'warning');
    } else if (quantity < 1) {
        input.value = 1;
    }
}

/* =========================
   QUICK SEARCH (POS product search)
========================= */
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => this.form.submit(), 500);
    });
}

/* =========================
   INIT: Load POS Cart UI on page load
========================= */
document.addEventListener('DOMContentLoaded', updateCartUI);
</script>

<style>
/* Enhanced Styles */
.product-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(145deg, #ffffff, #f8fafc);
}

.product-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 20px 40px rgba(14, 165, 233, 0.15);
    border-color: #0ea5e9;
}

.card {
    border: none;
    border-radius: 16px;
}

.card-header {
    border-radius: 16px 16px 0 0 !important;
}

.btn {
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.badge {
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 8px;
}

.sticky-top {
    z-index: 10;
}

/* Custom scrollbar for cart */
#cartItems::-webkit-scrollbar {
    width: 6px;
}

#cartItems::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#cartItems::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#cartItems::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .col-xxl-2 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    /* Smaller pagination on mobile */
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .col-xxl-2 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
@endsection
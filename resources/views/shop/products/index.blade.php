@extends('shop.layouts.app')

@section('title', 'Point of Sale - Quick Selling')

@section('content')
<div class="container-fluid">
    <!-- Quick Stats Header -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Products</h6>
                            <h4 class="mb-0">{{ $products->total() }}</h4>
                        </div>
                        <i class="bi bi-box-seam display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">In Stock</h6>
                            <h4 class="mb-0">{{ $products->where('stock', '>', 0)->count() }}</h4>
                        </div>
                        <i class="bi bi-check-circle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Low Stock</h6>
                            <h4 class="mb-0">{{ $products->where('stock', '<=', 5)->where('stock', '>', 0)->count() }}</h4>
                        </div>
                        <i class="bi bi-exclamation-triangle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Out of Stock</h6>
                            <h4 class="mb-0">{{ $products->where('stock', 0)->count() }}</h4>
                        </div>
                        <i class="bi bi-x-circle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('last_sale'))
        @php $sale = session('last_sale'); @endphp
        <div class="alert alert-info alert-dismissible fade show">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-receipt me-2"></i>
                    <strong>Sale Completed!</strong>
                    @if($sale['type'] === 'multi')
                        {{ count($sale['items']) }} items sold
                    @else
                        {{ $sale['quantity'] }}x <b>{{ $sale['product_name'] }}</b>
                    @endif
                    • Total: <b>UGX {{ number_format($sale['type'] === 'multi' ? $sale['total_amount'] : $sale['total']) }}</b> 
                    • Profit: <b class="text-success">UGX {{ number_format($sale['type'] === 'multi' ? $sale['total_profit'] : $sale['profit']) }}</b>
                </div>
                <a href="{{ route('shop.receipt.unified') }}" 
                   class="btn btn-sm btn-outline-info" target="_blank">
                    <i class="bi bi-receipt"></i> Print Receipt
                </a>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Quick Actions Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('shop.products.create') }}" class="btn btn-success btn-lg">
                            <i class="bi bi-plus-circle"></i> Add New Product
                        </a>
                        <a href="{{ route('shop.expenses.create') }}" class="btn btn-outline-warning">
                            <i class="bi bi-cash-coin"></i> Record Expense
                        </a>
                        <a href="{{ route('shop.reports.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-graph-up"></i> View Reports
                        </a>
                    </div>

                    <!-- Quick Sell Stats -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="border-bottom pb-2">Today's Summary</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between">
                                <span>Products Sold:</span>
                                <strong>0</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Total Revenue:</span>
                                <strong>UGX 0</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Total Profit:</span>
                                <strong class="text-success">UGX 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Cart Section -->
            <div class="card shadow-sm mb-4" id="cartSection" style="display: none;">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-cart"></i> Shopping Cart</h6>
                    <span class="badge bg-light text-dark" id="cartCountBadge">0 items</span>
                </div>
                <div class="card-body">
                    <div id="cartItems">
                        <!-- Cart items will be loaded here -->
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <strong>Total: <span id="cartTotalAmount">UGX 0</span></strong>
                        <div class="btn-group">
                            <button class="btn btn-outline-danger btn-sm" onclick="clearCart()">
                                <i class="bi bi-trash"></i> Clear
                            </button>
                            <button class="btn btn-success btn-sm" onclick="checkout()">
                                <i class="bi bi-credit-card"></i> Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form class="row g-2 align-items-center" method="GET" action="{{ route('shop.products.index') }}">
                        @csrf
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       class="form-control form-control-lg" 
                                       placeholder="Search products by name... (Type to filter)"
                                       id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                    <i class="bi bi-search me-1"></i> Search
                                </button>
                                <a href="{{ route('shop.products.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Active Filters -->
                    @if(request('search'))
                    <div class="mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted">Active filters:</small>
                            <span class="badge bg-primary">
                                Search: "{{ request('search') }}"
                                <a href="{{ route('shop.products.index', array_merge(request()->except('search'), ['page' => 1])) }}" 
                                   class="text-white ms-1" style="text-decoration: none;">×</a>
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card product-card h-100 shadow-sm border-hover">
                        <div class="card-body">
                            <!-- Product Header -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0 text-truncate" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h6>
                                <span class="badge bg-{{ $product->stock > 5 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                    {{ $product->stock }} in stock
                                </span>
                            </div>

                            <!-- Pricing -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Cost:</small>
                                    <small class="text-muted">UGX {{ number_format($product->cost_price) }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">Price:</strong>
                                    <strong class="text-primary">UGX {{ number_format($product->price) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Profit:</small>
                                    <small class="text-success fw-bold">
                                        UGX {{ number_format($product->price - $product->cost_price) }}
                                    </small>
                                </div>
                            </div>

                            <!-- Add to Cart Form -->
                            <form onsubmit="event.preventDefault(); addToCart({{ $product->id }}, this.quantity.value)" class="mt-auto">
                                @csrf
                                <div class="row g-2 align-items-center">
                                    <div class="col-7">
                                        <input type="number" 
                                               name="quantity" 
                                               min="1" 
                                               max="{{ $product->stock }}"
                                               class="form-control form-control-sm" 
                                               value="1"
                                               {{ $product->stock == 0 ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-5">
                                        <button class="btn btn-sm btn-success w-100" 
                                                {{ $product->stock == 0 ? 'disabled' : '' }}
                                                title="Add to Cart">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Additional Actions -->
                            @if($product->stock > 0)
                            <div class="mt-2">
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-sm btn-outline-warning"
                                            onclick="quickSell({{ $product->id }}, 1)"
                                            title="Quick Sell 1 Item">
                                        <i class="bi bi-lightning"></i> Quick
                                    </button>
                                    <a href="{{ route('shop.products.edit', $product) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit Product">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Stock Warning -->
                        @if($product->stock == 0)
                        <div class="card-footer bg-danger text-white text-center py-2">
                            <small><i class="bi bi-exclamation-triangle"></i> OUT OF STOCK</small>
                        </div>
                        @elseif($product->stock <= 5)
                        <div class="card-footer bg-warning text-dark text-center py-2">
                            <small><i class="bi bi-exclamation-triangle"></i> LOW STOCK</small>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="mt-4">
                <nav aria-label="Products pagination">
                    {{ $products->links() }}
                </nav>
            </div>
            @endif

            <!-- Empty State -->
            @if($products->count() == 0)
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-box-seam display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No products found</h4>
                    <p class="text-muted">
                        @if(request()->has('search'))
                            No products match your search criteria.
                        @else
                            You haven't added any products yet.
                        @endif
                    </p>
                    <a href="{{ route('shop.products.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Add Your First Product
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.product-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}
.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #0d6efd;
}
.card-title {
    font-size: 0.9rem;
    font-weight: 600;
}
.border-hover:hover {
    border-color: #0d6efd !important;
}
.sticky-top {
    z-index: 1;
}
.empty-state {
    max-width: 400px;
    margin: 0 auto;
}
</style>

<script>
// CORRECTED: URL format must match routes
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('quantity', quantity);

    // FIXED: Changed from /shop/cart/add/{id} to /shop/cart/{id}/add
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
                document.getElementById('cartCountBadge').textContent = `${cartCount} items`;
                document.getElementById('cartTotalAmount').textContent = `UGX ${data.cartTotal.toLocaleString()}`;
                loadCartItems(data.cartItems);
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
        cartItemsContainer.innerHTML = '<p class="text-muted text-center">No items in cart</p>';
        return;
    }
    
    let html = '';
    Object.values(cartItems).forEach(item => {
        const itemTotal = item.price * item.quantity;
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
                        <div class="fw-bold">UGX ${itemTotal.toLocaleString()}</div>
                    </div>
                    <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(${item.product_id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    cartItemsContainer.innerHTML = html;
}

// CORRECTED: URL format must match routes
function updateCartItem(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }

    // FIXED: Changed from /shop/cart/update/{id} to /shop/cart/{id}/update
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
        } else {
            showToast(data.error || 'Error updating cart', 'danger');
            updateCartUI();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error: ' + error.message, 'danger');
        updateCartUI();
    });
}

// CORRECTED: URL format must match routes
function removeFromCart(productId) {
    if (!confirm('Remove this item from cart?')) return;

    // FIXED: Changed from /shop/cart/remove/{id} to /shop/cart/{id}/remove
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
    if (!confirm('Clear all items from cart?')) return;

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
            showToast('Cart cleared', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error: ' + error.message, 'danger');
    });
}

function checkout() {
    if (!confirm('Complete sale and generate receipt?')) return;

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

function quickSell(productId, quantity) {
    if (!confirm(`Sell ${quantity} item(s)?`)) return;
    
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
            showToast('Sale completed!', 'success');
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

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize cart UI on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartUI();
});

// Quick search functionality
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
}
</script>
@endsection
@extends('pos.layouts.app')
@section('title', 'POS Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Search -->
    <div class="row mb-3">
        <div class="col-12">
            <form method="GET" action="{{ route('pos.dashboard') }}">
                <div class="input-group">
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-lg" placeholder="Search products...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    @if($search)
                    <a href="{{ route('pos.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions & Cart -->
        <div class="col-md-3 mb-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">Quick Actions</div>
                <div class="card-body">
                    <div class="d-grid gap-2 mb-3">
                        <button class="btn btn-success" onclick="clearCart()">Clear Cart</button>
                        <button class="btn btn-primary" onclick="checkout()">Checkout</button>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addExpenseModal">Add Expense</button>
                    </div>
                    <div id="cartSection" style="display:none;">
                        <h6>Shopping Cart <span id="cartCountBadge" class="badge bg-secondary">0 items</span></h6>
                        <div id="cartItems" class="mb-2"></div>
                        <div class="fw-bold">Total: <span id="cartTotalAmount">UGX 0</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-9">
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 product-card">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="text-truncate">{{ $product->name }}</h6>
                                <span class="badge bg-{{ $product->stock>5?'success':($product->stock>0?'warning':'danger') }}">{{ $product->stock }} in stock</span>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between"><small>Price:</small><strong class="text-success">UGX {{ number_format($product->price) }}</strong></div>
                                <div class="d-flex justify-content-between"><small>Cost:</small><small class="text-muted">UGX {{ number_format($product->cost_price) }}</small></div>
                            </div>
                            @if($product->stock>0)
                            <div class="mt-auto">
                                <div class="d-grid gap-1">
                                    <button class="btn btn-sm btn-success" onclick="addToCart({{ $product->id }},1)">Add to Cart</button>
                                    <button class="btn btn-sm btn-warning" onclick="quickSell({{ $product->id }},1)">Quick Sell</button>
                                </div>
                            </div>
                            @else
                            <div class="text-center text-danger mt-auto"><i class="bi bi-x-circle"></i> Out of Stock</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($products->hasPages())
            <div class="mt-4">{{ $products->links() }}</div>
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
                <div class="mb-3"><label>Category</label><select name="category" class="form-select" required>
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
.product-card { transition: all 0.3s ease; border: 1px solid #e9ecef; }
.product-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-color: #0d6efd; }
</style>
@endsection

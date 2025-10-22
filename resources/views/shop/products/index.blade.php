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
                    <strong>Sale Completed!</strong> {{ $sale['quantity'] }}x <b>{{ $sale['product_name'] }}</b> 
                    • Total: <b>UGX {{ number_format($sale['total']) }}</b> 
                    • Profit: <b class="text-success">UGX {{ number_format($sale['profit']) }}</b>
                </div>
                <a href="{{ route('shop.products.receipt', ['product' => 'last', 'qty' => $sale['quantity']]) }}" 
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

        <!-- Products Grid -->
        <div class="col-md-9">
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
                @if(request('search'))
                <span class="badge bg-primary">
                    Search: "{{ request('search') }}"
                    <a href="{{ route('shop.products.index', array_merge(request()->except('search'), ['page' => 1])) }}" 
                       class="text-white ms-1" style="text-decoration: none;">×</a>
                </span>
                @endif
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

                            <!-- Quick Sell Form -->
                            <form action="{{ route('shop.products.sell', $product) }}" method="POST" class="mt-auto">
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
                                                title="Sell Product">
                                            <i class="bi bi-cart-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Additional Actions -->
                            @if($product->stock > 0)
                            <div class="mt-2">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('shop.products.receipt', ['product' => $product->id, 'qty' => 1]) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       target="_blank"
                                       title="Print Receipt">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-warning"
                                            onclick="quickSell({{ $product->id }}, 1)"
                                            title="Quick Sell 1 Item">
                                        <i class="bi bi-lightning"></i>
                                    </button>
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
function quickSell(productId, quantity) {
    // This would be enhanced with JavaScript for faster selling
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/shop/products/sell/${productId}`;
    
    const csrf = document.createElement('input');
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    const qty = document.createElement('input');
    qty.name = 'quantity';
    qty.value = quantity;
    form.appendChild(qty);
    
    document.body.appendChild(form);
    form.submit();
}

// Quick search functionality
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});
</script>
@endsection
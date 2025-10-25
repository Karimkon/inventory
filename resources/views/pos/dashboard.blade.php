@extends('pos.layouts.app')

@section('title', 'POS Dashboard')

@section('content')
<div class="row">
    <!-- Search Bar -->
   <div class="card search-card"> {{-- Changed from product-card to search-card --}}
    <div class="card-body">
        <form method="GET" action="{{ route('pos.dashboard') }}">
            <div class="input-group">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       class="form-control form-control-lg" 
                       placeholder="Search products...">
                <button class="btn btn-pos btn-lg" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
                @if($search)
                    <a href="{{ route('pos.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Quick Action Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card product-card text-center">
            <div class="card-body">
                <i class="bi bi-cart-check display-4 text-success"></i>
                <h5 class="mt-2">Quick Sale</h5>
                <small class="text-muted">Sell products</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card product-card text-center">
            <div class="card-body">
                <i class="bi bi-receipt display-4 text-primary"></i>
                <h5 class="mt-2">Receipts</h5>
                <small class="text-muted">Print receipts</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card product-card text-center" data-bs-toggle="modal" data-bs-target="#addExpenseModal" style="cursor: pointer;">
            <div class="card-body">
                <i class="bi bi-cash-coin display-4 text-warning"></i>
                <h5 class="mt-2">Add Expense</h5>
                <small class="text-muted">Record shop expense</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card product-card text-center">
            <div class="card-body">
                <i class="bi bi-graph-up display-4 text-info"></i>
                <h5 class="mt-2">Today's Sales</h5>
                <small class="text-muted">View reports</small>
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">➕ Add Shop Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pos.expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Expense Type</label>
                        <select name="category" class="form-select" required>
                            <option value="">Select category...</option>
                            <option value="supplies">Supplies</option>
                            <option value="transport">Transport</option>
                            <option value="utilities">Utilities</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" 
                               placeholder="What was this expense for?" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Amount (UGX)</label>
                        <input type="number" name="amount" class="form-control" 
                               min="0" step="0.01" placeholder="Enter amount" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="expense_date" class="form-control" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Record Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!-- Products Grid -->
    <div class="col-12">
        <div class="row g-3">
            @foreach($products as $product)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card product-card h-100">
                    <div class="card-body">
                        <!-- Product Header -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">{{ $product->name }}</h6>
                            <span class="badge bg-{{ $product->stock > 5 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                {{ $product->stock }} in stock
                            </span>
                        </div>

                        <!-- Pricing -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <small>Price:</small>
                                <strong class="text-success">UGX {{ number_format($product->price) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>Cost:</small>
                                <small class="text-muted">UGX {{ number_format($product->cost_price) }}</small>
                            </div>
                        </div>

                        <!-- Sell Form -->
                        @if($product->stock > 0)
                        <form action="{{ route('pos.sell', $product) }}" method="POST">
                            @csrf
                            <div class="row g-2 align-items-center">
                                <div class="col-7">
                                    <input type="number" 
                                           name="quantity" 
                                           min="1" 
                                           max="{{ $product->stock }}"
                                           class="form-control" 
                                           value="1"
                                           required>
                                </div>
                                <div class="col-5">
                                    <button type="submit" class="btn btn-pos w-100">
                                        <i class="bi bi-cart-check"></i> Sell
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Quick Actions -->
                        <div class="mt-2">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('pos.receipt', ['product' => $product->id, 'qty' => 1]) }}" 
                                   class="btn btn-sm btn-outline-info" 
                                   target="_blank">
                                    <i class="bi bi-receipt"></i> Receipt
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning"
                                        onclick="quickSell({{ $product->id }}, 1)">
                                    <i class="bi bi-lightning"></i> Quick
                                </button>
                            </div>
                        </div>
                        @else
                        <div class="text-center text-danger py-2">
                            <i class="bi bi-x-circle"></i> Out of Stock
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
        @endif

        <!-- Empty State -->
        @if($products->count() == 0)
        <div class="text-center py-5">
            <i class="bi bi-box-seam display-1 text-muted"></i>
            <h4 class="mt-3 text-muted">No products found</h4>
            <p class="text-muted">
                @if($search)
                    No products match your search.
                @else
                    No products available for sale.
                @endif
            </p>
        </div>
        @endif
    </div>
</div>

<script>
function quickSell(productId, quantity) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/pos/sell/${productId}`;
    
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
</script>
@endsection
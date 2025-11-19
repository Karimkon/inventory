@extends('pos.layouts.app')

@section('title', 'Low Stock Report - POS')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📊 Low Stock Report</h1>
            <p class="text-muted mb-0">Monitor inventory levels for quick restocking</p>
        </div>
        <div>
            <a href="{{ route('pos.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to POS
            </a>
        </div>
    </div>

    <!-- Filter Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ route('pos.low-stock-report', ['type' => 'all']) }}" 
               class="card filter-card {{ $type == 'all' ? 'active' : '' }}">
                <div class="card-body text-center">
                    <div class="filter-icon text-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h3>{{ $counts['all'] }}</h3>
                    <p class="mb-0">All Alerts</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('pos.low-stock-report', ['type' => 'low']) }}" 
               class="card filter-card {{ $type == 'low' ? 'active' : '' }}">
                <div class="card-body text-center">
                    <div class="filter-icon text-warning">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <h3>{{ $counts['low'] }}</h3>
                    <p class="mb-0">Low Stock</p>
                    <small class="text-muted">1-5 items</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('pos.low-stock-report', ['type' => 'critical']) }}" 
               class="card filter-card {{ $type == 'critical' ? 'active' : '' }}">
                <div class="card-body text-center">
                    <div class="filter-icon text-danger">
                        <i class="bi bi-fire"></i>
                    </div>
                    <h3>{{ $counts['critical'] }}</h3>
                    <p class="mb-0">Critical</p>
                    <small class="text-muted">1-2 items</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('pos.low-stock-report', ['type' => 'out']) }}" 
               class="card filter-card {{ $type == 'out' ? 'active' : '' }}">
                <div class="card-body text-center">
                    <div class="filter-icon text-dark">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3>{{ $counts['out'] }}</h3>
                    <p class="mb-0">Out of Stock</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $title }}</h5>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th class="text-center">Current Stock</th>
                            <th class="text-center">Selling Price</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Quick Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-box-seam me-3 text-muted"></i>
                                    <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">SKU: #{{ $product->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $product->stock == 0 ? 'dark' : ($product->stock <= 2 ? 'danger' : 'warning') }} fs-6">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="text-center fw-semibold">UGX {{ number_format($product->price) }}</td>
                            <td class="text-center">
                                @if($product->stock == 0)
                                    <span class="badge bg-dark">Out of Stock</span>
                                @elseif($product->stock <= 2)
                                    <span class="badge bg-danger">Critical</span>
                                @else
                                    <span class="badge bg-warning">Low Stock</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->stock > 0)
                                <button class="btn btn-sm btn-success" 
                                        onclick="quickSell({{ $product->id }}, 1)"
                                        title="Quick Sell">
                                    <i class="bi bi-lightning"></i> Sell
                                </button>
                                @else
                                <span class="text-muted">Cannot sell</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $products->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-check-circle display-1 text-success opacity-50"></i>
                <h4 class="mt-3 text-muted">No stock alerts found</h4>
                <p class="text-muted">All products are well stocked!</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.filter-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
    text-decoration: none;
    color: inherit;
}

.filter-card:hover {
    transform: translateY(-2px);
    border-color: #0ea5e9;
    text-decoration: none;
    color: inherit;
}

.filter-card.active {
    border-color: #0ea5e9;
    background: rgba(14, 165, 233, 0.05);
}

.filter-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.filter-card h3 {
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.filter-card p {
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.25rem;
}
</style>

<script>
function quickSell(productId, quantity) {
    if (!confirm(`Quick sell ${quantity} item(s)? This will immediately complete the sale.`)) return;
    
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
            alert('Quick sale completed!');
            // Open receipt in new tab
            window.open(data.receipt_url, '_blank');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.error || 'Error completing sale');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
}
</script>
@endsection
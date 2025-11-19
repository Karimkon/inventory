@extends('shop.layouts.app')

@section('title', 'Low Stock Report - Redvers ShopFlow')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📊 Low Stock Report</h1>
            <p class="text-muted mb-0">Monitor and manage your inventory levels</p>
        </div>
        <div>
            <a href="{{ route('shop.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('shop.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Filter Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ route('shop.products.low-stock-report', ['type' => 'all']) }}" 
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
            <a href="{{ route('shop.products.low-stock-report', ['type' => 'low']) }}" 
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
            <a href="{{ route('shop.products.low-stock-report', ['type' => 'critical']) }}" 
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
            <a href="{{ route('shop.products.low-stock-report', ['type' => 'out']) }}" 
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
                            <th class="text-center">Cost Price</th>
                            <th class="text-center">Selling Price</th>
                            <th class="text-center">Profit/Unit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Last Updated</th>
                            <th class="text-end">Actions</th>
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
                            <td class="text-center">UGX {{ number_format($product->cost_price) }}</td>
                            <td class="text-center fw-semibold">UGX {{ number_format($product->price) }}</td>
                            <td class="text-center">
                                <span class="text-success fw-semibold">
                                    +UGX {{ number_format($product->price - $product->cost_price) }}
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
                            <td class="text-center">
                                <small>{{ $product->updated_at->format('M d, Y') }}</small>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('shop.products.edit', $product) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-up-circle"></i> Restock
                                </a>
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
    border-color: #6366f1;
    text-decoration: none;
    color: inherit;
}

.filter-card.active {
    border-color: #6366f1;
    background: rgba(99, 102, 241, 0.05);
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
@endsection
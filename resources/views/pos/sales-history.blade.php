@extends('pos.layouts.app')
@section('title', 'POS Sales History')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📊 Sales History</h1>
        <a href="{{ route('pos.dashboard') }}" class="btn btn-secondary btn-lg">
            <i class="bi bi-arrow-left"></i> Back to POS
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-receipt display-6 opacity-75 mb-2"></i>
                    <h3>{{ $totalSales }}</h3>
                    <small class="opacity-75">Total Transactions</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-box display-6 opacity-75 mb-2"></i>
                    <h3>{{ $totalQuantity }}</h3>
                    <small class="opacity-75">Items Sold</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-currency-dollar display-6 opacity-75 mb-2"></i>
                    <h3>UGX {{ number_format($totalRevenue) }}</h3>
                    <small class="opacity-75">Total Revenue</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-graph-up-arrow display-6 opacity-75 mb-2"></i>
                    <h3>UGX {{ number_format($totalProfit) }}</h3>
                    <small class="opacity-75">Total Profit</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-dark fw-semibold">Time Range</label>
                    <select name="time_range" class="form-select" onchange="this.form.submit()">
                        <option value="today" {{ $timeRange == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $timeRange == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $timeRange == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="custom" {{ $timeRange == 'custom' ? 'selected' : '' }}>Custom Date</option>
                    </select>
                </div>
                
                @if($timeRange == 'custom')
                <div class="col-md-3">
                    <label class="form-label text-dark fw-semibold">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" 
                           onchange="this.form.submit()">
                </div>
                @endif
                
                <div class="col-md-3">
                    <label class="form-label text-dark fw-semibold">Filter by Product</label>
                    <select name="product_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <a href="{{ route('pos.sales-history') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales by Product Summary -->
    @if($salesByProduct->count() > 0)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Sales Summary by Product</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Quantity Sold</th>
                            <th class="text-end">Total Revenue</th>
                            <th class="text-end">Total Profit</th>
                            <th class="text-center">Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByProduct as $productSales)
                        <tr>
                            <td class="fw-semibold">{{ $productSales['product_name'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6">{{ $productSales['total_quantity'] }}</span>
                            </td>
                            <td class="text-end text-success fw-bold">
                                UGX {{ number_format($productSales['total_revenue']) }}
                            </td>
                            <td class="text-end text-warning fw-bold">
                                UGX {{ number_format($productSales['total_profit']) }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $productSales['sales_count'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Sales History -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Detailed Sales History</h5>
            <span class="badge bg-primary fs-6">{{ $sales->total() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <small class="text-dark fw-semibold">{{ $sale->created_at->format('M d, Y') }}</small>
                                    <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td class="fw-semibold">{{ $sale->product->name }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary fs-6">{{ $sale->quantity }}</span>
                            </td>
                            <td class="text-end text-dark">
                                UGX {{ number_format($sale->sold_price) }}
                            </td>
                            <td class="text-end fw-bold text-success">
                                UGX {{ number_format($sale->sold_price * $sale->quantity) }}
                            </td>
                            <td class="text-end fw-bold text-warning">
                                UGX {{ number_format(($sale->sold_price - $sale->cost_price) * $sale->quantity) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-receipt display-1 opacity-50"></i>
                                    <p class="mt-3 fs-5">No sales records found</p>
                                    <p class="text-muted">No sales match your current filters.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sales->hasPages())
        <div class="card-footer">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.card {
    border-radius: 16px;
    border: none;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #475569;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.form-select, .form-control {
    border-radius: 10px;
    border: 2px solid #e2e8f0;
}

.btn {
    border-radius: 10px;
    font-weight: 500;
}
</style>
@endsection 
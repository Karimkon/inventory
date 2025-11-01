@extends('shop.layouts.app')

@section('title', 'Sales History')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📊 Sales History</h1>
        <a href="{{ route('shop.products.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Time Range</label>
                    <select name="time_range" class="form-select" onchange="this.form.submit()">
                        <option value="today" {{ $timeRange == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $timeRange == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $timeRange == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="custom" {{ $timeRange == 'custom' ? 'selected' : '' }}>Custom Date</option>
                    </select>
                </div>
                
                @if($timeRange == 'custom')
                <div class="col-md-3">
                    <label class="form-label">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" 
                           onchange="this.form.submit()">
                </div>
                @endif
                
                <div class="col-md-3">
                    <label class="form-label">Filter by Product</label>
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
                    <a href="{{ route('shop.products.sales-history') }}" class="btn btn-outline-secondary">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white text-center p-3">
                <h5>Total Sales</h5>
                <h3>{{ $totalSales }}</h3>
                <small>Transactions</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white text-center p-3">
                <h5>Items Sold</h5>
                <h3>{{ $totalQuantity }}</h3>
                <small>Units</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white text-center p-3">
                <h5>Total Revenue</h5>
                <h3>UGX {{ number_format($totalRevenue) }}</h3>
                <small>Amount</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-dark text-center p-3">
                <h5>Total Profit</h5>
                <h3>UGX {{ number_format($totalProfit) }}</h3>
                <small>Earnings</small>
            </div>
        </div>
    </div>

    <!-- Sales by Product Summary -->
    @if($salesByProduct->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">📈 Sales Summary by Product</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Quantity Sold</th>
                            <th>Total Revenue</th>
                            <th>Total Profit</th>
                            <th>Sales Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByProduct as $productSales)
                        <tr>
                            <td class="fw-semibold">{{ $productSales['product_name'] }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $productSales['total_quantity'] }}</span>
                            </td>
                            <td class="text-success fw-bold">UGX {{ number_format($productSales['total_revenue']) }}</td>
                            <td class="text-warning fw-bold">UGX {{ number_format($productSales['total_profit']) }}</td>
                            <td>{{ $productSales['sales_count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Sales History -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🕒 Detailed Sales History</h5>
            <span class="badge bg-primary">{{ $sales->total() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Profit</th>
                            <th>Cost Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>
                                <small class="text-muted">{{ $sale->created_at->format('M d, Y') }}</small>
                                <br>
                                <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="fw-semibold">{{ $sale->product->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $sale->quantity }}</span>
                            </td>
                            <td>UGX {{ number_format($sale->sold_price) }}</td>
                            <td class="fw-bold text-success">UGX {{ number_format($sale->sold_price * $sale->quantity) }}</td>
                            <td class="fw-bold text-warning">
                                UGX {{ number_format(($sale->sold_price - $sale->cost_price) * $sale->quantity) }}
                            </td>
                            <td class="text-muted">UGX {{ number_format($sale->cost_price) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-receipt display-4"></i>
                                    <p class="mt-2">No sales records found for the selected period.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
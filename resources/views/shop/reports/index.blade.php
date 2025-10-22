@extends('shop.layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📊 Financial Reports</h1>
        <div class="btn-group">
            <a href="{{ route('shop.reports.profit-loss') }}" class="btn btn-success">
                <i class="bi bi-graph-up"></i> Profit & Loss
            </a>
            <a href="{{ route('shop.reports.balance-sheet') }}" class="btn btn-info">
                <i class="bi bi-bar-chart"></i> Balance Sheet
            </a>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-3">
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <small class="text-muted">
                        Showing data from {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
                    </small>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Financial Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white text-center p-3">
                <h5>Total Revenue</h5>
                <h3>UGX {{ number_format($salesData->total_revenue ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white text-center p-3">
                <h5>Gross Profit</h5>
                <h3>UGX {{ number_format($salesData->total_profit ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white text-center p-3">
                <h5>Units Sold</h5>
                <h3>{{ number_format($salesData->total_units_sold ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-dark text-center p-3">
                <h5>Inventory Value</h5>
                <h3>UGX {{ number_format($inventoryValue->total_inventory_value ?? 0) }}</h3>
            </div>
        </div>
    </div>

    <!-- Profitability -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">💰 Profitability Analysis</h5>
                </div>
                <div class="card-body">
                    @if($salesData->total_revenue > 0)
                    <div class="mb-3">
                        <strong>Gross Profit Margin:</strong>
                        <span class="badge bg-{{ (($salesData->total_profit / $salesData->total_revenue) * 100) > 20 ? 'success' : 'warning' }} float-end">
                            {{ number_format(($salesData->total_profit / $salesData->total_revenue) * 100, 1) }}%
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Revenue:</strong>
                        <span class="float-end">UGX {{ number_format($salesData->total_revenue) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Cost of Goods:</strong>
                        <span class="float-end">UGX {{ number_format($salesData->total_cost) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Gross Profit:</strong>
                        <span class="float-end fw-bold text-success">UGX {{ number_format($salesData->total_profit) }}</span>
                    </div>
                    @else
                    <p class="text-muted text-center">No sales data for the selected period.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">🏆 Top Performing Products</h5>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sold</th>
                                    <th>Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->total_sold }}</td>
                                    <td class="text-success">UGX {{ number_format($product->total_profit) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">No sales data for the selected period.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
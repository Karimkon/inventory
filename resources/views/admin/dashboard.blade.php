@extends('admin.layouts.app')

@section('title', 'Admin Dashboard - SaaS Overview')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">🏢 SaaS Overview - All Shops</h1>

    <!-- SaaS Overview Cards -->
    <div class="row g-4">
        <!-- Total Shops -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white p-3 text-center">
                <h5>Total Shops</h5>
                <h2>{{ $totalShops }}</h2>
                <small>Active stores in system</small>
            </div>
        </div>

        <!-- Shop Users -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white p-3 text-center">
                <h5>Shop Users</h5>
                <h2>{{ $totalUsers }}</h2>
                <small>Active shop accounts</small>
            </div>
        </div>

        <!-- Total Products -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white p-3 text-center">
                <h5>Total Products</h5>
                <h2>{{ $totalProducts }}</h2>
                <small>Across all shops</small>
            </div>
        </div>

        <!-- Today's Sales -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-dark p-3 text-center">
                <h5>Today's Sales</h5>
                <h2>{{ $salesToday }} units</h2>
                <small>Profit: UGX {{ number_format($profitToday) }}</small>
            </div>
        </div>
    </div>

    <!-- Top Performing Shops -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">🏆 Top Performing Shops</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Shop Name</th>
                                    <th>Products</th>
                                    <th>Total Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topShops as $shop)
                                <tr>
                                    <td>{{ $shop->name }}</td>
                                    <td><span class="badge bg-primary">{{ $shop->products_count }}</span></td>
                                    <td><span class="badge bg-success">{{ $shop->sales_sum_quantity ?? 0 }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Shops -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">🆕 Recently Added Shops</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Shop Name</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentShops as $shop)
                                <tr>
                                    <td>{{ $shop->name }}</td>
                                    <td>{{ $shop->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Performance -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📈 System-wide Sales Performance</h5>
                    <div class="d-flex gap-2">
                        <div class="badge bg-primary">Today: UGX {{ number_format($profitToday) }}</div>
                        <div class="badge bg-success">Week: UGX {{ number_format($profitWeek) }}</div>
                        <div class="badge bg-info">Month: UGX {{ number_format($profitMonth) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Sales Across All Shops -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🛒 Latest Sales Across All Shops</h5>
                    <form class="d-flex gap-2" method="GET" action="{{ route('admin.dashboard') }}">
                        <select name="filter" class="form-select form-select-sm">
                            <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>This Month</option>
                        </select>
                        <button class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Shop</th>
                                    <th>Product</th>
                                    <th>Qty Sold</th>
                                    <th>Price</th>
                                    <th>Profit</th>
                                    <th>Sold At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestSales as $sale)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-secondary">{{ $sale->shop->name ?? 'N/A' }}</span></td>
                                    <td>{{ $sale->product->name }}</td>
                                    <td>{{ $sale->quantity }}</td>
                                    <td>UGX {{ number_format($sale->sold_price, 2) }}</td>
                                    <td>UGX {{ number_format(($sale->sold_price - $sale->cost_price) * $sale->quantity, 2) }}</td>
                                    <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $latestSales->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
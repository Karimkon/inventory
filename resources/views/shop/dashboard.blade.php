@extends('shop.layouts.app')

@section('title', 'Shop Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">🛍️ Shop Dashboard</h1>

    <div class="row g-4">
        <!-- Total Products -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-white p-3 text-center">
                <h5>Total Products</h5>
                <h2>{{ $totalProducts }}</h2>
            </div>
        </div>

        <!-- Expected Revenue -->
<div class="col-md-3">
    <div class="card shadow-sm bg-success text-white p-3 text-center">
        <h5>Stock Value</h5>
        <h2>UGX {{ number_format($expectedRevenue) }}</h2>
        <small>Potential: UGX {{ number_format($potentialProfit) }}</small>
    </div>
</div>

        <!-- Sales Today -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-light p-3 text-center">
                <h5>Sold Today</h5>
                <h2>{{ $salesToday }} units</h2>
                <p>Profit: UGX {{ number_format($profitToday) }}</p>
            </div>
        </div>

        <!-- Sales This Week -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-light p-3 text-center">
                <h5>This Week</h5>
                <h2>{{ $salesWeek }} units</h2>
                <p>Profit: UGX {{ number_format($profitWeek) }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white p-3 text-center">
                <h5>Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('shop.products.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </a>
                    <a href="{{ route('shop.products.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-box-seam"></i> View Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📦 Recent Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Stock</th>
                                    <th>Cost Price</th>
                                    <th>Selling Price</th>
                                    <th>Profit Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProducts as $product)
                                <tr>
                                    <td class="fw-semibold">{{ $product->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock > 5 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td>UGX {{ number_format($product->cost_price) }}</td>
                                    <td>UGX {{ number_format($product->price) }}</td>
                                    <td>
                                        <span class="badge bg-{{ ($product->price - $product->cost_price) > 0 ? 'success' : 'danger' }}">
                                            UGX {{ number_format($product->price - $product->cost_price) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this to shop/dashboard.blade.php -->
@if($lowStockProducts > 0 || $outOfStockProducts > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-{{ $outOfStockProducts > 0 ? 'danger' : 'warning' }}">
            <div class="card-header bg-{{ $outOfStockProducts > 0 ? 'danger' : 'warning' }} text-white">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Stock Alerts
                </h5>
            </div>
            <div class="card-body">
                @if($outOfStockProducts > 0)
                <div class="alert alert-danger">
                    <strong>{{ $outOfStockProducts }} products out of stock!</strong>
                </div>
                @endif
                
                @if($lowStockProducts > 0)
                <div class="alert alert-warning">
                    <strong>{{ $lowStockProducts }} products running low on stock</strong>
                </div>
                @endif

                @if($lowStockItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->stock == 0 ? 'danger' : 'warning' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->stock == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                    <span class="badge bg-warning">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
</div>
@endsection
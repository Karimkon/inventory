@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">📊 Dashboard</h1>

    <div class="row g-4">
        <!-- Total Products -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-white p-3 text-center">
                <h5>Total Products</h5>
                <h2>{{ $totalProducts }}</h2>
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
                <h5>Sold This Week</h5>
                <h2>{{ $salesWeek }} units</h2>
                <p>Profit: UGX {{ number_format($profitWeek) }}</p>
            </div>
        </div>

        <!-- Sales This Month -->
        <div class="col-md-3">
            <div class="card shadow-sm bg-light p-3 text-center">
                <h5>Sold This Month</h5>
                <h2>{{ $salesMonth }} units</h2>
                <p>Profit: UGX {{ number_format($profitMonth) }}</p>
            </div>
        </div>
    </div>

    <!-- Last Sale -->
    @if(session('last_sale'))
        @php $sale = session('last_sale'); @endphp
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm bg-light p-3">
                    <h5>🛒 Last Sale</h5>
                    <p>
                        {{ $sale['qty'] }} of <b>{{ $sale['product_name'] }}</b> sold at {{ $sale['sold_at'] }}<br>
                        Profit: <b>UGX {{ number_format($sale['profit']) }}</b>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Latest Sold Items -->
<div class="mt-5">
    <div class="table-responsive">
        <div class="mt-5 d-flex justify-content-between align-items-center">
    <h4>🛒 Latest Sold Items</h4>

    <form class="d-flex gap-2" method="GET" action="{{ route('admin.dashboard') }}">
        <select name="filter" class="form-select">
            <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
            <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>This Week</option>
            <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>This Month</option>
            <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom</option>
        </select>

        <input type="date" name="start" class="form-control" value="{{ request('start') }}">
        <input type="date" name="end" class="form-control" value="{{ request('end') }}">
        <button class="btn btn-primary">Filter</button>
    </form>

</div>

        <table class="table table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                    <th>Selling Price</th>
                    <th>Cost Price</th>
                    <th>Profit</th>
                    <th>Sold At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestSales as $sale)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sale->product->name }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td>{{ number_format($sale->sold_price,2) }}</td>
                    <td>{{ number_format($sale->cost_price,2) }}</td>
                    <td>{{ number_format(($sale->sold_price - $sale->cost_price) * $sale->quantity,2) }}</td>
                    <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

    <!-- Recent Products -->
    <div class="mt-5">
        <h4>Recent Products</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Cost</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentProducts as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>{{ number_format($product->price,2) }}</td>
                            <td>{{ number_format($product->cost_price,2) }}</td>
                            <td>{{ $product->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'All SN Hardware Products and Price')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-box-seam me-2"></i> All SN Hardware Products and Price</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add New Product
        </a>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Last Sale Info --}}
    @if(session('last_sale'))
        @php $sale = session('last_sale'); @endphp
        <div class="alert alert-info">
            <i class="bi bi-receipt me-2"></i>
            <strong>Last Sale:</strong> {{ $sale['qty'] }} of <b>{{ $sale['product_name'] }}</b> sold at 
            {{ $sale['sold_at'] }}. Profit: <b>UGX {{ number_format($sale['profit']) }}</b>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-sm-10 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               class="form-control" placeholder="Search products by name...">
                    </div>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Stock</th>
                            <th>Amount Bought (Cost)</th>
                            <th>Selling Price</th>
                            <th style="width: 350px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>
                                <span class="badge bg-{{ $product->stock > 5 ? 'success' : 'warning' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td>{{ number_format($product->cost_price,2) }}</td>
                            <td>{{ number_format($product->price, 2) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                   <form action="{{ route('admin.products.sell', $product) }}" method="POST" class="d-flex align-items-center">
                                        @csrf
                                        <input type="number" name="quantity" min="1" max="{{ $product->stock }}" 
                                            class="form-control form-control-sm me-1" style="width:70px;">
                                        <select name="print" class="form-select form-select-sm me-1" style="width:90px;">
                                            <option value="no">No Print</option>
                                            <option value="yes">Print</option>
                                        </select>
                                        <button class="btn btn-sm btn-danger"><i class="bi bi-cart-dash"></i> Sell</button>
                                    </form>


                                    <!-- Delete form -->
                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection

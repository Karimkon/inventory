@extends('admin.layouts.app')

@section('title', 'All Products - All Shops')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📦 All Products - All Shops</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Product
        </a>
    </div>

    <!-- Shop Filter -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-sm-5">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               class="form-control" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-sm-5">
                    <select name="shop_id" class="form-select">
                        <option value="">All Shops</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
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
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Shop</th>
                            <th>Product Name</th>
                            <th>Stock</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th style="width: 350px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $product->shop->name }}</span>
                            </td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>
                                <span class="badge bg-{{ $product->stock > 5 ? 'success' : 'warning' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td>UGX {{ number_format($product->cost_price, 2) }}</td>
                            <td>UGX {{ number_format($product->price, 2) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <!-- Your existing action buttons -->
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

                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure?');">
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
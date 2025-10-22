@extends('admin.layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">➕ Add Product</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Shop *</label>
            <select name="shop_id" class="form-control" required>
                <option value="">Select Shop</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cost Price (Amount bought)</label>
            <input type="number" name="cost_price" class="form-control" value="{{ old('cost_price') }}" min="0" step="0.01" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control" value="{{ old('price') }}" min="0" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-success">Add Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection

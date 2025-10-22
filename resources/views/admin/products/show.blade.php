@extends('admin.layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">🔍 Product Details</h1>

    <div class="card p-3 mb-3">
        <h5>Name: {{ $product->name }}</h5>
        <h5>Stock: {{ $product->stock }}</h5>
        <h5>Price: {{ number_format($product->price,2) }}</h5>
        <h5>Created At: {{ $product->created_at->format('d M Y') }}</h5>
        <h5>Updated At: {{ $product->updated_at->format('d M Y') }}</h5>
    </div>

    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to Products</a>
</div>
@endsection

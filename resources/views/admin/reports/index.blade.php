@extends('admin.layouts.app')

@section('title', 'Products Report')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">📋 Products Report</h1>

    <a href="{{ route('admin.reports.pdf') }}" class="btn btn-primary mb-3">
        <i class="bi bi-download"></i> Download PDF
    </a>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ number_format($product->cost_price, 2) }}</td>
                    <td>{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->created_at->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

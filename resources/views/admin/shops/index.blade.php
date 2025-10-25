@extends('admin.layouts.app')

@section('title', 'Manage Shops')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🏢 Manage Shops</h1>
        <a href="{{ route('admin.shops.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Shop
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Shop Name</th>
                            <th>Slug</th>
                            <th>POS PIN</th>
                            <th>Products</th>
                            <th>Users</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shops as $shop)
                        <tr>
                            <td>{{ $shop->id }}</td>
                            <td class="fw-semibold">{{ $shop->name }}</td>
                            <td><code>{{ $shop->slug }}</code></td>
                            <td>
                                <code class="text-success fw-bold">{{ $shop->pos_pin ?? 'N/A' }}</code>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $shop->products_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $shop->users_count }}</span>
                            </td>
                            <td>{{ $shop->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.shops.destroy', $shop) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this shop?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $shops->links() }}
        </div>
    </div>
</div>
@endsection
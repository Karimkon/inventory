@extends('admin.layouts.app')

@section('title', 'Shop Details - ' . $shop->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🏪 Shop Details: {{ $shop->name }}</h1>
        <div>
            <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Shops
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Shop Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Shop ID:</th>
                            <td>{{ $shop->id }}</td>
                        </tr>
                        <tr>
                            <th>Shop Name:</th>
                            <td class="fw-semibold">{{ $shop->name }}</td>
                        </tr>
                        <tr>
                            <th>Slug:</th>
                            <td><code>{{ $shop->slug }}</code></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $shop->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $shop->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h3 class="text-primary">{{ $shop->products->count() }}</h3>
                                <small class="text-muted">Products</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h3 class="text-info">{{ $shop->users->count() }}</h3>
                                <small class="text-muted">Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">👥 Shop Users</h5>
        </div>
        <div class="card-body">
            @if($shop->users->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shop->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-secondary">{{ $user->role }}</span></td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted text-center">No users found for this shop.</p>
            @endif
        </div>
    </div>
</div>
@endsection
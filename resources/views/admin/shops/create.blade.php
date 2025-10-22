@extends('admin.layouts.app')

@section('title', 'Add New Shop')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>➕ Add New Shop</h1>
        <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Shops
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.shops.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Shop Information</h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Shop Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Shop Slug *</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug') }}" required>
                            <div class="form-text">Unique identifier for the shop (lowercase, no spaces)</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Shop Admin User</h5>
                        
                        <div class="mb-3">
                            <label for="admin_name" class="form-label">Admin Name *</label>
                            <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                   id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                            @error('admin_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Admin Email *</label>
                            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                   id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                            @error('admin_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="admin_password" class="form-label">Admin Password *</label>
                            <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                   id="admin_password" name="admin_password" required>
                            @error('admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Shop
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
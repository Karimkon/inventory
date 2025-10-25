@extends('admin.layouts.app')

@section('title', 'Edit Shop - ' . $shop->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>✏️ Edit Shop: {{ $shop->name }}</h1>
        <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Shops
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.shops.update', $shop) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Shop Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $shop->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Shop Slug *</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug', $shop->slug) }}" required>
                            <div class="form-text">Unique identifier for the shop</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
            <label for="pos_pin" class="form-label">POS PIN *</label>
            <div class="input-group">
                <input type="text" class="form-control @error('pos_pin') is-invalid @enderror" 
                       id="pos_pin" name="pos_pin" value="{{ old('pos_pin', $shop->pos_pin) }}" 
                       maxlength="4" pattern="[0-9]{4}" required>
                <button type="button" class="btn btn-outline-secondary" onclick="generatePin()">
                    <i class="bi bi-arrow-repeat"></i> Generate
                </button>
            </div>
            <div class="form-text">4-digit PIN for POS access</div>
            @error('pos_pin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Shop
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function generatePin() {
    // Generate random 4-digit PIN
    const pin = Math.floor(1000 + Math.random() * 9000).toString();
    document.getElementById('pos_pin').value = pin;
}
</script>
@endsection
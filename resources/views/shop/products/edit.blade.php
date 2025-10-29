@extends('shop.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>✏️ Edit Product</h1>
        <a href="{{ route('shop.products.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('shop.products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Current Stock *</label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                   id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cost_price" class="form-label">Cost Price (Buying Price) *</label>
                            <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" 
                                   id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" required>
                            <div class="form-text">The amount you paid to acquire this product</div>
                            @error('cost_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Selling Price *</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', $product->price) }}" min="0" required>
                            <div class="form-text">The price at which you'll sell to customers</div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Quick Stock Update Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">🔄 Quick Stock Update</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label">Add to Current Stock</label>
                                        <div class="input-group">
                                            <input type="number" id="add_stock" class="form-control" min="0" value="0">
                                            <button type="button" class="btn btn-success" onclick="updateStock('add')">
                                                <i class="bi bi-plus-circle"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Set New Stock Level</label>
                                        <div class="input-group">
                                            <input type="number" id="set_stock" class="form-control" min="0" value="{{ $product->stock }}">
                                            <button type="button" class="btn btn-warning" onclick="updateStock('set')">
                                                <i class="bi bi-arrow-clockwise"></i> Set
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="p-3 bg-white rounded border">
                                            <small class="text-muted d-block">Current Stock</small>
                                            <strong class="h4 text-primary" id="current_stock_display">{{ $product->stock }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('shop.products.index') }}" class="btn btn-secondary me-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStock(action) {
    let stockInput, stockValue;
    
    if (action === 'add') {
        stockInput = document.getElementById('add_stock');
        stockValue = parseInt(stockInput.value);
        if (action === 'add') {
            stockValue = parseInt(document.getElementById('stock').value) + stockValue;
        }
    } else {
        stockInput = document.getElementById('set_stock');
        stockValue = parseInt(stockInput.value);
    }

    if (isNaN(stockValue) || stockValue < 0) {
        alert('Please enter a valid stock number');
        return;
    }

    // Update the stock field and submit the form
    document.getElementById('stock').value = stockValue;
    document.getElementById('set_stock').value = stockValue;
    document.getElementById('add_stock').value = 0;
    
    // Update display immediately
    document.getElementById('current_stock_display').textContent = stockValue;
    
    showAlert('Stock updated locally. Remember to click "Update Product" to save changes.', 'success');
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.card-body').prepend(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>
@endsection
@extends('shop.layouts.app')

@section('title', 'Edit Asset - ' . $depreciation->asset_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>✏️ Edit Asset: {{ $depreciation->asset_name }}</h1>
        <a href="{{ route('shop.depreciation.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Assets
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('shop.depreciation.update', $depreciation) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="asset_name" class="form-label">Asset Name *</label>
                            <input type="text" class="form-control @error('asset_name') is-invalid @enderror" 
                                   id="asset_name" name="asset_name" value="{{ old('asset_name', $depreciation->asset_name) }}" required>
                            @error('asset_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="current_value" class="form-label">Current Value (UGX) *</label>
                            <input type="number" step="0.01" class="form-control @error('current_value') is-invalid @enderror" 
                                   id="current_value" name="current_value" value="{{ old('current_value', $depreciation->current_value) }}" 
                                   min="0" required>
                            @error('current_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="depreciation_rate" class="form-label">Annual Depreciation Rate (%) *</label>
                            <input type="number" step="0.01" class="form-control @error('depreciation_rate') is-invalid @enderror" 
                                   id="depreciation_rate" name="depreciation_rate" value="{{ old('depreciation_rate', $depreciation->depreciation_rate) }}" 
                                   min="0" max="100" required>
                            @error('depreciation_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $depreciation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Asset Information (Readonly) -->
                <div class="card bg-light mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">📋 Asset Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Original Cost:</strong> UGX {{ number_format($depreciation->purchase_cost) }}
                            </div>
                            <div class="col-md-4">
                                <strong>Purchase Date:</strong> {{ $depreciation->purchase_date->format('M d, Y') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Useful Life:</strong> {{ $depreciation->useful_life_years }} years
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle"></i> Update Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
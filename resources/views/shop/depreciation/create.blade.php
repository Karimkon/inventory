@extends('shop.layouts.app')

@section('title', 'Add Depreciable Asset')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>➕ Add Depreciable Asset</h1>
        <a href="{{ route('shop.depreciation.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Assets
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('shop.depreciation.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="asset_name" class="form-label">Asset Name *</label>
                            <input type="text" class="form-control @error('asset_name') is-invalid @enderror" 
                                   id="asset_name" name="asset_name" value="{{ old('asset_name') }}" required
                                   placeholder="e.g., Delivery Van, Computer, Furniture">
                            @error('asset_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchase_cost" class="form-label">Purchase Cost (UGX) *</label>
                            <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror" 
                                   id="purchase_cost" name="purchase_cost" value="{{ old('purchase_cost') }}" 
                                   min="0" required placeholder="e.g., 5000000">
                            @error('purchase_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="depreciation_rate" class="form-label">Annual Depreciation Rate (%) *</label>
                            <input type="number" step="0.01" class="form-control @error('depreciation_rate') is-invalid @enderror" 
                                   id="depreciation_rate" name="depreciation_rate" value="{{ old('depreciation_rate', '10') }}" 
                                   min="0" max="100" required placeholder="e.g., 10">
                            <div class="form-text">Common rates: Vehicles 20%, Equipment 15%, Furniture 10%, Buildings 5%</div>
                            @error('depreciation_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Purchase Date *</label>
                            <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                   id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="useful_life_years" class="form-label">Useful Life (Years) *</label>
                            <input type="number" class="form-control @error('useful_life_years') is-invalid @enderror" 
                                   id="useful_life_years" name="useful_life_years" value="{{ old('useful_life_years', '5') }}" 
                                   min="1" max="50" required placeholder="e.g., 5">
                            @error('useful_life_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Additional details about the asset">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Depreciation Preview -->
                <div class="card bg-light mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">📊 Depreciation Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Annual Depreciation</small>
                                <div class="fw-bold text-primary" id="previewAnnual">UGX 0</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Monthly Depreciation</small>
                                <div class="fw-bold text-warning" id="previewMonthly">UGX 0</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Estimated Current Value</small>
                                <div class="fw-bold text-success" id="previewCurrent">UGX 0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle"></i> Add Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Real-time depreciation calculation
function calculateDepreciation() {
    const purchaseCost = parseFloat(document.getElementById('purchase_cost').value) || 0;
    const depreciationRate = parseFloat(document.getElementById('depreciation_rate').value) || 0;
    const purchaseDate = new Date(document.getElementById('purchase_date').value);
    const currentDate = new Date();

    // Calculate annual and monthly depreciation
    const annualDepreciation = (purchaseCost * depreciationRate) / 100;
    const monthlyDepreciation = annualDepreciation / 12;

    // Calculate current value based on time elapsed
    const monthsOwned = (currentDate.getFullYear() - purchaseDate.getFullYear()) * 12 
                      + (currentDate.getMonth() - purchaseDate.getMonth());
    const totalDepreciation = Math.min((annualDepreciation / 12) * monthsOwned, purchaseCost);
    const currentValue = Math.max(purchaseCost - totalDepreciation, 0);

    // Update preview
    document.getElementById('previewAnnual').textContent = 'UGX ' + annualDepreciation.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('previewMonthly').textContent = 'UGX ' + monthlyDepreciation.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('previewCurrent').textContent = 'UGX ' + currentValue.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Add event listeners for real-time calculation
document.getElementById('purchase_cost').addEventListener('input', calculateDepreciation);
document.getElementById('depreciation_rate').addEventListener('input', calculateDepreciation);
document.getElementById('purchase_date').addEventListener('change', calculateDepreciation);

// Initial calculation
calculateDepreciation();
</script>
@endsection
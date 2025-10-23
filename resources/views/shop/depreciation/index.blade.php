@extends('shop.layouts.app')

@section('title', 'Depreciation Assets')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🏢 Depreciation Assets</h1>
        <div>
            <a href="{{ route('shop.depreciation.financial-analysis') }}" class="btn btn-info me-2">
                <i class="bi bi-graph-up"></i> Financial Analysis
            </a>
            <a href="{{ route('shop.depreciation.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add Asset
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Depreciation Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-3">
                    <h6>Total Asset Cost</h6>
                    <h4>UGX {{ number_format($totalOriginalCost) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center py-3">
                    <h6>Current Value</h6>
                    <h4>UGX {{ number_format($totalCurrentValue) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center py-3">
                    <h6>Total Depreciation</h6>
                    <h4>UGX {{ number_format($totalDepreciationExpense) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center py-3">
                    <h6>Monthly Expense</h6>
                    <h4>UGX {{ number_format($totalDepreciationExpense / 12) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Asset Name</th>
                            <th>Purchase Cost</th>
                            <th>Current Value</th>
                            <th>Depreciation Rate</th>
                            <th>Purchase Date</th>
                            <th>Useful Life</th>
                            <th>Monthly Depreciation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td class="fw-semibold">{{ $asset->asset_name }}</td>
                            <td>UGX {{ number_format($asset->purchase_cost) }}</td>
                            <td class="fw-bold text-{{ $asset->current_value > 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($asset->current_value) }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $asset->depreciation_rate }}%</span>
                            </td>
                            <td>{{ $asset->purchase_date->format('M d, Y') }}</td>
                            <td>{{ $asset->useful_life_years }} years</td>
                            <td class="text-warning">UGX {{ number_format($asset->monthly_depreciation) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('shop.depreciation.edit', $asset) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('shop.depreciation.destroy', $asset) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this asset?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-building display-4"></i>
                                    <p class="mt-2">No depreciable assets recorded yet.</p>
                                    <a href="{{ route('shop.depreciation.create') }}" class="btn btn-primary btn-sm">
                                        Add Your First Asset
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $assets->links() }}
        </div>
    </div>

    <!-- Recalculate Button -->
    <div class="text-center mt-3">
        <form action="{{ route('shop.depreciation.recalculate-values') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-calculator"></i> Recalculate All Asset Values
            </button>
        </form>
    </div>
</div>
@endsection
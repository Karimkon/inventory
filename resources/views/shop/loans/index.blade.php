@extends('shop.layouts.app')

@section('title', 'Manage Loans')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🏦 Manage Loans</h1>
        <a href="{{ route('shop.loans.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Loan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Loan Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Borrowed</h6>
                            <h4 class="mb-0">UGX {{ number_format($totalPrincipal) }}</h4>
                        </div>
                        <i class="bi bi-cash-coin display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Remaining Balance</h6>
                            <h4 class="mb-0">UGX {{ number_format($totalRemaining) }}</h4>
                        </div>
                        <i class="bi bi-clock-history display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Interest Paid</h6>
                            <h4 class="mb-0">UGX {{ number_format($totalInterestPaid) }}</h4>
                        </div>
                        <i class="bi bi-graph-up display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Loan Name</th>
                            <th>Lender</th>
                            <th>Principal</th>
                            <th>Interest Rate</th>
                            <th>Monthly Payment</th>
                            <th>Remaining Balance</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr>
                            <td class="fw-semibold">{{ $loan->loan_name }}</td>
                            <td>{{ $loan->lender_name }}</td>
                            <td>UGX {{ number_format($loan->principal_amount) }}</td>
                            <td>
                                <span class="badge bg-info">{{ $loan->interest_rate }}%</span>
                            </td>
                            <td class="fw-bold text-primary">UGX {{ number_format($loan->monthly_payment) }}</td>
                            <td class="fw-bold text-{{ $loan->remaining_balance > 0 ? 'warning' : 'success' }}">
                                UGX {{ number_format($loan->remaining_balance) }}
                            </td>
                            <td>
                                @php
                                    $progress = (($loan->principal_amount - $loan->remaining_balance) / $loan->principal_amount) * 100;
                                @endphp
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $progress == 100 ? 'success' : 'primary' }}" 
                                         style="width: {{ $progress }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($progress, 1) }}% paid</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('shop.loans.show', $loan) }}" class="btn btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('shop.loans.edit', $loan) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('shop.loans.destroy', $loan) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this loan?')">
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
                                    <i class="bi bi-cash-coin display-4"></i>
                                    <p class="mt-2">No loans recorded yet.</p>
                                    <a href="{{ route('shop.loans.create') }}" class="btn btn-primary btn-sm">
                                        Record Your First Loan
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
            {{ $loans->links() }}
        </div>
    </div>
</div>
@endsection
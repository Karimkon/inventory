@extends('shop.layouts.app')

@section('title', 'Edit Loan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>✏️ Edit Loan</h1>
        <a href="{{ route('shop.loans.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Loans
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('shop.loans.update', $loan) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="loan_name" class="form-label">Loan Name *</label>
                            <input type="text" class="form-control @error('loan_name') is-invalid @enderror" 
                                   id="loan_name" name="loan_name" value="{{ old('loan_name', $loan->loan_name) }}" required>
                            @error('loan_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lender_name" class="form-label">Lender Name *</label>
                            <input type="text" class="form-control @error('lender_name') is-invalid @enderror" 
                                   id="lender_name" name="lender_name" value="{{ old('lender_name', $loan->lender_name) }}" required>
                            @error('lender_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="remaining_balance" class="form-label">Remaining Balance (UGX) *</label>
                            <input type="number" step="0.01" class="form-control @error('remaining_balance') is-invalid @enderror" 
                                   id="remaining_balance" name="remaining_balance" 
                                   value="{{ old('remaining_balance', $loan->remaining_balance) }}" min="0" required>
                            @error('remaining_balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="purpose" class="form-label">Loan Purpose</label>
                    <textarea class="form-control @error('purpose') is-invalid @enderror" 
                              id="purpose" name="purpose" rows="3">{{ old('purpose', $loan->purpose) }}</textarea>
                    @error('purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Display read-only loan details -->
                <div class="card bg-light mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">📊 Loan Details (Read Only)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Original Amount:</strong><br>
                                UGX {{ number_format($loan->principal_amount) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Interest Rate:</strong><br>
                                {{ $loan->interest_rate }}%
                            </div>
                            <div class="col-md-3">
                                <strong>Monthly Payment:</strong><br>
                                UGX {{ number_format($loan->monthly_payment) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Term:</strong><br>
                                {{ $loan->term_months }} months
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Update Loan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('shop.layouts.app')

@section('title', 'Add New Loan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>➕ Add New Loan</h1>
        <a href="{{ route('shop.loans.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Loans
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('shop.loans.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="loan_name" class="form-label">Loan Name *</label>
                            <input type="text" class="form-control @error('loan_name') is-invalid @enderror" 
                                   id="loan_name" name="loan_name" value="{{ old('loan_name') }}" required
                                   placeholder="e.g., Business Expansion Loan">
                            @error('loan_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lender_name" class="form-label">Lender Name *</label>
                            <input type="text" class="form-control @error('lender_name') is-invalid @enderror" 
                                   id="lender_name" name="lender_name" value="{{ old('lender_name') }}" required
                                   placeholder="e.g., Centenary Bank, Finance Company">
                            @error('lender_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="principal_amount" class="form-label">Loan Amount (UGX) *</label>
                            <input type="number" step="0.01" class="form-control @error('principal_amount') is-invalid @enderror" 
                                   id="principal_amount" name="principal_amount" value="{{ old('principal_amount') }}" 
                                   min="0" required placeholder="e.g., 1000000">
                            @error('principal_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="interest_rate" class="form-label">Annual Interest Rate (%) *</label>
                            <input type="number" step="0.01" class="form-control @error('interest_rate') is-invalid @enderror" 
                                   id="interest_rate" name="interest_rate" value="{{ old('interest_rate') }}" 
                                   min="0" max="100" required placeholder="e.g., 15.5">
                            @error('interest_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="term_months" class="form-label">Loan Term (Months) *</label>
                            <input type="number" class="form-control @error('term_months') is-invalid @enderror" 
                                   id="term_months" name="term_months" value="{{ old('term_months') }}" 
                                   min="1" required placeholder="e.g., 24">
                            @error('term_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="purpose" class="form-label">Loan Purpose</label>
                    <textarea class="form-control @error('purpose') is-invalid @enderror" 
                              id="purpose" name="purpose" rows="3" 
                              placeholder="What will this loan be used for?">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Loan Preview -->
                <div class="card bg-light mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">📊 Loan Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Estimated Monthly Payment</small>
                                <div class="fw-bold text-primary" id="previewPayment">UGX 0</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Total Interest</small>
                                <div class="fw-bold text-warning" id="previewInterest">UGX 0</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Total Repayment</small>
                                <div class="fw-bold text-success" id="previewTotal">UGX 0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle"></i> Record Loan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Real-time loan calculation
function calculateLoan() {
    const principal = parseFloat(document.getElementById('principal_amount').value) || 0;
    const interestRate = parseFloat(document.getElementById('interest_rate').value) || 0;
    const termMonths = parseInt(document.getElementById('term_months').value) || 1;

    const monthlyRate = (interestRate / 100) / 12;
    
    let monthlyPayment, totalInterest, totalRepayment;

    if (monthlyRate > 0) {
        monthlyPayment = (principal * monthlyRate * Math.pow(1 + monthlyRate, termMonths)) 
                       / (Math.pow(1 + monthlyRate, termMonths) - 1);
    } else {
        monthlyPayment = principal / termMonths;
    }

    totalRepayment = monthlyPayment * termMonths;
    totalInterest = totalRepayment - principal;

    // Update preview
    document.getElementById('previewPayment').textContent = 'UGX ' + monthlyPayment.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('previewInterest').textContent = 'UGX ' + totalInterest.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('previewTotal').textContent = 'UGX ' + totalRepayment.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Add event listeners for real-time calculation
document.getElementById('principal_amount').addEventListener('input', calculateLoan);
document.getElementById('interest_rate').addEventListener('input', calculateLoan);
document.getElementById('term_months').addEventListener('input', calculateLoan);

// Initial calculation
calculateLoan();
</script>
@endsection
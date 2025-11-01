@extends('shop.layouts.app')

@section('title', 'Edit Expense')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>✏️ Edit Expense</h1>
        <div>
            <a href="{{ route('shop.expenses.show', $expense) }}" class="btn btn-info me-2">
                <i class="bi bi-eye"></i> View Details
            </a>
            <a href="{{ route('shop.expenses.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Expenses
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="card-title mb-0">
                <i class="bi bi-pencil-square"></i> Edit Expense Record
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('shop.expenses.update', $expense) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select name="category" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $name)
                                    <option value="{{ $key }}" 
                                        {{ old('category', $expense->category) == $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                   id="description" name="description" 
                                   value="{{ old('description', $expense->description) }}" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (UGX) *</label>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" 
                                   value="{{ old('amount', $expense->amount) }}" min="0" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="expense_date" class="form-label">Expense Date *</label>
                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                   id="expense_date" name="expense_date" 
                                   value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4" 
                                      placeholder="Add any additional notes about this expense...">{{ old('notes', $expense->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Information -->
                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-info-circle"></i> Current Information
                                </h6>
                                <div class="small">
                                    <div><strong>Created:</strong> {{ $expense->created_at->format('M d, Y h:i A') }}</div>
                                    <div><strong>Last Updated:</strong> {{ $expense->updated_at->format('M d, Y h:i A') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('shop.expenses.show', $expense) }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Update Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Set focus to first field with error, or description field
document.addEventListener('DOMContentLoaded', function() {
    const firstErrorField = document.querySelector('.is-invalid');
    if (firstErrorField) {
        firstErrorField.focus();
    } else {
        document.getElementById('description').focus();
    }
});
</script>
@endsection                 
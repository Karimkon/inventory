@extends('shop.layouts.app')

@section('title', 'Expense Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📄 Expense Details</h1>
        <div>
            <a href="{{ route('shop.expenses.index') }}" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to Expenses
            </a>
            <a href="{{ route('shop.expenses.edit', $expense) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('shop.expenses.destroy', $expense) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this expense?')">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt"></i> Expense Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Description:</th>
                                    <td>{{ $expense->description }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        <span class="badge bg-secondary">{{ $expense->category_name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td class="fw-bold text-danger fs-5">
                                        UGX {{ number_format($expense->amount, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Date:</th>
                                    <td>{{ $expense->expense_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $expense->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $expense->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($expense->notes)
                    <div class="mt-4">
                        <h6 class="text-muted">Additional Notes:</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">{{ $expense->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('shop.expenses.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Add New Expense
                        </a>
                        <a href="{{ route('shop.expenses.analytics') }}" class="btn btn-outline-primary">
                            <i class="bi bi-graph-up"></i> View Analytics
                        </a>
                    </div>
                </div>
            </div>

            <!-- Category Summary -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-tags"></i> Category Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="display-6 text-primary">
                            <i class="bi bi-{{ $expense->category_icon }}"></i>
                        </div>
                        <h5 class="mt-2">{{ $expense->category_name }}</h5>
                        <p class="text-muted small">
                            This expense is categorized under {{ $expense->category_name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    font-weight: 600;
    color: #6c757d;
}
.card-header {
    border-bottom: none;
}
</style>
@endsection
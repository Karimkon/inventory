@extends('shop.layouts.app')

@section('title', 'Manage Expenses')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>💰 Manage Expenses</h1>
        <div>
            <a href="{{ route('shop.expenses.analytics') }}" class="btn btn-info me-2">
                <i class="bi bi-graph-up"></i> Analytics
            </a>
            <a href="{{ route('shop.expenses.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add Expense
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-3">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           class="form-control" placeholder="Search expenses...">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $name)
                            <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="month" name="month" class="form-control" value="{{ $month }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-3 text-end">
                    <div class="text-muted">
                        <strong>This Month:</strong> UGX {{ number_format($totalThisMonth) }}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $expense->category_name }}</span>
                            </td>
                            <td>{{ $expense->description }}</td>
                            <td class="fw-bold text-danger">UGX {{ number_format($expense->amount, 2) }}</td>
                            <td>
                                @if($expense->notes)
                                    <small class="text-muted">{{ Str::limit($expense->notes, 30) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('shop.expenses.show', $expense) }}" class="btn btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('shop.expenses.edit', $expense) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('shop.expenses.destroy', $expense) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this expense?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-receipt display-4"></i>
                                    <p class="mt-2">No expenses recorded yet.</p>
                                    <a href="{{ route('shop.expenses.create') }}" class="btn btn-primary btn-sm">
                                        Record Your First Expense
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
            {{ $expenses->links() }}
        </div>
    </div>
</div>
@endsection
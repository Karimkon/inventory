@extends('shop.layouts.app')

@section('title', 'Expense Analytics')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📊 Expense Analytics</h1>
        <a href="{{ route('shop.expenses.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Expenses
        </a>
    </div>

    <!-- Month Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Select Month:</label>
                    <input type="month" name="month" class="form-control" value="{{ $month }}" 
                           onchange="this.form.submit()">
                </div>
                <div class="col-md-9">
                    <small class="text-muted">
                        Expense analysis for {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                    </small>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm bg-danger text-white text-center p-3">
                <h5>Total Expenses</h5>
                <h3>UGX {{ number_format($totalExpenses) }}</h3>
                <small>This Month</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-warning text-dark text-center p-3">
                <h5>Categories</h5>
                <h3>{{ $expensesByCategory->count() }}</h3>
                <small>Active Categories</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-info text-white text-center p-3">
                <h5>Largest Category</h5>
                <h3>
                    @if($expensesByCategory->count() > 0)
                        {{ $expensesByCategory->first()->category_name }}
                    @else
                        N/A
                    @endif
                </h3>
                <small>By Amount</small>
            </div>
        </div>
    </div>

    <!-- Expenses by Category -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📈 Expenses by Category</h5>
                </div>
                <div class="card-body">
                    @if($expensesByCategory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>% of Total</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expensesByCategory as $expense)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $expense->category_name }}</span>
                                    </td>
                                    <td class="fw-bold text-danger">UGX {{ number_format($expense->total_amount) }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ number_format(($expense->total_amount / max($totalExpenses, 1)) * 100, 1) }}%
                                        </span>
                                    </td>
                                    <td>{{ $expense->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">No expense data for this period.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📅 Monthly Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    @if($monthlyTrend->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Total Expenses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyTrend as $trend)
                                <tr>
                                    <td>{{ \Carbon\Carbon::create($trend->year, $trend->month)->format('M Y') }}</td>
                                    <td class="fw-bold text-danger">UGX {{ number_format($trend->total) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">No historical data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Management Tips -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">💡 Expense Management Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Track all business expenses regularly to understand your cash flow</li>
                        <li>Review your largest expense categories monthly for cost-saving opportunities</li>
                        <li>Compare expenses against your revenue to maintain profitability</li>
                        <li>Set monthly expense budgets for different categories</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('shop.layouts.app')

@section('title', 'Profit & Loss Statement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>💰 Profit & Loss Statement</h1>
        <a href="{{ route('shop.reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Reports
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
                        Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                    </small>
                </div>
            </form>
        </div>
    </div>

    <!-- P&L Statement -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">PROFIT & LOSS STATEMENT</h4>
                    <small>{{ $startDate->format('F Y') }}</small>
                </div>
                <div class="card-body">
                    <!-- Revenue -->
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-8"><strong>REVENUE</strong></div>
                        <div class="col-4 text-end"><strong>UGX {{ number_format($revenue) }}</strong></div>
                        
                        <div class="col-8">Sales Revenue</div>
                        <div class="col-4 text-end">UGX {{ number_format($revenue) }}</div>
                    </div>

                    <!-- Cost of Goods Sold -->
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-8"><strong>COST OF GOODS SOLD</strong></div>
                        <div class="col-4 text-end"><strong>UGX {{ number_format($cogs) }}</strong></div>
                        
                        <div class="col-8">Product Costs</div>
                        <div class="col-4 text-end">UGX {{ number_format($cogs) }}</div>
                    </div>

                    <!-- Gross Profit -->
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-8"><strong>GROSS PROFIT</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-success">UGX {{ number_format($grossProfit) }}</strong>
                        </div>
                        
                        <div class="col-8">Gross Profit Margin</div>
                        <div class="col-4 text-end">
                            <span class="badge bg-{{ $grossMargin > 20 ? 'success' : 'warning' }}">
                                {{ number_format($grossMargin, 1) }}%
                            </span>
                        </div>
                    </div>

                    <!-- ADD: Operating Expenses Section -->
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-8"><strong>OPERATING EXPENSES</strong></div>
                        <div class="col-4 text-end"><strong class="text-danger">UGX {{ number_format($totalExpenses) }}</strong></div>
                        
                        <div class="col-8">Total Operating Expenses</div>
                        <div class="col-4 text-end">UGX {{ number_format($totalExpenses) }}</div>
                    </div>

                    <!-- ADD: Net Profit -->
                    <div class="row mb-4 border-bottom pb-2">
                        <div class="col-8"><strong>NET PROFIT</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-{{ $netProfit > 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($netProfit) }}
                            </strong>
                        </div>
                        
                        <div class="col-8">Net Profit Margin</div>
                        <div class="col-4 text-end">
                            <span class="badge bg-{{ $netMargin > 10 ? 'success' : ($netMargin > 0 ? 'warning' : 'danger') }}">
                                {{ number_format($netMargin, 1) }}%
                            </span>
                        </div>
                    </div>

                    <!-- Performance Indicators -->
                    <div class="row">
                        <div class="col-12">
                            <h6>Performance Indicators:</h6>
                            <ul class="list-unstyled">
                                <li>📈 <strong>Revenue Growth vs Previous Month:</strong> 
                                    <span class="badge bg-{{ $revenueGrowth > 0 ? 'success' : 'danger' }}">
                                        {{ number_format($revenueGrowth, 1) }}%
                                    </span>
                                </li>
                                <li>🎯 <strong>Gross Margin:</strong> 
                                    <span class="badge bg-{{ $grossMargin > 20 ? 'success' : 'warning' }}">
                                        {{ number_format($grossMargin, 1) }}%
                                    </span>
                                </li>
                                <li>💰 <strong>Net Profit Margin:</strong> 
                                    <span class="badge bg-{{ $netMargin > 10 ? 'success' : ($netMargin > 0 ? 'warning' : 'danger') }}">
                                        {{ number_format($netMargin, 1) }}%
                                    </span>
                                </li>
                                <li>💸 <strong>Expense Ratio:</strong> 
                                    <span class="badge bg-{{ ($totalExpenses / max($revenue, 1)) * 100 < 30 ? 'success' : 'warning' }}">
                                        {{ number_format(($totalExpenses / max($revenue, 1)) * 100, 1) }}%
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Financial Health Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-{{ $netProfit > 0 ? 'success' : 'warning' }}">
                                <h6>💡 Financial Summary for {{ $startDate->format('F Y') }}:</h6>
                                <ul class="mb-0">
                                    <li>Your business generated <strong>UGX {{ number_format($revenue) }}</strong> in revenue</li>
                                    <li>After product costs (<strong>UGX {{ number_format($cogs) }}</strong>), you had <strong>UGX {{ number_format($grossProfit) }}</strong> gross profit</li>
                                    <li>Operating expenses totaled <strong>UGX {{ number_format($totalExpenses) }}</strong></li>
                                    <li>You made a <strong>{{ $netProfit > 0 ? 'net profit' : 'net loss' }} of UGX {{ number_format(abs($netProfit)) }}</strong></li>
                                    <li>Your net profit margin is <strong>{{ number_format($netMargin, 1) }}%</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Efficiency -->
                    @if($netProfit > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6>📊 Expense Efficiency:</h6>
                                <ul class="mb-0">
                                    <li>For every UGX 100 in revenue, you spent:
                                        <strong>UGX {{ number_format($cogs > 0 ? ($cogs / $revenue * 100) : 0, 1) }}</strong> on products and 
                                        <strong>UGX {{ number_format($totalExpenses > 0 ? ($totalExpenses / $revenue * 100) : 0, 1) }}</strong> on expenses
                                    </li>
                                    <li>Your expense-to-revenue ratio is <strong>{{ number_format(($totalExpenses / max($revenue, 1)) * 100, 1) }}%</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
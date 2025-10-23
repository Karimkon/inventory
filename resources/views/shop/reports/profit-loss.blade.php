@extends('shop.layouts.app')

@section('title', 'Professional Income Statement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📊 Professional Income Statement</h1>
        <a href="{{ route('shop.reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Reports
        </a>
    </div>

    <!-- Period Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-center" method="GET">
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

    <!-- Income Statement -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">PROFESSIONAL INCOME STATEMENT</h4>
                    <small>{{ $startDate->format('F Y') }}</small>
                </div>
                <div class="card-body">
                    <!-- Revenue Section -->
                    <div class="row mb-2 border-bottom pb-2">
                        <div class="col-8"><strong>REVENUE</strong></div>
                        <div class="col-4 text-end"><strong>UGX {{ number_format($revenue) }}</strong></div>
                    </div>

                    <!-- Cost of Goods Sold -->
                    <div class="row mb-2">
                        <div class="col-8 ps-3">Cost of Goods Sold</div>
                        <div class="col-4 text-end">(UGX {{ number_format($cogs) }})</div>
                    </div>

                    <!-- Gross Profit -->
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-8"><strong>GROSS PROFIT</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-success">UGX {{ number_format($grossProfit) }}</strong>
                        </div>
                        <div class="col-8 ps-3"><small>Gross Margin</small></div>
                        <div class="col-4 text-end">
                            <small class="badge bg-{{ $grossMargin > 20 ? 'success' : 'warning' }}">
                                {{ number_format($grossMargin, 1) }}%
                            </small>
                        </div>
                    </div>

                    <!-- Operating Expenses -->
                    <div class="row mb-2">
                        <div class="col-8"><strong>OPERATING EXPENSES</strong></div>
                        <div class="col-4 text-end"><strong>(UGX {{ number_format($operatingExpenses) }})</strong></div>
                        
                        <div class="col-8 ps-3">General & Administrative</div>
                        <div class="col-4 text-end">(UGX {{ number_format($operatingExpenses) }})</div>
                        
                        <div class="col-8 ps-3">Subscription Fees</div>
                        <div class="col-4 text-end">(UGX {{ number_format($subscriptionFees) }})</div>
                    </div>

                    <!-- EBITDA -->
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-8"><strong>EBITDA</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-{{ $ebitda > 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($ebitda) }}
                            </strong>
                        </div>
                        <div class="col-8 ps-3"><small>EBITDA Margin</small></div>
                        <div class="col-4 text-end">
                            <small class="badge bg-{{ $ebitdaMargin > 15 ? 'success' : 'warning' }}">
                                {{ number_format($ebitdaMargin, 1) }}%
                            </small>
                        </div>
                    </div>

                    <!-- Depreciation -->
                    <div class="row mb-2">
                        <div class="col-8">Depreciation & Amortization</div>
                        <div class="col-4 text-end">(UGX {{ number_format($depreciation) }})</div>
                    </div>

                    <!-- EBIT -->
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-8"><strong>EBIT (Operating Income)</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-{{ $ebit > 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($ebit) }}
                            </strong>
                        </div>
                        <div class="col-8 ps-3"><small>Operating Margin</small></div>
                        <div class="col-4 text-end">
                            <small class="badge bg-{{ $operatingMargin > 10 ? 'success' : 'warning' }}">
                                {{ number_format($operatingMargin, 1) }}%
                            </small>
                        </div>
                    </div>

                    <!-- Interest Expense -->
                    <div class="row mb-2">
                        <div class="col-8">Interest Expense</div>
                        <div class="col-4 text-end">(UGX {{ number_format($interestExpense) }})</div>
                    </div>

                    <!-- EBT -->
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-8"><strong>EBT (Earnings Before Tax)</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-{{ $ebt > 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($ebt) }}
                            </strong>
                        </div>
                    </div>

                    <!-- Taxes -->
                    <div class="row mb-2">
                        <div class="col-8">Income Tax ({{ number_format($taxRate * 100) }}%)</div>
                        <div class="col-4 text-end">(UGX {{ number_format($taxes) }})</div>
                    </div>

                    <!-- NET INCOME -->
                    <div class="row mb-4 border-top pt-3">
                        <div class="col-8"><strong>NET INCOME</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-{{ $netIncome > 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($netIncome) }}
                            </strong>
                        </div>
                        <div class="col-8 ps-3"><small>Net Profit Margin</small></div>
                        <div class="col-4 text-end">
                            <small class="badge bg-{{ $netMargin > 5 ? 'success' : 'danger' }}">
                                {{ number_format($netMargin, 1) }}%
                            </small>
                        </div>
                    </div>

                    <!-- Performance Summary -->
                    <div class="row">
                        <div class="col-12">
                            <h6>📈 Performance Summary:</h6>
                            <ul class="list-unstyled">
                                <li>📊 <strong>Revenue Growth:</strong> 
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
                            </ul>
                        </div>
                    </div>

                    <!-- Financial Health -->
                    <div class="alert alert-{{ $netIncome > 0 ? 'success' : 'warning' }} mt-3">
                        <h6>💡 Financial Health Analysis</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Gross Margin:</strong> {{ number_format($grossMargin, 1) }}% 
                                    <span class="badge bg-{{ $grossMargin > 20 ? 'success' : 'warning' }}">Target: >20%</span>
                                </small><br>
                                <small><strong>EBITDA Margin:</strong> {{ number_format($ebitdaMargin, 1) }}%
                                    <span class="badge bg-{{ $ebitdaMargin > 15 ? 'success' : 'warning' }}">Target: >15%</span>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Net Margin:</strong> {{ number_format($netMargin, 1) }}%
                                    <span class="badge bg-{{ $netMargin > 5 ? 'success' : 'danger' }}">Target: >5%</span>
                                </small><br>
                                <small><strong>Profitability:</strong> 
                                    <span class="badge bg-{{ $netIncome > 0 ? 'success' : 'danger' }}">
                                        {{ $netIncome > 0 ? 'Profitable' : 'Loss Making' }}
                                    </span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
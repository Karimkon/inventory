@extends('shop.layouts.app')

@section('title', 'Financial Analysis - EBITDA & Net Income')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📊 Financial Analysis - {{ $currentMonth }}</h1>
        <a href="{{ route('shop.depreciation.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Assets
        </a>
    </div>

    <!-- Income Statement -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">INCOME STATEMENT</h4>
                    <small>{{ $currentMonth }}</small>
                </div>
                <div class="card-body">
                    <!-- Revenue Section -->
                    <div class="row mb-3 border-bottom pb-3">
                        <div class="col-8"><strong>GROSS PROFIT</strong></div>
                        <div class="col-4 text-end"><strong class="text-success">UGX {{ number_format($grossProfit) }}</strong></div>
                        
                        <div class="col-8 mt-2">Revenue - Cost of Goods Sold</div>
                        <div class="col-4 text-end mt-2">UGX {{ number_format($grossProfit) }}</div>
                    </div>

                    <!-- Operating Expenses -->
                    <div class="row mb-3 border-bottom pb-3">
                        <div class="col-8"><strong>OPERATING EXPENSES</strong></div>
                        <div class="col-4 text-end"><strong class="text-danger">- UGX {{ number_format($operatingExpenses) }}</strong></div>
                        
                        <div class="col-8 mt-2">Rent, Salaries, Utilities, etc.</div>
                        <div class="col-4 text-end mt-2">- UGX {{ number_format($operatingExpenses) }}</div>
                    </div>

                    <!-- EBITDA -->
                    <div class="row mb-3 border-bottom pb-3">
                        <div class="col-8"><strong>EBITDA</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-{{ $ebitda >= 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($ebitda) }}
                            </strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Earnings Before Interest, Taxes, Depreciation & Amortization</small>
                        </div>
                    </div>

                    <!-- Depreciation -->
                    <div class="row mb-3 border-bottom pb-3">
                        <div class="col-8"><strong>DEPRECIATION EXPENSE</strong></div>
                        <div class="col-4 text-end"><strong class="text-warning">- UGX {{ number_format($depreciationExpense) }}</strong></div>
                        
                        <div class="col-8 mt-2">Asset Depreciation</div>
                        <div class="col-4 text-end mt-2">- UGX {{ number_format($depreciationExpense) }}</div>
                    </div>

                    <!-- EBIT -->
                    <div class="row mb-3 border-bottom pb-3">
                        <div class="col-8"><strong>EBIT</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-{{ $ebit >= 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($ebit) }}
                            </strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Earnings Before Interest & Taxes</small>
                        </div>
                    </div>

                    <!-- Loan Interest -->
                    <div class="row mb-3 border-bottom pb-3">
                        <div class="col-8"><strong>LOAN INTEREST EXPENSE</strong></div>
                        <div class="col-4 text-end"><strong class="text-danger">- UGX {{ number_format($loanInterest) }}</strong></div>
                        
                        <div class="col-8 mt-2">Interest on Business Loans</div>
                        <div class="col-4 text-end mt-2">- UGX {{ number_format($loanInterest) }}</div>
                    </div>

                    <!-- EBT -->
                    <div class="row mb-3 border-bottom pb-3">
                        <div class="col-8"><strong>EBT</strong></div>
                        <div class="col-4 text-end">
                            <strong class="text-{{ $ebt >= 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($ebt) }}
                            </strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Earnings Before Taxes</small>
                        </div>
                    </div>

                    <!-- Taxes -->
                    <div class="row mb-4 border-bottom pb-3">
                        <div class="col-8"><strong>TAXES (30%)</strong></div>
                        <div class="col-4 text-end"><strong class="text-danger">- UGX {{ number_format($taxes) }}</strong></div>
                        
                        <div class="col-8 mt-2">Corporate Income Tax</div>
                        <div class="col-4 text-end mt-2">- UGX {{ number_format($taxes) }}</div>
                    </div>

                    <!-- Net Income -->
                    <div class="row mb-3">
                        <div class="col-8"><h5>NET INCOME</h5></div>
                        <div class="col-4 text-end">
                            <h5 class="text-{{ $netIncome >= 0 ? 'success' : 'danger' }}">
                                UGX {{ number_format($netIncome) }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets and Loans Summary -->
    <div class="row mt-4">
        <!-- Depreciable Assets -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">🏢 Depreciable Assets</h5>
                </div>
                <div class="card-body">
                    @if($assets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Asset</th>
                                    <th>Cost</th>
                                    <th>Current Value</th>
                                    <th>Monthly Dep.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $asset)
                                <tr>
                                    <td>{{ $asset->asset_name }}</td>
                                    <td>UGX {{ number_format($asset->purchase_cost) }}</td>
                                    <td class="text-success">UGX {{ number_format($asset->current_value) }}</td>
                                    <td class="text-warning">UGX {{ number_format($asset->monthly_depreciation) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">No depreciable assets recorded.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">🏦 Active Loans</h5>
                </div>
                <div class="card-body">
                    @if($loans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Loan</th>
                                    <th>Balance</th>
                                    <th>Rate</th>
                                    <th>Monthly Int.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $loan)
                                <tr>
                                    <td>{{ $loan->loan_name }}</td>
                                    <td class="text-warning">UGX {{ number_format($loan->remaining_balance) }}</td>
                                    <td>{{ $loan->interest_rate }}%</td>
                                    <td class="text-danger">
                                        UGX {{ number_format($loan->remaining_balance * ($loan->interest_rate / 100) / 12) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">No active loans.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Health Indicator -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Financial Health: 
                        <span class="badge bg-{{ $netIncome >= 0 ? 'success' : 'danger' }}">
                            {{ $netIncome >= 0 ? '✅ PROFITABLE' : '⚠️ LOSS MAKING' }}
                        </span>
                    </h5>
                    <p class="mb-0">
                        @if($netIncome >= 0)
                        Your business is generating profit after accounting for all expenses, depreciation, loan interest, and taxes.
                        @else
                        Your business is currently operating at a loss. Consider reviewing expenses and revenue streams.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
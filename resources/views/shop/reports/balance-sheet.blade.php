@extends('shop.layouts.app')

@section('title', 'Balance Sheet')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>⚖️ Balance Sheet</h1>
        <a href="{{ route('shop.reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Reports
        </a>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white text-center">
                    <h4 class="mb-0">BALANCE SHEET</h4>
                    <small>As of {{ now()->format('M d, Y') }}</small>
                </div>
                <div class="card-body">
                    <!-- Assets Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-success mb-3">ASSETS</h5>
                            
                            <div class="row">
                                <div class="col-8"><strong>Current Assets:</strong></div>
                                <div class="col-4 text-end"></div>
                                
                                <div class="col-8 ps-4">Cash & Bank Balance</div>
                                <div class="col-4 text-end">UGX {{ number_format($cashBalance) }}</div>
                                
                                <div class="col-8 ps-4">Inventory (at cost)</div>
                                <div class="col-4 text-end">UGX {{ number_format($inventoryValue) }}</div>
                                
                                <div class="col-8 border-top pt-2"><strong>Total Assets</strong></div>
                                <div class="col-4 border-top pt-2 text-end">
                                    <strong class="text-success">UGX {{ number_format($totalAssets) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liabilities & Equity Section -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">LIABILITIES & EQUITY</h5>
                            
                            <!-- Liabilities -->
                            <div class="row mb-3">
                                <div class="col-8"><strong>Liabilities:</strong></div>
                                <div class="col-4 text-end"></div>
                                
                                <div class="col-8 ps-4">Total Liabilities</div>
                                <div class="col-4 text-end">UGX {{ number_format($totalLiabilities) }}</div>
                            </div>
                            
                            <!-- Equity -->
                            <div class="row">
                                <div class="col-8"><strong>Owner's Equity:</strong></div>
                                <div class="col-4 text-end"></div>
                                
                                <div class="col-8 ps-4">Retained Earnings</div>
                                <div class="col-4 text-end">UGX {{ number_format($ownersEquity) }}</div>
                                
                                <div class="col-8 border-top pt-2"><strong>Total Liabilities & Equity</strong></div>
                                <div class="col-4 border-top pt-2 text-end">
                                    <strong class="text-primary">UGX {{ number_format($totalAssets) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>📈 Financial Summary</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small><strong>Total Revenue:</strong> UGX {{ number_format($totalRevenue) }}</small><br>
                                            <small><strong>Cost of Goods:</strong> UGX {{ number_format($totalCostOfGoods) }}</small><br>
                                            <small><strong>Total Expenses:</strong> UGX {{ number_format($totalExpenses) }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Gross Profit:</strong> UGX {{ number_format($totalRevenue - $totalCostOfGoods) }}</small><br>
                                            <small><strong>Net Profit:</strong> UGX {{ number_format($cashBalance) }}</small><br>
                                            <small><strong>Business Worth:</strong> UGX {{ number_format($totalAssets) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Health Analysis -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-{{ $cashBalance > 0 && $totalAssets > 0 ? 'success' : 'warning' }}">
                                <h6>💡 Financial Health Analysis</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="mb-2">
                                            <li><strong>Cash Position:</strong> 
                                                <span class="badge bg-{{ $cashBalance > 0 ? 'success' : 'danger' }}">
                                                    UGX {{ number_format($cashBalance) }}
                                                </span>
                                            </li>
                                            <li><strong>Inventory Investment:</strong> 
                                                UGX {{ number_format($inventoryValue) }}
                                            </li>
                                            <li><strong>Debt Ratio:</strong> 
                                                <span class="badge bg-{{ $debtToEquity == 0 ? 'success' : 'warning' }}">
                                                    {{ number_format($debtToEquity, 1) }}%
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="mb-0">
                                            <li><strong>Cash to Assets:</strong> 
                                                <span class="badge bg-{{ $cashRatio > 20 ? 'success' : 'warning' }}">
                                                    {{ number_format($cashRatio, 1) }}%
                                                </span>
                                            </li>
                                            <li><strong>Inventory to Assets:</strong> 
                                                <span class="badge bg-info">
                                                    {{ number_format($inventoryRatio, 1) }}%
                                                </span>
                                            </li>
                                            <li><strong>Financial Position:</strong> 
                                                <span class="badge bg-{{ $totalAssets > 0 && $cashBalance >= 0 ? 'success' : 'danger' }}">
                                                    {{ $totalAssets > 0 && $cashBalance >= 0 ? 'Healthy' : 'Needs Attention' }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Ratios -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>📊 Key Financial Ratios</h6>
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="border rounded p-2">
                                        <small>Cash Ratio</small>
                                        <div class="fw-bold text-{{ $cashRatio > 20 ? 'success' : 'warning' }}">
                                            {{ number_format($cashRatio, 1) }}%
                                        </div>
                                        <small class="text-muted">>20% is healthy</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2">
                                        <small>Inventory %</small>
                                        <div class="fw-bold text-info">
                                            {{ number_format($inventoryRatio, 1) }}%
                                        </div>
                                        <small class="text-muted">Of total assets</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2">
                                        <small>Debt Ratio</small>
                                        <div class="fw-bold text-{{ $debtToEquity == 0 ? 'success' : 'warning' }}">
                                            {{ number_format($debtToEquity, 1) }}%
                                        </div>
                                        <small class="text-muted">Lower is better</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2">
                                        <small>Business Health</small>
                                        <div class="fw-bold text-{{ $totalAssets > 100000 && $cashBalance > 0 ? 'success' : 'info' }}">
                                            @if($totalAssets > 100000 && $cashBalance > 0)
                                                Strong
                                            @elseif($totalAssets > 0)
                                                Growing
                                            @else
                                                Starting
                                            @endif
                                        </div>
                                        <small class="text-muted">Overall status</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
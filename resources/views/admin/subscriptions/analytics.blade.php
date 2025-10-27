@extends('admin.layouts.app')

@section('title', 'Subscription Analytics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Subscription Analytics</h1>
        <div class="btn-group">
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Subscriptions
            </a>
            <a href="{{ route('admin.subscriptions.revenue') }}" class="btn btn-primary">
                <i class="bi bi-currency-dollar"></i> Revenue Report
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                UGX {{ number_format($totalStats['total_revenue']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStats['active_subscriptions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approval</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStats['pending_approval'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStats['total_subscriptions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Revenue by Month -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue by Month (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Revenue</th>
                                    <th>Subscriptions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByMonth as $revenue)
                                <tr>
                                    <td>
                                        {{ Carbon\Carbon::create($revenue->year, $revenue->month)->format('F Y') }}
                                    </td>
                                    <td class="text-success">
                                        <strong>UGX {{ number_format($revenue->revenue) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $revenue->count ?? 1 }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No revenue data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriptions by Plan -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Subscriptions by Plan Type</h5>
                </div>
                <div class="card-body">
                    @foreach($subscriptionsByPlan as $plan)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-primary">{{ ucfirst($plan->plan_type) }}</span>
                        </div>
                        <div class="fw-bold">{{ $plan->count }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Recent Payments</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Shop</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->shop->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $payment->plan_type }}</span>
                            </td>
                            <td class="text-success">
                                <strong>UGX {{ number_format($payment->total_amount) }}</strong>
                            </td>
                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($payment->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No recent payments</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
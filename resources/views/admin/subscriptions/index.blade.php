@extends('admin.layouts.app')

@section('title', 'Subscription Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Subscription Management</h1>
        <div class="btn-group">
            <a href="{{ route('admin.subscriptions.analytics') }}" class="btn btn-success">
                <i class="bi bi-graph-up"></i> View Analytics
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
                                Total Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-credit-card-2-front fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_approval'] }}</div>
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
                                Revenue Generated</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                UGX {{ number_format($totalRevenue) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Filter Subscriptions</h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'all']) }}" 
                           class="btn btn-outline-primary {{ $status == 'all' ? 'active' : '' }}">All</a>
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'active']) }}" 
                           class="btn btn-outline-success {{ $status == 'active' ? 'active' : '' }}">Active</a>
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'pending_approval']) }}" 
                           class="btn btn-outline-warning {{ $status == 'pending_approval' ? 'active' : '' }}">Pending</a>
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'expired']) }}" 
                           class="btn btn-outline-danger {{ $status == 'expired' ? 'active' : '' }}">Expired</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Shop</th>
                            <th>Plan</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                        <tr>
                            <td>
                                <strong>{{ $subscription->shop->name ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $subscription->shop->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $subscription->plan_type }}</span>
                            </td>
                            <td>{{ $subscription->months }} months</td>
                            <td>
                                <strong>UGX {{ number_format($subscription->total_amount) }}</strong>
                            </td>
                            <td>
                                @if($subscription->is_active && !$subscription->is_expired)
                                    <span class="badge bg-success">Active</span>
                                @elseif($subscription->payment_status === 'paid' && !$subscription->is_active)
                                    <span class="badge bg-warning">Pending Approval</span>
                                @elseif($subscription->is_expired)
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-secondary">{{ $subscription->payment_status }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $subscription->activated_at ? $subscription->activated_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                @if($subscription->expires_at)
                                    {{ $subscription->expires_at->format('M d, Y') }}
                                    @if($subscription->is_expired)
                                        <br><small class="text-danger">Expired</small>
                                    @else
                                        <br><small class="text-success">{{ $subscription->remaining_days }} days left</small>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($subscription->payment_status === 'paid' && !$subscription->is_active)
                                    <form action="{{ route('admin.subscriptions.approve', $subscription) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" 
                                                onclick="return confirm('Approve this subscription?')">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($subscription->is_active)
                                    <form action="{{ route('admin.subscriptions.deactivate', $subscription) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning" 
                                                onclick="return confirm('Deactivate this subscription?')">
                                            <i class="bi bi-pause"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fa-2x mb-2"></i>
                                <br>No subscriptions found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} of {{ $subscriptions->total() }} entries
                </div>
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
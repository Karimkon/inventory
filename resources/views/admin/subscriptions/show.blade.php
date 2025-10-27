@extends('admin.layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Subscription Details</h1>
        <div class="btn-group">
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Subscription Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Subscription Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Shop Name:</th>
                                    <td>{{ $subscription->shop->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Plan Type:</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $subscription->plan_type }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td>{{ $subscription->months }} months</td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td class="h5 text-success">UGX {{ number_format($subscription->total_amount) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Activation Fee:</th>
                                    <td>UGX {{ number_format($subscription->activation_fee) }}</td>
                                </tr>
                                <tr>
                                    <th>Monthly Fee:</th>
                                    <td>UGX {{ number_format($subscription->monthly_fee) }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $subscription->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($subscription->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Active Status:</th>
                                    <td>
                                        @if($subscription->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dates Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Activated At:</strong><br>
                            {{ $subscription->activated_at ? $subscription->activated_at->format('M d, Y H:i') : 'Not activated' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Expires At:</strong><br>
                            @if($subscription->expires_at)
                                {{ $subscription->expires_at->format('M d, Y H:i') }}
                                @if($subscription->is_expired)
                                    <br><span class="text-danger">Expired</span>
                                @else
                                    <br><span class="text-success">{{ $subscription->remaining_days }} days remaining</span>
                                @endif
                            @else
                                Not set
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Subscription Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($subscription->payment_status === 'paid' && !$subscription->is_active)
                        <div class="col-md-4">
                            <form action="{{ route('admin.subscriptions.approve', $subscription) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle"></i> Approve & Activate
                                </button>
                            </form>
                        </div>
                        @endif

                        @if($subscription->is_active)
                        <div class="col-md-4">
                            <form action="{{ route('admin.subscriptions.deactivate', $subscription) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" 
                                        onclick="return confirm('Deactivate this subscription?')">
                                    <i class="bi bi-pause-circle"></i> Deactivate
                                </button>
                            </form>
                        </div>
                        @endif

                        <div class="col-md-4">
                            <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#extendModal">
                                <i class="bi bi-calendar-plus"></i> Extend
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Shop Info & Quick Stats -->
        <div class="col-lg-4">
            <!-- Shop Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shop Information</h5>
                </div>
                <div class="card-body">
                    @if($subscription->shop)
                    <div class="text-center mb-3">
                        <div class="fw-bold">{{ $subscription->shop->name }}</div>
                        <small class="text-muted">{{ $subscription->shop->email }}</small>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $subscription->shop->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Location:</strong></td>
                            <td>{{ $subscription->shop->location ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $subscription->shop->is_active ? 'success' : 'secondary' }}">
                                    {{ $subscription->shop->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                    <a href="{{ route('admin.shops.show', $subscription->shop) }}" class="btn btn-outline-primary btn-sm w-100">
                        View Shop Details
                    </a>
                    @else
                    <div class="text-center text-muted">
                        <i class="bi bi-shop fa-2x mb-2"></i>
                        <br>No shop information available
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Payment Reference:</strong></td>
                            <td class="text-muted">{{ $subscription->payment_reference ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tracking ID:</strong></td>
                            <td class="text-muted">{{ $subscription->pesapal_tracking_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Paid At:</strong></td>
                            <td class="text-muted">
                                {{ $subscription->paid_at ? $subscription->paid_at->format('M d, Y H:i') : 'N/A' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Subscription Modal -->
<div class="modal fade" id="extendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extend Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.subscriptions.extend', $subscription) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Extension Days</label>
                        <input type="number" name="extension_days" class="form-control" 
                               min="1" max="365" value="30" required>
                        <div class="form-text">Number of days to extend the subscription</div>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            Current expiry: {{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'Not set' }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Extend Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
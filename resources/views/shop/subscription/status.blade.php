@extends('shop.layouts.app')

@section('title', 'Subscription Status')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Subscription Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card"></i> Subscription Status
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Shop Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Shop Information</h6>
                            <p><strong>Shop Name:</strong> {{ $shop->name }}</p>
                            <p><strong>Business Type:</strong> 
                                <span class="text-capitalize">{{ $shop->business_type }}</span>
                            </p>
                            <p><strong>Subscription Status:</strong> 
                                <span class="badge bg-{{ $shop->subscription_status === 'active' ? 'success' : ($shop->subscription_status === 'pending_approval' ? 'warning' : 'danger') }}">
                                    {{ ucfirst(str_replace('_', ' ', $shop->subscription_status)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Account Status</h6>
                            @if($shop->has_active_subscription)
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i> 
                                    <strong>Active Subscription</strong>
                                    <p class="mb-0 mt-1">Your shop is fully active with all features available.</p>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <strong>Subscription Required</strong>
                                    <p class="mb-0 mt-1">Your subscription has expired or is inactive. Please renew to access all features.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Current Subscription Details -->
                    @if($activeSubscription)
                    <div class="card bg-light mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Current Subscription</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Plan Type:</strong><br>
                                    {{ $activeSubscription->plan_details['name'] ?? 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Monthly Fee:</strong><br>
                                    UGX {{ number_format($activeSubscription->monthly_fee) }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Activation Fee:</strong><br>
                                    UGX {{ number_format($activeSubscription->activation_fee) }}
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <strong>Payment Status:</strong><br>
                                    <span class="badge bg-{{ $activeSubscription->payment_status === 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($activeSubscription->payment_status) }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Expiry Date:</strong><br>
                                    {{ $activeSubscription->expires_at ? $activeSubscription->expires_at->format('M d, Y') : 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-{{ $activeSubscription->is_active ? 'success' : 'danger' }}">
                                        {{ $activeSubscription->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($activeSubscription->expires_at)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Remaining Time:</strong><br>
                                    @php
                                        $remainingDays = \Carbon\Carbon::now()->diffInDays($activeSubscription->expires_at, false);
                                    @endphp
                                    @if($remainingDays > 0)
                                        <span class="text-success">
                                            {{ $remainingDays }} day(s) remaining
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            Expired {{ abs($remainingDays) }} day(s) ago
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        @if($shop->has_active_subscription)
                            <a href="{{ route('shop.subscription.renew') }}" class="btn btn-warning">
                                <i class="bi bi-arrow-clockwise"></i> Renew Subscription
                            </a>
                        @else
                            @if($activeSubscription)
                                <a href="{{ route('shop.subscription.renew') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-clockwise"></i> Renew Subscription
                                </a>
                            @else
                                <a href="{{ route('shop.subscription.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Activate Subscription
                                </a>
                            @endif
                        @endif
                        
                        <a href="{{ route('shop.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>

                    <!-- Subscription History -->
                    @if($subscription && $shop->subscriptions->count() > 1)
                    <div class="mt-4">
                        <h6>Subscription History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shop->subscriptions->take(5) as $sub)
                                    <tr>
                                        <td>{{ $sub->plan_details['name'] ?? 'N/A' }}</td>
                                        <td>UGX {{ number_format($sub->activation_fee) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $sub->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($sub->payment_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $sub->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Help Section -->
            <div class="card bg-light">
                <div class="card-header">
                    <h6 class="mb-0">Need Help?</h6>
                </div>
                <div class="card-body">
                    <p>If you're experiencing issues with your subscription or payment, please contact our support team:</p>
                    <ul>
                        <li><strong>Email:</strong> redversemobility@gmail.com</li>
                        <li><strong>Phone:</strong> +256 788135150 / +256 741613506</li>
                        <li><strong>Hours:</strong> Mon-Sat, 8:00 AM - 5:00 PM</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
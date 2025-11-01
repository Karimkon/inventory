@extends('shop.layouts.app')

@section('title', 'Renew Subscription')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-clockwise"></i> Renew Subscription
                    </h5>
                </div>
                <div class="card-body">
                    @if($activeSubscription)
                    <!-- Current Subscription Info -->
                    <div class="alert alert-info">
                        <h6>Current Subscription Details:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Plan:</strong> {{ $activeSubscription->plan_details['name'] ?? 'N/A' }}</p>
                                <p><strong>Monthly Fee:</strong> UGX {{ number_format($activeSubscription->monthly_fee) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Expires:</strong> {{ $activeSubscription->expires_at->format('M d, Y') }}</p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $activeSubscription->is_active ? 'success' : 'danger' }}">
                                        {{ $activeSubscription->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Remaining Days -->
                        @php
                            $remainingDays = \Carbon\Carbon::now()->diffInDays($activeSubscription->expires_at, false);
                        @endphp
                        @if($remainingDays > 0)
                            <p class="mb-0 mt-2 text-success">
                                <i class="bi bi-clock"></i> 
                                {{ $remainingDays }} day(s) remaining
                            </p>
                        @else
                            <p class="mb-0 mt-2 text-danger">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Expired {{ abs($remainingDays) }} day(s) ago
                            </p>
                        @endif
                    </div>

                    <!-- Renewal Form -->
                    <form action="{{ route('shop.subscription.process-renewal') }}" method="POST" id="renewalForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="months" class="form-label fw-bold">Select Renewal Period</label>
                            <select name="months" id="months" class="form-select form-select-lg" required>
                                <option value="">Choose renewal period...</option>
                                <option value="1">1 Month - UGX {{ number_format($activeSubscription->monthly_fee * 1) }}</option>
                                <option value="3">3 Months - UGX {{ number_format($activeSubscription->monthly_fee * 3) }}</option>
                                <option value="6">6 Months - UGX {{ number_format($activeSubscription->monthly_fee * 6) }}</option>
                                <option value="12">12 Months - UGX {{ number_format($activeSubscription->monthly_fee * 12) }}</option>
                            </select>
                            <div class="form-text">Select how many months you want to renew for</div>
                        </div>

                        <!-- Amount Display -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Payment Summary</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1">Monthly Fee:</p>
                                        <p class="mb-1">Selected Period:</p>
                                        <p class="mb-0 fw-bold">Total Amount:</p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-1">UGX <span id="monthlyFee">{{ number_format($activeSubscription->monthly_fee) }}</span></p>
                                        <p class="mb-1"><span id="selectedMonths">0</span> month(s)</p>
                                        <p class="mb-0 fw-bold fs-5 text-primary">UGX <span id="totalAmount">0</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-credit-card"></i> Proceed to Payment
                            </button>
                            <a href="{{ route('shop.subscription.status') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Status
                            </a>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-triangle"></i> No Active Subscription Found</h6>
                        <p class="mb-3">You don't have an active subscription to renew.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('shop.subscription.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Activate New Subscription
                            </a>
                            <a href="{{ route('shop.dashboard') }}" class="btn btn-outline-secondary">
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pricing Info -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Renewal Benefits</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="text-primary">
                                <i class="bi bi-arrow-repeat fs-1"></i>
                            </div>
                            <h6>Seamless Renewal</h6>
                            <p class="small">Continue without interruption</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="text-success">
                                <i class="bi bi-shield-check fs-1"></i>
                            </div>
                            <h6>All Features</h6>
                            <p class="small">Full access to all system features</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="text-warning">
                                <i class="bi bi-headset fs-1"></i>
                            </div>
                            <h6>Priority Support</h6>
                            <p class="small">Dedicated support for active subscribers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyFee = {{ $activeSubscription->monthly_fee }};
    const monthsSelect = document.getElementById('months');
    const submitBtn = document.getElementById('submitBtn');
    const selectedMonthsSpan = document.getElementById('selectedMonths');
    const totalAmountSpan = document.getElementById('totalAmount');
    
    function updateTotalAmount(months) {
        const selectedMonths = parseInt(months);
        const totalAmount = monthlyFee * selectedMonths;
        
        selectedMonthsSpan.textContent = selectedMonths;
        totalAmountSpan.textContent = totalAmount.toLocaleString();
        
        if (selectedMonths) {
            submitBtn.innerHTML = `<i class="bi bi-credit-card"></i> Pay UGX ${totalAmount.toLocaleString()}`;
            submitBtn.disabled = false;
        } else {
            submitBtn.innerHTML = `<i class="bi bi-credit-card"></i> Proceed to Payment`;
            submitBtn.disabled = true;
        }
    }
    
    // Event listener for select change
    monthsSelect.addEventListener('change', function() {
        updateTotalAmount(this.value);
    });
    
    // Form validation
    document.getElementById('renewalForm').addEventListener('submit', function(e) {
        const months = monthsSelect.value;
        if (!months) {
            e.preventDefault();
            alert('Please select a renewal period');
            return false;
        }
    });
    
    // Initialize
    updateTotalAmount(0);
});
</script>
@endsection
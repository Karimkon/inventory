@extends('shop.layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card"></i> Complete Your Payment
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title">Subscription Summary</h6>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Plan:</td>
                                    <td><strong>{{ ucfirst($subscription->plan_type) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Activation Fee:</td>
                                    <td><strong class="text-primary">UGX {{ number_format($subscription->activation_fee) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Monthly Fee:</td>
                                    <td>UGX {{ number_format($subscription->monthly_fee) }}/month</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td>
                                        <span class="badge bg-warning">{{ ucfirst($subscription->payment_status) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        You will be redirected to Pesapal to complete your payment of <strong>UGX {{ number_format($subscription->activation_fee) }}</strong>.
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <form action="{{ route('shop.subscription.process-payment', $subscription) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-lock"></i> Pay with Pesapal
                            </button>
                        </form>

                        <a href="{{ route('shop.subscription.manual-payment', $subscription) }}" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-bank"></i> Manual / Bank Payment
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('shop.subscription.status') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

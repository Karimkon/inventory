@extends('shop.layouts.app')

@section('title', 'Manual Payment Instructions')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bank"></i> Manual Payment Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i>
                        After making payment, please contact support with your payment details for activation.
                    </div>

                    <h6>Bank Transfer Details:</h6>
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <p><strong>Bank:</strong> Example Bank Uganda</p>
                            <p><strong>Account Name:</strong> Redvers ShopFlow</p>
                            <p><strong>Account Number:</strong> 1234567890</p>
                            <p><strong>Branch:</strong> Kampala Main</p>
                            <p><strong>Amount:</strong> UGX {{ number_format($subscription->activation_fee) }}</p>
                            <p><strong>Reference:</strong> {{ $subscription->shop->name }} - Activation</p>
                        </div>
                    </div>

                    <h6>Mobile Money:</h6>
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <p><strong>Provider:</strong> MTN Mobile Money</p>
                            <p><strong>Number:</strong> 0770 000 000</p>
                            <p><strong>Amount:</strong> UGX {{ number_format($subscription->activation_fee) }}</p>
                            <p><strong>Reference:</strong> {{ $subscription->shop->name }}</p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6>Contact Support After Payment:</h6>
                        <p><strong>Phone:</strong> +256 700 000 000</p>
                        <p><strong>Email:</strong> support@redversshopflow.com</p>
                        <p>Please provide your shop name and payment reference.</p>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('shop.subscription.payment', $subscription) }}" class="btn btn-outline-primary">
                            Back to Payment Options
                        </a>
                        <a href="{{ route('shop.subscription.status') }}" class="btn btn-outline-secondary">
                            View Subscription Status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
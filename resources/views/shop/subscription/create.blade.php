@extends('shop.layouts.app')

@section('title', 'Choose Subscription Plan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card"></i> Choose Your Subscription Plan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Select a plan that matches your business needs. You'll only pay the activation fee now.
                    </div>

                    <div class="row">
                        @foreach(App\Http\Controllers\Shop\SubscriptionController::PLANS as $type => $plan)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100 shadow-sm border-{{ $type === 'retail' ? 'success' : ($type === 'wholesale' ? 'info' : ($type === 'hardware' ? 'warning' : 'primary')) }}">
                                <div class="card-header bg-{{ $type === 'retail' ? 'success' : ($type === 'wholesale' ? 'info' : ($type === 'hardware' ? 'warning' : 'primary')) }} text-white text-center">
                                    <h6 class="mb-0">{{ $plan['name'] }}</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="text-primary">UGX {{ number_format($plan['activation_fee']) }}</h3>
                                    <p class="text-muted">One-time Activation</p>
                                    <p><strong>Monthly: UGX {{ number_format($plan['monthly_fee']) }}</strong></p>
                                    
                                    <ul class="list-unstyled text-start small">
                                        @foreach($plan['features'] as $feature)
                                        <li class="mb-2"><i class="bi bi-check-circle text-success"></i> {{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-footer text-center">
                                    <form action="{{ route('shop.subscription.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan_type" value="{{ $type }}">
                                        <button type="submit" class="btn btn-{{ $type === 'retail' ? 'success' : ($type === 'wholesale' ? 'info' : ($type === 'hardware' ? 'warning' : 'primary')) }} w-100">
                                            Select Plan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('shop.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
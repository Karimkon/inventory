@extends('shop.layouts.app')

@section('title', 'Loan Details - ' . $loan->loan_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📋 Loan Details: {{ $loan->loan_name }}</h1>
        <div>
            <a href="{{ route('shop.loans.edit', $loan) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('shop.loans.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Loans
            </a>
        </div>
    </div>

    <!-- Loan Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-3">
                    <h6>Original Amount</h6>
                    <h4>UGX {{ number_format($loan->principal_amount) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center py-3">
                    <h6>Remaining Balance</h6>
                    <h4>UGX {{ number_format($loan->remaining_balance) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center py-3">
                    <h6>Monthly Payment</h6>
                    <h4>UGX {{ number_format($loan->monthly_payment) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center py-3">
                    <h6>Interest Rate</h6>
                    <h4>{{ $loan->interest_rate }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Loan Information -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📝 Loan Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Loan Name:</th>
                            <td>{{ $loan->loan_name }}</td>
                        </tr>
                        <tr>
                            <th>Lender:</th>
                            <td>{{ $loan->lender_name }}</td>
                        </tr>
                        <tr>
                            <th>Start Date:</th>
                            <td>{{ $loan->start_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Term:</th>
                            <td>{{ $loan->term_months }} months</td>
                        </tr>
                        <tr>
                            <th>Amount Paid:</th>
                            <td class="text-success">
                                UGX {{ number_format($loan->principal_amount - $loan->remaining_balance) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Progress:</th>
                            <td>
                                @php
                                    $progress = (($loan->principal_amount - $loan->remaining_balance) / $loan->principal_amount) * 100;
                                @endphp
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-{{ $progress == 100 ? 'success' : 'primary' }}" 
                                         style="width: {{ $progress }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($progress, 1) }}% paid</small>
                            </td>
                        </tr>
                        @if($loan->purpose)
                        <tr>
                            <th>Purpose:</th>
                            <td>{{ $loan->purpose }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Record Payment -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">💵 Record Payment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('shop.loans.record-payment', $loan) }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Payment Amount</label>
                                <input type="number" step="0.01" name="payment_amount" 
                                       class="form-control" min="0" max="{{ $loan->remaining_balance }}"
                                       placeholder="UGX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100 mt-2">
                                    <i class="bi bi-cash-coin"></i> Record Payment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment Schedule -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📅 Payment Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-sm">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th>Month</th>
                                    <th>Date</th>
                                    <th>Payment</th>
                                    <th>Principal</th>
                                    <th>Interest</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentSchedule as $payment)
                                <tr class="{{ $payment['balance'] == 0 ? 'table-success' : '' }}">
                                    <td>{{ $payment['month'] }}</td>
                                    <td>{{ $payment['date'] }}</td>
                                    <td>UGX {{ number_format($payment['payment']) }}</td>
                                    <td>UGX {{ number_format($payment['principal']) }}</td>
                                    <td class="text-warning">UGX {{ number_format($payment['interest']) }}</td>
                                    <td class="{{ $payment['balance'] == 0 ? 'text-success fw-bold' : '' }}">
                                        UGX {{ number_format($payment['balance']) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
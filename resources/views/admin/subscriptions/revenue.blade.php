@extends('admin.layouts.app')

@section('title', 'Revenue Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Revenue Report</h1>
        <div class="btn-group">
            <a href="{{ route('admin.subscriptions.analytics') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Analytics
            </a>
        </div>
    </div>

    <!-- Revenue by Plan Type -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Revenue by Plan Type</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Plan Type</th>
                            <th>Total Revenue</th>
                            <th>Subscriptions</th>
                            <th>Average Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAllRevenue = 0; $totalAllSubscriptions = 0; @endphp
                        @foreach($revenueData as $data)
                        @php 
                            $totalAllRevenue += $data->revenue;
                            $totalAllSubscriptions += $data->count;
                        @endphp
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ ucfirst($data->plan_type) }}</span>
                            </td>
                            <td class="text-success">
                                <strong>UGX {{ number_format($data->revenue) }}</strong>
                            </td>
                            <td>{{ $data->count }}</td>
                            <td>UGX {{ number_format($data->revenue / $data->count) }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-primary">
                            <td><strong>Total</strong></td>
                            <td class="text-success"><strong>UGX {{ number_format($totalAllRevenue) }}</strong></td>
                            <td><strong>{{ $totalAllSubscriptions }}</strong></td>
                            <td><strong>UGX {{ number_format($totalAllSubscriptions > 0 ? $totalAllRevenue / $totalAllSubscriptions : 0) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Monthly Revenue (Last 12 Months)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Revenue</th>
                            <th>Growth</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $previousRevenue = null;
                        @endphp
                        @forelse($monthlyRevenue as $revenue)
                        @php
                            $currentRevenue = $revenue->revenue;
                            $growth = $previousRevenue ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
                            $previousRevenue = $currentRevenue;
                        @endphp
                        <tr>
                            <td>
                                {{ Carbon\Carbon::create($revenue->year, $revenue->month)->format('F Y') }}
                            </td>
                            <td class="text-success">
                                <strong>UGX {{ number_format($revenue->revenue) }}</strong>
                            </td>
                            <td>
                                @if($growth > 0)
                                    <span class="badge bg-success">+{{ number_format($growth, 1) }}%</span>
                                @elseif($growth < 0)
                                    <span class="badge bg-danger">{{ number_format($growth, 1) }}%</span>
                                @else
                                    <span class="badge bg-secondary">0%</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No monthly revenue data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
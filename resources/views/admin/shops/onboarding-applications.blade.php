@extends('admin.layouts.app')

@section('title', 'Onboarding Applications')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📋 Onboarding Applications</h1>
        <div class="btn-group">
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Shops
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-6">
            <div class="card bg-primary text-white text-center">
                <div class="card-body py-3">
                    <h5 class="card-title">{{ $stats['all'] }}</h5>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card bg-warning text-dark text-center">
                <div class="card-body py-3">
                    <h5 class="card-title">{{ $stats['paid'] }}</h5>
                    <small>Paid</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card bg-success text-white text-center">
                <div class="card-body py-3">
                    <h5 class="card-title">{{ $stats['approved'] }}</h5>
                    <small>Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card bg-info text-white text-center">
                <div class="card-body py-3">
                    <h5 class="card-title">{{ $stats['pending'] }}</h5>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card bg-danger text-white text-center">
                <div class="card-body py-3">
                    <h5 class="card-title">{{ $stats['rejected'] }}</h5>
                    <small>Rejected</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.shops.onboarding-applications', ['status' => 'all']) }}" 
                   class="btn btn-{{ $status == 'all' ? 'primary' : 'outline-primary' }}">All</a>
                <a href="{{ route('admin.shops.onboarding-applications', ['status' => 'paid']) }}" 
                   class="btn btn-{{ $status == 'paid' ? 'warning' : 'outline-warning' }}">Paid</a>
                <a href="{{ route('admin.shops.onboarding-applications', ['status' => 'approved']) }}" 
                   class="btn btn-{{ $status == 'approved' ? 'success' : 'outline-success' }}">Approved</a>
                <a href="{{ route('admin.shops.onboarding-applications', ['status' => 'pending']) }}" 
                   class="btn btn-{{ $status == 'pending' ? 'info' : 'outline-info' }}">Pending</a>
                <a href="{{ route('admin.shops.onboarding-applications', ['status' => 'rejected']) }}" 
                   class="btn btn-{{ $status == 'rejected' ? 'danger' : 'outline-danger' }}">Rejected</a>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Business</th>
                            <th>Owner</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                        <tr>
                            <td>
                                <code>{{ $application->reference }}</code>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $application->business_name }}</div>
                                <small class="text-muted">{{ $application->location }}</small>
                            </td>
                            <td>
                                <div>{{ $application->owner_name }}</div>
                                <small class="text-muted">{{ $application->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $application->plan_details['name'] ?? 'N/A' }}</span>
                            </td>
                            <td class="fw-bold text-success">
                                UGX {{ number_format($application->activation_fee) }}
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'secondary',
                                        'paid' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'processing_payment' => 'info',
                                        'payment_failed' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$application->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                </span>
                            </td>
                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.shops.onboarding-application-show', $application) }}" 
                                       class="btn btn-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if($application->status === 'paid')
                                    <button type="button" class="btn btn-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#approveModal{{ $application->id }}"
                                            title="Approve Application">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $application->id }}"
                                            title="Reject Application">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Approve Modal -->
                                <div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Approve Application</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.shops.approve-application', $application) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Create shop account for: <strong>{{ $application->business_name }}</strong></p>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Admin Email *</label>
                                                        <input type="email" name="admin_email" class="form-control" required
                                                               value="{{ strtolower(str_replace(' ', '.', $application->owner_name)) }}@shop.com">
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Admin Password *</label>
                                                        <div class="input-group">
                                                            <input type="text" name="admin_password" class="form-control" 
                                                                   id="password{{ $application->id }}" required>
                                                            <button type="button" class="btn btn-outline-secondary" 
                                                                    onclick="generatePassword({{ $application->id }})">
                                                                <i class="bi bi-arrow-repeat"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Create Shop & Approve</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Reject Application</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.shops.reject-application', $application) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Reject application for: <strong>{{ $application->business_name }}</strong></p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Reason for Rejection *</label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" 
                                                                  placeholder="Please provide a reason for rejection..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Reject Application</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4"></i>
                                    <p class="mt-2">No applications found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $applications->links() }}
        </div>
    </div>
</div>

<script>
function generatePassword(applicationId) {
    fetch('{{ route("admin.shops.generate-password") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('password' + applicationId).value = data.password;
        });
}
</script>
@endsection
@extends('admin.layouts.app')

@section('title', 'Application Details - ' . $application->reference)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📄 Application: {{ $application->reference }}</h1>
        <div>
            <a href="{{ route('admin.shops.onboarding-applications') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Applications
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Application Details -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Business Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Reference:</th>
                            <td><code>{{ $application->reference }}</code></td>
                        </tr>
                        <tr>
                            <th>Business Name:</th>
                            <td class="fw-semibold">{{ $application->business_name }}</td>
                        </tr>
                        <tr>
                            <th>Business Type:</th>
                            <td>
                                <span class="badge bg-info">{{ $application->plan_details['name'] ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td>{{ $application->location }}</td>
                        </tr>
                        <tr>
                            <th>Activation Fee:</th>
                            <td class="fw-bold text-success">UGX {{ number_format($application->activation_fee) }}</td>
                        </tr>
                        <tr>
                            <th>Monthly Fee:</th>
                            <td class="fw-bold text-warning">UGX {{ number_format($application->monthly_fee) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Owner Details -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Owner Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Owner Name:</th>
                            <td>{{ $application->owner_name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $application->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $application->phone }}</td>
                        </tr>
                        <tr>
                            <th>Application Date:</th>
                            <td>{{ $application->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Date:</th>
                            <td>
                                @if($application->paid_at)
                                    {{ $application->paid_at->format('M d, Y H:i') }}
                                @else
                                    <span class="text-muted">Not paid</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status & Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Application Status & Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="text-center">
                                @php
                                    $statusColors = [
                                        'pending' => 'secondary',
                                        'paid' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'processing_payment' => 'info',
                                        'payment_failed' => 'danger'
                                    ];
                                    $statusIcon = [
                                        'pending' => '⏳',
                                        'paid' => '💰',
                                        'approved' => '✅',
                                        'rejected' => '❌',
                                        'processing_payment' => '🔄',
                                        'payment_failed' => '💥'
                                    ];
                                @endphp
                                <div class="display-4 mb-2">{{ $statusIcon[$application->status] ?? '📄' }}</div>
                                <h4 class="text-{{ $statusColors[$application->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                </h4>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            @if($application->status === 'paid')
                            <div class="alert alert-warning">
                                <h6>Ready for Approval</h6>
                                <p class="mb-3">This application has been paid and is ready for shop creation.</p>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success" 
                                            data-bs-toggle="modal" data-bs-target="#approveModal">
                                        <i class="bi bi-check-circle"></i> Approve & Create Shop
                                    </button>
                                    <button type="button" class="btn btn-danger"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="bi bi-x-circle"></i> Reject Application
                                    </button>
                                </div>
                            </div>
                            @elseif($application->status === 'approved')
                            <div class="alert alert-success">
                                <h6>✅ Application Approved</h6>
                                <p class="mb-0">This application has been approved and the shop has been created.</p>
                            </div>
                            @elseif($application->status === 'rejected')
                            <div class="alert alert-danger">
                                <h6>❌ Application Rejected</h6>
                                <p class="mb-0">{{ $application->admin_notes }}</p>
                            </div>
                            @endif

                            @if($application->admin_notes)
                            <div class="mt-3">
                                <strong>Admin Notes:</strong>
                                <p class="text-muted">{{ $application->admin_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
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
                                   id="password" required>
                            <button type="button" class="btn btn-outline-secondary" 
                                    onclick="generatePassword()">
                                <i class="bi bi-arrow-repeat"></i> Generate
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
<div class="modal fade" id="rejectModal" tabindex="-1">
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

<script>
function generatePassword() {
    fetch('{{ route("admin.shops.generate-password") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('password').value = data.password;
        });
}
</script>
@endsection
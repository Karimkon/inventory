<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #f8fafc;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .pos-header {
            background: rgba(30, 41, 59, 0.95);
            border-bottom: 2px solid #0ea5e9;
            padding: 1rem 0;
            backdrop-filter: blur(10px);
        }
        .pos-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .shop-info {
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.4);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #e0f2fe;
        }
        
        /* Product Cards */
        .product-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            color: #1e293b;
        }
        .product-card:hover {
            border-color: #0ea5e9;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.15);
        }
        
        /* Text Colors for Light Background */
        .product-card .card-title {
            color: #1e293b;
            font-weight: 600;
        }
        .product-card .text-success {
            color: #059669 !important;
            font-weight: 600;
        }
        .product-card .text-muted {
            color: #64748b !important;
        }
        
        /* Buttons */
        .btn-pos {
            background: #0ea5e9;
            border: none;
            color: white;
            font-weight: 500;
        }
        .btn-pos:hover {
            background: #0284c7;
            color: white;
            transform: scale(1.02);
        }
        
        /* Form Controls */
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #cbd5e1;
            color: #1e293b;
        }
        .form-control:focus {
            background: white;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            color: #1e293b;
        }
        
        /* Search Card */
        .search-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: #1e293b;
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 10px;
            font-weight: 500;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #047857;
            border-left: 4px solid #10b981;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }
        
        /* Pagination */
        .pagination .page-link {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #cbd5e1;
            color: #475569;
        }
        .pagination .page-link:hover {
            background: #0ea5e9;
            border-color: #0ea5e9;
            color: white;
        }
        .pagination .page-item.active .page-link {
            background: #0ea5e9;
            border-color: #0ea5e9;
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            font-size: 0.75rem;
        }
        
        /* Quick Action Buttons */
        .btn-outline-info {
            color: #0ea5e9;
            border-color: #0ea5e9;
        }
        .btn-outline-info:hover {
            background: #0ea5e9;
            color: white;
        }
        .btn-outline-warning {
            color: #f59e0b;
            border-color: #f59e0b;
        }
        .btn-outline-warning:hover {
            background: #f59e0b;
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            color: #64748b;
        }
    </style>
</head>
<body>
    <!-- POS Header -->
    <div class="pos-header">
        <div class="pos-container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">🖥️ POS Terminal</h1>
                    <small class="text-muted">Quick Selling System</small>
                </div>
                <div class="text-end">
                    <div class="shop-info d-inline-block me-3">
                        <strong>{{ session('pos_shop_name') }}</strong>
                    </div>
                    <form action="{{ route('pos.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-box-arrow-right"></i> Exit POS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pos-container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
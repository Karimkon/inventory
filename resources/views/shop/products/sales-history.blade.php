@extends('shop.layouts.app')

@section('title', 'Sales History & Analytics')

@section('content')
<div class="sales-history-container">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="bi bi-graph-up"></i> Sales Analytics
                </h1>
                <p class="page-subtitle text-muted">
                    Track your sales performance and revenue insights
                </p>
            </div>
            <div class="header-actions">
                <a href="{{ route('shop.products.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Back to POS
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid mb-4">
        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $totalSales }}</div>
                        <div class="stat-label">Total Transactions</div>
                        <div class="stat-trend">
                            <i class="bi bi-arrow-up"></i> This period
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $totalQuantity }}</div>
                        <div class="stat-label">Items Sold</div>
                        <div class="stat-trend">
                            <i class="bi bi-box"></i> Total units
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">UGX {{ number_format($totalRevenue) }}</div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-trend">
                            <i class="bi bi-cash-stack"></i> Gross sales
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">UGX {{ number_format($totalProfit) }}</div>
                        <div class="stat-label">Total Profit</div>
                        <div class="stat-trend">
                            <i class="bi bi-arrow-up-right"></i> Net earnings
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filter Sales Data
            </h5>
        </div>
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET" id="salesFilterForm">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Time Range</label>
                    <select name="time_range" class="form-select" onchange="updateDateField(this.value)">
                        <option value="today" {{ $timeRange == 'today' ? 'selected' : '' }}>📅 Today</option>
                        <option value="yesterday" {{ $timeRange == 'yesterday' ? 'selected' : '' }}>📅 Yesterday</option>
                        <option value="week" {{ $timeRange == 'week' ? 'selected' : '' }}>📅 This Week</option>
                        <option value="month" {{ $timeRange == 'month' ? 'selected' : '' }}>📅 This Month</option>
                        <option value="custom" {{ $timeRange == 'custom' ? 'selected' : '' }}>📅 Custom Date</option>
                    </select>
                </div>
                
                <div class="col-lg-3 col-md-6" id="dateField" style="{{ $timeRange == 'custom' ? '' : 'display: none;' }}">
                    <label class="form-label fw-semibold">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" 
                           onchange="this.form.submit()">
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Filter by Product</label>
                    <select name="product_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('shop.products.sales-history') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Summary by Product -->
    @if($salesByProduct->count() > 0)
    <div class="summary-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-bar-chart"></i> Product Performance
            </h5>
            <span class="badge bg-primary">{{ $salesByProduct->count() }} products</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Units Sold</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Profit</th>
                            <th class="text-center">Transactions</th>
                            <th class="text-center">Avg. Profit/Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByProduct as $productSales)
                        <tr>
                            <td class="fw-semibold">
                                <div class="d-flex align-items-center">
                                    <div class="product-badge me-2">
                                        <i class="bi bi-box"></i>
                                    </div>
                                    <span title="{{ $productSales['product_name'] }}">
                                        {{ \Illuminate\Support\Str::limit($productSales['product_name'], 30) }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill">{{ $productSales['total_quantity'] }}</span>
                            </td>
                            <td class="text-end text-success fw-bold">UGX {{ number_format($productSales['total_revenue']) }}</td>
                            <td class="text-end text-warning fw-bold">UGX {{ number_format($productSales['total_profit']) }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary rounded-pill">{{ $productSales['sales_count'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-info fw-semibold">
                                    UGX {{ number_format($productSales['total_quantity'] > 0 ? $productSales['total_profit'] / $productSales['total_quantity'] : 0) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    @if($salesByProduct->count() > 5)
                    <tfoot class="table-light">
                        <tr>
                            <td class="fw-bold">Totals</td>
                            <td class="text-center fw-bold">{{ $totalQuantity }}</td>
                            <td class="text-end fw-bold text-success">UGX {{ number_format($totalRevenue) }}</td>
                            <td class="text-end fw-bold text-warning">UGX {{ number_format($totalProfit) }}</td>
                            <td class="text-center fw-bold">{{ $totalSales }}</td>
                            <td class="text-center fw-bold text-info">
                                UGX {{ number_format($totalQuantity > 0 ? $totalProfit / $totalQuantity : 0) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Sales History -->
    <div class="sales-history-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-clock-history"></i> Transaction History
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary">{{ $sales->total() }} records</span>
                <button class="btn btn-sm btn-outline-secondary" onclick="exportToCSV()">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="salesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date & Time</th>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end">Profit</th>
                            <th class="text-end">Cost</th>
                            <th class="text-center pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr class="sale-row">
                            <td class="ps-3">
                                <div class="d-flex flex-column">
                                    <small class="fw-semibold">{{ $sale->created_at->format('M d, Y') }}</small>
                                    <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="product-icon me-2">
                                        <i class="bi bi-box text-primary"></i>
                                    </div>
                                    <span class="fw-semibold product-name" title="{{ $sale->product->name }}">
                                        {{ \Illuminate\Support\Str::limit($sale->product->name, 25) }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill px-2">{{ $sale->quantity }}</span>
                            </td>
                            <td class="text-end fw-semibold">UGX {{ number_format($sale->sold_price) }}</td>
                            <td class="text-end fw-bold text-success">UGX {{ number_format($sale->sold_price * $sale->quantity) }}</td>
                            <td class="text-end">
                                <span class="profit-badge">
                                    UGX {{ number_format(($sale->sold_price - $sale->cost_price) * $sale->quantity) }}
                                </span>
                            </td>
                            <td class="text-end text-muted">UGX {{ number_format($sale->cost_price) }}</td>
                            <td class="text-center pe-3">
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="showSaleDetails({{ $sale->id }})"
                                        title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-receipt display-4 text-muted"></i>
                                    <h5 class="mt-3 text-muted">No Sales Records</h5>
                                    <p class="text-muted mb-3">No sales found for the selected period and filters.</p>
                                    <a href="{{ route('shop.products.index') }}" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i> Start Selling
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sales->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $sales->firstItem() ?? 0 }} to {{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }} entries
                </div>
                {{ $sales->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Sale Details Modal -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sale Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="saleDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<style>
.sales-history-container {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.page-subtitle {
    opacity: 0.9;
    margin-bottom: 0;
}

/* Stats Grid */
.stats-grid .stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    height: 100%;
}

.stats-grid .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.stat-primary { border-color: #6366f1; }
.stat-success { border-color: #10b981; }
.stat-info { border-color: #3b82f6; }
.stat-warning { border-color: #f59e0b; }

.stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
    flex-shrink: 0;
}

.stat-primary .stat-icon { background: linear-gradient(135deg, #6366f1, #818cf8); }
.stat-success .stat-icon { background: linear-gradient(135deg, #10b981, #34d399); }
.stat-info .stat-icon { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
.stat-warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.stat-trend {
    font-size: 0.75rem;
    color: #64748b;
}

/* Cards */
.filters-card,
.summary-card,
.sales-history-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-header {
    background: #f8fafc;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.card-body {
    padding: 1.5rem;
}

/* Table Styles */
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    border-bottom: 2px solid #e2e8f0;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.sale-row:hover {
    background-color: #f8fafc;
}

.product-badge,
.product-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.profit-badge {
    padding: 0.25rem 0.5rem;
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Empty State */
.empty-state {
    padding: 2rem;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .sales-history-container {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .stats-grid .stat-card {
        padding: 1rem;
    }
    
    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
    
    .stat-value {
         font-size: 1rem;
        font-weight: 800;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>

<script>
function updateDateField(timeRange) {
    const dateField = document.getElementById('dateField');
    if (timeRange === 'custom') {
        dateField.style.display = 'block';
    } else {
        dateField.style.display = 'none';
        document.getElementById('salesFilterForm').submit();
    }
}

function showSaleDetails(saleId) {
    // You can implement AJAX to fetch sale details
    const modal = new bootstrap.Modal(document.getElementById('saleDetailsModal'));
    
    // For now, show basic info. You can implement AJAX call here
    document.getElementById('saleDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <i class="bi bi-receipt display-4 text-primary mb-3"></i>
            <h5>Sale Details</h5>
            <p class="text-muted">Sale ID: ${saleId}</p>
            <p>Detailed sale information would be loaded here via AJAX.</p>
        </div>
    `;
    
    modal.show();
}

function exportToCSV() {
    // Simple CSV export implementation
    const table = document.getElementById('salesTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // Clean the text and remove icons/buttons
            let text = cols[j].innerText.replace(/[↗↘]/g, '').trim();
            row.push('"' + text + '"');
        }
        
        csv.push(row.join(','));
    }

    // Download CSV file
    const csvString = csv.join('\n');
    const blob = new Blob([csvString], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', `sales-history-${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    showToast('Sales data exported successfully!', 'success');
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Auto-refresh data every 30 seconds if needed
// setInterval(() => {
//     if (!document.hidden) {
//         document.getElementById('salesFilterForm').submit();
//     }
// }, 30000);

document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
});
</script>
@endsection
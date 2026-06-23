@extends('layouts.app')

@section('title', __('pos.reports'))

@section('content')
<div class="container-fluid">
    <!-- Page Header & Global Filters -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-primary"><i class="bi bi-bar-chart-line-fill me-2"></i> {{ __('pos.reports') }}</h3>
        <div class="card shadow-sm border-0">
            <div class="card-body p-2 px-3">
                <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small mb-1 fw-bold">{{ __('pos.from_date') }}</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $filters['from_date'] }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1 fw-bold">{{ __('pos.to_date') }}</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $filters['to_date'] }}">
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div class="col-auto">
                        <label class="form-label small mb-1 fw-bold">{{ __('pos.branch') }}</label>
                        <select name="branch_id" class="form-select form-select-sm">
                            <option value="all" {{ is_null($filters['branch_id']) ? 'selected' : '' }}>{{ __('pos.all_branches') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $filters['branch_id'] == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->getTranslation('name') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-filter me-1"></i> {{ __('pos.apply') ?? 'Apply' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- main Tabs Navigation -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <ul class="nav nav-pills nav-fill p-2 custom-report-tabs" id="reportTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sales-report" type="button"><i class="bi bi-cart4 me-2"></i>{{ __('pos.sales_report') }}</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#purchase-report" type="button"><i class="bi bi-bag-check me-2"></i>{{ __('pos.purchase_report') }}</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inventory-report" type="button"><i class="bi bi-box-seam me-2"></i>{{ __('pos.inventory_report') }}</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#customer-report" type="button"><i class="bi bi-people me-2"></i>{{ __('pos.customer_report') }}</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#supplier-report" type="button"><i class="bi bi-truck me-2"></i>{{ __('pos.supplier_report') }}</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#expenses-report" type="button"><i class="bi bi-receipt me-2"></i>{{ __('pos.expenses_report') }}</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#financial-report" type="button"><i class="bi bi-bank me-2"></i>{{ __('pos.financial_report') }}</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#vat-report" type="button"><i class="bi bi-percent me-2"></i>{{ __('pos.vat_report') }}</button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tabs Content -->
    <div class="tab-content border-0 mb-5" id="reportTabsContent">
        
        <!-- 1. SALES REPORT TAB -->
        <div class="tab-pane fade show active" id="sales-report">
            <x-report-kpi-cards :cards="[
                ['title' => __('pos.total_sales'), 'value' => number_format($salesReport['total_sales'], 2), 'unit' => $setting->currency, 'icon' => 'cash-stack', 'color' => 'primary'],
                ['title' => __('pos.total_vat'), 'value' => number_format($salesReport['total_tax'], 2), 'unit' => $setting->currency, 'icon' => 'percent', 'color' => 'info'],
                ['title' => __('pos.total_discounts'), 'value' => number_format($salesReport['total_discount'], 2), 'unit' => $setting->currency, 'icon' => 'tag', 'color' => 'warning'],
                ['title' => __('pos.invoice_count'), 'value' => $salesReport['invoice_count'], 'icon' => 'receipt', 'color' => 'success'],
                ['title' => __('pos.total_paid'), 'value' => number_format($salesReport['total_paid'], 2), 'unit' => $setting->currency, 'icon' => 'check2-circle', 'color' => 'success'],
                ['title' => __('pos.total_remaining'), 'value' => number_format($salesReport['total_remaining'], 2), 'unit' => $setting->currency, 'icon' => 'hourglass-split', 'color' => 'danger']
            ]" />

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">{{ __('pos.sales_history') }}</h6>
                            <div class="btn-group">
                                <a href="{{ route('reports.export', ['type' => 'sales', 'format' => 'pdf'] + $filters) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> {{ __('pos.download_pdf') }}</a>
                                <a href="{{ route('reports.export', ['type' => 'sales', 'format' => 'excel'] + $filters) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel"></i> {{ __('pos.download_excel') }}</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>{{ __('pos.invoice_number') }}</th>
                                            <th>{{ __('pos.customer') }}</th>
                                            <th>{{ __('pos.total') }}</th>
                                            <th>{{ __('pos.vat') }}</th>
                                            <th>{{ __('pos.paid') }}</th>
                                            <th>{{ __('pos.remaining') }}</th>
                                            <th>{{ __('pos.date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($salesReport['invoices'] as $invoice)
                                        @php
                                            $remaining = $invoice->total - $invoice->paid_amount;
                                        @endphp
                                        <tr>
                                            <td><span class="fw-bold text-primary">#{{ $invoice->invoice_number }}</span></td>
                                            <td>{{ $invoice->customer->name ?? __('pos.walk_in_customer') }}</td>
                                            <td class="fw-bold">{{ number_format($invoice->total, 2) }}</td>
                                            <td><span class="badge bg-info bg-opacity-10 text-info">{{ number_format($invoice->tax, 2) }}</span></td>
                                            <td><span class="badge bg-success bg-opacity-10 text-success">{{ number_format($invoice->paid_amount, 2) }}</span></td>
                                            <td>
                                                @if($remaining > 0)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">{{ number_format($remaining, 2) }}</span>
                                                @else
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ __('pos.paid') }}</span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4 h-50">
                        <div class="card-header bg-white py-3 border-0"><h6 class="mb-0 fw-bold">{{ __('pos.top_selling_products') }}</h6></div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @foreach($salesReport['top_products'] as $product)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-0 py-1">
                                    <small class="fw-bold">{{ $product->name }}</small>
                                    <span class="badge bg-primary rounded-pill small">{{ $product->total_quantity }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0 h-50">
                        <div class="card-header bg-white py-2 border-0"><h6 class="mb-0 fw-bold small text-muted">{{ __('pos.sales_trend') }}</h6></div>
                        <div class="card-body p-2">
                            <canvas id="salesChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PURCHASE REPORT TAB -->
        <div class="tab-pane fade" id="purchase-report">
            <x-report-kpi-cards :cards="[
                ['title' => __('pos.total_purchases'), 'value' => number_format($purchaseReport['total_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'cart-plus', 'color' => 'dark'],
                ['title' => __('pos.total_discounts'), 'value' => number_format($purchaseReport['total_discount'], 2), 'unit' => $setting->currency, 'icon' => 'tag', 'color' => 'warning'],
                ['title' => __('pos.invoice_count'), 'value' => $purchaseReport['invoice_count'], 'icon' => 'receipt-cutoff', 'color' => 'info'],
                ['title' => __('pos.total_paid'), 'value' => number_format($purchaseReport['total_paid'], 2), 'unit' => $setting->currency, 'icon' => 'check2-circle', 'color' => 'success'],
                ['title' => __('pos.total_remaining'), 'value' => number_format($purchaseReport['total_remaining'], 2), 'unit' => $setting->currency, 'icon' => 'hourglass-split', 'color' => 'danger']
            ]" />

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ __('pos.purchase_history') }}</h6>
                    <div class="btn-group">
                        <a href="{{ route('reports.export', ['type' => 'purchases', 'format' => 'pdf'] + $filters) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> {{ __('pos.download_pdf') }}</a>
                        <a href="{{ route('reports.export', ['type' => 'purchases', 'format' => 'excel'] + $filters) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel"></i> {{ __('pos.download_excel') }}</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('pos.invoice_number') }}</th>
                                    <th>{{ __('pos.supplier') }}</th>
                                    <th>{{ __('pos.total') }}</th>
                                    <th>{{ __('pos.paid') }}</th>
                                    <th>{{ __('pos.remaining') }}</th>
                                    <th>{{ __('pos.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseReport['purchases'] as $p)
                                <tr>
                                    <td><span class="fw-bold text-primary">#{{ $p->invoice_number }}</span></td>
                                    <td class="fw-bold">{{ $p->supplier->name ?? '-' }}</td>
                                    <td>{{ number_format($p->total_amount, 2) }}</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success">{{ number_format($p->paid_amount, 2) }}</span></td>
                                    <td>
                                        @if($p->remaining_amount > 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">{{ number_format($p->remaining_amount, 2) }}</span>
                                        @else
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ __('pos.paid') }}</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $p->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- 3. INVENTORY REPORT TAB -->
        <div class="tab-pane fade" id="inventory-report">
            <x-report-kpi-cards :cards="[
                ['title' => __('pos.total_products'), 'value' => $inventoryReport['total_products'], 'icon' => 'box-seam', 'color' => 'primary'],
                ['title' => __('pos.total_inventory_value'), 'value' => number_format($inventoryReport['inventory_value'], 2), 'unit' => $setting->currency, 'icon' => 'currency-dollar', 'color' => 'success'],
                ['title' => __('pos.total_sold_qty'), 'value' => number_format($inventoryReport['total_sold_qty'], 2), 'unit' => __('pos.unit'), 'icon' => 'cart-check', 'color' => 'info'],
                ['title' => __('pos.total_remaining_stock'), 'value' => number_format($inventoryReport['total_remaining_stock'], 2), 'unit' => __('pos.unit'), 'icon' => 'archive', 'color' => 'warning'],
                ['title' => __('pos.turnover_rate'), 'value' => $inventoryReport['turnover_rate'], 'unit' => 'x', 'icon' => 'arrow-repeat', 'color' => 'primary'],
                ['title' => __('pos.sales_to_stock_ratio'), 'value' => $inventoryReport['sales_to_stock_ratio'], 'unit' => '%', 'icon' => 'graph-up-arrow', 'color' => 'success'],
                ['title' => __('pos.low_stock_items'), 'value' => $inventoryReport['low_stock_count'], 'icon' => 'exclamation-triangle', 'color' => 'warning'],
                ['title' => __('pos.out_of_stock'), 'value' => $inventoryReport['out_of_stock_count'], 'icon' => 'x-circle', 'color' => 'danger'],
                ['title' => __('pos.expired_products'), 'value' => $inventoryReport['expired_count'], 'icon' => 'calendar-x', 'color' => 'dark']
            ]" />

            @if($inventoryReport['low_stock_count'] > 0)
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                    <h5 class="mb-0 fw-bold">{{ __('pos.low_stock_alert') ?? 'Low Stock Alert' }}</h5>
                </div>
                <div class="table-responsive bg-white rounded shadow-sm mt-3">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('pos.product') }}</th>
                                <th>{{ __('pos.current_stock') }}</th>
                                <th>{{ __('pos.minimum_stock') }}</th>
                                <th>{{ __('pos.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryReport['low_stock'] as $lp)
                            <tr>
                                <td class="fw-bold">{{ $lp->name }}</td>
                                <td><span class="text-danger fw-bold fs-6">{{ $lp->current_stock }}</span></td>
                                <td>{{ $lp->minimum_stock }}</td>
                                <td>
                                    <a href="{{ route('purchases.create', ['product_id' => $lp->id]) }}" class="btn btn-xs btn-primary py-0 px-2 small">{{ __('pos.add_stock') ?? 'Restock' }}</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">{{ __('pos.current_stock_status') }}</h6>
                            <div class="btn-group">
                                <a href="{{ route('reports.export', ['type' => 'inventory', 'format' => 'pdf'] + $filters) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> {{ __('pos.download_pdf') }}</a>
                                <a href="{{ route('reports.export', ['type' => 'inventory', 'format' => 'excel'] + $filters) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel"></i> {{ __('pos.download_excel') }}</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 500px">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th>{{ __('pos.product') }}</th>
                                            <th>{{ __('pos.stock_quantity') }}</th>
                                            <th>{{ __('pos.minimum_stock') }}</th>
                                            <th>{{ __('pos.stock_value') }}</th>
                                            <th>{{ __('pos.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventoryReport['products'] as $p)
                                        <tr>
                                            <td class="fw-bold">{{ $p->name }}</td>
                                            <td>
                                                <span class="badge {{ $p->current_stock <= $p->minimum_stock ? 'bg-danger' : 'bg-primary' }}">
                                                    {{ $p->current_stock }}
                                                </span>
                                            </td>
                                            <td>{{ $p->minimum_stock }}</td>
                                            <td class="fw-bold text-success">
                                                {{ number_format($p->stock_value, 2) }} {{ $setting->currency }}
                                            </td>
                                            <td>
                                                @if($p->current_stock <= 0) <span class="badge bg-dark">{{ __('pos.out_of_stock') }}</span>
                                                @elseif($p->current_stock <= $p->minimum_stock) <span class="badge bg-warning text-dark">{{ __('pos.low_stock') }}</span>
                                                @else <span class="badge bg-success">{{ __('pos.sufficient') }}</span> @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light fw-bold border-top border-2">
                                        <tr>
                                            <td colspan="3" class="text-end text-muted">{{ __('pos.total_inventory_value') }}</td>
                                            <td class="text-success fs-6">{{ number_format($inventoryReport['inventory_value'], 2) }} {{ $setting->currency }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4 border-start border-warning border-4">
                        <div class="card-header bg-white border-0 py-2"><h6 class="mb-0 fw-bold text-warning">{{ __('pos.expired_products') }}</h6></div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush overflow-auto" style="max-height: 300px">
                                @forelse($inventoryReport['expired'] as $eb)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="small">{{ $eb->product->name }}</span>
                                    <span class="badge bg-danger">{{ $eb->expiry_date->format('Y-m-d') }}</span>
                                </li>
                                @empty <li class="list-group-item small text-muted text-center py-3">{{ __('pos.no_expired_items') }}</li> @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0 border-start border-secondary border-4">
                        <div class="card-header bg-white border-0 py-2"><h6 class="mb-0 fw-bold text-secondary">{{ __('pos.waste_adjustments') }}</h6></div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush overflow-auto" style="max-height: 400px">
                                @forelse($inventoryReport['adjustments'] as $adj)
                                <li class="list-group-item small">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">{{ $adj->product->name }}</span>
                                        <span class="text-danger">{{ $adj->quantity }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted">
                                        <span>{{ $adj->type }}</span>
                                        <span>{{ $adj->created_at->format('m/d') }}</span>
                                    </div>
                                </li>
                                @empty <li class="list-group-item small text-muted text-center py-3">{{ __('pos.no_adjustments_found') }}</li> @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. CUSTOMER REPORT TAB -->
        <div class="tab-pane fade" id="customer-report">
            <x-report-kpi-cards :cards="[
                ['title' => __('pos.total_customers'), 'value' => $customerReport['total_customers'], 'icon' => 'people', 'color' => 'primary'],
                ['title' => __('pos.total_purchases'), 'value' => number_format($customerReport['total_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'bag-fill', 'color' => 'info'],
                ['title' => __('pos.total_paid'), 'value' => number_format($customerReport['total_paid'], 2), 'unit' => $setting->currency, 'icon' => 'cash-coin', 'color' => 'success'],
                ['title' => __('pos.collection_rate'), 'value' => number_format($customerReport['collection_rate'], 1), 'unit' => '%', 'icon' => 'graph-up-arrow', 'color' => 'warning'],
                ['title' => __('pos.total_remaining'), 'value' => number_format($customerReport['total_remaining'], 2), 'unit' => $setting->currency, 'icon' => 'hourglass-split', 'color' => 'danger']
            ]" />

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ __('pos.customers') }}</h6>
                    <div class="btn-group">
                        <a href="{{ route('reports.export', ['type' => 'customers', 'format' => 'pdf'] + $filters) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> {{ __('pos.download_pdf') }}</a>
                        <a href="{{ route('reports.export', ['type' => 'customers', 'format' => 'excel'] + $filters) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel"></i> {{ __('pos.download_excel') }}</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('pos.customer_id') }}</th>
                                    <th>{{ __('pos.customer_name') }}</th>
                                    <th>{{ __('pos.phone') }}</th>
                                    <th>{{ __('pos.email') }}</th>
                                    <th>{{ __('pos.address') }}</th>
                                    <th>{{ __('pos.visits') }}</th>
                                    <th>{{ __('pos.total_purchases') }}</th>
                                    <th>{{ __('pos.paid') }}</th>
                                    <th>{{ __('pos.balance') }}</th>
                                    <th>{{ __('pos.responsible_user') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerReport['customers'] as $c)
                                <tr>
                                    <td><span class="text-muted small">#{{ $c->id }}</span></td>
                                    <td class="fw-bold">{{ $c->name }}</td>
                                    <td>{{ $c->phone }}</td>
                                    <td><small>{{ $c->email ?? '-' }}</small></td>
                                    <td><small>{{ $c->address ?? '-' }}</small></td>
                                    <td>
                                        <span class="badge rounded-pill" style="background: linear-gradient(135deg, #8b5cf6, #6366f1); padding: 0.4em 0.8em; box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);">
                                            <i class="bi bi-arrow-repeat me-1 fw-bold"></i> {{ $c->visits }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">{{ number_format($c->total_purchases, 2) }}</td>
                                    <td class="text-success">{{ number_format($c->total_paid, 2) }}</td>
                                    <td class="text-danger">{{ number_format($c->balance, 2) }}</td>
                                    <td><span class="badge bg-info bg-opacity-10 text-info small">{{ $c->responsible_user ?? '-' }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. SUPPLIER REPORT TAB -->
        <div class="tab-pane fade" id="supplier-report">
            <x-report-kpi-cards :cards="[
                ['title' => __('pos.total_suppliers'), 'value' => $supplierReport['total_suppliers'], 'icon' => 'truck', 'color' => 'primary'],
                ['title' => __('pos.total_purchases'), 'value' => number_format($supplierReport['total_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'cart-check', 'color' => 'info'],
                ['title' => __('pos.total_paid'), 'value' => number_format($supplierReport['total_paid'], 2), 'unit' => $setting->currency, 'icon' => 'shield-check', 'color' => 'success'],
                ['title' => __('pos.total_remaining'), 'value' => number_format($supplierReport['total_remaining'], 2), 'unit' => $setting->currency, 'icon' => 'wallet2', 'color' => 'danger']
            ]" />

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ __('pos.suppliers') }}</h6>
                    <div class="btn-group">
                        <a href="{{ route('reports.export', ['type' => 'suppliers', 'format' => 'pdf'] + $filters) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> {{ __('pos.download_pdf') }}</a>
                        <a href="{{ route('reports.export', ['type' => 'suppliers', 'format' => 'excel'] + $filters) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel"></i> {{ __('pos.download_excel') }}</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('pos.supplier_number') }}</th>
                                    <th>{{ __('pos.supplier_name') }}</th>
                                    <th>{{ __('pos.email') }}</th>
                                    <th>{{ __('pos.address') }}</th>
                                    <th>{{ __('pos.total_purchases') }}</th>
                                    <th>{{ __('pos.paid') }}</th>
                                    <th>{{ __('pos.remaining') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplierReport['suppliers'] as $s)
                                <tr>
                                    <td><span class="text-muted small">{{ $s->supplier_number ?? '#'.$s->id }}</span></td>
                                    <td class="fw-bold">{{ $s->name }}</td>
                                    <td><small>{{ $s->email ?? '-' }}</small></td>
                                    <td><small>{{ $s->address ?? '-' }}</small></td>
                                    <td class="fw-bold">{{ number_format($s->total_purchases, 2) }}</td>
                                    <td class="text-success">{{ number_format($s->total_paid, 2) }}</td>
                                    <td class="text-danger">{{ number_format($s->total_remaining, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: EXPENSES REPORT TAB -->
        <div class="tab-pane fade" id="expenses-report">
            <x-report-kpi-cards :cards="[
                ['title' => __('pos.total_expenses'), 'value' => number_format($expensesReport['total_expenses'], 2), 'unit' => $setting->currency, 'icon' => 'wallet2', 'color' => 'danger'],
                ['title' => __('pos.expense_count'), 'value' => $expensesReport['expense_count'], 'icon' => 'receipt', 'color' => 'info'],
                ['title' => __('pos.average_expense'), 'value' => number_format($expensesReport['average_expense'], 2), 'unit' => $setting->currency, 'icon' => 'calculator', 'color' => 'warning'],
                ['title' => __('pos.highest_expense'), 'value' => number_format($expensesReport['highest_expense']['amount'], 2), 'unit' => $setting->currency, 'icon' => 'arrow-up-circle', 'color' => 'danger']
            ]" />

            {{-- Highest Expense Highlight Card --}}
            @if($expensesReport['highest_expense']['amount'] > 0)
            <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center gap-3"
                 style="background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%); border-left: 5px solid #dc3545 !important;">
                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:52px;height:52px;">
                    <i class="bi bi-arrow-up-circle-fill text-danger fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-danger fs-6">{{ __('pos.highest_expense') }}</div>
                    <div class="d-flex align-items-center gap-3 mt-1 flex-wrap">
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold fs-6 px-3 py-2">
                            {{ number_format($expensesReport['highest_expense']['amount'], 2) }} {{ $setting->currency }}
                        </span>
                        <span class="text-dark fw-semibold">
                            <i class="bi bi-tag me-1"></i>{{ $expensesReport['highest_expense']['type'] }}
                        </span>
                        @if($expensesReport['highest_expense']['date'])
                        <span class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>{{ $expensesReport['highest_expense']['date'] }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif


            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ __('pos.expenses_list') ?? 'Expenses List' }}</h6>
                    <div class="btn-group">
                        <a href="{{ route('reports.export', ['type' => 'expenses', 'format' => 'pdf'] + $filters) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> {{ __('pos.download_pdf') }}</a>
                        <a href="{{ route('reports.export', ['type' => 'expenses', 'format' => 'excel'] + $filters) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel"></i> {{ __('pos.download_excel') }}</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('pos.date') }}</th>
                                    <th>{{ __('pos.category') }}</th>
                                    <th>{{ __('pos.amount') }}</th>
                                    <th>{{ __('pos.description') }}</th>
                                    <th>{{ __('pos.branch') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expensesReport['expenses'] as $e)
                                <tr>
                                    <td>{{ $e->expense_date }}</td>
                                    <td><span class="badge bg-secondary">{{ $e->type }}</span></td>
                                    <td class="fw-bold">{{ number_format($e->amount, 2) }} {{ $setting->currency }}</td>
                                    <td><small>{{ $e->description_ar ?: $e->description_en }}</small></td>
                                    <td>{{ $e->branch->name ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. FINANCIAL REPORT (PROFIT & LOSS) TAB -->
        <div class="tab-pane fade" id="financial-report">
            <x-report-kpi-cards :cards="[
                ['title' => __('pos.total_sales'), 'value' => number_format($financialReport['total_sales'], 2), 'unit' => $setting->currency, 'icon' => 'graph-up', 'color' => 'success'],
                ['title' => __('pos.total_purchases'), 'value' => number_format($financialReport['total_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'graph-down', 'color' => 'danger'],
                ['title' => __('pos.total_expenses'), 'value' => number_format($financialReport['total_expenses'], 2), 'unit' => $setting->currency, 'icon' => 'receipt', 'color' => 'warning'],
                ['title' => __('pos.net_profit'), 'value' => number_format($financialReport['net_profit'], 2), 'unit' => $setting->currency, 'icon' => 'bank', 'color' => 'primary']
            ]" />

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">{{ __('pos.profit_loss_summary') ?? 'Profit & Loss Summary' }}</h6>
                            <div class="btn-group">
                                <a href="{{ route('reports.export', ['type' => 'financial', 'format' => 'pdf'] + $filters) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                                <a href="{{ route('reports.export', ['type' => 'financial', 'format' => 'excel'] + $filters) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                            </div>
                        </div>
                        <div class="card-body py-4">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span>{{ __('pos.total_sales') }}</span>
                                <span class="fw-bold text-success">+ {{ number_format($financialReport['total_sales'], 2) }} {{ $setting->currency }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span>{{ __('pos.total_purchases') }}</span>
                                <span class="fw-bold text-danger">- {{ number_format($financialReport['total_purchases'], 2) }} {{ $setting->currency }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span>{{ __('pos.total_expenses') }}</span>
                                <span class="fw-bold text-danger">- {{ number_format($financialReport['total_expenses'], 2) }} {{ $setting->currency }}</span>
                            </div>
                            
                            <!-- Expense Breakdown -->
                            <div class="mt-4 mb-4">
                                <h6 class="small fw-bold text-muted text-uppercase mb-3">{{ __('pos.expense_breakdown') ?? 'Expense Breakdown' }}</h6>
                                @foreach($financialReport['expense_breakdown'] as $eb)
                                <div class="d-flex justify-content-between small mb-2 text-muted">
                                    <span>{{ $eb->type }}</span>
                                    <span>{{ number_format($eb->total, 2) }} {{ $setting->currency }}</span>
                                </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between bg-light p-3 rounded-3 mt-4">
                                <span class="fw-bold fs-5">{{ __('pos.net_profit') }}</span>
                                <span class="fw-bold fs-5 {{ $financialReport['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($financialReport['net_profit'], 2) }} {{ $setting->currency }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3 border-0"><h6 class="mb-0 fw-bold">{{ __('pos.profit_analysis') }}</h6></div>
                        <div class="card-body">
                            <canvas id="profitChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. VAT REPORT TAB -->
        <div class="tab-pane fade" id="vat-report">
            @php
                $vatRateLabel = $vatReport['vat_rate'] > 0 ? '(' . $vatReport['vat_rate'] . '%)' : '';
            @endphp
            <x-report-kpi-cards :cards="[
                ['title' => __('pos.total_sales'), 'value' => number_format($vatReport['total_sales'], 2), 'unit' => $setting->currency, 'icon' => 'shop', 'color' => 'primary'],
                ['title' => __('pos.vat_collected') . ' ' . $vatRateLabel, 'value' => number_format($vatReport['vat_collected'], 2), 'unit' => $setting->currency, 'icon' => 'percent', 'color' => 'success'],
                ['title' => __('pos.net_sales'), 'value' => number_format($vatReport['net_sales'], 2), 'unit' => $setting->currency, 'icon' => 'calculator', 'color' => 'info']
            ]" />
        </div>

    </div>
</div>

<style>
    .custom-report-tabs .nav-link { 
        border: none; 
        color: #6c757d; 
        font-weight: 600; 
        border-radius: 12px; 
        padding: 14px 18px;
        transition: 0.3s;
    }
    .custom-report-tabs .nav-link.active { 
        background-color: var(--primary-color) !important; 
        color: #fff !important; 
        box-shadow: 0 4px 15px rgba(70, 191, 163, 0.25); 
    }
    .custom-report-tabs .nav-link:hover:not(.active) { background-color: rgba(70, 191, 163, 0.05); color: var(--primary-color); }
    
    @media print {
        .navbar, #sidebar, .btn-group, form, .nav-pills, .bi, .no-print { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        #content { margin: 0 !important; width: 100% !important; }
        .container-fluid { width: 100% !important; padding: 0 !important; }
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesReport['sales_by_day']->keys()) !!},
            datasets: [{
                label: "{{ __('pos.total_sales') }}",
                data: {!! json_encode($salesReport['sales_by_day']->values()) !!},
                borderColor: '#46bfa3',
                backgroundColor: 'rgba(70, 191, 163, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Profit Analysis Chart
    const profitCtx = document.getElementById('profitChart').getContext('2d');
    new Chart(profitCtx, {
        type: 'bar',
        data: {
            labels: ["{{ __('pos.sales') }}", "{{ __('pos.purchases') }}", "{{ __('pos.expenses') }}"],
            datasets: [{
                label: 'Summary',
                data: [{{ $financialReport['total_sales'] }}, {{ $financialReport['total_purchases'] }}, {{ $financialReport['total_expenses'] }}],
                backgroundColor: ['#46bfa3', '#344767', '#dc3545'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Handle Tab Persistence
    $('#reportTabs button').on('click', function (e) {
        $(this).tab('show');
    });
</script>
@endpush

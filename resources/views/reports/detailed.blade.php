@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 welcome-hero-card overflow-hidden position-relative">
        <div class="hero-blob blob-1"></div>
        <div class="hero-blob blob-2"></div>
        
        <div class="card-body p-4 position-relative" style="z-index: 1;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <a href="{{ route('reports.index', $filters) }}" class="btn btn-sm btn-outline-light mb-3 rounded-pill text-xs px-3" style="border: 1px solid rgba(255,255,255,0.3);">
                        <i class="bi bi-arrow-left me-1"></i>{{ app()->getLocale() == 'ar' ? 'العودة للوحة التقارير' : 'Back to Dashboard' }}
                    </a>
                    <h3 class="fw-bold welcome-title mb-1" style="color: #fff !important;">
                        @switch($type)
                            @case('top-selling')
                                {{ app()->getLocale() == 'ar' ? 'المنتجات الأكثر مبيعاً' : 'Top Selling Products' }}
                                @break
                            @case('top-profitable')
                                {{ app()->getLocale() == 'ar' ? 'المنتجات الأكثر ربحية' : 'Top Profitable Products' }}
                                @break
                            @case('least-profitable')
                                {{ app()->getLocale() == 'ar' ? 'المنتجات الأقل ربحية' : 'Least Profitable Products' }}
                                @break
                            @case('fast-moving')
                                {{ app()->getLocale() == 'ar' ? 'المنتجات سريعة الحركة' : 'Fast Moving Products' }}
                                @break
                            @case('slow-moving')
                                {{ app()->getLocale() == 'ar' ? 'المنتجات بطيئة الحركة' : 'Slow Moving Products' }}
                                @break
                            @case('top-customers')
                                {{ app()->getLocale() == 'ar' ? 'تحليلات العملاء الأكثر شراءً' : 'Top Customers Analytics' }}
                                @break
                            @case('suppliers')
                                {{ app()->getLocale() == 'ar' ? 'تحليلات كبار الموردين' : 'Top Suppliers Analytics' }}
                                @break
                            @case('purchase-products')
                                {{ app()->getLocale() == 'ar' ? 'المنتجات الأكثر شراءً' : 'Top Purchased Products' }}
                                @break
                            @case('low-stock')
                                {{ app()->getLocale() == 'ar' ? 'الأصناف منخفضة المخزون' : 'Low Stock Products' }}
                                @break
                            @case('out-of-stock')
                                {{ app()->getLocale() == 'ar' ? 'الأصناف النافدة من المخزون' : 'Out of Stock Products' }}
                                @break
                            @case('overstock')
                                {{ app()->getLocale() == 'ar' ? 'الأصناف المتكدسة في المخزون' : 'Overstock Products' }}
                                @break
                            @case('healthy-stock')
                                {{ app()->getLocale() == 'ar' ? 'مستويات المخزون الصحي والمستقر' : 'Healthy Stock Levels' }}
                                @break
                            @case('expiring-soon')
                                {{ app()->getLocale() == 'ar' ? 'الأصناف التي توشك صلاحيتها على الانتهاء' : 'Expiring Soon Products' }}
                                @break
                            @case('expired')
                                {{ app()->getLocale() == 'ar' ? 'الأصناف منتهية الصلاحية' : 'Expired Products' }}
                                @break
                            @case('inventory-batches')
                                {{ app()->getLocale() == 'ar' ? 'تقرير دفعات وباتشات المخزون' : 'Inventory Batches Journal' }}
                                @break
                            @case('inventory-transactions')
                                {{ app()->getLocale() == 'ar' ? 'سجل حركات ومعاملات المخزون' : 'Stock Transactions Ledger' }}
                                @break
                        @endswitch
                    </h3>
                    <p class="welcome-subtitle mb-0 small opacity-75" style="color: #cbd5e1 !important;">
                        {{ app()->getLocale() == 'ar' ? 'عرض تفصيلي شامل مع خيارات الفرز، البحث والتصدير' : 'Comprehensive detailed analysis with filtering, sorting, and export capabilities' }}
                    </p>
                </div>

                <!-- Global Filters Form -->
                <form action="{{ route('reports.detailed') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                    <input type="hidden" name="type" value="{{ $type }}">
                    
                    @if($branches)
                    <div class="form-group mb-0">
                        <select name="branch_id" class="form-select form-select-sm bg-transparent text-white border-white border-opacity-25" style="height: 38px;" onchange="this.form.submit()">
                            <option value="all" class="text-dark">{{ app()->getLocale() == 'ar' ? 'كل الفروع' : 'All Branches' }}</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ (isset($filters['branch_id']) && $filters['branch_id'] == $b->id) ? 'selected' : '' }} class="text-dark">{{ $b->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="form-group mb-0">
                        <input type="date" name="from_date" class="form-control form-control-sm bg-transparent text-white border-white border-opacity-25" value="{{ $filters['from_date'] }}" style="height: 38px;" onchange="this.form.submit()">
                    </div>
                    <div class="form-group mb-0">
                        <input type="date" name="to_date" class="form-control form-control-sm bg-transparent text-white border-white border-opacity-25" value="{{ $filters['to_date'] }}" style="height: 38px;" onchange="this.form.submit()">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold" style="height: 38px;">
                        <i class="bi bi-filter"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <!-- Search -->
                <div class="position-relative" style="min-width: 250px;">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y start-0 ms-3 text-muted" style="font-size: 0.85rem;"></i>
                    <input type="text" id="detailedReportSearch" class="form-control form-control-sm ps-5 rounded-3" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث...' : 'Search...' }}" onkeyup="filterDetailedTable()" style="height: 38px; border-radius: 10px !important;">
                </div>
            </div>
        </div>

        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table id="detailedReportTable" class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                            @if($type === 'top-customers')
                                <th class="py-3 px-4 pointer-cursor" onclick="sortDetailedTable(0)">{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                                <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'عدد الفواتير' : 'Invoices Count' }}</th>
                                <th class="py-3 px-3 text-end pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'متوسط قيمة الطلب' : 'Average Order Value' }}</th>
                                <th class="py-3 px-4 text-end pointer-cursor" onclick="sortDetailedTable(3)">{{ app()->getLocale() == 'ar' ? 'إجمالي المشتريات' : 'Total Spent' }}</th>
                            @elseif($type === 'suppliers')
                                <th class="py-3 px-4 pointer-cursor" onclick="sortDetailedTable(0)">{{ app()->getLocale() == 'ar' ? 'المورد' : 'Supplier Name' }}</th>
                                <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'عدد الطلبات' : 'Orders Count' }}</th>
                                <th class="py-3 px-3 text-end pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'إجمالي المشتريات' : 'Total Purchases' }}</th>
                                <th class="py-3 px-4 text-end pointer-cursor" onclick="sortDetailedTable(3)">{{ app()->getLocale() == 'ar' ? 'النسبة' : 'Share %' }}</th>
                            @elseif($type === 'inventory-batches')
                                <th class="py-3 px-3 pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'رقم الدفعة' : 'Batch' }}</th>
                                <th class="py-3 px-2 pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                <th class="py-3 px-2 pointer-cursor" onclick="sortDetailedTable(3)">{{ app()->getLocale() == 'ar' ? 'المورد' : 'Supplier' }}</th>
                                <th class="py-3 px-2 text-center pointer-cursor" onclick="sortDetailedTable(4)">{{ app()->getLocale() == 'ar' ? 'تاريخ الشراء' : 'Purchase' }}</th>
                                <th class="py-3 px-2 text-center pointer-cursor" onclick="sortDetailedTable(5)">{{ app()->getLocale() == 'ar' ? 'الانتهاء' : 'Expiry' }}</th>
                                <th class="py-3 px-2 text-center pointer-cursor" onclick="sortDetailedTable(6)">{{ app()->getLocale() == 'ar' ? 'المتبقي' : 'Remaining' }}</th>
                                <th class="py-3 px-2 text-end pointer-cursor" onclick="sortDetailedTable(7)">{{ app()->getLocale() == 'ar' ? 'التكلفة' : 'Cost' }}</th>
                                <th class="py-3 px-3 text-center">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                            @elseif($type === 'inventory-transactions')
                                <th class="py-3 px-3 pointer-cursor" onclick="sortDetailedTable(0)">{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                                <th class="py-3 px-3 pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'نوع الحركة' : 'Type' }}</th>
                                <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(3)">{{ app()->getLocale() == 'ar' ? 'الكمية' : 'Qty' }}</th>
                                <th class="py-3 px-3 pointer-cursor" onclick="sortDetailedTable(4)">{{ app()->getLocale() == 'ar' ? 'رقم الحركة/المرجع' : 'Reference' }}</th>
                                <th class="py-3 px-4 text-end pointer-cursor" onclick="sortDetailedTable(5)">{{ app()->getLocale() == 'ar' ? 'بواسطة' : 'User' }}</th>
                            @else
                                <th class="py-3 px-4 pointer-cursor" onclick="sortDetailedTable(0)">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                @if($type === 'top-selling')
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'الكمية المباعة' : 'Qty Sold' }}</th>
                                    <th class="py-3 px-4 text-end pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'إجمالي المبيعات' : 'Total Sales' }}</th>
                                @elseif($type === 'purchase-products')
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'الكمية المشتراة' : 'Qty Purchased' }}</th>
                                    <th class="py-3 px-4 text-end pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'إجمالي التكلفة' : 'Total Cost' }}</th>
                                @elseif($type === 'top-profitable' || $type === 'least-profitable')
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'صافي الأرباح' : 'Net Profit' }}</th>
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'هامش الربح' : 'Margin %' }}</th>
                                    <th class="py-3 px-4 text-end pointer-cursor" onclick="sortDetailedTable(3)">{{ app()->getLocale() == 'ar' ? 'إجمالي المبيعات' : 'Total Sales' }}</th>
                                @elseif($type === 'fast-moving' || $type === 'slow-moving')
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'الكمية المباعة' : 'Qty Sold' }}</th>
                                    <th class="py-3 px-3 text-end pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'المخزون الحالي' : 'Current Stock' }}</th>
                                    <th class="py-3 px-4 text-end">{{ app()->getLocale() == 'ar' ? 'حالة الحركة' : 'Status' }}</th>
                                @elseif($type === 'low-stock' || $type === 'out-of-stock' || $type === 'overstock' || $type === 'healthy-stock')
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'المخزون الحالي' : 'Current Stock' }}</th>
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'الحد الأدنى' : 'Min Stock' }}</th>
                                    @if($type === 'overstock')
                                        <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(3)">{{ app()->getLocale() == 'ar' ? 'الموصى به' : 'Rec Stock' }}</th>
                                    @endif
                                    <th class="py-3 px-4 text-end">{{ app()->getLocale() == 'ar' ? 'حالة المخزون' : 'Status' }}</th>
                                @elseif($type === 'expiring-soon')
                                    <th class="py-3 px-3 pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'رقم الدفعة' : 'Batch No' }}</th>
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(3)">{{ app()->getLocale() == 'ar' ? 'الكمية المتبقية' : 'Remaining' }}</th>
                                    <th class="py-3 px-4 text-end pointer-cursor" onclick="sortDetailedTable(4)">{{ app()->getLocale() == 'ar' ? 'الأيام المتبقية' : 'Days Left' }}</th>
                                @elseif($type === 'expired')
                                    <th class="py-3 px-3 pointer-cursor" onclick="sortDetailedTable(1)">{{ app()->getLocale() == 'ar' ? 'رقم الدفعة' : 'Batch No' }}</th>
                                    <th class="py-3 px-3 text-center pointer-cursor" onclick="sortDetailedTable(2)">{{ app()->getLocale() == 'ar' ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                                    <th class="py-3 px-4 text-end pointer-cursor" onclick="sortDetailedTable(3)">{{ app()->getLocale() == 'ar' ? 'الكمية التالفة' : 'Remaining' }}</th>
                                @endif
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                        <tr class="detailed-row" style="border-bottom: 1px solid var(--border-color);">
                            @if($type === 'top-customers')
                                <td class="py-3 px-4 fw-semibold small">{{ $row->customer_name ?? $row->name }}</td>
                                <td class="py-3 px-3 text-center fw-bold text-primary small">{{ number_format($row->invoices_count, 0) }}</td>
                                <td class="py-3 px-3 text-end fw-semibold text-info small">{{ number_format($row->average_order_value, 2) }}</td>
                                <td class="py-3 px-4 text-end fw-bold text-success small" data-sort-value="{{ $row->total_spent }}">{{ number_format($row->total_spent, 2) }} {{ $setting->currency }}</td>
                            @elseif($type === 'suppliers')
                                <td class="py-3 px-4 fw-semibold small">{{ $row->name }}</td>
                                <td class="py-3 px-3 text-center fw-bold text-info small">{{ number_format($row->orders_count, 0) }}</td>
                                <td class="py-3 px-3 text-end fw-bold text-success small" data-sort-value="{{ $row->total_amount }}">{{ number_format($row->total_amount, 2) }} {{ $setting->currency }}</td>
                                <td class="py-3 px-4 text-end small"><span class="badge bg-light text-dark border px-2 py-1">{{ $row->percentage }}%</span></td>
                            @elseif($type === 'inventory-batches')
                                <td class="py-3 px-3 fw-bold text-dark font-monospace text-xs">{{ $row->batch_number }}</td>
                                <td class="py-3 px-2 d-flex align-items-center gap-2">
                                    @if(isset($row->product_image) && $row->product_image)
                                        <img src="{{ asset('storage/' . $row->product_image) }}" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded-2 d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;">
                                            <i class="bi bi-box" style="font-size: 0.8rem;"></i>
                                        </div>
                                    @endif
                                    <span class="fw-semibold small text-primary">{{ $row->product_name ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-2 small">{{ $row->supplier_name ?? '-' }}</td>
                                <td class="py-3 px-2 text-center small text-muted">{{ $row->purchase_date ? \Carbon\Carbon::parse($row->purchase_date)->format('Y-m-d') : '-' }}</td>
                                <td class="py-3 px-2 text-center small text-muted">{{ $row->expiry_date ?: '-' }}</td>
                                <td class="py-3 px-2 text-center fw-bold text-success font-monospace small" data-sort-value="{{ $row->remaining_quantity }}">{{ number_format($row->remaining_quantity, 0) }}</td>
                                <td class="py-3 px-2 text-end fw-semibold font-monospace small" data-sort-value="{{ $row->purchase_price }}">{{ number_format($row->purchase_price, 2) }}</td>
                                <td class="py-3 px-3 text-center">
                                    @if($row->remaining_quantity <= 0)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'مكتمل' : 'Completed' }}</span>
                                    @elseif($row->expiry_date && \Carbon\Carbon::parse($row->expiry_date)->isPast())
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'منتهي الصلاحية' : 'Expired' }}</span>
                                    @elseif($row->expiry_date && \Carbon\Carbon::parse($row->expiry_date)->diffInDays(now()) < 30)
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'قريب الانتهاء' : 'Nearly Expired' }}</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'نشط' : 'Active' }}</span>
                                    @endif
                                </td>
                            @elseif($type === 'inventory-transactions')
                                <td class="py-3 px-3 small text-muted">{{ $row->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3 px-3 d-flex align-items-center gap-2">
                                    @if(isset($row->product->image) && $row->product->image)
                                        <img src="{{ asset('storage/' . $row->product->image) }}" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded-2 d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;">
                                            <i class="bi bi-box" style="font-size: 0.8rem;"></i>
                                        </div>
                                    @endif
                                    <span class="fw-semibold small text-primary">{{ $row->product->name ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="badge {{ $row->quantity > 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} fw-bold text-xs px-2 py-1">
                                        {{ app()->getLocale() == 'ar' ? ($row->quantity > 0 ? 'توريد/زيادة' : 'صرف/نقصان') : ucfirst($row->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center fw-bold font-monospace text-xs" data-sort-value="{{ $row->quantity }}">{{ number_format($row->quantity, 0) }}</td>
                                <td class="py-3 px-3 text-muted small">{{ $row->movement_number }}</td>
                                <td class="py-3 px-4 text-end small">{{ $row->creator->full_name ?? '-' }}</td>
                            @else
                                <td class="py-3 px-4 d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-2 d-flex align-items-center justify-content-center text-muted" style="width: 36px; height: 36px; min-width: 36px; border: 1px solid var(--border-color) !important;">
                                        <i class="bi bi-box"></i>
                                    </div>
                                    @php
                                        $productName = in_array($type, ['expiring-soon', 'expired']) 
                                            ? ($row->product->name ?? '-') 
                                            : ($row->name ?? '-');
                                    @endphp
                                    <span class="fw-semibold small">{{ $productName }}</span>
                                </td>
                                
                                @if($type === 'top-selling')
                                    <td class="py-3 px-3 text-center fw-bold text-primary small" data-sort-value="{{ $row->total_quantity }}">{{ number_format($row->total_quantity, 0) }}</td>
                                    <td class="py-3 px-4 text-end fw-bold text-success small" data-sort-value="{{ $row->total_revenue }}">{{ number_format($row->total_revenue, 2) }} {{ $setting->currency }}</td>
                                @elseif($type === 'purchase-products')
                                    <td class="py-3 px-3 text-center fw-bold text-primary small" data-sort-value="{{ $row->total_quantity }}">{{ number_format($row->total_quantity, 0) }}</td>
                                    <td class="py-3 px-4 text-end fw-bold text-success small" data-sort-value="{{ $row->total_amount }}">{{ number_format($row->total_amount, 2) }} {{ $setting->currency }}</td>
                                @elseif($type === 'top-profitable' || $type === 'least-profitable')
                                    <td class="py-3 px-3 text-center fw-bold text-success small" data-sort-value="{{ $row->total_profit }}">{{ number_format($row->total_profit, 2) }} {{ $setting->currency }}</td>
                                    <td class="py-3 px-3 text-center small"><span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $row->profit_margin }}%</span></td>
                                    <td class="py-3 px-4 text-end fw-semibold small" data-sort-value="{{ $row->total_revenue }}">{{ number_format($row->total_revenue, 2) }} {{ $setting->currency }}</td>
                                @elseif($type === 'fast-moving' || $type === 'slow-moving')
                                    <td class="py-3 px-3 text-center fw-bold text-primary small" data-sort-value="{{ $row->total_quantity }}">{{ number_format($row->total_quantity, 0) }}</td>
                                    <td class="py-3 px-3 text-end fw-semibold small" data-sort-value="{{ $row->current_stock }}">{{ number_format($row->current_stock, 0) }}</td>
                                    <td class="py-3 px-4 text-end">
                                        <span class="badge bg-opacity-10 text-{{ $row->total_quantity >= 15 ? 'success' : 'warning' }} fw-bold text-{{ $row->total_quantity >= 15 ? 'success' : 'warning' }} small">
                                            {{ $row->movement_status }}
                                        </span>
                                    </td>
                                @elseif($type === 'low-stock' || $type === 'out-of-stock' || $type === 'overstock' || $type === 'healthy-stock')
                                    <td class="py-3 px-3 text-center fw-bold text-primary small" data-sort-value="{{ $row->current_stock }}">{{ number_format($row->current_stock, 0) }}</td>
                                    <td class="py-3 px-3 text-center small">{{ number_format($row->minimum_stock, 0) }}</td>
                                    @if($type === 'overstock')
                                        <td class="py-3 px-3 text-center fw-semibold text-info small">{{ number_format($row->recommended_stock, 0) }}</td>
                                    @endif
                                    <td class="py-3 px-4 text-end">
                                        @if($row->current_stock <= 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'نفد من المخزون' : 'Out of Stock' }}</span>
                                        @elseif($row->current_stock <= $row->minimum_stock)
                                            <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'منخفض المخزون' : 'Low Stock' }}</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'مخزون صحي ومستقر' : 'Optimal Level' }}</span>
                                        @endif
                                    </td>
                                @elseif($type === 'expiring-soon')
                                    <td class="py-3 px-3 text-muted font-monospace text-xs">{{ $row->batch_number }}</td>
                                    <td class="py-3 px-3 text-center small">{{ $row->expiry_date }}</td>
                                    <td class="py-3 px-3 text-center text-info fw-bold font-monospace small" data-sort-value="{{ $row->quantity }}">{{ number_format($row->quantity, 0) }}</td>
                                    <td class="py-3 px-4 text-end" data-sort-value="{{ $row->days_remaining }}">
                                        <span class="badge bg-warning text-dark px-2 py-1 font-monospace">{{ $row->days_remaining }} {{ app()->getLocale() == 'ar' ? 'يوم' : 'Days' }}</span>
                                    </td>
                                @elseif($type === 'expired')
                                    <td class="py-3 px-3 text-muted font-monospace text-xs">{{ $row->batch_number }}</td>
                                    <td class="py-3 px-3 text-center text-danger fw-bold small">{{ $row->expiry_date }}</td>
                                    <td class="py-3 px-4 text-end text-danger font-monospace fw-bold small" data-sort-value="{{ $row->quantity }}">{{ number_format($row->quantity, 0) }}</td>
                                @endif
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات متاحة لهذا النطاق من التصفية' : 'No records available matching this criteria' }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Custom JS Client-side Pagination Footer -->
        <div class="card-footer bg-transparent border-0 py-3 px-4 detailed-pagination-footer">
            <span id="paginationInfo" class="text-muted small"></span>
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1" id="detailedTablePagination"></ul>
            </nav>
        </div>
    </div>
</div>

<style>
    .detailed-pagination-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    @media (max-width: 768px) {
        .detailed-pagination-footer {
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
            padding: 1.25rem 1rem !important;
        }
        .detailed-pagination-footer #paginationInfo {
            margin-bottom: 4px !important;
        }
        .detailed-pagination-footer nav {
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
        }
        .detailed-pagination-footer .pagination {
            justify-content: center !important;
            flex-wrap: wrap !important;
            gap: 4px !important;
        }
        .detailed-pagination-footer .page-item .page-link {
            min-width: 32px !important;
            height: 32px !important;
            font-size: 0.8rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
        }
    }

    .welcome-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        border-radius: 20px !important;
    }
    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.15;
        z-index: 0;
    }
    .blob-1 { width: 150px; height: 150px; background: #3b82f6; top: -20px; right: 10%; }
    .blob-2 { width: 200px; height: 200px; background: #10b981; bottom: -50px; left: 20%; }
</style>

@push('scripts')
<script>
    // Client-side pagination config
    const rowsPerPage = 15;
    let currentPage = 1;
    let filteredRows = [];

    document.addEventListener('DOMContentLoaded', function () {
        initializePagination();
    });

    function initializePagination() {
        const tbody = document.querySelector('#detailedReportTable tbody');
        filteredRows = Array.from(tbody.querySelectorAll('tr.detailed-row'));
        showPage(1);
    }

    function showPage(page) {
        currentPage = page;
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        
        filteredRows.forEach((row, index) => {
            const start = (page - 1) * rowsPerPage;
            const end = page * rowsPerPage;
            if (index >= start && index < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update pagination UI
        const info = document.getElementById('paginationInfo');
        if (totalRows === 0) {
            info.textContent = "{{ app()->getLocale() == 'ar' ? 'لا يوجد سجلات' : 'No records' }}";
        } else {
            const startIdx = (page - 1) * rowsPerPage + 1;
            const endIdx = Math.min(page * rowsPerPage, totalRows);
            const isAr = "{{ app()->getLocale() }}" === 'ar';
            if (isAr) {
                info.textContent = `عرض ${startIdx} إلى ${endIdx} من أصل ${totalRows} مدخلات`;
            } else {
                info.textContent = `Showing ${startIdx} to ${endIdx} of ${totalRows} entries`;
            }
        }

        const paginationUl = document.getElementById('detailedTablePagination');
        paginationUl.innerHTML = '';

        if (totalPages <= 1) return;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link rounded-2" href="#" onclick="showPage(${page - 1}); return false;">&laquo;</a>`;
        paginationUl.appendChild(prevLi);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === page ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link rounded-2" href="#" onclick="showPage(${i}); return false;">${i}</a>`;
            paginationUl.appendChild(li);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link rounded-2" href="#" onclick="showPage(${page + 1}); return false;">&raquo;</a>`;
        paginationUl.appendChild(nextLi);
    }

    // Client-side search filter
    function filterDetailedTable() {
        const query = document.getElementById('detailedReportSearch').value.toLowerCase().trim();
        const tbody = document.querySelector('#detailedReportTable tbody');
        const allRows = Array.from(tbody.querySelectorAll('tr.detailed-row'));

        filteredRows = allRows.filter(row => {
            return row.textContent.toLowerCase().includes(query);
        });

        allRows.forEach(r => r.style.display = 'none');
        showPage(1);
    }

    // Client-side sorting
    let sortDirections = {};
    function sortDetailedTable(colIndex) {
        const table = document.getElementById('detailedReportTable');
        const tbody = table.querySelector('tbody');
        const allRows = Array.from(tbody.querySelectorAll('tr.detailed-row'));

        sortDirections[colIndex] = !sortDirections[colIndex];
        const asc = sortDirections[colIndex];

        allRows.sort((a, b) => {
            const aCell = a.getElementsByTagName('td')[colIndex];
            const bCell = b.getElementsByTagName('td')[colIndex];
            let aVal = aCell.getAttribute('data-sort-value') || aCell.textContent.trim();
            let bVal = bCell.getAttribute('data-sort-value') || bCell.textContent.trim();

            if (!isNaN(parseFloat(aVal)) && !isNaN(parseFloat(bVal))) {
                return asc ? parseFloat(aVal) - parseFloat(bVal) : parseFloat(bVal) - parseFloat(aVal);
            }
            return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        });

        tbody.innerHTML = '';
        allRows.forEach(r => tbody.appendChild(r));
        initializePagination();
    }

    // Export Dynamic HTML Table to CSV
    function exportDetailedTable(format) {
        let csv = [];
        const table = document.getElementById('detailedReportTable');
        const rows = table.querySelectorAll('tr');
        
        rows.forEach(row => {
            let rowData = [];
            const cols = row.querySelectorAll('th, td');
            cols.forEach(col => {
                rowData.push(' juice ' + col.textContent.replace(/"/g, '""').trim() + ' juice ');
            });
            csv.push(rowData.join(','));
        });

        const csvContent = "data:text/csv;charset=utf-8,\uFEFF" + csv.join("\n");
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `detailed_report_${format}_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endpush
@endsection

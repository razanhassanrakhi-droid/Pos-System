@extends('layouts.app')

@section('title', __('pos.reports'))

@section('content')
<div class="container-fluid">
    <!-- Header Banner Section (Stunning Dark Indigo Hero Card) -->
    <div class="welcome-hero-card py-4 px-4 rounded-4 border-0 position-relative shadow-sm mb-4" style="overflow: visible !important;">
        <!-- Background wrapper for blobs that handles overflow clipping -->
        <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4 overflow-hidden" style="pointer-events: none; z-index: 0;">
            <!-- Glowing blobs in background -->
            <div class="hero-blob blob-1"></div>
            <div class="hero-blob blob-2"></div>
        </div>
        
        <div class="position-relative d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="z-index: 2;">
            <!-- Title and Subtitle -->
            <div>
                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                    <h4 class="fw-bold mb-0 text-white welcome-title tracking-tight fs-4">
                        {{ __('pos.reports') }}
                    </h4>
                </div>
                <p class="mb-0 welcome-subtitle" style="font-size: 0.85rem;">
                    {{ app()->getLocale() == 'ar' ? 'نظرة عامة على أداء أعمالك' : 'An overview of your business performance' }}
                </p>
            </div>
            
            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Filter Form containing Branch and Dates -->
                <form action="{{ route('reports.index') }}" method="GET" class="d-flex align-items-center flex-wrap gap-2 mb-0">
                    <!-- Branch selection dropdown inside the form -->
                    @if(auth()->user()->isAdmin())
                    <div>
                        <select name="branch_id" class="form-select form-select-sm py-2 px-3 rounded-3" style="background-color: rgba(255, 255, 255, 0.08) !important; color: #ffffff !important; border: 1.5px solid rgba(255, 255, 255, 0.2) !important; height: 42px; font-size: 0.85rem; border-radius: 12px !important;" onchange="this.form.submit()">
                            <option value="all" style="background-color: #0f172a;" {{ is_null($filters['branch_id']) ? 'selected' : '' }}>{{ __('pos.all_branches') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" style="background-color: #0f172a;" {{ $filters['branch_id'] == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->getTranslation('name') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <!-- Date Pickers in inline layout -->
                    <div class="d-flex align-items-center gap-2 px-3 py-1 rounded-3 border" style="border: 1.5px solid rgba(255, 255, 255, 0.2) !important; background-color: rgba(255, 255, 255, 0.08) !important; height: 42px; border-radius: 12px !important;">
                        <input type="date" name="from_date" class="form-control form-control-sm border-0 p-0 {{ app()->getLocale() == 'ar' ? 'text-center' : 'text-start' }}" style="background: transparent; color: #ffffff !important; width: 130px; font-size: 0.85rem; color-scheme: dark;" value="{{ $filters['from_date'] }}">
                        <span class="text-white-50 px-1">-</span>
                        <input type="date" name="to_date" class="form-control form-control-sm border-0 p-0 {{ app()->getLocale() == 'ar' ? 'text-center' : 'text-start' }}" style="background: transparent; color: #ffffff !important; width: 130px; font-size: 0.85rem; color-scheme: dark;" value="{{ $filters['to_date'] }}">
                    </div>

                    <button type="submit" class="btn px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" style="background-color: rgba(255, 255, 255, 0.08) !important; color: #ffffff !important; border: 1.5px solid rgba(255, 255, 255, 0.2) !important; height: 42px; width: 42px; border-radius: 12px !important;">
                        <i class="bi bi-funnel text-white"></i>
                    </button>
                </form>

                <!-- Export dropdown next to the form -->
                <div class="dropdown">
                    <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold dropdown-toggle" type="button" id="exportReportDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="height: 42px; border-radius: 12px !important;">
                        <span>{{ app()->getLocale() == 'ar' ? 'تصدير التقرير' : 'Export Report' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3" aria-labelledby="exportReportDropdown">
                        <li>
                            <a id="export-pdf-btn" href="#" class="dropdown-item py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                                <span>{{ app()->getLocale() == 'ar' ? 'تصدير PDF' : 'Export PDF' }}</span>
                            </a>
                        </li>
                        <li>
                            <a id="export-excel-btn" href="#" class="dropdown-item py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-excel text-success fs-5"></i>
                                <span>{{ app()->getLocale() == 'ar' ? 'تصدير Excel' : 'Export Excel' }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- main Tabs Navigation -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
        <div class="card-body p-2">
            <ul class="nav nav-pills justify-content-between text-center border-0 flex-nowrap overflow-auto py-1 px-1 custom-report-tabs" id="reportTabs" role="tablist" style="scrollbar-width: none; gap: 8px;">
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center gap-2 active" data-bs-toggle="tab" data-bs-target="#sales-report" type="button" role="tab">
                        <div class="tab-icon-wrapper d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-cart3 fs-4"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.8rem;">{{ __('pos.sales_report') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center gap-2" data-bs-toggle="tab" data-bs-target="#purchase-report" type="button" role="tab">
                        <div class="tab-icon-wrapper d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-cart-plus fs-4"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.8rem;">{{ __('pos.purchase_report') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center gap-2" data-bs-toggle="tab" data-bs-target="#inventory-report" type="button" role="tab">
                        <div class="tab-icon-wrapper d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.8rem;">{{ __('pos.inventory_report') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center gap-2" data-bs-toggle="tab" data-bs-target="#customer-report" type="button" role="tab">
                        <div class="tab-icon-wrapper d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.8rem;">{{ __('pos.customer_report') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center gap-2" data-bs-toggle="tab" data-bs-target="#supplier-report" type="button" role="tab">
                        <div class="tab-icon-wrapper d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-truck fs-4"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.8rem;">{{ __('pos.supplier_report') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center gap-2" data-bs-toggle="tab" data-bs-target="#expenses-report" type="button" role="tab">
                        <div class="tab-icon-wrapper d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-file-earmark-text fs-4"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.8rem;">{{ __('pos.expenses_report') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center gap-2" data-bs-toggle="tab" data-bs-target="#financial-report" type="button" role="tab">
                        <div class="tab-icon-wrapper d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-bank fs-4"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.8rem;">{{ __('pos.financial_report') }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center gap-2" data-bs-toggle="tab" data-bs-target="#vat-report" type="button" role="tab">
                        <div class="tab-icon-wrapper d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-percent fs-4"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.8rem;">{{ __('pos.vat_report') }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tabs Content -->
    <div class="tab-content border-0 mb-5" id="reportTabsContent">
        <!-- 1. SALES REPORT TAB (Stunning 2026 SaaS Analytics Redesign) -->
        <div class="tab-pane fade show active" id="sales-report">
            
            <!-- SECTION 0: KPI Summary Cards Grid -->
            <div class="row g-3 mb-4 kpi-carousel-wrapper">
                @php
                    $kpiDetails = [
                        'total_sales' => ['icon' => 'cart-check', 'color' => '#1e293b', 'desc' => 'Total sales before returned goods'],
                        'net_sales' => ['icon' => 'wallet2', 'color' => '#0ea5e9', 'desc' => 'Total sales minus sales returns amount'],
                        'gross_profit' => ['icon' => 'piggy-bank', 'color' => '#10b981', 'desc' => 'Net sales minus cost of goods sold'],
                        'total_orders' => ['icon' => 'receipt', 'color' => '#06b6d4', 'desc' => 'Total number of completed sales invoices'],
                        'avg_order_value' => ['icon' => 'calculator', 'color' => '#6366f1', 'desc' => 'Net sales divided by number of orders'],
                        'total_items_sold' => ['icon' => 'box', 'color' => '#f59e0b', 'desc' => 'Total unit items sold in base quantities'],
                        'returns_amount' => ['icon' => 'arrow-counterclockwise', 'color' => '#ef4444', 'desc' => 'Total sales returns amount and invoice count'],
                        'total_discount' => ['icon' => 'percent', 'color' => '#f43f5e', 'desc' => 'Total discounts applied to sales invoices'],
                        'total_tax' => ['icon' => 'shield-check', 'color' => '#0d9488', 'desc' => 'Total VAT collected from invoices'],
                        'total_cogs' => ['icon' => 'truck', 'color' => '#1e3a8a', 'desc' => 'Total cost of goods sold from corresponding batches'],
                        'profit_margin' => ['icon' => 'graph-up-arrow', 'color' => '#059669', 'desc' => 'Gross profit divided by net sales as a percentage'],
                        'total_paid' => ['icon' => 'cash-coin', 'color' => '#10b981', 'desc' => 'Total paid amount from sales'],
                        'total_remaining' => ['icon' => 'hourglass-split', 'color' => '#ef4444', 'desc' => 'Total remaining balance from sales'],
                    ];
                @endphp

                @foreach($salesKPIs as $key => $kpi)
                    @if(!in_array($key, ['total_cogs', 'profit_margin', 'total_items_sold', 'avg_order_value', 'best_category']))
                    <div class="col-6 col-md-4 col-xl-3 kpi-card-col">
                        <div class="card border-0 rounded-3 shadow-sm h-100 py-2 px-3 d-flex flex-row align-items-center gap-2" style="background-color: var(--card-bg); border-left: 3.5px solid {{ $kpiDetails[$key]['color'] }} !important; min-height: 72px;" data-bs-toggle="tooltip" title="{{ $kpiDetails[$key]['desc'] }}">
                            <!-- Circular Icon Wrapper -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--bs-light); border-color: var(--border-color) !important;">
                                <i class="bi bi-{{ $kpiDetails[$key]['icon'] }}" style="color: {{ $kpiDetails[$key]['color'] }}; font-size: 0.95rem;"></i>
                            </div>
                            
                            <!-- KPI Content -->
                            <div class="d-flex flex-column justify-content-center text-start">
                                <span class="text-muted text-uppercase fw-bold text-truncate" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    {{ app()->getLocale() == 'ar' ? __("pos.{$key}") ?? $kpi['title'] : $kpi['title'] }}
                                </span>
                                <div class="d-flex align-items-baseline gap-1 mt-0">
                                    <span class="fw-bold mb-0 text-dark" style="font-size: 1rem; line-height: 1.2;">
                                        @if($key === 'profit_margin')
                                            {{ number_format($kpi['value'], 2) }}%
                                        @elseif(in_array($key, ['total_sales', 'net_sales', 'gross_profit', 'returns_amount', 'total_discount', 'total_tax', 'total_cogs', 'avg_order_value', 'total_paid', 'total_remaining']))
                                            {{ number_format($kpi['value'], 2) }}
                                        @else
                                            {{ floor($kpi['value']) == $kpi['value'] ? number_format($kpi['value'], 0) : number_format($kpi['value'], 2) }}
                                        @endif
                                    </span>
                                    @if($key !== 'profit_margin' && $key !== 'total_orders' && $key !== 'total_items_sold')
                                        <span class="text-muted fw-semibold ms-1" style="font-size: 0.65rem;">{{ $setting->currency }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- SECTION 1: Sales Trend -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-color);">
                            <i class="bi bi-graph-up text-primary me-2"></i>{{ __('pos.sales_trend') }}
                        </h5>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'تحليل ومتابعة المبيعات عبر الفترات الزمنية' : 'Visualize and track sales trend over different periods' }}</p>
                    </div>
                    <!-- Time Granularity Switcher -->
                    <div class="btn-group btn-group-sm rounded-3 overflow-hidden p-1 bg-light border" style="background-color: rgba(0,0,0,0.03) !important; flex-wrap: nowrap !important;">
                        <button type="button" class="btn btn-sm px-2 px-sm-3 rounded-2 fw-semibold active" onclick="switchTrendPeriod('daily', this)">{{ app()->getLocale() == 'ar' ? 'يومي' : 'Daily' }}</button>
                        <button type="button" class="btn btn-sm px-2 px-sm-3 rounded-2 fw-semibold text-muted" onclick="switchTrendPeriod('weekly', this)">{{ app()->getLocale() == 'ar' ? 'أسبوعي' : 'Weekly' }}</button>
                        <button type="button" class="btn btn-sm px-2 px-sm-3 rounded-2 fw-semibold text-muted" onclick="switchTrendPeriod('monthly', this)">{{ app()->getLocale() == 'ar' ? 'شهري' : 'Monthly' }}</button>
                        <button type="button" class="btn btn-sm px-2 px-sm-3 rounded-2 fw-semibold text-muted" onclick="switchTrendPeriod('yearly', this)">{{ app()->getLocale() == 'ar' ? 'سنوي' : 'Yearly' }}</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-scroll-wrapper" style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <div class="chart-inner-container" style="position: relative; height: 350px; width: 100%;">
                            <canvas id="interactiveSalesTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Product Performance -->
            <div class="row g-4 mb-4">
                <!-- A. Top Selling Products -->
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-1" style="color: var(--text-color);">
                                    <i class="bi bi-star-fill text-warning me-2"></i>{{ __('pos.top_selling_products') }}
                                </h6>
                                <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'المنتجات الأكثر طلباً من حيث الكمية المباعة' : 'Most ordered items based on quantity sold' }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('reports.detailed', ['type' => 'top-selling'] + $filters) }}" class="btn btn-link p-0 text-decoration-none text-xs fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل ←' : 'View All →' }}</a>
                                <select onchange="changeTopAnalyticsLimit('top-selling', this.value, this)" class="form-select form-select-sm py-0 px-1 rounded-2 text-xs" style="width: 55px; height: 22px;">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ app()->getLocale() == 'ar' ? 'الكل' : 'All' }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table align-middle table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                            <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                            <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'الكمية' : 'Qty' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'المبيعات' : 'Sales' }}</th>
                                            <th class="pb-2 text-end" style="width: 80px;">{{ app()->getLocale() == 'ar' ? 'نسبة المشتريات (%)' : 'Purchase Share (%)' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $maxQty = $salesReport['top_products']->max('total_quantity') ?: 1; @endphp
                                        @forelse($salesReport['top_products'] as $product)
                                        <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                            <td class="py-2 d-flex align-items-center gap-2">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded-2 d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-image" style="font-size: 0.8rem;"></i>
                                                    </div>
                                                @endif
                                                <span class="fw-semibold small" style="white-space: normal; word-break: break-word; line-height: 1.3;">{{ $product->name }}</span>
                                            </td>
                                            <td class="py-2 text-center fw-bold text-primary small">{{ number_format($product->total_quantity, 0) }}</td>
                                            <td class="py-2 text-end fw-semibold small text-success">{{ number_format($product->total_revenue, 2) }}</td>
                                            <td class="py-2">
                                                <div class="progress rounded-pill" style="height: 5px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($product->total_quantity / $maxQty) * 100 }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B. Top Profitable Products -->
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-1" style="color: var(--text-color);">
                                    <i class="bi bi-graph-up-arrow text-success me-2"></i>{{ app()->getLocale() == 'ar' ? 'المنتجات الأكثر ربحية' : 'Top Profitable Products' }}
                                </h6>
                                <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'المنتجات التي حققت أعلى صافي ربح' : 'Products contributing highest net profits' }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('reports.detailed', ['type' => 'top-profitable'] + $filters) }}" class="btn btn-link p-0 text-decoration-none text-xs fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل ←' : 'View All →' }}</a>
                                <select onchange="changeTopAnalyticsLimit('top-profitable', this.value, this)" class="form-select form-select-sm py-0 px-1 rounded-2 text-xs" style="width: 55px; height: 22px;">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ app()->getLocale() == 'ar' ? 'الكل' : 'All' }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table align-middle table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                            <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                            <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'الربح' : 'Profit' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'هامش الربح (%)' : 'Profit Margin (%)' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'المبيعات' : 'Sales' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salesReport['top_profitable_products'] as $product)
                                        <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                            <td class="py-2 d-flex align-items-center gap-2">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded-2 d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-image" style="font-size: 0.8rem;"></i>
                                                    </div>
                                                @endif
                                                <span class="fw-semibold small" style="white-space: normal; word-break: break-word; line-height: 1.3;">{{ $product->name }}</span>
                                            </td>
                                            <td class="py-2 text-center fw-bold text-success small">{{ number_format($product->total_profit, 2) }}</td>
                                            <td class="py-2 text-end"><span class="badge bg-success bg-opacity-10 text-success fw-bold small">{{ $product->profit_margin }}%</span></td>
                                            <td class="py-2 text-end fw-semibold small">{{ number_format($product->total_revenue, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- C. Least Profitable Products -->
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-1" style="color: var(--text-color);">
                                    <i class="bi bi-graph-down-arrow text-danger me-2"></i>{{ app()->getLocale() == 'ar' ? 'المنتجات الأقل ربحية' : 'Least Profitable Products' }}
                                </h6>
                                <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'المنتجات ذات هوامش الربح المنخفضة' : 'Products with lowest profit margins' }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('reports.detailed', ['type' => 'least-profitable'] + $filters) }}" class="btn btn-link p-0 text-decoration-none text-xs fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل ←' : 'View All →' }}</a>
                                <select onchange="changeTopAnalyticsLimit('least-profitable', this.value, this)" class="form-select form-select-sm py-0 px-1 rounded-2 text-xs" style="width: 55px; height: 22px;">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ app()->getLocale() == 'ar' ? 'الكل' : 'All' }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table align-middle table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                            <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                            <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'الربح' : 'Profit' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'هامش الربح (%)' : 'Profit Margin (%)' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'المبيعات' : 'Sales' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salesReport['least_profitable_products'] as $product)
                                        <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                            <td class="py-2 d-flex align-items-center gap-2">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded-2 d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-image" style="font-size: 0.8rem;"></i>
                                                    </div>
                                                @endif
                                                <span class="fw-semibold small" style="white-space: normal; word-break: break-word; line-height: 1.3;">{{ $product->name }}</span>
                                            </td>
                                            <td class="py-2 text-center fw-bold text-danger small">{{ number_format($product->total_profit, 2) }}</td>
                                            <td class="py-2 text-end"><span class="badge bg-danger bg-opacity-10 text-danger fw-bold small">{{ $product->profit_margin }}%</span></td>
                                            <td class="py-2 text-end fw-semibold small">{{ number_format($product->total_revenue, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- D. Fast / Slow Moving Products -->
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-1" style="color: var(--text-color);">
                                    <i class="bi bi-activity text-info me-2"></i>{{ app()->getLocale() == 'ar' ? 'حركة المنتجات' : 'Product Movement' }}
                                </h6>
                                <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'معدل دوران الأصناف في المخزن' : 'Stock rotation and item movement status' }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a id="productMovementViewAllLink" href="{{ route('reports.detailed', ['type' => 'fast-moving'] + $filters) }}" class="btn btn-link p-0 text-decoration-none text-xs fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل ←' : 'View All →' }}</a>
                                <select onchange="changeTopAnalyticsLimit('movement', this.value, this)" class="form-select form-select-sm py-0 px-1 rounded-2 text-xs" style="width: 55px; height: 22px;">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ app()->getLocale() == 'ar' ? 'الكل' : 'All' }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <!-- Tabs -->
                            <ul class="nav nav-tabs border-bottom mb-3" id="movementSpeedTabs" role="tablist" style="border-color: var(--border-color) !important;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold small pb-2 border-0 bg-transparent" id="fast-moving-tab" data-bs-toggle="tab" data-bs-target="#fast-moving" type="button" role="tab">
                                        {{ app()->getLocale() == 'ar' ? 'سريع الحركة' : 'Fast Moving' }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold small pb-2 border-0 bg-transparent" id="slow-moving-tab" data-bs-toggle="tab" data-bs-target="#slow-moving" type="button" role="tab">
                                        {{ app()->getLocale() == 'ar' ? 'بطيء الحركة' : 'Slow Moving' }}
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="movementTabsContent">
                                <div class="tab-pane fade show active" id="fast-moving" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-borderless mb-0">
                                            <thead>
                                                <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                                    <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                                    <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'المبيعات' : 'Qty' }}</th>
                                                    <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'المخزون' : 'Stock' }}</th>
                                                    <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($salesReport['fast_moving_products'] as $product)
                                                <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                                    <td class="py-2 fw-semibold small">{{ $product->name }}</td>
                                                    <td class="py-2 text-center fw-bold small text-primary">{{ number_format($product->total_quantity, 0) }}</td>
                                                    <td class="py-2 text-end fw-semibold small">{{ number_format($product->current_stock, 0) }}</td>
                                                    <td class="py-2 text-end"><span class="badge bg-success bg-opacity-10 text-success fw-bold small">{{ $product->movement_status }}</span></td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="slow-moving" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-borderless mb-0">
                                            <thead>
                                                <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                                    <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                                    <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'المبيعات' : 'Qty' }}</th>
                                                    <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'المخزون' : 'Stock' }}</th>
                                                    <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($salesReport['slow_moving_products'] as $product)
                                                <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                                    <td class="py-2 fw-semibold small">{{ $product->name }}</td>
                                                    <td class="py-2 text-center fw-bold small text-warning">{{ number_format($product->total_quantity, 0) }}</td>
                                                    <td class="py-2 text-end fw-semibold small">{{ number_format($product->current_stock, 0) }}</td>
                                                    <td class="py-2 text-end"><span class="badge bg-warning bg-opacity-10 text-warning fw-bold small">{{ $product->movement_status }}</span></td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Sales Distribution -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-1" style="color: var(--text-color);">
                        <i class="bi bi-pie-chart text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'توزيع المبيعات' : 'Sales Distribution' }}
                    </h5>
                    <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'تحليل نسب وحصص المبيعات' : 'Analyze revenue shares in detail' }}</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-4">
                        <div class="col-lg-4 text-center">
                            <h6 class="fw-bold mb-3 small text-muted">{{ app()->getLocale() == 'ar' ? 'المبيعات حسب التصنيف' : 'Sales by Category' }}</h6>
                            <div style="position: relative; height: 200px;"><canvas id="distributionCategoryChart"></canvas></div>
                        </div>
                        <div class="col-lg-4 text-center">
                            <h6 class="fw-bold mb-3 small text-muted">{{ app()->getLocale() == 'ar' ? 'طرق الدفع' : 'Sales by Payment Method' }}</h6>
                            <div style="position: relative; height: 200px;"><canvas id="distributionPaymentChart"></canvas></div>
                        </div>
                        <div class="col-lg-4 text-center">
                            <h6 class="fw-bold mb-3 small text-muted">{{ app()->getLocale() == 'ar' ? 'المبيعات حسب الفروع' : 'Sales by Branch' }}</h6>
                            <div style="position: relative; height: 200px;"><canvas id="distributionBranchChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: Customer Analytics -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-color);">
                            <i class="bi bi-people text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'تحليلات العملاء' : 'Customer Analytics' }}
                        </h5>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'العملاء الأكثر شراءً وتكراراً للطلبات' : 'Insights about top customers spending patterns and visit frequencies' }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('reports.detailed', ['type' => 'top-customers'] + $filters) }}" class="btn btn-link p-0 text-decoration-none text-xs fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل ←' : 'View All →' }}</a>
                        <select onchange="changeTopAnalyticsLimit('top-customers', this.value, this)" class="form-select form-select-sm py-0 px-1 rounded-2 text-xs" style="width: 55px; height: 22px;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="all">{{ app()->getLocale() == 'ar' ? 'الكل' : 'All' }}</option>
                        </select>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-borderless mb-0">
                            <thead>
                                <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                    <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'اسم العميل' : 'Customer Name' }}</th>
                                    <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'عدد الطلبات' : 'Invoices Count' }}</th>
                                    <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'متوسط قيمة الطلب' : 'Average Order' }}</th>
                                    <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'إجمالي المشتريات' : 'Sales Amount' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesReport['customer_analytics'] as $cust)
                                <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                    <td class="py-2 fw-semibold small">{{ $cust->customer_name }}</td>
                                    <td class="py-2 text-center fw-bold text-primary small">{{ number_format($cust->invoices_count, 0) }}</td>
                                    <td class="py-2 text-center fw-semibold text-info small">{{ number_format($cust->average_order_value, 2) }}</td>
                                    <td class="py-2 text-end fw-bold text-success small">{{ number_format($cust->total_spent, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: Sales History -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-color);">
                            <i class="bi bi-clock-history text-primary me-2"></i>{{ __('pos.sales_history') }}
                        </h5>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'سجل فواتير مبيعات فروعك بالتفصيل' : 'View, search, or filter all sales orders' }}</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="position-relative" style="min-width: 200px;">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y start-0 ms-2 text-muted" style="font-size: 0.8rem;"></i>
                            <input type="text" id="salesHistorySearch" class="form-control form-control-sm ps-4 rounded-3" placeholder="{{ app()->getLocale() == 'ar' ? 'البحث...' : 'Search...' }}" onkeyup="filterSalesHistoryTable()">
                        </div>
                        <select id="salesHistoryPaymentFilter" class="form-select form-select-sm rounded-3" style="width: 130px;" onchange="filterSalesHistoryTable()">
                            <option value="all">{{ app()->getLocale() == 'ar' ? 'طريقة الدفع' : 'Payment Method' }}</option>
                            @foreach($salesReport['invoices']->pluck('payment_method')->unique()->filter()->values() as $hp)
                                <option value="{{ $hp }}">{{ __('pos.' . $hp) ?? ucfirst($hp) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0 mt-2">
                    <div class="table-responsive">
                        <table id="salesHistoryTable" class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small" style="border-bottom: 2px solid var(--border-color);">
                                    <th class="py-3 px-3 pointer-cursor" onclick="sortSalesHistoryTable(0)">{{ __('pos.invoice_number') }}</th>
                                    <th class="py-3 px-2 pointer-cursor" onclick="sortSalesHistoryTable(1)">{{ __('pos.date') }}</th>
                                    <th class="py-3 px-2 pointer-cursor" onclick="sortSalesHistoryTable(2)">{{ __('pos.customer') }}</th>
                                    <th class="py-3 px-2 text-center">{{ app()->getLocale() == 'ar' ? 'القطع' : 'Items' }}</th>
                                    <th class="py-3 px-2 text-end">{{ app()->getLocale() == 'ar' ? 'الفرعي' : 'Subtotal' }}</th>
                                    <th class="py-3 px-2 text-end">{{ __('pos.discount') }}</th>
                                    <th class="py-3 px-2 text-end">{{ __('pos.vat') }}</th>
                                    <th class="py-3 px-2 text-end pointer-cursor" onclick="sortSalesHistoryTable(7)">{{ __('pos.net_total') }}</th>
                                    <th class="py-3 px-2 text-center">{{ __('pos.payment_method') }}</th>
                                    <th class="py-3 px-2 text-center">{{ __('pos.status') }}</th>
                                    <th class="py-3 px-2 text-center">{{ app()->getLocale() == 'ar' ? 'الفرع' : 'Branch' }}</th>
                                    <th class="py-3 px-3 text-center">{{ __('pos.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesReport['invoices'] as $invoice)
                                @php
                                    $remaining = $invoice->total - $invoice->paid_amount;
                                    $itemsCount = $invoice->items->sum('quantity');
                                    $branchName = $invoice->branch ? $invoice->branch->getTranslation('name') : '-';
                                @endphp
                                <tr class="sales-history-row" data-branch-id="{{ $invoice->branch_id ?? '' }}" data-payment-method="{{ $invoice->payment_method }}" style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-2 px-3"><span class="fw-bold text-primary">#{{ $invoice->invoice_number }}</span></td>
                                    <td class="py-2 px-2 small text-muted" data-sort-value="{{ $invoice->created_at->timestamp }}">{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-2 px-2 fw-semibold small">{{ $invoice->customer->name ?? __('pos.walk_in_customer') }}</td>
                                    <td class="py-2 px-2 text-center fw-bold small text-info">{{ number_format($itemsCount, 0) }}</td>
                                    <td class="py-2 px-2 text-end fw-semibold small">{{ number_format($invoice->subtotal, 2) }}</td>
                                    <td class="py-2 px-2 text-end fw-semibold small text-danger">{{ number_format($invoice->discount, 2) }}</td>
                                    <td class="py-2 px-2 text-end fw-semibold small text-info">{{ number_format($invoice->tax, 2) }}</td>
                                    <td class="py-2 px-2 text-end fw-bold text-dark small" data-sort-value="{{ $invoice->total }}">{{ number_format($invoice->total, 2) }}</td>
                                    <td class="py-2 px-2 text-center small"><span class="badge bg-light text-dark border px-2 py-1">{{ __('pos.' . $invoice->payment_method) ?? ucfirst($invoice->payment_method) }}</span></td>
                                    <td class="py-2 px-2 text-center">
                                        @if($remaining > 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'متبقي' : 'Due' }}</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">{{ __('pos.paid') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-2 text-center small text-muted fw-semibold">{{ $branchName }}</td>
                                    <td class="py-2 px-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="{{ route('sales.show', $invoice->id) }}" class="btn btn-xs btn-outline-primary px-2 py-1 rounded-2" title="{{ __('pos.view') }}"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('sales.print', $invoice->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary px-2 py-1 rounded-2" title="Print"><i class="bi bi-printer"></i></a>
                                            @if($invoice->status !== 'returned')
                                                <a href="{{ route('sales_returns.create', ['invoice_number' => $invoice->invoice_number]) }}" class="btn btn-xs btn-outline-danger px-2 py-1 rounded-2" title="{{ app()->getLocale() == 'ar' ? 'إرجاع' : 'Refund' }}"><i class="bi bi-arrow-counterclockwise"></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="12" class="text-center text-muted py-4 small">{{ app()->getLocale() == 'ar' ? 'لا توجد فواتير مبيعات' : 'No sales invoices found' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: Export & Actions Bottom Bar -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-body p-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-color);">{{ app()->getLocale() == 'ar' ? 'تصدير وحفظ التقرير الحالي' : 'Export and Save Current Report' }}</h6>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'حمل التقرير بصيغ مختلفة للتدقيق المالي' : 'Download report files in various formats for accounting & auditing' }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('reports.export', ['type' => 'sales', 'format' => 'pdf'] + $filters) }}" class="btn btn-outline-danger d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                        </a>
                        <a href="{{ route('reports.export', ['type' => 'sales', 'format' => 'excel'] + $filters) }}" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-file-earmark-excel-fill"></i> Excel
                        </a>

                        <button onclick="window.print()" class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-printer-fill"></i> {{ app()->getLocale() == 'ar' ? 'طباعة' : 'Print' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PURCHASE REPORT TAB -->
        <div class="tab-pane fade" id="purchase-report">
            <!-- SECTION 0: KPI Summary Cards Grid -->
            <div class="row g-3 mb-4 kpi-carousel-wrapper">
                @php
                    $purchaseKpiDetails = [
                        ['title' => __('pos.total_purchases'), 'value' => number_format($purchaseReport['total_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'cart-plus', 'color' => '#1e293b', 'desc' => 'Total purchases amount'],
                        ['title' => app()->getLocale() == 'ar' ? 'إجمالي قيمة المخزون' : 'Total Inventory Value', 'value' => number_format($purchaseReport['total_inventory_value'], 2), 'unit' => $setting->currency, 'icon' => 'safe2', 'color' => '#8b5cf6', 'desc' => 'Total inventory value based on purchase cost of remaining batches'],
                        ['title' => app()->getLocale() == 'ar' ? 'إجمالي كمية المخزون' : 'Total Inventory Quantity', 'value' => (floor($purchaseReport['total_inventory_qty']) == $purchaseReport['total_inventory_qty'] ? number_format($purchaseReport['total_inventory_qty'], 0) : number_format($purchaseReport['total_inventory_qty'], 2)), 'unit' => '', 'icon' => 'archive', 'color' => '#f43f5e', 'desc' => 'Total remaining quantity of all products across batches'],
                        ['title' => __('pos.total_purchased_qty'), 'value' => (floor($purchaseReport['total_qty']) == $purchaseReport['total_qty'] ? number_format($purchaseReport['total_qty'], 0) : number_format($purchaseReport['total_qty'], 2)), 'unit' => '', 'icon' => 'box-seam', 'color' => '#0ea5e9', 'desc' => 'Total quantity of items purchased'],
                        ['title' => __('pos.total_discounts'), 'value' => number_format($purchaseReport['total_discount'], 2), 'unit' => $setting->currency, 'icon' => 'tag', 'color' => '#f59e0b', 'desc' => 'Total discounts received'],
                        ['title' => __('pos.purchase_vat'), 'value' => number_format($purchaseReport['total_tax'], 2), 'unit' => $setting->currency, 'icon' => 'percent', 'color' => '#6366f1', 'desc' => 'Total VAT paid on purchases'],
                        ['title' => __('pos.total_purchase_orders'), 'value' => $purchaseReport['invoice_count'], 'unit' => '', 'icon' => 'receipt-cutoff', 'color' => '#06b6d4', 'desc' => 'Total purchase orders count'],
                        ['title' => __('pos.total_paid'), 'value' => number_format($purchaseReport['total_paid'], 2), 'unit' => $setting->currency, 'icon' => 'check2-circle', 'color' => '#10b981', 'desc' => 'Total amount paid'],
                        ['title' => __('pos.total_remaining'), 'value' => number_format($purchaseReport['total_remaining'], 2), 'unit' => $setting->currency, 'icon' => 'hourglass-split', 'color' => '#ef4444', 'desc' => 'Total remaining balance due']
                    ];
                @endphp

                @foreach($purchaseKpiDetails as $kpi)
                    <div class="col-6 col-md-4 col-xl-3 kpi-card-col">
                        <div class="card border-0 rounded-3 shadow-sm h-100 py-2 px-3 d-flex flex-row align-items-center gap-2" style="background-color: var(--card-bg); border-left: 3.5px solid {{ $kpi['color'] }} !important; min-height: 72px;" data-bs-toggle="tooltip" title="{{ $kpi['desc'] }}">
                            <!-- Circular Icon Wrapper -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--bs-light); border-color: var(--border-color) !important;">
                                <i class="bi bi-{{ $kpi['icon'] }}" style="color: {{ $kpi['color'] }}; font-size: 0.95rem;"></i>
                            </div>
                            
                            <!-- KPI Content -->
                            <div class="d-flex flex-column justify-content-center text-start">
                                <span class="text-muted text-uppercase fw-bold text-truncate" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    {{ $kpi['title'] }}
                                </span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fw-bold mb-0 text-dark" style="font-size: 1.1rem; line-height: 1.1; font-weight: 800 !important;">
                                        {{ $kpi['value'] }}
                                    </span>
                                    @if($kpi['unit'])
                                        <span class="text-muted fw-semibold ms-1" style="font-size: 0.65rem;">{{ $kpi['unit'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- SECTION 1: Purchase Trend Chart -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-color);">
                            <i class="bi bi-graph-up text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'اتجاهات المشتريات' : 'Purchase Trend' }}
                        </h5>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'مراقبة حجم وقيمة طلبات الشراء بمرور الوقت' : 'Monitor purchase volume and order count over time' }}</p>
                    </div>
                    <div class="btn-group btn-group-sm rounded-3 overflow-hidden shadow-sm" role="group" id="purchaseTrendPeriodGroup" style="flex-wrap: nowrap !important;">
                        <button type="button" class="btn btn-outline-primary active px-2 px-sm-3" onclick="switchPurchaseTrendPeriod('daily', this)">{{ app()->getLocale() == 'ar' ? 'يومي' : 'Daily' }}</button>
                        <button type="button" class="btn btn-outline-primary px-2 px-sm-3 text-muted" onclick="switchPurchaseTrendPeriod('weekly', this)">{{ app()->getLocale() == 'ar' ? 'أسبوعي' : 'Weekly' }}</button>
                        <button type="button" class="btn btn-outline-primary px-2 px-sm-3 text-muted" onclick="switchPurchaseTrendPeriod('monthly', this)">{{ app()->getLocale() == 'ar' ? 'شهري' : 'Monthly' }}</button>
                        <button type="button" class="btn btn-outline-primary px-2 px-sm-3 text-muted" onclick="switchPurchaseTrendPeriod('yearly', this)">{{ app()->getLocale() == 'ar' ? 'سنوي' : 'Yearly' }}</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-scroll-wrapper" style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <div class="chart-inner-container" style="height: 320px; position: relative; width: 100%;">
                            <canvas id="interactivePurchaseTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Supplier Analytics -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-people text-info me-2"></i>{{ app()->getLocale() == 'ar' ? 'أبرز الموردين' : 'Top Suppliers' }}
                            </h6>
                            <a href="{{ route('reports.detailed', ['type' => 'suppliers'] + $filters) }}" class="btn btn-link p-0 text-decoration-none text-xs fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل ←' : 'View All →' }}</a>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table align-middle table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                            <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'المورد' : 'Supplier Name' }}</th>
                                            <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'الطلبات' : 'Orders' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'المشتريات' : 'Amount' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'نسبة المشتريات (%)' : 'Purchase Share (%)' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchaseReport['top_suppliers'] as $sup)
                                        <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                            <td class="py-2 fw-semibold small">{{ $sup->name }}</td>
                                            <td class="py-2 text-center fw-bold text-info small">{{ $sup->orders_count }}</td>
                                            <td class="py-2 text-end fw-bold text-success small">{{ number_format($sup->total_amount, 2) }}</td>
                                            <td class="py-2 text-end small">
                                                <span class="badge bg-light text-dark border px-2">{{ $sup->percentage }}%</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-pie-chart text-warning me-2"></i>{{ app()->getLocale() == 'ar' ? 'توزيع المشتريات حسب الموردين' : 'Purchases by Supplier' }}
                            </h6>
                        </div>
                        <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                            <div style="height: 220px; width: 100%; position: relative;">
                                <canvas id="supplierShareChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Product Purchase Analytics -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-box-seam text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'أكثر المنتجات شراءً' : 'Top Purchased Products' }}
                            </h6>
                            <a href="{{ route('reports.detailed', ['type' => 'purchase-products'] + $filters) }}" class="btn btn-link p-0 text-decoration-none text-xs fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل ←' : 'View All →' }}</a>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table align-middle table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                            <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                            <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'الكمية المشحونة' : 'Qty' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'إجمالي التكلفة' : 'Cost Amount' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchaseReport['top_purchased_products'] as $tp)
                                        <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                            <td class="py-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($tp->image)
                                                        <img src="{{ asset('storage/' . $tp->image) }}" class="rounded-2" style="width: 28px; height: 28px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-2 bg-light d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                            <i class="bi bi-image text-muted" style="font-size: 0.8rem;"></i>
                                                        </div>
                                                    @endif
                                                    <span class="fw-semibold small" style="white-space: normal; word-break: break-word; line-height: 1.3;">{{ $tp->name }}</span>
                                                </div>
                                            </td>
                                            <td class="py-2 text-center fw-bold text-primary small">{{ number_format($tp->total_quantity, 0) }}</td>
                                            <td class="py-2 text-end fw-bold text-success small">{{ number_format($tp->total_amount, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-tag text-danger me-2"></i>{{ app()->getLocale() == 'ar' ? 'المنتجات الأعلى تكلفة' : 'Most Expensive Purchased Products' }}
                            </h6>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table align-middle table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom" style="border-color: var(--border-color) !important;">
                                            <th class="pb-2">{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                            <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'آخر سعر' : 'Latest Cost' }}</th>
                                            <th class="pb-2 text-center">{{ app()->getLocale() == 'ar' ? 'أعلى سعر' : 'Highest Cost' }}</th>
                                            <th class="pb-2 text-end">{{ app()->getLocale() == 'ar' ? 'المتوسط' : 'Avg Cost' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchaseReport['most_expensive_products'] as $ep)
                                        <tr class="border-bottom" style="border-color: var(--border-color) !important;">
                                            <td class="py-2 fw-semibold small">{{ $ep->name }}</td>
                                            <td class="py-2 text-center fw-bold text-dark small">{{ number_format($ep->latest_cost, 2) }}</td>
                                            <td class="py-2 text-center fw-bold text-danger small">{{ number_format($ep->max_cost, 2) }}</td>
                                            <td class="py-2 text-end fw-semibold text-muted small">{{ number_format($ep->avg_cost, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: Purchase Distribution -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-grid text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'حسب التصنيف' : 'Purchases by Category' }}
                            </h6>
                        </div>
                        <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                            <div style="height: 180px; width: 100%; position: relative;">
                                <canvas id="purchaseCategoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-wallet2 text-success me-2"></i>{{ app()->getLocale() == 'ar' ? 'طريقة الدفع' : 'Purchases by Payment Method' }}
                            </h6>
                        </div>
                        <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                            <div style="height: 180px; width: 100%; position: relative;">
                                <canvas id="purchasePaymentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-building text-info me-2"></i>{{ app()->getLocale() == 'ar' ? 'حسب الفروع' : 'Purchases by Branch' }}
                            </h6>
                        </div>
                        <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                            <div style="height: 180px; width: 100%; position: relative;">
                                <canvas id="purchaseBranchChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: Recent Purchases (Purchase History) -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-color);">
                            <i class="bi bi-clock-history text-success me-2"></i>{{ __('pos.purchase_history') }}
                        </h5>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'إدارة واستعراض وتصفية فواتير وطلبات المشتريات بالتفصيل' : 'View, search, or filter all purchase invoices' }}</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="position-relative" style="min-width: 200px;">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y start-0 ms-2 text-muted" style="font-size: 0.8rem;"></i>
                            <input type="text" id="purchaseHistorySearch" class="form-control form-control-sm ps-4 rounded-3" placeholder="{{ app()->getLocale() == 'ar' ? 'البحث...' : 'Search...' }}" onkeyup="filterPurchaseHistoryTable()">
                        </div>
                        <select id="purchaseHistoryPaymentFilter" class="form-select form-select-sm rounded-3" style="width: 130px;" onchange="filterPurchaseHistoryTable()">
                            <option value="all">{{ app()->getLocale() == 'ar' ? 'طريقة الدفع' : 'Payment Method' }}</option>
                            @foreach($purchaseReport['purchases']->pluck('payment_method')->unique()->filter()->values() as $hp)
                                @php
                                    $key = strtolower(str_replace(' ', '_', $hp));
                                    $translated = __("pos.{$key}");
                                    $hpLabel = ($translated === "pos.{$key}") ? $hp : $translated;
                                @endphp
                                <option value="{{ $hp }}">{{ $hpLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0 mt-2">
                    <div class="table-responsive">
                        <table id="purchaseHistoryTable" class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small" style="border-bottom: 2px solid var(--border-color);">
                                    <th class="py-3 px-3">{{ __('pos.invoice_number') }}</th>
                                    <th class="py-3 px-2">{{ __('pos.date') }}</th>
                                    <th class="py-3 px-2">{{ __('pos.supplier') }}</th>
                                    <th class="py-3 px-2 text-center">{{ app()->getLocale() == 'ar' ? 'الأصناف' : 'Items' }}</th>
                                    <th class="py-3 px-2 text-end">{{ app()->getLocale() == 'ar' ? 'الفرعي' : 'Subtotal' }}</th>
                                    <th class="py-3 px-2 text-end">{{ __('pos.discount') }}</th>
                                    <th class="py-3 px-2 text-end">{{ __('pos.purchase_vat') }}</th>
                                    <th class="py-3 px-2 text-end">{{ __('pos.total') }}</th>
                                    <th class="py-3 px-2 text-center">{{ __('pos.payment_method') }}</th>
                                    <th class="py-3 px-2 text-center">{{ __('pos.status') }}</th>
                                    <th class="py-3 px-3 text-center">{{ __('pos.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseReport['purchases'] as $p)
                                @php
                                    $pRemaining = $p->total_amount - $p->paid_amount;
                                    $pItemsCount = $p->items->sum('quantity');
                                @endphp
                                <tr class="purchase-history-row" data-payment-method="{{ $p->payment_method }}" style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-2 px-3 fw-bold text-primary">#{{ $p->invoice_number }}</td>
                                    <td class="py-2 px-2 small text-muted">{{ $p->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-2 px-2 fw-semibold small">{{ $p->supplier->name ?? '-' }}</td>
                                    <td class="py-2 px-2 text-center fw-bold small text-info">{{ number_format($pItemsCount, 0) }}</td>
                                    <td class="py-2 px-2 text-end fw-semibold small">{{ number_format($p->subtotal, 2) }}</td>
                                    <td class="py-2 px-2 text-end fw-semibold small text-danger">{{ number_format($p->discount, 2) }}</td>
                                    <td class="py-2 px-2 text-end fw-semibold small text-info">{{ number_format($p->tax_amount, 2) }}</td>
                                    <td class="py-2 px-2 text-end fw-bold text-dark small">{{ number_format($p->total_amount, 2) }}</td>
                                    <td class="py-2 px-2 text-center small">
                                        @php
                                            $key = strtolower(str_replace(' ', '_', $p->payment_method));
                                            $translated = __("pos.{$key}");
                                            $paymentMethodLabel = ($translated === "pos.{$key}") ? $p->payment_method : $translated;
                                        @endphp
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $paymentMethodLabel }}</span>
                                    </td>
                                    <td class="py-2 px-2 text-center">
                                        @if($pRemaining > 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'متبقي' : 'Due' }}</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">{{ __('pos.paid') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="{{ route('purchases.show', $p->id) }}" class="btn btn-xs btn-outline-primary px-2 py-1 rounded-2" title="{{ __('pos.view') }}"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('purchases.print', $p->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary px-2 py-1 rounded-2" title="Print"><i class="bi bi-printer"></i></a>
                                            <a href="{{ route('reports.export', ['type' => 'purchases', 'format' => 'pdf'] + $filters) }}" class="btn btn-xs btn-outline-danger px-2 py-1 rounded-2" title="Download PDF"><i class="bi bi-file-earmark-pdf"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="11" class="text-center text-muted py-4 small">{{ app()->getLocale() == 'ar' ? 'لا توجد فواتير مشتريات' : 'No purchase invoices found' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION 7: Export & Bottom Actions Bar -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-body p-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-color);">{{ app()->getLocale() == 'ar' ? 'تصدير وحفظ التقرير الحالي' : 'Export and Save Current Report' }}</h6>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'حمل التقرير بصيغ مختلفة للتدقيق المالي' : 'Download report files in various formats for accounting & auditing' }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('reports.export', ['type' => 'purchases', 'format' => 'pdf'] + $filters) }}" class="btn btn-outline-danger d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                        </a>
                        <a href="{{ route('reports.export', ['type' => 'purchases', 'format' => 'excel'] + $filters) }}" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-file-earmark-excel-fill"></i> Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-printer-fill"></i> {{ app()->getLocale() == 'ar' ? 'طباعة' : 'Print' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- 3. INVENTORY REPORT TAB -->
        <div class="tab-pane fade" id="inventory-report">
            <!-- SECTION 0: KPI Summary Cards Grid -->
            <div class="row g-3 mb-4 kpi-carousel-wrapper">
                @php
                    $inventoryKpiDetails = [
                        ['title' => __('pos.total_products'), 'value' => $inventoryReport['total_products'], 'unit' => '', 'icon' => 'box-seam', 'color' => '#1e293b', 'desc' => 'Total products count'],
                        ['title' => __('pos.total_inventory_value'), 'value' => number_format($inventoryReport['inventory_value'], 2), 'unit' => $setting->currency, 'icon' => 'currency-dollar', 'color' => '#10b981', 'desc' => 'Total inventory value based on cost'],
                        ['title' => __('pos.total_remaining_stock'), 'value' => number_format($inventoryReport['total_remaining_stock'], 0), 'unit' => __('pos.unit'), 'icon' => 'archive', 'color' => '#f59e0b', 'desc' => 'Total remaining stock quantity'],
                        ['title' => __('pos.turnover_rate'), 'value' => (floor($inventoryReport['turnover_rate']) == $inventoryReport['turnover_rate'] ? number_format($inventoryReport['turnover_rate'], 0) : number_format($inventoryReport['turnover_rate'], 2)), 'unit' => 'x', 'icon' => 'arrow-repeat', 'color' => '#3b82f6', 'desc' => 'Inventory turnover rate (Sold / Remaining)'],
                        ['title' => __('pos.low_stock_items'), 'value' => $inventoryReport['low_stock_count'], 'unit' => '', 'icon' => 'exclamation-triangle', 'color' => '#f43f5e', 'desc' => 'Items with low stock levels'],
                        ['title' => __('pos.out_of_stock'), 'value' => $inventoryReport['out_of_stock_count'], 'unit' => '', 'icon' => 'x-circle', 'color' => '#ef4444', 'desc' => 'Out of stock products'],
                        ['title' => __('pos.expired_products'), 'value' => $inventoryReport['expired_count'], 'unit' => '', 'icon' => 'calendar-x', 'color' => '#6b7280', 'desc' => 'Expired batches count']
                    ];
                @endphp

                @foreach($inventoryKpiDetails as $kpi)
                    <div class="col-6 col-md-4 col-xl-3 kpi-card-col">
                        <div class="card border-0 rounded-3 shadow-sm h-100 py-2 px-3 d-flex flex-row align-items-center gap-2" style="background-color: var(--card-bg); border-left: 3.5px solid {{ $kpi['color'] }} !important; min-height: 72px;" data-bs-toggle="tooltip" title="{{ $kpi['desc'] }}">
                            <!-- Circular Icon Wrapper -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--bs-light); border-color: var(--border-color) !important;">
                                <i class="bi bi-{{ $kpi['icon'] }}" style="color: {{ $kpi['color'] }}; font-size: 0.95rem;"></i>
                            </div>
                            
                            <!-- KPI Content -->
                            <div class="d-flex flex-column justify-content-center text-start">
                                <span class="text-muted text-uppercase fw-bold text-truncate" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    {{ $kpi['title'] }}
                                </span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fw-bold mb-0 text-dark" style="font-size: 1.1rem; line-height: 1.1; font-weight: 800 !important;">
                                        {{ $kpi['value'] }}
                                    </span>
                                    @if($kpi['unit'])
                                        <span class="text-muted fw-semibold ms-1" style="font-size: 0.65rem;">{{ $kpi['unit'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <!-- SECTION 2: Stock Status Analysis -->
            <h5 class="fw-bold mb-3 mt-4" style="color: var(--text-color);">
                <i class="bi bi-grid-3x3-gap text-success me-2"></i>{{ app()->getLocale() == 'ar' ? 'تحليل حالة المخزون' : 'Stock Status Analysis' }}
            </h5>
            <div class="row g-4 mb-4">
                <!-- A. Low Stock Products -->
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-xs text-muted text-uppercase">{{ app()->getLocale() == 'ar' ? 'أصناف منخفضة المخزون' : 'Low Stock Products' }}</span>
                            <a href="{{ route('reports.detailed', ['type' => 'low-stock'] + $filters) }}" class="text-decoration-none text-xs fw-semibold text-primary">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</a>
                        </div>
                        <div class="card-body px-4 pb-4 pt-2">
                            <div class="d-flex flex-column gap-3">
                                @forelse($inventoryReport['low_stock']->take(5) as $lp)
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($lp->image)
                                            <img src="{{ asset('storage/' . $lp->image) }}" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-2 d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;"><i class="bi bi-box small text-muted"></i></div>
                                        @endif
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-xs" style="color: var(--text-color, #0f172a); white-space: normal; word-break: break-word; max-width: 130px; line-height: 1.3;">{{ $lp->name }}</span>
                                            <span class="text-muted text-xxs">{{ app()->getLocale() == 'ar' ? 'الحد الأدنى' : 'Min' }}: {{ $lp->minimum_stock }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-warning text-dark font-monospace text-xs px-2 py-1">{{ number_format($lp->current_stock, 0) }}</span>
                                </div>
                                @empty
                                <div class="text-center text-muted text-xs py-4">{{ app()->getLocale() == 'ar' ? 'لا توجد أصناف منخفضة' : 'No low stock products' }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B. Out of Stock Products -->
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-xs text-muted text-uppercase">{{ app()->getLocale() == 'ar' ? 'نفد من المخزون' : 'Out of Stock' }}</span>
                            <a href="{{ route('reports.detailed', ['type' => 'out-of-stock'] + $filters) }}" class="text-decoration-none text-xs fw-semibold text-primary">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</a>
                        </div>
                        <div class="card-body px-4 pb-4 pt-2">
                            <div class="d-flex flex-column gap-3">
                                @forelse($inventoryReport['out_of_stock']->take(5) as $op)
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($op->image)
                                            <img src="{{ asset('storage/' . $op->image) }}" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-2 d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;"><i class="bi bi-box small text-muted"></i></div>
                                        @endif
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-xs" style="color: var(--text-color, #0f172a); white-space: normal; word-break: break-word; max-width: 130px; line-height: 1.3;">{{ $op->name }}</span>
                                            <span class="text-muted text-xxs">{{ $op->category->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger text-white font-monospace text-xs px-2 py-1">0</span>
                                </div>
                                @empty
                                <div class="text-center text-muted text-xs py-4">{{ app()->getLocale() == 'ar' ? 'مخزون ممتاز (لا يوجد نفاد)' : 'No out of stock items' }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- SECTION 4 & 5: Expiry Analysis & Category Distribution -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-calendar-x text-danger me-2"></i>{{ app()->getLocale() == 'ar' ? 'تحليل ومراقبة تواريخ انتهاء الصلاحية' : 'Expiry & Batches Monitoring' }}
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('reports.detailed', ['type' => 'expiring-soon'] + $filters) }}" class="text-decoration-none text-xs fw-semibold text-primary">{{ app()->getLocale() == 'ar' ? 'يوشك على الانتهاء' : 'Expiring Soon' }}</a>
                                <span class="text-muted">|</span>
                                <a href="{{ route('reports.detailed', ['type' => 'expired'] + $filters) }}" class="text-decoration-none text-xs fw-semibold text-danger">{{ app()->getLocale() == 'ar' ? 'منتهية الصلاحية' : 'Expired' }}</a>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <h6 class="text-danger fw-bold text-xs mb-3 text-uppercase"><i class="bi bi-clock text-danger me-1"></i>{{ app()->getLocale() == 'ar' ? 'دفعات تنتهي قريباً (30 يوم)' : 'Expiring Soon (30 Days)' }}</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'رقم الدفعة' : 'Batch No' }}</th>
                                            <th class="text-center">{{ app()->getLocale() == 'ar' ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                                            <th class="text-center">{{ app()->getLocale() == 'ar' ? 'الكمية المتبقية' : 'Remaining' }}</th>
                                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'الأيام المتبقية' : 'Days Left' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inventoryReport['expiring_soon']->take(3) as $es)
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td class="py-2 fw-semibold text-dark">{{ $es->product->name ?? '-' }}</td>
                                            <td class="py-2 text-muted font-monospace text-xs">{{ $es->batch_number }}</td>
                                            <td class="py-2 text-center small">{{ $es->expiry_date }}</td>
                                            <td class="py-2 text-center text-info fw-bold font-monospace">{{ number_format($es->quantity, 0) }}</td>
                                            <td class="py-2 text-end">
                                                <span class="badge bg-warning text-dark px-2 py-1 font-monospace">{{ $es->days_remaining }} {{ app()->getLocale() == 'ar' ? 'يوم' : 'Days' }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد دفعات منتهية قريباً' : 'No critical batches soon' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <h6 class="text-dark fw-bold text-xs mt-4 mb-3 text-uppercase"><i class="bi bi-trash-fill text-danger me-1"></i>{{ app()->getLocale() == 'ar' ? 'أحدث الدفعات منتهية الصلاحية' : 'Latest Expired Batches' }}</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'رقم الدفعة' : 'Batch No' }}</th>
                                            <th class="text-center">{{ app()->getLocale() == 'ar' ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'الكمية التالفة' : 'Remaining' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inventoryReport['expired_batches']->take(3) as $exb)
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td class="py-2 fw-bold text-danger">{{ $exb->product->name ?? '-' }}</td>
                                            <td class="py-2 text-muted font-monospace text-xs">{{ $exb->batch_number }}</td>
                                            <td class="py-2 text-center small text-danger fw-bold">{{ $exb->expiry_date }}</td>
                                            <td class="py-2 text-end text-danger font-monospace fw-bold">{{ number_format($exb->quantity, 0) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد دفعات منتهية الصلاحية' : 'No expired batches' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-pie-chart text-info me-2"></i>{{ app()->getLocale() == 'ar' ? 'حالة سلامة المخزون' : 'Stock Health Status' }}
                            </h6>
                        </div>
                        <div class="card-body px-4 pb-4 d-flex flex-column align-items-center justify-content-center">
                            <div style="height: 180px; width: 100%; position: relative;" class="mb-4">
                                <canvas id="inventoryCategoryChart"></canvas>
                            </div>
                            <div class="w-100 border-top pt-3">
                                <div class="d-flex justify-content-between text-xs mb-2">
                                    <span class="text-muted"><i class="bi bi-circle-fill text-danger me-1 small"></i>{{ app()->getLocale() == 'ar' ? 'أصناف تالفة/منتهية' : 'Expired Stock' }}</span>
                                    <span class="fw-bold text-danger font-monospace">{{ $inventoryReport['expired_count'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-xs mb-2">
                                    <span class="text-muted"><i class="bi bi-circle-fill text-warning me-1 small"></i>{{ app()->getLocale() == 'ar' ? 'أصناف حرجة (شبه منتهية)' : 'Expiring Soon' }}</span>
                                    <span class="fw-bold text-warning font-monospace">{{ $inventoryReport['expiring_soon']->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-xs">
                                    <span class="text-muted"><i class="bi bi-circle-fill text-success me-1 small"></i>{{ app()->getLocale() == 'ar' ? 'أصناف آمنة ومستقرة' : 'Healthy Stock' }}</span>
                                    <span class="fw-bold text-success font-monospace">{{ max(0, $inventoryReport['total_products'] - $inventoryReport['expired_count'] - $inventoryReport['expiring_soon']->count()) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- SECTION 7: Transactions History -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <div>
                                <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                    <i class="bi bi-arrow-left-right text-success me-2"></i>{{ app()->getLocale() == 'ar' ? 'سجل حركات المخزون التفصيلي' : 'Stock Movements Journal' }}
                                </h6>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="position-relative" style="min-width: 150px;">
                                    <i class="bi bi-search position-absolute top-50 translate-middle-y start-0 ms-2 text-muted" style="font-size: 0.8rem;"></i>
                                    <input type="text" id="inventoryTxSearch" class="form-control form-control-sm ps-4 rounded-3 text-xs" placeholder="{{ app()->getLocale() == 'ar' ? 'البحث...' : 'Search...' }}" onkeyup="filterInventoryTxTable()">
                                </div>
                                <a href="{{ route('reports.detailed', ['type' => 'inventory-transactions'] + $filters) }}" class="btn btn-outline-primary btn-sm rounded-pill text-xs px-3">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</a>
                            </div>
                        </div>
                        <div class="card-body p-0 mt-3">
                            <div class="table-responsive">
                                <table id="inventoryTxTable" class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="text-muted small">
                                            <th>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'المنتج' : 'Product' }}</th>
                                            <th class="text-center">{{ app()->getLocale() == 'ar' ? 'نوع الحركة' : 'Type' }}</th>
                                            <th class="text-center">{{ app()->getLocale() == 'ar' ? 'الكمية' : 'Qty' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'رقم السند/المرجع' : 'Ref Number' }}</th>
                                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'بواسطة' : 'User' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inventoryReport['transactions']->take(5) as $tx)
                                        <tr class="inventory-tx-row" data-tx-type="{{ $tx->type }}" style="border-bottom: 1px solid var(--border-color);">
                                            <td class="py-2 small text-muted">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                            <td class="py-2 fw-semibold small text-primary">{{ $tx->product->name ?? '-' }}</td>
                                            <td class="py-2 text-center">
                                                <span class="badge {{ $tx->quantity > 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} fw-bold text-xs px-2 py-1">
                                                    {{ app()->getLocale() == 'ar' ? ($tx->quantity > 0 ? 'توريد/زيادة' : 'صرف/نقصان') : ucfirst($tx->type) }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-center fw-bold font-monospace text-xs">{{ number_format($tx->quantity, 0) }}</td>
                                            <td class="py-2 text-muted small">{{ $tx->movement_number }}</td>
                                            <td class="py-2 text-end small">{{ $tx->creator->full_name ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4 small">{{ app()->getLocale() == 'ar' ? 'لا توجد حركات مخزنية مسجلة' : 'No transactions recorded' }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 9: Export Controls for Inventory Reports -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-body p-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-color);">
                            <i class="bi bi-download text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'أدوات التصدير والطباعة' : 'Export & Report Actions' }}
                        </h6>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'تصدير بيانات المخزون الحالية لملفات خارجية أو إرسالها للطباعة المباشرة' : 'Download inventory tables as PDF, Excel files or send to local printer' }}</p>
                    </div>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <a href="{{ route('reports.export', ['type' => 'inventory', 'format' => 'pdf'] + $filters) }}" class="btn btn-outline-danger d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                        </a>
                        <a href="{{ route('reports.export', ['type' => 'inventory', 'format' => 'excel'] + $filters) }}" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-file-earmark-excel-fill"></i> Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-printer-fill"></i> {{ app()->getLocale() == 'ar' ? 'طباعة' : 'Print' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. CUSTOMER REPORT TAB -->
        <div class="tab-pane fade" id="customer-report">
            <!-- SECTION 0: KPI Summary Cards Grid (Matching Inventory KPI style) -->
            <div class="row g-3 mb-4 kpi-carousel-wrapper">
                @php
                    $customerKpiDetails = [
                        ['title' => __('pos.total_customers'), 'value' => $customerReport['total_customers'], 'unit' => '', 'icon' => 'people', 'color' => '#3b82f6', 'desc' => 'Total customers in the system'],
                        ['title' => app()->getLocale() == 'ar' ? 'العملاء النشطون' : 'Active Customers', 'value' => $customerReport['active_customers'], 'unit' => '', 'icon' => 'person-check-fill', 'color' => '#10b981', 'desc' => 'Active customers with orders'],
                        ['title' => app()->getLocale() == 'ar' ? 'العملاء الجدد' : 'New Customers', 'value' => $customerReport['new_customers'], 'unit' => '', 'icon' => 'person-plus-fill', 'color' => '#06b6d4', 'desc' => 'New customer registrations'],
                        ['title' => app()->getLocale() == 'ar' ? 'العملاء المستمرون' : 'Returning Customers', 'value' => $customerReport['returning_customers'], 'unit' => '', 'icon' => 'arrow-repeat', 'color' => '#f59e0b', 'desc' => 'Returning customers count'],
                        ['title' => __('pos.total_purchases'), 'value' => number_format($customerReport['total_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'bag-fill', 'color' => '#3b82f6', 'desc' => 'Total purchases amount'],
                        ['title' => __('pos.total_paid'), 'value' => number_format($customerReport['total_paid'], 2), 'unit' => $setting->currency, 'icon' => 'cash-coin', 'color' => '#10b981', 'desc' => 'Total paid amount'],
                        ['title' => __('pos.collection_rate'), 'value' => number_format($customerReport['collection_rate'], 1), 'unit' => '%', 'icon' => 'graph-up-arrow', 'color' => '#f59e0b', 'desc' => 'Collection rate percentage'],
                        ['title' => __('pos.total_remaining'), 'value' => number_format($customerReport['total_remaining'], 2), 'unit' => $setting->currency, 'icon' => 'hourglass-split', 'color' => '#ef4444', 'desc' => 'Remaining balance (credit)']
                    ];
                @endphp

                @foreach($customerKpiDetails as $kpi)
                    <div class="col-6 col-md-4 col-xl-3 kpi-card-col">
                        <div class="card border-0 rounded-3 shadow-sm h-100 py-2 px-3 d-flex flex-row align-items-center gap-2" style="background-color: var(--card-bg); border-left: 3.5px solid {{ $kpi['color'] }} !important; min-height: 72px;" data-bs-toggle="tooltip" title="{{ $kpi['desc'] }}">
                            <!-- Circular Icon Wrapper -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--bs-light); border-color: var(--border-color) !important;">
                                <i class="bi bi-{{ $kpi['icon'] }}" style="color: {{ $kpi['color'] }}; font-size: 0.95rem;"></i>
                            </div>
                            
                            <!-- KPI Content -->
                            <div class="d-flex flex-column justify-content-center text-start">
                                <span class="text-muted text-uppercase fw-bold text-truncate" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    {{ $kpi['title'] }}
                                </span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fw-bold mb-0 text-dark" style="font-size: 1.1rem; line-height: 1.1; font-weight: 800 !important;">
                                        {{ $kpi['value'] }}
                                    </span>
                                    @if($kpi['unit'])
                                        <span class="text-muted fw-semibold ms-1" style="font-size: 0.65rem;">{{ $kpi['unit'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- SECTION 1: Customer Growth over Time -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-color);">
                            <i class="bi bi-graph-up text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'نمو وتفاعل العملاء' : 'Customer Growth & Engagement' }}
                        </h5>
                        <p class="text-muted small mb-0">{{ app()->getLocale() == 'ar' ? 'متابعة تسجيل العملاء الجدد ومعدل عودة العملاء الحاليين' : 'Monitor customer acquisition and retention trends' }}</p>
                    </div>
                    <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border-0" role="group" id="growthPeriodSelector">
                        <button type="button" class="btn btn-primary rounded-pill px-3 active" onclick="switchCustomerGrowthPeriod('daily')">{{ app()->getLocale() == 'ar' ? 'يومي' : 'Daily' }}</button>
                        <button type="button" class="btn btn-light rounded-pill px-3" onclick="switchCustomerGrowthPeriod('monthly')">{{ app()->getLocale() == 'ar' ? 'شهري' : 'Monthly' }}</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-scroll-wrapper" style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <div class="chart-inner-container" style="height: 320px; position: relative; width: 100%;">
                            <canvas id="customerGrowthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Customer Analytics Grid -->
            <div class="row g-4 mb-4">
                <!-- A. Top Customers by Spending -->
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-xs text-muted text-uppercase">{{ app()->getLocale() == 'ar' ? 'الأكثر شراءً' : 'Top Spenders' }}</span>
                            <button class="btn btn-link btn-sm text-xs p-0 text-decoration-none fw-semibold" onclick="openAllCustomersModal('spending')">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</button>
                        </div>
                        <div class="card-body px-4 pb-4 pt-2">
                            <div class="d-flex flex-column gap-3">
                                @forelse($customerReport['customers']->sortByDesc('total_purchases')->take(5) as $idx => $c)
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary text-xs fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">{{ $idx + 1 }}</div>
                                        <span class="fw-semibold text-xs" style="color: var(--text-color, #0f172a); font-size: 0.8rem !important; white-space: normal; word-break: break-word; max-width: 130px; line-height: 1.3;">{{ $c->name }}</span>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold font-monospace text-xs">{{ number_format($c->total_purchases, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></span>
                                </div>
                                @empty
                                <div class="text-center text-muted text-xs py-4">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data' }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- C. Most Frequent Visitors -->
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-xs text-muted text-uppercase">{{ app()->getLocale() == 'ar' ? 'الأكثر تكراراً' : 'Most Frequent' }}</span>
                            <button class="btn btn-link btn-sm text-xs p-0 text-decoration-none fw-semibold" onclick="openAllCustomersModal('frequent')">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</button>
                        </div>
                        <div class="card-body px-4 pb-4 pt-2">
                            <div class="d-flex flex-column gap-3">
                                @forelse($customerReport['customers']->sortByDesc('visits')->take(5) as $idx => $c)
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning text-xs fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">{{ $idx + 1 }}</div>
                                        <span class="fw-semibold text-xs" style="color: var(--text-color, #0f172a); font-size: 0.8rem !important; white-space: normal; word-break: break-word; max-width: 130px; line-height: 1.3;">{{ $c->name }}</span>
                                    </div>
                                    <span class="badge bg-warning text-dark fw-bold font-monospace text-xs">{{ $c->visits }} {{ app()->getLocale() == 'ar' ? 'زيارة' : 'Visits' }}</span>
                                </div>
                                @empty
                                <div class="text-center text-muted text-xs py-4">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data' }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- SECTION 5: Customer Purchase History Table -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                            <i class="bi bi-list-stars text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'سجل تفاصيل ومشتريات العملاء الكامل' : 'Comprehensive Customer Directory' }}
                        </h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="position-relative" style="min-width: 250px;">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y start-0 ms-3 text-muted" style="font-size: 0.85rem;"></i>
                            <input type="text" id="custTableSearch" class="form-control form-control-sm ps-5 rounded-3" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث باسم العميل أو الهاتف...' : 'Search customers...' }}" onkeyup="filterCustomerListTable()" style="height: 38px; border-radius: 10px !important;">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table id="customerListTable" class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small">
                                    <th class="py-3 px-4">{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer Name' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الهاتف' : 'Phone' }}</th>
                                    <th class="text-center">{{ app()->getLocale() == 'ar' ? 'عدد الطلبات' : 'Orders Count' }}</th>
                                    <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجمالي المشتريات' : 'Total Purchases' }}</th>
                                    <th class="text-end">{{ app()->getLocale() == 'ar' ? 'المتبقي (ديون)' : 'Balance' }}</th>
                                    <th class="text-center">{{ app()->getLocale() == 'ar' ? 'حالة العميل' : 'Status' }}</th>
                                    <th class="text-end px-4">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customerReport['customers'] as $c)
                                <tr class="customer-row-item" data-name="{{ strtolower($c->name) }}" data-phone="{{ $c->phone }}" style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="bi bi-person text-muted"></i>
                                            </div>
                                            <div class="d-flex flex-column text-start">
                                                <span class="fw-bold text-dark">{{ $c->name }}</span>
                                                <span class="text-muted text-xxs">ID: #{{ $c->id }} | {{ $c->address ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="font-monospace text-xs">{{ $c->phone }}</span></td>
                                    <td class="text-center fw-bold text-primary font-monospace text-xs">{{ $c->visits }}</td>
                                    <td class="text-end fw-bold text-success font-monospace text-xs">{{ number_format($c->total_purchases, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                    <td class="text-end fw-semibold text-danger font-monospace text-xs">{{ number_format($c->balance, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                    <td class="text-center">
                                        @if($c->visits > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'نشط' : 'Active' }}</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'خامل' : 'Inactive' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <button onclick="previewCustomerProfile({{ json_encode($c) }})" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-xs"><i class="bi bi-eye"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-4 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No customers' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Glassmorphism Styles (Converted to Solid Clean Premium White Styles) -->
            <style>
                .glass-modal .modal-content {
                    background: #ffffff !important;
                    border: 1px solid rgba(0, 0, 0, 0.08) !important;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
                    border-radius: 20px !important;
                }
                .glass-modal .modal-header {
                    background: #ffffff !important;
                    border-bottom: 1px solid #f1f5f9 !important;
                }
                .glass-modal .modal-body {
                    background: #ffffff !important;
                    padding: 1.5rem !important;
                }
                .glass-modal .dark-gradient-header {
                    background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important;
                    border-bottom: 0 !important;
                    padding: 1.25rem 1.5rem !important;
                    border-top-left-radius: 19px !important;
                    border-top-right-radius: 19px !important;
                }
                .glass-modal .glass-card {
                    background: #ffffff !important;
                    border: 1.5px solid #e2e8f0 !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02) !important;
                    border-radius: 12px !important;
                    transition: all 0.2s ease;
                }
                .glass-modal .glass-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05) !important;
                    border-color: #cbd5e1 !important;
                }
                .glass-modal .list-group-item {
                    background: #ffffff !important;
                    border-bottom: 1px solid #f1f5f9 !important;
                    padding-left: 1.25rem !important;
                    padding-right: 1.25rem !important;
                    margin-bottom: 0 !important;
                    border-radius: 0 !important;
                }
                .glass-modal .list-group-item:last-child {
                    border-bottom: 0 !important;
                }
            
    /* Dark Mode Overrides for Reports Page */
    html[data-app-theme="dark"] .glass-modal .modal-content {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .glass-modal .modal-header {
        background: #0f172a !important;
        border-bottom-color: #334155 !important;
    }
    html[data-app-theme="dark"] .glass-modal .modal-body {
        background: #0f172a !important;
    }
    html[data-app-theme="dark"] .glass-modal .glass-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .glass-modal .list-group-item {
        background: #0f172a !important;
        border-bottom-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .glass-modal .list-group-item .fw-bold,
    html[data-app-theme="dark"] .glass-modal .list-group-item .fw-semibold,
    html[data-app-theme="dark"] .glass-modal .list-group-item span {
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .text-dark {
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .fw-semibold.text-xs,
    html[data-app-theme="dark"] .fw-semibold.small {
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .text-muted.text-xxs,
    html[data-app-theme="dark"] .text-xxs {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .border-bottom {
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .table td,
    html[data-app-theme="dark"] .table th {
        color: #e2e8f0 !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .table thead th {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .table tr:hover td {
        background: #1e293b !important;
    }
    html[data-app-theme="dark"] .table-borderless td {
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .rounded-circle.bg-warning.bg-opacity-10 {
        background-color: rgba(245, 158, 11, 0.15) !important;
    }
    html[data-app-theme="dark"] .rounded-circle.bg-primary.bg-opacity-10 {
        background-color: rgba(59, 130, 246, 0.15) !important;
    }
    html[data-app-theme="dark"] .rounded-circle.bg-success.bg-opacity-10 {
        background-color: rgba(16, 185, 129, 0.15) !important;
    }
    html[data-app-theme="dark"] .nav-pills .nav-link:not(.active) {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .form-control,
    html[data-app-theme="dark"] .form-select {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .btn-outline-secondary {
        color: #94a3b8 !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .btn-outline-secondary:hover {
        background: #1e293b !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] small.text-muted,
    html[data-app-theme="dark"] span.text-muted {
        color: #94a3b8 !important;
    }

    /* Fix list-group-item white backgrounds in dark mode */
    html[data-app-theme="dark"] .list-group-item {
        background-color: var(--card-bg) !important;
        border-color: var(--border-color) !important;
        color: var(--text-color) !important;
    }
    html[data-app-theme="dark"] .list-group-item .text-dark,
    html[data-app-theme="dark"] .list-group-item .fw-bold.text-dark {
        color: var(--text-color) !important;
    }
    html[data-app-theme="dark"] .card-body {
        background-color: var(--card-bg) !important;
    }
    html[data-app-theme="dark"] .card-header {
        background-color: var(--card-bg) !important;
    }
</style>

            <!-- SECTION 6: Customer Profile Preview Modal -->
            <div class="modal fade glass-modal" id="customerProfilePreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 shadow-lg">
                        <div class="modal-header dark-gradient-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white bg-opacity-10 text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-person-badge fs-4 text-white"></i>
                                </div>
                                <div class="d-flex flex-column text-start">
                                    <h5 class="modal-title fw-bold text-white mb-0">{{ app()->getLocale() == 'ar' ? 'ملف تعريف العميل' : 'Customer Profile Summary' }}</h5>
                                    <small class="text-white-50 text-xs">{{ app()->getLocale() == 'ar' ? 'تقرير تفاصيل العميل' : 'Customer Report' }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn border-0 text-white bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="width: 38px; height: 38px;">
                                <i class="bi bi-x fs-4"></i>
                            </button>
                        </div>
                        <div class="modal-body p-4" id="customerProfileModalBody">
                            <!-- Dynamic Content Filled by JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- ALL CUSTOMERS LIST MODAL (For View All buttons) -->
            <div class="modal fade glass-modal" id="allCustomersReportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 rounded-4 shadow-lg">
                        <div class="modal-header dark-gradient-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white bg-opacity-10 text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-people fs-4 text-white"></i>
                                </div>
                                <div class="d-flex flex-column text-start">
                                    <h5 class="modal-title fw-bold text-white mb-0" id="allCustomersModalTitle"></h5>
                                    <small class="text-white-50 text-xs">{{ app()->getLocale() == 'ar' ? 'تقرير العملاء الشامل' : 'Comprehensive Customer Report' }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn border-0 text-white bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="width: 38px; height: 38px;">
                                <i class="bi bi-x fs-4"></i>
                            </button>
                        </div>
                        <div class="modal-body p-4" id="allCustomersModalBody" style="max-height: 480px; overflow-y: auto;">
                            <!-- Filled dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. SUPPLIER REPORT TAB -->
        <div class="tab-pane fade" id="supplier-report">
            <!-- SECTION 0: KPI Summary Cards Grid (Matching Customer KPI style) -->
            <div class="row g-3 mb-4 kpi-carousel-wrapper">
                @php
                    $supplierKpiDetails = [
                        ['title' => __('pos.total_suppliers'), 'value' => $supplierReport['total_suppliers'], 'unit' => '', 'icon' => 'truck', 'color' => '#3b82f6', 'desc' => 'Total suppliers in the system'],
                        ['title' => __('pos.total_purchases'), 'value' => number_format($supplierReport['total_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'cart-check', 'color' => '#06b6d4', 'desc' => 'Total purchases amount'],
                        ['title' => __('pos.total_paid'), 'value' => number_format($supplierReport['total_paid'], 2), 'unit' => $setting->currency, 'icon' => 'shield-check', 'color' => '#10b981', 'desc' => 'Total paid to suppliers'],
                        ['title' => __('pos.total_remaining'), 'value' => number_format($supplierReport['total_remaining'], 2), 'unit' => $setting->currency, 'icon' => 'wallet2', 'color' => '#ef4444', 'desc' => 'Total remaining balance due']
                    ];
                @endphp

                @foreach($supplierKpiDetails as $kpi)
                    <div class="col-6 col-md-3 kpi-card-col">
                        <div class="card border-0 rounded-3 shadow-sm h-100 py-2 px-3 d-flex flex-row align-items-center gap-2" style="background-color: var(--card-bg); border-left: 3.5px solid {{ $kpi['color'] }} !important; min-height: 72px;" data-bs-toggle="tooltip" title="{{ $kpi['desc'] }}">
                            <!-- Circular Icon Wrapper -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--bs-light); border-color: var(--border-color) !important;">
                                <i class="bi bi-{{ $kpi['icon'] }}" style="color: {{ $kpi['color'] }}; font-size: 0.95rem;"></i>
                            </div>
                            
                            <!-- KPI Content -->
                            <div class="d-flex flex-column justify-content-center text-start">
                                <span class="text-muted text-uppercase fw-bold text-truncate" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    {{ $kpi['title'] }}
                                </span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fw-bold mb-0 text-dark" style="font-size: 1.1rem; line-height: 1.1; font-weight: 800 !important;">
                                        {{ $kpi['value'] }}
                                    </span>
                                    @if($kpi['unit'])
                                        <span class="text-muted fw-semibold ms-1" style="font-size: 0.65rem;">{{ $kpi['unit'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @php
                $topByTotal = collect($supplierReport['suppliers'])->sortByDesc('total_purchases')->take(5);
                $topByInvoices = collect($supplierReport['suppliers'])->sortByDesc('invoice_count')->take(5);
                $topByAverage = collect($supplierReport['suppliers'])->map(function($s) {
                    $s->avg_value = $s->invoice_count > 0 ? $s->total_purchases / $s->invoice_count : 0;
                    return $s;
                })->sortByDesc('avg_value')->take(5);
            @endphp

            <!-- SECTION 1: Purchase Trend Chart -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                            <i class="bi bi-graph-up-arrow text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'تحليل اتجاهات ونشاط المشتريات' : 'Purchase Activity & Volume Trend' }}
                        </h6>
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'مراقبة وتتبع حركة المشتريات مع الموردين وقيم التوريد' : 'Monitor procurement value and invoice volumes over time' }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2" id="purchaseTrendPeriodSelector">
                        <button onclick="switchSupplierPurchaseTrendPeriod('daily', this)" class="btn btn-sm btn-primary rounded-pill px-3">{{ app()->getLocale() == 'ar' ? 'يومي' : 'Daily' }}</button>
                        <button onclick="switchSupplierPurchaseTrendPeriod('monthly', this)" class="btn btn-sm btn-light border rounded-pill px-3">{{ app()->getLocale() == 'ar' ? 'شهري' : 'Monthly' }}</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-scroll-wrapper" style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <div class="chart-inner-container" style="height: 350px; position: relative; width: 100%;">
                            <canvas id="supplierPurchaseTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Supplier Performance -->
            <div class="row g-4 mb-4">
                <!-- A. Top Suppliers by Purchase Value -->
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">{{ app()->getLocale() == 'ar' ? 'أعلى الموردين توريداً (قيمة)' : 'Top Suppliers (Value)' }}</h6>
                            <button onclick="openAllSuppliersModal('value')" class="btn btn-xs btn-link text-primary text-decoration-none p-0 fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</button>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="list-group list-group-flush">
                                @forelse($topByTotal as $index => $s)
                                <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 gap-2" style="background-color: var(--card-bg); border-color: var(--border-color) !important; min-width: 0;">
                                    <div class="d-flex align-items-center gap-3" style="min-width: 0; flex: 1 1 auto;">
                                        <span class="text-muted fw-bold font-monospace text-xs">#{{ $index + 1 }}</span>
                                        <div class="d-flex flex-column text-start" style="min-width: 0; flex: 1 1 auto;">
                                            <span class="fw-bold text-xs" style="color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $s->name }}</span>
                                            <span class="text-muted text-xxs" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $s->invoice_count }} {{ app()->getLocale() == 'ar' ? 'فواتير' : 'Invoices' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold font-monospace text-xs flex-shrink-0">{{ number_format($s->total_purchases, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></span>
                                </div>
                                @empty
                                <div class="text-center py-4 text-muted small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data' }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B. Most Purchased Suppliers (Volume) -->
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">{{ app()->getLocale() == 'ar' ? 'الأكثر تكراراً وتعاملاً (حجم)' : 'Most Purchased (Volume)' }}</h6>
                            <button onclick="openAllSuppliersModal('volume')" class="btn btn-xs btn-link text-primary text-decoration-none p-0 fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</button>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="list-group list-group-flush">
                                @forelse($topByInvoices as $index => $s)
                                <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 gap-2" style="background-color: var(--card-bg); border-color: var(--border-color) !important; min-width: 0;">
                                    <div class="d-flex align-items-center gap-3" style="min-width: 0; flex: 1 1 auto;">
                                        <span class="text-muted fw-bold font-monospace text-xs">#{{ $index + 1 }}</span>
                                        <div class="d-flex flex-column text-start" style="min-width: 0; flex: 1 1 auto;">
                                            <span class="fw-bold text-xs" style="color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $s->name }}</span>
                                            <span class="text-muted text-xxs" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $s->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold font-monospace text-xs flex-shrink-0">{{ $s->invoice_count }} {{ app()->getLocale() == 'ar' ? 'طلب توريد' : 'Orders' }}</span>
                                </div>
                                @empty
                                <div class="text-center py-4 text-muted small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data' }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Purchase Distribution Charts -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-pie-chart text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'توزيع المشتريات حسب الموردين' : 'Purchases by Supplier' }}
                            </h6>
                        </div>
                        <div class="card-body p-4 d-flex align-items-center justify-content-center">
                            <div style="width: 100%; max-width: 320px; height: 260px; position: relative;">
                                <canvas id="supplierPurchasesShareChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-bar-chart-steps text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'المشتريات حسب تصنيف المنتجات' : 'Purchases by Category' }}
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div style="height: 260px; position: relative;">
                                <canvas id="supplierCategoryPurchasesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- SECTION 5: Supplier Purchase History Table -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                            <i class="bi bi-list-task text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'سجل تفاصيل ومشتريات الموردين' : 'Supplier Procurement Directory' }}
                        </h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="position-relative" style="min-width: 250px;">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y start-0 ms-3 text-muted" style="font-size: 0.85rem;"></i>
                            <input type="text" id="suppTableSearch" class="form-control form-control-sm ps-5 rounded-3" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث باسم المورد أو الرقم...' : 'Search suppliers...' }}" onkeyup="filterSupplierListTable()" style="height: 38px; border-radius: 10px !important;">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table id="supplierListTable" class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small">
                                    <th class="py-3 px-4">{{ app()->getLocale() == 'ar' ? 'المورد' : 'Supplier Name' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</th>
                                    <th class="text-center">{{ app()->getLocale() == 'ar' ? 'طلبات التوريد' : 'Invoices Count' }}</th>
                                    <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجمالي التوريد' : 'Total Purchases' }}</th>
                                    <th class="text-end">{{ app()->getLocale() == 'ar' ? 'المتبقي (ديون)' : 'Remaining Amount' }}</th>
                                    <th class="text-center">{{ app()->getLocale() == 'ar' ? 'حالة الحساب' : 'Status' }}</th>
                                    <th class="text-end px-4">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplierReport['suppliers'] as $s)
                                <tr class="supplier-row-item" data-name="{{ strtolower($s->name) }}" data-number="{{ strtolower($s->supplier_number) }}" style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="bi bi-truck text-muted"></i>
                                            </div>
                                            <div class="d-flex flex-column text-start">
                                                <span class="fw-bold text-dark">{{ $s->name }}</span>
                                                <span class="text-muted text-xxs">NO: {{ $s->supplier_number ?? '#'.$s->id }} | {{ $s->address ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-xs">{{ $s->email ?? '-' }}</span></td>
                                    <td class="text-center fw-bold text-primary font-monospace text-xs">{{ $s->invoice_count }}</td>
                                    <td class="text-end fw-bold text-success font-monospace text-xs">{{ number_format($s->total_purchases, 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                    <td class="text-end fw-semibold text-danger font-monospace text-xs">{{ number_format($s->total_remaining, 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                    <td class="text-center">
                                        @if($s->invoice_count > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'نشط' : 'Active' }}</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2 py-1">{{ app()->getLocale() == 'ar' ? 'خامل' : 'Inactive' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <button onclick="previewSupplierProfile({{ json_encode($s) }})" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-xs"><i class="bi bi-eye"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-4 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No suppliers' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: Supplier Profile Preview Modal -->
            <div class="modal fade glass-modal" id="supplierProfilePreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 shadow-lg">
                        <div class="modal-header dark-gradient-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white bg-opacity-10 text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-truck fs-4 text-white"></i>
                                </div>
                                <div class="d-flex flex-column text-start">
                                    <h5 class="modal-title fw-bold text-white mb-0">{{ app()->getLocale() == 'ar' ? 'ملف تعريف المورد' : 'Supplier Profile Summary' }}</h5>
                                    <small class="text-white-50 text-xs">{{ app()->getLocale() == 'ar' ? 'تقرير تفاصيل المورد وحركة التوريد' : 'Supplier Procurement Report' }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn border-0 text-white bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="width: 38px; height: 38px;">
                                <i class="bi bi-x fs-4"></i>
                            </button>
                        </div>
                        <div class="modal-body p-4" id="supplierProfileModalBody">
                            <!-- Dynamic Content Filled by JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- ALL SUPPLIERS LIST MODAL -->
            <div class="modal fade glass-modal" id="allSuppliersReportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 rounded-4 shadow-lg">
                        <div class="modal-header dark-gradient-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white bg-opacity-10 text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-truck-flatbed fs-4 text-white"></i>
                                </div>
                                <div class="d-flex flex-column text-start">
                                    <h5 class="modal-title fw-bold text-white mb-0" id="allSuppliersModalTitle"></h5>
                                    <small class="text-white-50 text-xs">{{ app()->getLocale() == 'ar' ? 'تقرير الموردين الشامل' : 'Comprehensive Supplier Report' }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn border-0 text-white bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="width: 38px; height: 38px;">
                                <i class="bi bi-x fs-4"></i>
                            </button>
                        </div>
                        <div class="modal-body p-4" id="allSuppliersModalBody" style="max-height: 480px; overflow-y: auto;">
                            <!-- Filled dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: EXPENSES REPORT TAB -->
        <div class="tab-pane fade" id="expenses-report">
            <!-- SECTION 0: KPI Summary Cards Grid (Matching Inventory & Customer KPI style) -->
            <div class="row g-3 mb-4 kpi-carousel-wrapper">
                @php
                    $expensesKpiDetails = [
                        ['title' => __('pos.total_expenses'), 'value' => number_format($expensesReport['total_expenses'], 2), 'unit' => $setting->currency, 'icon' => 'wallet2', 'color' => '#ef4444', 'desc' => 'Total approved expenses'],
                        ['title' => app()->getLocale() == 'ar' ? 'مصروفات هذا الشهر' : 'This Month Expenses', 'value' => number_format($expensesReport['this_month_expenses'], 2), 'unit' => $setting->currency, 'icon' => 'calendar-month', 'color' => '#3b82f6', 'desc' => 'Total expenses for the current calendar month'],
                        ['title' => __('pos.expense_count'), 'value' => $expensesReport['expense_count'], 'unit' => '', 'icon' => 'receipt', 'color' => '#06b6d4', 'desc' => 'Total count of approved expenses'],
                        ['title' => __('pos.average_expense'), 'value' => number_format($expensesReport['average_expense'], 2), 'unit' => $setting->currency, 'icon' => 'calculator', 'color' => '#f59e0b', 'desc' => 'Average expense amount'],
                        ['title' => __('pos.highest_expense'), 'value' => number_format($expensesReport['highest_expense']['amount'], 2), 'unit' => $setting->currency, 'icon' => 'arrow-up-circle', 'color' => '#ef4444', 'desc' => 'Highest recorded approved expense'],
                        ['title' => app()->getLocale() == 'ar' ? 'أدنى مصروف' : 'Lowest Expense', 'value' => number_format($expensesReport['lowest_expense']['amount'], 2), 'unit' => $setting->currency, 'icon' => 'arrow-down-circle', 'color' => '#10b981', 'desc' => 'Lowest recorded approved expense']
                    ];
                @endphp

                @foreach($expensesKpiDetails as $kpi)
                    <div class="col-6 col-md-4 col-xl-3 kpi-card-col">
                        <div class="card border-0 rounded-3 shadow-sm h-100 py-2 px-3 d-flex flex-row align-items-center gap-2" style="background-color: var(--card-bg); border-left: 3.5px solid {{ $kpi['color'] }} !important; min-height: 72px;" data-bs-toggle="tooltip" title="{{ $kpi['desc'] }}">
                            <!-- Circular Icon Wrapper -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--bs-light); border-color: var(--border-color) !important;">
                                <i class="bi bi-{{ $kpi['icon'] }}" style="color: {{ $kpi['color'] }}; font-size: 0.95rem;"></i>
                            </div>
                            
                            <!-- KPI Content -->
                            <div class="d-flex flex-column justify-content-center text-start">
                                <span class="text-muted text-uppercase fw-bold text-truncate" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    {{ $kpi['title'] }}
                                </span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fw-bold mb-0 text-dark" style="font-size: 1.1rem; line-height: 1.1; font-weight: 800 !important;">
                                        {{ $kpi['value'] }}
                                    </span>
                                    @if($kpi['unit'])
                                        <span class="text-muted fw-semibold ms-1" style="font-size: 0.65rem;">{{ $kpi['unit'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @php
                $topCategories = collect($expensesReport['highest_categories'])->take(5);
                $largestExpenses = collect($expensesReport['largest_expenses'])->take(5);
                $frequentCategories = collect($expensesReport['frequent_categories'])->take(5);
                $paymentMethods = collect($expensesReport['payment_methods']);
            @endphp

            <!-- SECTION 1: Expense Trend Chart -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                            <i class="bi bi-graph-up-arrow text-danger me-2"></i>{{ app()->getLocale() == 'ar' ? 'تحليل اتجاهات ونشاط المصروفات' : 'Expense Trend & Activity Analysis' }}
                        </h6>
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'مراقبة تطور قيم المصروفات الإجمالية وعدد العمليات' : 'Monitor total expense amounts and transaction volumes over time' }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2" id="expenseTrendPeriodSelector">
                        <button onclick="switchExpenseTrendPeriod('daily')" class="btn btn-sm btn-primary rounded-pill px-3">{{ app()->getLocale() == 'ar' ? 'يومي' : 'Daily' }}</button>
                        <button onclick="switchExpenseTrendPeriod('monthly')" class="btn btn-sm btn-light border rounded-pill px-3">{{ app()->getLocale() == 'ar' ? 'شهري' : 'Monthly' }}</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-scroll-wrapper" style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <div class="chart-inner-container" style="height: 320px; position: relative; width: 100%;">
                            <canvas id="expenseTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Expense Analytics -->
            <div class="row g-4 mb-4">
                <!-- A. Highest Expense Categories -->
                <div class="col-12">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">{{ app()->getLocale() == 'ar' ? 'الأعلى صرفاً (حسب الفئة)' : 'Highest Categories (Value)' }}</h6>
                            <button onclick="openAllExpensesModal('highest_categories')" class="btn btn-xs btn-link text-primary text-decoration-none p-0 fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</button>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="list-group list-group-flush">
                                @forelse($topCategories as $index => $c)
                                <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="text-muted fw-bold font-monospace text-xs">#{{ $index + 1 }}</span>
                                        <div class="d-flex flex-column text-start">
                                            <span class="fw-bold text-dark text-xs">{{ $c->category_label }}</span>
                                            <span class="text-muted text-xxs">{{ $c->count }} {{ app()->getLocale() == 'ar' ? 'عمليات' : 'Transactions' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold font-monospace text-xs">{{ number_format($c->total_amount, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></span>
                                </div>
                                @empty
                                <div class="text-center py-4 text-muted small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data' }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Expense Distribution Charts -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                                <i class="bi bi-pie-chart text-danger me-2"></i>{{ app()->getLocale() == 'ar' ? 'توزيع المصروفات حسب الفئة' : 'Expenses by Category (Share)' }}
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div style="height: 250px; position: relative;">
                                <canvas id="expenseCategoryDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: Expense History Table -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                            <i class="bi bi-list-task text-danger me-2"></i>{{ app()->getLocale() == 'ar' ? 'سجل المصروفات المعتمدة' : 'Approved Expense Ledger' }}
                        </h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="position-relative" style="min-width: 220px;">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y start-0 ms-3 text-muted" style="font-size: 0.85rem;"></i>
                            <input type="text" id="expenseTableSearch" class="form-control form-control-sm ps-5 rounded-3" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في المصروفات...' : 'Search expenses...' }}" onkeyup="filterExpenseListTable()" style="height: 38px; border-radius: 10px !important;">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table id="expenseListTable" class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small">
                                    <th class="py-3 px-4">{{ app()->getLocale() == 'ar' ? 'رقم المصروف' : 'Expense Number' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الاسم/البيان' : 'Expense Name' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'الفئة' : 'Category' }}</th>
                                    <th class="text-end">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'طريقة السداد' : 'Payment Method' }}</th>
                                    <th>{{ app()->getLocale() == 'ar' ? 'بواسطة' : 'Created By' }}</th>
                                    <th class="text-end px-4">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expensesReport['expenses'] as $e)
                                <tr class="expense-row-item" data-name="{{ strtolower($e->description_ar . ' ' . $e->description_en) }}" data-number="{{ strtolower($e->expense_number) }}" style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 fw-semibold font-monospace text-xs text-secondary">
                                        {{ $e->expense_number ?? '#'.$e->id }}
                                    </td>
                                    <td class="text-xs">{{ $e->expense_date }}</td>
                                    <td>
                                        <div class="d-flex flex-column text-start">
                                            <span class="fw-bold text-dark text-xs">{{ $e->description_ar ?: $e->description_en ?: '-' }}</span>
                                            @if($e->notes)
                                                <small class="text-muted text-xxs text-truncate" style="max-width: 200px;">{{ $e->notes }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold px-2 py-1 text-xxs">{{ $e->type }}</span></td>
                                    <td class="text-end fw-bold text-danger font-monospace text-xs">{{ number_format($e->amount, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                    <td><span class="text-xs">{{ __('pos.' . strtolower(str_replace(' ', '_', $e->payment_method))) ?? $e->payment_method }}</span></td>
                                    <td>
                                        <span class="text-xs text-muted">{{ $e->user->full_name ?? '-' }}</span>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <button onclick="previewExpenseDetails({{ json_encode($e) }})" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-xs" title="View"><i class="bi bi-eye"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted py-4 small">{{ app()->getLocale() == 'ar' ? 'لا توجد مصروفات مسجلة' : 'No expenses recorded' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: Expense Details Preview Modal -->
            <div class="modal fade" id="expenseDetailPreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content glass border-0 rounded-4 shadow">
                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0 modal-title" id="expenseDetailModalLabel" style="color: var(--text-color);">
                                <i class="bi bi-file-earmark-text text-danger me-2"></i>{{ app()->getLocale() == 'ar' ? 'تفاصيل المصروف' : 'Expense Details' }}
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4" id="expenseDetailModalBody">
                            <!-- Populated dynamically via JS -->
                        </div>
                        <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إغلاق' : 'Close' }}</button>
                            <div id="expenseModalActions">
                                <!-- Print or Edit action buttons injected dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: View All Expenses Report Modal -->
            <div class="modal fade" id="allExpensesReportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content glass border-0 rounded-4 shadow">
                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" id="allExpensesReportModalTitle" style="color: var(--text-color);"></h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4" id="allExpensesReportModalBody">
                            <!-- Dynamic content -->
                        </div>
                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إغلاق' : 'Close' }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. FINANCIAL REPORT (PROFIT & LOSS) TAB -->
        <div class="tab-pane fade" id="financial-report">
            <!-- SECTION 0: KPI Summary Cards Grid (Same size as others) -->
            <div class="row g-3 mb-4 kpi-carousel-wrapper">
                @php
                    $financialKpis = [
                        ['title' => __('pos.total_sales'), 'value' => number_format($financialReport['total_sales'], 2), 'unit' => $setting->currency, 'icon' => 'graph-up', 'color' => '#10b981', 'desc' => 'Total sales in period'],
                        ['title' => __('pos.net_revenue'), 'value' => number_format($financialReport['net_revenue'], 2), 'unit' => $setting->currency, 'icon' => 'wallet2', 'color' => '#06b6d4', 'desc' => 'Net sales revenue after returns'],
                        ['title' => __('pos.total_returns'), 'value' => number_format($financialReport['total_returns'], 2), 'unit' => $setting->currency, 'icon' => 'arrow-return-left', 'color' => '#ef4444', 'desc' => 'Total value of sales returns'],
                        ['title' => __('pos.total_purchases'), 'value' => number_format($financialReport['total_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'graph-down', 'color' => '#f59e0b', 'desc' => 'Total supplier purchases'],
                        ['title' => __('pos.total_expenses'), 'value' => number_format($financialReport['total_expenses'], 2), 'unit' => $setting->currency, 'icon' => 'receipt', 'color' => '#a855f7', 'desc' => 'Total operational expenses'],
                        ['title' => __('pos.total_waste_value'), 'value' => number_format($financialReport['total_waste'], 2), 'unit' => $setting->currency, 'icon' => 'trash3', 'color' => '#6b7280', 'desc' => 'Total value of expired/damaged stock'],
                        ['title' => __('pos.most_frequent_categories'), 'value' => $financialReport['most_frequent_category'], 'unit' => '', 'icon' => 'tags', 'color' => '#3b82f6', 'desc' => 'Highest expense category'],
                        ['title' => __('pos.net_profit'), 'value' => number_format($financialReport['net_profit'], 2), 'unit' => $setting->currency, 'icon' => 'bank', 'color' => '#3b82f6', 'desc' => 'Net profit after all costs']
                    ];
                @endphp

                @foreach($financialKpis as $kpi)
                    <div class="col-6 col-md-4 col-xl-3 kpi-card-col">
                        <div class="card border-0 rounded-3 shadow-sm h-100 py-2 px-3 d-flex flex-row align-items-center gap-2" style="background-color: var(--card-bg); border-left: 3.5px solid {{ $kpi['color'] }} !important; min-height: 72px;" data-bs-toggle="tooltip" title="{{ $kpi['desc'] }}">
                            <!-- Circular Icon Wrapper -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--bs-light); border-color: var(--border-color) !important;">
                                <i class="bi bi-{{ $kpi['icon'] }}" style="color: {{ $kpi['color'] }}; font-size: 0.95rem;"></i>
                            </div>
                            
                            <!-- KPI Content -->
                            <div class="d-flex flex-column justify-content-center text-start">
                                <span class="text-muted text-uppercase fw-bold text-truncate" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    {{ $kpi['title'] }}
                                </span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fw-bold mb-0 text-dark text-truncate" style="font-size: 1.1rem; line-height: 1.1; font-weight: 800 !important; max-width: 130px;">
                                        {{ $kpi['value'] }}
                                    </span>
                                    @if($kpi['unit'])
                                        <span class="text-muted fw-semibold ms-1" style="font-size: 0.65rem;">{{ $kpi['unit'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @php
                $topRevenueCats = collect($financialReport['top_revenue_categories'])->take(5);
                $topExpenseCats = collect($financialReport['expense_breakdown'])->sortByDesc('total')->take(5);
                $finPaymentMethods = collect($financialReport['payment_methods']);
            @endphp

            <!-- SECTION 1: Revenue vs Expenses Chart -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                            <i class="bi bi-graph-up-arrow text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'تحليل الإيرادات والمصروفات وصافي الأرباح' : 'Revenue vs Expenses & Net Profit' }}
                        </h6>
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'مقارنة الأداء المالي والربحي مع مرور الوقت' : 'Compare financial performance and profitability over time' }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2" id="financialTrendPeriodSelector">
                        <button onclick="switchFinancialTrendPeriod('daily', this)" class="btn btn-sm btn-primary rounded-pill px-3">{{ app()->getLocale() == 'ar' ? 'يومي' : 'Daily' }}</button>
                        <button onclick="switchFinancialTrendPeriod('monthly', this)" class="btn btn-sm btn-light border rounded-pill px-3">{{ app()->getLocale() == 'ar' ? 'شهري' : 'Monthly' }}</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-scroll-wrapper" style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <div class="chart-inner-container" style="height: 320px; position: relative; width: 100%;">
                            <canvas id="financialTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Profit Analysis & Breakdown -->
            <div class="row g-4 mb-4">
                <!-- A. Revenue vs Expenses Breakdown Donut Charts -->
                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">{{ app()->getLocale() == 'ar' ? 'توزيع مصادر الإيرادات' : 'Revenue Sources (Share)' }}</h6>
                        </div>
                        <div class="card-body p-4 d-flex align-items-center justify-content-center">
                            <div style="height: 220px; width: 100%; position: relative;">
                                <canvas id="financialRevenueDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100" style="background-color: var(--card-bg);">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="color: var(--text-color);">{{ app()->getLocale() == 'ar' ? 'توزيع المصروفات حسب الفئة' : 'Expense Categories (Share)' }}</h6>
                            <button onclick="openAllFinancialModal('expenses')" class="btn btn-xs btn-link text-primary text-decoration-none p-0 fw-bold">{{ app()->getLocale() == 'ar' ? 'عرض الكل' : 'View All' }}</button>
                        </div>
                        <div class="card-body p-4 d-flex align-items-center justify-content-center">
                            <div style="height: 220px; width: 100%; position: relative;">
                                <canvas id="financialExpenseDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- SECTION 4: Financial Summary Table -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                        <i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'ملخص القوائم المالية والأرباح' : 'Financial Statement & Profit Summary' }}
                    </h6>
                    <!-- Export Actions -->
                    <div class="btn-group">
                        <a href="{{ route('reports.export', ['type' => 'financial', 'format' => 'pdf'] + $filters) }}" class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                        <a href="{{ route('reports.export', ['type' => 'financial', 'format' => 'excel'] + $filters) }}" class="btn btn-sm btn-outline-success rounded-3"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                    </div>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small">
                                    <th class="py-3 px-4">{{ app()->getLocale() == 'ar' ? 'البند المالي / المؤشر' : 'Financial Metric' }}</th>
                                    <th class="text-end px-4">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'إجمالي المبيعات (النشاط الأساسي)' : 'Total Sales (Gross)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-success">+ {{ number_format($financialReport['total_sales'], 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'مرتجع المبيعات' : 'Sales Returns' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-danger">- {{ number_format($financialReport['total_returns'], 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr class="bg-light bg-opacity-50" style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-bold text-primary">{{ app()->getLocale() == 'ar' ? 'صافي الإيرادات' : 'Net Revenue' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-primary">{{ number_format($financialReport['net_revenue'], 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'تكلفة البضاعة المباعة (COGS)' : 'Cost of Goods Sold (COGS)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-danger">- {{ number_format($financialReport['total_cogs'], 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr class="bg-light bg-opacity-50" style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-bold text-success">{{ app()->getLocale() == 'ar' ? 'مجمل الأرباح' : 'Gross Profit' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-success">{{ number_format($financialReport['total_sales'] - $financialReport['total_returns'] - $financialReport['total_cogs'], 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'إجمالي المشتريات لتغذية المخزون' : 'Total Purchases (Inventory Feed)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-danger">- {{ number_format($financialReport['total_purchases'], 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                
                                <!-- Detailed Operating Expenses -->
                                <tr class="bg-light bg-opacity-10" style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-bold text-dark">{{ app()->getLocale() == 'ar' ? 'المصروفات التشغيلية بالتفصيل' : 'Operating Expenses (Detailed)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-danger">- {{ number_format($financialReport['total_expenses'], 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                @foreach($financialReport['expense_breakdown'] as $expCat)
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-2 px-4 text-xs text-muted ps-5">— {{ $expCat->type }}</td>
                                    <td class="text-end px-4 font-monospace text-xs text-muted">- {{ number_format($expCat->total, 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                @endforeach

                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'قيمة الهدر والتالف من التسويات' : 'Waste / Defect Value' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-danger">- {{ number_format($financialReport['total_waste'], 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                </tr>
                                @foreach($financialReport['waste_breakdown'] as $wCat)
                                    @php
                                        $wType = $wCat->adjustment_type;
                                        if (app()->getLocale() == 'ar') {
                                            $wLabel = $wType == 'EXPIRED' ? 'منتهي الصلاحية' : ($wType == 'DAMAGED' ? 'تالف' : 'مفقود/ضائع');
                                        } else {
                                            $wLabel = ucfirst(strtolower($wType));
                                        }
                                    @endphp
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td class="py-2 px-4 text-xs text-muted ps-5">— {{ $wLabel }}</td>
                                        <td class="text-end px-4 font-monospace text-xs text-muted">- {{ number_format($wCat->total, 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span></td>
                                    </tr>
                                @endforeach
                                @php
                                    $cProfit = ($financialReport['total_sales'] - $financialReport['total_returns'] - $financialReport['total_cogs']) - $financialReport['total_expenses'] - $financialReport['total_waste'];
                                @endphp
                                <tr class="bg-primary bg-opacity-10" style="border-bottom: 2px solid var(--bs-primary);">
                                    <td class="py-3 px-4 text-xs fw-bold text-dark">{{ app()->getLocale() == 'ar' ? 'صافي الأرباح (الخسائر)' : 'Net Profit (Loss)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs {{ $cProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($cProfit, 2) }} <span style="font-family: 'Cairo', sans-serif; font-weight: normal; font-size: 0.75rem;">{{ $setting->currency }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <!-- Modals for Full View lists -->
            <div class="modal fade" id="allFinancialReportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content glass border-0 rounded-4 shadow">
                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0" id="allFinancialReportModalTitle" style="color: var(--text-color);"></h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4" id="allFinancialReportModalBody">
                            <!-- Dynamic content -->
                        </div>
                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إغلاق' : 'Close' }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="vat-report">
            @php
                $vatKpis = [
                    ['title' => __('pos.total_tax_collected'), 'value' => number_format($vatReport['net_tax_payable'], 2), 'unit' => $setting->currency, 'icon' => 'bank', 'color' => '#3b82f6', 'desc' => __('pos.total_tax_collected')],
                    ['title' => __('pos.taxable_sales'), 'value' => number_format($vatReport['taxable_sales'], 2), 'unit' => $setting->currency, 'icon' => 'shop', 'color' => '#10b981', 'desc' => __('pos.taxable_sales')],
                    ['title' => __('pos.taxable_purchases'), 'value' => number_format($vatReport['taxable_purchases'], 2), 'unit' => $setting->currency, 'icon' => 'cart', 'color' => '#0ea5e9', 'desc' => __('pos.taxable_purchases')],
                    ['title' => __('pos.input_tax'), 'value' => number_format($vatReport['vat_paid'], 2), 'unit' => $setting->currency, 'icon' => 'graph-down', 'color' => '#f59e0b', 'desc' => __('pos.input_tax')],
                    ['title' => __('pos.output_tax'), 'value' => number_format($vatReport['vat_collected'], 2), 'unit' => $setting->currency, 'icon' => 'graph-up', 'color' => '#ef4444', 'desc' => __('pos.output_tax')]
                ];
            @endphp
            <div class="row g-3 mb-4 kpi-carousel-wrapper">
                @foreach($vatKpis as $kpi)
                    <div class="col-6 col-md-4 col-xl-3 kpi-card-col">
                        <div class="card border-0 rounded-3 shadow-sm h-100 py-2 px-3 d-flex flex-row align-items-center gap-2" style="background-color: var(--card-bg); border-left: 3.5px solid {{ $kpi['color'] }} !important; min-height: 72px;" data-bs-toggle="tooltip" title="{{ $kpi['desc'] }}">
                            <!-- Circular Icon Wrapper -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--bs-light); border-color: var(--border-color) !important;">
                                <i class="bi bi-{{ $kpi['icon'] }}" style="color: {{ $kpi['color'] }}; font-size: 0.95rem;"></i>
                            </div>
                            
                            <!-- KPI Content -->
                            <div class="d-flex flex-column justify-content-center text-start">
                                <span class="text-muted text-uppercase fw-bold text-truncate" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    {{ $kpi['title'] }}
                                </span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fw-bold mb-0 text-dark" style="font-size: 1.1rem; line-height: 1.1; font-weight: 800 !important;">
                                        {{ $kpi['value'] }}
                                    </span>
                                    @if($kpi['unit'])
                                        <span class="text-muted fw-semibold ms-1" style="font-size: 0.65rem;">{{ $kpi['unit'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tax Summary Table -->
            <div class="card border-0 rounded-4 shadow-sm mt-4" style="background-color: var(--card-bg);">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0" style="color: var(--text-color);">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'ملخص الإقرار الضريبي' : 'Tax Declaration Summary' }}
                    </h6>
                    <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'ملخص القيمة الضريبية للفترة المحددة' : 'Summary of tax values for the selected period' }}</small>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small">
                                    <th class="py-3 px-4">{{ app()->getLocale() == 'ar' ? 'البند الضريبي / الوصف' : 'Tax Metric / Description' }}</th>
                                    <th class="text-end px-4">{{ app()->getLocale() == 'ar' ? 'المبلغ الخاضع للضريبة / القيمة' : 'Amount' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $salesReturnsVat = $financialReport['total_returns'] > 0 ? ($financialReport['total_returns'] * ($vatReport['vat_rate'] / 100)) : 0;
                                    $purchaseReturnsVat = 0; 
                                    $zeroRatedSales = 0;
                                    $taxExemptSales = 0;
                                    $netTaxPayable = $vatReport['net_tax_payable'];
                                @endphp
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'المبيعات الخاضعة للضريبة' : 'Taxable Sales' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-dark">{{ number_format($vatReport['taxable_sales'], 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'ضريبة المخرجات (ضريبة المبيعات)' : 'Output VAT (Sales Tax)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-danger">+ {{ number_format($vatReport['vat_collected'], 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'المشتريات الخاضعة للضريبة' : 'Taxable Purchases' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-dark">{{ number_format($vatReport['taxable_purchases'], 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'ضريبة المدخلات (ضريبة المشتريات)' : 'Input VAT (Purchase Tax)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-success">- {{ number_format($vatReport['vat_paid'], 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'ضريبة مرتجعات المبيعات' : 'Sales Returns VAT' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-success">- {{ number_format($salesReturnsVat, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'ضريبة مرتجعات المشتريات' : 'Purchase Returns VAT' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-danger">+ {{ number_format($purchaseReturnsVat, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'المبيعات ذات النسبة الصفرية' : 'Zero-Rated Sales' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-muted">{{ number_format($zeroRatedSales, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td class="py-3 px-4 text-xs fw-semibold text-dark">{{ app()->getLocale() == 'ar' ? 'المبيعات المعفاة من الضريبة' : 'Tax-Exempt Sales' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-muted">{{ number_format($taxExemptSales, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span></td>
                                </tr>
                                
                                @if($netTaxPayable >= 0)
                                <tr class="bg-danger bg-opacity-10" style="border-bottom: 2px solid var(--bs-danger);">
                                    <td class="py-3 px-4 text-xs fw-bold text-danger">{{ app()->getLocale() == 'ar' ? 'صافي الضريبة المستحقة للدفع (التزام)' : 'Net Tax Payable (Liability)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-danger">
                                        {{ number_format($netTaxPayable, 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span>
                                    </td>
                                </tr>
                                @else
                                <tr class="bg-success bg-opacity-10" style="border-bottom: 2px solid var(--bs-success);">
                                    <td class="py-3 px-4 text-xs fw-bold text-success">{{ app()->getLocale() == 'ar' ? 'صافي الضريبة المستردة (أصل)' : 'Net Tax Refundable (Asset)' }}</td>
                                    <td class="text-end px-4 fw-bold font-monospace text-xs text-success">
                                        {{ number_format(abs($netTaxPayable), 2) }} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">{{ $setting->currency }}</span>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<style>
    /* Clean layout for charts on mobile */
    @media (max-width: 768px) {
        .chart-scroll-wrapper {
            overflow-x: auto !important;
            width: 100% !important;
            -webkit-overflow-scrolling: touch;
        }
        .chart-inner-container {
            min-width: 700px !important;
            width: 700px !important;
            max-width: none !important;
        }
        .chart-inner-container canvas {
            max-width: none !important;
            width: 700px !important;
        }
    }

    /* Welcome Hero Card (Stunning Slate Indigo theme) */
    .welcome-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        border-radius: 24px !important;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 10px 10px -5px rgba(15, 23, 42, 0.04) !important;
    }
    html[data-app-theme="dark"] .welcome-hero-card {
        background: linear-gradient(135deg, #090e1a 0%, #0f172a 100%) !important;
    }
    .welcome-title {
        letter-spacing: -0.5px;
        color: #ffffff !important;
    }
    .welcome-subtitle {
        color: #94a3b8 !important;
    }
    .text-primary-light {
        color: #60a5fa !important;
    }

    @media (max-width: 576px) {
        .welcome-hero-card {
            padding: 16px 16px !important;
            margin-bottom: 16px !important;
            border-radius: 16px !important;
        }
        .welcome-hero-card .welcome-title {
            font-size: 1.15rem !important;
        }
        .welcome-hero-card .welcome-subtitle {
            font-size: 0.72rem !important;
            margin-bottom: 4px !important;
        }
        /* Make form elements and buttons compact and properly sized */
        .welcome-hero-card .d-flex.flex-wrap {
            gap: 8px !important;
        }
        .welcome-hero-card form {
            width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        .welcome-hero-card form > div {
            flex: 1 1 100% !important;
        }
        .welcome-hero-card form select {
            width: 100% !important;
            height: 38px !important;
            font-size: 0.8rem !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            border-radius: 10px !important;
        }
        .welcome-hero-card form .d-flex.align-items-center.gap-2.px-3 {
            width: calc(100% - 46px) !important;
            flex: 1 !important;
            height: 38px !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
            border-radius: 10px !important;
        }
        .welcome-hero-card form .d-flex.align-items-center.gap-2.px-3 input[type="date"] {
            width: 45% !important;
            font-size: 0.78rem !important;
        }
        .welcome-hero-card form button[type="submit"] {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
            padding: 0 !important;
        }
        .welcome-hero-card .dropdown {
            width: 100% !important;
        }
        .welcome-hero-card .dropdown button {
            width: 100% !important;
            height: 38px !important;
            font-size: 0.8rem !important;
            border-radius: 10px !important;
            justify-content: center !important;
        }
    }

    /* Glowing blobs in background */
    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.15;
        z-index: 0;
    }
    .blob-1 {
        width: 150px;
        height: 150px;
        background: #3b82f6;
        top: -20px;
        right: 10%;
    }
    .blob-2 {
        width: 200px;
        height: 200px;
        background: #10b981;
        bottom: -50px;
        left: 20%;
    }

    .glass-header-card {
        background: rgba(255, 255, 255, 0.35) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.45) !important;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05) !important;
    }
    html[data-app-theme="dark"] .glass-header-card {
        background: rgba(15, 23, 42, 0.45) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3) !important;
    }

    /* KPI Carousel for Mobile Swipeable Layout */
    @media (max-width: 767.98px) {
        .kpi-carousel-wrapper {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            scroll-snap-type: x mandatory !important;
            -webkit-overflow-scrolling: touch !important;
            gap: 12px;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .kpi-carousel-wrapper::-webkit-scrollbar {
            height: 4px;
        }
        .kpi-carousel-wrapper::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }
        .kpi-carousel-wrapper .kpi-card-col {
            flex: 0 0 85% !important;
            max-width: 85% !important;
            scroll-snap-align: start !important;
        }
    }

    .custom-report-tabs .nav-link { 
        border: none; 
        color: var(--text-muted); 
        background: transparent;
        transition: all 0.25s ease-in-out;
    }
    #movementSpeedTabs .nav-link {
        border-bottom: 2px solid transparent !important;
        color: var(--text-muted) !important;
        transition: all 0.2s ease-in-out;
    }
    #movementSpeedTabs .nav-link.active {
        border-bottom: 2px solid var(--primary-color) !important;
        color: var(--primary-color) !important;
    }
    .custom-report-tabs .nav-link .tab-icon-wrapper {
        background-color: rgba(108, 117, 125, 0.05);
        color: var(--text-muted);
        transition: all 0.25s ease-in-out;
    }
    .custom-report-tabs .nav-link:hover .tab-icon-wrapper {
        background-color: rgba(30, 136, 229, 0.08);
        color: var(--primary-color);
    }
    .custom-report-tabs .nav-link.active { 
        background-color: rgba(30, 136, 229, 0.06) !important; 
        color: var(--primary-color) !important; 
    }
    .custom-report-tabs .nav-link.active .tab-icon-wrapper {
        background-color: rgba(30, 136, 229, 0.12) !important;
        color: var(--primary-color) !important;
    }
    .custom-report-tabs::-webkit-scrollbar {
        display: none;
    }
    
    @media (max-width: 767.98px) {
        .card-body tbody tr:nth-child(n+4) {
            display: none !important;
        }
    }
    
    @media print {
        .navbar, #sidebar, .btn-group, form, .nav-pills, .bi, .no-print { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        #content { margin: 0 !important; width: 100% !important; }
        .container-fluid { width: 100% !important; padding: 0 !important; }
    }

    /* Dark Mode Overrides for Reports Page */
    html[data-app-theme="dark"] .glass-modal .modal-content {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .glass-modal .modal-header {
        background: #0f172a !important;
        border-bottom-color: #334155 !important;
    }
    html[data-app-theme="dark"] .glass-modal .modal-body {
        background: #0f172a !important;
    }
    html[data-app-theme="dark"] .glass-modal .glass-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .glass-modal .list-group-item {
        background: #0f172a !important;
        border-bottom-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .glass-modal .list-group-item .fw-bold,
    html[data-app-theme="dark"] .glass-modal .list-group-item .fw-semibold,
    html[data-app-theme="dark"] .glass-modal .list-group-item span {
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .text-dark {
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .fw-semibold.text-xs,
    html[data-app-theme="dark"] .fw-semibold.small {
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .text-muted.text-xxs,
    html[data-app-theme="dark"] .text-xxs {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .border-bottom {
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .table td,
    html[data-app-theme="dark"] .table th {
        color: #e2e8f0 !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .table thead th {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .table tr:hover td {
        background: #1e293b !important;
    }
    html[data-app-theme="dark"] .table-borderless td {
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .rounded-circle.bg-warning.bg-opacity-10 {
        background-color: rgba(245, 158, 11, 0.15) !important;
    }
    html[data-app-theme="dark"] .rounded-circle.bg-primary.bg-opacity-10 {
        background-color: rgba(59, 130, 246, 0.15) !important;
    }
    html[data-app-theme="dark"] .rounded-circle.bg-success.bg-opacity-10 {
        background-color: rgba(16, 185, 129, 0.15) !important;
    }
    html[data-app-theme="dark"] .nav-pills .nav-link:not(.active) {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .form-control,
    html[data-app-theme="dark"] .form-select {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] .btn-outline-secondary {
        color: #94a3b8 !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .btn-outline-secondary:hover {
        background: #1e293b !important;
        color: #e2e8f0 !important;
    }
    html[data-app-theme="dark"] small.text-muted,
    html[data-app-theme="dark"] span.text-muted {
        color: #94a3b8 !important;
    }

    /* Fix list-group-item white backgrounds in dark mode */
    html[data-app-theme="dark"] .list-group-item {
        background-color: var(--card-bg) !important;
        border-color: var(--border-color) !important;
        color: var(--text-color) !important;
    }
    html[data-app-theme="dark"] .list-group-item .text-dark,
    html[data-app-theme="dark"] .list-group-item .fw-bold.text-dark {
        color: var(--text-color) !important;
    }
    html[data-app-theme="dark"] .card-body {
        background-color: var(--card-bg) !important;
    }
    html[data-app-theme="dark"] .card-header {
        background-color: var(--card-bg) !important;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Sales Trend Interactive Chart
    const rawSalesData = @json($salesReport['sales_by_day']);
    const filters = @json($filters);
    
    const processedData = {
        daily: { labels: [], data: [] },
        weekly: { labels: [], data: [] },
        monthly: { labels: [], data: [] },
        yearly: { labels: [], data: [] }
    };
    
    // Generate full daily calendar sequence between from_date and to_date
    const startDate = new Date(filters.from_date);
    const endDate = new Date(filters.to_date);
    
    const dailyMap = {};
    const weeklyMap = {};
    const monthlyMap = {};
    const yearlyMap = {};
    
    let cur = new Date(startDate);
    while (cur <= endDate) {
        const year = cur.getFullYear();
        const month = String(cur.getMonth() + 1).padStart(2, '0');
        const day = String(cur.getDate()).padStart(2, '0');
        const dateKey = `${year}-${month}-${day}`;
        
        const val = parseFloat(rawSalesData[dateKey] || 0);
        dailyMap[dateKey] = val;
        
        // Month key: YYYY-MM
        const monthKey = `${year}-${month}`;
        monthlyMap[monthKey] = (monthlyMap[monthKey] || 0) + val;
        
        // Year key: YYYY
        const yearKey = `${year}`;
        yearlyMap[yearKey] = (yearlyMap[yearKey] || 0) + val;
        
        // ISO Week key
        const tempDate = new Date(cur.getTime());
        tempDate.setHours(0, 0, 0, 0);
        tempDate.setDate(tempDate.getDate() + 3 - (tempDate.getDay() + 6) % 7);
        const week1 = new Date(tempDate.getFullYear(), 0, 4);
        const weekNum = 1 + Math.round(((tempDate.getTime() - week1.getTime()) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
        const weekKey = `${tempDate.getFullYear()}-W${String(weekNum).padStart(2, '0')}`;
        weeklyMap[weekKey] = (weeklyMap[weekKey] || 0) + val;
        
        // Increment day
        cur.setDate(cur.getDate() + 1);
    }
    
    // Compile Daily
    Object.keys(dailyMap).sort().forEach(k => {
        processedData.daily.labels.push(k);
        processedData.daily.data.push(dailyMap[k]);
    });
    
    // Compile Weekly
    Object.keys(weeklyMap).sort().forEach(k => {
        processedData.weekly.labels.push(k);
        processedData.weekly.data.push(weeklyMap[k]);
    });
    
    // Compile Monthly
    Object.keys(monthlyMap).sort().forEach(k => {
        processedData.monthly.labels.push(k);
        processedData.monthly.data.push(monthlyMap[k]);
    });
    
    // Compile Yearly
    Object.keys(yearlyMap).sort().forEach(k => {
        processedData.yearly.labels.push(k);
        processedData.yearly.data.push(yearlyMap[k]);
    });

    const trendEl = document.getElementById('interactiveSalesTrendChart');
    let trendChart = null;
    if (trendEl) {
        trendChart = new Chart(trendEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: processedData.daily.labels,
                datasets: [{
                    label: "{{ __('pos.total_sales') }}",
                    data: processedData.daily.data,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#3b82f6',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { padding: 12, cornerRadius: 8 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                if (typeof label === 'string' && label.length === 10 && label.includes('-')) {
                                    const parts = label.split('-');
                                    return `${parts[1]}-${parts[2]}`;
                                }
                                return label;
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: { 
                        position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}",
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5] } 
                    }
                }
            }
        });
    }

    window.switchTrendPeriod = function(period, btn) {
        // Toggle Active state on buttons
        const btnGroup = btn.parentElement;
        btnGroup.querySelectorAll('button').forEach(b => {
            b.classList.remove('active', 'text-dark');
            b.classList.add('text-muted');
        });
        btn.classList.add('active');
        btn.classList.remove('text-muted');

        // Swap labels & data
        if (trendChart) {
            trendChart.data.labels = processedData[period].labels;
            trendChart.data.datasets[0].data = processedData[period].data;
            trendChart.update('active');
        }
    };

    // 2. Sales Distribution Charts
    // Category distribution
    const categoryData = @json($salesReport['sales_by_category'] ?? []);
    const distCatCtx = document.getElementById('distributionCategoryChart');
    if (distCatCtx) {
        new Chart(distCatCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: categoryData.map(c => c.name),
                datasets: [{
                    data: categoryData.map(c => c.total),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, color: (document.documentElement.getAttribute('data-app-theme') === 'dark' ? '#ffffff' : '#475569'), font: { size: 12, weight: 'bold' } } } },
                cutout: '65%'
            }
        });
    }

    // Payment method distribution
    const paymentData = @json($salesReport['sales_by_payment_method'] ?? []);
    const distPayCtx = document.getElementById('distributionPaymentChart');
    if (distPayCtx) {
        new Chart(distPayCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: paymentData.map(p => p.payment_method_label),
                datasets: [{
                    data: paymentData.map(p => p.total),
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#6b7280'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, color: (document.documentElement.getAttribute('data-app-theme') === 'dark' ? '#ffffff' : '#475569'), font: { size: 12, weight: 'bold' } } } },
                cutout: '65%'
            }
        });
    }

    // Branch distribution
    const branchData = @json($salesReport['sales_by_branch'] ?? []);
    const distBranchCtx = document.getElementById('distributionBranchChart');
    if (distBranchCtx) {
        new Chart(distBranchCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: branchData.map(b => b.name),
                datasets: [{
                    data: branchData.map(b => b.total),
                    backgroundColor: ['#6366f1', '#a855f7', '#ec4899', '#f43f5e'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, color: (document.documentElement.getAttribute('data-app-theme') === 'dark' ? '#ffffff' : '#475569'), font: { size: 12, weight: 'bold' } } } },
                cutout: '65%'
            }
        });
    }

    // 3. Client-side sales history search & filter logic
    window.filterSalesHistoryTable = function() {
        const query = document.getElementById('salesHistorySearch').value.toLowerCase().trim();
        const paymentVal = document.getElementById('salesHistoryPaymentFilter').value;
        const rows = document.querySelectorAll('#salesHistoryTable tbody tr.sales-history-row');

        rows.forEach(row => {
            const invoiceNoEl = row.cells[0];
            const customerNameEl = row.cells[2];
            const invoiceNo = invoiceNoEl ? invoiceNoEl.textContent.toLowerCase() : '';
            const customerName = customerNameEl ? customerNameEl.textContent.toLowerCase() : '';
            
            const rowPaymentMethod = row.getAttribute('data-payment-method');

            const matchesSearch = invoiceNo.includes(query) || customerName.includes(query);
            const matchesPayment = (paymentVal === 'all') || (rowPaymentMethod === paymentVal);

            if (matchesSearch && matchesPayment) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    // Client-side sorting logic
    let sortDirections = {};
    window.sortSalesHistoryTable = function(colIndex) {
        const table = document.getElementById('salesHistoryTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr.sales-history-row'));
        
        // Determine sorting order
        sortDirections[colIndex] = !sortDirections[colIndex];
        const asc = sortDirections[colIndex];

        rows.sort((a, b) => {
            const aCell = a.getElementsByTagName('td')[colIndex];
            const bCell = b.getElementsByTagName('td')[colIndex];
            
            let aVal = aCell.getAttribute('data-sort-value') || aCell.textContent.trim();
            let bVal = bCell.getAttribute('data-sort-value') || bCell.textContent.trim();

            // Handle numbers
            if (!isNaN(parseFloat(aVal)) && !isNaN(parseFloat(bVal))) {
                return asc ? parseFloat(aVal) - parseFloat(bVal) : parseFloat(bVal) - parseFloat(aVal);
            }
            // Handle strings
            return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        });

        tbody.innerHTML = '';
        rows.forEach(r => tbody.appendChild(r));
    };

    // Profit Analysis Chart
    const profitEl = document.getElementById('profitChart');
    if (profitEl) {
        new Chart(profitEl.getContext('2d'), {
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
                scales: { y: { position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}", beginAtZero: true } }
            }
        });
    }

    // Dynamic AJAX limits fetching function for TOP analytics sections
    window.changeTopAnalyticsLimit = function(type, limit, selectElement) {
        let activeType = type;
        const card = selectElement.closest('.card');
        const tbody = card.querySelector('.tab-pane.active tbody') || card.querySelector('tbody');
        
        if (type === 'movement') {
            const activeTab = card.querySelector('.nav-link.active');
            if (activeTab && activeTab.id.includes('slow')) {
                activeType = 'slow-moving';
            } else {
                activeType = 'fast-moving';
            }
        }
        
        const colCount = tbody.closest('table').querySelectorAll('thead th').length;
        tbody.innerHTML = `
            <tr>
                <td colspan="${colCount}" class="py-4 text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </td>
            </tr>
        `;
        
        const filters = @json($filters);
        let url = `{{ route('reports.top_analytics') }}?type=${activeType}&limit=${limit}`;
        for (const [key, value] of Object.entries(filters)) {
            if (value !== null && key !== 'type') {
                url += `&${key}=${encodeURIComponent(value)}`;
            }
        }
        
        fetch(url)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted py-3 small">{{ app()->getLocale() == 'ar' ? 'لا توجد بيانات' : 'No data available' }}</td></tr>`;
                    return;
                }
                
                let maxVal = 1;
                if (activeType === 'top-selling') {
                    maxVal = Math.max(...data.map(item => parseFloat(item.total_quantity || 0))) || 1;
                }
                
                data.forEach((item, index) => {
                    let tr = document.createElement('tr');
                    tr.className = 'border-bottom';
                    tr.style.borderColor = 'var(--border-color)';
                    
                    let html = '';
                    if (activeType === 'top-customers') {
                        html += `
                            <td class="py-2 fw-semibold small">${item.customer_name || item.name}</td>
                            <td class="py-2 text-center fw-bold text-primary small">${Number(item.invoices_count).toLocaleString()}</td>
                            <td class="py-2 text-center fw-semibold text-info small">${Number(item.average_order_value).toFixed(2)}</td>
                            <td class="py-2 text-end fw-bold text-success small">${Number(item.total_spent).toFixed(2)}</td>
                        `;
                    } else {
                        let imgHtml = '';
                        if (item.image) {
                            imgHtml = `<img src="/storage/${item.image}" class="rounded-2" style="width: 32px; height: 32px; object-fit: cover;">`;
                        } else {
                            imgHtml = `
                                <div class="bg-light rounded-2 d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;">
                                    <i class="bi bi-image" style="font-size: 0.8rem;"></i>
                                </div>
                            `;
                        }
                        
                        html += `
                            <td class="py-2 d-flex align-items-center gap-2">
                                ${imgHtml}
                                <span class="fw-semibold small" style="white-space: normal; word-break: break-word; line-height: 1.3;">${item.name}</span>
                            </td>
                        `;
                        
                        if (activeType === 'top-selling') {
                            let percent = (parseFloat(item.total_quantity || 0) / maxVal) * 100;
                            html += `
                                <td class="py-2 text-center fw-bold text-primary small">${Number(item.total_quantity).toLocaleString()}</td>
                                <td class="py-2 text-end fw-semibold small text-success">${Number(item.total_revenue).toFixed(2)}</td>
                                <td class="py-2">
                                    <div class="progress rounded-pill" style="height: 5px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: ${percent}%"></div>
                                    </div>
                                </td>
                            `;
                        } else if (activeType === 'top-profitable' || activeType === 'least-profitable') {
                            let marginClass = activeType === 'top-profitable' ? 'success' : 'danger';
                            html += `
                                <td class="py-2 text-center fw-bold text-${marginClass} small">${Number(item.total_profit).toFixed(2)}</td>
                                <td class="py-2 text-end"><span class="badge bg-${marginClass} bg-opacity-10 text-${marginClass} fw-bold small">${item.profit_margin}%</span></td>
                                <td class="py-2 text-end fw-semibold small">${Number(item.total_revenue).toFixed(2)}</td>
                            `;
                        } else if (activeType === 'fast-moving' || activeType === 'slow-moving') {
                            let statusClass = parseFloat(item.total_quantity || 0) >= 15 ? 'success' : 'warning';
                            html += `
                                <td class="py-2 text-center fw-bold small text-${statusClass}">${Number(item.total_quantity).toLocaleString()}</td>
                                <td class="py-2 text-end fw-semibold small">${Number(item.current_stock).toLocaleString()}</td>
                                <td class="py-2 text-end"><span class="badge bg-${statusClass} bg-opacity-10 text-${statusClass} fw-bold small">${item.movement_status}</span></td>
                            `;
                        }
                    }
                    
                    tr.innerHTML = html;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger py-3 small">{{ app()->getLocale() == 'ar' ? 'حدث خطأ أثناء تحميل البيانات' : 'Error loading data' }}</td></tr>`;
            });
    };

    // Handle Tab Persistence & Export Links Update
    document.addEventListener('DOMContentLoaded', function () {
        const reportTabs = document.querySelectorAll('#reportTabs button[data-bs-toggle="tab"]');
        const pdfBtn = document.getElementById('export-pdf-btn');
        const excelBtn = document.getElementById('export-excel-btn');
        const filters = @json($filters);

        // Product movement tab View All link update
        const fastTabBtn = document.getElementById('fast-moving-tab');
        const slowTabBtn = document.getElementById('slow-moving-tab');
        const movementViewAll = document.getElementById('productMovementViewAllLink');
        
        if (fastTabBtn && movementViewAll) {
            fastTabBtn.addEventListener('shown.bs.tab', function() {
                let base = "{{ route('reports.detailed', ['type' => 'fast-moving']) }}";
                let params = new URLSearchParams();
                for (const [key, value] of Object.entries(filters)) {
                    if (value !== null && key !== 'type') params.append(key, value);
                }
                movementViewAll.href = `${base}&${params.toString()}`;
            });
        }
        if (slowTabBtn && movementViewAll) {
            slowTabBtn.addEventListener('shown.bs.tab', function() {
                let base = "{{ route('reports.detailed', ['type' => 'slow-moving']) }}";
                let params = new URLSearchParams();
                for (const [key, value] of Object.entries(filters)) {
                    if (value !== null && key !== 'type') params.append(key, value);
                }
                movementViewAll.href = `${base}&${params.toString()}`;
            });
        }

        function updateExportLinks(activeTabId) {
            let type = 'sales';
            if (activeTabId === 'sales-report') type = 'sales';
            else if (activeTabId === 'purchase-report') type = 'purchases';
            else if (activeTabId === 'inventory-report') type = 'inventory';
            else if (activeTabId === 'customer-report') type = 'customers';
            else if (activeTabId === 'supplier-report') type = 'suppliers';
            else if (activeTabId === 'expenses-report') type = 'expenses';
            else if (activeTabId === 'financial-report') type = 'financial';
            else if (activeTabId === 'vat-report') type = 'vat';

            let queryParams = new URLSearchParams();
            for (const [key, value] of Object.entries(filters)) {
                if (value !== null && key !== 'type') {
                    queryParams.append(key, value);
                }
            }

            let baseExportUrl = "{{ route('reports.export', ['type' => 'TYPE_PLACEHOLDER']) }}";
            let exportUrl = baseExportUrl.replace('TYPE_PLACEHOLDER', type);
            
            pdfBtn.href = `${exportUrl}?${queryParams.toString()}&format=pdf`;
            excelBtn.href = `${exportUrl}?${queryParams.toString()}&format=excel`;
        }

    // --------------------------------------------------
    // PURCHASES REPORT ANALYTICS CHARTS & LOGIC (2026 ERP Style)
    // --------------------------------------------------

    // 1. Purchase Trend Chart
    const rawPurchaseData = @json($purchaseReport['purchases_by_day'] ?? []);
    const rawPurchaseOrdersData = @json($purchaseReport['purchase_orders_by_day'] ?? []);
    
    const purchaseProcessedData = {
        daily: { labels: [], amount: [], count: [] },
        weekly: { labels: [], amount: [], count: [] },
        monthly: { labels: [], amount: [], count: [] },
        yearly: { labels: [], amount: [], count: [] }
    };
    
    const pStartDate = new Date(filters.from_date);
    const pEndDate = new Date(filters.to_date);
    
    const pDailyMap = {};
    const pDailyOrdersMap = {};
    const pWeeklyMap = {};
    const pWeeklyOrdersMap = {};
    const pMonthlyMap = {};
    const pMonthlyOrdersMap = {};
    const pYearlyMap = {};
    const pYearlyOrdersMap = {};
    
    let pCur = new Date(pStartDate);
    while (pCur <= pEndDate) {
        const year = pCur.getFullYear();
        const month = String(pCur.getMonth() + 1).padStart(2, '0');
        const day = String(pCur.getDate()).padStart(2, '0');
        const dateKey = `${year}-${month}-${day}`;
        
        const amountVal = parseFloat(rawPurchaseData[dateKey] || 0);
        const countVal = parseInt(rawPurchaseOrdersData[dateKey] || 0);
        
        pDailyMap[dateKey] = amountVal;
        pDailyOrdersMap[dateKey] = countVal;
        
        const monthKey = `${year}-${month}`;
        pMonthlyMap[monthKey] = (pMonthlyMap[monthKey] || 0) + amountVal;
        pMonthlyOrdersMap[monthKey] = (pMonthlyOrdersMap[monthKey] || 0) + countVal;
        
        const yearKey = `${year}`;
        pYearlyMap[yearKey] = (pYearlyMap[yearKey] || 0) + amountVal;
        pYearlyOrdersMap[yearKey] = (pYearlyOrdersMap[yearKey] || 0) + countVal;
        
        const tempDate = new Date(pCur.getTime());
        tempDate.setHours(0, 0, 0, 0);
        tempDate.setDate(tempDate.getDate() + 3 - (tempDate.getDay() + 6) % 7);
        const week1 = new Date(tempDate.getFullYear(), 0, 4);
        const weekNum = 1 + Math.round(((tempDate.getTime() - week1.getTime()) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
        const weekKey = `${tempDate.getFullYear()}-W${String(weekNum).padStart(2, '0')}`;
        pWeeklyMap[weekKey] = (pWeeklyMap[weekKey] || 0) + amountVal;
        pWeeklyOrdersMap[weekKey] = (pWeeklyOrdersMap[weekKey] || 0) + countVal;
        
        pCur.setDate(pCur.getDate() + 1);
    }
    
    Object.keys(pDailyMap).sort().forEach(k => {
        purchaseProcessedData.daily.labels.push(k);
        purchaseProcessedData.daily.amount.push(pDailyMap[k]);
        purchaseProcessedData.daily.count.push(pDailyOrdersMap[k]);
    });
    Object.keys(pWeeklyMap).sort().forEach(k => {
        purchaseProcessedData.weekly.labels.push(k);
        purchaseProcessedData.weekly.amount.push(pWeeklyMap[k]);
        purchaseProcessedData.weekly.count.push(pWeeklyOrdersMap[k]);
    });
    Object.keys(pMonthlyMap).sort().forEach(k => {
        purchaseProcessedData.monthly.labels.push(k);
        purchaseProcessedData.monthly.amount.push(pMonthlyMap[k]);
        purchaseProcessedData.monthly.count.push(pMonthlyOrdersMap[k]);
    });
    Object.keys(pYearlyMap).sort().forEach(k => {
        purchaseProcessedData.yearly.labels.push(k);
        purchaseProcessedData.yearly.amount.push(pYearlyMap[k]);
        purchaseProcessedData.yearly.count.push(pYearlyOrdersMap[k]);
    });

    const pTrendEl = document.getElementById('interactivePurchaseTrendChart');
    let purchaseTrendChart = null;
    if (pTrendEl) {
        purchaseTrendChart = new Chart(pTrendEl.getContext('2d'), {
        type: 'line',
        data: {
            labels: purchaseProcessedData.daily.labels,
            datasets: [
                {
                    label: "{{ app()->getLocale() == 'ar' ? 'قيمة المشتريات' : 'Purchase Amount' }}",
                    data: purchaseProcessedData.daily.amount,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#10b981',
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { padding: 12, cornerRadius: 8 }
            },
            scales: {
                x: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                if (typeof label === 'string' && label.length === 10 && label.includes('-')) {
                                    const parts = label.split('-');
                                    return `${parts[1]}-${parts[2]}`;
                                }
                                return label;
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                y: { 
                    type: 'linear',
                    position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}",
                    display: true,
                    grid: { borderDash: [5, 5] },
                    beginAtZero: true
                }
            }
        }
    });
    }

    window.switchPurchaseTrendPeriod = function(period, btn) {
        const btnGroup = btn.parentElement;
        btnGroup.querySelectorAll('button').forEach(b => {
            b.classList.remove('active', 'text-dark');
            b.classList.add('text-muted');
        });
        btn.classList.add('active');
        btn.classList.remove('text-muted');

        if (purchaseTrendChart) {
            purchaseTrendChart.data.labels = purchaseProcessedData[period].labels;
            purchaseTrendChart.data.datasets[0].data = purchaseProcessedData[period].amount;
            purchaseTrendChart.update('active');
        }
    };

    // 2. Supplier Share Donut Chart
    const rawSupData = @json($purchaseReport['purchases_by_supplier'] ?? []);
    const supShareCtx = document.getElementById('supplierShareChart');
    if (supShareCtx) {
        new Chart(supShareCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: rawSupData.map(s => s.name),
                datasets: [{
                    data: rawSupData.map(s => s.total),
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 13, weight: 'bold' } } } },
                cutout: '65%'
            }
        });
    }

    // 4. Purchases Distribution Charts
    const pCatData = @json($purchaseReport['purchases_by_category'] ?? []);
    const pCatCtx = document.getElementById('purchaseCategoryChart');
    if (pCatCtx) {
        new Chart(pCatCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: pCatData.map(c => c.name),
                datasets: [{
                    data: pCatData.map(c => c.total),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 13, weight: 'bold' } } } },
                cutout: '60%'
            }
        });
    }

    const pPayData = @json($purchaseReport['purchases_by_payment_method'] ?? []);
    const pPayCtx = document.getElementById('purchasePaymentChart');
    if (pPayCtx) {
        new Chart(pPayCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: pPayData.map(p => p.payment_method_label),
                datasets: [{
                    data: pPayData.map(p => p.total),
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#6b7280'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 13, weight: 'bold' } } } },
                cutout: '60%'
            }
        });
    }

    const pBranchData = @json($purchaseReport['purchases_by_branch'] ?? []);
    const pBranchCtx = document.getElementById('purchaseBranchChart');
    if (pBranchCtx) {
        new Chart(pBranchCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: pBranchData.map(b => b.name),
                datasets: [{
                    data: pBranchData.map(b => b.total),
                    backgroundColor: ['#6366f1', '#a855f7', '#ec4899', '#f43f5e'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 13, weight: 'bold' } } } },
                cutout: '60%'
            }
        });
    }




    // 6. Purchase History Search and Filter
    window.filterPurchaseHistoryTable = function() {
        const query = document.getElementById('purchaseHistorySearch').value.toLowerCase().trim();
        const paymentVal = document.getElementById('purchaseHistoryPaymentFilter').value;
        const rows = document.querySelectorAll('#purchaseHistoryTable tbody tr.purchase-history-row');

        rows.forEach(row => {
            const invoiceNo = row.cells[0].textContent.toLowerCase();
            const supplierName = row.cells[2].textContent.toLowerCase();
            const rowPaymentMethod = row.getAttribute('data-payment-method');

            const matchesSearch = invoiceNo.includes(query) || supplierName.includes(query);
            const matchesPayment = (paymentVal === 'all') || (rowPaymentMethod === paymentVal);

            if (matchesSearch && matchesPayment) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };


    // Initialize stock health donut
    const expiredCount = {{ $inventoryReport['expired_count'] }};
    const expiringSoonCount = {{ $inventoryReport['expiring_soon']->count() }};
    const totalProducts = {{ $inventoryReport['total_products'] }};
    const healthyCount = Math.max(0, totalProducts - expiredCount - expiringSoonCount);

    const catCtx = document.getElementById('inventoryCategoryChart');
    if (catCtx) {
        new Chart(catCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [
                    "{{ app()->getLocale() == 'ar' ? 'أصناف تالفة/منتهية' : 'Expired Stock' }}",
                    "{{ app()->getLocale() == 'ar' ? 'أصناف حرجة (شبه منتهية)' : 'Expiring Soon' }}",
                    "{{ app()->getLocale() == 'ar' ? 'أصناف آمنة ومستقرة' : 'Healthy Stock' }}"
                ],
                datasets: [{
                    data: [expiredCount, expiringSoonCount, healthyCount],
                    backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });
    }

    // Client-side transactions table filter
    window.filterInventoryTxTable = function() {
        const query = document.getElementById('inventoryTxSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#inventoryTxTable tbody tr.inventory-tx-row');
        rows.forEach(row => {
            const productName = row.cells[1].textContent.toLowerCase();
            const refNo = row.cells[4].textContent.toLowerCase();
            if (productName.includes(query) || refNo.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };



    // ==========================================
    // CUSTOMER REPORT REDESIGN SCRIPTS
    // ==========================================
    const growthData = @json($customerReport['growth'] ?? []);
    const byTypeData = @json($customerReport['byType'] ?? []);
    const byCityData = @json($customerReport['byCity'] ?? []);
    const allCustomers = @json($customerReport['customers'] ?? []);
    const inactiveCustomers = @json($customerReport['inactiveCustomers'] ?? []);
    const currencySym = "{{ $setting->currency }}";

    const fromDateStr = "{{ $filters['from_date'] ?? now()->startOfMonth()->format('Y-m-d') }}";
    const toDateStr = "{{ $filters['to_date'] ?? now()->format('Y-m-d') }}";

    function getDatesInRange(startDate, endDate) {
        const date = new Date(startDate);
        const dates = [];
        const end = new Date(endDate);
        while (date <= end) {
            dates.push(new Date(date).toISOString().split('T')[0]);
            date.setDate(date.getDate() + 1);
        }
        return dates;
    }

    function getMonthsInRange(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const months = [];
        while (start <= end) {
            const year = start.getFullYear();
            const month = String(start.getMonth() + 1).padStart(2, '0');
            const period = `${year}-${month}`;
            if (!months.includes(period)) {
                months.push(period);
            }
            start.setMonth(start.getMonth() + 1);
        }
        return months;
    }

    // 1. Customer Growth Chart Setup
    const dailyLabels = getDatesInRange(fromDateStr, toDateStr);
    const dailyNewData = dailyLabels.map(d => (growthData.dailyNew && growthData.dailyNew[d]) ? growthData.dailyNew[d] : 0);
    const dailyRetData = dailyLabels.map(d => (growthData.dailyReturning && growthData.dailyReturning[d]) ? growthData.dailyReturning[d] : 0);

    const monthlyLabels = getMonthsInRange(fromDateStr, toDateStr);
    const monthlyNewData = monthlyLabels.map(m => (growthData.monthlyNew && growthData.monthlyNew[m]) ? growthData.monthlyNew[m] : 0);
    const monthlyRetData = monthlyLabels.map(m => (growthData.monthlyReturning && growthData.monthlyReturning[m]) ? growthData.monthlyReturning[m] : 0);

    const growthCtx = document.getElementById('customerGrowthChart');
    let customerGrowthChart = null;

    if (growthCtx) {
        customerGrowthChart = new Chart(growthCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [
                    {
                        label: "{{ app()->getLocale() == 'ar' ? 'عملاء جدد' : 'New Customers' }}",
                        data: dailyNewData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: "{{ app()->getLocale() == 'ar' ? 'عملاء عائدون' : 'Returning Customers' }}",
                        data: dailyRetData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { padding: 12, cornerRadius: 8 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                if (typeof label === 'string' && label.length === 10 && label.includes('-')) {
                                    const parts = label.split('-');
                                    return `${parts[1]}-${parts[2]}`;
                                }
                                return label;
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: { 
                        position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}",
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5] } 
                    }
                }
            }
        });
    }

    window.switchCustomerGrowthPeriod = function(period) {
        const selector = document.getElementById('growthPeriodSelector');
        selector.querySelectorAll('button').forEach(btn => btn.classList.replace('btn-primary', 'btn-light'));
        
        if (period === 'daily') {
            event.target.classList.replace('btn-light', 'btn-primary');
            customerGrowthChart.data.labels = dailyLabels;
            customerGrowthChart.data.datasets[0].data = dailyNewData;
            customerGrowthChart.data.datasets[1].data = dailyRetData;
        } else {
            event.target.classList.replace('btn-light', 'btn-primary');
            customerGrowthChart.data.labels = monthlyLabels;
            customerGrowthChart.data.datasets[0].data = monthlyNewData;
            customerGrowthChart.data.datasets[1].data = monthlyRetData;
        }
        customerGrowthChart.update();
    };

    // 2. Distributions Setup
    const typeCtx = document.getElementById('customerTypeChart');
    if (typeCtx) {
        new Chart(typeCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: byTypeData.map(t => t.customer_type || 'General'),
                datasets: [{
                    data: byTypeData.map(t => t.count),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ec4899'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });
    }

    const cityCtx = document.getElementById('customerCityChart');
    if (cityCtx) {
        new Chart(cityCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: byCityData.map(c => c.city || 'Other'),
                datasets: [{
                    data: byCityData.map(c => c.count),
                    backgroundColor: ['#8b5cf6', '#ef4444', '#10b981', '#f59e0b', '#3b82f6'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });
    }

    // 3. Filter Table
    window.filterCustomerListTable = function() {
        const query = document.getElementById('custTableSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#customerListTable tbody tr.customer-row-item');
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const phone = row.getAttribute('data-phone');
            if (name.includes(query) || phone.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    // 4. View Profile Modal Preview
    window.previewCustomerProfile = function(c) {
        const modalBody = document.getElementById('customerProfileModalBody');
        const langAr = "{{ app()->getLocale() == 'ar' }}";

        const formattedSpent = parseFloat(c.total_purchases || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
        const formattedBal = parseFloat(c.balance || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
        const formattedTicket = (c.visits > 0 ? parseFloat(c.total_purchases / c.visits) : 0).toLocaleString(undefined, {minimumFractionDigits: 2});

        const visitsLabel = langAr ? 'زيارات' : 'visits';
        const spentLabel = langAr ? 'إجمالي المشتريات' : 'Total Spent';
        const balLabel = langAr ? 'الديون المتبقية' : 'Remaining Balance';
        const statusActive = langAr ? 'نشط' : 'Active';
        const statusInactive = langAr ? 'خامل' : 'Inactive';
        const statusBadge = c.visits > 0 ? `<span class="badge bg-success bg-opacity-10 text-success fw-bold">${statusActive}</span>` : `<span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold">${statusInactive}</span>`;

        modalBody.innerHTML = `
            <div class="row g-4">
                <!-- Info Section -->
                <div class="col-md-5 border-end">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 72px; height: 72px;">
                            <i class="bi bi-person text-primary fs-3"></i>
                        </div>
                        <h4 class="fw-bold mb-1 text-dark">${c.name}</h4>
                        <span class="text-muted text-xs">#${c.customer_number || 'CUS-'+c.id}</span>
                        <div class="mt-2">${statusBadge}</div>
                    </div>
                    <div class="d-flex flex-column gap-3 text-start">
                        <div class="border-bottom pb-2">
                            <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'الهاتف' : 'Phone'}</span>
                            <div class="fw-semibold text-xs text-dark">${c.phone || '-'}</div>
                        </div>
                        <div class="border-bottom pb-2">
                            <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'البريد الإلكتروني' : 'Email'}</span>
                            <div class="fw-semibold text-xs text-dark">${c.email || '-'}</div>
                        </div>
                        <div class="border-bottom pb-2">
                            <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'العنوان' : 'Address'}</span>
                            <div class="fw-semibold text-xs text-dark">${c.address || '-'}</div>
                        </div>
                        <div>
                            <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'ملاحظات' : 'Notes'}</span>
                            <p class="text-muted text-xs mt-1 mb-0">${c.notes || '-'}</p>
                        </div>
                    </div>
                </div>

                <!-- Financials & Timeline -->
                <div class="col-md-7">
                    <h6 class="fw-bold text-xs text-uppercase text-muted mb-3">${langAr ? 'الملخص المالي والنشاط' : 'Financial Summary & Activity'}</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-4 text-center">
                            <div class="glass-card p-3 rounded-3">
                                <span class="text-muted text-xxs block mb-1 d-block">${spentLabel}</span>
                                <span class="fw-bold text-success font-monospace text-xs">${formattedSpent} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 text-center">
                            <div class="glass-card p-3 rounded-3">
                                <span class="text-muted text-xxs block mb-1 d-block">${langAr ? 'متوسط السلة' : 'Avg Ticket'}</span>
                                <span class="fw-bold text-primary font-monospace text-xs">${formattedTicket} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 text-center">
                            <div class="glass-card p-3 rounded-3">
                                <span class="text-muted text-xxs block mb-1 d-block">${balLabel}</span>
                                <span class="fw-bold text-danger font-monospace text-xs">${formattedBal} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-xs text-uppercase text-muted mb-3">${langAr ? 'خط النشاط الزمني' : 'Purchase Timeline'}</h6>
                    <div class="timeline timeline-one-side">
                        <div class="d-flex gap-3 mb-3 border-start pb-3 ps-3 position-relative" style="border-color: var(--border-color) !important;">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 24px; height: 24px; position: absolute; left: -12px; top: 0;">
                                <i class="bi bi-clock text-xs text-primary"></i>
                            </div>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-xs text-dark">${langAr ? 'تاريخ التسجيل' : 'Registered'}</span>
                                <small class="text-muted text-xxs mt-1">${c.created_at || '-'}</small>
                            </div>
                        </div>
                        ${c.visits > 0 ? `
                        <div class="d-flex gap-3 mb-3 border-start pb-3 ps-3 position-relative" style="border-color: var(--border-color) !important;">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 24px; height: 24px; position: absolute; left: -12px; top: 0;">
                                <i class="bi bi-bag-check-fill text-xs text-success"></i>
                            </div>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-xs text-dark">${langAr ? 'إجمالي الطلبات' : 'Purchases Recorded'}</span>
                                <span class="text-muted text-xxs mt-1">${c.visits} ${visitsLabel}</span>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        const modal = new bootstrap.Modal(document.getElementById('customerProfilePreviewModal'));
        modal.show();
    };

    // 5. View All Customer List Modals
    window.openAllCustomersModal = function(type) {
        const modalTitle = document.getElementById('allCustomersModalTitle');
        const modalBody = document.getElementById('allCustomersModalBody');
        const langAr = "{{ app()->getLocale() == 'ar' }}";

        let titleText = '';
        let listHtml = '<div class="list-group border-0">';

        if (type === 'spending') {
            titleText = langAr ? 'العملاء الأكثر إنفاقاً (الكل)' : 'All Spenders (Full List)';
            const sorted = [...allCustomers].sort((a,b) => b.total_purchases - a.total_purchases);
            sorted.forEach((c, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0" style="background-color: var(--card-bg); border-color: var(--border-color) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${c.name}</span>
                                <span class="text-muted text-xxs">${c.phone}</span>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold font-monospace text-xs">${parseFloat(c.total_purchases || 0).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                    </div>
                `;
            });
        } else if (type === 'ticket') {
            titleText = langAr ? 'أعلى قيمة سلة للعملاء' : 'Highest Ticket Value (Full List)';
            const sorted = [...allCustomers].sort((a,b) => {
                const at = a.visits > 0 ? a.total_purchases / a.visits : 0;
                const bt = b.visits > 0 ? b.total_purchases / b.visits : 0;
                return bt - at;
            });
            sorted.forEach((c, i) => {
                const ticketVal = c.visits > 0 ? c.total_purchases / c.visits : 0;
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0" style="background-color: var(--card-bg); border-color: var(--border-color) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${c.name}</span>
                                <span class="text-muted text-xxs">${c.phone}</span>
                            </div>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info fw-bold font-monospace text-xs">${ticketVal.toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                    </div>
                `;
            });
        } else if (type === 'frequent') {
            titleText = langAr ? 'الأكثر تكراراً وتردداً' : 'Most Frequent Visitors';
            const sorted = [...allCustomers].sort((a,b) => b.visits - a.visits);
            sorted.forEach((c, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0" style="background-color: var(--card-bg); border-color: var(--border-color) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${c.name}</span>
                                <span class="text-muted text-xxs">${c.phone}</span>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark fw-bold font-monospace text-xs">${c.visits} ${langAr ? 'زيارة' : 'Visits'}</span>
                    </div>
                `;
            });
        } else if (type === 'inactive') {
            titleText = langAr ? 'قائمة العملاء الراكدين' : 'Inactive Customers (Full List)';
            inactiveCustomers.forEach((c, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0" style="background-color: var(--card-bg); border-color: var(--border-color) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-danger fw-bold font-monospace text-xs">!</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${c.name}</span>
                                <span class="text-muted text-xxs">${c.phone} | Last: ${c.last_purchase_date || '-'}</span>
                            </div>
                        </div>
                        <span class="text-danger fw-bold font-monospace text-xs">${c.days_since_last_purchase || '-'} ${langAr ? 'يوم' : 'days'}</span>
                    </div>
                `;
            });
        }

        listHtml += '</div>';
        modalTitle.innerText = titleText;
        modalBody.innerHTML = listHtml;

        const modal = new bootstrap.Modal(document.getElementById('allCustomersReportModal'));
        modal.show();
    };

    // ==========================================
    // SUPPLIER REPORT REDESIGN SCRIPTS
    // ==========================================
    const supplierTrends = @json($supplierReport['trends'] ?? []);
    const supplierPurchasesByCategory = @json($supplierReport['purchasesByCategory'] ?? []);
    const topProductsBySupplier = @json($supplierReport['topProductsBySupplier'] ?? []);
    const supplierCategories = @json($supplierReport['supplierCategories'] ?? []);
    const supplierPaymentMethods = @json($supplierReport['paymentMethods'] ?? []);
    const allSuppliers = @json($supplierReport['suppliers'] ?? []);

    // 1. Purchase Trends Chart Setup
    const suppDailyLabels = getDatesInRange(fromDateStr, toDateStr);
    const suppDailyAmountData = suppDailyLabels.map(d => (supplierTrends.dailyPurchaseAmount && supplierTrends.dailyPurchaseAmount[d]) ? parseFloat(supplierTrends.dailyPurchaseAmount[d]) : 0);
    const suppDailyCountData = suppDailyLabels.map(d => (supplierTrends.dailyPurchaseCount && supplierTrends.dailyPurchaseCount[d]) ? parseInt(supplierTrends.dailyPurchaseCount[d]) : 0);

    const suppMonthlyLabels = getMonthsInRange(fromDateStr, toDateStr);
    const suppMonthlyAmountData = suppMonthlyLabels.map(m => (supplierTrends.monthlyPurchaseAmount && supplierTrends.monthlyPurchaseAmount[m]) ? parseFloat(supplierTrends.monthlyPurchaseAmount[m]) : 0);
    const suppMonthlyCountData = suppMonthlyLabels.map(m => (supplierTrends.monthlyPurchaseCount && supplierTrends.monthlyPurchaseCount[m]) ? parseInt(supplierTrends.monthlyPurchaseCount[m]) : 0);

    const supplierPurchaseTrendCtx = document.getElementById('supplierPurchaseTrendChart');
    let supplierPurchaseTrendChart = null;

    if (supplierPurchaseTrendCtx) {
        supplierPurchaseTrendChart = new Chart(supplierPurchaseTrendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: suppDailyLabels,
                datasets: [
                    {
                        label: "{{ app()->getLocale() == 'ar' ? 'قيمة المشتريات' : 'Purchase Value' }}",
                        data: suppDailyAmountData,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.05)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { padding: 12, cornerRadius: 8 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                if (typeof label === 'string' && label.length === 10 && label.includes('-')) {
                                    const parts = label.split('-');
                                    return `${parts[1]}-${parts[2]}`;
                                }
                                return label;
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        type: 'linear',
                        position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}",
                        display: true,
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] },
                        title: {
                            display: true,
                            text: "{{ app()->getLocale() == 'ar' ? 'القيمة' : 'Value' }} ({{ $setting->currency }})"
                        }
                    }
                }
            }
        });
    }

    window.switchSupplierPurchaseTrendPeriod = function(period, btn) {
        const selector = document.getElementById('purchaseTrendPeriodSelector');
        selector.querySelectorAll('button').forEach(b => {
            b.classList.remove('btn-primary');
            b.classList.add('btn-light');
        });
        btn.classList.remove('btn-light');
        btn.classList.add('btn-primary');
        
        if (supplierPurchaseTrendChart) {
            if (period === 'daily') {
                supplierPurchaseTrendChart.data.labels = suppDailyLabels;
                supplierPurchaseTrendChart.data.datasets[0].data = suppDailyAmountData;
            } else {
                supplierPurchaseTrendChart.data.labels = suppMonthlyLabels;
                supplierPurchaseTrendChart.data.datasets[0].data = suppMonthlyAmountData;
            }
            supplierPurchaseTrendChart.update();
        }
    };

    // 2. Purchases by Supplier Donut Chart
    const shareCtx = document.getElementById('supplierPurchasesShareChart');
    if (shareCtx) {
        const activeSuppliers = allSuppliers.filter(s => parseFloat(s.total_purchases || 0) > 0);
        const topSuppliers = activeSuppliers.slice(0, 5);
        const otherSuppliersTotal = activeSuppliers.slice(5).reduce((acc, curr) => acc + parseFloat(curr.total_purchases || 0), 0);
        const labels = topSuppliers.map(s => s.name);
        const data = topSuppliers.map(s => parseFloat(s.total_purchases || 0));
        if (otherSuppliersTotal > 0) {
            labels.push("{{ app()->getLocale() == 'ar' ? 'آخرون' : 'Others' }}");
            data.push(otherSuppliersTotal);
        }
        new Chart(shareCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#6366f1', '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#a855f7'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                },
                cutout: '60%'
            }
        });
    }

    // 3. Purchases by Category Bar Chart
    const catPurchCtx = document.getElementById('supplierCategoryPurchasesChart');
    if (catPurchCtx) {
        const labels = supplierPurchasesByCategory.map(c => c.name);
        const data = supplierPurchasesByCategory.map(c => parseFloat(c.total || 0));
        new Chart(catPurchCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "{{ app()->getLocale() == 'ar' ? 'إجمالي قيمة الشراء' : 'Total Purchase Value' }}",
                    data: data,
                    backgroundColor: 'rgba(99, 102, 241, 0.85)',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                if (typeof label === 'string' && label.length === 10 && label.includes('-')) {
                                    const parts = label.split('-');
                                    return `${parts[1]}-${parts[2]}`;
                                }
                                return label;
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: { 
                        position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}",
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5] } 
                    }
                }
            }
        });
    }

    // 4. Directory Search Filter
    window.filterSupplierListTable = function() {
        const query = document.getElementById('suppTableSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#supplierListTable tbody tr.supplier-row-item');
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const number = row.getAttribute('data-number');
            if (name.includes(query) || number.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    // 5. Preview Supplier Profile
    window.previewSupplierProfile = function(s) {
        const modalBody = document.getElementById('supplierProfileModalBody');
        const langAr = "{{ app()->getLocale() == 'ar' }}";

        const spentLabel = langAr ? 'إجمالي المشتريات' : 'Total Purchases';
        const balLabel = langAr ? 'المديونية المتبقية' : 'Remaining Balance';
        const ordersLabel = langAr ? 'طلب' : 'Orders';

        const formattedSpent = parseFloat(s.total_purchases || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const formattedBal = parseFloat(s.total_remaining || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        
        const avgValue = s.invoice_count > 0 ? (s.total_purchases / s.invoice_count) : 0;
        const formattedAvg = avgValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const statusBadge = s.invoice_count > 0 
            ? `<span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">${langAr ? 'نشط' : 'Active'}</span>`
            : `<span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-2 py-1">${langAr ? 'خامل' : 'Inactive'}</span>`;

        // Get recent products from this supplier
        const relatedProducts = topProductsBySupplier.filter(p => p.supplier_name === s.name);
        let productsHtml = '';
        if (relatedProducts.length > 0) {
            productsHtml = `<h6 class="fw-bold text-xs text-uppercase text-muted mb-3 mt-4">${langAr ? 'المنتجات الأكثر توريداً' : 'Most Supplied Products'}</h6>
            <div class="list-group list-group-flush mb-4">`;
            relatedProducts.forEach(p => {
                productsHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-2 px-0 bg-transparent">
                        <span class="text-xs text-dark fw-semibold">${p.product_name}</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark font-monospace text-xs">${parseInt(p.quantity)} ${langAr ? 'وحدة' : 'units'}</span>
                            <span class="badge bg-success bg-opacity-10 text-success font-monospace text-xs">${parseFloat(p.total).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                        </div>
                    </div>
                `;
            });
            productsHtml += `</div>`;
        }

        modalBody.innerHTML = `
            <div class="row">
                <!-- Info Section -->
                <div class="col-md-5 border-end">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 72px; height: 72px;">
                            <i class="bi bi-truck text-primary fs-3"></i>
                        </div>
                        <h4 class="fw-bold mb-1 text-dark">${s.name}</h4>
                        <span class="text-muted text-xs">NO: ${s.supplier_number || '#'+s.id}</span>
                        <div class="mt-2">${statusBadge}</div>
                    </div>
                    <div class="d-flex flex-column gap-3 text-start">
                        <div class="border-bottom pb-2">
                            <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'البريد الإلكتروني' : 'Email'}</span>
                            <div class="fw-semibold text-xs text-dark">${s.email || '-'}</div>
                        </div>
                        <div class="border-bottom pb-2">
                            <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'العنوان' : 'Address'}</span>
                            <div class="fw-semibold text-xs text-dark">${s.address || '-'}</div>
                        </div>
                    </div>
                </div>

                <!-- Financials & Timeline -->
                <div class="col-md-7">
                    <h6 class="fw-bold text-xs text-uppercase text-muted mb-3">${langAr ? 'الملخص المالي وحجم التوريد' : 'Financial Summary & Activity'}</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-4 text-center">
                            <div class="glass-card p-3 rounded-3">
                                <span class="text-muted text-xxs block mb-1 d-block">${spentLabel}</span>
                                <span class="fw-bold text-success font-monospace text-xs">${formattedSpent} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 text-center">
                            <div class="glass-card p-3 rounded-3">
                                <span class="text-muted text-xxs block mb-1 d-block">${langAr ? 'متوسط قيمة التوريد' : 'Avg Order Value'}</span>
                                <span class="fw-bold text-primary font-monospace text-xs">${formattedAvg} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 text-center">
                            <div class="glass-card p-3 rounded-3">
                                <span class="text-muted text-xxs block mb-1 d-block">${balLabel}</span>
                                <span class="fw-bold text-danger font-monospace text-xs">${formattedBal} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-xs text-uppercase text-muted mb-3">${langAr ? 'خط النشاط والطلبات' : 'Procurement History'}</h6>
                    <div class="timeline timeline-one-side">
                        <div class="d-flex gap-3 mb-3 border-start pb-3 ps-3 position-relative" style="border-color: var(--border-color) !important;">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 24px; height: 24px; position: absolute; left: -12px; top: 0;">
                                <i class="bi bi-receipt text-xs text-primary"></i>
                            </div>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-xs text-dark">${langAr ? 'إجمالي فواتير الشراء' : 'Total Invoices'}</span>
                                <small class="text-muted text-xxs mt-1">${s.invoice_count} ${ordersLabel}</small>
                            </div>
                        </div>
                    </div>

                    ${productsHtml}
                </div>
            </div>
        `;
        const modal = new bootstrap.Modal(document.getElementById('supplierProfilePreviewModal'));
        modal.show();
    };

    // 6. View All Supplier List Modals
    window.openAllSuppliersModal = function(type) {
        const modalTitle = document.getElementById('allSuppliersModalTitle');
        const modalBody = document.getElementById('allSuppliersModalBody');
        const langAr = "{{ app()->getLocale() == 'ar' }}";

        let titleText = '';
        let listHtml = '<div class="list-group border-0">';

        if (type === 'value') {
            titleText = langAr ? 'أعلى الموردين توريداً (الكل)' : 'All Suppliers by Value (Full List)';
            const sorted = [...allSuppliers].sort((a,b) => b.total_purchases - a.total_purchases);
            sorted.forEach((s, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${s.name}</span>
                                <span class="text-muted text-xxs">${s.email || '-'}</span>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold font-monospace text-xs">${parseFloat(s.total_purchases || 0).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                    </div>
                `;
            });
        } else if (type === 'volume') {
            titleText = langAr ? 'الأكثر تكراراً وتعاملاً (الكل)' : 'Most Purchased (Full List)';
            const sorted = [...allSuppliers].sort((a,b) => b.invoice_count - a.invoice_count);
            sorted.forEach((s, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${s.name}</span>
                                <span class="text-muted text-xxs">${s.email || '-'}</span>
                            </div>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold font-monospace text-xs">${s.invoice_count} ${langAr ? 'طلبات' : 'Orders'}</span>
                    </div>
                `;
            });
        } else if (type === 'average') {
            titleText = langAr ? 'متوسط قيمة التوريد (الكل)' : 'Average Purchase Value (Full List)';
            const sorted = [...allSuppliers].map(s => {
                s.avg_value = s.invoice_count > 0 ? s.total_purchases / s.invoice_count : 0;
                return s;
            }).sort((a,b) => b.avg_value - a.avg_value);
            sorted.forEach((s, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${s.name}</span>
                                <span class="text-muted text-xxs">${langAr ? 'إجمالي:' : 'Total:'} ${parseFloat(s.total_purchases || 0).toFixed(2)}</span>
                            </div>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info fw-bold font-monospace text-xs">${s.avg_value.toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                    </div>
                `;
            });
        } else if (type === 'products') {
            titleText = langAr ? 'المنتجات الأكثر شراءً من الموردين (الكل)' : 'All Products Supplied (Full List)';
            topProductsBySupplier.forEach((p, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${p.product_name}</span>
                                <span class="text-muted text-xxs">${langAr ? 'المورد:' : 'Supplier:'} ${p.supplier_name}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark font-monospace text-xs">${parseInt(p.quantity)}</span>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold font-monospace text-xs">${parseFloat(p.total).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                        </div>
                    </div>
                `;
            });
        }

        listHtml += '</div>';
        modalTitle.innerText = titleText;
        modalBody.innerHTML = listHtml;

        const modal = new bootstrap.Modal(document.getElementById('allSuppliersReportModal'));
        modal.show();
    };

    // ==========================================
    // EXPENSE REPORT REDESIGN SCRIPTS
    // ==========================================
    const expenseDailyTrend = @json($expensesReport['daily_trend'] ?? []);
    const expenseMonthlyTrend = @json($expensesReport['monthly_trend'] ?? []);
    const expenseHighestCategories = @json($expensesReport['highest_categories'] ?? []);
    const expenseLargestExpenses = @json($expensesReport['largest_expenses'] ?? []);
    const expenseFrequentCategories = @json($expensesReport['frequent_categories'] ?? []);
    const expensePaymentMethods = @json($expensesReport['payment_methods'] ?? []);
    const allExpenses = @json($expensesReport['expenses'] ?? []);

    // 1. Expense Trend Chart Setup
    const expDailyLabels = getDatesInRange(fromDateStr, toDateStr);
    const expDailyAmountData = expDailyLabels.map(d => {
        const found = expenseDailyTrend.find(x => x.period === d);
        return found ? parseFloat(found.amount) : 0;
    });
    const expDailyCountData = expDailyLabels.map(d => {
        const found = expenseDailyTrend.find(x => x.period === d);
        return found ? parseInt(found.count) : 0;
    });

    const expMonthlyLabels = getMonthsInRange(fromDateStr, toDateStr);
    const expMonthlyAmountData = expMonthlyLabels.map(m => {
        const found = expenseMonthlyTrend.find(x => x.period === m);
        return found ? parseFloat(found.amount) : 0;
    });
    const expMonthlyCountData = expMonthlyLabels.map(m => {
        const found = expenseMonthlyTrend.find(x => x.period === m);
        return found ? parseInt(found.count) : 0;
    });

    const expenseTrendCtx = document.getElementById('expenseTrendChart');
    let expenseTrendChart = null;

    if (expenseTrendCtx) {
        expenseTrendChart = new Chart(expenseTrendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: expDailyLabels,
                datasets: [
                    {
                        label: "{{ app()->getLocale() == 'ar' ? 'قيمة المصروفات' : 'Expense Value' }}",
                        data: expDailyAmountData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { padding: 12, cornerRadius: 8 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                if (typeof label === 'string' && label.length === 10 && label.includes('-')) {
                                    const parts = label.split('-');
                                    return `${parts[1]}-${parts[2]}`;
                                }
                                return label;
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        type: 'linear',
                        position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}",
                        display: true,
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] },
                        title: {
                            display: true,
                            text: "{{ app()->getLocale() == 'ar' ? 'القيمة' : 'Value' }} ({{ $setting->currency }})"
                        }
                    }
                }
            }
        });
    }

    window.switchExpenseTrendPeriod = function(period) {
        const selector = document.getElementById('expenseTrendPeriodSelector');
        selector.querySelectorAll('button').forEach(btn => btn.classList.replace('btn-primary', 'btn-light'));
        
        if (period === 'daily') {
            event.target.classList.replace('btn-light', 'btn-primary');
            expenseTrendChart.data.labels = expDailyLabels;
            expenseTrendChart.data.datasets[0].data = expDailyAmountData;
        } else {
            event.target.classList.replace('btn-light', 'btn-primary');
            expenseTrendChart.data.labels = expMonthlyLabels;
            expenseTrendChart.data.datasets[0].data = expMonthlyAmountData;
        }
        expenseTrendChart.update();
    };

    // 2. Expense by Category Donut Chart
    const expCategoryCtx = document.getElementById('expenseCategoryDonutChart');
    if (expCategoryCtx) {
        const labels = expenseHighestCategories.map(c => c.category_label);
        const data = expenseHighestCategories.map(c => parseFloat(c.total_amount || 0));
        new Chart(expCategoryCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#06b6d4', '#a855f7'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                },
                cutout: '60%'
            }
        });
    }

    // 3. Monthly Expense Distribution Bar Chart
    const expMonthlyCtx = document.getElementById('expenseMonthlyBarChart');
    if (expMonthlyCtx) {
        const labels = expenseMonthlyTrend.map(m => m.period);
        const data = expenseMonthlyTrend.map(m => parseFloat(m.amount || 0));
        new Chart(expMonthlyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "{{ app()->getLocale() == 'ar' ? 'إجمالي المصروفات' : 'Total Expenses' }}",
                    data: data,
                    backgroundColor: '#ef4444',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                if (typeof label === 'string' && label.length === 10 && label.includes('-')) {
                                    const parts = label.split('-');
                                    return `${parts[1]}-${parts[2]}`;
                                }
                                return label;
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: { 
                        position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}",
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5] } 
                    }
                }
            }
        });
    }

    // 4. Filter and Search for Expense Table
    window.filterExpenseListTable = function() {
        const searchInput = document.getElementById('expenseTableSearch').value.toLowerCase();
        const rows = document.querySelectorAll('#expenseListTable tbody tr.expense-row-item');
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const number = row.getAttribute('data-number');
            if (name.includes(searchInput) || number.includes(searchInput)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    // 5. Preview Details Modal
    window.previewExpenseDetails = function(e) {
        const modalBody = document.getElementById('expenseDetailModalBody');
        const langAr = "{{ app()->getLocale() == 'ar' }}";
        const currencySym = "{{ $setting->currency }}";
        
        let attachmentHtml = `<span class="text-muted text-xs">${langAr ? 'لا يوجد مرفقات' : 'No attachment'}</span>`;
        if (e.attachment) {
            const url = `/storage/${e.attachment}`;
            attachmentHtml = `
                <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill text-xs mt-1 d-inline-flex align-items-center gap-1">
                    <i class="bi bi-paperclip"></i> ${langAr ? 'عرض المرفق' : 'View Attachment'}
                </a>
            `;
        }

        let pmTranslated = e.payment_method || '-';
        if (langAr) {
            const methodLower = (e.payment_method || '').toLowerCase();
            if (methodLower === 'card') pmTranslated = 'بطاقة';
            else if (methodLower === 'mobile payment' || methodLower === 'mobile_payment') pmTranslated = 'دفع هاتف محمول';
            else if (methodLower === 'cash') pmTranslated = 'نقداً';
            else if (methodLower === 'bank transfer' || methodLower === 'bank_transfer') pmTranslated = 'تحويل بنكي';
        }

        let createdBy = '-';
        if (e.user) {
            if (typeof e.user.full_name === 'object' && e.user.full_name !== null) {
                createdBy = e.user.full_name[langAr ? 'ar' : 'en'] || e.user.full_name['ar'] || e.user.full_name['en'] || '-';
            } else if (typeof e.user.full_name === 'string') {
                try {
                    const parsed = JSON.parse(e.user.full_name);
                    createdBy = parsed[langAr ? 'ar' : 'en'] || parsed['ar'] || parsed['en'] || '-';
                } catch (err) {
                    createdBy = e.user.full_name;
                }
            } else {
                createdBy = e.user.name || '-';
            }
        }

        modalBody.innerHTML = `
            <div class="row g-3">
                <div class="col-md-6 border-bottom pb-2">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'رقم المصروف' : 'Expense Number'}</span>
                    <div class="fw-bold text-xs text-dark">${e.expense_number || '#'+e.id}</div>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'التاريخ' : 'Date'}</span>
                    <div class="fw-semibold text-xs text-dark">${e.expense_date}</div>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'البيان/الاسم' : 'Expense Name'}</span>
                    <div class="fw-bold text-xs text-dark">${e.description_ar || e.description_en || '-'}</div>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'الفئة' : 'Category'}</span>
                    <div><span class="badge bg-secondary bg-opacity-10 text-secondary text-xxs px-2 py-1">${e.type}</span></div>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'المبلغ' : 'Amount'}</span>
                    <div class="fw-bold text-danger text-xs">${parseFloat(e.amount).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></div>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'طريقة السداد' : 'Payment Method'}</span>
                    <div class="fw-semibold text-xs text-dark">${pmTranslated}</div>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'بواسطة' : 'Created By'}</span>
                    <div class="fw-semibold text-xs text-dark">${createdBy}</div>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'المرفقات' : 'Attachments'}</span>
                    <div>${attachmentHtml}</div>
                </div>
                <div class="col-12">
                    <span class="text-muted text-xxs text-uppercase fw-bold">${langAr ? 'ملاحظات' : 'Notes'}</span>
                    <div class="text-xs text-dark border p-2 rounded bg-light" style="min-height:60px;">${e.notes || '-'}</div>
                </div>
            </div>
        `;
        
        // Print / Edit Actions in Footer
        const modalActions = document.getElementById('expenseModalActions');
        modalActions.innerHTML = '';

        const modal = new bootstrap.Modal(document.getElementById('expenseDetailPreviewModal'));
        modal.show();
    };

    // 6. View All Expenses Report Modal
    window.openAllExpensesModal = function(type) {
        const modalTitle = document.getElementById('allExpensesReportModalTitle');
        const modalBody = document.getElementById('allExpensesReportModalBody');
        const langAr = "{{ app()->getLocale() == 'ar' }}";
        const currencySym = "{{ $setting->currency }}";

        let titleText = '';
        let listHtml = '<div class="list-group border-0">';

        if (type === 'highest_categories') {
            titleText = langAr ? 'الأعلى صرفاً حسب الفئة (الكل)' : 'All Categories by Expense Value (Full List)';
            expenseHighestCategories.forEach((c, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${c.category_label}</span>
                                <span class="text-muted text-xxs">${c.count} ${langAr ? 'عمليات' : 'Transactions'}</span>
                            </div>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold font-monospace text-xs">${parseFloat(c.total_amount).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                    </div>
                `;
            });
        } else if (type === 'largest_expenses') {
            titleText = langAr ? 'أكبر المصروفات الفردية (الكل)' : 'All Largest Expenses (Full List)';
            expenseLargestExpenses.forEach((e, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${e.description_ar || e.description_en || e.expense_number}</span>
                                <span class="text-muted text-xxs">${e.type_label} | ${e.expense_date}</span>
                            </div>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold font-monospace text-xs">${parseFloat(e.amount).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                    </div>
                `;
            });
        } else if (type === 'frequent_categories') {
            titleText = langAr ? 'الفئات الأكثر تكراراً (الكل)' : 'All Frequent Categories (Full List)';
            expenseFrequentCategories.forEach((c, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark text-xs">${c.category_label}</span>
                                <span class="text-muted text-xxs">${langAr ? 'إجمالي المبالغ:' : 'Total amount:'} ${parseFloat(c.total_amount).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                            </div>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold font-monospace text-xs">${c.transaction_count} ${langAr ? 'عمليات' : 'Trans.'}</span>
                    </div>
                `;
            });
        }

        listHtml += '</div>';
        modalTitle.innerText = titleText;
        modalBody.innerHTML = listHtml;

        const modal = new bootstrap.Modal(document.getElementById('allExpensesReportModal'));
        modal.show();
    };

    // ==========================================
    // FINANCIAL REPORT REDESIGN SCRIPTS
    // ==========================================
    const finDailySales = @json($financialReport['daily_sales'] ?? []);
    const finDailyExpenses = @json($financialReport['daily_expenses'] ?? []);
    const finMonthlySales = @json($financialReport['monthly_sales'] ?? []);
    const finMonthlyExpenses = @json($financialReport['monthly_expenses'] ?? []);

    const finRevenueCategories = @json($financialReport['top_revenue_categories'] ?? []);
    const finExpenseCategories = @json($financialReport['expense_breakdown'] ?? []);

    const finDailyLabels = getDatesInRange(fromDateStr, toDateStr);
    const finDailySalesData = finDailyLabels.map(d => {
        const found = finDailySales.find(x => x.period === d);
        return found ? parseFloat(found.amount) : 0;
    });
    const finDailyExpensesData = finDailyLabels.map(d => {
        const found = finDailyExpenses.find(x => x.period === d);
        return found ? parseFloat(found.amount) : 0;
    });
    const finDailyProfitData = finDailyLabels.map((d, idx) => {
        return finDailySalesData[idx] - finDailyExpensesData[idx];
    });

    const finMonthlyLabels = getMonthsInRange(fromDateStr, toDateStr);
    const finMonthlySalesData = finMonthlyLabels.map(m => {
        const found = finMonthlySales.find(x => x.period === m);
        return found ? parseFloat(found.amount) : 0;
    });
    const finMonthlyExpensesData = finMonthlyLabels.map(m => {
        const found = finMonthlyExpenses.find(x => x.period === m);
        return found ? parseFloat(found.amount) : 0;
    });
    const finMonthlyProfitData = finMonthlyLabels.map((m, idx) => {
        return finMonthlySalesData[idx] - finMonthlyExpensesData[idx];
    });

    const financialTrendCtx = document.getElementById('financialTrendChart');
    let financialTrendChart = null;

    if (financialTrendCtx) {
        financialTrendChart = new Chart(financialTrendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: finDailyLabels,
                datasets: [
                    {
                        label: "{{ app()->getLocale() == 'ar' ? 'الإيرادات' : 'Revenue' }}",
                        data: finDailySalesData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.03)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: "{{ app()->getLocale() == 'ar' ? 'المصروفات' : 'Expenses' }}",
                        data: finDailyExpensesData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.03)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: "{{ app()->getLocale() == 'ar' ? 'صافي الربح' : 'Net Profit' }}",
                        data: finDailyProfitData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.03)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { padding: 12, cornerRadius: 8 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                if (typeof label === 'string' && label.length === 10 && label.includes('-')) {
                                    const parts = label.split('-');
                                    return `${parts[1]}-${parts[2]}`;
                                }
                                return label;
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        position: "{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}",
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] }
                    }
                }
            }
        });
    }

    window.switchFinancialTrendPeriod = function(period, btn) {
        const selector = document.getElementById('financialTrendPeriodSelector');
        selector.querySelectorAll('button').forEach(b => {
            b.classList.remove('btn-primary');
            b.classList.add('btn-light', 'border');
        });
        if (btn) {
            btn.classList.remove('btn-light', 'border');
            btn.classList.add('btn-primary');
        }

        if (period === 'daily') {
            financialTrendChart.data.labels = finDailyLabels;
            financialTrendChart.data.datasets[0].data = finDailySalesData;
            financialTrendChart.data.datasets[1].data = finDailyExpensesData;
            financialTrendChart.data.datasets[2].data = finDailyProfitData;
        } else {
            financialTrendChart.data.labels = finMonthlyLabels;
            financialTrendChart.data.datasets[0].data = finMonthlySalesData;
            financialTrendChart.data.datasets[1].data = finMonthlyExpensesData;
            financialTrendChart.data.datasets[2].data = finMonthlyProfitData;
        }
        financialTrendChart.update();
    };

    // Revenue Donut Chart
    const finRevCtx = document.getElementById('financialRevenueDonutChart');
    if (finRevCtx) {
        new Chart(finRevCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ["{{ app()->getLocale() == 'ar' ? 'مبيعات المنتجات' : 'Product Sales' }}"],
                datasets: [{
                    data: [{{ $financialReport['total_sales'] }}],
                    backgroundColor: ['#10b981', '#3b82f6'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                },
                cutout: '60%'
            }
        });
    }

    // Expense Donut Chart
    const finExpCtx = document.getElementById('financialExpenseDonutChart');
    if (finExpCtx) {
        const labels = finExpenseCategories.slice(0, 5).map(c => c.type);
        const data = finExpenseCategories.slice(0, 5).map(c => parseFloat(c.total));
        new Chart(finExpCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#06b6d4', '#a855f7'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                },
                cutout: '60%'
            }
        });
    }

    // View All Modal for Financials
    window.openAllFinancialModal = function(type) {
        const modalTitle = document.getElementById('allFinancialReportModalTitle');
        const modalBody = document.getElementById('allFinancialReportModalBody');
        const langAr = "{{ app()->getLocale() == 'ar' }}";
        const currencySym = "{{ $setting->currency }}";

        let titleText = '';
        let listHtml = '<div class="list-group border-0">';

        if (type === 'revenue_categories') {
            titleText = langAr ? 'كل فئات المبيعات تحقيقاً للإيراد' : 'All Product Sales Categories by Revenue';
            finRevenueCategories.forEach((c, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <span class="fw-bold text-dark text-xs">${c.category_label}</span>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold font-monospace text-xs">${parseFloat(c.total_revenue).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                    </div>
                `;
            });
        } else if (type === 'expenses') {
            titleText = langAr ? 'توزيع كل فئات المصروفات' : 'All Expense Categories';
            finExpenseCategories.forEach((c, i) => {
                listHtml += `
                    <div class="list-group-item border-0 border-bottom d-flex align-items-center justify-content-between py-3 px-0 bg-transparent">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-bold font-monospace text-xs">#${i+1}</span>
                            <span class="fw-bold text-dark text-xs">${c.type}</span>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold font-monospace text-xs">${parseFloat(c.total).toFixed(2)} <span style="font-family: system-ui, -apple-system, sans-serif; font-weight: normal;" class="currency-font">${currencySym}</span></span>
                    </div>
                `;
            });
        }

        listHtml += '</div>';
        modalTitle.innerText = titleText;
        modalBody.innerHTML = listHtml;

        const modal = new bootstrap.Modal(document.getElementById('allFinancialReportModal'));
        modal.show();
    };

    // Tab persistence logic
    const reportTabsButtons = document.querySelectorAll('#reportTabs button');
        
        // 1. Restore active tab on load
        const activeReportTab = localStorage.getItem('activeReportTab');
        if (activeReportTab) {
            const targetBtn = document.querySelector(`#reportTabs button[data-bs-target="${activeReportTab}"]`);
            if (targetBtn) {
                const tabInstance = bootstrap.Tab.getOrCreateInstance(targetBtn);
                tabInstance.show();
                updateExportLinks(activeReportTab.replace('#', ''));
            }
        } else {
            // Set initial export links if no saved tab
            const activeTabButton = document.querySelector('#reportTabs button.active');
            if (activeTabButton) {
                const targetId = activeTabButton.getAttribute('data-bs-target').replace('#', '');
                updateExportLinks(targetId);
            }
        }
        
        // 2. Save active tab on switch and update export links
        reportTabsButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function (event) {
                const targetId = event.target.getAttribute('data-bs-target');
                localStorage.setItem('activeReportTab', targetId);
                updateExportLinks(targetId.replace('#', ''));

                // Force Chart.js to recalculate dimensions inside the newly shown tab
                setTimeout(function() {
                    window.dispatchEvent(new Event('resize'));
                }, 80);
            });
        });
    });
</script>
@endpush

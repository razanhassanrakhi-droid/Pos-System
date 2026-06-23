@extends('layouts.app')

@section('title', __('pos.dashboard'))

@section('content')
<!-- Override body background for a premium feel -->
<style>
    body {
        background-color: var(--bg-color) !important;
    }
</style>

<!-- Header Banner Section (Stunning Dark Indigo Hero Card) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-hero-card py-3 px-4 rounded-4 border-0 position-relative overflow-hidden shadow-sm">
            <div class="position-relative z-index-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <span class="badge bg-primary bg-opacity-25 text-primary-light px-2 py-1 rounded-pill text-uppercase fw-semibold tracking-wider" style="font-size: 0.6rem;">
                            <i class="bi bi-cpu me-1"></i> {{ app()->getLocale() == 'ar' ? 'النظام متصل' : 'System Online' }}
                        </span>
                        <h4 class="fw-bold mb-0 text-white welcome-title tracking-tight fs-4">
                            {{ __('pos.welcome_back', ['name' => auth()->user()->full_name ?? auth()->user()->username ?? 'User']) }} 👋
                        </h4>
                    </div>
                    <p class="mb-0 welcome-subtitle" style="font-size: 0.85rem;">
                        {{ __('pos.dashboard_subtitle', ['date' => now()->translatedFormat('l, d F Y')]) }}
                    </p>
                </div>
                <div class="hero-time-badge px-3 py-1 rounded-3 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history fs-6"></i>
                    <span id="live-clock" class="fw-bold fs-6">{{ now()->format('H:i') }}</span>
                </div>
            </div>
            <!-- Decorative light blobs -->
            <div class="hero-blob blob-1"></div>
            <div class="hero-blob blob-2"></div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Total Products -->
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card h-100 stat-card border-0 rounded-5 overflow-hidden position-relative shadow-sm">
            <div class="accent-bar bg-primary"></div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-xs text-secondary text-uppercase tracking-wider fw-bold">{{ __('pos.total_products') }}</span>
                    <div class="icon-glow bg-primary-soft text-primary rounded-4 d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <h2 class="font-weight-bolder mb-1 fw-bold text-dark tracking-tight fs-1">
                            {{ number_format($totalProducts) }}
                        </h2>
                        <span class="text-xs text-muted d-flex align-items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-right text-success"></i>
                            <span class="text-success fw-bold">{{ app()->getLocale() == 'ar' ? 'نشط' : 'Active' }}</span> {{ app()->getLocale() == 'ar' ? 'في المخزون' : 'in inventory' }}
                        </span>
                    </div>
                    <!-- Sparkline -->
                    <div class="sparkline-container">
                        <svg class="sparkline" width="70" height="30" viewBox="0 0 70 30">
                            <path d="M0,25 Q15,5 30,15 T60,10 T70,5" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round"></path>
                            <circle cx="70" cy="5" r="3" fill="#3b82f6"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Today Sales -->
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card h-100 stat-card border-0 rounded-5 overflow-hidden position-relative shadow-sm">
            <div class="accent-bar bg-success"></div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-xs text-secondary text-uppercase tracking-wider fw-bold">{{ __('pos.today_sales') }}</span>
                    <div class="icon-glow bg-success-soft text-success rounded-4 d-flex align-items-center justify-content-center">
                        <i class="bi bi-cart-check fs-4"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <h2 class="font-weight-bolder mb-1 fw-bold text-dark tracking-tight fs-1">
                            {{ $todaySalesCount }}
                        </h2>
                        <span class="text-xs text-muted d-flex align-items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-right text-success"></i>
                            <span class="text-success fw-bold">{{ app()->getLocale() == 'ar' ? 'مباشر' : 'Live' }}</span> {{ app()->getLocale() == 'ar' ? 'طلبات اليوم' : 'today\'s orders' }}
                        </span>
                    </div>
                    <!-- Sparkline -->
                    <div class="sparkline-container">
                        <svg class="sparkline" width="70" height="30" viewBox="0 0 70 30">
                            <path d="M0,28 C10,25 20,10 30,18 C40,25 50,5 60,10 L70,2" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"></path>
                            <circle cx="70" cy="2" r="3" fill="#10b981"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Soon Products -->
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card h-100 stat-card border-0 rounded-5 overflow-hidden position-relative shadow-sm">
            <div class="accent-bar bg-warning"></div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-xs text-secondary text-uppercase tracking-wider fw-bold">{{ __('pos.expiring_soon_products') }}</span>
                    <div class="icon-glow bg-warning-soft text-warning rounded-4 d-flex align-items-center justify-content-center">
                        <i class="bi bi-calendar-x fs-4"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <h2 class="font-weight-bolder mb-1 fw-bold text-warning-custom tracking-tight fs-1">
                            {{ $expiringSoonCount }}
                        </h2>
                        <span class="text-xs text-muted d-flex align-items-center gap-1 mt-1">
                            <i class="bi bi-exclamation-circle text-warning"></i>
                            <span class="text-warning fw-bold">{{ app()->getLocale() == 'ar' ? 'إجراء مطلوب' : 'Action required' }}</span> {{ app()->getLocale() == 'ar' ? 'قريباً' : 'soon' }}
                        </span>
                    </div>
                    <!-- Sparkline -->
                    <div class="sparkline-container">
                        <svg class="sparkline" width="70" height="30" viewBox="0 0 70 30">
                            <path d="M0,10 Q20,28 40,15 T70,22" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round"></path>
                            <circle cx="70" cy="22" r="3" fill="#f59e0b"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100 stat-card border-0 rounded-5 overflow-hidden position-relative shadow-sm">
            <div class="accent-bar bg-danger"></div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-xs text-secondary text-uppercase tracking-wider fw-bold">{{ __('pos.low_stock_products') }}</span>
                    <div class="icon-glow bg-danger-soft text-danger rounded-4 d-flex align-items-center justify-content-center">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <h2 class="font-weight-bolder mb-1 fw-bold text-danger-custom tracking-tight fs-1">
                            {{ $lowStockCount }}
                        </h2>
                        <span class="text-xs text-muted d-flex align-items-center gap-1 mt-1">
                            <i class="bi bi-exclamation-triangle text-danger"></i>
                            <span class="text-danger fw-bold">{{ app()->getLocale() == 'ar' ? 'حرج' : 'Critical' }}</span> {{ app()->getLocale() == 'ar' ? 'تنبيه مخزون' : 'stock alert' }}
                        </span>
                    </div>
                    <!-- Sparkline -->
                    <div class="sparkline-container">
                        <svg class="sparkline" width="70" height="30" viewBox="0 0 70 30">
                            <path d="M0,5 Q15,25 35,10 T70,25" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round"></path>
                            <circle cx="70" cy="25" r="3" fill="#ef4444"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Premium Low Stock Products Widget -->
    <div class="col-md-6 mb-4">
        <div class="card premium-inventory-card border-0 shadow-sm overflow-hidden h-100" data-trans-showing="{{ __('pos.showing_products_info') }}">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="alert-icon-square d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-4" style="width: 52px; height: 52px;">
                                <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold text-dark tracking-tight">{{ __('pos.low_stock_products') }}</h4>
                                <p class="mb-0 text-muted small">{{ __('pos.low_stock_subtitle') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('products.index') }}" class="btn btn-premium-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-sliders"></i>
                            <span>{{ __('pos.manage', ['page' => __('pos.products')]) }}</span>
                            <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>

                    <!-- Search and Filter Bar -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 px-1">
                        <div class="search-input-wrapper position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" id="product-search-input" class="form-control ps-5 rounded-3 text-sm py-2" placeholder="{{ __('pos.search_low_stock') }}" oninput="handleSearch()">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-xs text-muted">{{ __('pos.status_label') }}</span>
                            <div class="btn-group btn-group-sm border rounded-pill overflow-hidden bg-light-soft p-1" role="group">
                                <button type="button" class="btn btn-filter rounded-pill active" onclick="filterStatus('all', this)">{{ __('pos.all') }}</button>
                                <button type="button" class="btn btn-filter rounded-pill" onclick="filterStatus('critical', this)">{{ __('pos.critical') }}</button>
                                <button type="button" class="btn btn-filter rounded-pill" onclick="filterStatus('low', this)">{{ __('pos.low') }}</button>
                                <button type="button" class="btn btn-filter rounded-pill" onclick="filterStatus('warning', this)">{{ __('pos.warning') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product List Section -->
                    <div class="product-list-wrapper d-flex flex-column gap-3 mb-4" id="low-stock-products-list">
                        @forelse($lowStockProducts as $p)
                            @php
                                // Calculate stock status percentage dynamically relative to minimum stock
                                $minStock = $p->minimum_stock > 0 ? $p->minimum_stock : 10;
                                $currentStock = $p->current_stock;
                                $percentage = min(100, max(0, round(($currentStock / $minStock) * 100)));
                                
                                // Determine status details
                                if ($percentage <= 10) {
                                    $statusText = __('pos.critical');
                                    $statusColor = '#EF4444';
                                    $statusBg = 'rgba(239, 68, 68, 0.08)';
                                    $statusClass = 'critical';
                                } elseif ($percentage <= 40) {
                                    $statusText = __('pos.low');
                                    $statusColor = '#F97316';
                                    $statusBg = 'rgba(249, 115, 22, 0.08)';
                                    $statusClass = 'low';
                                } elseif ($percentage <= 99) {
                                    $statusText = __('pos.warning');
                                    $statusColor = '#EAB308';
                                    $statusBg = 'rgba(234, 179, 8, 0.08)';
                                    $statusClass = 'warning';
                                } else {
                                    $statusText = __('pos.healthy');
                                    $statusColor = '#22C55E';
                                    $statusBg = 'rgba(34, 197, 94, 0.08)';
                                    $statusClass = 'healthy';
                                }
                                
                                $unitRaw = $p->base_unit_name ?? 'units';
                                $unit = __('pos.' . strtolower($unitRaw)) == 'pos.' . strtolower($unitRaw) ? $unitRaw : __('pos.' . strtolower($unitRaw));
                            @endphp
                            <div class="horizontal-product-card d-flex align-items-center justify-content-between p-3 rounded-4 border transition-all flex-wrap gap-2" data-status="{{ $statusClass }}" data-name="{{ strtolower($p->name) }}" data-id="{{ $p->id }}">
                                <!-- Left Section -->
                                <div class="d-flex align-items-center gap-3" style="min-width: 150px; flex: 2 1 200px;">
                                    <div class="product-icon-square d-flex align-items-center justify-content-center rounded-3" style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; width: 48px; height: 48px; min-width: 48px;">
                                        <i class="bi bi-box-seam fs-5"></i>
                                    </div>
                                    <div class="d-flex flex-column min-width-0">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate text-sm" style="max-width: 130px;">{{ $p->name }}</h6>
                                        <span class="text-muted text-xxs text-truncate">{{ __('pos.product_id') }}: #{{ $p->id }}</span>
                                    </div>
                                </div>
                                
                                <!-- Center Section (Quantity) -->
                                <div class="d-flex flex-column text-start" style="min-width: 70px; flex: 1 1 80px;">
                                    <span class="text-xxs text-muted text-uppercase tracking-wider">{{ __('pos.quantity') }}</span>
                                    <div class="d-flex align-items-baseline gap-1">
                                        <span class="fw-bold fs-5 text-dark" style="color: {{ $statusColor }} !important;">{{ (int)$currentStock }}</span>
                                        <span class="text-muted text-xxs">{{ $unit }}</span>
                                    </div>
                                </div>
                                
                                <!-- Stock Health Progress Section -->
                                <div class="d-flex flex-column px-1" style="min-width: 120px; flex: 2 1 150px;">
                                    <span class="text-xxs text-muted text-uppercase tracking-wider mb-1">{{ __('pos.stock_level') }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; background-color: var(--border-color); border-radius: 3px; overflow: hidden;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: {{ $statusColor }}; border-radius: 3px;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="text-xxs fw-bold text-dark">{{ $percentage }}%</span>
                                    </div>
                                    <div class="mt-1 d-flex align-items-center gap-1">
                                        <span class="status-dot" style="background-color: {{ $statusColor }}; width: 6px; height: 6px; border-radius: 50%; display: inline-block;"></span>
                                        <span class="badge rounded-pill text-xxs px-2 py-0.5 fw-semibold" style="color: {{ $statusColor }}; background-color: {{ $statusBg }};">{{ $statusText }}</span>
                                    </div>
                                </div>
                                
                                <!-- Right Section (CTA Actions) -->
                                <div class="d-flex align-items-center justify-content-end" style="min-width: 100px; flex: 1 1 100px;">
                                    <a href="{{ route('purchases.create') }}?product_id={{ $p->id }}" class="btn btn-restock rounded-pill px-3 py-2 text-xxs fw-bold text-white transition-all d-flex align-items-center gap-1" style="background-color: {{ $statusColor }}; border: 1px solid {{ $statusColor }};">
                                        <i class="bi bi-cart-plus-fill"></i>
                                        {{ __('pos.restock_now') }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted border rounded-4 border-dashed bg-light-soft">
                                <div class="py-4">
                                    <i class="bi bi-shield-check text-success fs-1 mb-2 d-block opacity-75"></i>
                                    <h6 class="fw-bold text-dark">{{ __('pos.no_low_stock_products') }}</h6>
                                    <span class="small text-muted">{{ __('pos.no_low_stock_products') }}</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Footer Pagination -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-1 mt-auto" id="low-stock-pagination-footer">
                    <span class="text-xs text-muted" id="low-stock-pagination-info">{{ app()->getLocale() == 'ar' ? 'عرض 1 إلى 4 من 4 منتجات' : 'Showing 1 to 4 of 4 products' }}</span>
                    <nav aria-label="Product list pagination">
                        <ul class="pagination pagination-sm mb-0 gap-1" id="low-stock-pagination-controls">
                            <li class="page-item disabled" id="low-stock-prev-btn">
                                <button class="page-link rounded-3 border-0 d-flex align-items-center justify-content-center" onclick="changePage(-1)" style="width: 32px; height: 32px;"><i class="bi bi-chevron-left"></i></button>
                            </li>
                            <li class="page-item active" id="low-stock-page-num-btn">
                                <button class="page-link rounded-3 border-0 d-flex align-items-center justify-content-center active text-white" style="width: 32px; height: 32px; background-color: var(--primary-color);">1</button>
                            </li>
                            <li class="page-item disabled" id="low-stock-next-btn">
                                <button class="page-link rounded-3 border-0 d-flex align-items-center justify-content-center" onclick="changePage(1)" style="width: 32px; height: 32px;"><i class="bi bi-chevron-right"></i></button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Soon Batches Widget -->
    <div class="col-md-6 mb-4">
        <div class="card premium-inventory-card border-0 shadow-sm overflow-hidden h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="alert-icon-square d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-4" style="width: 52px; height: 52px;">
                                <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0 fw-bold text-dark tracking-tight">{{ __('purchases.expiring_soon') ?? 'Expiring Soon' }}</h4>
                                    <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 rounded-pill text-xs">{{ $expiringSoonBatches->count() }}</span>
                                </div>
                                <p class="mb-0 text-muted small">{{ __('pos.expiring_soon_subtitle') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('adjustments.index') }}" class="btn btn-premium-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-sm fw-bold">
                            <i class="bi bi-sliders"></i>
                            <span>{{ __('purchases.waste_management') }}</span>
                            <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>

                    <!-- Batches List Section -->
                    <div class="batches-list-wrapper d-flex flex-column gap-3 mb-4 flex-grow-1 overflow-y-auto" style="max-height: 440px; padding-right: 4px;">
                        @forelse($expiringSoonBatches as $b)
                            @php
                                $daysLeft = (int) round(now()->diffInDays($b->expiry_date, false));
                                $statusColor = $daysLeft < 30 ? '#EF4444' : ($daysLeft < 60 ? '#F97316' : '#EAB308');
                                $statusBg = $daysLeft < 30 ? 'rgba(239, 68, 68, 0.08)' : ($daysLeft < 60 ? 'rgba(249, 115, 22, 0.08)' : 'rgba(234, 179, 8, 0.08)');
                                $badgeClass = $daysLeft < 30 ? 'bg-danger text-white' : ($daysLeft < 60 ? 'bg-warning text-dark' : 'bg-secondary text-white');
                            @endphp
                            <div class="horizontal-product-card d-flex align-items-center justify-content-between p-3 rounded-4 border transition-all flex-wrap gap-2" style="border-color: var(--border-color) !important;">
                                <!-- Left Section -->
                                <div class="d-flex align-items-center gap-3" style="min-width: 150px; flex: 2 1 200px;">
                                    <div class="product-icon-square d-flex align-items-center justify-content-center rounded-3" style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; width: 48px; height: 48px; min-width: 48px;">
                                        <i class="bi bi-hourglass-split fs-5"></i>
                                    </div>
                                    <div class="d-flex flex-column min-width-0">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate text-sm" style="max-width: 130px;">{{ $b->product->name }}</h6>
                                        <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                                            <span class="badge bg-light-soft text-muted border text-xxs">Batch: {{ $b->batch_number }}</span>
                                            <span class="badge bg-light-soft text-muted border text-xxs">Stock: {{ (int)$b->remaining_quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Center Section (Expiry Date) -->
                                <div class="d-flex flex-column text-start" style="min-width: 90px; flex: 1 1 100px;">
                                    <span class="text-xxs text-muted text-uppercase tracking-wider">{{ __('pos.expiry_date') ?? 'Expiry Date' }}</span>
                                    <span class="text-xs text-muted fw-bold d-flex align-items-center gap-1 mt-1">
                                        <i class="bi bi-calendar-event"></i>{{ $b->expiry_date->format('d M Y') }}
                                    </span>
                                </div>
                                
                                <!-- Expiry Status Badge -->
                                <div class="d-flex flex-column px-1" style="min-width: 100px; flex: 1 1 100px;">
                                    <span class="text-xxs text-muted text-uppercase tracking-wider mb-1">{{ __('pos.status_label') }}</span>
                                    <span class="badge rounded-pill text-xxs px-2 py-1 fw-bold text-center {{ $badgeClass }}" style="width: max-content;">
                                        {{ $daysLeft }} {{ __('purchases.days_left') ?? 'Days Left' }}
                                    </span>
                                </div>
                                
                                <!-- Right Section (CTA Actions) -->
                                <div class="d-flex align-items-center justify-content-end gap-2" style="min-width: 120px; flex: 1 1 120px;">
                                    <a href="{{ route('adjustments.index') }}?product_id={{ $b->product_id }}&batch_id={{ $b->id }}" class="btn btn-restock rounded-pill px-3 py-2 text-xxs fw-bold text-white transition-all d-flex align-items-center gap-1" style="background-color: {{ $statusColor }}; border: 1px solid {{ $statusColor }};">
                                        <i class="bi bi-sliders"></i>
                                        {{ __('pos.adjust') ?? 'Adjust' }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted border rounded-4 border-dashed bg-light-soft">
                                <div class="py-4">
                                    <i class="bi bi-shield-check text-success fs-1 mb-2 d-block opacity-75"></i>
                                    <h6 class="fw-bold text-dark">{{ __('purchases.no_expiring_products_found') ?? 'No expiring products found' }}</h6>
                                    <span class="small text-muted">{{ __('purchases.no_expiring_products_found') ?? 'No expiring products found within 90 days.' }}</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Welcome Hero Card (Stunning Slate Indigo theme) */
    .welcome-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 28px !important;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 10px 10px -5px rgba(15, 23, 42, 0.04) !important;
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
    .hero-time-badge {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #ffffff;
        backdrop-filter: blur(8px);
    }
    .hero-time-badge span, .hero-time-badge i {
        color: #ffffff !important;
    }
    
    /* Glowing blobs in background */
    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.15;
        z-index: 1;
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

    /* Stats Cards Styles */
    .stat-card {
        border-radius: 28px !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.03) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 35px rgba(0, 0, 0, 0.06) !important;
    }
    
    /* Accent Bar */
    .accent-bar {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        height: 100%;
    }
    html[dir="ltr"] .accent-bar { left: 0; }
    html[dir="rtl"] .accent-bar { right: 0; }

    /* Glowing icons */
    .icon-glow {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }
    .bg-primary-soft { background-color: rgba(59, 130, 246, 0.08); }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.08); }
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.08); }
    .bg-danger-soft  { background-color: rgba(239, 68, 68, 0.08); }

    .text-warning-custom { color: #d97706; }
    .text-danger-custom { color: #ef4444; }
    
    /* Sparklines styling */
    .sparkline-container {
        height: 30px;
        display: flex;
        align-items: flex-end;
    }
    .sparkline {
        overflow: visible;
    }

    /* Soft Badges */
    .badge-soft-danger {
        background-color: rgba(239, 68, 68, 0.04);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.12);
    }
    .badge-soft-warning {
        background-color: rgba(245, 158, 11, 0.04);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.12);
    }
    
    /* Dot indicators inside badges */
    .dot-indicator {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    
    /* Widget Cards */
    .alert-widget-card {
        border-radius: 28px !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.03) !important;
    }
    .bg-light-soft {
        background-color: rgba(241, 245, 249, 0.6);
    }
    .btn-action {
        background-color: #ffffff;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .btn-action:hover {
        background-color: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    
    /* Pulse Indicators */
    .pulse-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    .pulse-indicator.bg-warning {
        animation: pulse-warning 2s infinite;
    }
    @keyframes pulse-warning {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    
    /* Alert Tables */
    .custom-alert-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s;
    }
    .custom-alert-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .custom-alert-table tbody tr:last-child {
        border-bottom: none;
    }
    .custom-alert-table thead th {
        background: #f8fafc;
        padding-top: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        color: #475569;
    }
    .custom-alert-table tbody td {
        padding-top: 18px;
        padding-bottom: 18px;
    }
    
    /* RTL Support */
    html[dir="rtl"] .hero-time-badge i {
        margin-left: 0.5rem;
        margin-right: 0;
    }

    /* Dark Mode Overrides for Dashboard */
    html[data-app-theme="dark"] .welcome-hero-card {
        background: linear-gradient(135deg, #090e1a 0%, #0f172a 100%);
    }
    html[data-app-theme="dark"] .text-dark {
        color: var(--text-color) !important;
    }
    html[data-app-theme="dark"] .stat-card .text-secondary,
    html[data-app-theme="dark"] .stat-card .text-muted {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .custom-alert-table tbody tr:hover {
        background-color: rgba(255,255,255,0.03) !important;
    }
    html[data-app-theme="dark"] .custom-alert-table thead th {
        background: rgba(0,0,0,0.15) !important;
        color: var(--text-color) !important;
    }
    html[data-app-theme="dark"] .btn-action {
        background-color: var(--card-bg) !important;
        color: var(--text-color) !important;
        border-color: var(--border-color) !important;
    }
    html[data-app-theme="dark"] .custom-alert-table tbody tr {
        border-bottom-color: var(--border-color) !important;
    }
    html[data-app-theme="dark"] .bg-light-soft {
        background-color: rgba(255,255,255,0.05) !important;
    }
    @media (max-width: 576px) {
        .custom-alert-table thead th {
            padding: 8px 4px !important;
            font-size: 10px !important;
        }
        .custom-alert-table tbody td {
            padding: 8px 4px !important;
            font-size: 11px !important;
        }
        .custom-alert-table .me-3 {
            display: none !important;
        }
        .custom-alert-table h6.text-sm {
            font-size: 11px !important;
        }
        .custom-alert-table .badge {
            padding: 3px 6px !important;
            font-size: 10px !important;
        }
        .custom-alert-table td span.text-sm {
            font-size: 11px !important;
        }
        .alert-widget-card .card-header {
            padding: 12px 16px !important;
        }
        .alert-widget-card .btn-action {
            padding: 4px 8px !important;
            font-size: 10px !important;
        }
        .alert-widget-card h6.fs-6 {
            font-size: 13px !important;
        }
    }
    
    /* Premium SaaS Card Redesign */
    .premium-inventory-card {
        border-radius: 24px !important;
        background-color: var(--card-bg) !important;
        border: 1px solid var(--border-color) !important;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.02), 0 4px 12px -2px rgba(0, 0, 0, 0.01) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-premium-secondary {
        background-color: rgba(241, 245, 249, 0.8);
        border: 1px solid rgba(226, 232, 240, 0.8);
        color: #475569;
        transition: all 0.2s ease;
    }
    .btn-premium-secondary:hover {
        background-color: #f1f5f9;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .btn-premium-secondary i {
        color: #3b82f6 !important;
    }
    
    /* Stat Subcards */
    .stat-subcard {
        border: 1px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: var(--card-bg);
    }
    .stat-subcard:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.05);
    }
    .stat-val {
        font-size: 1.6rem;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }
    
    /* Stat Subcard Colors */
    .stat-subcard.red {
        background-color: rgba(239, 68, 68, 0.02) !important;
        border-color: rgba(239, 68, 68, 0.1) !important;
    }
    .stat-subcard.red:hover {
        background-color: rgba(239, 68, 68, 0.04) !important;
        border-color: rgba(239, 68, 68, 0.2) !important;
    }
    .stat-subcard.red .stat-subcard-icon {
        background-color: rgba(239, 68, 68, 0.08);
        color: #ef4444;
        width: 38px;
        height: 38px;
    }
    .stat-subcard.red .stat-val {
        color: #ef4444 !important;
    }
    
    .stat-subcard.orange {
        background-color: rgba(249, 115, 22, 0.02) !important;
        border-color: rgba(249, 115, 22, 0.1) !important;
    }
    .stat-subcard.orange:hover {
        background-color: rgba(249, 115, 22, 0.04) !important;
        border-color: rgba(249, 115, 22, 0.2) !important;
    }
    .stat-subcard.orange .stat-subcard-icon {
        background-color: rgba(249, 115, 22, 0.08);
        color: #f97316;
        width: 38px;
        height: 38px;
    }
    .stat-subcard.orange .stat-val {
        color: #f97316 !important;
    }
    
    .stat-subcard.yellow {
        background-color: rgba(234, 179, 8, 0.02) !important;
        border-color: rgba(234, 179, 8, 0.1) !important;
    }
    .stat-subcard.yellow:hover {
        background-color: rgba(234, 179, 8, 0.04) !important;
        border-color: rgba(234, 179, 8, 0.2) !important;
    }
    .stat-subcard.yellow .stat-subcard-icon {
        background-color: rgba(234, 179, 8, 0.08);
        color: #eab308;
        width: 38px;
        height: 38px;
    }
    .stat-subcard.yellow .stat-val {
        color: #eab308 !important;
    }
    
    .stat-subcard.blue {
        background-color: rgba(59, 130, 246, 0.02) !important;
        border-color: rgba(59, 130, 246, 0.1) !important;
    }
    .stat-subcard.blue:hover {
        background-color: rgba(59, 130, 246, 0.04) !important;
        border-color: rgba(59, 130, 246, 0.2) !important;
    }
    .stat-subcard.blue .stat-subcard-icon {
        background-color: rgba(59, 130, 246, 0.08);
        color: #3b82f6;
        width: 38px;
        height: 38px;
    }
    .stat-subcard.blue .stat-val {
        color: #3b82f6 !important;
    }
    
    /* Horizontal Product Cards */
    .horizontal-product-card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color) !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        flex-wrap: wrap;
    }
    .horizontal-product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.03);
        border-color: rgba(0, 0, 0, 0.12) !important;
    }
    
    /* Search Box style */
    .search-input-wrapper {
        flex: 1 1 200px;
        max-width: 300px;
    }
    .search-input-wrapper input {
        border: 1px solid var(--border-color) !important;
        background-color: var(--bg-color) !important;
        color: var(--text-color) !important;
        width: 100%;
        transition: all 0.2s ease;
    }
    .search-input-wrapper input:focus {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
        border-color: rgba(59, 130, 246, 0.4) !important;
    }
    
    /* Slim Premium Scrollbar for Batches List */
    .batches-list-wrapper::-webkit-scrollbar {
        width: 6px;
    }
    .batches-list-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }
    .batches-list-wrapper::-webkit-scrollbar-thumb {
        background-color: var(--border-color);
        border-radius: 20px;
    }
    
    /* Filters buttons */
    .btn-filter {
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
        padding: 4px 12px;
        border: none !important;
        background: transparent;
        transition: all 0.2s ease;
    }
    .btn-filter:hover {
        color: var(--text-color);
    }
    .btn-filter.active {
        background-color: #ffffff !important;
        color: #0f172a !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    
    /* Restock Button style */
    .btn-restock {
        transition: all 0.2s ease;
        font-size: 0.7rem;
    }
    .btn-restock:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
    .btn-more-actions:hover {
        background-color: rgba(241, 245, 249, 0.6) !important;
    }
    
    /* Dark Mode Tweaks for Premium Card */
    html[data-app-theme="dark"] .premium-inventory-card {
        background-color: var(--card-bg) !important;
        border-color: var(--border-color) !important;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.25) !important;
    }
    html[data-app-theme="dark"] .premium-inventory-card .text-muted,
    html[data-app-theme="dark"] .premium-inventory-card .text-secondary,
    html[data-app-theme="dark"] .premium-inventory-card .small,
    html[data-app-theme="dark"] .premium-inventory-card .text-xxs {
        color: #cbd5e1 !important;
    }
    html[data-app-theme="dark"] .premium-inventory-card .text-dark {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .horizontal-product-card {
        background-color: rgba(30, 41, 59, 0.2);
    }
    html[data-app-theme="dark"] .horizontal-product-card:hover {
        border-color: rgba(255, 255, 255, 0.15) !important;
        background-color: rgba(30, 41, 59, 0.4);
    }
    html[data-app-theme="dark"] .btn-premium-secondary {
        background-color: rgba(30, 41, 59, 0.5) !important;
        border-color: var(--border-color) !important;
        color: var(--text-color) !important;
    }
    html[data-app-theme="dark"] .btn-premium-secondary:hover {
        background-color: rgba(30, 41, 59, 0.8) !important;
    }
    html[data-app-theme="dark"] .btn-filter.active {
        background-color: #1e293b !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    html[data-app-theme="dark"] .btn-filter {
        color: #94a3b8;
    }
    html[data-app-theme="dark"] .btn-more-actions {
        background-color: rgba(30, 41, 59, 0.4) !important;
    }
    html[data-app-theme="dark"] .btn-more-actions:hover {
        background-color: rgba(30, 41, 59, 0.8) !important;
    }
    
    /* Animations */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .horizontal-product-card {
        animation: slideIn 0.3s ease-out forwards;
    }
</style>
@endpush

@push('scripts')
<script>
    // Interactive JS for Low Stock Products Management
    let currentSearchQuery = '';
    let currentStatusFilter = 'all';
    let currentPage = 1;
    const itemsPerPage = 4;

    window.handleSearch = function() {
        const input = document.getElementById('product-search-input');
        currentSearchQuery = input.value.trim().toLowerCase();
        currentPage = 1;
        renderProductsList();
    };

    window.filterStatus = function(status, btn) {
        currentStatusFilter = status;
        currentPage = 1;
        
        // Update active button state
        document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        if (btn) {
            btn.classList.add('active');
        } else {
            // Find corresponding filter button to activate it
            const targetBtn = document.querySelector(`.btn-filter[onclick*="${status}"]`);
            if (targetBtn) targetBtn.classList.add('active');
        }
        renderProductsList();
    };

    window.changePage = function(direction) {
        currentPage += direction;
        renderProductsList();
    };

    function renderProductsList() {
        const cards = Array.from(document.querySelectorAll('.horizontal-product-card'));
        let visibleCards = [];
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const status = card.getAttribute('data-status');
            
            const matchesSearch = name.includes(currentSearchQuery);
            const matchesStatus = (currentStatusFilter === 'all') || (status === currentStatusFilter);
            
            if (matchesSearch && matchesStatus) {
                visibleCards.push(card);
            } else {
                card.style.display = 'none';
            }
        });
        
        const totalMatching = visibleCards.length;
        const totalPages = Math.ceil(totalMatching / itemsPerPage) || 1;
        
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        if (currentPage < 1) {
            currentPage = 1;
        }
        
        const startIdx = (currentPage - 1) * itemsPerPage;
        const endIdx = startIdx + itemsPerPage;
        
        visibleCards.forEach((card, idx) => {
            if (idx >= startIdx && idx < endIdx) {
                card.style.setProperty('display', 'flex', 'important');
            } else {
                card.style.setProperty('display', 'none', 'important');
            }
        });
        
        // Update footer info
        const infoTextEl = document.getElementById('low-stock-pagination-info');
        if (infoTextEl) {
            const startNum = totalMatching === 0 ? 0 : startIdx + 1;
            const endNum = Math.min(totalMatching, endIdx);
            const cardEl = document.querySelector('.premium-inventory-card');
            const pattern = "{{ app()->getLocale() == 'ar' ? 'عرض :start إلى :end من :total منتجات' : 'Showing :start to :end of :total products' }}";
            const infoText = pattern
                .replace(':start', startNum)
                .replace(':end', endNum)
                .replace(':total', totalMatching);
            infoTextEl.textContent = infoText;
        }
        
        // Update navigation controls
        const prevBtn = document.getElementById('low-stock-prev-btn');
        const nextBtn = document.getElementById('low-stock-next-btn');
        const pageNumBtn = document.getElementById('low-stock-page-num-btn');
        
        if (prevBtn) {
            if (currentPage === 1) {
                prevBtn.classList.add('disabled');
            } else {
                prevBtn.classList.remove('disabled');
            }
        }
        
        if (nextBtn) {
            if (currentPage === totalPages) {
                nextBtn.classList.add('disabled');
            } else {
                nextBtn.classList.remove('disabled');
            }
        }
        
        if (pageNumBtn) {
            const buttonEl = pageNumBtn.querySelector('button');
            if (buttonEl) {
                buttonEl.textContent = currentPage;
            }
        }
    }

    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const clockEl = document.getElementById('live-clock');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateClock();
        setInterval(updateClock, 1000);
        
        renderProductsList();
        
    });
</script>
@endpush
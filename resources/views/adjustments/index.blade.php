@extends('layouts.app')

@section('title', __('purchases.inventory_adjustments') ?? 'Inventory Adjustments')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    :root {
        --pos-brand: #3b82f6;
        --pos-brand-hover: #2563eb;
        --pos-brand-light: #eff6ff;
        --pos-surface: #ffffff;
        --pos-bg: #f8fafc;
        --pos-border: #f1f5f9;
        --pos-text-main: #0f172a;
        --pos-text-muted: #64748b;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        --shadow-soft: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    html[data-app-theme="dark"] {
        --pos-bg: #0f172a;
        --pos-surface: #1e293b;
        --pos-border: #334155;
        --pos-text-main: #f8fafc;
        --pos-text-muted: #cbd5e0;
        --pos-brand-light: rgba(59, 130, 246, 0.1);
    }

    html[data-app-theme="dark"] .text-muted {
        color: #cbd5e0 !important;
    }
    html[data-app-theme="dark"] .form-control::placeholder {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .form-control {
        color: #f8fafc !important;
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .input-group-text {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    html[data-app-theme="dark"] .step-node:not(.active) {
        color: #cbd5e0 !important;
        border-color: #475569 !important;
        background: #1e293b !important;
    }
    html[data-app-theme="dark"] .btn-outline-secondary {
        color: #cbd5e0 !important;
        border-color: #475569 !important;
    }
    html[data-app-theme="dark"] .btn-outline-secondary:hover,
    html[data-app-theme="dark"] .btn-outline-secondary.active {
        color: #0f172a !important;
        background-color: #cbd5e0 !important;
        border-color: #cbd5e0 !important;
    }

    body {
        background-color: var(--pos-bg);
        color: var(--pos-text-main);
    }

    .kpi-card {
        background: var(--pos-surface);
        border: 1px solid var(--pos-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.08);
    }

    .section-card {
        background: var(--pos-surface);
        border: 1px solid var(--pos-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .premium-card-header {
        background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%);
        color: white;
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .premium-card-header h4 {
        margin: 0;
        font-weight: 700;
        color: #ffffff !important;
    }

    .premium-card-body {
        padding: 24px;
    }

    /* Stepper Styling */
    .stepper-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
    }
    .stepper-header::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--pos-border);
        z-index: 1;
    }
    .stepper-progress-bar {
        position: absolute;
        top: 20px;
        left: 0;
        height: 3px;
        background: var(--pos-brand);
        z-index: 2;
        transition: width 0.3s ease;
        width: 0%;
    }
    html[dir="rtl"] .stepper-progress-bar {
        left: auto;
        right: 0;
    }
    .step-node {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--pos-surface);
        border: 3px solid var(--pos-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        z-index: 3;
        transition: 0.3s;
        color: var(--pos-text-muted);
        cursor: pointer;
    }
    .step-node.active {
        border-color: var(--pos-brand);
        background: var(--pos-brand);
        color: white;
        box-shadow: 0 0 0 4px var(--pos-brand-light);
    }
    .step-node.completed {
        border-color: var(--pos-brand);
        background: var(--pos-surface);
        color: var(--pos-brand);
    }

    /* Stepper Cards selection */
    .type-card {
        border: 2px solid var(--pos-border);
        border-radius: var(--radius-md);
        padding: 16px;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
        background: var(--pos-surface);
    }
    .type-card:hover {
        border-color: var(--pos-brand);
        background: var(--pos-brand-light);
    }
    .type-card.selected {
        border-color: var(--pos-brand);
        background: var(--pos-brand);
        color: white;
    }

    .reason-card {
        border: 1px solid var(--pos-border);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.15s;
        background: var(--pos-surface);
        font-size: 0.9rem;
    }
    .reason-card:hover, .reason-card.selected {
        border-color: var(--pos-brand);
        color: var(--pos-brand);
        background: var(--pos-brand-light);
    }

    /* Activity Feed */
    .activity-feed {
        position: relative;
        padding-left: 36px;
        padding-right: 16px;
    }
    html[dir="rtl"] .activity-feed {
        padding-left: 16px;
        padding-right: 36px;
    }
    .activity-feed::before {
        content: '';
        position: absolute;
        top: 8px;
        bottom: 8px;
        left: 15px;
        width: 2px;
        background: var(--pos-border);
    }
    html[dir="rtl"] .activity-feed::before {
        left: auto;
        right: 15px;
    }
    .activity-feed::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .activity-feed::-webkit-scrollbar-track {
        background: transparent;
    }
    .activity-feed::-webkit-scrollbar-thumb {
        background-color: var(--pos-border);
        border-radius: 10px;
    }
    .activity-feed::-webkit-scrollbar-thumb:hover {
        background-color: var(--pos-text-muted);
    }
    .activity-item {
        position: relative;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--pos-border);
        cursor: pointer;
        transition: transform 0.2s;
    }
    .activity-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    .activity-item:hover {
        transform: translateX(4px);
    }
    html[dir="rtl"] .activity-item:hover {
        transform: translateX(-4px);
    }
    .activity-dot {
        position: absolute;
        left: -28px;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--pos-text-muted);
        border: 3px solid var(--pos-surface);
        z-index: 2;
    }
    html[dir="rtl"] .activity-dot {
        left: auto;
        right: -28px;
    }

    .activity-dot.EXPIRED { background-color: var(--bs-danger); }
    .activity-dot.DAMAGED { background-color: var(--bs-warning); }
    .activity-dot.LOST { background-color: var(--pos-text-main); }
    .activity-dot.STOCK_COUNT_ADJUSTMENT { background-color: var(--bs-info); }
    .activity-dot.OTHER { background-color: var(--pos-text-muted); }

    /* Sliding Drawer */
    .side-drawer {
        position: fixed;
        top: 0;
        right: -450px;
        width: 420px;
        height: 100vh;
        background: var(--pos-surface);
        box-shadow: -8px 0 25px rgba(0,0,0,0.08);
        z-index: 1060;
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
        border-left: 1px solid var(--pos-border);
    }
    html[dir="rtl"] .side-drawer {
        right: auto;
        left: -450px;
        box-shadow: 8px 0 25px rgba(0,0,0,0.08);
        border-left: none;
        border-right: 1px solid var(--pos-border);
        transition: left 0.3s ease;
    }
    .side-drawer.open {
        right: 0;
    }
    html[dir="rtl"] .side-drawer.open {
        left: 0;
    }
    .drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(1px);
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transition: 0.3s;
    }
    .drawer-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .drawer-header {
        padding: 20px;
        border-bottom: 1px solid var(--pos-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--pos-bg);
    }

    .drawer-body {
        padding: 24px;
        flex: 1;
        overflow-y: auto;
    }

    /* Stepper plus/minus buttons */
    .qty-stepper {
        display: flex;
        align-items: center;
        border: 2px solid var(--pos-border);
        border-radius: var(--radius-md);
        padding: 4px;
        max-width: 220px;
    }
    .qty-stepper-btn {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        border: none;
        background: var(--pos-border);
        color: var(--pos-text-main);
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }
    .qty-stepper-btn:hover {
        background: var(--pos-brand);
        color: white;
    }
    .qty-stepper-input {
        flex: 1;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--pos-text-main);
    }
    .qty-stepper-input:focus {
        outline: none;
    }

    .batch-list-item {
        border: 1px solid var(--pos-border);
        border-radius: var(--radius-md);
        padding: 12px;
        cursor: pointer;
        transition: 0.2s;
        margin-bottom: 10px;
        background: var(--pos-surface);
    }
    .batch-list-item:hover {
        border-color: var(--pos-brand);
        background: var(--pos-brand-light);
    }
    .batch-list-item.selected {
        border-color: var(--pos-brand);
        background: var(--pos-brand-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--pos-text-muted); }

    /* Responsive Summary Table for wizard Step 5 on mobile viewports */
    @media (max-width: 576px) {
        .section-card {
            padding: 16px !important;
        }
        #step_5 table td {
            display: block !important;
            width: 100% !important;
            text-align: start !important;
            padding: 4px 0 !important;
        }
        #step_5 table tr {
            display: block !important;
            margin-bottom: 12px !important;
            border-bottom: 1px solid var(--pos-border) !important;
            padding-bottom: 8px !important;
        }
        #step_5 table tr:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <!-- Top Stats / KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card p-3 d-flex align-items-center gap-3">
                <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-calendar-x fs-3"></i>
                </div>
                <div>
                    <div class="small text-muted fw-bold text-uppercase">{{ __('purchases.expired_today') ?? 'Expired Today' }}</div>
                    <h3 class="fw-bold m-0 text-danger">{{ (int)$expiredToday }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card p-3 d-flex align-items-center gap-3">
                <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-trash3 fs-3"></i>
                </div>
                <div>
                    <div class="small text-muted fw-bold text-uppercase">{{ __('purchases.damaged_today') ?? 'Damaged Today' }}</div>
                    <h3 class="fw-bold m-0 text-warning">{{ (int)$damagedToday }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card p-3 d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-sliders fs-3"></i>
                </div>
                <div>
                    <div class="small text-muted fw-bold text-uppercase">{{ __('purchases.adjustments_today') ?? 'Adjustments Today' }}</div>
                    <h3 class="fw-bold m-0 text-primary">{{ $adjustmentsToday }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card p-3 d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-cash-coin fs-3"></i>
                </div>
                <div>
                    <div class="small text-muted fw-bold text-uppercase">{{ __('purchases.loss_value') ?? 'Loss Value' }}</div>
                    <h3 class="fw-bold m-0 text-success">{{ number_format($lossValueToday, 2) }} {{ $setting->currency ?? 'SAR' }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Side: Interactive Wizard & Expiring Soon -->
        <div class="col-lg-6">
            
            <!-- Wizard / Form Card -->
            <div class="section-card">
                <div class="premium-card-header">
                    <h4><i class="bi bi-plus-circle me-2"></i>{{ __('purchases.create_adjustment') ?? 'Create Inventory Adjustment' }}</h4>
                </div>
                
                <div class="premium-card-body">
                    <div class="stepper-header" id="stepperHeader">
                    <div class="stepper-progress-bar" id="stepperProgress"></div>
                    <div class="step-node active" data-step="1" title="Product">1</div>
                    <div class="step-node" data-step="2" title="Batch">2</div>
                    <div class="step-node" data-step="3" title="Type">3</div>
                    <div class="step-node" data-step="4" title="Qty & Reason">4</div>
                    <div class="step-node" data-step="5" title="Summary">5</div>
                </div>

                <form action="{{ route('adjustments.store') }}" method="POST" id="adjustmentWizardForm">
                    @csrf
                    <input type="hidden" name="product_id" id="wizard_product_id">
                    <input type="hidden" name="batch_id" id="wizard_batch_id">
                    <input type="hidden" name="adjustment_type" id="wizard_adjustment_type" value="EXPIRED">

                    <!-- Step 1: Product Selection -->
                    <div class="step-content" id="step_1">
                        <h5 class="fw-bold mb-3"><i class="bi bi-search me-1"></i> {{ __('purchases.step_1_product') ?? 'Step 1: Product Selection' }}</h5>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">{{ __('purchases.scan_or_search') ?? 'Scan Barcode or Search Product' }}</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-upc-scan text-muted"></i></span>
                                <input type="text" id="wizard_product_search" class="form-control form-control-lg border-start-0 ps-0" placeholder="{{ __('purchases.search_product_placeholder') ?? 'Type product name, barcode, SKU...' }}" autocomplete="off">
                            </div>
                            <div id="wizard_search_results" class="list-group shadow-sm" style="max-height: 250px; overflow-y: auto; display: none;"></div>
                        </div>
                        <div id="selected_product_preview" style="display: none;">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold fs-5" id="prev_prod_name">Product Name</div>
                                    <div class="text-muted small" id="prev_prod_barcode">{{ app()->getLocale() == 'ar' ? 'الباركود: -' : 'Barcode: -' }}</div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-end">
                                        <div class="small text-muted">{{ __('purchases.current_stock') ?? 'Current Stock' }}</div>
                                        <span class="badge bg-primary fs-6" id="prev_prod_stock">0</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle d-flex align-items-center justify-content-center" id="clear_selected_product" title="{{ __('pos.clear') ?? 'Clear' }}" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Batch Selection -->
                    <div class="step-content" id="step_2" style="display: none;">
                        <h5 class="fw-bold mb-3"><i class="bi bi-box-seam me-1"></i> {{ __('purchases.step_2_batch') ?? 'Step 2: Batch Selection' }}</h5>
                        <div id="batch_selection_list" class="mb-4" style="max-height: 300px; overflow-y: auto;">
                            <!-- Dynamically loaded batches -->
                        </div>
                        <div class="alert alert-info py-2 small" id="no_batches_alert" style="display: none;">
                            <i class="bi bi-info-circle me-1"></i> {{ __('purchases.no_active_batches') ?? 'No active batches found for this product. Stock correction will apply at product level.' }}
                        </div>
                    </div>

                    <!-- Step 3: Adjustment Type -->
                    <div class="step-content" id="step_3" style="display: none;">
                        <h5 class="fw-bold mb-3"><i class="bi bi-tags me-1"></i> {{ __('purchases.step_3_type') ?? 'Step 3: Adjustment Type' }}</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-sm-4">
                                <div class="type-card selected" data-type="EXPIRED">
                                    <i class="bi bi-calendar-x fs-2 d-block mb-2 text-danger"></i>
                                    <span class="fw-bold">{{ __('purchases.expired') ?? 'Expired' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="type-card" data-type="DAMAGED">
                                    <i class="bi bi-trash3 fs-2 d-block mb-2 text-warning"></i>
                                    <span class="fw-bold">{{ __('purchases.damaged') ?? 'Damaged' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="type-card" data-type="LOST">
                                    <i class="bi bi-eye-slash fs-2 d-block mb-2 text-secondary"></i>
                                    <span class="fw-bold">{{ __('purchases.lost') ?? 'Lost' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6">
                                <div class="type-card" data-type="STOCK_COUNT_ADJUSTMENT">
                                    <i class="bi bi-calculator fs-2 d-block mb-2 text-info"></i>
                                    <span class="fw-bold">{{ __('purchases.stock_correction') ?? 'Stock Correction' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6">
                                <div class="type-card" data-type="OTHER">
                                    <i class="bi bi-gear fs-2 d-block mb-2 text-dark"></i>
                                    <span class="fw-bold">{{ __('purchases.other') ?? 'Other' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Quantity, Unit, Reason & Notes -->
                    <div class="step-content" id="step_4" style="display: none;">
                        <h5 class="fw-bold mb-3"><i class="bi bi-sliders2-vertical me-1"></i> {{ __('purchases.step_4_details') ?? 'Step 4: Adjustment Details' }}</h5>
                        
                        <!-- Unit Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">{{ __('purchases.select_unit') ?? 'Select Unit' }}</label>
                            <select name="product_unit_id" id="wizard_product_unit_id" class="form-select form-select-lg"></select>
                        </div>

                        <!-- Quantity Stepper -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">{{ __('purchases.adjustment_quantity') ?? 'Adjustment Quantity' }}</label>
                            <div class="qty-stepper">
                                <button type="button" class="qty-stepper-btn" id="qty_minus"><i class="bi bi-dash"></i></button>
                                <input type="number" name="quantity" id="wizard_quantity" class="qty-stepper-input" value="1" min="1" step="1">
                                <button type="button" class="qty-stepper-btn" id="qty_plus"><i class="bi bi-plus"></i></button>
                            </div>
                            <div class="form-text text-danger" id="qty_validation_msg" style="display: none;"></div>
                        </div>

                        <!-- Conversion Preview -->
                        <div class="p-3 bg-light rounded-4 mb-4" id="conversion_preview_box" style="display: none; border: 1px dashed var(--pos-border);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted fw-bold small">{{ __('purchases.entered_quantity') ?? 'Entered Quantity' }}</span>
                                <span class="fw-bold text-dark" id="preview_entered_qty_display">1 Carton</span>
                            </div>
                            <div class="text-center my-2 text-muted fw-bold">
                                <i class="bi bi-arrow-down-up"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted fw-bold small">{{ __('purchases.equivalent_in_base_units') ?? 'Equivalent in Base Units' }}</span>
                                <span class="fw-bold text-primary" id="preview_converted_qty_display">24 Bottles</span>
                            </div>
                            <hr class="my-2" style="border-top: 1px dashed var(--pos-border);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-bold small">{{ __('purchases.inventory_stock_impact') ?? 'Inventory Stock Impact' }}</span>
                                <span class="fw-bold text-danger fs-5" id="preview_stock_impact_display">-24 Bottles</span>
                            </div>
                        </div>

                        <!-- Validation Message -->
                        <div class="alert alert-danger py-2 small" id="wizard_qty_validation_msg" style="display: none;">
                            <i class="bi bi-exclamation-triangle me-1"></i> {{ __('purchases.insufficient_batch_stock') ?? 'Insufficient stock available in selected batch.' }}
                        </div>

                        <!-- Reason -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">{{ __('purchases.reason') ?? 'Reason' }}</label>
                            <input type="text" name="reason" id="wizard_reason" class="form-control form-control-lg" placeholder="{{ __('purchases.type_custom_reason') ?? 'Type custom reason...' }}" required>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">{{ __('purchases.notes') ?? 'Additional Notes' }}</label>
                            <textarea name="notes" id="wizard_notes" class="form-control" rows="2" placeholder="{{ __('purchases.notes_placeholder') ?? 'Any extra details...' }}"></textarea>
                        </div>
                    </div>

                    <!-- Step 5: Summary -->
                    <div class="step-content" id="step_5" style="display: none;">
                        <h5 class="fw-bold mb-3"><i class="bi bi-receipt me-1"></i> {{ __('purchases.step_5_summary') ?? 'Step 5: Adjustment Summary' }}</h5>
                        
                        <div class="p-3 bg-light rounded-3 mb-4">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted fw-bold">{{ __('purchases.product') }}</td>
                                    <td class="fw-bold text-end" id="sum_product">-</td>
                                </tr>
                                <tr id="sum_batch_row">
                                    <td class="text-muted fw-bold">{{ __('purchases.batch') }}</td>
                                    <td class="fw-bold text-end" id="sum_batch">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">{{ __('purchases.adjustment_type') }}</td>
                                    <td class="fw-bold text-end" id="sum_type">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">{{ __('purchases.selected_unit') ?? 'Selected Unit' }}</td>
                                    <td class="fw-bold text-end" id="sum_unit">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">{{ __('purchases.entered_quantity') ?? 'Entered Quantity' }}</td>
                                    <td class="fw-bold text-end" id="sum_entered_qty">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">{{ __('purchases.quantity') }} ({{ __('purchases.base_unit') ?? 'Base Unit' }})</td>
                                    <td class="fw-bold text-end text-danger fs-5" id="sum_qty">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">{{ __('purchases.current_stock') }}</td>
                                    <td class="fw-bold text-end" id="sum_current_stock">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">{{ __('purchases.stock_after_adjustment') }}</td>
                                    <td class="fw-bold text-end text-success fs-5" id="sum_after_stock">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">{{ __('purchases.reason') ?? 'Reason' }}</td>
                                    <td class="fw-bold text-end text-primary" id="sum_reason">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary px-4 fw-bold" id="prevBtn" style="display: none;"><i class="bi bi-arrow-left me-1"></i> {{ __('pos.back') ?? 'Back' }}</button>
                        <button type="button" class="btn btn-primary px-4 fw-bold ms-auto" id="nextBtn">{{ __('pos.next') ?? 'Next' }} <i class="bi bi-arrow-right ms-1"></i></button>
                        <button type="button" class="btn btn-success px-4 fw-bold ms-auto" id="submitBtn" style="display: none;"><i class="bi bi-check-lg me-1"></i> {{ __('purchases.confirm_and_create') ?? 'Confirm & Create' }}</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Recent Activity Feed -->
        <div class="col-lg-6">
            <div class="section-card h-100 d-flex flex-column">
                <div class="premium-card-header">
                    <h4><i class="bi bi-clock-history me-2"></i>{{ __('purchases.recent_adjustments') ?? 'Recent Adjustments' }}</h4>
                    
                    <div class="btn-group bg-white rounded shadow-sm" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active filter-btn" data-filter="all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="today">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="week">Week</button>
                    </div>
                </div>

                <div class="activity-feed flex-grow-1" style="max-height: 60vh; overflow-y: auto;">
                    @forelse($adjustments as $adj)
                        @php
                            $labelColor = match($adj->adjustment_type) {
                                'EXPIRED' => 'text-danger',
                                'DAMAGED' => 'text-warning',
                                'LOST' => 'text-dark',
                                'STOCK_COUNT_ADJUSTMENT' => 'text-info',
                                default => 'text-secondary'
                            };
                            $typeText = __('purchases.' . strtolower($adj->adjustment_type)) ?? $adj->adjustment_type;
                            $daysDiff = $adj->created_at->diffForHumans();
                        @endphp
                        <div class="activity-item" onclick="openDetailsDrawer({{ $adj->id }})" data-date-group="{{ $adj->created_at->isToday() ? 'today' : ($adj->created_at->gt(now()->subWeek()) ? 'week' : 'older') }}">
                            <div class="activity-dot {{ $adj->adjustment_type }}"></div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-light text-dark border fw-bold">{{ $adj->short_number }}</span>
                                    <h6 class="fw-bold mt-2 mb-1">{{ $adj->product->name }}</h6>
                                    @if($adj->batch)
                                        <div class="small text-muted mb-1">{{ app()->getLocale() == 'ar' ? 'الدفعة:' : 'Batch:' }} <code class="text-primary">{{ $adj->batch->batch_number }}</code></div>
                                    @endif
                                    <div class="small text-muted">{{ app()->getLocale() == 'ar' ? 'الكمية المدخلة:' : 'Entered:' }} <span class="fw-bold text-dark">{{ (float)($adj->entered_quantity ?? $adj->quantity) }} {{ $adj->productUnit ? $adj->productUnit->unit_name : ($adj->product->base_unit_name ?? 'Piece') }}</span></div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold fs-6 {{ $adj->quantity < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ app()->getLocale() == 'ar' ? 'التأثير:' : 'Impact:' }} {{ $adj->quantity > 0 ? '+' : '' }}{{ (float)$adj->quantity }} {{ $adj->product->base_unit_name ?? 'Piece' }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ $daysDiff }}</div>
                                    <div class="small fw-semibold mt-1 {{ $labelColor }}">{{ $typeText }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-slash-circle fs-1 mb-2 d-block"></i>
                            {{ __('purchases.no_adjustments_recorded') ?? 'No adjustments recorded.' }}
                        </div>
                    @endforelse
                </div>

                @if($adjustments->hasPages())
                    <div class="mt-4">
                        {{ $adjustments->links() }}
                    </div>
                @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Drawer Overlay -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDetailsDrawer()"></div>

<!-- Sliding Details Side Drawer -->
<div class="side-drawer" id="detailsDrawer">
    <div class="drawer-header premium-card-header">
        <h5 class="fw-bold m-0 text-white"><i class="bi bi-info-circle me-1"></i> Adjustment Details</h5>
        <button class="btn-close btn-close-white" onclick="closeDetailsDrawer()"></button>
    </div>
    <div class="drawer-body" id="drawerBody">
        <!-- Content injected dynamically via JavaScript -->
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Variables & Predefined reasons for types
    const PREDEFINED_REASONS = {
        'EXPIRED': ['Expired Product', 'Exceeded Safe Store Date', 'Chemical Instability'],
        'DAMAGED': ['Damaged Packaging', 'Broken Bottle', 'Leaked Liquid', 'Temperature Damage'],
        'LOST': ['Lost During Inventory', 'Stolen Item', 'Mishandled'],
        'STOCK_COUNT_ADJUSTMENT': ['Stock Count Difference', 'System Correction', 'Audit Reconciliation'],
        'OTHER': ['Other']
    };

    let currentStep = 1;
    let selectedProduct = null;
    let selectedBatch = null;

    $(document).ready(function() {
        // Timeline filtering
        $('.filter-btn').on('click', function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            const filter = $(this).data('filter');
            
            $('.activity-item').each(function() {
                const group = $(this).data('date-group');
                if (filter === 'all') {
                    $(this).show();
                } else if (filter === 'today') {
                    if (group === 'today' || group === 'week' && (new Date() - new Date($(this).data('created-at')) < 86400000)) $(this).show(); // just in case
                    else if (group === 'today') $(this).show();
                    else $(this).hide();
                } else if (filter === 'week') {
                    if (group === 'today' || group === 'week') $(this).show();
                    else $(this).hide();
                }
            });
        });

        // Step 1: Product Autocomplete search
        $('#wizard_product_search').on('input', function() {
            const query = $(this).val().trim();
            if (!query) {
                $('#wizard_search_results').hide().empty();
                return;
            }

            $.ajax({
                url: "{{ route('products.search') }}",
                data: { term: query },
                dataType: 'json',
                success: function(data) {
                    $('#wizard_search_results').empty();
                    if (data.length === 0) {
                        const noProductsText = isAr ? 'لم يتم العثور على منتجات' : 'No products found';
                        $('#wizard_search_results').append(`<div class="list-group-item text-muted">${noProductsText}</div>`).show();
                        return;
                    }
                    data.forEach((prod, i) => {
                        const barcodeLabel = isAr ? 'الباركود:' : 'Barcode:';
                        const notAvailable = isAr ? 'غير متوفر' : 'N/A';
                        const itemHtml = `
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center wizard-prod-opt ${i === 0 ? 'active' : ''}" 
                               data-id="${prod.id}" 
                               data-name="${prod.text}" 
                               data-barcode="${prod.barcode || ''}"
                               data-stock="${prod.stock || 0}">
                               <div>
                                    <div class="fw-bold">${prod.text}</div>
                                    <small class="text-muted">${barcodeLabel} ${prod.barcode || notAvailable}</small>
                               </div>
                               <span class="badge bg-secondary">${prod.stock || 0}</span>
                            </a>
                        `;
                        $('#wizard_search_results').append(itemHtml);
                    });
                    $('#wizard_search_results').show();
                }
            });
        });

        // Autocomplete Keyboard Navigation
        $('#wizard_product_search').on('keydown', function(e) {
            const listItems = $('#wizard_search_results .wizard-prod-opt');
            let activeItem = $('#wizard_search_results .wizard-prod-opt.active');
            let index = listItems.index(activeItem);

            if (e.which === 40) { // Arrow Down
                e.preventDefault();
                if (listItems.length === 0) return;
                
                if (activeItem.length === 0 || index === listItems.length - 1) {
                    listItems.removeClass('active');
                    listItems.first().addClass('active');
                } else {
                    listItems.removeClass('active');
                    listItems.eq(index + 1).addClass('active');
                }
                
                // Scroll to active item if needed
                const newActive = $('#wizard_search_results .wizard-prod-opt.active');
                if (newActive.length) {
                    newActive[0].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.which === 38) { // Arrow Up
                e.preventDefault();
                if (listItems.length === 0) return;

                if (activeItem.length === 0 || index === 0) {
                    listItems.removeClass('active');
                    listItems.last().addClass('active');
                } else {
                    listItems.removeClass('active');
                    listItems.eq(index - 1).addClass('active');
                }
                
                // Scroll to active item if needed
                const newActive = $('#wizard_search_results .wizard-prod-opt.active');
                if (newActive.length) {
                    newActive[0].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.which === 13) { // Enter key
                e.preventDefault();
                if (activeItem.length) {
                    activeItem.click(); // Trigger click event
                }
            }
        });

        // Sync hover with active item class
        $(document).on('mouseenter', '.wizard-prod-opt', function() {
            $('#wizard_search_results .wizard-prod-opt').removeClass('active');
            $(this).addClass('active');
        });

        // Select product from search results
        $(document).on('click', '.wizard-prod-opt', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const barcode = $(this).data('barcode');
            const stock = $(this).data('stock');

            selectedProduct = { id, name, barcode, stock };
            $('#wizard_product_id').val(id);
            $('#prev_prod_name').text(name);
            $('#prev_prod_barcode').text((isAr ? 'الباركود: ' : 'Barcode: ') + (barcode || (isAr ? 'غير متوفر' : 'N/A')));
            $('#prev_prod_stock').text(stock);

            $('#selected_product_preview').fadeIn();
            $('#wizard_search_results').hide().empty();
            $('#wizard_product_search').val('');

            // Automatically load batches for step 2
            loadProductBatches(id);
            // Load units
            loadProductUnits(id);

            // Auto-advance to Step 2 (Batch Selection)
            goToStep(2);
        });

        // Clear selected product trigger
        $(document).on('click', '#clear_selected_product', function(e) {
            e.preventDefault();
            selectedProduct = null;
            selectedBatch = null;
            $('#wizard_product_id').val('');
            $('#wizard_batch_id').val('');
            $('#selected_product_preview').hide();
            $('#wizard_product_search').val('').focus();
            
            // Clear batch step selections
            $('#batch_selection_list').empty();
            $('#no_batches_alert').hide();
        });

        // Step 2: Batch Selection click
        $(document).on('click', '.batch-list-item', function() {
            $('.batch-list-item').removeClass('selected');
            $(this).addClass('selected');
            const batchId = $(this).data('batch-id');
            const num = $(this).data('batch-number');
            const stock = $(this).data('stock');
            const exp = $(this).data('expiry');

            selectedBatch = { id: batchId, number: num, stock, expiry: exp };
            $('#wizard_batch_id').val(batchId);

            // Update Validation max quantity
            $('#wizard_quantity').attr('max', stock);

            // Auto-advance to Step 3 (Adjustment Type)
            goToStep(3);
        });

        // Step 3: Selectable adjustment type cards
        $('.type-card').on('click', function() {
            $('.type-card').removeClass('selected');
            $(this).addClass('selected');
            const type = $(this).data('type');
            $('#wizard_adjustment_type').val(type);
            
            // Build predefined reasons
            buildPredefinedReasons(type);

            // Auto-advance to Step 4 (Quantity & Reason)
            goToStep(4);
        });

        // Step 4: Reasons click
        $(document).on('click', '.reason-card', function() {
            $('.reason-card').removeClass('selected');
            $(this).addClass('selected');
            const reason = $(this).text();
            $('#wizard_reason').val(reason);
        });

        $('#wizard_reason').on('input', function() {
            $('.reason-card').removeClass('selected');
        });

        // Stepper Quantity Increment/Decrement
        $('#qty_plus').on('click', function() {
            const input = $('#wizard_quantity');
            const max = parseInt(input.attr('max')) || 999999;
            const current = parseInt(input.val()) || 0;
            if (current < max) {
                input.val(current + 1).trigger('change');
            }
        });

        $('#qty_minus').on('click', function() {
            const input = $('#wizard_quantity');
            const current = parseInt(input.val()) || 1;
            if (current > 1) {
                input.val(current - 1).trigger('change');
            }
        });

        $('#wizard_quantity, #wizard_product_unit_id, #wizard_adjustment_type').on('input change', function() {
            updateConversionPreview();
        });

        // Wizard Navigation Buttons
        $('#nextBtn').on('click', function() {
            if (validateStep(currentStep)) {
                goToStep(currentStep + 1);
            }
        });

        $('#prevBtn').on('click', function() {
            goToStep(currentStep - 1);
        });

        // Confirm and Submit
        $('#submitBtn').on('click', function() {
            Swal.fire({
                title: "{{ __('pos.are_you_sure') ?? 'Are you sure?' }}",
                text: "{{ __('purchases.confirm_submit_adjustment') ?? 'Do you want to submit this inventory adjustment?' }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('purchases.yes_confirm') ?? 'Yes, Confirm!' }}",
                cancelButtonText: "{{ __('pos.cancel') ?? 'Cancel' }}",
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0 p-4',
                    title: 'fw-bold text-dark fs-4 mb-2',
                    htmlContainer: 'text-muted fs-6 mb-4',
                    confirmButton: 'btn btn-success px-4 py-2.5 fw-bold rounded-3 mx-2 shadow-sm',
                    cancelButton: 'btn btn-light border px-4 py-2.5 fw-bold rounded-3 mx-2 text-dark'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#adjustmentWizardForm').submit();
                }
            });
        });
        // Press Enter to go Next or Submit
        $('#wizard_quantity, #wizard_reason, #wizard_notes').on('keydown', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $('#nextBtn').click();
            }
        });

        $(document).on('keydown', function(e) {
            if (e.which === 13) {
                if (currentStep === 5) {
                    if (typeof Swal !== 'undefined' && !Swal.isVisible()) {
                        e.preventDefault();
                        $('#submitBtn').click();
                    }
                }
            }
        });
        // Build default reasons
        buildPredefinedReasons('EXPIRED');

        // Pre-fill from URL parameters if provided
        const urlParams = new URLSearchParams(window.location.search);
        const prefillProductId = urlParams.get('product_id');
        const prefillBatchId = urlParams.get('batch_id');
        if (prefillProductId && prefillBatchId) {
            const productsList = {
                @foreach($products as $p)
                    "{{ $p->id }}": "{{ addslashes($p->name) }}",
                @endforeach
            };
            const prodName = productsList[prefillProductId] || 'Product';

            $.get(`/products/batches/${prefillProductId}`, function(data) {
                $('#batch_selection_list').empty();
                if (data.length === 0) {
                    $('#no_batches_alert').show();
                    return;
                }
                $('#no_batches_alert').hide();
                let targetBatch = null;
                data.forEach(batch => {
                    const exp = batch.expiry_date ? batch.expiry_date.substring(0, 10) : (isAr ? 'غير متوفر' : 'N/A');
                    const isTarget = String(batch.id) === String(prefillBatchId);
                    const expiryLabel = isAr ? 'تاريخ الانتهاء:' : 'Expiry:';
                    const stockLabel = isAr ? 'المخزون' : 'Stock';
                    const batchHtml = `
                        <div class="batch-list-item ${isTarget ? 'selected' : ''}" data-batch-id="${batch.id}" data-batch-number="${batch.batch_number}" data-stock="${batch.quantity}" data-expiry="${exp}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold fs-6 text-primary">${batch.batch_number}</span>
                                    <div class="small text-muted mt-1">${expiryLabel} ${exp}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted">${stockLabel}</div>
                                    <span class="fw-bold text-dark fs-6">${parseInt(batch.quantity)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#batch_selection_list').append(batchHtml);
                    if (isTarget) {
                        targetBatch = batch;
                    }
                });

                if (targetBatch) {
                    selectedProduct = { id: prefillProductId, name: prodName, barcode: '', stock: targetBatch.quantity };
                    $('#wizard_product_id').val(prefillProductId);
                    $('#prev_prod_name').text(prodName);
                    $('#prev_prod_stock').text(targetBatch.quantity);
                    $('#selected_product_preview').show();

                    selectedBatch = { id: targetBatch.id, number: targetBatch.batch_number, stock: targetBatch.quantity, expiry: targetBatch.expiry_date };
                    $('#wizard_batch_id').val(targetBatch.id);
                    $('#wizard_quantity').attr('max', targetBatch.quantity);

                    loadProductUnits(prefillProductId);

                    goToStep(3); // jump directly to Type step
                    $('html, body').animate({
                        scrollTop: $("#stepperHeader").offset().top - 100
                    }, 300);
                }
            });
        }
    });

    function loadProductBatches(productId) {
        $.get(`/products/batches/${productId}`, function(data) {
            $('#batch_selection_list').empty();
            if (data.length === 0) {
                $('#no_batches_alert').show();
                selectedBatch = null;
                $('#wizard_batch_id').val('');
                return;
            }
            $('#no_batches_alert').hide();
            data.forEach(batch => {
                const exp = batch.expiry_date ? batch.expiry_date.substring(0, 10) : (isAr ? 'غير متوفر' : 'N/A');
                const expiryLabel = isAr ? 'تاريخ الانتهاء:' : 'Expiry:';
                const stockLabel = isAr ? 'المخزون' : 'Stock';
                const batchHtml = `
                    <div class="batch-list-item" data-batch-id="${batch.id}" data-batch-number="${batch.batch_number}" data-stock="${batch.quantity}" data-expiry="${exp}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold fs-6 text-primary">${batch.batch_number}</span>
                                <div class="small text-muted mt-1">${expiryLabel} ${exp}</div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted">${stockLabel}</div>
                                <span class="fw-bold text-dark fs-6">${parseInt(batch.quantity)}</span>
                            </div>
                        </div>
                    </div>
                `;
                $('#batch_selection_list').append(batchHtml);
            });
        });
    }

    // Predefined reasons for different languages
    const isAr = "{{ app()->getLocale() }}" === 'ar';
    const PREDEFINED_REASONS_TRANSLATED = {
        'EXPIRED': isAr ? ['منتج منتهي الصلاحية', 'تجاوز فترة التخزين الآمنة', 'تلف كيميائي/عدم استقرار'] : ['Expired Product', 'Exceeded Safe Store Date', 'Chemical Instability'],
        'DAMAGED': isAr ? ['تلف العبوة الخارجية', 'زجاجة مكسورة', 'تسرب السوائل', 'ضرر درجات الحرارة'] : ['Damaged Packaging', 'Broken Bottle', 'Leaked Liquid', 'Temperature Damage'],
        'LOST': isAr ? ['مفقود أثناء الجرد', 'سرقة', 'سوء مناولة'] : ['Lost During Inventory', 'Stolen Item', 'Mishandled'],
        'STOCK_COUNT_ADJUSTMENT': isAr ? ['فرق في الجرد الفعلي', 'تصحيح نظامي', 'تسوية تدقيق الحسابات'] : ['Stock Count Difference', 'System Correction', 'Audit Reconciliation'],
        'OTHER': isAr ? ['أخرى'] : ['Other']
    };

    function buildPredefinedReasons(type) {
        // Disabled since quick suggestions are deleted
    }

    let productUnits = [];
    let baseUnitName = 'Piece';

    function loadProductUnits(productId) {
        $.get(`/products/units/${productId}`, function(data) {
            baseUnitName = data.base_unit_name || 'Piece';
            productUnits = data.units || [];
            
            const select = $('#wizard_product_unit_id');
            select.empty();
            select.append(`<option value="" data-factor="1" data-name="${baseUnitName}">${baseUnitName} (${isAr ? 'الوحدة الأساسية' : 'Base Unit'})</option>`);
            productUnits.forEach(unit => {
                select.append(`<option value="${unit.id}" data-factor="${unit.conversion_factor}" data-name="${unit.unit_name}">${unit.unit_name} (x${parseInt(unit.conversion_factor)})</option>`);
            });
            
            updateConversionPreview();
        });
    }

    function updateConversionPreview() {
        const qty = parseFloat($('#wizard_quantity').val()) || 0;
        const selectedOption = $('#wizard_product_unit_id option:selected');
        const factor = parseFloat(selectedOption.data('factor')) || 1.0;
        const unitName = selectedOption.data('name') || 'Unit';
        
        const convertedQty = qty * factor;
        
        $('#preview_entered_qty_display').text(qty + ' ' + unitName);
        $('#preview_converted_qty_display').text(convertedQty + ' ' + baseUnitName);
        
        const type = $('#wizard_adjustment_type').val();
        let sign = '';
        if (['EXPIRED', 'DAMAGED', 'LOST'].indexOf(type) !== -1) {
            sign = '-';
        }
        $('#preview_stock_impact_display').text(sign + convertedQty + ' ' + baseUnitName);
        $('#conversion_preview_box').show();
        
        validateQuantityLive(convertedQty);
    }

    function validateQuantityLive(convertedQty) {
        const currentStock = selectedBatch ? selectedBatch.stock : (selectedProduct ? selectedProduct.stock : 0);
        if (convertedQty > currentStock) {
            $('#wizard_qty_validation_msg').show();
            $('#nextBtn').attr('disabled', true);
        } else {
            $('#wizard_qty_validation_msg').hide();
            $('#nextBtn').attr('disabled', false);
        }
    }

    function validateStep(step) {
        if (step === 1) {
            if (!selectedProduct) {
                Swal.fire(isAr ? 'خطأ' : 'Error', isAr ? 'يرجى اختيار منتج أولاً.' : 'Please select a product first.', 'error');
                return false;
            }
        }
        if (step === 2) {
            const hasBatches = $('.batch-list-item').length > 0;
            if (hasBatches && !selectedBatch) {
                Swal.fire(isAr ? 'خطأ' : 'Error', isAr ? 'يرجى اختيار رقم الدفعة (Batch).' : 'Please select a batch.', 'error');
                return false;
            }
        }
        if (step === 4) {
            const qty = parseFloat($('#wizard_quantity').val()) || 0;
            const factor = parseFloat($('#wizard_product_unit_id option:selected').data('factor')) || 1.0;
            const convertedQty = qty * factor;
            if (qty <= 0) {
                Swal.fire(isAr ? 'خطأ' : 'Error', isAr ? 'يجب أن تكون الكمية 1 على الأقل.' : 'Quantity must be at least 1.', 'error');
                return false;
            }
            const currentStock = selectedBatch ? selectedBatch.stock : selectedProduct.stock;
            if (convertedQty > currentStock) {
                const msg = isAr 
                    ? `الكمية لا يمكن أن تتجاوز المخزون المتبقي للدفعة (${currentStock} ${baseUnitName}).` 
                    : `Quantity cannot exceed remaining batch stock (${currentStock} ${baseUnitName}).`;
                Swal.fire(isAr ? 'خطأ' : 'Error', msg, 'error');
                return false;
            }
        }
        return true;
    }

    function goToStep(step) {
        $('.step-content').hide();
        $(`#step_${step}`).fadeIn();

        // Update step nodes status
        $('.step-node').removeClass('active completed');
        for (let i = 1; i <= 5; i++) {
            const node = $(`.step-node[data-step="${i}"]`);
            if (i === step) {
                node.addClass('active');
            } else if (i < step) {
                node.addClass('completed');
            }
        }

        // Update progress bar
        const progressPct = ((step - 1) / 4) * 100;
        $('#stepperProgress').css('width', progressPct + '%');

        currentStep = step;

        // Show/hide navigation buttons
        if (step === 1) {
            $('#prevBtn').hide();
            $('#nextBtn').show();
            $('#submitBtn').hide();
        } else if (step === 5) {
            $('#prevBtn').show();
            $('#nextBtn').hide();
            $('#submitBtn').show();
            buildSummary();
        } else {
            $('#prevBtn').show();
            $('#nextBtn').show();
            $('#submitBtn').hide();
        }
    }

    function buildSummary() {
        const type = $('#wizard_adjustment_type').val();
        const qty = parseFloat($('#wizard_quantity').val()) || 0;
        const selectedOption = $('#wizard_product_unit_id option:selected');
        const factor = parseFloat(selectedOption.data('factor')) || 1.0;
        const unitName = selectedOption.data('name') || 'Unit';
        const convertedQty = qty * factor;
        const currentStock = selectedBatch ? selectedBatch.stock : selectedProduct.stock;
        
        $('#sum_product').text(selectedProduct.name);
        
        if (selectedBatch) {
            $('#sum_batch_row').show();
            $('#sum_batch').text(selectedBatch.number);
        } else {
            $('#sum_batch_row').hide();
        }

        const typeTranslations = {
            'EXPIRED': isAr ? 'منتهي الصلاحية' : 'Expired',
            'DAMAGED': isAr ? 'تالف' : 'Damaged',
            'LOST': isAr ? 'مفقود' : 'Lost',
            'STOCK_COUNT_ADJUSTMENT': isAr ? 'تسوية الجرد' : 'Stock Count Adjustment',
            'OTHER': isAr ? 'أخرى' : 'Other'
        };
        $('#sum_type').text(typeTranslations[type] || type);
        $('#sum_unit').text(unitName);
        $('#sum_entered_qty').text(qty);
        $('#sum_qty').text('-' + convertedQty);
        $('#sum_current_stock').text(currentStock + ' ' + baseUnitName);
        $('#sum_after_stock').text((currentStock - convertedQty) + ' ' + baseUnitName);
        $('#sum_reason').text($('#wizard_reason').val() || 'N/A');
    }

    // Side Drawer Details
    function openDetailsDrawer(id) {
        $('#drawerBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
        $('#drawerOverlay').addClass('show');
        $('#detailsDrawer').addClass('open');

        $.get(`/adjustments/${id}`, function(data) {
            const statusClass = matchStatusClass(data.adjustment_type);
            const typeTranslations = {
                'EXPIRED': isAr ? 'منتهي الصلاحية' : 'Expired',
                'DAMAGED': isAr ? 'تالف' : 'Damaged',
                'LOST': isAr ? 'مفقود' : 'Lost',
                'STOCK_COUNT_ADJUSTMENT': isAr ? 'تسوية الجرد' : 'Stock Count Adjustment',
                'OTHER': isAr ? 'أخرى' : 'Other'
            };
            const translatedType = typeTranslations[data.adjustment_type] || data.adjustment_type;
            const html = `
                <div class="mb-4 text-center">
                    <span class="badge bg-light text-dark border fs-6 px-3 py-2 fw-bold">${data.short_number}</span>
                    <h5 class="fw-bold mt-3">${data.product.name}</h5>
                    <div class="text-muted small">${isAr ? 'الباركود:' : 'Barcode:'} ${data.product.barcode || (isAr ? 'غير متوفر' : 'N/A')}</div>
                </div>

                <div class="p-3 border rounded-3 mb-4 bg-light">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted fw-bold">{{ __('purchases.adjustment_type') ?? 'Adjustment Type' }}</td>
                            <td class="text-end fw-bold ${statusClass}">${translatedType}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">{{ __('purchases.entered_quantity') ?? 'Entered Quantity' }}</td>
                            <td class="text-end fw-bold">${data.entered_quantity} ${data.product_unit}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">{{ __('purchases.inventory_stock_impact') ?? 'Inventory Stock Impact' }}</td>
                            <td class="text-end fw-bold text-danger fs-5">${data.quantity}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">{{ __('purchases.loss_value') ?? 'Loss Value' }}</td>
                            <td class="text-end fw-bold text-danger">${data.loss_value} {{ $setting->currency ?? 'SAR' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-muted small text-uppercase">{{ __('purchases.tracking_details') ?? 'Tracking Details' }}</h6>
                    <div class="p-2 border rounded-2 bg-white">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('purchases.batch_number') ?? 'Batch Number' }}:</span>
                            <span class="fw-bold text-primary">${data.batch ? data.batch.batch_number : '{{ __('purchases.not_available') ?? 'N/A' }}'}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('purchases.expiry_date') ?? 'Expiry Date' }}:</span>
                            <span>${data.batch ? data.batch.expiry_date : '{{ __('purchases.not_available') ?? 'N/A' }}'}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('purchases.movement_reference') ?? 'Movement Reference' }}:</span>
                            <span class="fw-bold">${data.movement_reference}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-muted small text-uppercase">{{ __('purchases.reason') ?? 'Reason' }} & {{ __('purchases.notes') ?? 'Notes' }}</h6>
                    <div class="p-3 border rounded-2 bg-white">
                        <div class="mb-2"><strong>{{ __('purchases.reason') ?? 'Reason' }}:</strong> ${data.reason || '{{ __('purchases.not_available') ?? 'N/A' }}'}</div>
                        <div><strong>{{ __('purchases.notes') ?? 'Notes' }}:</strong> ${data.notes || '{{ __('purchases.no_notes') ?? 'No extra notes.' }}'}</div>
                    </div>
                </div>

                <div class="small text-muted text-center mt-5">
                    {{ __('pos.created_by') ?? 'Created by' }}: <strong>${data.user.name}</strong><br>
                    {{ __('pos.date') ?? 'Date' }}: ${data.created_at}
                </div>
            `;
            $('#drawerBody').html(html);
        }).fail(function() {
            $('#drawerBody').html('<div class="alert alert-danger">Failed to load adjustment details.</div>');
        });
    }

    function closeDetailsDrawer() {
        $('#detailsDrawer').removeClass('open');
        $('#drawerOverlay').removeClass('show');
    }

    function matchStatusClass(type) {
        switch (type) {
            case 'EXPIRED': return 'text-danger';
            case 'DAMAGED': return 'text-warning';
            case 'LOST': return 'text-dark';
            case 'STOCK_COUNT_ADJUSTMENT': return 'text-info';
            default: return 'text-secondary';
        }
    }

    // Quick adjustment trigger from expiring soon section
    function quickAdjustExpiring(productId, batchId, prodName, batchNum, stock) {
        selectedProduct = { id: productId, name: prodName, barcode: '', stock: stock };
        $('#wizard_product_id').val(productId);
        $('#prev_prod_name').text(prodName);
        $('#prev_prod_stock').text(stock);
        $('#selected_product_preview').show();

        selectedBatch = { id: batchId, number: batchNum, stock: stock, expiry: '' };
        $('#wizard_batch_id').val(batchId);
        
        loadProductBatches(productId);

        goToStep(3); // jump directly to Type step
        $('html, body').animate({
            scrollTop: $("#stepperHeader").offset().top - 100
        }, 300);
    }
</script>
@endpush

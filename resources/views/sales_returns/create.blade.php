@extends('layouts.app')

@section('title', __('pos.add') . ' ' . __('pos.sales_returns'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --pos-brand: #3b82f6; /* Blue 500 */
        --pos-brand-hover: #2563eb; /* Blue 600 */
        --pos-brand-light: #eff6ff; /* Blue 50 */
        --pos-bg: #f8fafc;
        --pos-surface: #ffffff;
        --pos-border: #e2e8f0;
        --pos-text-main: #0f172a;
        --pos-text-muted: #64748b;
        --pos-danger: #ef4444;
        --pos-danger-light: #fef2f2;
        --pos-success: #10b981;
        --pos-success-light: #ecfdf5;
        --radius-lg: 20px;
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
        --pos-text-muted: #94a3b8;
        --pos-brand-light: rgba(59, 130, 246, 0.1);
    }

    body {
        font-family: 'Inter', 'Cairo', sans-serif;
        background-color: var(--pos-bg);
        color: var(--pos-text-main);
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
    .premium-card-header h4, .premium-card-header h5, .premium-card-header h6 {
        margin: 0;
        font-weight: 700;
        color: #ffffff !important;
    }

    .search-section {
        background: var(--pos-surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--pos-border);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--pos-bg);
        border: 2px solid var(--pos-border);
        border-radius: 100px;
        padding: 4px 8px;
        transition: 0.2s;
    }
    .search-input-wrapper:focus-within {
        border-color: var(--pos-brand);
        box-shadow: 0 0 0 4px var(--pos-brand-light);
    }
    .search-icon {
        font-size: 1.25rem;
        color: var(--pos-text-muted);
        margin-left: 12px;
        margin-right: 8px;
    }
    .search-field {
        border: none;
        background: transparent;
        color: var(--pos-text-main);
        font-weight: 600;
        font-size: 1.1rem;
        width: 100%;
        padding: 10px 12px;
    }
    .search-field:focus {
        outline: none;
    }
    .btn-search-trigger {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        border: none;
        border-radius: 100px;
        padding: 10px 24px;
        font-weight: 700;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.2);
    }
    .btn-search-trigger:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        transform: translateY(-2px);
    }
    .btn-search-trigger:active { transform: translateY(0); }

    /* Invoice Summary Card */
    .invoice-summary-card {
        background: var(--pos-surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--pos-border);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .invoice-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }
    .summary-info-item .lbl {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--pos-text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }
    .summary-info-item .val {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--pos-text-main);
    }

    /* Product Eligible Return List */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .product-return-card {
        background: var(--pos-surface);
        border-radius: var(--radius-lg);
        border: 1px solid var(--pos-border);
        box-shadow: var(--shadow-sm);
        padding: 20px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-return-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-soft);
    }
    .prod-image-wrapper {
        height: 120px;
        background: var(--pos-bg);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        border: 1px solid var(--pos-border);
        overflow: hidden;
    }
    .prod-image {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    .prod-image-placeholder {
        font-size: 2.5rem;
        color: var(--pos-brand);
    }
    .prod-name {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--pos-text-main);
        margin-bottom: 12px;
        line-height: 1.3;
    }
    .prod-stats {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
        background: var(--pos-bg);
        padding: 12px;
        border-radius: var(--radius-md);
    }
    .stat-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
    }
    .stat-row .stat-lbl {
        color: var(--pos-text-muted);
        font-weight: 500;
    }
    .stat-row .stat-val {
        font-weight: 700;
        color: var(--pos-text-main);
    }
    .btn-action-return {
        width: 100%;
        background: var(--pos-brand);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: 12px;
        font-weight: 700;
        transition: 0.2s;
        margin-top: auto;
    }
    .btn-action-return:hover {
        background: var(--pos-brand-hover);
    }

    /* Return Drawer CSS */
    .return-drawer {
        position: fixed;
        top: 0;
        right: -500px;
        width: 480px;
        height: 100vh;
        height: 100dvh;
        background: var(--pos-surface);
        box-shadow: -10px 0 30px rgba(0,0,0,0.1);
        z-index: 1050;
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    html[dir="rtl"] .return-drawer {
        right: auto;
        left: -500px;
        box-shadow: 10px 0 30px rgba(0,0,0,0.1);
        transition: left 0.3s ease;
    }
    .return-drawer.open {
        right: 0;
    }
    html[dir="rtl"] .return-drawer.open {
        left: 0;
        right: auto;
    }
    .drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0,0,0,0.4);
        backdrop-filter: blur(2px);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: 0.3s;
    }
    .drawer-overlay.open {
        opacity: 1;
        visibility: visible;
    }
    .drawer-header {
        padding: 24px;
        border-bottom: 1px solid var(--pos-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .drawer-body {
        padding: 24px;
        flex: 1;
        overflow-y: auto;
    }
    .drawer-footer {
        padding: 24px;
        border-top: 1px solid var(--pos-border);
        background: var(--pos-bg);
    }
    .close-drawer {
        background: var(--pos-bg);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
    }

    /* Stepper Styling */
    .stepper {
        display: flex;
        align-items: center;
        background: var(--pos-bg);
        border: 2px solid var(--pos-border);
        border-radius: 100px;
        padding: 4px;
        width: 100%;
        max-width: 200px;
    }
    .stepper-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: var(--pos-surface);
        color: var(--pos-brand);
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        box-shadow: var(--shadow-sm);
    }
    .stepper-btn:hover {
        background: var(--pos-brand-light);
    }
    .stepper-input {
        flex: 1;
        width: 100%;
        text-align: center;
        border: none;
        background: transparent;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--pos-text-main);
    }

    /* Selectable Cards (Refund method) */
    .refund-methods {
        display: flex;
        gap: 12px;
    }
    .refund-pill {
        flex: 1;
        text-align: center;
        padding: 16px 8px;
        border: 2px solid var(--pos-border);
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: 700;
        color: var(--pos-text-muted);
        transition: 0.2s;
        background: var(--pos-surface);
    }
    .refund-pill.active {
        border-color: var(--pos-brand);
        color: var(--pos-brand);
        background: var(--pos-brand-light);
    }
    .refund-pill i {
        display: block;
        font-size: 1.8rem;
        margin-bottom: 8px;
    }

    /* Predefined reasons */
    .reason-select {
        width: 100%;
        background: var(--pos-bg);
        border: 2px solid var(--pos-border);
        color: var(--pos-text-main);
        border-radius: var(--radius-md);
        padding: 12px;
        font-weight: 600;
    }

    /* Return Summary Card inside Drawer */
    .return-summary-drawer-card {
        background: var(--pos-brand-light);
        border-radius: var(--radius-md);
        padding: 16px;
        border: 1px dashed var(--pos-brand);
        margin-top: 24px;
    }
    .return-summary-drawer-card .summary-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--pos-brand);
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .summary-calc-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    .summary-calc-row.grand-row {
        border-top: 1px solid rgba(59, 130, 246, 0.2);
        padding-top: 8px;
        margin-top: 8px;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--pos-brand);
    }

    /* Success Screen Container */
    .success-overlay-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--pos-surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--pos-border);
        padding: 50px 30px;
        text-align: center;
        margin-top: 40px;
    }
    .success-icon-badge {
        width: 90px;
        height: 90px;
        background: var(--pos-success-light);
        color: var(--pos-success);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        margin-bottom: 24px;
        animation: successScale 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes successScale {
        0% { transform: scale(0); }
        100% { transform: scale(1); }
    }

    /* Pending Return list card */
    .pending-returns-card {
        background: var(--pos-surface);
        border-radius: var(--radius-lg);
        border: 1px solid var(--pos-border);
        box-shadow: var(--shadow-soft);
        margin-top: 24px;
        overflow: hidden;
    }
    .pending-return-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--pos-border);
    }
    .pending-return-row:last-child {
        border-bottom: none;
    }

    /* Scroll Lock style on iOS/Mobile Safari */
    body.overflow-hidden {
        position: fixed !important;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    /* Dark Mode labels override */
    html[data-app-theme="dark"] .text-muted {
        color: var(--pos-text-muted) !important;
    }

    /* Mobile Responsive Drawer & Refund Pills */
    @media (max-width: 576px) {
        .return-drawer {
            width: 100% !important;
            right: -100% !important;
        }
        html[dir="rtl"] .return-drawer {
            right: auto !important;
            left: -100% !important;
        }
        .return-drawer.open {
            right: 0 !important;
        }
        html[dir="rtl"] .return-drawer.open {
            left: 0 !important;
            right: auto !important;
        }
    }
    
    @media (max-width: 400px) {
        .refund-pill {
            padding: 10px 4px !important;
            font-size: 0.8rem !important;
        }
        .refund-pill i {
            font-size: 1.4rem !important;
            margin-bottom: 4px !important;
        }
    }

    /* Mobile Search Input Adjustments */
    @media (max-width: 576px) {
        .search-section {
            padding: 16px !important;
        }
        .search-field {
            font-size: 0.95rem !important;
            padding: 6px 8px !important;
        }
        .btn-search-trigger {
            padding: 8px 16px !important;
            font-size: 0.9rem !important;
            white-space: nowrap !important;
        }
        .search-icon {
            margin-left: 6px !important;
            margin-right: 4px !important;
            font-size: 1.1rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <!-- Step 1: Invoice Search -->
    <div id="search_step" class="search-section">
        <div class="premium-card-header">
            <h5><i class="bi bi-search me-2"></i>{{ __('pos.search_sale_by_invoice') }}</h5>
        </div>
        <div class="p-4">
            <div class="search-input-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="invoice_search" class="search-field" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث برقم الفاتورة...' : 'Search invoice number...' }}" value="{{ request('invoice') ?? '' }}">
                <button type="button" id="btn_search" class="btn-search-trigger">
                    {{ __('pos.search') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Return Processing Panel -->
    <div id="return_panel" class="d-none">
        
        <!-- Step 2: Invoice Summary Card -->
        <div class="invoice-summary-card">
            <div class="premium-card-header">
                <h6><i class="bi bi-receipt me-2"></i>{{ __('pos.sale_details') }}</h6>
            </div>
            <div class="p-4">
                <div class="invoice-summary-grid">
                    <div class="summary-info-item">
                        <div class="lbl">{{ __('pos.invoice_number') }}</div>
                        <div class="val" id="summary_invoice_no">#INV-</div>
                    </div>
                    <div class="summary-info-item">
                        <div class="lbl">{{ __('pos.customer') }}</div>
                        <div class="val" id="summary_customer">Walk-in Customer</div>
                    </div>
                    <div class="summary-info-item">
                        <div class="lbl">{{ __('pos.date') }}</div>
                        <div class="val" id="summary_date">15 Jun 2026</div>
                    </div>
                    <div class="summary-info-item">
                        <div class="lbl">{{ __('pos.total') }}</div>
                        <div class="val text-primary" id="summary_total">0.00</div>
                    </div>
                    <div class="summary-info-item">
                        <div class="lbl">{{ __('pos.payment_method') }}</div>
                        <div class="val" id="summary_payment">Cash</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Product Return List Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold m-0"><i class="bi bi-box-seam me-2 text-primary"></i>{{ __('pos.invoice_includes_returns') ?? 'Eligible Products' }}</h5>
            <button type="button" id="btn_return_all" class="btn btn-danger btn-sm fw-bold rounded-pill px-3 py-2 d-none">
                <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('pos.return_all') }}
            </button>
        </div>

        <!-- Product Grid containing touch-friendly cards -->
        <div class="product-grid" id="product_return_cards_area">
            <!-- Cards populated dynamically -->
        </div>

        <!-- Pending Returns Cart (Added Items to be completed) -->
        <div class="pending-returns-card d-none" id="pending_returns_section">
            <div class="premium-card-header bg-danger" style="background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 100%);">
                <h5><i class="bi bi-cart-x me-2"></i>{{ app()->getLocale() == 'ar' ? 'المنتجات الجاري إرجاعها' : 'Items To Return' }}</h5>
            </div>
            <div class="p-4">
                <div id="pending_returns_list">
                    <!-- Rows injected here -->
                </div>
                
                <div class="text-end mt-4">
                    <button type="button" id="btn_submit_return" class="btn btn-success px-5 py-3 fw-bold rounded-3 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> {{ __('pos.submit_return') }}
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Step 10: Success Screen View -->
    <div id="success_step" class="success-overlay-container d-none">
        <div class="success-icon-badge"><i class="bi bi-check2"></i></div>
        <h2 class="fw-bold mb-2">{{ app()->getLocale() == 'ar' ? 'تمت عملية الإرجاع بنجاح' : 'Return Completed Successfully' }}</h2>
        <p class="text-muted fs-5 mb-4" id="success_return_info"></p>
        
        <div class="d-flex gap-3 justify-content-center">
            <button class="btn btn-outline-dark btn-lg fw-bold px-5 rounded-pill" id="btn_new_return">
                <i class="bi bi-plus-circle me-2"></i> {{ app()->getLocale() == 'ar' ? 'إرجاع جديد' : 'New Return' }}
            </button>
            <a href="{{ route('sales.index') }}" class="btn btn-primary btn-lg fw-bold px-5 rounded-pill" style="background: var(--pos-brand); border: none;">
                <i class="bi bi-arrow-left me-2"></i> {{ app()->getLocale() == 'ar' ? 'الرجوع للمبيعات' : 'Back to Sales' }}
            </a>
        </div>
    </div>
</div>

<div class="drawer-overlay" id="drawer_overlay"></div>
<div class="return-drawer" id="return_drawer">
    <div class="drawer-header premium-card-header">
        <h4 class="m-0 text-white"><i class="bi bi-arrow-counterclockwise me-2"></i>{{ app()->getLocale() == 'ar' ? 'إرجاع منتج' : 'Return Product' }}</h4>
        <button class="close-drawer btn-close btn-close-white bg-transparent text-white border-0" id="btn_close_drawer"></button>
    </div>
    
    <div class="drawer-body">
        <h5 class="fw-extrabold mb-4" id="drawer_product_name" style="color: var(--pos-text-main);">Product Name</h5>
        
        <!-- Stats summary -->
        <div class="prod-stats mb-4">
            <div class="stat-row">
                <span class="stat-lbl">{{ app()->getLocale() == 'ar' ? 'الكمية المباعة' : 'Sold Quantity' }}</span>
                <span class="stat-val" id="drawer_sold_qty">0</span>
            </div>
            <div class="stat-row">
                <span class="stat-lbl">{{ app()->getLocale() == 'ar' ? 'تم إرجاعه مسبقاً' : 'Already Returned' }}</span>
                <span class="stat-val text-warning" id="drawer_returned_qty">0</span>
            </div>
            <div class="stat-row" style="border-top: 1px solid var(--pos-border); padding-top: 8px; margin-top: 8px;">
                <span class="stat-lbl">{{ app()->getLocale() == 'ar' ? 'المتاح للإرجاع' : 'Available To Return' }}</span>
                <span class="stat-val text-primary" id="drawer_available_qty">0</span>
            </div>
        </div>

        <!-- Step 5: Quantity selection -->
        <div class="mb-4">
            <label class="form-label fw-bold text-muted text-uppercase small">{{ app()->getLocale() == 'ar' ? 'كمية الإرجاع' : 'Return Quantity' }}</label>
            <div class="stepper">
                <button type="button" class="stepper-btn" id="qty_minus"><i class="bi bi-dash"></i></button>
                <input type="number" id="return_qty_input" class="stepper-input" value="1" min="1" step="1">
                <button type="button" class="stepper-btn" id="qty_plus"><i class="bi bi-plus"></i></button>
            </div>
        </div>

        <!-- Step 6: Return Reason -->
        <div class="mb-4">
            <label class="form-label fw-bold text-muted text-uppercase small">{{ app()->getLocale() == 'ar' ? 'سبب الإرجاع' : 'Return Reason' }}</label>
            <select id="return_reason_select" class="reason-select">
                <option value="Customer Request">{{ app()->getLocale() == 'ar' ? 'رغبة العميل' : 'Customer Request' }}</option>
                <option value="Damaged Item">{{ app()->getLocale() == 'ar' ? 'منتج تالف' : 'Damaged Item' }}</option>
                <option value="Wrong Item">{{ app()->getLocale() == 'ar' ? 'منتج خاطئ' : 'Wrong Item' }}</option>
                <option value="Expired Product">{{ app()->getLocale() == 'ar' ? 'منتج منتهي الصلاحية' : 'Expired Product' }}</option>
                <option value="Incorrect Sale">{{ app()->getLocale() == 'ar' ? 'بيعة خاطئة' : 'Incorrect Sale' }}</option>
                <option value="Other">{{ app()->getLocale() == 'ar' ? 'أخرى' : 'Other' }}</option>
            </select>
        </div>
        
        <div class="mb-4 d-none" id="custom_reason_wrapper">
            <label class="form-label fw-bold text-muted text-uppercase small">{{ app()->getLocale() == 'ar' ? 'اكتب السبب' : 'Specify Reason' }}</label>
            <textarea id="return_reason_text" class="form-control" rows="2" placeholder="Write reason here..."></textarea>
        </div>

        <!-- Step 7: Refund Method selection -->
        <div class="mb-4">
            <label class="form-label fw-bold text-muted text-uppercase small">{{ app()->getLocale() == 'ar' ? 'طريقة الاسترجاع' : 'Refund Method' }}</label>
            <div class="refund-methods">
                <div class="refund-pill active" data-method="Cash"><i class="bi bi-cash"></i> {{ app()->getLocale() == 'ar' ? 'نقدي' : 'Cash' }}</div>
                <div class="refund-pill" data-method="Store Credit"><i class="bi bi-wallet2"></i> {{ app()->getLocale() == 'ar' ? 'رصيد متجر' : 'Store Credit' }}</div>
                <div class="refund-pill" data-method="Exchange"><i class="bi bi-arrow-left-right"></i> {{ app()->getLocale() == 'ar' ? 'استبدال' : 'Exchange' }}</div>
            </div>
        </div>

        <!-- Step 8: Return Summary Calculations -->
        <div class="return-summary-drawer-card">
            <div class="summary-title">{{ app()->getLocale() == 'ar' ? 'ملخص الإرجاع للمنتج' : 'Item Return Summary' }}</div>
            <div class="summary-calc-row">
                <span>{{ app()->getLocale() == 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</span>
                <span id="summary_unit_price">0.00</span>
            </div>
            <div class="summary-calc-row">
                <span>{{ app()->getLocale() == 'ar' ? 'الكمية المسترجعة' : 'Return Qty' }}</span>
                <span id="summary_qty_display">0.00</span>
            </div>
            <div class="summary-calc-row">
                <span>{{ app()->getLocale() == 'ar' ? 'تعديل الضريبة' : 'Tax Adjustment' }}</span>
                <span id="summary_tax_adjustment">0.00</span>
            </div>
            <div class="summary-calc-row grand-row">
                <span>{{ app()->getLocale() == 'ar' ? 'إجمالي المسترد' : 'Total Refund' }}</span>
                <span id="summary_total_refund">0.00</span>
            </div>
        </div>
    </div>
    
    <div class="drawer-footer">
        <button id="btn_confirm_item_return" class="btn-complete w-100 btn btn-primary py-3 fw-bold rounded-3 fs-5">{{ app()->getLocale() == 'ar' ? 'تأكيد وإتمام الإرجاع' : 'Confirm & Complete Return' }}</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const isArLocale = {{ app()->getLocale() == 'ar' ? 'true' : 'false' }};
        let currentSale = null;
        let responseItems = [];
        let itemsToReturn = {}; // Map of batch_id/product_id keys to return options
        let activeItem = null; // The item currently being configured in the drawer

        // Instant search / search triggers
        $('#btn_search').click(triggerInvoiceSearch);
        $('#invoice_search').on('keypress', function(e) {
            if (e.key === 'Enter') triggerInvoiceSearch();
        });

        if ($('#invoice_search').val()) {
            triggerInvoiceSearch();
        }

        function triggerInvoiceSearch() {
            let invoice = $('#invoice_search').val().trim();
            if (!invoice) return;
            if (invoice.startsWith('#')) {
                invoice = invoice.substring(1);
            }

            const btn = $('#btn_search');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + (isArLocale ? 'جاري البحث...' : 'Searching...'));

            $.ajax({
                url: "{{ route('sales_returns.search') }}",
                data: { invoice_number: invoice },
                success: function(response) {
                    btn.prop('disabled', false).html('<i class="bi bi-search me-1"></i> ' + (isArLocale ? 'بحث' : 'Search'));
                    if (response.success) {
                        currentSale = response.sale;
                        responseItems = response.items;
                        itemsToReturn = {}; // Reset return items list
                        $('#pending_returns_section').addClass('d-none');
                        
                        // Populate step 2 summary card
                        $('#summary_invoice_no').text(currentSale.short_number);
                        $('#summary_customer').text(response.customer_name);
                        $('#summary_date').text(new Date(currentSale.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }));
                        
                        const currency = "{{ $setting->currency ?? 'SAR' }}";
                        $('#summary_total').text(parseFloat(currentSale.total || 0).toFixed(2) + ' ' + currency);
                        $('#summary_payment').text(currentSale.payment_method.toUpperCase());

                        // Populate Step 3 cards
                        let cardsHtml = '';
                        responseItems.forEach((item, index) => {
                            if (parseFloat(item.available_to_return) > 0) {
                                let imgSrc = item.product.image ? '/storage/' + item.product.image : null;
                                let imgHtml = imgSrc 
                                    ? `<img src="${imgSrc}" class="prod-image" alt="${item.product.name}">` 
                                    : `<i class="bi bi-box prod-image-placeholder"></i>`;

                                let factor = parseFloat(item.conversion_factor) || 1;
                                if (factor <= 0) factor = 1;

                                let soldQtyInUnit = parseFloat(item.quantity) / factor;
                                let availableQtyInUnit = parseFloat(item.available_to_return) / factor;
                                let alreadyReturnedInUnit = (parseFloat(item.quantity) - parseFloat(item.available_to_return)) / factor;

                                cardsHtml += `
                                    <div class="product-return-card" data-index="${index}">
                                        <div class="prod-image-wrapper">
                                            ${imgHtml}
                                        </div>
                                        <div class="prod-name">${item.product.name} (${item.unit_name || ''})</div>
                                        <div class="prod-stats">
                                            <div class="stat-row">
                                                <span class="stat-lbl">{{ app()->getLocale() == 'ar' ? 'الكمية المباعة' : 'Sold Quantity' }}</span>
                                                <span class="stat-val">${soldQtyInUnit.toFixed(0)} ${item.unit_name || ''}</span>
                                            </div>
                                            <div class="stat-row">
                                                <span class="stat-lbl">{{ app()->getLocale() == 'ar' ? 'تم إرجاعه مسبقاً' : 'Already Returned' }}</span>
                                                <span class="stat-val text-warning">${alreadyReturnedInUnit.toFixed(0)} ${item.unit_name || ''}</span>
                                            </div>
                                            <div class="stat-row">
                                                <span class="stat-lbl">{{ app()->getLocale() == 'ar' ? 'المتاح للإرجاع' : 'Available To Return' }}</span>
                                                <span class="stat-val text-primary fw-bold">${availableQtyInUnit.toFixed(0)} ${item.unit_name || ''}</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-action-return btn_trigger_item_drawer" data-index="${index}">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> {{ app()->getLocale() == 'ar' ? 'إرجاع المنتج' : 'Return Product' }}
                                        </button>
                                    </div>
                                `;
                            }
                        });

                        if (cardsHtml === '') {
                            cardsHtml = '<div class="col-12 text-center py-5 text-danger fw-bold">No products on this invoice are eligible for return.</div>';
                            $('#btn_return_all').addClass('d-none');
                        } else {
                            $('#btn_return_all').removeClass('d-none');
                        }

                        $('#product_return_cards_area').html(cardsHtml);
                        $('#return_panel').removeClass('d-none');
                        $('#success_step').addClass('d-none');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-search me-1"></i> ' + (isArLocale ? 'بحث' : 'Search'));
                    Swal.fire("{{ __('pos.error') }}", xhr.responseJSON ? xhr.responseJSON.message : "Invoice not found", "error");
                    $('#return_panel').addClass('d-none');
                }
            });
        }

        // Open return configuration drawer
        $(document).on('click', '.btn_trigger_item_drawer', function() {
            const index = $(this).data('index');
            activeItem = responseItems[index];

            let factor = parseFloat(activeItem.conversion_factor) || 1;
            if (factor <= 0) factor = 1;

            let soldQtyInUnit = parseFloat(activeItem.quantity) / factor;
            let alreadyReturnedInUnit = (parseFloat(activeItem.quantity) - parseFloat(activeItem.available_to_return)) / factor;
            let availableQtyInUnit = parseFloat(activeItem.available_to_return) / factor;

            // Set drawer information
            $('#drawer_product_name').text(activeItem.product.name + ' (' + (activeItem.unit_name || '') + ')');
            $('#drawer_sold_qty').text(soldQtyInUnit.toFixed(0) + ' ' + (activeItem.unit_name || ''));
            
            $('#drawer_returned_qty').text(alreadyReturnedInUnit.toFixed(0) + ' ' + (activeItem.unit_name || ''));
            $('#drawer_available_qty').text(availableQtyInUnit.toFixed(0) + ' ' + (activeItem.unit_name || ''));
            
            // Reset quantity to 1 (or max available if max available is less than 1)
            let defaultVal = Math.min(1, availableQtyInUnit);
            $('#return_qty_input').val(defaultVal.toFixed(0)).attr('max', Math.floor(availableQtyInUnit));

            // Reset reason selectors
            $('#return_reason_select').val('Customer Request');
            $('#custom_reason_wrapper').addClass('d-none');
            $('#return_reason_text').val('');

            // Reset refund pills
            $('.refund-pill').removeClass('active');
            $('.refund-pill[data-method="Cash"]').addClass('active');

            // Draw initial summary
            calculateItemSummary();

            // Open drawer
            $('#return_drawer, #drawer_overlay').addClass('open');
            $('body').addClass('overflow-hidden');
        });

        // Close drawer handlers
        $('#btn_close_drawer, #drawer_overlay').click(function() {
            $('#return_drawer, #drawer_overlay').removeClass('open');
            $('body').removeClass('overflow-hidden');
            activeItem = null;
        });

        // Drawer stepper handlers
        $('#qty_plus').click(function() {
            let val = parseInt($('#return_qty_input').val()) || 0;
            let factor = parseFloat(activeItem.conversion_factor) || 1;
            if (factor <= 0) factor = 1;
            const maxVal = Math.floor(parseFloat(activeItem.available_to_return) / factor);
            if (val < maxVal) {
                $('#return_qty_input').val(Math.min(maxVal, val + 1)).trigger('input');
            }
        });

        $('#qty_minus').click(function() {
            let val = parseInt($('#return_qty_input').val()) || 0;
            if (val > 1) {
                $('#return_qty_input').val(Math.max(1, val - 1)).trigger('input');
            }
        });

        $('#return_qty_input').on('input', function() {
            let val = parseInt($(this).val()) || 0;
            let factor = parseFloat(activeItem.conversion_factor) || 1;
            if (factor <= 0) factor = 1;
            const maxVal = Math.floor(parseFloat(activeItem.available_to_return) / factor);
            if (val > maxVal) {
                $(this).val(maxVal);
                val = maxVal;
            }
            if (val < 1) {
                $(this).val(1);
                val = 1;
            }
            calculateItemSummary();
        });

        // Toggle custom reason textarea
        $('#return_reason_select').change(function() {
            if ($(this).val() === 'Other') {
                $('#custom_reason_wrapper').removeClass('d-none');
            } else {
                $('#custom_reason_wrapper').addClass('d-none');
            }
        });

        // Refund method selection
        $('.refund-pill').click(function() {
            $('.refund-pill').removeClass('active');
            $(this).addClass('active');
        });

        // Calculate active item summary calculations
        function calculateItemSummary() {
            if (!activeItem) return;
            const qtyInUnit = parseFloat($('#return_qty_input').val()) || 0;
            let factor = parseFloat(activeItem.conversion_factor) || 1;
            if (factor <= 0) factor = 1;

            const qtyInBase = qtyInUnit * factor;
            const price = parseFloat(activeItem.price) || 0;
            const taxRate = currentSale.subtotal > 0 ? (currentSale.tax / currentSale.subtotal) : 0.15;
            
            const refundAmount = qtyInBase * price;
            const taxAdjustment = refundAmount * taxRate;
            const totalRefund = refundAmount + taxAdjustment;

            $('#summary_unit_price').text((price * factor).toFixed(2));
            $('#summary_qty_display').text(qtyInUnit.toFixed(0));
            $('#summary_tax_adjustment').text(taxAdjustment.toFixed(2));
            $('#summary_total_refund').text(totalRefund.toFixed(2));
        }

        // Add configured return product to the returns cart (Direct submit)
        $('#btn_confirm_item_return').click(function() {
            if (!activeItem) return;
            const qtyInUnit = parseFloat($('#return_qty_input').val()) || 0;
            if (qtyInUnit <= 0) {
                Swal.fire('Error', 'Please enter a valid return quantity.', 'warning');
                return;
            }

            let factor = parseFloat(activeItem.conversion_factor) || 1;
            if (factor <= 0) factor = 1;
            const qtyInBase = qtyInUnit * factor;

            let reason = $('#return_reason_select').val();
            if (reason === 'Other') {
                reason = $('#return_reason_text').val() || 'Other';
            }

            const refundMethod = $('.refund-pill.active').data('method') || 'Cash';
            
            Swal.fire({
                title: "{{ app()->getLocale() == 'ar' ? 'تأكيد الإرجاع' : 'Confirm Return' }}",
                text: "{{ app()->getLocale() == 'ar' ? 'هل تريد إرجاع هذا المنتج الآن؟' : 'Do you want to return this product now?' }}",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، إتمام' : 'Yes, Complete' }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = $('#btn_confirm_item_return');
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                    $.ajax({
                        url: "{{ route('sales_returns.store') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            sale_id: currentSale.id,
                            items: [{
                                product_id: activeItem.product_id,
                                batch_id: activeItem.batch_id,
                                quantity: qtyInBase,
                                reason: reason
                            }]
                        },
                        success: function(response) {
                            btn.prop('disabled', false).html("{{ app()->getLocale() == 'ar' ? 'تأكيد وإتمام الإرجاع' : 'Confirm & Complete Return' }}");
                            if (response.success) {
                                // Display success screen
                                $('#return_panel').addClass('d-none');
                                $('#search_step').addClass('d-none');
                                
                                const returnNo = 'RET-' + currentSale.invoice_number.replace('INV-', '');
                                const refundAmountTotal = qtyInBase * parseFloat(activeItem.price);
                                $('#success_return_info').html(`
                                    <strong>{{ app()->getLocale() == 'ar' ? 'رقم الإرجاع' : 'Return Number' }}:</strong> ${returnNo}<br>
                                    <strong>{{ app()->getLocale() == 'ar' ? 'مبلغ المسترد' : 'Refund Amount' }}:</strong> ${refundAmountTotal.toFixed(2)} {{ $setting->currency ?? 'SAR' }}
                                `);
                                
                                $('#success_step').removeClass('d-none');

                                // Close drawer and clear active item
                                $('#return_drawer, #drawer_overlay').removeClass('open');
                                $('body').removeClass('overflow-hidden');
                                activeItem = null;
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html("{{ app()->getLocale() == 'ar' ? 'تأكيد وإتمام الإرجاع' : 'Confirm & Complete Return' }}");
                            Swal.fire("{{ __('pos.error') }}", xhr.responseJSON ? xhr.responseJSON.message : "Error submitting return", "error");
                        }
                    });
                }
            });
        });



        // Render the returns list
        function renderReturnsCart() {
            let listHtml = '';
            let count = 0;
            
            for (let key in itemsToReturn) {
                const item = itemsToReturn[key];
                listHtml += `
                    <div class="pending-return-row">
                        <div>
                            <div class="fw-bold fs-6">${item.name}</div>
                            <small class="text-muted">
                                ${item.reason} | {{ app()->getLocale() == 'ar' ? 'طريقة الاسترداد' : 'Refund' }}: ${item.refund_method}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="fw-bold">${item.quantity.toFixed(0)} ${item.unit_name || ''}</div>
                            <div class="fw-bold text-success">${(item.quantity * item.price).toFixed(2)}</div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-return-item" data-key="${key}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                count++;
            }

            $('#pending_returns_list').html(listHtml);

            if (count > 0) {
                $('#pending_returns_section').removeClass('d-none');
            } else {
                $('#pending_returns_section').addClass('d-none');
            }
        }

        // Remove item from returns cart
        $(document).on('click', '.remove-return-item', function() {
            const key = $(this).data('key');
            delete itemsToReturn[key];
            renderReturnsCart();
        });

        // Submit the entire return batch
        $('#btn_submit_return').click(function() {
            let items = [];
            let refundAmountTotal = 0;

            for (let key in itemsToReturn) {
                const item = itemsToReturn[key];
                items.push({
                    product_id: item.product_id,
                    batch_id: item.batch_id,
                    quantity: item.quantity,
                    reason: item.reason
                });
                refundAmountTotal += item.quantity * item.price;
            }

            if (items.length === 0) return;

            Swal.fire({
                title: "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}",
                text: "{{ app()->getLocale() == 'ar' ? 'هل تريد تأكيد إرجاع هذه المنتجات؟' : 'Do you want to confirm returning these items?' }}",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، إتمام الإرجاع' : 'Yes, Complete Return' }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = $(this);
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                    $.ajax({
                        url: "{{ route('sales_returns.store') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            sale_id: currentSale.id,
                            items: items
                        },
                        success: function(response) {
                            btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> {{ __("pos.submit_return") }}');
                            if (response.success) {
                                // Display success screen
                                $('#return_panel').addClass('d-none');
                                $('#search_step').addClass('d-none');
                                
                                // Generate return reference/number dynamically for visual screen display
                                const returnNo = 'RET-' + currentSale.invoice_number.replace('INV-', '');
                                $('#success_return_info').html(`
                                    <strong>{{ app()->getLocale() == 'ar' ? 'رقم الإرجاع' : 'Return Number' }}:</strong> ${returnNo}<br>
                                    <strong>{{ app()->getLocale() == 'ar' ? 'مبلغ المسترد' : 'Refund Amount' }}:</strong> ${refundAmountTotal.toFixed(2)} {{ $setting->currency ?? 'SAR' }}
                                `);
                                
                                $('#success_step').removeClass('d-none');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> {{ __("pos.submit_return") }}');
                            Swal.fire("{{ __('pos.error') }}", xhr.responseJSON ? xhr.responseJSON.message : "Error submitting return", "error");
                        }
                    });
                }
            });
        });

        // Start new return session
        $('#btn_new_return').click(function() {
            $('#invoice_search').val('');
            $('#search_step').removeClass('d-none');
            $('#success_step').addClass('d-none');
            $('#return_panel').addClass('d-none');
            $('#btn_return_all').addClass('d-none');
            itemsToReturn = {};
            currentSale = null;
            responseItems = [];
        });

        // Return all items from invoice
        $('#btn_return_all').click(function() {
            if (!currentSale || responseItems.length === 0) return;

            const isAr = "{{ app()->getLocale() == 'ar' }}";
            
            Swal.fire({
                title: isAr ? 'إرجاع جميع المنتجات' : 'Return All Products',
                html: `
                    <p class="text-muted">${isAr ? 'سيتم إرجاع جميع الكميات المتاحة في هذه الفاتورة.' : 'All available quantities in this invoice will be returned.'}</p>
                    <div class="text-start mb-2">
                        <label class="form-label fw-bold small text-muted text-uppercase">${isAr ? 'سبب الإرجاع' : 'Return Reason'}</label>
                        <select id="swal_return_reason" class="form-select mb-3">
                            <option value="Customer Request">${isAr ? 'رغبة العميل' : 'Customer Request'}</option>
                            <option value="Damaged Item">${isAr ? 'منتج تالف' : 'Damaged Item'}</option>
                            <option value="Wrong Item">${isAr ? 'منتج خاطئ' : 'Wrong Item'}</option>
                            <option value="Expired Product">${isAr ? 'منتج منتهي الصلاحية' : 'Expired Product'}</option>
                            <option value="Incorrect Sale">${isAr ? 'بيعة خاطئة' : 'Incorrect Sale'}</option>
                            <option value="Other">${isAr ? 'أخرى' : 'Other'}</option>
                        </select>
                        <textarea id="swal_custom_reason" class="form-control d-none mb-3" rows="2" placeholder="${isAr ? 'اكتب السبب هنا...' : 'Write reason here...'}"></textarea>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#64748b',
                confirmButtonText: isAr ? 'تأكيد الإرجاع بالكامل' : 'Confirm Return All',
                cancelButtonText: isAr ? 'إلغاء' : 'Cancel',
                didOpen: () => {
                    $('#swal_return_reason').change(function() {
                        if ($(this).val() === 'Other') {
                            $('#swal_custom_reason').removeClass('d-none');
                        } else {
                            $('#swal_custom_reason').addClass('d-none');
                        }
                    });
                },
                preConfirm: () => {
                    let reason = $('#swal_return_reason').val();
                    if (reason === 'Other') {
                        reason = $('#swal_custom_reason').val().trim() || 'Other';
                    }
                    return { reason: reason };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const reason = result.value.reason;
                    const items = [];
                    let refundAmountTotal = 0;

                    responseItems.forEach(item => {
                        const availableQty = parseFloat(item.available_to_return) || 0;
                        if (availableQty > 0) {
                            items.push({
                                product_id: item.product_id,
                                batch_id: item.batch_id,
                                quantity: availableQty,
                                reason: reason
                            });
                            refundAmountTotal += availableQty * parseFloat(item.price);
                        }
                    });

                    if (items.length === 0) return;

                    const btn = $('#btn_return_all');
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                    $.ajax({
                        url: "{{ route('sales_returns.store') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            sale_id: currentSale.id,
                            items: items
                        },
                        success: function(response) {
                            btn.prop('disabled', false).html(originalHtml);
                            if (response.success) {
                                // Display success screen
                                $('#return_panel').addClass('d-none');
                                $('#search_step').addClass('d-none');
                                
                                const returnNo = 'RET-' + currentSale.invoice_number.replace('INV-', '');
                                $('#success_return_info').html(`
                                    <strong>${isAr ? 'رقم الإرجاع' : 'Return Number'} :</strong> ${returnNo}<br>
                                    <strong>${isAr ? 'مبلغ المسترد' : 'Refund Amount'} :</strong> ${refundAmountTotal.toFixed(2)} {{ $setting->currency ?? 'SAR' }}
                                `);
                                
                                $('#success_step').removeClass('d-none');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html(originalHtml);
                            Swal.fire("{{ __('pos.error') }}", xhr.responseJSON ? xhr.responseJSON.message : "Error submitting return", "error");
                        }
                    });
                }
            });
        });

    });
</script>
@endpush

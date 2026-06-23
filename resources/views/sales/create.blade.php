@extends('layouts.app')

@section('title', __('pos.add') . ' ' . __('pos.sales'))

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --pos-brand: #f97316; /* Orange 500 */
        --pos-brand-hover: #ea580c; /* Orange 600 */
        --pos-brand-light: #fff7ed; /* Orange 50 */
        --pos-bg: #f8fafc;
        --pos-surface: #ffffff;
        --pos-border: #f1f5f9;
        --pos-text-main: #0f172a;
        --pos-text-muted: #64748b;
        --pos-danger: #ef4444;
        --pos-danger-light: #fef2f2;
        --pos-success: #10b981;
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
        --pos-brand-light: rgba(249, 115, 22, 0.1);
    }
    
    html[data-app-theme="dark"] .text-muted {
        color: var(--pos-text-muted) !important;
    }

    html[data-app-theme="dark"] .btn-outline-dark {
        color: var(--pos-text-main) !important;
        border-color: var(--pos-border) !important;
    }

    html[data-app-theme="dark"] .btn-outline-dark:hover {
        background-color: var(--pos-border) !important;
        color: var(--pos-text-main) !important;
    }

    html[data-app-theme="dark"] #custom_product_search {
        color: var(--pos-text-main) !important;
    }
    html[data-app-theme="dark"] #custom_product_search::placeholder {
        color: var(--pos-text-muted) !important;
        opacity: 0.7;
    }

    html, body {
        overflow-x: hidden;
        width: 100%;
        max-width: 100vw;
    }

    body {
        font-family: 'Inter', 'Cairo', sans-serif;
        background-color: var(--pos-bg);
        color: var(--pos-text-main);
    }

    /* Container Spacing */
    .pos-wrapper {
        padding: 10px 0 20px;
        min-height: calc(100vh - 100px);
    }

    /* Left Side: Product List Area */
    
    /* Search Bar */
    .search-wrapper {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 24px;
    }
    
    .search-input-container {
        flex: 1;
        position: relative;
        background: var(--pos-surface);
        border-radius: 100px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        padding: 0 8px;
        border: 1px solid var(--pos-border);
        transition: 0.2s;
    }
    .search-input-container:focus-within {
        border-color: var(--pos-brand);
        box-shadow: 0 0 0 4px var(--pos-brand-light);
    }
    
    .search-icon {
        color: var(--pos-text-muted);
        font-size: 1.2rem;
        padding-left: 16px;
    }
    html[dir="rtl"] .search-icon { padding-left: 0; padding-right: 16px; }

    /* Custom dropdown menu styles */
    .custom-dropdown-menu {
        border: 1px solid var(--pos-border);
        background: var(--pos-surface);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-soft);
        max-height: 400px;
        overflow-y: auto;
        margin-top: 8px;
    }
    .dropdown-item-product {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid var(--pos-border);
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .dropdown-item-product:last-child {
        border-bottom: none;
    }
    .dropdown-item-product:hover, .dropdown-item-product.highlighted {
        background-color: var(--pos-brand-light);
    }
    .dropdown-item-product .unit-btn {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 100px;
        border: 1px solid var(--pos-brand);
        background-color: transparent;
        color: var(--pos-brand);
        transition: all 0.2s ease;
        margin-inline-start: 8px;
    }
    .dropdown-item-product .unit-btn:hover {
        background-color: var(--pos-brand);
        color: white;
    }

    .barcode-icon {
        color: var(--pos-text-muted);
        font-size: 1.5rem;
        padding-right: 16px;
        cursor: pointer;
    }
    html[dir="rtl"] .barcode-icon { padding-right: 0; padding-left: 16px; }

    /* Add Product Button (Orange +) */
    .btn-add-product {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: var(--pos-brand);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        transition: 0.2s;
    }
    .btn-add-product:hover { background: var(--pos-brand-hover); transform: scale(1.05); }

    /* Cart Card */
    .cart-card {
        background: var(--pos-surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        padding: 24px;
        border: 1px solid var(--pos-border);
    }

    .cart-header {
        display: grid;
        grid-template-columns: minmax(0, 2fr) 110px 140px 110px 110px 40px;
        gap: 15px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--pos-border);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--pos-text-muted);
    }

    .cart-items-wrapper {
        min-height: 200px;
        max-height: 50vh;
        overflow-y: auto;
    }

    .cart-row {
        display: grid;
        grid-template-columns: minmax(0, 2fr) 110px 140px 110px 110px 40px;
        gap: 15px;
        align-items: center;
        padding: 20px 0;
        border-bottom: 1px solid var(--pos-border);
    }
    .cart-row > div { min-width: 0; }
    .cart-row:last-child { border-bottom: none; }

    /* Product Info in Row */
    .product-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .product-thumb {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-md);
        background: var(--pos-bg);
        object-fit: cover;
        border: 1px solid var(--pos-border);
    }
    .product-thumb-placeholder {
        width: 60px;
        height: 60px;
        min-width: 60px;
        border-radius: var(--radius-md);
        background: var(--pos-bg);
        color: var(--pos-brand);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        border: 1px solid var(--pos-brand-light);
    }
    .product-details { display: flex; flex-direction: column; gap: 4px; min-width: 0; flex: 1; }
    .product-name { font-weight: 700; font-size: 1.1rem; color: var(--pos-text-main); }
    .product-barcode { font-size: 0.85rem; color: var(--pos-text-muted); }
    
    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--pos-brand-light);
        color: var(--pos-brand);
        padding: 2px 8px;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 700;
        width: max-content;
    }
    .stock-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: var(--pos-brand); }

    /* Stepper */
    .stepper {
        display: flex;
        align-items: center;
        background: var(--pos-surface);
        border: 2px solid var(--pos-brand-light);
        border-radius: 100px;
        padding: 4px;
        width: 100%;
    }
    .stepper-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: var(--pos-brand);
        font-size: 1.2rem;
        display: flex; align-items: center; justify-content: center;
        transition: 0.2s;
    }
    .stepper-btn:hover { background: var(--pos-brand-light); }
    .stepper-input {
        flex: 1;
        width: 100%;
        text-align: center;
        border: none;
        background: transparent;
        font-weight: 700;
        font-size: 1rem;
        color: var(--pos-text-main);
        -moz-appearance: textfield;
    }
    .stepper-input::-webkit-outer-spin-button, .stepper-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    /* Unit & Prices */
    .clean-input {
        width: 100%;
        border: none;
        background: transparent;
        font-weight: 600;
        color: var(--pos-text-main);
        font-size: 1rem;
        text-align: center;
        padding: 4px;
    }
    .clean-input:focus { outline: none; background: var(--pos-bg); border-radius: 8px; }

    .clean-select {
        width: 100%;
        border: 2px solid var(--pos-brand-light);
        background-color: var(--pos-surface);
        border-radius: var(--radius-md);
        font-weight: 700;
        color: var(--pos-brand);
        font-size: 0.95rem;
        cursor: pointer;
        transition: 0.2s;
        padding: 6px 12px;
        text-align: center;
        text-align-last: center;
    }
    .clean-select:hover {
        border-color: var(--pos-brand);
        background-color: var(--pos-brand-light);
    }
    .clean-select:focus {
        outline: none;
        border-color: var(--pos-brand);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
    }
    
    .row-total {
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--pos-brand);
        text-align: center;
    }

    .btn-delete {
        background: var(--pos-danger-light);
        color: var(--pos-danger);
        border: none;
        width: 36px; height: 36px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        transition: 0.2s;
    }
    .btn-delete:hover { background: var(--pos-danger); color: white; }

    /* Add More Card */
    .add-more-card {
        border: 2px dashed #cbd5e1;
        border-radius: var(--radius-md);
        padding: 20px;
        text-align: center;
        color: var(--pos-text-muted);
        margin-top: 16px;
        background: transparent;
        transition: 0.2s;
        cursor: pointer;
    }
    .add-more-card:hover { border-color: var(--pos-brand); color: var(--pos-brand); background: var(--pos-brand-light); }
    .add-more-card i { font-size: 1.5rem; margin-right: 8px; }
    html[dir="rtl"] .add-more-card i { margin-right: 0; margin-left: 8px; }

    /* Right Side: Checkout Panel */
    .summary-panel {
        display: flex;
        flex-direction: column;
        gap: 16px;
        height: 100%;
    }

    .panel-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--pos-brand);
        text-align: center;
        margin-bottom: 8px;
    }

    .summary-card {
        background: var(--pos-surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        padding: 24px;
        border: 1px solid var(--pos-border);
    }
    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        row-gap: 24px;
        column-gap: 16px;
    }
    .summary-item { text-align: center; }
    .summary-item .lbl { font-size: 0.75rem; font-weight: 700; color: var(--pos-text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px; }
    .summary-item .val { font-size: 1.3rem; font-weight: 800; color: var(--pos-text-main); }
    
    .divider { height: 1px; background: var(--pos-border); margin: 20px -24px; }

    .net-total-card {
        background: var(--pos-brand);
        border-radius: var(--radius-lg);
        padding: 24px;
        text-align: center;
        color: white;
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);
    }
    .net-total-card .lbl { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; opacity: 0.9; margin-bottom: 8px; letter-spacing: 1px;}
    .net-total-card .val { font-size: 2.8rem; font-weight: 800; line-height: 1; }

    .btn-pos-checkout {
        background: var(--pos-brand);
        color: white;
        border: none;
        border-radius: var(--radius-lg);
        padding: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        box-shadow: 0 12px 24px rgba(249, 115, 22, 0.25);
        transition: 0.2s;
        cursor: pointer;
    }
    .btn-pos-checkout:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(249, 115, 22, 0.35); background: var(--pos-brand-hover); }
    .btn-pos-checkout i.main-icon { font-size: 3.5rem; }
    .btn-pos-checkout span { font-size: 1.5rem; font-weight: 800; }
    .btn-pos-checkout i.arrow-icon { font-size: 1.2rem; background: white; color: var(--pos-brand); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin-top: 8px;}

    .btn-clear-all {
        background: var(--pos-surface);
        color: var(--pos-danger);
        border: 1px solid var(--pos-danger-light);
        border-radius: var(--radius-md);
        padding: 16px;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: 0.2s;
    }
    .btn-clear-all:hover { background: var(--pos-danger-light); }


    .checkout-drawer { position: fixed; top: 0; bottom: 0; right: -500px; width: 450px; height: 100vh; height: 100dvh; background: var(--pos-surface); box-shadow: -10px 0 30px rgba(0,0,0,0.1); z-index: 1050; transition: right 0.3s ease; display: flex; flex-direction: column; }
    html[dir="rtl"] .checkout-drawer { right: auto; left: -500px; box-shadow: 10px 0 30px rgba(0,0,0,0.1); transition: left 0.3s ease; }
    .checkout-drawer.open { right: 0; }
    html[dir="rtl"] .checkout-drawer.open { left: 0; }
    .drawer-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); backdrop-filter: blur(2px); z-index: 1040; opacity: 0; visibility: hidden; transition: 0.3s; }
    .drawer-overlay.open { opacity: 1; visibility: visible; }
    .drawer-header { padding: 24px; border-bottom: 1px solid var(--pos-border); display: flex; justify-content: space-between; align-items: center; }
    .drawer-body { padding: 24px; flex: 1; overflow-y: auto; }
    .drawer-footer { padding: 24px; border-top: 1px solid var(--pos-border); background: var(--pos-bg); }
    .close-drawer { background: var(--pos-bg); border: none; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; }
    .drawer-input { width: 100%; background: var(--pos-bg); border: 2px solid var(--pos-border); color: var(--pos-text-main); border-radius: 12px; padding: 14px 16px; font-size: 1.1rem; font-weight: 600; transition: 0.2s; }
    .drawer-input:focus { border-color: var(--pos-brand); outline: none; }
    .payment-methods { display: flex; gap: 12px; margin-bottom: 24px; }
    .payment-pill { flex: 1; text-align: center; padding: 16px 8px; border: 2px solid var(--pos-border); border-radius: 12px; cursor: pointer; font-weight: 700; color: var(--pos-text-muted); transition: 0.2s; }
    .payment-pill.active { border-color: var(--pos-brand); color: var(--pos-brand); background: var(--pos-brand-light); }
    .payment-pill i { display: block; font-size: 1.8rem; margin-bottom: 8px; }
    .btn-complete { background: var(--pos-brand); color: white; border: none; border-radius: 12px; padding: 20px; font-size: 1.4rem; font-weight: 800; width: 100%; box-shadow: 0 8px 16px rgba(249, 115, 22, 0.2); }
    .btn-complete:hover { background: var(--pos-brand-hover); }

    .badge-paid { background-color: rgba(16, 185, 129, 0.08); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.15); }
    .badge-partial { background-color: rgba(245, 158, 11, 0.08); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.15); }
    .badge-unpaid { background-color: rgba(244, 63, 94, 0.08); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.15); }

    /* Success Overlay */
    .success-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: var(--pos-surface); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: 0.4s; transform: scale(0.95); }
    .success-overlay.show { opacity: 1; visibility: visible; transform: scale(1); }
    body.overflow-hidden {
        position: fixed !important;
        width: 100% !important;
        height: 100% !important;
        overflow: hidden !important;
    }
    .success-icon { width: 100px; height: 100px; background: var(--pos-brand-light); color: var(--pos-brand); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin-bottom: 24px; animation: scaleIn 0.5s ease-out forwards; }
    @keyframes scaleIn { 0% { transform: scale(0); } 70% { transform: scale(1.2); } 100% { transform: scale(1); } }
    
    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--pos-text-muted); }

    /* Responsive Mobile styles for POS cart */
    @media (max-width: 991px) {
        .cart-header {
            display: none !important;
        }
        .cart-items-wrapper {
            max-height: none !important;
            min-height: auto !important;
        }
        .cart-row {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            padding: 16px !important;
            border: 1px solid var(--pos-border) !important;
            border-radius: var(--radius-md) !important;
            margin-bottom: 16px !important;
            background: var(--pos-bg) !important;
        }
        .cart-row > div {
            width: 100% !important;
            text-align: start !important;
        }
        .cart-row .product-info {
            border-bottom: 1px solid var(--pos-border);
            padding-bottom: 8px;
        }
        .cart-row .stepper {
            max-width: 100% !important;
        }
        .cart-row .clean-select {
            text-align: start !important;
            text-align-last: start !important;
        }
        .cart-row .clean-input {
            text-align: start !important;
            background: var(--pos-surface) !important;
            border: 1.5px solid var(--pos-brand-light) !important;
            border-radius: var(--radius-sm) !important;
            padding: 6px 12px !important;
        }
        .cart-row .row-total {
            text-align: end !important;
            font-size: 1.25rem !important;
        }
        .cart-row .row-total::before {
            content: "Total: ";
            font-size: 0.85rem;
            color: var(--pos-text-muted);
            font-weight: 500;
        }
        html[dir="rtl"] .cart-row .row-total::before {
            content: "الإجمالي: ";
        }
        .cart-row .btn-delete {
            width: 100% !important;
            justify-content: center !important;
        }
        
        /* Responsive checkout drawer */
        .checkout-drawer {
            width: 100% !important;
            max-width: 450px;
        }
    }
    @media (max-width: 576px) {
        .checkout-drawer {
            width: 100% !important;
            right: -100% !important;
            height: 100dvh !important;
            bottom: 0 !important;
        }
        html[dir="rtl"] .checkout-drawer {
            left: -100% !important;
            right: auto !important;
        }
        .checkout-drawer.open {
            right: 0 !important;
        }
        html[dir="rtl"] .checkout-drawer.open {
            left: 0 !important;
        }

        /* Dropdown wrap to prevent unit pills overlap */
        .dropdown-item-product {
            flex-wrap: wrap !important;
            gap: 8px 0 !important;
        }
        .dropdown-item-product .unit-options-pane {
            width: 100% !important;
            margin-top: 6px !important;
            justify-content: flex-start !important;
            flex-wrap: wrap !important;
            padding-inline-start: 52px !important;
        }
        html[dir="rtl"] .dropdown-item-product .unit-options-pane {
            padding-inline-start: 0 !important;
            padding-inline-end: 52px !important;
        }
        .dropdown-item-product .unit-btn {
            margin-inline-start: 0 !important;
            margin-inline-end: 8px !important;
            margin-bottom: 4px !important;
        }
        html[dir="rtl"] .dropdown-item-product .unit-btn {
            margin-inline-end: 0 !important;
            margin-inline-start: 8px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="pos-wrapper container-fluid">
    <div class="row g-4 h-100">
        
        <!-- Left Side: Product List Area -->
        <div class="col-xl-9 col-lg-8 d-flex flex-column">
            
            <!-- Search Bar -->
            <div class="search-wrapper" style="position: relative;">
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="custom_product_search" class="form-control border-0 bg-transparent px-3 py-2 fw-semibold fs-5" placeholder="{{ __('pos.scan_barcode_here') }} / {{ __('pos.product_search') }}" autocomplete="off" style="box-shadow: none;">
                    <i class="bi bi-upc-scan barcode-icon" title="Scan Barcode"></i>
                </div>
                <div id="search_results_dropdown" class="custom-dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; background: var(--pos-surface); border: 1px solid var(--pos-border); border-radius: var(--radius-md); box-shadow: var(--shadow-soft); max-height: 400px; overflow-y: auto; margin-top: 8px; padding: 8px 0;">
                </div>
            </div>

            <!-- Cart Card -->
            <div class="cart-card flex-grow-1 d-flex flex-column">
                <div class="cart-header">
                    <div class="px-2">{{ __('pos.product') }}</div>
                    <div class="text-center">{{ __('pos.unit') }}</div>
                    <div class="text-center">{{ __('pos.quantity_short') }}</div>
                    <div class="text-center">{{ __('pos.unit_price') }}</div>
                    <div class="text-center">{{ __('pos.total') }}</div>
                    <div></div>
                </div>
                
                <div class="cart-items-wrapper" id="cart_items_area">
                    <!-- Items injected via JS -->
                </div>
                
                <div class="add-more-card mt-auto" onclick="$('#custom_product_search').focus();">
                    <span class="fw-bold"><i class="bi bi-cart2"></i> {{ __('pos.add_more_products') }}</span>
                    <div class="small mt-1 opacity-75">{{ __('pos.scan_or_search_to_add') }}</div>
                </div>
            </div>

        </div>

        <!-- Right Side: Checkout Summary Panel -->
        <div class="col-xl-3 col-lg-4">
            <div class="summary-panel">
                <h4 class="panel-title">{{ __('pos.order_confirmation') ?? 'Order Confirmation' }}</h4>
                
                <div class="summary-card">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="lbl">{{ __('pos.items_count') }}</div>
                            <div class="val" id="panel_items_count">0</div>
                        </div>
                        <div class="summary-item">
                            <div class="lbl">{{ __('pos.subtotal') }}</div>
                            <div class="val" id="panel_subtotal">0.00</div>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="lbl">{{ __('pos.discount') }}</div>
                            <div class="val text-danger" id="panel_discount">0.00</div>
                        </div>
                        <div class="summary-item">
                            <div class="lbl">{{ __('pos.vat') }} (<span id="panel_tax_pct">{{ $setting->default_tax ?? 15 }}</span>%)</div>
                            <div class="val" id="panel_tax">0.00</div>
                        </div>
                    </div>
                </div>

                <div class="net-total-card">
                    <div class="lbl">{{ __('pos.net_total') }}</div>
                    <div class="val" id="panel_net_total">0.00</div>
                </div>

                <button class="btn-pos-checkout" id="btn_open_drawer">
                    <i class="bi bi-bag-check main-icon"></i>
                    <span>{{ __('pos.pos_checkout') }}</span>
                    <i class="bi bi-arrow-right arrow-icon"></i>
                </button>

                <button class="btn-clear-all" id="clear_cart_btn">
                    <i class="bi bi-trash3-fill"></i> {{ __('pos.clear_all') }}
                </button>

            </div>
        </div>
        
    </div>
</div>

<!-- Drawer Overlay & Checkout Drawer (Functional part hidden in Drawer) -->
<div class="drawer-overlay" id="drawer_overlay"></div>
<div class="checkout-drawer" id="checkout_drawer">
    <div class="drawer-header">
        <h3 class="fw-bold m-0"><i class="bi bi-wallet2 me-2" style="color: var(--pos-brand);"></i>{{ __('pos.payment') }}</h3>
        <button class="close-drawer" id="btn_close_drawer"><i class="bi bi-x-lg"></i></button>
    </div>
    
    <div class="drawer-body">
        <div class="mb-4">
            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">{{ __('pos.customer') }}</label>
            <select id="customer_id" class="form-select select2 drawer-input p-2">
                <option value="">{{ __('pos.walk_in_customer') }}</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->customer_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">{{ __('pos.payment_method') }}</label>
            <div class="payment-methods">
                <div class="payment-pill active" data-method="cash"><i class="bi bi-cash-stack"></i> {{ __('pos.cash') }}</div>
                <div class="payment-pill" data-method="card"><i class="bi bi-credit-card"></i> {{ __('pos.card') }}</div>
                <div class="payment-pill" data-method="transfer"><i class="bi bi-bank"></i> {{ __('pos.transfer') }}</div>
            </div>
            <input type="hidden" id="payment_method" value="cash">
        </div>

        <div class="mb-4 row g-3">
            <div class="col-6">
                <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.8rem;">{{ __('pos.discount') }}</label>
                <input type="number" id="discount_input" class="drawer-input text-end" value="0.00" step="0.01" min="0">
            </div>
            <div class="col-6">
                <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.8rem;">{{ __('pos.vat') }} (%)</label>
                <input type="number" id="vat_percent" class="drawer-input text-end" value="{{ $setting->default_tax ?? 15 }}" step="0.1" min="0">
            </div>
        </div>

        <hr class="my-4" style="border-color: var(--pos-border);">

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-bold text-uppercase mb-0" style="font-size: 0.8rem; color: var(--pos-text-muted);">{{ __('pos.amount_paid') }}</label>
                <span class="badge badge-unpaid py-1 px-2 rounded font-monospace" id="paidBadge">Unpaid</span>
            </div>
            <input type="number" id="paid_amount" class="drawer-input text-end font-monospace" value="0.00" step="0.01">
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="fw-semibold text-danger" id="change_label">{{ __('pos.change') }}</span>
            <span class="h5 mb-0 fw-bold text-danger font-monospace" id="change_amount">0.00</span>
        </div>
        
        <div class="mb-3 mt-4">
            <textarea id="sale_notes" class="drawer-input" rows="2" placeholder="{{ __('pos.notes') }}..." style="font-size: 1rem; font-weight: 400;"></textarea>
        </div>
    </div>
    
    <div class="drawer-footer">
        <div class="d-flex justify-content-between mb-3 text-muted fw-bold text-uppercase" style="font-size: 0.9rem;">
            <span>{{ __('pos.to_pay') }}:</span>
            <span id="drawer_net_total" class="text-dark fs-4">0.00</span>
        </div>
        <button id="save_sale_btn" class="btn-complete">{{ __('pos.confirm_and_pay') }}</button>
    </div>
</div>

<!-- Success Overlay -->
<div class="success-overlay" id="success_overlay">
    <div class="success-icon"><i class="bi bi-check-lg"></i></div>
    <h1 class="fw-bold mb-2">{{ __('pos.sale_completed') }}</h1>
    <p class="text-muted fs-4 mb-5" id="success_invoice_no">{{ __('pos.invoice_hash') }}INV-</p>
    
    <div class="d-flex flex-column flex-sm-row gap-3 w-100 px-4 justify-content-center align-items-center">
        <a href="{{ route('sales.create') }}" id="btn_new_sale" class="btn btn-outline-dark btn-lg fw-bold px-4 d-inline-flex align-items-center justify-content-center w-100 w-sm-auto" style="border-radius: 100px; height: 60px; max-width: 280px;">
            <i class="bi bi-plus-circle me-2"></i> {{ __('pos.new_sale') }}
        </a>
        <button class="btn btn-primary btn-lg fw-bold px-4 d-inline-flex align-items-center justify-content-center w-100 w-sm-auto" style="border-radius: 100px; height: 60px; background: var(--pos-brand); border: none; max-width: 280px;" id="btn_print_receipt">
            <i class="bi bi-printer me-2"></i> {{ __('pos.print_receipt') }}
        </button>
    </div>
</div>

<!-- Quick Add Modal (Hidden initially) -->
<div class="modal fade" id="quickAddModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius-lg); border: none; box-shadow: var(--shadow-soft);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Use the full product management section for now.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let cart = [];
    let saleIdForPrint = null;
    let paidAmountManuallyEdited = false;

    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $('#checkout_drawer') });
        const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});

        function formatProductSearch(product) {
            if (product.loading) return product.text;
            if (product.isNew) {
                return $(`<div class="fw-bold text-primary p-2" style="background: var(--pos-brand-light); border-radius: 8px;"><i class="bi bi-upc-scan me-2"></i> ${product.text}</div>`);
            }
            
            let imgSrc = product.image ? '/storage/' + product.image : null;
            let imgHtml = imgSrc 
                ? `<img src="${imgSrc}" style="width:36px; height:36px; object-fit:cover; border-radius:6px; margin-inline-end:12px; border:1px solid var(--pos-border);">` 
                : `<div style="width:36px; height:36px; background:var(--pos-brand-light); color:var(--pos-brand); border-radius:6px; display:inline-flex; align-items:center; justify-content:center; margin-inline-end:12px; font-size:1.2rem;"><i class="bi bi-box"></i></div>`;
            
            let priceText = parseFloat(product.price || 0).toFixed(2);
            let unitBadge = '';
            if (product.matched_unit_id && product.matched_unit_id !== 'base') {
                unitBadge = `<span class="badge" style="background:var(--pos-brand-light); color:var(--pos-brand); margin-inline-start:8px; border-radius:100px; font-size:0.75rem; border:1px solid var(--pos-brand);">${product.matched_unit_name}</span>`;
            }
            
            return $(`
                <div style="display:flex; align-items:center; padding: 4px 0;">
                    ${imgHtml}
                    <div style="flex:1;">
                        <div style="font-weight:700; font-size:0.95rem; line-height:1.2; color:var(--pos-text-main);">${product.text} ${unitBadge}</div>
                        <div style="font-size:0.8rem; font-weight:600; color:var(--pos-brand); margin-top:2px;">${priceText}</div>
                    </div>
                </div>
            `);
        }

        let barcodeBuffer = '';
        let barcodeTimer = null;
        $(document).on('keypress', function(e) {
            const target = $(e.target);
            if (target.is('input, textarea, select')) return;
            
            if (e.key === 'Enter') {
                if (barcodeBuffer.length >= 2) {
                    e.preventDefault();
                    handleBarcodeScan(barcodeBuffer);
                    barcodeBuffer = '';
                }
            } else {
                barcodeBuffer += e.key;
                clearTimeout(barcodeTimer);
                barcodeTimer = setTimeout(() => { barcodeBuffer = ''; }, 50);
            }
        });

        // Custom Autocomplete Search Logic
        const searchInput = $('#custom_product_search');
        const dropdown = $('#search_results_dropdown');
        let searchTimeout = null;
        let selectedIndex = -1;

        searchInput.on('input', function() {
            const term = $(this).val().trim();
            clearTimeout(searchTimeout);
            
            if (!term) {
                dropdown.hide().empty();
                selectedIndex = -1;
                return;
            }

            searchTimeout = setTimeout(() => {
                $.ajax({
                    url: "{{ route('products.search') }}",
                    data: { term: term },
                    dataType: 'json',
                    success: function(data) {
                        renderSearchDropdown(data);
                    }
                });
            }, 150);
        });

        function renderSearchDropdown(products) {
            dropdown.empty();
            if (products.length === 0) {
                dropdown.append('<div class="p-3 text-muted text-center">No products found</div>').show();
                selectedIndex = -1;
                return;
            }

            products.forEach((product, index) => {
                let imgSrc = product.image ? '/storage/' + product.image : null;
                let imgHtml = imgSrc 
                    ? `<img src="${imgSrc}" style="width:40px; height:40px; object-fit:cover; border-radius:6px; margin-inline-end:12px;">` 
                    : `<div style="width:40px; height:40px; background:var(--pos-brand-light); color:var(--pos-brand); border-radius:6px; display:inline-flex; align-items:center; justify-content:center; margin-inline-end:12px; font-size:1.2rem;"><i class="bi bi-box"></i></div>`;
                
                // Base unit option
                let unitButtonsHtml = `<button class="unit-btn add-unit-trigger" data-unit-id="base" data-index="${index}">${product.base_unit_name} (${parseFloat(product.price).toFixed(2)})</button>`;
                
                // Additional unit options
                if (product.additional_units && product.additional_units.length > 0) {
                    product.additional_units.forEach(u => {
                        unitButtonsHtml += `<button class="unit-btn add-unit-trigger" data-unit-id="${u.id}" data-index="${index}">${u.unit_name} (${parseFloat(u.sale_price).toFixed(2)})</button>`;
                    });
                }

                // Determine default unit badge
                let unitBadge = '';
                if (product.matched_unit_id && product.matched_unit_id !== 'base') {
                    unitBadge = `<span class="badge bg-warning ms-2">${product.matched_unit_name}</span>`;
                }

                const itemHtml = $(`
                    <div class="dropdown-item-product" data-index="${index}">
                        ${imgHtml}
                        <div style="flex: 1; min-width: 0;">
                            <div class="fw-bold text-truncate" style="font-size:1rem; color:var(--pos-text-main);">${product.text} ${unitBadge}</div>
                            <div class="small text-muted">Barcode: ${product.barcode || 'N/A'} | Stock: ${product.stock}</div>
                        </div>
                        <div class="unit-options-pane" style="display: flex; gap: 4px;">
                            ${unitButtonsHtml}
                        </div>
                    </div>
                `);

                // Store product data directly on the DOM element
                itemHtml.data('product', product);
                dropdown.append(itemHtml);
            });

            dropdown.show();
            selectedIndex = -1;
        }

        // Handle clicking direct unit buttons
        $(document).on('click', '.add-unit-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const trigger = $(this);
            const parentRow = trigger.closest('.dropdown-item-product');
            const product = parentRow.data('product');
            const unitId = trigger.data('unit-id');

            // Find price for the selected unit
            let finalPrice = parseFloat(product.price);
            if (unitId !== 'base' && product.additional_units) {
                const foundUnit = product.additional_units.find(u => String(u.id) === String(unitId));
                if (foundUnit) {
                    finalPrice = parseFloat(foundUnit.sale_price);
                }
            }

            addToCart(product.id, product.text, finalPrice, product.batches, product.has_warranty, product.warranty_months, product.base_unit_name, product.additional_units, unitId, product.image, product.barcode);
            Toast.fire({ icon: 'success', title: product.text, text: 'Added' });

            // Clear search
            searchInput.val('').focus();
            dropdown.hide().empty();
        });

        // Handle clicking the general product row (adds default or matched unit)
        $(document).on('click', '.dropdown-item-product', function(e) {
            const product = $(this).data('product');
            if (!product) return;

            // Use the matched unit from backend search result
            const unitId = product.matched_unit_id || 'base';
            let finalPrice = parseFloat(product.price);

            addToCart(product.id, product.text, finalPrice, product.batches, product.has_warranty, product.warranty_months, product.base_unit_name, product.additional_units, unitId, product.image, product.barcode);
            Toast.fire({ icon: 'success', title: product.text, text: 'Added' });

            searchInput.val('').focus();
            dropdown.hide().empty();
        });

        // Keydown navigation (Arrow Up, Arrow Down, Enter)
        searchInput.on('keydown', function(e) {
            const items = dropdown.find('.dropdown-item-product');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (items.length === 0) return;
                selectedIndex = (selectedIndex + 1) % items.length;
                items.removeClass('highlighted');
                $(items[selectedIndex]).addClass('highlighted')[0].scrollIntoView({ block: 'nearest' });
            } 
            else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (items.length === 0) return;
                selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                items.removeClass('highlighted');
                $(items[selectedIndex]).addClass('highlighted')[0].scrollIntoView({ block: 'nearest' });
            } 
            else if (e.key === 'Enter') {
                e.preventDefault();
                const val = $(this).val().trim();
                if (!val) return;

                // 1. Try exact barcode scan/match first
                $.ajax({
                    url: `/products/barcode/${val}`,
                    method: 'GET',
                    success: function(response) {
                        const product = response.product;
                        const batches = response.batches || [];
                        const price = batches.length > 0 ? parseFloat(batches[0].price || product.sale_price) : parseFloat(product.sale_price);
                        const name = response.name || (typeof product.name === 'string' ? product.name : '');
                        
                        addToCart(product.id, name, price, batches, product.has_warranty, product.warranty_period_months, response.base_unit_name, response.additional_units, response.scanned_unit_id, product.image, product.barcode);
                        Toast.fire({ icon: 'success', title: name, text: 'Added' });
                        
                        // Clear search field
                        searchInput.val('').focus();
                        dropdown.hide().empty();
                    },
                    error: function() {
                        // 2. Fallback to selecting highlighted option if it exists
                        if (selectedIndex >= 0 && selectedIndex < items.length) {
                            $(items[selectedIndex]).trigger('click');
                        } else if (items.length > 0) {
                            $(items[0]).trigger('click');
                        } else {
                            Toast.fire({ icon: 'error', title: "Not Found", text: "Product not found" });
                        }
                    }
                });
            }
            else if (e.key === 'Escape') {
                dropdown.hide().empty();
            }
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-wrapper').length) {
                dropdown.hide();
            }
        });

        // Re-open dropdown on focus if not empty
        searchInput.on('focus', function() {
            if ($(this).val().trim()) {
                dropdown.show();
            }
        });

        // Drawer
        $('#btn_open_drawer').on('click', function() {
            if(cart.length === 0) { Toast.fire({ icon: 'warning', title: 'Cart is empty!' }); return; }
            $('#checkout_drawer, #drawer_overlay').addClass('open');
            const total = parseFloat($('#panel_net_total').text()) || 0;
            paidAmountManuallyEdited = false; // Reset flag on drawer open
            $('#paid_amount').val(''); // Keep empty
            updateTotals();
            setTimeout(() => { $('#paid_amount').focus(); }, 300);
        });

        $('#paid_amount').on('input', function() {
            paidAmountManuallyEdited = true;
        });
        $('#btn_close_drawer, #drawer_overlay').on('click', function() {
            $('#checkout_drawer, #drawer_overlay').removeClass('open');
            $('#custom_product_search').focus();
        });

        // Press Enter inside checkout drawer inputs to complete the sale
        $('#checkout_drawer').on('keydown', 'input, textarea, select', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#save_sale_btn').click();
            }
        });

        $('.payment-pill').on('click', function() {
            $('.payment-pill').removeClass('active');
            $(this).addClass('active');
            $('#payment_method').val($(this).data('method'));
        });

        function handleBarcodeScan(barcode) {
            if (!barcode) return;
            $.ajax({
                url: `/products/barcode/${barcode}`,
                method: 'GET',
                success: function(response) {
                    const product = response.product;
                    const batches = response.batches || [];
                    const price = batches.length > 0 ? parseFloat(batches[0].price || product.sale_price) : parseFloat(product.sale_price);
                    const name = response.name || (typeof product.name === 'string' ? product.name : '');
                    
                    addToCart(product.id, name, price, batches, product.has_warranty, product.warranty_period_months, response.base_unit_name, response.additional_units, response.scanned_unit_id, product.image, product.barcode);
                    Toast.fire({ icon: 'success', title: name, text: 'Added' });
                },
                error: function(xhr) {
                    Toast.fire({ icon: 'error', title: "Not Found", text: xhr.responseJSON ? xhr.responseJSON.message : "Error" });
                }
            });
        }

        $('#discount_input, #paid_amount, #vat_percent').on('input', updateTotals);

        let saleSubmissionInProgress = false;

        $('#save_sale_btn').on('click', function() {
            if (cart.length === 0 || saleSubmissionInProgress) return;
            saleSubmissionInProgress = true;
            
            const subtotal = parseFloat($('#panel_subtotal').text()) || 0;
            const discount = parseFloat($('#discount_input').val()) || 0;
            const tax = parseFloat($('#panel_tax').text()) || 0;
            const total = parseFloat($('#panel_net_total').text()) || 0;
            const paid = parseFloat($('#paid_amount').val()) || 0;

            const data = {
                _token: "{{ csrf_token() }}", customer_id: $('#customer_id').val(),
                subtotal: subtotal, discount: discount, tax: tax, total: total,
                paid_amount: paid, change_amount: parseFloat($('#change_amount').text()) || 0,
                payment_method: $('#payment_method').val(), notes: $('#sale_notes').val(),
                items: cart.map(item => {
                    const breakdown = calculateTotalAndWeightedPrice(item);
                    return { product_id: item.id, quantity: item.quantity, unit_name: item.unit_name, conversion_factor: item.conversion_factor, price: breakdown.weightedPrice, is_manual_price: (item.manualPrice !== undefined && item.manualPrice !== null), serial_number: item.serial_number || null };
                })
            };

            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...');

            $.ajax({
                url: "{{ route('sales.store') }}", method: "POST", data: data,
                success: function(response) {
                    btn.prop('disabled', false).html("{{ __('pos.confirm_and_pay') }}");
                    saleSubmissionInProgress = false;
                    cart = []; // Clear cart immediately to prevent duplicate submissions
                    renderCart(); // Re-render empty cart UI
                    
                    // Reset discount, notes, paid amount in drawer
                    $('#discount_input').val('0.00');
                    $('#paid_amount').val('0.00');
                    $('#sale_notes').val('');
                    
                    $('#checkout_drawer, #drawer_overlay').removeClass('open');
                    $('#success_invoice_no').text(response.invoice_number || 'Sale Completed');
                    saleIdForPrint = response.sale_id;
                    $('#success_overlay').addClass('show');
                    $('body').addClass('overflow-hidden');
                    
                    // Blur any active element to prevent any key press from triggering submit again
                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                    // Focus on the print receipt button so Enter key triggers printing instead of duplicating the sale
                    setTimeout(function() {
                        $('#btn_print_receipt').focus();
                    }, 100);
                },
                error: function(xhr) {
                    saleSubmissionInProgress = false;
                    btn.prop('disabled', false).html("{{ __('pos.confirm_and_pay') }}");
                    Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Error saving sale', 'error');
                }
            });
        });

        $('#btn_print_receipt').on('click', function() {
            if (saleIdForPrint) {
                let printFrame = document.getElementById('print_receipt_frame');
                if (!printFrame) {
                    printFrame = document.createElement('iframe');
                    printFrame.id = 'print_receipt_frame';
                    printFrame.style.position = 'fixed';
                    printFrame.style.right = '0';
                    printFrame.style.bottom = '0';
                    printFrame.style.width = '0';
                    printFrame.style.height = '0';
                    printFrame.style.border = 'none';
                    document.body.appendChild(printFrame);
                }
                printFrame.src = `/sales/${saleIdForPrint}/print`;

                // Close the success overlay immediately and focus search
                $('#success_overlay').removeClass('show');
                $('body').removeClass('overflow-hidden');
                setTimeout(() => {
                    $('#custom_product_search').focus();
                }, 300);
            }
        });

        $('#btn_new_sale').on('click', function(e) {
            e.preventDefault();
            // Just close the success overlay and focus on search, keeping the page light and fast
            $('#success_overlay').removeClass('show');
            $('body').removeClass('overflow-hidden');
            setTimeout(() => {
                $('#custom_product_search').focus();
            }, 300);
        });

        // Keyboard navigation on the success screen using Arrow Left / Right
        $(document).on('keydown', function(e) {
            if ($('#success_overlay').hasClass('show')) {
                const buttons = [$('#btn_new_sale'), $('#btn_print_receipt')];
                let focusedIndex = buttons.findIndex(btn => btn.is(':focus'));

                if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                    e.preventDefault();
                    if (focusedIndex === -1) {
                        $('#btn_print_receipt').focus();
                    } else {
                        const nextIndex = (focusedIndex === 0) ? 1 : 0;
                        buttons[nextIndex].focus();
                    }
                }
            }
        });

        $('#clear_cart_btn').on('click', function() {
            if(cart.length > 0) {
                Swal.fire({ title: 'Clear Cart?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes, clear it!' }).then((result) => {
                    if (result.isConfirmed) { cart = []; renderCart(); }
                });
            }
        });
        
    });

    function addToCart(productId, name, price, batches = [], has_warranty = false, warranty_months = 0, base_unit_name = 'Piece', additional_units = [], scanned_unit_id = 'base', image = null, barcode = null) {
        let itemPrice = parseFloat(price) || 0;
        let conversionFactor = 1;
        let unitName = base_unit_name;

        if (scanned_unit_id !== 'base' && additional_units && additional_units.length > 0) {
            const unitObj = additional_units.find(u => String(u.id) === String(scanned_unit_id));
            if (unitObj) {
                itemPrice = parseFloat(unitObj.sale_price) || 0; conversionFactor = parseFloat(unitObj.conversion_factor) || 1; unitName = unitObj.unit_name;
            } else { scanned_unit_id = 'base'; }
        }

        let existing = cart.find(item => String(item.id) === String(productId) && String(item.selected_unit_id) === String(scanned_unit_id));
        
        if (existing && (existing.has_warranty == 1 || existing.has_warranty === true || existing.has_warranty === "1")) {
            existing = undefined; 
        }

        if (existing) { existing.quantity++; } 
        else {
            cart.push({
                id: productId, name: name, price: itemPrice, base_price: parseFloat(price) || 0,
                quantity: 1, batches: batches || [], has_warranty: has_warranty, warranty_months: warranty_months,
                serial_number: '', base_unit_name: base_unit_name || 'Piece', additional_units: additional_units || [],
                selected_unit_id: scanned_unit_id, unit_name: unitName, conversion_factor: conversionFactor,
                image: image, barcode: barcode || productId
            });
        }
        renderCart();
    }

    function calculateTotalAndWeightedPrice(item) {
        let unitPrice = (item.manualPrice !== undefined && item.manualPrice !== null) ? item.manualPrice : item.price;
        return { total: (unitPrice * item.quantity), weightedPrice: unitPrice };
    }

    function renderCart() {
        const container = $('#cart_items_area');
        container.empty();

        if (cart.length === 0) {
            // Handled visually by having nothing, add more card is below it
        } else {
            cart.forEach((item, index) => {
                const breakdown = calculateTotalAndWeightedPrice(item);
                const rowTotal = breakdown.total;
                const displayPrice = breakdown.weightedPrice;
                
                let unitOptionsHtml = `<option value="base" data-factor="1" data-price="${item.base_price}" ${item.selected_unit_id === 'base' ? 'selected' : ''}>${item.base_unit_name}</option>`;
                if (item.additional_units && item.additional_units.length > 0) {
                    item.additional_units.forEach(u => { unitOptionsHtml += `<option value="${u.id}" data-factor="${u.conversion_factor}" data-price="${u.sale_price}" ${String(item.selected_unit_id) === String(u.id) ? 'selected' : ''}>${u.unit_name}</option>`; });
                }

                let imgHtml = item.image ? `<img src="/storage/${item.image}" class="product-thumb" alt="">` : `<div class="product-thumb-placeholder"><i class="bi bi-box"></i></div>`;

                const rowHtml = `
                    <div class="cart-row">
                        <div class="product-info">
                            ${imgHtml}
                            <div class="product-details">
                                <div class="product-name text-truncate" title="${item.name}">${item.name}</div>
                                <div class="product-barcode">{{ __('pos.barcode') }}: ${item.barcode}</div>
                                <div class="stock-badge">{{ __('pos.sufficient') }}</div>
                            </div>
                        </div>
                        <div>
                            <select class="form-select clean-select unit-select" data-index="${index}">${unitOptionsHtml}</select>
                        </div>
                        <div>
                            <div class="stepper">
                                <button type="button" class="stepper-btn qty-minus" data-index="${index}"><i class="bi bi-dash"></i></button>
                                <input type="number" class="stepper-input quantity-input" value="${item.quantity}" min="1" step="1" data-index="${index}">
                                <button type="button" class="stepper-btn qty-plus" data-index="${index}"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                        <div class="text-center">
                            <input type="number" class="clean-input text-center" value="${displayPrice.toFixed(2)}" min="0" step="0.01" data-index="${index}">
                        </div>
                        <div class="row-total">${rowTotal.toFixed(2)}</div>
                        <div class="text-end">
                            <button class="btn-delete remove-item" data-index="${index}"><i class="bi bi-trash3"></i></button>
                        </div>
                    </div>
                `;
                container.append(rowHtml);
            });
        }
        attachCartEvents();
        updateTotals();
    }

    function attachCartEvents() {
        $('.clean-input[type="number"]').off('change').on('change', function() {
            const index = $(this).data('index'); const newPrice = parseFloat($(this).val());
            const item = cart[index];
            if (item && !isNaN(newPrice)) { item.manualPrice = newPrice; renderCart(); }
        });

        $('.quantity-input').off('change').on('change', function() {
            const index = $(this).data('index'); const newQty = parseFloat($(this).val());
            const item = cart[index];
            if (item && newQty > 0) { item.quantity = newQty; renderCart(); }
        });
        
        $('.qty-plus').off('click').on('click', function() { const index = $(this).data('index'); const input = $(`.quantity-input[data-index="${index}"]`); input.val(parseFloat(input.val()) + 1).trigger('change'); });
        $('.qty-minus').off('click').on('click', function() { const index = $(this).data('index'); const input = $(`.quantity-input[data-index="${index}"]`); input.val(Math.max(1, parseFloat(input.val()) - 1)).trigger('change'); });
        $('.remove-item').off('click').on('click', function() { const index = $(this).data('index'); cart.splice(index, 1); renderCart(); });
        
        $('.unit-select').off('change').on('change', function() {
            const index = $(this).data('index'); const option = $(this).find(':selected');
            const item = cart[index];
            if (item) {
                item.selected_unit_id = $(this).val(); 
                item.conversion_factor = parseFloat(option.data('factor')) || 1; 
                item.price = parseFloat(option.data('price')) || 0;
                item.unit_name = item.selected_unit_id === 'base' ? item.base_unit_name : option.text(); 
                item.manualPrice = null; // Clear manual price override so it updates to the new unit price
                renderCart();
            }
        });
    }

    function updateTotals() {
        let subtotal = 0; let itemsCount = 0;
        cart.forEach(item => { const breakdown = calculateTotalAndWeightedPrice(item); subtotal += breakdown.total; itemsCount += parseFloat(item.quantity); });
        
        // Reset discount to 0 if cart is empty
        if (cart.length === 0) {
            $('#discount_input').val('0.00');
        }
        
        const discount = parseFloat($('#discount_input').val()) || 0; const subtotalAfterDiscount = Math.max(0, subtotal - discount);
        const vatPercent = parseFloat($('#vat_percent').val()) || 0; const vatAmount = subtotalAfterDiscount * (vatPercent / 100);
        const total = subtotalAfterDiscount + vatAmount; 
        
        const paid = parseFloat($('#paid_amount').val()) || 0;
        const diff = paid - total; const changeDisplay = Math.abs(diff).toFixed(2);
        
        const isAr = "{{ app()->getLocale() }}" === 'ar';
        if (diff >= 0) { 
            const labelText = isAr ? 'الباقي' : 'Change';
            $('#change_label').text(labelText).removeClass('text-danger').addClass('text-success'); 
            $('#change_amount').text(changeDisplay).removeClass('text-danger').addClass('text-success'); 
        } else { 
            const labelText = isAr ? 'الباقي' : 'Remaining';
            $('#change_label').text(labelText).removeClass('text-success').addClass('text-danger'); 
            $('#change_amount').text(changeDisplay).removeClass('text-success').addClass('text-danger'); 
        }

        const paidBadge = $('#paidBadge');
        paidBadge.removeClass('badge-paid badge-partial badge-unpaid');
        if (paid === 0) {
            paidBadge.addClass('badge-unpaid').text('Unpaid');
        } else if (paid >= total && total > 0) {
            paidBadge.addClass('badge-paid').text('Fully Paid');
        } else {
            paidBadge.addClass('badge-partial').text('Partially Paid');
        }

        $('#panel_items_count').text(itemsCount);
        $('#panel_subtotal').text(subtotal.toFixed(2));
        $('#panel_discount').text(discount.toFixed(2));
        $('#panel_tax_pct').text(vatPercent);
        $('#panel_tax').text(vatAmount.toFixed(2));
        $('#panel_net_total, #drawer_net_total').text(total.toFixed(2));
    }
</script>
@endpush

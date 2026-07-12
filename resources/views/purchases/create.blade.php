@extends('layouts.app')

@section('title', __('purchases.add_purchase'))

@section('content')
<!-- Custom Modern Styles matching Products Page theme -->
@push('styles')
<style>
    /* ============================================================
       DIGITAL AGE POS — Enterprise Inventory UI (Purchases Redesign)
       Premium SaaS · Dark/Light Mode · Glassmorphism
     ============================================================ */

    :root,
    [data-pm-theme="light"] {
        --da-cyan:          #00C8FF;
        --da-cyan-dark:     #0099CC;
        --da-cyan-soft:     rgba(0,200,255,0.10);
        --da-cyan-glow:     rgba(0,200,255,0.25);
        --da-pink:          #FF1493;
        --da-pink-soft:     rgba(255,20,147,0.10);
        --da-pink-glow:     rgba(255,20,147,0.20);
        --da-grad:          linear-gradient(135deg, #00C8FF 0%, #8B5CF6 50%, #FF1493 100%);

        /* Semantic */
        --pm-primary:       #3b82f6;
        --pm-primary-hover: #2563eb;
        --pm-primary-soft:  rgba(59, 130, 246, 0.06);
        --pm-success:       #10b981;
        --pm-success-soft:  rgba(16,185,129,0.08);
        --pm-warning:       #f59e0b;
        --pm-warning-soft:  rgba(245,158,11,0.08);
        --pm-danger:        #f43f5e;
        --pm-danger-soft:   rgba(244,63,94,0.08);
        --pm-info:          #06b6d4;
        --pm-info-soft:     rgba(6,182,212,0.08);
        --pm-neutral:       #64748b;
        --pm-neutral-soft:  #f8fafc;

        /* Surfaces */
        --pm-bg:            #f0f4ff;
        --pm-surface:       #ffffff;
        --pm-surface-2:     #f8fafc;
        --pm-surface-3:     #f1f5f9;
        --pm-border:        #e2e8f0;
        --pm-border-soft:   rgba(226,232,240,0.6);

        /* Text */
        --pm-text-1:        #0f172a;
        --pm-text-2:        #334155;
        --pm-text-3:        #475569;
        --pm-text-muted:    #94a3b8;

        /* Shadows */
        --pm-shadow-xs:     0 1px 3px rgba(0,0,0,0.04);
        --pm-shadow-sm:     0 4px 12px rgba(0,0,0,0.05);
        --pm-shadow-md:     0 8px 24px rgba(0,0,0,0.07);
        --pm-shadow-lg:     0 20px 60px rgba(0,0,0,0.10);

        --pm-radius:        16px;
        --pm-radius-sm:     10px;
    }

    [data-pm-theme="dark"] {
        --da-cyan:          #00D4FF;
        --da-cyan-dark:     #00AACC;
        --da-cyan-soft:     rgba(0,212,255,0.12);
        --da-cyan-glow:     rgba(0,212,255,0.30);
        --da-pink:          #FF1493;
        --da-pink-soft:     rgba(255,20,147,0.12);
        --da-pink-glow:     rgba(255,20,147,0.25);
        --da-grad:          linear-gradient(135deg, #00D4FF 0%, #8B5CF6 50%, #FF1493 100%);

        --pm-primary:       #60a5fa;
        --pm-primary-hover: #3b82f6;
        --pm-primary-soft:  rgba(96,165,250,0.12);
        --pm-success:       #34d399;
        --pm-success-soft:  rgba(52,211,153,0.12);
        --pm-warning:       #fbbf24;
        --pm-warning-soft:  rgba(251,191,36,0.12);
        --pm-danger:        #fb7185;
        --pm-danger-soft:   rgba(251,113,133,0.12);
        --pm-info:          #22d3ee;
        --pm-info-soft:     rgba(34,211,238,0.12);
        --pm-neutral:       #94a3b8;
        --pm-neutral-soft:  rgba(148,163,184,0.08);

        --pm-bg:            #040b18;
        --pm-surface:       #0b1427;
        --pm-surface-2:     #0f1e35;
        --pm-surface-3:     #162035;
        --pm-border:        rgba(0,200,255,0.15);
        --pm-border-soft:   rgba(0,200,255,0.08);

        --pm-text-1:        #e8f4ff;
        --pm-text-2:        #cbd5e1;
        --pm-text-3:        #94b8d4;
        --pm-text-muted:    #4a6b8a;

        --pm-shadow-xs:     0 1px 3px rgba(0,0,0,0.30);
        --pm-shadow-sm:     0 4px 12px rgba(0,0,0,0.40);
        --pm-shadow-md:     0 8px 24px rgba(0,0,0,0.50);
        --pm-shadow-lg:     0 20px 60px rgba(0,0,0,0.60);
    }

    /* ═══════════════════ PAGE BACKGROUND ═══════════════════ */
    #content {
        background:
            radial-gradient(ellipse 80% 60% at 10% 0%, rgba(99,102,241,.07) 0%, transparent 60%),
            radial-gradient(ellipse 60% 50% at 90% 10%, rgba(59,130,246,.07) 0%, transparent 55%),
            radial-gradient(ellipse 50% 40% at 50% 80%, rgba(16,185,129,.05) 0%, transparent 55%),
            radial-gradient(ellipse 40% 30% at 80% 90%, rgba(245,158,11,.04) 0%, transparent 50%),
            linear-gradient(160deg, var(--pm-bg) 0%, var(--pm-bg) 100%) !important;
        background-attachment: fixed;
    }

    /* Core Layout */
    .purchase-container {
        color: var(--pm-text-1);
    }

    .pm-card {
        background: var(--pm-surface);
        border: 1px solid var(--pm-border);
        border-radius: 24px;
        box-shadow: var(--pm-shadow-sm);
        transition: box-shadow 0.3s, background 0.3s, border-color 0.3s;
    }

    .pm-card:hover {
        box-shadow: var(--pm-shadow-md);
    }

    .sticky-sidebar {
        position: sticky;
        top: 24px;
        z-index: 10;
    }

    /* Form Controls */
    .pm-form-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--pm-text-3);
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: .6px;
        transition: color 0.3s;
    }

    .pm-form-control {
        border: 1.5px solid var(--pm-border);
        border-radius: 12px;
        padding: 11px 16px;
        font-size: 0.875rem;
        width: 100%;
        background: var(--pm-surface-2);
        color: var(--pm-text-1);
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }

    input[type="date"].pm-form-control {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
        display: block !important;
        margin: 0 !important;
    }

    /* Target inner native controls on iOS/WebKit and blink to prevent overflow */
    input[type="date"].pm-form-control::-webkit-datetime-edit {
        display: flex !important;
        width: 100% !important;
        overflow: hidden !important;
        padding: 0 !important;
    }

    input[type="date"].pm-form-control::-webkit-datetime-edit-fields-wrapper {
        display: flex !important;
        flex: 1 !important;
        min-width: 0 !important;
    }

    @media (max-width: 576px) {
        input[type="date"].pm-form-control {
            padding-left: 8px !important;
            padding-right: 8px !important;
            font-size: 0.8rem !important;
        }
    }

    .pm-form-control:focus {
        border-color: var(--da-cyan);
        background: var(--pm-surface);
        box-shadow: 0 0 0 3px var(--da-cyan-soft);
    }

    select.pm-form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 16px center;
        background-repeat: no-repeat;
        background-size: 18px;
        padding-right: 40px;
    }

    html[dir="rtl"] select.pm-form-control {
        background-position: left 16px center;
        padding-left: 40px;
        padding-right: 16px;
    }

    /* Product Cards styling */
    .product-card {
        border: 1.5px solid var(--pm-border);
        border-radius: 20px;
        background-color: var(--pm-surface);
        transition: all 0.2s ease-in-out;
        overflow: hidden;
    }

    .product-card:hover {
        border-color: var(--da-cyan);
        transform: translateY(-2px);
        box-shadow: var(--pm-shadow-md);
    }

    .product-card-header {
        background: var(--pm-surface-2);
        border-bottom: 1.5px solid var(--pm-border);
        padding: 18px 24px;
        transition: background 0.3s, border-color 0.3s;
    }

    .product-avatar {
        background: var(--da-cyan-soft);
        color: var(--da-cyan);
        border-radius: 14px;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Premium Button style */
    .pm-btn-save {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        border: none;
        border-radius: 12px;
        padding: 12px 28px;
        font-size: .875rem;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        transition: all .25s;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.2) !important;
        font-family: inherit;
        letter-spacing: .2px;
        position: relative;
        overflow: hidden;
    }

    .pm-btn-save::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.12) 0%, transparent 100%);
        opacity: 0;
        transition: opacity .2s;
    }

    .pm-btn-save:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
        box-shadow: 0 8px 28px rgba(37, 99, 235, 0.3) !important;
        transform: translateY(-2px);
        color: #fff;
    }

    .pm-btn-save:hover::before { opacity: 1; }

    .pm-btn-cancel {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--pm-surface);
        border: 1.5px solid var(--pm-border);
        border-radius: 12px;
        padding: 11px 22px;
        font-size: .875rem;
        font-weight: 600;
        color: var(--pm-text-3);
        cursor: pointer;
        transition: all .2s;
        font-family: inherit;
        text-decoration: none;
    }

    .pm-btn-cancel:hover { background: var(--pm-surface-2); border-color: var(--pm-border); color: var(--pm-text-1); }

    /* Hero header */
    .da-hero {
        background: var(--pm-surface);
        border: 1px solid var(--pm-border);
        border-radius: 24px;
        padding: 24px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--pm-shadow-md);
        transition: background 0.3s, border-color 0.3s;
    }
    .da-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--da-grad);
    }

    /* Premium Create/Edit Modal */
    .pm-modal-premium .modal-content {
        border: 1px solid var(--pm-border);
        border-radius: 24px;
        box-shadow: var(--pm-shadow-lg);
        background: var(--pm-surface);
        overflow: hidden;
        transition: background 0.3s, border-color 0.3s;
    }

    .pm-modal-header-premium {
        background: linear-gradient(135deg, #060d1f 0%, #0f172a 60%, #060d1f 100%) !important;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        border-bottom: none !important;
        display: block !important;
    }
    .pm-modal-header-premium::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
        background-size: 22px 22px;
        pointer-events: none;
    }
    .pm-modal-header-glow {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
    }
    .pm-modal-header-glow-1 { width:220px; height:220px; background:rgba(0,200,255,.25); top:-80px; right:-60px; }
    .pm-modal-header-glow-2 { width:160px; height:160px; background:rgba(255,20,147,.18); bottom:-60px; left:-40px; }

    .pm-modal-icon-premium {
        width: 52px; height: 52px;
        border-radius: 16px;
        background: rgba(255,255,255,.1);
        border: 1.5px solid rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        color: var(--da-cyan);
        flex-shrink: 0;
        backdrop-filter: blur(8px);
    }
    .pm-modal-title-premium {
        font-size: 1.15rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        letter-spacing: -.3px;
    }
    .pm-modal-sub-premium {
        font-size: .78rem;
        color: #60a5fa !important;
        margin: 3px 0 0;
        font-weight: 500;
    }
    .pm-modal-close-premium {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: rgba(255,255,255,.08);
        border: 1.5px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.7);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all .2s;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .pm-modal-close-premium:hover { background: rgba(255,255,255,.16); color: #fff; }

    .pm-modal-body-premium {
        padding: 28px 32px;
        background: var(--pm-surface);
        transition: background 0.3s;
    }

    .pm-section-label {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .9px;
        color: var(--da-cyan);
        margin-bottom: 16px;
        margin-top: 8px;
    }
    .pm-section-label::after {
        content: '';
        flex: 1;
        height: 1.5px;
        background: linear-gradient(90deg, var(--da-cyan-soft) 0%, transparent 100%);
        border-radius: 99px;
    }

    .pm-input-group {
        display: flex;
        align-items: stretch;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid var(--pm-border);
        background: var(--pm-surface-2);
        transition: all 0.2s;
    }
    .pm-input-group:focus-within {
        border-color: var(--da-cyan);
        background: var(--pm-surface);
        box-shadow: 0 0 0 3px var(--da-cyan-soft);
    }
    .pm-input-group-text {
        background: var(--pm-surface-3);
        padding: 0 16px;
        color: var(--pm-text-3);
        font-size: 0.875rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        user-select: none;
        border-right: 1.5px solid var(--pm-border);
        transition: background 0.3s, border-color 0.3s, color 0.3s;
    }
    html[dir="rtl"] .pm-input-group-text {
        border-right: none;
        border-left: 1.5px solid var(--pm-border);
    }
    .pm-input-group .pm-form-control {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        flex: 1;
        border-radius: 0 !important;
    }

    /* Smart Search Results */
    .search-container {
        position: relative;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: var(--pm-surface);
        border: 1px solid var(--pm-border);
        border-radius: 14px;
        box-shadow: var(--pm-shadow-lg);
        max-height: 320px;
        overflow-y: auto;
        display: none;
    }

    .search-result-item {
        padding: 12px 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--pm-border-soft);
        transition: background 0.2s;
    }

    .search-result-item:hover, .search-result-item.active {
        background: var(--da-cyan-soft);
        color: var(--da-cyan);
    }

    .search-result-item .unit-btn {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 100px;
        border: 1.5px solid var(--pm-primary);
        background-color: transparent;
        color: var(--pm-primary);
        transition: all 0.2s ease;
        margin-left: 8px;
    }
    html[dir="rtl"] .search-result-item .unit-btn {
        margin-left: 0;
        margin-right: 8px;
    }
    .search-result-item .unit-btn:hover {
        background-color: var(--pm-primary);
        color: white !important;
    }

    /* Badges & Statuses */
    .badge-paid { background-color: rgba(16, 185, 129, 0.08); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.15); }
    .badge-partial { background-color: rgba(245, 158, 11, 0.08); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.15); }
    .badge-unpaid { background-color: rgba(244, 63, 94, 0.08); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.15); }

    /* Custom Input Group Button alignment */
    .btn-input-group {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    html[dir="rtl"] .btn-input-group {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    /* Dot grid overlay for background */
    #content::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image: radial-gradient(rgba(99,102,241,.09) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
        z-index: 0;
    }
    #content > .container-fluid { position: relative; z-index: 1; }
    @media (max-width: 576px) {
        .pm-modal-premium.modal-dialog-centered {
            align-items: flex-start !important;
            margin: 0.5rem auto !important;
        }
        .pm-modal-header-premium {
            padding: 14px 16px !important;
        }
        .pm-modal-icon-premium {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
            font-size: 1.1rem !important;
        }
        .pm-modal-title-premium {
            font-size: 0.95rem !important;
        }
        .pm-modal-sub-premium {
            font-size: 0.7rem !important;
            margin-top: 1px !important;
        }
        .pm-modal-close-premium {
            width: 30px !important;
            height: 30px !important;
            border-radius: 8px !important;
            font-size: 0.85rem !important;
        }
    }

    @media (max-width: 768px) {
        .search-container .pm-input-group {
            display: flex !important;
            flex-direction: column !important;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            gap: 10px !important;
        }
        .search-container .pm-input-group-text {
            display: none !important;
        }
        .search-container .pm-input-group .pm-form-control {
            border: 1.5px solid var(--pm-border) !important;
            border-radius: 12px !important;
            background: var(--pm-surface-2) !important;
            padding: 12px 16px !important;
            width: 100% !important;
        }
        .search-container .pm-input-group button {
            width: 100% !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
    }

    [data-pm-theme="dark"] hr,
    [data-pm-theme="dark"] .border-bottom,
    [data-pm-theme="dark"] .border-top {
        border-color: rgba(255, 255, 255, 0.15) !important;
        opacity: 1 !important;
    }

    [data-pm-theme="dark"] .text-muted,
    [data-pm-theme="dark"] .pm-form-label,
    [data-pm-theme="dark"] .pm-input-group-text,
    [data-pm-theme="dark"] #emptyState h5,
    [data-pm-theme="dark"] #emptyState p,
    [data-pm-theme="dark"] #emptyState i,
    [data-pm-theme="dark"] .text-dark,
    [data-pm-theme="dark"] .text-secondary,
    [data-pm-theme="dark"] .small.text-muted,
    [data-pm-theme="dark"] .text-white-50 {
        color: rgba(255, 255, 255, 0.75) !important;
    }

    [data-pm-theme="dark"] ::placeholder,
    [data-pm-theme="dark"] input::placeholder,
    [data-pm-theme="dark"] textarea::placeholder,
    [data-pm-theme="dark"] .pm-form-control::placeholder {
        color: rgba(255, 255, 255, 0.45) !important;
    }
</style>
@endpush

<div class="purchase-container py-3">
    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- Left Side: Main Header & Product Cards -->
            <div class="col-lg-8">
                <!-- 1. Purchase Header Section -->
                <div class="pm-card p-4 mb-4">
                    <h5 class="mb-4 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-card-list text-primary"></i> 
                        {{ __('purchases.purchase_invoice') }}
                    </h5>

                    <div class="row g-3">
                        <!-- Supplier -->
                        <div class="col-12 col-md-6">
                            <label class="pm-form-label">{{ __('purchases.supplier') }} <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplierSelect" class="pm-form-control select2-supplier" required>
                                <option value="">{{ __('purchases.select_supplier') }}</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->supplier_number }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Invoice Number -->
                        <div class="col-12 col-md-3">
                            <label class="pm-form-label">{{ __('purchases.invoice_number') }}</label>
                            <input type="text" name="invoice_number" class="pm-form-control fw-bold font-monospace" readonly placeholder="{{ app()->getLocale() == 'ar' ? 'توليد تلقائي' : 'Auto-Generated' }}" value="">
                        </div>

                        <!-- Purchase Date -->
                        <div class="col-12 col-md-3">
                            <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'تاريخ الشراء' : 'Purchase Date' }} <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="pm-form-control" required value="{{ date('Y-m-d') }}">
                        </div>


                        <!-- Notes -->
                        <div class="col-12">
                            <label class="pm-form-label">{{ __('purchases.notes') }}</label>
                            <textarea name="notes" class="pm-form-control" rows="2" placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب أي شروط خاصة، مراجع أو ملاحظات هنا...' : 'Write any specific terms, references or notes here...' }}"></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Smart Product Search Section -->
                <div class="pm-card p-4 mb-4">
                    <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-search text-primary"></i> 
                        {{ app()->getLocale() == 'ar' ? 'البحث وإضافة المنتجات' : 'Search & Add Products' }}
                    </h5>
                    <div class="search-container">
                        <div class="pm-input-group">
                            <span class="pm-input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="smartSearch" class="pm-form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث باسم المنتج، الباركود، العلامة التجارية، أو رمز SKU...' : 'Search by product name, barcode, brand, or SKU...' }}" autocomplete="off">
                            <button type="button" class="btn btn-primary px-4 border-0" style="border-radius: 0 12px 12px 0;" data-bs-toggle="modal" data-bs-target="#quickProductModal">
                                <i class="bi bi-plus-circle me-1"></i> {{ __('purchases.quick_add_product') }}
                            </button>
                        </div>
                        <!-- Dropdown Results list -->
                        <div class="search-results" id="searchResults">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>

                <!-- 3. Product Cards Container -->
                <h5 class="fw-bold mb-3 d-flex justify-content-between align-items-center text-dark">
                    <span><i class="bi bi-box-seam me-2 text-primary"></i> {{ __('purchases.products') }}</span>
                    <span class="badge bg-secondary-subtle text-secondary" id="cardCount">{{ app()->getLocale() == 'ar' ? '0 منتجات' : '0 Products' }}</span>
                </h5>
                <div id="productCardsContainer">
                    <!-- Template Card (Hidden) -->
                    <div class="product-card pm-card mb-3 d-none" id="cardTemplate">
                        <div class="product-card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="product-avatar">
                                    <i class="bi bi-box-seam fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold product-name-display">[Product Name]</h6>
                                    <small class="text-muted">
                                        {{ app()->getLocale() == 'ar' ? 'الرمز الكودي (SKU):' : 'SKU:' }} <code class="product-sku-display">[SKU]</code> | 
                                        {{ app()->getLocale() == 'ar' ? 'الماركة:' : 'Brand:' }} <span class="product-brand-display">[Brand]</span>
                                    </small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-card-btn" style="border-radius: 8px;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <input type="hidden" class="product-id-input">
                            <div class="row g-3">
                                <!-- Unit Selector -->
                                <div class="col-md-6 col-lg-3">
                                    <label class="pm-form-label">{{ __('Unit') }}</label>
                                    <select class="pm-form-control unit-select">
                                        <!-- Dynamically loaded -->
                                    </select>
                                    <input type="hidden" class="unit-name-input">
                                    <input type="hidden" class="conversion-factor-input" value="1">
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-6 col-lg-3">
                                    <label class="pm-form-label">{{ __('purchases.quantity') }}</label>
                                    <input type="number" step="1" class="pm-form-control quantity-input" min="1" placeholder="0">
                                </div>

                                <!-- Purchase Price -->
                                <div class="col-md-6 col-lg-3">
                                    <label class="pm-form-label">{{ __('purchases.purchase_price') }}</label>
                                    <input type="number" step="0.01" class="pm-form-control price-input" min="0" placeholder="0.00">
                                </div>

                                <!-- Expiry Date -->
                                <div class="col-md-6 col-lg-3">
                                    <label class="pm-form-label">{{ __('purchases.expiry_date') }}</label>
                                    <input type="date" class="pm-form-control expiry-input">
                                </div>
                            </div>

                            <!-- Conversion Details & Stock Preview -->
                            <div class="row g-3 mt-3">
                                <input type="hidden" class="batch-id-input">
                                <input type="hidden" class="batch-number-input" value="">
                                
                                <div class="col-md-12 d-flex flex-row justify-content-between gap-3">
                                    <!-- Unit conversion preview -->
                                    <div class="p-2 bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-3 small flex-grow-1 d-flex align-items-center">
                                        <div class="w-100">
                                            <i class="bi bi-info-circle me-1"></i> <span class="conversion-text">1 Base Unit</span>
                                        </div>
                                    </div>
                                    <!-- Live Stock preview -->
                                    <div class="p-2 bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-3 small flex-grow-1 d-flex align-items-center">
                                        <div class="w-100">
                                            <i class="bi bi-plus-circle me-1"></i> Stock Added: <span class="stock-added-text fw-bold">0 Base Units</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Subtotal Row -->
                            <div class="mt-3 text-end border-top pt-3">
                                <span class="text-muted small me-2">{{ __('purchases.total') }}:</span>
                                <span class="h5 fw-bold text-primary mb-0 card-subtotal-display">0.00</span>
                                <input type="hidden" class="row-subtotal-input" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center py-5 text-muted pm-card" id="emptyState">
                    <i class="bi bi-cart-x fs-1 mb-3 text-muted"></i>
                    <h5>{{ app()->getLocale() == 'ar' ? 'لم يتم إضافة منتجات بعد' : 'No products added yet' }}</h5>
                    <p class="small text-muted mb-0">{{ app()->getLocale() == 'ar' ? 'استخدم البحث الذكي أعلاه أو الإضافة السريعة لملء قائمة بطاقات الشراء.' : 'Use the smart search above or quick add a product to populate the purchase card list.' }}</p>
                </div>
            </div>

            <!-- Right Side: Sticky Summary Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="pm-card overflow-hidden">
                        <div class="bg-primary text-white p-4" style="background: linear-gradient(135deg, #060d1f 0%, #0f172a 60%, #060d1f 100%) !important; position: relative;">
                            <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                            <h5 class="mb-0 fw-bold position-relative" style="z-index: 2;"><i class="bi bi-wallet2 me-2"></i> {{ app()->getLocale() == 'ar' ? 'الملخص والدفع' : 'Summary & Payment' }}</h5>
                            <p class="text-white-50 small mb-0 mt-1 position-relative" style="z-index: 2;">{{ app()->getLocale() == 'ar' ? 'راجع المجاميع وحدد طريقة الدفع' : 'Review totals and assign payment method' }}</p>
                        </div>
                        <div class="p-4">
                            <!-- Financial Fields -->
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="fw-semibold text-dark">{{ __('purchases.subtotal') }}</span>
                                <span class="fw-bold text-dark" id="subtotalDisplay">0.00</span>
                                <input type="hidden" name="subtotal" id="subtotalInput" value="0">
                            </div>

                            <!-- Discount Input -->
                            <div class="mb-3">
                                <label class="pm-form-label">{{ __('purchases.discount') }}</label>
                                <div class="pm-input-group">
                                    <input type="number" name="discount" id="discountInput" class="pm-form-control text-end" step="0.01" value="0">
                                    <span class="pm-input-group-text text-muted">USD</span>
                                </div>
                            </div>

                            <!-- Shipping Input -->
                            <div class="mb-3">
                                <label class="pm-form-label">{{ __('purchases.shipping_cost') }}</label>
                                <div class="pm-input-group">
                                    <input type="number" name="shipping_cost" id="shippingCostInput" class="pm-form-control text-end" step="0.01" value="0">
                                    <span class="pm-input-group-text text-muted">USD</span>
                                </div>
                            </div>

                            <!-- Tax Toggle & Rates -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="pm-form-label mb-0">{{ __('purchases.tax_rate') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="taxToggle" style="cursor:pointer">
                                    </div>
                                </div>
                                <div id="taxRateContainer" style="display:none">
                                    <div class="pm-input-group">
                                        <input type="number" name="tax_rate" id="taxRateInput" class="pm-form-control text-end" step="0.01" value="0">
                                        <button type="button" class="btn btn-outline-primary px-3 btn-input-group" id="suggestTax">15%</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tax Amount Display Row -->
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2" id="taxAmountRow" style="display:none">
                                <span class="small text-muted">{{ __('purchases.tax_amount') }}</span>
                                <span class="small text-muted" id="taxAmountDisplay">0.00</span>
                                <input type="hidden" name="tax_amount" id="taxAmountInput" value="0">
                            </div>

                            <!-- Net Total -->
                            <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-3" style="background: var(--pm-primary-soft); border: 1.5px solid rgba(59, 130, 246, 0.15);">
                                <span class="h6 mb-0 fw-bold text-primary">{{ __('purchases.net_total') }}</span>
                                <span class="h4 mb-0 fw-bold text-primary font-monospace" id="netTotalDisplay">0.00</span>
                                <input type="hidden" name="total_amount" id="netTotalInput" value="0">
                            </div>

                            <hr class="my-4">

                            <!-- Payment Method -->
                            <div class="mb-3">
                                <label class="pm-form-label">{{ __('purchases.payment_method') }} <span class="text-danger">*</span></label>
                                <select name="payment_method" id="paymentMethod" class="pm-form-control" required>
                                    <option value="Cash">{{ __('purchases.cash') }}</option>
                                    <option value="Bank Transfer">{{ __('purchases.bank_transfer') }}</option>
                                    <option value="Card">{{ __('purchases.card') }}</option>
                                    <option value="Credit">{{ __('purchases.credit') }}</option>
                                </select>
                            </div>

                            <!-- Paid Amount -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="pm-form-label mb-0">{{ __('purchases.paid_amount') }} <span class="text-danger">*</span></label>
                                    <span class="badge badge-unpaid py-1 px-2 rounded" id="paidBadge">{{ app()->getLocale() == 'ar' ? 'غير مدفوع' : 'Unpaid' }}</span>
                                </div>
                                <input type="number" name="paid_amount" id="paidAmountInput" class="pm-form-control text-end font-monospace" step="0.01" value="0" required>
                            </div>

                            <!-- Balance Due -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-semibold text-danger">{{ __('purchases.remaining_balance') }}</span>
                                <span class="h5 mb-0 fw-bold text-danger font-monospace" id="remainingBalanceDisplay">0.00</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-column gap-2 mt-4">
                                <button type="submit" class="pm-btn-save w-100 py-3 justify-content-center">
                                    <i class="bi bi-check-lg me-1"></i> {{ __('purchases.save_purchase') }}
                                </button>
                                <button type="button" class="btn btn-outline-danger w-100 py-2" style="border-radius: 12px;" onclick="window.location.reload();">
                                    <i class="bi bi-arrow-counterclockwise"></i> {{ __('pos.reset') ?? 'Reset' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quick Add Supplier Modal -->
<div class="modal fade pm-modal" id="quickSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered pm-modal-premium">
        <form id="quickSupplierForm" class="modal-content">
            {{-- Premium Header --}}
            <div class="pm-modal-header-premium modal-header">
                <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium">Quick Add Supplier</h5>
                        <p class="pm-modal-sub-premium">{{ __('purchases.purchase_invoice') }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="pm-modal-body-premium modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="pm-form-label">Supplier Name (Arabic) <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="pm-form-control">
                    </div>
                    <div class="col-12">
                        <label class="pm-form-label">Supplier Name (English) <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="pm-form-control">
                    </div>
                    <div class="col-12">
                        <label class="pm-form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="pm-form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="pm-form-label">Email</label>
                        <input type="email" name="email" class="pm-form-control">
                    </div>
                    <div class="col-12">
                        <label class="pm-form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="pm-form-control">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pm-modal-footer-premium modal-footer">
                <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="pm-btn-save"><i class="bi bi-save me-1"></i> Save Supplier</button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Add Product Modal -->
<div class="modal fade pm-modal" id="quickProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered pm-modal-premium">
        <form id="quickProductForm" enctype="multipart/form-data" class="modal-content">
            {{-- Premium Header --}}
            <div class="pm-modal-header-premium modal-header">
                <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium">{{ __('purchases.quick_add_product') }}</h5>
                        <p class="pm-modal-sub-premium">Product Management</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="pm-modal-body-premium modal-body">
                <div class="row g-3">
                    {{-- Section: Basic Info --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-info-circle-fill"></i> Product Information</div>
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('purchases.product_name_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="pm-form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('purchases.product_name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="pm-form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('pos.barcode') }} <span class="text-danger">*</span></label>
                        <input type="text" name="barcode" id="quickBarcode" class="pm-form-control bg-light" readonly required>
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('pos.category') }} <span class="text-danger">*</span></label>
                        <select name="category_id" class="pm-form-control" required>
                            <option value="">{{ __('purchases.select_product') }}</option>
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Section: Price & Units --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-tag-fill"></i> Price & Units</div>
                    </div>
                    <div class="col-md-4">
                        <label class="pm-form-label">{{ __('purchases.purchase_price') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="purchase_price" class="pm-form-control" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="pm-form-label">{{ __('purchases.min_stock') }} <span class="text-danger">*</span></label>
                        <input type="number" name="minimum_stock" class="pm-form-control" value="0" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="pm-form-label">{{ __('base_unit_name') ?? 'Base Unit' }} <span class="text-danger">*</span></label>
                        <input type="text" name="base_unit_name" class="pm-form-control" value="Piece" required>
                    </div>

                    {{-- Image & Desc --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-image"></i> Details & Image</div>
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('purchases.product_image') }}</label>
                        <input type="file" name="image" class="pm-form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('purchases.description_ar') }}</label>
                        <textarea name="description_ar" class="pm-form-control" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="pm-form-label">{{ __('purchases.description_en') }}</label>
                        <textarea name="description_en" class="pm-form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pm-modal-footer-premium modal-footer">
                <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">{{ __('pos.cancel') }}</button>
                <button type="submit" class="pm-btn-save">
                    <i class="bi bi-save me-1"></i> {{ __('pos.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#quickSupplierModal').appendTo('body');
        $('#quickProductModal').appendTo('body');
        const cardsContainer = $('#productCardsContainer');
        const cardTemplate = $('#cardTemplate');
        const emptyState = $('#emptyState');
        const smartSearch = $('#smartSearch');
        const searchResults = $('#searchResults');

        const subtotalDisplay = $('#subtotalDisplay');
        const discountInput = $('#discountInput');
        const shippingCostInput = $('#shippingCostInput');
        const taxRateInput = $('#taxRateInput');
        const taxAmountInput = $('#taxAmountInput');
        const taxAmountDisplay = $('#taxAmountDisplay');
        const netTotalDisplay = $('#netTotalDisplay');
        const netTotalInput = $('#netTotalInput');
        const paidAmountInput = $('#paidAmountInput');
        const remainingBalanceDisplay = $('#remainingBalanceDisplay');
        const paidBadge = $('#paidBadge');

        let cardCount = 0;
        let activeSearchIndex = -1;
        const locale = "{{ app()->getLocale() }}";

        // Initialize Select2 Supplier
        $('.select2-supplier').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Trigger attachment file name display
        $('#invoiceAttachment').on('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            if (fileName) {
                $('#attachmentName').removeClass('d-none').find('span').text(fileName);
            } else {
                $('#attachmentName').addClass('d-none');
            }
        });

        // Smart Search functionality
        smartSearch.on('input', function() {
            const term = $(this).val().trim();
            if (term.length < 2) {
                searchResults.hide().empty();
                return;
            }

            $.get('{{ route("products.search") }}', { term: term, for_purchase: 1 }, function(data) {
                searchResults.empty();
                if (data.length === 0) {
                    const noProductsText = locale === 'ar' ? 'لم يتم العثور على منتجات.' : 'No products found.';
                    searchResults.append(`<div class="p-3 text-muted text-center">${noProductsText}</div>`).show();
                    return;
                }

                data.forEach((product, idx) => {
                    const brand = product.brand || (locale === 'ar' ? 'بدون ماركة' : 'No Brand');
                    const sku = product.sku || 'N/A';
                    
                    let unitButtonsHtml = `<button type="button" class="unit-btn add-unit-trigger" data-unit-id="base" data-index="${idx}">${product.base_unit_name}</button>`;
                    if (product.additional_units && product.additional_units.length > 0) {
                        product.additional_units.forEach(u => {
                            unitButtonsHtml += `<button type="button" class="unit-btn add-unit-trigger" data-unit-id="${u.id}" data-index="${idx}">${u.unit_name}</button>`;
                        });
                    }

                    let unitBadge = '';
                    if (product.matched_unit_id && product.matched_unit_id !== 'base') {
                        unitBadge = ` <span class="badge bg-warning text-dark ms-2">${product.matched_unit_name}</span>`;
                    }
                    
                    const skuLabel = locale === 'ar' ? 'الرمز الكودي (SKU):' : 'SKU:';
                    const brandLabel = locale === 'ar' ? 'الماركة:' : 'Brand:';
                    const stockLabel = locale === 'ar' ? 'المخزون:' : 'Stock:';
                    
                    const itemHtml = $(`
                        <div class="search-result-item d-flex justify-content-between align-items-center" data-index="${idx}">
                            <div>
                                <strong class="text-primary">${product.text}</strong>${unitBadge}<br>
                                <small class="text-muted">${skuLabel} ${sku} | ${brandLabel} ${brand} | ${stockLabel} ${product.stock} ${product.base_unit_name}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="unit-options-pane d-flex me-3">
                                    ${unitButtonsHtml}
                                </div>
                                <span class="badge bg-secondary font-monospace">${product.price.toFixed(2)} USD</span>
                            </div>
                        </div>
                    `);
                    itemHtml.data('product', product);
                    searchResults.append(itemHtml);
                });
                searchResults.show();
                activeSearchIndex = -1;
            });
        });

        // Barcode reader instant trigger (on Enter key)
        smartSearch.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const items = searchResults.find('.search-result-item');
                if (activeSearchIndex >= 0 && items.length > 0) {
                    items.eq(activeSearchIndex).click();
                } else if (items.length === 1) {
                    items.first().click();
                } else {
                    const barcode = $(this).val();
                    if (!barcode) return;

                    $.get(`/products/barcode/${barcode}`, function(data) {
                        smartSearch.val('');
                        searchResults.hide().empty();
                        
                        const mappedProd = {
                            id: data.product.id,
                            text: data.name,
                            barcode: data.product.barcode,
                            price: parseFloat(data.product.purchase_price || 0),
                            purchase_price: parseFloat(data.product.purchase_price || 0),
                            stock: 0,
                            base_unit_name: data.base_unit_name || 'Piece',
                            additional_units: data.additional_units || [],
                            sku: data.product.sku,
                            brand: data.product.brand,
                            matched_unit_id: data.scanned_unit_id
                        };
                        addProductCard(mappedProd);
                    }).fail(function() {
                        Swal.fire({
                            title: '{{ __("purchases.quick_add_product") }}',
                            text: '{{ __("purchases.product_not_found") }}',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#2563EB',
                            cancelButtonColor: '#64748B',
                            confirmButtonText: '{{ __("purchases.yes_add_it") }}',
                            cancelButtonText: '{{ __("purchases.no_cancel") }}'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#quickBarcode').val(barcode);
                                $('#quickProductModal').modal('show');
                            }
                        });
                    });
                }
            }
        });

        // Keyboard navigation in search results
        smartSearch.on('keydown', function(e) {
            const items = searchResults.find('.search-result-item');
            if (!items.length) return;

            if (e.which === 40) { // Down
                e.preventDefault();
                activeSearchIndex = (activeSearchIndex + 1) % items.length;
                items.removeClass('active');
                items.eq(activeSearchIndex).addClass('active').get(0).scrollIntoView({ block: 'nearest' });
            } else if (e.which === 38) { // Up
                e.preventDefault();
                activeSearchIndex = (activeSearchIndex - 1 + items.length) % items.length;
                items.removeClass('active');
                items.eq(activeSearchIndex).addClass('active').get(0).scrollIntoView({ block: 'nearest' });
            }
        });

        // Handle clicking direct unit buttons in dropdown
        $(document).on('click', '.add-unit-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const trigger = $(this);
            const parentRow = trigger.closest('.search-result-item');
            const product = parentRow.data('product');
            const unitId = trigger.data('unit-id');

            // Clone product object and override matched_unit_id
            const productClone = Object.assign({}, product, { matched_unit_id: unitId });
            addProductCard(productClone);

            smartSearch.val('');
            searchResults.hide().empty();
        });

        // Click search result item
        $(document).on('click', '.search-result-item', function(e) {
            if ($(e.target).closest('.add-unit-trigger').length) {
                return;
            }
            const product = $(this).data('product');
            addProductCard(product);
            smartSearch.val('');
            searchResults.hide().empty();
        });

        // Hide search results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-container').length) {
                searchResults.hide();
            }
        });

        function addProductCard(product) {
            cardCount++;
            emptyState.addClass('d-none');
            const isAr = '{{ app()->getLocale() }}' === 'ar';
            const productText = isAr ? (cardCount > 1 || cardCount == 0 ? 'منتجات' : 'منتج') : `Product${cardCount > 1 ? 's' : ''}`;
            $('#cardCount').text(`${cardCount} ${productText}`);

            const newCard = cardTemplate.clone();
            newCard.removeClass('d-none').attr('id', `product-card-${cardCount}`);

            newCard.find('.product-name-display').text(product.text);
            newCard.find('.product-sku-display').text(product.sku || 'N/A');
            newCard.find('.product-brand-display').text(product.brand || 'No Brand');

            newCard.find('.product-id-input').attr('name', `items[${cardCount}][product_id]`).val(product.id);
            newCard.find('.unit-name-input').attr('name', `items[${cardCount}][unit_name]`).val(product.base_unit_name);
            newCard.find('.conversion-factor-input').attr('name', `items[${cardCount}][conversion_factor]`).val(1);
            newCard.find('.quantity-input').attr('name', `items[${cardCount}][quantity]`).attr('required', true);
            const defaultPrice = product.purchase_price !== undefined ? product.purchase_price : (product.price || 0);
            newCard.find('.price-input').attr('name', `items[${cardCount}][purchase_price]`).attr('required', true).val(defaultPrice);
            newCard.find('.expiry-input').attr('name', `items[${cardCount}][expiry_date]`);
            newCard.find('.batch-id-input').attr('name', `items[${cardCount}][batch_id]`);
            newCard.find('.batch-number-input').attr('name', `items[${cardCount}][batch_number]`);

            const unitSelect = newCard.find('.unit-select');
            unitSelect.attr('name', `items[${cardCount}][unit_select_display]`);
            unitSelect.append(`<option value="base" data-factor="1" data-name="${product.base_unit_name}">${product.base_unit_name} (Base)</option>`);
            
            if (product.additional_units && Array.isArray(product.additional_units)) {
                product.additional_units.forEach(u => {
                    unitSelect.append(`<option value="${u.id}" data-factor="${u.conversion_factor}" data-name="${u.unit_name}">${u.unit_name} (x${parseFloat(u.conversion_factor)})</option>`);
                });
            }

            // Set default selected unit based on barcode scanning
            const matchedUnitId = product.matched_unit_id || product.scanned_unit_id || 'base';
            unitSelect.val(matchedUnitId);

            loadBatchesInline(product.id, newCard);

            cardsContainer.append(newCard);

            const qtyInput = newCard.find('.quantity-input');
            const priceInput = newCard.find('.price-input');
            const inlineBatchSelect = newCard.find('.inline-batch-select');
            const batchNumInput = newCard.find('.batch-number-input');

            const recalculateCard = () => {
                const qty = parseFloat(qtyInput.val()) || 0;
                const price = parseFloat(priceInput.val()) || 0;
                const factor = parseFloat(newCard.find('.conversion-factor-input').val()) || 1;
                const baseUnit = product.base_unit_name;

                const total = qty * price;
                newCard.find('.card-subtotal-display').text(total.toFixed(2));
                newCard.find('.row-subtotal-input').val(total.toFixed(2));

                const baseQty = qty * factor;
                newCard.find('.stock-added-text').text(`${baseQty.toFixed(2)} ${baseUnit}`);

                calculateGrandTotal();
            };

            qtyInput.on('input change', recalculateCard);
            priceInput.on('input change', recalculateCard);

            unitSelect.on('change', function() {
                const opt = $(this).find('option:selected');
                const factor = parseFloat(opt.data('factor')) || 1;
                const name = opt.data('name');

                newCard.find('.unit-name-input').val(name);
                newCard.find('.conversion-factor-input').val(factor);

                newCard.find('.conversion-text').html(`<i class="bi bi-info-circle me-1"></i> 1 ${name} = ${factor} ${product.base_unit_name}`);

                const basePrice = parseFloat(product.purchase_price !== undefined ? product.purchase_price : product.price) || 0;
                priceInput.val((basePrice * factor).toFixed(2));

                recalculateCard();
            });

            inlineBatchSelect.on('change', function() {
                const opt = $(this).find('option:selected');
                if (opt.val()) {
                    newCard.find('.batch-id-input').val(opt.val());
                    newCard.find('.current-batch-status').text('Selected Batch').removeClass('bg-secondary-subtle').addClass('bg-primary-subtle text-primary');
                    batchNumInput.val(opt.data('number')).prop('disabled', true);
                    
                    newCard.find('.stock-value').text(opt.data('qty') + ' ' + product.base_unit_name);
                    newCard.find('.batch-stock-info').slideDown(150);

                    if (opt.data('price')) {
                        const factor = parseFloat(newCard.find('.conversion-factor-input').val()) || 1;
                        priceInput.val((parseFloat(opt.data('price')) * factor).toFixed(2));
                    }
                    if (opt.data('expiry')) {
                        newCard.find('.expiry-input').val(opt.data('expiry'));
                    }
                } else {
                    newCard.find('.batch-id-input').val('');
                    newCard.find('.current-batch-status').text('New Batch (Auto)').removeClass('bg-primary-subtle text-primary').addClass('bg-secondary-subtle');
                    batchNumInput.val('').prop('disabled', false);
                    newCard.find('.batch-stock-info').slideUp(150);
                }
                recalculateCard();
            });

            batchNumInput.on('input', function() {
                newCard.find('.batch-id-input').val('');
                if ($(this).val()) {
                    newCard.find('.current-batch-status').text('Custom Batch').removeClass('bg-secondary-subtle').addClass('bg-warning-subtle text-warning');
                } else {
                    newCard.find('.current-batch-status').text('New Batch (Auto)').removeClass('bg-warning-subtle text-warning').addClass('bg-secondary-subtle');
                }
            });

            newCard.find('.remove-card-btn').on('click', function() {
                newCard.slideUp(200, function() {
                    newCard.remove();
                    cardCount--;
                    const isAr = '{{ app()->getLocale() }}' === 'ar';
                    const productText = isAr ? (cardCount > 1 || cardCount == 0 ? 'منتجات' : 'منتج') : `Product${cardCount > 1 ? 's' : ''}`;
                    $('#cardCount').text(`${cardCount} ${productText}`);
                    if (cardCount === 0) {
                        emptyState.removeClass('d-none');
                    }
                    calculateGrandTotal();
                });
            });

            unitSelect.trigger('change');
            setTimeout(() => { qtyInput.focus(); }, 150);
        }

        function loadBatchesInline(productId, card) {
            const select = card.find('.inline-batch-select');
            select.html('<option value="">-- Auto-Create New Batch --</option>');
            $.get(`/products/batches/${productId}`, function(data) {
                if (data && data.length > 0) {
                    data.forEach(batch => {
                        const expiry = batch.expiry_date ? batch.expiry_date.substring(0, 10) : '';
                        select.append(`
                            <option value="${batch.id}" data-qty="${batch.quantity}" data-number="${batch.batch_number || ''}" data-price="${batch.purchase_price || ''}" data-expiry="${expiry}">
                                ${batch.batch_number || 'Batch #' + batch.id} (${batch.quantity} avail)
                            </option>
                        `);
                    });
                }
            });
        }

        function calculateGrandTotal() {
            let subtotal = 0;
            $('.row-subtotal-input').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });

            const discount = parseFloat(discountInput.val()) || 0;
            const shipping = parseFloat(shippingCostInput.val()) || 0;
            const taxRate = parseFloat(taxRateInput.val()) || 0;

            const taxableAmount = subtotal - discount;
            const taxAmount = taxableAmount * (taxRate / 100);
            const netTotal = taxableAmount + shipping + taxAmount;
            
            const paid = parseFloat(paidAmountInput.val()) || 0;
            const balance = netTotal - paid;

            subtotalDisplay.text(subtotal.toFixed(2));
            $('#subtotalInput').val(subtotal);

            taxAmountDisplay.text(taxAmount.toFixed(2));
            taxAmountInput.val(taxAmount);

            netTotalDisplay.text(netTotal.toFixed(2));
            netTotalInput.val(netTotal);

            remainingBalanceDisplay.text(balance.toFixed(2));

            const isAr = "{{ app()->getLocale() }}" === 'ar';
            paidBadge.removeClass('badge-paid badge-partial badge-unpaid');
            if (paid === 0) {
                paidBadge.addClass('badge-unpaid').text(isAr ? 'غير مدفوع' : 'Unpaid');
            } else if (paid >= netTotal && netTotal > 0) {
                paidBadge.addClass('badge-paid').text(isAr ? 'مدفوع بالكامل' : 'Fully Paid');
            } else {
                paidBadge.addClass('badge-partial').text(isAr ? 'مدفوع جزئياً' : 'Partially Paid');
            }
        }

        discountInput.on('input', calculateGrandTotal);
        shippingCostInput.on('input', calculateGrandTotal);
        taxRateInput.on('input', calculateGrandTotal);
        paidAmountInput.on('input', calculateGrandTotal);

        $('#taxToggle').on('change', function() {
            if (this.checked) {
                $('#taxRateContainer').fadeIn(200);
                $('#taxAmountRow').fadeIn(200);
            } else {
                $('#taxRateContainer').fadeOut(200);
                $('#taxAmountRow').fadeOut(200);
                taxRateInput.val(0).trigger('input');
            }
        });

        $('#suggestTax').on('click', function() {
            taxRateInput.val(15).trigger('input');
        });

        $('#quickSupplierForm').on('submit', function(e) {
            e.preventDefault();
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: '{{ route("suppliers.quick_store") }}',
                type: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(res) {
                    if (res.success) {
                        const newOpt = `<option value="${res.supplier.id}" selected>${res.name} (${res.supplier_number})</option>`;
                        $('#supplierSelect').append(newOpt).trigger('change');
                        $('#quickSupplierModal').modal('hide');
                        $('#quickSupplierForm')[0].reset();
                        
                        Swal.fire({
                            title: 'Success',
                            text: 'Supplier created successfully!',
                            icon: 'success',
                            confirmButtonColor: '#2563EB'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON.message || 'Error creating supplier',
                        icon: 'error',
                        confirmButtonColor: '#2563EB'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Supplier');
                }
            });
        });

        $('#quickProductForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: '{{ route("products.quick_store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        const mappedProd = {
                            id: res.product.id,
                            text: res.name,
                            barcode: res.product.barcode,
                            price: parseFloat(res.product.purchase_price || 0),
                            stock: 0,
                            base_unit_name: res.base_unit_name || 'Piece',
                            additional_units: res.additional_units || [],
                            sku: res.product.sku,
                            brand: res.product.brand
                        };
                        addProductCard(mappedProd);
                        $('#quickProductModal').modal('hide');
                        $('#quickProductForm')[0].reset();

                        Swal.fire({
                            title: 'Success',
                            text: 'Product created successfully!',
                            icon: 'success',
                            confirmButtonColor: '#2563EB'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON.message || 'Error creating product',
                        icon: 'error',
                        confirmButtonColor: '#2563EB'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save');
                }
            });
        });
    });
</script>
@endpush

@endsection

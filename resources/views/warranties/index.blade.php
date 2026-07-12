@extends('layouts.app')

@section('title', __('pos.warranty_management') ?? 'سجل الضمانات')

@push('styles')
<style>
    /* Category Style Base */
    .top-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .kpi-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.08);
    }
    .cat-toolbar {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: #f8fafc;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        border-left: 1px solid rgba(226, 232, 240, 0.8);
        border-right: 1px solid rgba(226, 232, 240, 0.8);
        border-top: 1px solid rgba(226, 232, 240, 0.8);
    }
    .cat-toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
        flex-wrap: wrap;
    }
    .cat-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    .cat-search-wrap { position: relative; min-width: 220px; max-width: 340px; flex: 1; }
    .cat-search-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; font-size: 0.88rem; z-index: 2; }
    .cat-search-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 14px 10px 38px; font-size: 0.85rem; background: #ffffff; color: #0f172a; outline: none; transition: all 0.2s; }
    .cat-search-input:focus { border-color: #00C8FF; box-shadow: 0 0 0 3px rgba(0,200,255,0.1); }
    .saas-table-card {
        background: white;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-top: none;
        overflow: hidden;
    }
    .saas-table { width: 100%; border-collapse: collapse; }
    .saas-table th { background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important; color: #ffffff !important; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; border-bottom: none !important; white-space: nowrap; text-align: center; vertical-align: middle; }
    .saas-table td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #F8FAFC; font-size: 0.9rem; color: #0F172A; font-weight: 500; white-space: nowrap; text-align: center; }
    .saas-table tr:hover td { background: #F8FAFC; }
    .saas-table tr:last-child td { border-bottom: none; }
    
    .cat-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 11px;
        padding: 9px 14px;
        font-size: 0.82rem;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .cat-select:focus { border-color: #00C8FF; box-shadow: 0 0 0 3px rgba(0,200,255,0.1); }
    .cat-btn-apply {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: #fff !important;
        border: none;
        border-radius: 11px;
        padding: 9px 18px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
    }
    .cat-btn-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3); }

    .cat-add-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #fff !important;
        border: none;
        border-radius: 14px;
        padding: 12px 24px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.2);
        white-space: nowrap;
        text-decoration: none;
    }
    .cat-add-btn:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        transform: translateY(-2px);
        color: #fff;
    }
    .cat-add-btn:active { transform: translateY(0); }

    .badge-vip { background: linear-gradient(135deg, #FFD700 0%, #FDB931 100%); color: #000; }
    .badge-wholesale { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; }
    .badge-regular { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .badge-walkin { background-color: #e9ecef; color: #495057; }
    .status-active { background-color: #d1e7dd; color: #0f5132; }
    .status-inactive { background-color: #e2e3e5; color: #41464b; }
    .status-blocked { background-color: #f8d7da; color: #842029; }

    /* Dark Mode Overrides for Customers Index */
    html[data-app-theme="dark"] .kpi-card {
        background: #1e293b;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .kpi-card h3 {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .kpi-card .text-uppercase {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .cat-toolbar {
        background: #0f172a;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .saas-table-card {
        background: #1e293b;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .saas-table td,
    html[data-app-theme="dark"] .saas-table td .text-dark,
    html[data-app-theme="dark"] .saas-table td .text-muted,
    html[data-app-theme="dark"] .saas-table td .small {
        color: #ffffff !important;
        border-bottom-color: #334155;
    }
    html[data-app-theme="dark"] .saas-table tr:hover td {
        background: #0f172a;
    }
    html[data-app-theme="dark"] .cat-search-input {
        background: #1e293b;
        color: #ffffff;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .cat-select {
        background-color: #1e293b;
        color: #ffffff;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .cat-toolbar-left .text-muted {
        color: #ffffff !important;
    }
    /* Premium Modal Dark Mode Overrides */
    html[data-app-theme="dark"] .pm-modal-premium .modal-content {
        background: #0f172a;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .pm-modal-body-premium {
        background: #0f172a;
    }
    html[data-app-theme="dark"] .pm-modal-footer-premium {
        background: #1e293b;
        border-top-color: #334155;
    }
    html[data-app-theme="dark"] .pm-form-control {
        background: #1e293b;
        border-color: #334155;
        color: #ffffff;
    }
    html[data-app-theme="dark"] .pm-form-control[readonly],
    html[data-app-theme="dark"] .pm-form-control[disabled] {
        background: #334155 !important;
        border-color: #475569 !important;
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .pm-form-label.text-muted {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .pm-modal-body-premium .text-muted {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .pm-btn-cancel {
        background: #1e293b;
        border-color: #334155;
        color: #ffffff;
    }
    html[data-app-theme="dark"] .pm-btn-cancel:hover {
        background: #334155;
        color: #ffffff;
    }
    html[data-app-theme="dark"] .pm-form-label {
        color: #94a3b8;
    }
    html[data-app-theme="dark"] .pm-section-label {
        color: #818cf8;
    }

    /* ══ Premium Create/Edit Modal ══ */
    .pm-modal-premium .modal-content {
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        background: #ffffff;
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
        color: #00c8ff;
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
        position: absolute;
        top: 28px;
        right: 32px;
        z-index: 10;
    }
    html[dir="rtl"] .pm-modal-close-premium { right: auto; left: 32px; }
    .pm-modal-close-premium:hover { background: rgba(255,255,255,.16); color: #fff; }

    .pm-modal-body-premium {
        padding: 28px 32px;
        background: #ffffff;
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
        color: #6366f1;
        margin-bottom: 16px;
        margin-top: 8px;
    }
    .pm-section-label::after {
        content: '';
        flex: 1;
        height: 1.5px;
        background: linear-gradient(90deg, rgba(99,102,241,0.2) 0%, transparent 100%);
        border-radius: 99px;
    }
    html[dir="rtl"] .pm-section-label::after { background: linear-gradient(270deg, rgba(99,102,241,0.2) 0%, transparent 100%); }
    .pm-section-label i { font-size: .88rem; }

    .pm-modal-footer-premium {
        padding: 20px 32px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        transition: background 0.3s, border-color 0.3s;
    }
    .pm-btn-cancel {
        display: inline-flex; align-items: center; gap: 7px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 11px 22px;
        font-size: .875rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all .2s;
        font-family: inherit;
        text-decoration: none;
    }
    .pm-btn-cancel:hover { background: #f1f5f9; border-color: #cbd5e1; color: #0f172a; }

    .pm-btn-save {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 28px;
        font-size: .875rem;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        transition: all .25s;
        box-shadow: 0 4px 16px rgba(99,102,241,0.3);
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
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99,102,241,0.4);
    }
    .pm-btn-save:hover::before { opacity: 1; }
    .pm-btn-save:active { transform: translateY(0); }

    .pm-form-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: .6px;
    }
    .pm-form-control {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 11px 16px;
        font-size: 0.875rem;
        width: 100%;
        background: #f8fafc;
        color: #0f172a;
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }
    .pm-form-control:focus {
        border-color: #6366f1;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
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
    /* Mobile Responsiveness for Toolbar */
    @media (max-width: 767.98px) {
        .cat-toolbar {
            flex-direction: column;
            align-items: stretch;
            padding: 12px;
        }
        .cat-toolbar-left, .cat-toolbar-right {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            flex-wrap: nowrap;
        }
        .cat-search-wrap {
            max-width: 100%;
            width: 100%;
        }
        .cat-select, .cat-add-btn, .cat-btn-apply {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
        .saas-table-card {
            border-radius: 12px;
        }
    }
    .status-warning { background-color: #fff3cd; color: #664d03; }
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="font-family: 'Inter', sans-serif;">
    <!-- Top Stats Cards -->
    <div class="top-stats-row">
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #DCFCE7; color: #16A34A; flex-shrink: 0;">
                <i class="bi bi-shield-check fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.active_warranties') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $kpis['active'] }}</h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #FEF08A; color: #CA8A04; flex-shrink: 0;">
                <i class="bi bi-shield-exclamation fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.expiring_soon_label') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $kpis['expiring_soon'] }}</h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #FEE2E2; color: #EF4444; flex-shrink: 0;">
                <i class="bi bi-shield-x fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.expired_warranties') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $kpis['expired'] }}</h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #E0F2FE; color: #0284C7; flex-shrink: 0;">
                <i class="bi bi-tools fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.total_claims') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $kpis['claims'] }}</h3>
            </div>
        </div>
    </div>

    <form action="{{ route('warranties.index') }}" method="GET">
        <!-- Toolbar (Category Style) -->
        <div class="cat-toolbar">
            <div class="cat-toolbar-left">
                <div class="cat-search-wrap">
                    <i class="bi bi-search cat-search-icon"></i>
                    <input type="text" name="search" class="cat-search-input" placeholder="{{ __('pos.search_warranty_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="cat-toolbar-right">
                <select name="customer_id" class="cat-select" onchange="this.form.submit()">
                    <option value="">{{ __('pos.all_customers') }}</option>
                    <option value="walk_in" {{ $customerId === 'walk_in' ? 'selected' : '' }}>{{ __('pos.walk_in_customer') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>

                <select name="warranty_type" class="cat-select" onchange="this.form.submit()">
                    <option value="">{{ __('pos.all_types') }}</option>
                    <option value="Manufacturer Warranty" {{ $warrantyType == 'Manufacturer Warranty' ? 'selected' : '' }}>{{ __('pos.manufacturer_warranty') }}</option>
                    <option value="Store Warranty" {{ $warrantyType == 'Store Warranty' ? 'selected' : '' }}>{{ __('pos.store_warranty') }}</option>
                    <option value="Extended Warranty" {{ $warrantyType == 'Extended Warranty' ? 'selected' : '' }}>{{ __('pos.extended_warranty') }}</option>
                </select>

                <select name="status" class="cat-select" onchange="this.form.submit()">
                    <option value="">{{ __('pos.all_statuses') }}</option>
                    <option value="Active" {{ $status == 'Active' ? 'selected' : '' }}>{{ __('pos.status_active') }}</option>
                    <option value="Expiring Soon" {{ $status == 'Expiring Soon' ? 'selected' : '' }}>{{ __('pos.status_expiring_soon') }}</option>
                    <option value="Expired" {{ $status == 'Expired' ? 'selected' : '' }}>{{ __('pos.status_expired') }}</option>
                    <option value="Claim Submitted" {{ $status == 'Claim Submitted' ? 'selected' : '' }}>{{ __('pos.status_claim_submitted') }}</option>
                    <option value="Claim Approved" {{ $status == 'Claim Approved' ? 'selected' : '' }}>{{ __('pos.status_claim_approved') }}</option>
                </select>
                
                @if(request()->anyFilled(['search', 'warranty_type', 'status', 'customer_id']))
                <a href="{{ route('warranties.index') }}" class="cat-select text-danger text-decoration-none d-flex align-items-center justify-content-center" title="{{ __('pos.clear_filters') }}" style="padding: 9px; width: 38px;">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Main Table Card -->
    <div class="saas-table-card mb-4">
        <div class="table-responsive">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>{{ __('pos.warranty_number') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.product_details') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.customer') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.type') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.validity_period') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.status') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warranties as $warranty)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark" style="font-size:0.95rem;">{{ $warranty->warranty_number ?? '-' }}</div>
                            <small class="text-muted">{{ __('pos.serial_label') }}: {{ $warranty->serial_number ?: 'N/A' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $warranty->product->name ?? '-' }}</div>
                            <small class="text-muted">{{ __('pos.invoice_label') }}: {{ $warranty->sale->invoice_number ?? '-' }}</small>
                        </td>
                        <td>
                            <div>{{ $warranty->customer->name ?? __('pos.walk_in_customer') }}</div>
                            <small class="text-muted">{{ $warranty->customer->phone ?? '' }}</small>
                        </td>
                        <td>
                            @php
                                $typeTranslations = [
                                    'Manufacturer Warranty' => __('pos.manufacturer_warranty'),
                                    'Store Warranty'        => __('pos.store_warranty'),
                                    'Extended Warranty'     => __('pos.extended_warranty'),
                                ];
                                $translatedType = $typeTranslations[$warranty->warranty_type] ?? ($warranty->warranty_type ?: __('pos.other'));
                            @endphp
                            <span class="badge rounded-pill badge-regular fw-normal px-3">{{ $translatedType }}</span>
                        </td>
                        <td>
                            <div>{{ $warranty->warranty_start_date->format('M d, Y') }}</div>
                            <small class="text-muted">{{ __('pos.to_label') }} {{ $warranty->warranty_end_date->format('M d, Y') }}</small>
                        </td>
                        <td>
                            @php
                                $calcStatus = $warranty->calculated_status;
                                $statusClass = match($calcStatus) {
                                    'Active'           => 'status-active',
                                    'Expiring Soon'    => 'status-warning',
                                    'Expired'          => 'status-blocked',
                                    'Claim Submitted'  => 'status-inactive',
                                    'Claim Approved'   => 'badge-vip',
                                    default            => 'status-inactive'
                                };
                                $statusTranslations = [
                                    'Active'           => __('pos.status_active'),
                                    'Expiring Soon'    => __('pos.status_expiring_soon'),
                                    'Expired'          => __('pos.status_expired'),
                                    'Claim Submitted'  => __('pos.status_claim_submitted'),
                                    'Claim Approved'   => __('pos.status_claim_approved'),
                                ];
                                $translatedStatus = $statusTranslations[$calcStatus] ?? $calcStatus;
                            @endphp
                            <span class="badge rounded-pill {{ $statusClass }} fw-normal px-3">{{ $translatedStatus }}</span>
                        </td>
                        <td>
                            <a href="{{ route('warranties.show', $warranty->id) }}" class="btn-action-edit me-1" title="{{ __('pos.view') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('print-warranties')
                            <a href="{{ route('warranties.print', $warranty->id) }}" class="btn-action-edit" target="_blank" title="{{ __('pos.print') }}">
                                <i class="bi bi-printer"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-shield-x text-muted fs-1 d-block mb-3"></i>
                            <h5 class="text-muted">{{ __('pos.no_warranties_match') }}</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($warranties->hasPages())
    <div class="d-flex justify-content-center">
        {{ $warranties->links() }}
    </div>
    @endif
</div>
@endsection
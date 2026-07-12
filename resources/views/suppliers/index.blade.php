@extends('layouts.app')

@section('title', __('pos.suppliers'))

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
    .saas-table th { background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important; color: #ffffff !important; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; border-bottom: none !important; white-space: nowrap; text-align: left; vertical-align: middle; }
    .saas-table td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #F8FAFC; font-size: 0.9rem; color: #0F172A; font-weight: 500; white-space: nowrap; text-align: left; }
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

    .status-active { background-color: #d1e7dd; color: #0f5132; }
    .status-inactive { background-color: #e2e3e5; color: #41464b; }
    .status-blocked { background-color: #f8d7da; color: #842029; }

    /* Dark Mode Overrides for Index */
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
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="font-family: 'Inter', sans-serif;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Top Stats Cards -->
    <div class="top-stats-row">
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #F3F0FF; color: #6D5DFC; flex-shrink: 0;">
                <i class="bi bi-buildings-fill fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.total_suppliers') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $kpis['total_suppliers'] }}</h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #DCFCE7; color: #16A34A; flex-shrink: 0;">
                <i class="bi bi-building-check fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.active_suppliers') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $kpis['active_suppliers'] }}</h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #E0F2FE; color: #0284C7; flex-shrink: 0;">
                <i class="bi bi-receipt-cutoff fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.total_purchases') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $kpis['total_purchases'] }}</h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #FFEDD5; color: #EA580C; flex-shrink: 0;">
                <i class="bi bi-bag-plus-fill fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.month_purchases') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $kpis['month_purchases'] }}</h3>
            </div>
        </div>
    </div>

    <form action="{{ route('suppliers.index') }}" method="GET">
        <!-- Toolbar (Category Style) -->
        <div class="cat-toolbar">
            <div class="cat-toolbar-left">
                <div class="cat-search-wrap">
                    <i class="bi bi-search cat-search-icon"></i>
                    <input type="text" name="search" class="cat-search-input" placeholder="{{ app()->getLocale() == 'ar' ? 'البحث بالاسم، الهاتف، أو الكود...' : 'Search by Name, Phone, or Code...' }}" value="{{ request('search') }}">
                </div>
                @can('create-suppliers')
                <button type="button" class="cat-add-btn" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                    <i class="bi bi-plus-lg"></i> {{ __('pos.add_supplier') ?? 'Add Supplier' }}
                </button>
                @endcan

            </div>
            <div class="cat-toolbar-right">
                <select name="status" class="cat-select" onchange="this.form.submit()">
                    <option value="">{{ __('pos.status') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ __('pos.'.strtolower($status)) ?? ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Main Table Card -->
    <div class="saas-table-card mb-4">
        <div class="table-responsive">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th class="ps-4">{{ __('pos.supplier_number') ?? 'Code' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.supplier_name') ?? 'Name' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.contact_person') ?? 'Contact' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.phone') ?? 'Phone' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.status') ?? 'Status' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.purchases') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.last_purchase') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th class="text-end pe-4">{{ __('pos.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    @php
                        $statusClass = match(strtolower($supplier->status)) {
                            'active' => 'status-active',
                            'inactive' => 'status-inactive',
                            'blocked' => 'status-blocked',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <tr>
                        <td class="ps-4"><span class="text-muted small fw-semibold">{{ $supplier->supplier_number ?? '#'.$supplier->id }}</span></td>
                        <td>
                            <a href="{{ route('suppliers.show', $supplier->id) }}" class="fw-bold text-decoration-none text-dark d-block">
                                {{ $supplier->name }}
                            </a>
                        </td>
                        <td>
                            <span class="d-block">{{ $supplier->contact_person ?: '-' }}</span>
                            @if($supplier->email) <small class="text-muted">{{ $supplier->email }}</small> @endif
                        </td>
                        <td><i class="bi bi-telephone text-muted me-1"></i>{{ $supplier->phone }}</td>
                        <td><span class="badge rounded-pill {{ $statusClass }} fw-normal px-3">{{ __('pos.'.strtolower($supplier->status ?? 'active')) ?? ucfirst($supplier->status ?? 'Active') }}</span></td>
                        <td>
                            <span class="badge rounded-pill" style="background: linear-gradient(135deg, #8b5cf6, #6366f1); padding: 0.4em 0.8em; box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);">
                                <i class="bi bi-cart me-1 fw-bold"></i> {{ $supplier->purchases_count ?? 0 }}
                            </span>
                        </td>
                        <td><span class="text-muted small">{{ $supplier->purchases_max_created_at ? \Carbon\Carbon::parse($supplier->purchases_max_created_at)->format('Y-m-d') : '-' }}</span></td>
                        <td class="text-end pe-4">
                            <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-sm rounded-pill px-3 me-1" style="background-color: #f8f9fa; border: 1px solid #e2e8f0; color: #0d6efd; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9';" onmouseout="this.style.background='#f8f9fa';" title="View Profile">
                                <i class="bi bi-person-lines-fill"></i>
                            </a>
                            @can('edit-suppliers')
                            <button type="button" class="btn btn-sm rounded-pill px-3" style="background-color: #f8f9fa; border: 1px solid #e2e8f0; color: #64748B; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9';" onmouseout="this.style.background='#f8f9fa';" onclick="editSupplier({{ $supplier->id }})" title="{{ __('pos.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @endcan

                            @can('delete-suppliers')
                            <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') ?? 'Are you sure you want to delete?' }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm rounded-pill px-3 ms-1" style="background-color: #fef2f2; border: 1px solid #fecaca; color: #ef4444; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2';" onmouseout="this.style.background='#fef2f2';" title="{{ __('pos.delete') ?? 'Delete' }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-buildings text-muted fs-1 d-block mb-3"></i>
                            <h5 class="text-muted">{{ app()->getLocale() == 'ar' ? 'لم يتم العثور على موردين' : 'No suppliers found' }}</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($suppliers->hasPages())
    <div class="d-flex justify-content-center">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>

{{-- Create Modal (Multi-section) --}}
<div class="modal fade pm-modal-premium" id="createSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="pm-modal-header-premium">
                    <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                    <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                    <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width: 100%;">
                        <div class="pm-modal-icon-premium" style="color: #6366f1;">
                            <i class="bi bi-building-add"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="pm-modal-title-premium">{{ __('pos.add_supplier') ?? 'Add Supplier' }}</h5>
                            <p class="pm-modal-sub-premium">{{ str_starts_with(app()->getLocale(), 'ar') ? 'إدارة الموردين' : 'Suppliers Management' }}</p>
                        </div>
                        <button type="button" class="pm-modal-close-premium border-0" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="pm-modal-body-premium">
                    
                    <!-- Section 1: Basic Information -->
                    <div class="mb-4">
                        <div class="pm-section-label"><i class="bi bi-info-circle-fill"></i> {{ __('pos.basic_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.supplier_name') ?? 'Supplier Name' }} (AR) <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" class="pm-form-control" value="{{ old('name_ar') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.supplier_name') ?? 'Supplier Name' }} (EN) <span class="text-danger">*</span></label>
                                <input type="text" name="name_en" class="pm-form-control" value="{{ old('name_en') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.contact_person') ?? 'Contact Person' }} (AR)</label>
                                <input type="text" name="contact_person_ar" class="pm-form-control" value="{{ old('contact_person_ar') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.contact_person') ?? 'Contact Person' }} (EN)</label>
                                <input type="text" name="contact_person_en" class="pm-form-control" value="{{ old('contact_person_en') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.email') ?? 'Email' }}</label>
                                <input type="email" name="email" class="pm-form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.phone') ?? 'Primary Phone' }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="pm-form-control" value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.alternative_phone') }}</label>
                                <input type="text" name="alternative_phone" class="pm-form-control" value="{{ old('alternative_phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.status') }} <span class="text-danger">*</span></label>
                                <select name="status" class="pm-form-control" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status', 'active') == $status ? 'selected' : '' }}>{{ __('pos.'.strtolower($status)) ?? ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Additional Information -->
                    <div class="mb-4">
                        <div class="pm-section-label"><i class="bi bi-journal-text"></i> {{ __('pos.additional_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.address_ar') }}</label>
                                <textarea name="address_ar" class="pm-form-control" rows="2">{{ old('address_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.address_en') }}</label>
                                <textarea name="address_en" class="pm-form-control" rows="2">{{ old('address_en') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="pm-form-label">{{ __('pos.notes') }}</label>
                                <textarea name="notes" class="pm-form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 3: System Information -->
                    <div class="mb-2">
                        <div class="pm-section-label"><i class="bi bi-shield-check"></i> {{ __('pos.system_info') }}</div>
                        <p class="small text-muted mb-0" style="font-size:0.8rem;"><i class="bi bi-info-circle me-1"></i> {{ app()->getLocale() == 'ar' ? 'سيتم إنشاء كود المورد وتاريخ الإنشاء تلقائياً عند الحفظ.' : 'Supplier Code and Created Date will be automatically generated upon saving.' }}</p>
                    </div>

                </div>
                <div class="pm-modal-footer-premium">
                    <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal"><i class="bi bi-x"></i> {{ __('pos.cancel') }}</button>
                    <button type="submit" class="pm-btn-save"><i class="bi bi-check-lg"></i> {{ __('pos.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade pm-modal-premium" id="editSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="editSupplierForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="pm-modal-header-premium">
                    <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                    <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                    <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width: 100%;">
                        <div class="pm-modal-icon-premium" style="color: #6366f1;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="pm-modal-title-premium">{{ __('pos.edit_supplier') ?? 'Edit Supplier' }}</h5>
                            <p class="pm-modal-sub-premium">{{ str_starts_with(app()->getLocale(), 'ar') ? 'إدارة الموردين' : 'Suppliers Management' }}</p>
                        </div>
                        <button type="button" class="pm-modal-close-premium border-0" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="pm-modal-body-premium">
                    
                    <div class="mb-4">
                        <div class="pm-section-label"><i class="bi bi-info-circle-fill"></i> {{ __('pos.basic_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.supplier_name') ?? 'Supplier Name' }} (AR) <span class="text-danger">*</span></label>
                                <input type="text" id="edit_name_ar" name="name_ar" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.supplier_name') ?? 'Supplier Name' }} (EN) <span class="text-danger">*</span></label>
                                <input type="text" id="edit_name_en" name="name_en" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.contact_person') ?? 'Contact Person' }} (AR)</label>
                                <input type="text" id="edit_contact_person_ar" name="contact_person_ar" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.contact_person') ?? 'Contact Person' }} (EN)</label>
                                <input type="text" id="edit_contact_person_en" name="contact_person_en" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.email') ?? 'Email' }}</label>
                                <input type="email" id="edit_email" name="email" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.phone') ?? 'Primary Phone' }} <span class="text-danger">*</span></label>
                                <input type="text" id="edit_phone" name="phone" class="pm-form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.alternative_phone') }}</label>
                                <input type="text" id="edit_alternative_phone" name="alternative_phone" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.status') }} <span class="text-danger">*</span></label>
                                <select id="edit_status" name="status" class="pm-form-control" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">{{ __('pos.'.strtolower($status)) ?? ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="pm-section-label"><i class="bi bi-journal-text"></i> {{ __('pos.additional_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.address_ar') }}</label>
                                <textarea id="edit_address_ar" name="address_ar" class="pm-form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.address_en') }}</label>
                                <textarea id="edit_address_en" name="address_en" class="pm-form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="pm-form-label">{{ __('pos.notes') }}</label>
                                <textarea id="edit_notes" name="notes" class="pm-form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="pm-section-label"><i class="bi bi-shield-check"></i> {{ __('pos.system_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pm-form-label text-muted">{{ __('pos.supplier_code') }}</label>
                                <input type="text" id="edit_supplier_number" class="pm-form-control" style="background-color: var(--bs-gray-200); cursor:not-allowed;" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label text-muted">{{ __('pos.created_at') }}</label>
                                <input type="text" id="edit_created_at" class="pm-form-control" style="background-color: var(--bs-gray-200); cursor:not-allowed;" readonly disabled>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="pm-modal-footer-premium">
                    <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal"><i class="bi bi-x"></i> {{ __('pos.cancel') }}</button>
                    <button type="submit" class="pm-btn-save"><i class="bi bi-check-lg"></i> {{ __('pos.update') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Modal is closed on redirect to allow viewing global error alerts clearly
    });

    function editSupplier(id) {
        $.getJSON("{{ url('suppliers') }}/" + id, function(data) {
            $('#edit_name_ar').val(data.name_ar);
            $('#edit_name_en').val(data.name_en);
            $('#edit_contact_person_ar').val(data.contact_person_ar);
            $('#edit_contact_person_en').val(data.contact_person_en);
            $('#edit_phone').val(data.phone);
            $('#edit_alternative_phone').val(data.alternative_phone);
            $('#edit_email').val(data.email);
            $('#edit_address_ar').val(data.address_ar);
            $('#edit_address_en').val(data.address_en);
            $('#edit_notes').val(data.notes);
            $('#edit_status').val(data.status);
            
            $('#edit_supplier_number').val(data.supplier_number || '#'+data.id);
            $('#edit_created_at').val(data.created_at);

            const url = "{{ url('suppliers') }}/" + id;
            $('#editSupplierForm').attr('action', url);

            new bootstrap.Modal(document.getElementById('editSupplierModal')).show();
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Failed to fetch supplier data:", textStatus, errorThrown);
            alert("Failed to load supplier details. Please try again.");
        });
    }
</script>
@endpush

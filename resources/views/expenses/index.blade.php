@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة المصروفات' : 'Manage Expenses')

@push('styles')
<style>
    /* Category Style Base (from Customers) */
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
    .saas-table th { background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important; color: #ffffff !important; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; border-bottom: none !important; white-space: nowrap; text-align: start; vertical-align: middle; }
    .saas-table td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #F8FAFC; font-size: 0.9rem; color: #0F172A; font-weight: 500; white-space: nowrap; text-align: start; }
    .saas-table tr:hover td { background: #F8FAFC; }
    .saas-table tr:last-child td { border-bottom: none; }
    
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

    .status-active { background-color: #d1e7dd; color: #0f5132; }
    .status-warning { background-color: #fff3cd; color: #664d03; }
    .status-danger { background-color: #f8d7da; color: #842029; }

    /* Dark Mode Overrides */
    html[data-app-theme="dark"] .kpi-card { background: #1e293b; border-color: #334155; }
    html[data-app-theme="dark"] .kpi-card h3, html[data-app-theme="dark"] .kpi-card .text-uppercase { color: #ffffff !important; }
    html[data-app-theme="dark"] .cat-toolbar { background: #0f172a; border-color: #334155; }
    html[data-app-theme="dark"] .saas-table-card { background: #1e293b; border-color: #334155; }
    html[data-app-theme="dark"] .saas-table td, html[data-app-theme="dark"] .saas-table td .text-dark, html[data-app-theme="dark"] .saas-table td .text-muted, html[data-app-theme="dark"] .saas-table td .small { color: #ffffff !important; border-bottom-color: #334155; }
    html[data-app-theme="dark"] .saas-table tr:hover td { background: #0f172a; }
    html[data-app-theme="dark"] .cat-search-input { background: #1e293b; color: #ffffff; border-color: #334155; }
    html[data-app-theme="dark"] .cat-select { background-color: #1e293b; color: #ffffff; border-color: #334155; }
    html[data-app-theme="dark"] .cat-toolbar-left .text-muted { color: #ffffff !important; }

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
    @media (max-width: 767.98px) {
        .cat-toolbar { flex-direction: column; align-items: stretch; padding: 12px; }
        .cat-toolbar-left, .cat-toolbar-right { flex-direction: column; align-items: stretch; width: 100%; flex-wrap: nowrap; }
        .cat-search-wrap { max-width: 100%; width: 100%; }
        .cat-add-btn { width: 100%; text-align: center; justify-content: center; }
        .saas-table-card { border-radius: 12px; }
    }
    
    /* DataTable customization for SaaS Table */
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 15px; padding: 0 1.5rem; }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { padding: 15px 1.5rem; }
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; outline: none; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #00C8FF; }
    html[data-app-theme="dark"] .dataTables_wrapper .dataTables_length, html[data-app-theme="dark"] .dataTables_wrapper .dataTables_info, html[data-app-theme="dark"] .dataTables_wrapper .dataTables_paginate { color: #fff; }

    /* Select2 overrides for premium modal */
    .select2-container--bootstrap-5 .select2-selection {
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 12px !important;
        min-height: 46px !important;
        background: #f8fafc !important;
        display: flex;
        align-items: center;
        box-shadow: none !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #6366f1 !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1) !important;
    }
    html[data-app-theme="dark"] .select2-container--bootstrap-5 .select2-selection {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #818cf8 !important;
        background: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(129,140,248,0.1) !important;
    }
    .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #0f172a !important;
        font-size: 0.875rem !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
    }
    html[data-app-theme="dark"] .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #ffffff !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    html[data-app-theme="dark"] .select2-container--bootstrap-5 .select2-dropdown {
        background: #1e293b;
        border-color: #334155;
    }
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 8px 16px;
        font-size: 0.875rem;
    }
    html[data-app-theme="dark"] .select2-container--bootstrap-5 .select2-results__option {
        color: #ffffff;
    }
    html[data-app-theme="dark"] .select2-container--bootstrap-5 .select2-results__option[aria-selected="true"] {
        background-color: rgba(255,255,255,0.05);
    }
    html[data-app-theme="dark"] .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
        color: #ffffff;
    }
html[data-app-theme="dark"] .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #ffffff !important;
    }

    /* Expense Page Dark Mode Font Color Fixes */
    .expense-kpi-value {
        color: #0F172A;
    }
    .expense-amount-text {
        color: #0F172A;
    }
    html[data-app-theme="dark"] .expense-kpi-value {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .expense-amount-text {
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="font-family: 'Inter', sans-serif;">

    <!-- Top Stats Cards -->
    <div class="top-stats-row">
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #F3F0FF; color: #6D5DFC; flex-shrink: 0;">
                <i class="bi bi-calendar-day fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ app()->getLocale() == 'ar' ? "مصروفات اليوم" : "Today's Expenses" }}</div>
                <h3 class="fw-bold m-0 expense-kpi-value" style="font-size: 1.35rem;">{{ str_replace('.00', '', number_format($stats['today'], 2)) }} <span style="font-size:0.8rem; color:#64748B;">{{ $setting->currency }}</span></h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #DCFCE7; color: #16A34A; flex-shrink: 0;">
                <i class="bi bi-calendar-week fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ app()->getLocale() == 'ar' ? "مصروفات الأسبوع" : "This Week" }}</div>
                <h3 class="fw-bold m-0 expense-kpi-value" style="font-size: 1.35rem;">{{ str_replace('.00', '', number_format($stats['week'], 2)) }} <span style="font-size:0.8rem; color:#64748B;">{{ $setting->currency }}</span></h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #E0F2FE; color: #0284C7; flex-shrink: 0;">
                <i class="bi bi-calendar-month fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ app()->getLocale() == 'ar' ? "مصروفات الشهر" : "This Month" }}</div>
                <h3 class="fw-bold m-0 expense-kpi-value" style="font-size: 1.35rem;">{{ str_replace('.00', '', number_format($stats['month'], 2)) }} <span style="font-size:0.8rem; color:#64748B;">{{ $setting->currency }}</span></h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #FFFBEB; color: #D97706; flex-shrink: 0;">
                <i class="bi bi-calendar-year fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ app()->getLocale() == 'ar' ? "مصروفات العام" : "This Year" }}</div>
                <h3 class="fw-bold m-0 expense-kpi-value" style="font-size: 1.35rem;">{{ str_replace('.00', '', number_format($stats['year'], 2)) }} <span style="font-size:0.8rem; color:#64748B;">{{ $setting->currency }}</span></h3>
            </div>
        </div>
    </div>

    <!-- Toolbar (Category Style) -->
    <div class="cat-toolbar">
        <div class="cat-toolbar-left">
            <div class="cat-search-wrap">
                <i class="bi bi-search cat-search-icon"></i>
                <input type="text" id="customSearchInput" class="cat-search-input" placeholder="{{ app()->getLocale() == 'ar' ? 'البحث بالرقم، النوع، الملاحظات...' : 'Search by No, Type, Notes...' }}">
            </div>
            @can('create-expenses')
            <button type="button" class="cat-add-btn" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
                <i class="bi bi-plus-lg"></i> {{ __('pos.add') }}
            </button>
            @endcan

            <button type="button" class="cat-select d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#manageExpenseTypesModal" style="width: auto; gap: 8px; font-weight: 500;">
                <i class="bi bi-gear" style="color: #64748B;"></i> <span>{{ app()->getLocale() == 'ar' ? 'الأنواع' : 'Types' }}</span>
            </button>
        </div>
        <div class="cat-toolbar-right">
            <select id="customStatusFilter" class="cat-select">
                <option value="">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</option>
                <option value="Approved">{{ app()->getLocale() == 'ar' ? 'معتمد' : 'Approved' }}</option>
                <option value="Draft">{{ app()->getLocale() == 'ar' ? 'مسودة' : 'Draft' }}</option>
                <option value="Cancelled">{{ app()->getLocale() == 'ar' ? 'ملغي' : 'Cancelled' }}</option>
            </select>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="saas-table-card mb-4">
        <div class="table-responsive pt-3">
            <table id="expensesTable" class="saas-table w-100">
                <thead>
                    <tr>
                        <th class="ps-4">{{ app()->getLocale() == 'ar' ? 'رقم المصروف' : 'Expense No' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.expense_type') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.amount') }} ({{ $setting->currency }}) <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ app()->getLocale() == 'ar' ? 'طريقة الدفع' : 'Payment Method' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.expense_date') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.status') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ __('pos.created_by') }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th class="text-end pe-4">{{ __('pos.settings') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    @php
                        $typeName = $expense->type;
                        $typeObj = \App\Models\ExpenseType::where('name_en', $expense->type)->orWhere('name_ar', $expense->type)->first();
                        if ($typeObj) {
                            $typeName = app()->getLocale() == 'ar' ? $typeObj->name_ar : $typeObj->name_en;
                        }

                        $statusClass = match($expense->status) {
                            'Approved' => 'status-active',
                            'Draft' => 'status-warning',
                            'Cancelled' => 'status-danger',
                            default => 'bg-secondary',
                        };

                        $statusLabel = $expense->status;
                        if(app()->getLocale() == 'ar') {
                            $statusLabel = $statusLabel == 'Approved' ? 'معتمد' : ($statusLabel == 'Draft' ? 'مسودة' : 'ملغي');
                        }
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="text-primary fw-bold" style="font-size: 0.85rem;">{{ $expense->expense_number }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $typeName }}</div>
                            <small class="text-muted d-block text-truncate" style="max-width: 150px;">
                                {{ app()->getLocale() == 'ar' ? $expense->description_ar : $expense->description_en }}
                            </small>
                        </td>
                        <td>
                            <span class="fw-bold expense-amount-text" style="font-size: 1.05rem;">{{ str_replace('.00', '', number_format($expense->amount, 2)) }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($expense->payment_method == 'Cash')
                                    <i class="bi bi-cash text-success me-2 fs-5"></i>
                                @elseif($expense->payment_method == 'Card')
                                    <i class="bi bi-credit-card text-primary me-2 fs-5"></i>
                                @elseif($expense->payment_method == 'Bank Transfer')
                                    <i class="bi bi-bank text-info me-2 fs-5"></i>
                                @else
                                    <i class="bi bi-wallet2 text-secondary me-2 fs-5"></i>
                                @endif
                                <span class="small fw-semibold">
                                    @php
                                        $pm = $expense->payment_method;
                                        if (app()->getLocale() == 'ar') {
                                            $pmTranslations = [
                                                'cash' => 'نقدي',
                                                'card' => 'بطاقة',
                                                'bank transfer' => 'تحويل بنكي',
                                                'mobile payment' => 'دفع عبر الهاتف',
                                            ];
                                            $pm = $pmTranslations[strtolower($pm)] ?? $pm;
                                        }
                                    @endphp
                                    {{ $pm }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted small"><i class="bi bi-calendar me-1"></i> {{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</span>
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $statusClass }} fw-normal px-3 py-1">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person text-muted me-2"></i>
                                <span class="small">{{ $expense->user->full_name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            @if($expense->attachment)
                            <a href="{{ $expense->attachment_url }}" target="_blank" class="btn btn-sm rounded-pill px-3 me-1" style="background-color: #f8f9fa; border: 1px solid #e2e8f0; color: #0dcaf0; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9';" onmouseout="this.style.background='#f8f9fa';" title="{{ app()->getLocale() == 'ar' ? 'عرض المرفق' : 'View Attachment' }}">
                                <i class="bi bi-paperclip"></i>
                            </a>
                            @endif
                            @can('edit-expenses')
                            <button type="button" class="btn btn-sm rounded-pill px-3 me-1" style="background-color: #f8f9fa; border: 1px solid #e2e8f0; color: #3b82f6; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9';" onmouseout="this.style.background='#f8f9fa';" onclick="editExpense({{ $expense->id }})" title="{{ __('pos.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @endcan

                            @can('delete-expenses')
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm rounded-pill px-3" style="background-color: #fef2f2; border: 1px solid #fecaca; color: #ef4444; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2';" onmouseout="this.style.background='#fef2f2';" title="{{ __('pos.delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@php
    $expenseTypes = \App\Models\ExpenseType::all();
@endphp

{{-- Create Modal (Premium Style) --}}
<div class="modal fade pm-modal-premium" id="createExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="pm-modal-header-premium">
                    <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                    <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                    <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width: 100%;">
                        <div class="pm-modal-icon-premium" style="color: #6366f1;">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="pm-modal-title-premium">{{ app()->getLocale() == 'ar' ? 'إضافة مصروف جديد' : 'Add New Expense' }}</h5>
                            <p class="pm-modal-sub-premium">{{ str_starts_with(app()->getLocale(), 'ar') ? 'إدارة المصروفات' : 'Expenses Management' }}</p>
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
                                <label class="pm-form-label">{{ __('pos.expense_type') }} <span class="text-danger">*</span></label>
                                <select name="type" class="pm-form-control" required>
                                    <option value="">{{ __('pos.select') }}</option>
                                    @foreach($expenseTypes as $type)
                                        <option value="{{ $type->name_en }}">{{ app()->getLocale() == 'ar' ? $type->name_ar : $type->name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.amount') }} ({{ $setting->currency ?? 'SAR' }}) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="amount" class="pm-form-control" required placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.expense_date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="pm-form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'طريقة الدفع' : 'Payment Method' }} <span class="text-danger">*</span></label>
                                <select name="payment_method" class="pm-form-control" required>
                                    <option value="Cash">{{ app()->getLocale() == 'ar' ? 'نقدي (Cash)' : 'Cash' }}</option>
                                    <option value="Card">{{ app()->getLocale() == 'ar' ? 'بطاقة ائتمان (Card)' : 'Card' }}</option>
                                    <option value="Bank Transfer">{{ app()->getLocale() == 'ar' ? 'تحويل بنكي (Bank Transfer)' : 'Bank Transfer' }}</option>
                                    <option value="Mobile Payment">{{ app()->getLocale() == 'ar' ? 'دفع عبر الهاتف (Mobile)' : 'Mobile Payment' }}</option>
                                    <option value="Other">{{ app()->getLocale() == 'ar' ? 'أخرى (Other)' : 'Other' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="pm-section-label"><i class="bi bi-journal-text"></i> {{ __('pos.additional_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الوصف (عربي)' : 'Description (Arabic)' }}</label>
                                <textarea name="description_ar" class="pm-form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الوصف (إنجليزي)' : 'Description (English)' }}</label>
                                <textarea name="description_en" class="pm-form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="pm-section-label"><i class="bi bi-shield-check"></i> {{ __('pos.system_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.status') }} <span class="text-danger">*</span></label>
                                <select name="status" class="pm-form-control" required>
                                    <option value="Approved" selected>{{ app()->getLocale() == 'ar' ? 'معتمد' : 'Approved' }}</option>
                                    <option value="Draft">{{ app()->getLocale() == 'ar' ? 'مسودة' : 'Draft' }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'المرفقات' : 'Attachment' }}</label>
                                <div class="position-relative">
                                    <input type="file" name="attachment" id="create_attachment" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer" accept=".jpg,.jpeg,.png,.pdf" onchange="document.getElementById('create_attachment_text').innerText = this.files[0] ? this.files[0].name : '{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}'">
                                    <div class="pm-form-control d-flex align-items-center justify-content-between cursor-pointer" style="padding: 11px 16px;">
                                        <span id="create_attachment_text" class="text-muted text-truncate" style="font-size: 0.88rem; max-width: 60%;">{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}</span>
                                        <span class="btn btn-sm btn-light border px-2 py-1" style="font-size: 0.8rem; font-weight: 600;">{{ app()->getLocale() == 'ar' ? 'اختر ملف' : 'Choose File' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
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
<div class="modal fade pm-modal-premium" id="editExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="editExpenseForm" method="POST" enctype="multipart/form-data">
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
                            <h5 class="pm-modal-title-premium">{{ app()->getLocale() == 'ar' ? 'تعديل المصروف' : 'Edit Expense' }}</h5>
                            <p class="pm-modal-sub-premium">{{ str_starts_with(app()->getLocale(), 'ar') ? 'إدارة المصروفات' : 'Expenses Management' }}</p>
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
                                <label class="pm-form-label">{{ __('pos.expense_type') }} <span class="text-danger">*</span></label>
                                <select id="edit_type" name="type" class="pm-form-control" required>
                                    <option value="">{{ __('pos.select') }}</option>
                                    @foreach($expenseTypes as $type)
                                        <option value="{{ $type->name_en }}">{{ app()->getLocale() == 'ar' ? $type->name_ar : $type->name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.amount') }} ({{ $setting->currency ?? 'SAR' }}) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" id="edit_amount" name="amount" class="pm-form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.expense_date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="edit_expense_date" name="expense_date" class="pm-form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'طريقة الدفع' : 'Payment Method' }} <span class="text-danger">*</span></label>
                                <select id="edit_payment_method" name="payment_method" class="pm-form-control" required>
                                    <option value="Cash">{{ app()->getLocale() == 'ar' ? 'نقدي (Cash)' : 'Cash' }}</option>
                                    <option value="Card">{{ app()->getLocale() == 'ar' ? 'بطاقة ائتمان (Card)' : 'Card' }}</option>
                                    <option value="Bank Transfer">{{ app()->getLocale() == 'ar' ? 'تحويل بنكي (Bank Transfer)' : 'Bank Transfer' }}</option>
                                    <option value="Mobile Payment">{{ app()->getLocale() == 'ar' ? 'دفع عبر الهاتف (Mobile)' : 'Mobile Payment' }}</option>
                                    <option value="Other">{{ app()->getLocale() == 'ar' ? 'أخرى (Other)' : 'Other' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="pm-section-label"><i class="bi bi-journal-text"></i> {{ __('pos.additional_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الوصف (عربي)' : 'Description (Arabic)' }}</label>
                                <textarea id="edit_description_ar" name="description_ar" class="pm-form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الوصف (إنجليزي)' : 'Description (English)' }}</label>
                                <textarea id="edit_description_en" name="description_en" class="pm-form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="pm-section-label"><i class="bi bi-shield-check"></i> {{ __('pos.system_info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="pm-form-label">{{ __('pos.status') }} <span class="text-danger">*</span></label>
                                <select id="edit_status" name="status" class="pm-form-control" required>
                                    <option value="Approved">{{ app()->getLocale() == 'ar' ? 'معتمد' : 'Approved' }}</option>
                                    <option value="Draft">{{ app()->getLocale() == 'ar' ? 'مسودة' : 'Draft' }}</option>
                                    <option value="Cancelled">{{ app()->getLocale() == 'ar' ? 'ملغي' : 'Cancelled' }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'المرفقات' : 'Attachment' }}</label>
                                <div class="position-relative">
                                    <input type="file" name="attachment" id="edit_attachment" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer" accept=".jpg,.jpeg,.png,.pdf" onchange="document.getElementById('edit_attachment_text').innerText = this.files[0] ? this.files[0].name : '{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}'">
                                    <div class="pm-form-control d-flex align-items-center justify-content-between cursor-pointer" style="padding: 11px 16px;">
                                        <span id="edit_attachment_text" class="text-muted text-truncate" style="font-size: 0.88rem; max-width: 60%;">{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}</span>
                                        <span class="btn btn-sm btn-light border px-2 py-1" style="font-size: 0.8rem; font-weight: 600;">{{ app()->getLocale() == 'ar' ? 'اختر ملف' : 'Choose File' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="pm-form-label text-muted">{{ app()->getLocale() == 'ar' ? 'رقم المصروف' : 'Expense No.' }}</label>
                                <input type="text" id="edit_expense_number" class="pm-form-control" style="background-color: var(--bs-gray-200); cursor:not-allowed;" readonly disabled>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="pm-modal-footer-premium">
                    <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal"><i class="bi bi-x"></i> {{ __('pos.cancel') }}</button>
                    <button type="submit" class="pm-btn-save"><i class="bi bi-check-lg"></i> {{ __('pos.save_changes') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Manage Expense Types Modal --}}
<div class="modal fade pm-modal-premium" id="manageExpenseTypesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="pm-modal-header-premium">
                <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width: 100%;">
                    <div class="pm-modal-icon-premium" style="color: #6366f1;">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium">{{ app()->getLocale() == 'ar' ? 'أنواع المصروفات' : 'Expense Types' }}</h5>
                        <p class="pm-modal-sub-premium">{{ app()->getLocale() == 'ar' ? 'إضافة أو حذف' : 'Add or Delete' }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium border-0" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="pm-modal-body-premium">
                
                <form id="addExpenseTypeForm" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col">
                            <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الاسم (عربي)' : 'Name (AR)' }}</label>
                            <input type="text" id="type_name_ar" class="pm-form-control">
                        </div>
                        <div class="col">
                            <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الاسم (إنجليزي)' : 'Name (EN)' }}</label>
                            <input type="text" id="type_name_en" class="pm-form-control">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="cat-add-btn" style="padding: 11px 16px;">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover border">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'عربي' : 'AR' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'إنجليزي' : 'EN' }}</th>
                                <th class="text-end">{{ __('pos.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="expenseTypesTableBody">
                            @foreach($expenseTypes as $type)
                            <tr id="type-row-{{ $type->id }}">
                                <td>{{ $type->name_ar }}</td>
                                <td>{{ $type->name_en }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-danger rounded-circle" onclick="deleteExpenseType({{ $type->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#expensesTable').DataTable({
            language: {
                @if(app()->getLocale() == 'ar')
                    search: "البحث:",
                    lengthMenu: "عرض _MENU_ مصروفات",
                    info: "صفحة _PAGE_ من _PAGES_",
                    infoEmpty: "صفحة 0 من 0",
                    infoFiltered: "(تصفية من مجموع _MAX_ مصروف)",
                    zeroRecords: "لم يتم العثور على أية مصروفات",
                    emptyTable: "لا توجد مصروفات متاحة في الجدول",
                    paginate: {
                        first: "الأول",
                        previous: "السابق",
                        next: "التالي",
                        last: "الأخير"
                    }
                @else
                    search: "Search:",
                    lengthMenu: "Show _MENU_ expenses",
                    info: "Page _PAGE_ of _PAGES_",
                    infoEmpty: "Page 0 of 0",
                    infoFiltered: "(filtered from _MAX_ total expenses)",
                    zeroRecords: "No matching expenses found",
                    emptyTable: "No expenses available in table",
                    paginate: {
                        first: "First",
                        previous: "Previous",
                        next: "Next",
                        last: "Last"
                    }
                @endif
            },
            order: [[0, 'desc']], // Order by Expense Number descending
            pageLength: 25,
            dom: 'rt<"row align-items-center mt-3"<"col-md-6"i><"col-md-6"p>>', // Hidden default search & length
        });

        // Custom Search Input
        $('#customSearchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Custom Status Filter
        $('#customStatusFilter').on('change', function() {
            var val = $(this).find('option:selected').text();
            if(!$(this).val()) val = '';
            // Column 5 is Status
            table.column(5).search(val).draw();
        });

        $('.print-btn').on('click', function() {
            var id = $(this).data('id');
            var printWindow = window.open('/expenses/' + id + '/print', '_blank', 'width=800,height=600');
            printWindow.focus();
        });
        
        // Modal is closed on redirect to allow viewing global error alerts clearly

        // Initialize Select2 for modals
        $('#createExpenseModal select').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#createExpenseModal'),
            width: '100%',
            dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
        });

        $('#editExpenseModal select').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#editExpenseModal'),
            width: '100%',
            dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
        });
    });

    function editExpense(id) {
        $.getJSON("{{ url('daily-expenses') }}/" + id, function(data) {
            $('#edit_type').val(data.type).trigger('change');
            $('#edit_amount').val(data.amount);
            
            // Format date correctly for date input
            var expenseDate = data.expense_date;
            if(expenseDate && expenseDate.includes('T')) {
                expenseDate = expenseDate.split('T')[0];
            } else if (expenseDate && expenseDate.includes(' ')) {
                expenseDate = expenseDate.split(' ')[0];
            }
            $('#edit_expense_date').val(expenseDate);
            
            $('#edit_payment_method').val(data.payment_method).trigger('change');
            $('#edit_description_ar').val(data.description_ar);
            $('#edit_description_en').val(data.description_en);
            $('#edit_status').val(data.status).trigger('change');
            $('#edit_expense_number').val(data.expense_number);

            const url = "{{ url('daily-expenses') }}/" + id;
            $('#editExpenseForm').attr('action', url);

            new bootstrap.Modal(document.getElementById('editExpenseModal')).show();
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Failed to fetch expense data:", textStatus, errorThrown);
            alert("Failed to load expense details. Please try again.");
        });
    }

    // Expense Types Ajax Actions
    // Autofill visual mirroring
    $('#type_name_ar').on('keyup', function() {
        if(!$('#type_name_en').data('user-edited')) {
            $('#type_name_en').val($(this).val());
        }
    });
    $('#type_name_en').on('input', function() {
        $(this).data('user-edited', true);
        if($(this).val() === '') $(this).data('user-edited', false);
    });
    
    $('#type_name_en').on('keyup', function() {
        if(!$('#type_name_ar').data('user-edited')) {
            $('#type_name_ar').val($(this).val());
        }
    });
    $('#type_name_ar').on('input', function() {
        $(this).data('user-edited', true);
        if($(this).val() === '') $(this).data('user-edited', false);
    });

    $('#addExpenseTypeForm').on('submit', function(e) {
        e.preventDefault();
        var ar = $('#type_name_ar').val().trim();
        var en = $('#type_name_en').val().trim();
        
        if (!ar && !en) {
            alert('{{ app()->getLocale() == "ar" ? "يرجى إدخال اسم المصروف" : "Please enter the expense type name" }}');
            return;
        }
        
        if (!ar) ar = en;
        if (!en) en = ar;
        
        // Update inputs visually just in case
        $('#type_name_ar').val(ar);
        $('#type_name_en').val(en);
        
        $.ajax({
            url: "{{ route('expense-types.store') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name_ar: ar,
                name_en: en
            },
            success: function(response) {
                if(response.success) {
                    $('#type_name_ar').val('').data('user-edited', false);
                    $('#type_name_en').val('').data('user-edited', false);
                    // Append to table
                    $('#expenseTypesTableBody').append(`
                        <tr id="type-row-${response.id}">
                            <td>${ar}</td>
                            <td>${en}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-danger rounded-circle" onclick="deleteExpenseType(${response.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                    // Append to dropdowns
                    var optionHtml = `<option value="${en}">${"{{ app()->getLocale() }}" == "ar" ? ar : en}</option>`;
                    $('select[name="type"]').append(optionHtml);
                }
            },
            error: function() {
                alert("Error adding expense type.");
            }
        });
    });

    function deleteExpenseType(id) {
        if(confirm("{{ __('pos.confirm_delete') ?? 'Are you sure?' }}")) {
            $.ajax({
                url: "/expense-types/" + id,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.success) {
                        $('#type-row-' + id).remove();
                        // Ideally also remove from dropdowns, or just wait for next refresh
                        $('select[name="type"] option').each(function() {
                            if($(this).val() == response.name_en || $(this).text() == response.name_ar) {
                                $(this).remove();
                            }
                        });
                    }
                },
                error: function() {
                    alert("Error deleting expense type.");
                }
            });
        }
    }
</script>
@endpush

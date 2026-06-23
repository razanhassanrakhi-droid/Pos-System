@extends('layouts.app')

@section('title', __('pos.supplier') . ' - ' . $supplier->name)

@push('styles')
<style>
    .welcome-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px !important;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 10px 10px -5px rgba(15, 23, 42, 0.04) !important;
    }
    .welcome-title { letter-spacing: -0.5px; color: #ffffff !important; }
    .welcome-subtitle { color: #94a3b8 !important; }
    html[data-app-theme="dark"] .welcome-subtitle { color: #ffffff !important; }
    .hero-blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.15; z-index: 1; }
    .blob-1 { width: 150px; height: 150px; background: #3b82f6; top: -20px; right: 10%; }
    .blob-2 { width: 200px; height: 200px; background: #10b981; bottom: -50px; left: 20%; }
    .z-index-2 { z-index: 2; }
    .status-active { background-color: #d1e7dd; color: #0f5132; }
    .status-inactive { background-color: #e2e3e5; color: #41464b; }
    .status-blocked { background-color: #f8d7da; color: #842029; }
    
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
    
    .saas-table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }
    .saas-table { width: 100%; border-collapse: collapse; }
    .saas-table th { background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important; color: #ffffff !important; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; border-bottom: none !important; white-space: nowrap; text-align: left; vertical-align: middle; }
    .saas-table td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #F8FAFC; font-size: 0.9rem; color: #0F172A; font-weight: 500; white-space: nowrap; text-align: left; }
    .saas-table tr:hover td { background: #F8FAFC; }
    .saas-table tr:last-child td { border-bottom: none; }

    /* Dark Mode Overrides for Profile Page */
    html[data-app-theme="dark"] .kpi-card {
        background: #1e293b;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .kpi-card h3 {
        color: #f8fafc !important;
    }
    html[data-app-theme="dark"] .kpi-card .text-uppercase {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .saas-table-card {
        background: #1e293b;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .saas-table td {
        color: #f8fafc;
        border-bottom-color: #334155;
    }
    html[data-app-theme="dark"] .saas-table tr:hover td {
        background: #0f172a;
    }
    html[data-app-theme="dark"] .saas-table .text-muted {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] #historyTabs .nav-link {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] #historyTabs .nav-link.active {
        color: #f8fafc !important;
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
    html[data-app-theme="dark"] a.text-muted {
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
        color: #ffffff;
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
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="font-family: 'Inter', sans-serif;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('suppliers.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i> {{ __('pos.back') ?? 'Back' }}
            </a>
        </div>
        <div>
            <button class="btn btn-primary rounded-pill shadow-sm" onclick="editSupplier({{ $supplier->id }})">
                <i class="bi bi-pencil-square me-1"></i> {{ __('pos.edit_supplier') ?? 'Edit' }}
            </button>
        </div>
    </div>

    <!-- Supplier Header Card -->
    <div class="welcome-hero-card py-4 px-4 position-relative overflow-hidden mb-4">
        <div class="position-relative z-index-2 row align-items-center g-4">
            <div class="col-auto">
                <div class="bg-white text-primary fw-bold fs-1 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                    {{ mb_substr($supplier->name, 0, 1) }}
                </div>
            </div>
            <div class="col">
                @php
                    $statusClass = match($supplier->status) {
                        'active' => 'status-active',
                        'inactive' => 'status-inactive',
                        'blocked' => 'status-blocked',
                        default => 'bg-secondary',
                    };
                @endphp
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h4 class="fw-bold mb-0 welcome-title fs-3">{{ $supplier->name }}</h4>
                    <span class="badge rounded-pill {{ $statusClass }} fw-normal">{{ __('pos.'.strtolower($supplier->status)) ?? $supplier->status }}</span>
                </div>
                <div class="d-flex flex-wrap gap-4 welcome-subtitle small">
                    <span class="d-flex align-items-center"><i class="bi bi-hash me-1"></i> {{ $supplier->supplier_number ?? '#'.$supplier->id }}</span>
                    <span class="d-flex align-items-center"><i class="bi bi-telephone me-1"></i> {{ $supplier->phone }}</span>
                    @if($supplier->email) <span class="d-flex align-items-center"><i class="bi bi-envelope me-1"></i> {{ $supplier->email }}</span> @endif
                    @if($supplier->contact_person) <span class="d-flex align-items-center"><i class="bi bi-person me-1"></i> {{ $supplier->contact_person }}</span> @endif
                    @if($supplier->address) <span class="d-flex align-items-center"><i class="bi bi-geo-alt me-1"></i> {{ $supplier->address }}</span> @endif
                </div>
            </div>
            <div class="col-auto text-end border-start border-secondary ps-4 d-none d-md-block">
                <p class="welcome-subtitle small mb-1">{{ __('pos.outstanding_balance') ?? 'Outstanding' }}</p>
                <h4 class="fw-bold text-white mb-0">{{ \App\Helpers\FormatHelper::money($outstandingBalance) }} <span class="fs-6 welcome-subtitle">{{ \App\Models\Setting::first()->currency ?? 'SAR' }}</span></h4>
            </div>
        </div>
        <!-- Decorative light blobs -->
        <div class="hero-blob blob-1"></div>
        <div class="hero-blob blob-2"></div>
    </div>

    <!-- Statistics Cards -->
    <div class="top-stats-row mb-5">
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #F3F0FF; color: #6D5DFC; flex-shrink: 0;">
                <i class="bi bi-cart-check-fill fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.total_purchases') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ number_format($totalPurchases) }}</h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #DCFCE7; color: #16A34A; flex-shrink: 0;">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.total_purchase_value') ?? 'Total Value' }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ \App\Helpers\FormatHelper::money($totalPurchaseValue) }} <span class="fs-6 text-muted fw-normal">{{ \App\Models\Setting::first()->currency ?? 'SAR' }}</span></h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #E0F2FE; color: #0284C7; flex-shrink: 0;">
                <i class="bi bi-graph-up-arrow fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.average_purchase') ?? 'Average Order' }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $totalPurchases > 0 ? \App\Helpers\FormatHelper::money($totalPurchaseValue / $totalPurchases) : '0' }} <span class="fs-6 text-muted fw-normal">{{ \App\Models\Setting::first()->currency ?? 'SAR' }}</span></h3>
            </div>
        </div>
        <div class="kpi-card">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #FFEDD5; color: #EA580C; flex-shrink: 0;">
                <i class="bi bi-calendar-check-fill fs-4"></i>
            </div>
            <div>
                <div class="small fw-bold text-uppercase" style="color: #64748B; font-size: 0.8rem; letter-spacing: 0.05em; margin-bottom: 0.35rem;">{{ __('pos.last_purchase') }}</div>
                <h3 class="fw-bold m-0" style="color: #0F172A; font-size: 1.35rem;">{{ $lastPurchaseDate ? \Carbon\Carbon::parse($lastPurchaseDate)->format('Y-m-d') : '-' }}</h3>
            </div>
        </div>
    </div>

    <!-- History Tabs -->
    <ul class="nav nav-tabs border-bottom-0 mb-3" id="historyTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-dark border-0 border-bottom border-primary border-3 bg-transparent" id="purchases-tab" data-bs-toggle="tab" data-bs-target="#purchases" type="button" role="tab"><i class="bi bi-receipt me-2"></i>{{ __('pos.purchase_history') }}</button>
        </li>
    </ul>

    <div class="tab-content" id="historyTabsContent">
        <!-- Purchase History -->
        <div class="tab-pane fade show active" id="purchases" role="tabpanel">
            <div class="saas-table-card">
                <div class="table-responsive">
                    <table class="saas-table">
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('pos.invoice_number') }}</th>
                                <th>{{ __('pos.date') }}</th>
                                <th>{{ __('pos.total') }}</th>
                                <th>{{ __('pos.paid') }}</th>
                                <th>{{ __('pos.remaining') }}</th>
                                <th class="text-end pe-4">{{ __('pos.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                            <tr>
                                <td class="ps-4"><span class="fw-bold text-primary">#{{ $purchase->invoice_number ?? $purchase->id }}</span></td>
                                <td><span class="text-muted">{{ $purchase->created_at->format('Y-m-d H:i') }}</span></td>
                                <td class="fw-bold">{{ \App\Helpers\FormatHelper::money($purchase->total_amount) }}</td>
                                <td><span class="text-success">{{ \App\Helpers\FormatHelper::money($purchase->paid_amount) }}</span></td>
                                <td><span class="text-danger">{{ \App\Helpers\FormatHelper::money($purchase->remaining_amount) }}</span></td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-light rounded-pill shadow-sm"><i class="bi bi-eye" style="color: #6D5DFC;"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i> No purchases found</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal for Profile --}}
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
                            <h5 class="pm-modal-title-premium">{{ __('pos.edit_supplier') }}</h5>
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
                                <label class="pm-form-label">{{ __('pos.supplier_name') }} (AR) <span class="text-danger">*</span></label>
                                <input type="text" id="edit_name_ar" name="name_ar" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.supplier_name') }} (EN) <span class="text-danger">*</span></label>
                                <input type="text" id="edit_name_en" name="name_en" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.contact_person') }} (AR)</label>
                                <input type="text" id="edit_contact_person_ar" name="contact_person_ar" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.contact_person') }} (EN)</label>
                                <input type="text" id="edit_contact_person_en" name="contact_person_en" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.email') }}</label>
                                <input type="email" id="edit_email" name="email" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.phone') }} <span class="text-danger">*</span></label>
                                <input type="text" id="edit_phone" name="phone" class="pm-form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.alternative_phone') }}</label>
                                <input type="text" id="edit_alternative_phone" name="alternative_phone" class="pm-form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="pm-form-label">{{ __('pos.status') }}</label>
                                <select id="edit_status" name="status" class="pm-form-control">
                                    <option value="active">{{ __('pos.active') ?? 'Active' }}</option>
                                    <option value="inactive">{{ __('pos.inactive') ?? 'Inactive' }}</option>
                                    <option value="blocked">{{ __('pos.blocked') ?? 'Blocked' }}</option>
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

            $('#edit_supplier_number').val(data.supplier_number);
            $('#edit_created_at').val(data.created_at);

            const url = "{{ url('suppliers') }}/" + id;
            $('#editSupplierForm').attr('action', url);

            new bootstrap.Modal(document.getElementById('editSupplierModal')).show();
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Failed to fetch supplier data:", textStatus, errorThrown);
            alert("Failed to load supplier details. Please try again.");
        });
    }

    // Toggle active tab style
    $('#historyTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('#historyTabs button').removeClass('text-dark border-bottom border-primary border-3').addClass('text-muted border-0');
        $(e.target).removeClass('text-muted border-0').addClass('text-dark border-bottom border-primary border-3');
    });
</script>
@endpush

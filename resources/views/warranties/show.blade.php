@extends('layouts.app')

@section('title', __('pos.warranty_title') . ': ' . $warranty->warranty_number)

@push('styles')
<style>
    /* Category Style Base */
    .kpi-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.08);
    }
    
    /* Layout Cards Matching Screenshot */
    .warranty-general-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }
    
    .saas-table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }
    .saas-table { width: 100%; border-collapse: collapse; }
    .saas-table th { background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important; color: #ffffff !important; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; border-bottom: none !important; white-space: nowrap; text-align: center; vertical-align: middle; }
    .saas-table td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #F8FAFC; font-size: 0.9rem; color: #0F172A; font-weight: 500; white-space: nowrap; text-align: center; }
    .saas-table tr:hover td { background: #F8FAFC; }
    .saas-table tr:last-child td { border-bottom: none; }
    
    .cat-select, .cat-search-input, .cat-input, textarea.cat-input {
        border: 1.5px solid #e2e8f0;
        border-radius: 11px;
        padding: 9px 14px;
        font-size: 0.875rem;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        transition: all 0.2s;
        width: 100%;
    }
    .cat-select:focus, .cat-search-input:focus, .cat-input:focus { border-color: #00C8FF; box-shadow: 0 0 0 3px rgba(0,200,255,0.1); }
    
    .cat-btn-apply {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #fff !important;
        border: none;
        border-radius: 11px;
        padding: 10px 20px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);
    }
    .cat-btn-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.35); }

    .cat-add-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff !important;
        border: none;
        border-radius: 11px;
        padding: 8px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .cat-add-btn:hover { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: #fff; }
    
    /* Premium Header */
    .pm-modal-header-premium {
        background: linear-gradient(135deg, #060d1f 0%, #0f172a 60%, #060d1f 100%) !important;
        padding: 22px 28px !important;
        position: relative; overflow: hidden; border-bottom: none !important;
        border-radius: 20px 20px 0 0;
    }
    .pm-modal-header-premium::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.045) 1px, transparent 1px);
        background-size: 22px 22px; pointer-events: none;
    }
    .pm-modal-header-glow { position: absolute; border-radius: 50%; filter: blur(60px); pointer-events: none; }
    .pm-modal-header-glow-1 { width: 220px; height: 220px; background: rgba(124,58,237,.25) !important; top: -80px; right: -60px; }
    .pm-modal-header-glow-2 { width: 160px; height: 160px; background: rgba(99,102,241,.18) !important; bottom: -60px; left: -40px; }
    .pm-modal-icon-premium {
        width: 48px; height: 48px; border-radius: 14px;
        background: rgba(255,255,255,.1); border: 1.5px solid rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; color: #c4b5fd !important; flex-shrink: 0; backdrop-filter: blur(8px);
    }
    .pm-modal-title-premium { font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0; letter-spacing: -.3px; }
    .pm-modal-sub-premium { font-size: .76rem; color: #a5b4fc !important; margin: 2px 0 0; font-weight: 500; }
    
    .pm-modal-close-premium {
        width: 34px; height: 34px; border-radius: 10px; background: rgba(255,255,255,.08);
        border: 1.5px solid rgba(255,255,255,.12); color: rgba(255,255,255,.7) !important;
        transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; text-decoration: none;
    }
    .pm-modal-close-premium:hover { background: rgba(255,255,255,.16); color: #fff !important; transform: scale(1.05); }

    /* Info rows inside cards */
    .info-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 13px 0; border-bottom: 1px solid #f1f5f9; gap: 16px;
    }
    .info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .info-label { font-size: 0.9rem; color: #0F172A; font-weight: 700; white-space: nowrap; }
    .info-value { font-size: 0.88rem; font-weight: 500; color: #4b5563; text-align: end; }

    /* Customer avatar styling */
    .customer-avatar {
        width: 120px; height: 120px; border-radius: 50%;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; color: #94a3b8; margin: 0 auto 1.5rem;
    }

    /* Modal styles */
    .pm-modal-premium .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .pm-modal-premium .modal-body {
        padding: 1.5rem;
    }
    
    .cat-btn-cancel {
        display: inline-flex; align-items: center; gap: 7px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 11px;
        padding: 10px 20px;
        font-size: .875rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
    }
    .cat-btn-cancel:hover { background: #f1f5f9; border-color: #cbd5e1; color: #0f172a; }

    /* RTL specific layout values */
    html[dir="rtl"] .text-end-rtl { text-align: left !important; }
    html[dir="rtl"] .ms-custom { margin-right: auto !important; margin-left: 0 !important; }

    /* Dark Mode Overrides */
    html[data-app-theme="dark"] .kpi-card, 
    html[data-app-theme="dark"] .warranty-general-card,
    html[data-app-theme="dark"] .saas-table-card { background: #1E293B; border-color: #334155; }
    html[data-app-theme="dark"] .info-row { border-color: #334155; }
    html[data-app-theme="dark"] .info-label { color: #f8fafc; }
    html[data-app-theme="dark"] .info-value { color: #94a3b8; }
    html[data-app-theme="dark"] .customer-avatar { background: #0f172a; color: #334155; }
    html[data-app-theme="dark"] .cat-select, 
    html[data-app-theme="dark"] .cat-search-input, 
    html[data-app-theme="dark"] .cat-input { background: #0f172a; color: #fff; border-color: #334155; }
    html[data-app-theme="dark"] .pm-modal-premium .modal-content { background: #1e293b; }
    html[data-app-theme="dark"] .text-muted { color: #94a3b8 !important; }
    html[data-app-theme="dark"] h5.fw-bold { color: #fff !important; }
</style>
@endpush

@section('content')
<div class="container-fluid mb-5">

    {{-- Top Back Nav Row --}}
    <div class="d-flex justify-content-end align-items-center mb-3">
        <a href="{{ session('warranties_index_url', route('warranties.index')) }}" class="btn btn-sm btn-light border" style="border-radius:10px; font-weight:600;">
            {{ __('pos.back_to_registry') }} ←
        </a>
    </div>

    {{-- 1. Hero Header Card matching the exact styling in screenshot --}}
    <div class="pm-card-premium mb-4" style="border-radius:20px; overflow:hidden;">
        <div class="pm-modal-header-premium py-4 px-4">
            <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
            <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
            <div class="row align-items-center position-relative w-100 g-3" style="z-index:2; margin:0;">
                <div class="col-auto">
                    <div class="pm-modal-icon-premium"><i class="bi bi-shield-check"></i></div>
                </div>
                <div class="col-md-5">
                    <h4 class="pm-modal-title-premium fs-5 fw-bold mb-1">
                        {{ __('pos.warranty_title') }}: {{ $warranty->warranty_number }}
                    </h4>
                    <p class="pm-modal-sub-premium mb-0">{{ __('pos.warranty_subtitle') }}</p>
                </div>
                
                @php
                    $calcStatus = $warranty->calculated_status;
                    $statusTranslations = [
                        'Active'          => __('pos.status_active'),
                        'Expiring Soon'   => __('pos.status_expiring_soon'),
                        'Expired'         => __('pos.status_expired'),
                        'Claim Submitted' => __('pos.status_claim_submitted'),
                        'Claim Approved'  => __('pos.status_claim_approved'),
                    ];
                    $badgeClass = match($calcStatus) {
                        'Active'           => 'bg-success-subtle text-success border border-success-subtle',
                        'Expiring Soon'    => 'bg-warning-subtle text-warning border border-warning-subtle',
                        'Expired'          => 'bg-danger-subtle text-danger border border-danger-subtle',
                        'Claim Submitted'  => 'bg-info-subtle text-info border border-info-subtle',
                        'Claim Approved'   => 'bg-primary-subtle text-primary border border-primary-subtle',
                        default            => 'bg-secondary-subtle text-secondary'
                    };
                @endphp
                <div class="col-auto">
                    <span class="badge rounded-pill px-3 py-1.5 fw-bold {{ $badgeClass }}" style="font-size:0.85rem;">
                        ● {{ $statusTranslations[$calcStatus] ?? $calcStatus }}
                    </span>
                </div>
                
                <div class="col-md-4 text-start ms-custom d-flex justify-content-end gap-4 text-white align-items-center">
                    <div class="border-end pe-4 text-end">
                        <small class="text-white-50 d-block" style="font-size: 0.75rem;">{{ __('pos.created_at') }}</small>
                        <strong class="fs-6 d-flex align-items-center gap-1">{{ $warranty->created_at->format('M d, Y') }} <i class="bi bi-calendar3 text-white-50" style="font-size:0.8rem;"></i></strong>
                    </div>
                    <div class="text-end">
                        <small class="text-white-50 d-block" style="font-size: 0.75rem;">{{ __('pos.invoice_number') }}</small>
                        <strong class="fs-6 d-flex align-items-center gap-1">{{ $warranty->sale->invoice_number ?? '-' }} <i class="bi bi-receipt text-white-50" style="font-size:0.8rem;"></i></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Left Column: General Info --}}
        <div class="col-md-7">
            <div class="warranty-general-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h5 class="fw-bold text-indigo mb-0 d-flex align-items-center gap-2">
                        <span class="p-1.5 bg-primary-subtle rounded-3 text-primary d-inline-flex"><i class="bi bi-info-circle"></i></span>
                        {{ __('pos.general_info') }}
                    </h5>
                    <button class="btn btn-sm btn-light border d-flex align-items-center gap-1" style="border-radius:8px;" data-bs-toggle="modal" data-bs-target="#editWarrantyModal">
                        <i class="bi bi-pencil small"></i> {{ __('pos.edit') }}
                    </button>
                </div>
                
                @php
                    $typeTranslations = [
                        'Manufacturer Warranty' => __('pos.manufacturer_warranty'),
                        'Store Warranty'        => __('pos.store_warranty'),
                        'Extended Warranty'     => __('pos.extended_warranty'),
                    ];
                @endphp
                <div class="info-row">
                    <span class="info-label">{{ __('pos.status') }}</span>
                    <span class="info-value">
                        <span class="badge rounded-pill px-2.5 py-1.5 fw-bold {{ $badgeClass }}">
                            {{ $statusTranslations[$calcStatus] ?? $calcStatus }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('pos.warranty_type_label') }}</span>
                    <span class="info-value">{{ $typeTranslations[$warranty->warranty_type] ?? ($warranty->warranty_type ?: __('pos.other')) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('pos.product_label') }}</span>
                    <span class="info-value text-indigo fw-bold">{{ $warranty->product->name ?? __('pos.not_available') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('pos.serial_number') }}</span>
                    <span class="info-value font-monospace">{{ $warranty->serial_number ?: __('pos.not_available') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('pos.validity_period_label') }}</span>
                    <span class="info-value">
                        <span class="fw-bold">{{ $warranty->warranty_start_date->format('d M, Y') }}</span> 
                        <span class="text-muted mx-1">{{ __('pos.to_label') }}</span>
                        <span class="fw-bold">{{ $warranty->warranty_end_date->format('d M, Y') }}</span>
                        <span class="text-muted small ms-1">({{ $warranty->warranty_period_months }} {{ __('pos.months_label') }} <i class="bi bi-calendar-check"></i>)</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('pos.linked_sale') }}</span>
                    <span class="info-value">
                        @if($warranty->sale)
                            <a href="{{ route('sales.show', $warranty->sale->id) }}" class="text-decoration-none fw-bold" style="color:#6366f1;">
                                {{ $warranty->sale->invoice_number }} <i class="bi bi-box-arrow-up-right small"></i>
                            </a>
                        @else
                            <span class="text-muted">{{ __('pos.not_available') }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Right Column: Customer Details --}}
        <div class="col-md-5">
            <div class="warranty-general-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h5 class="fw-bold text-indigo mb-0 d-flex align-items-center gap-2">
                        <span class="p-1.5 bg-success-subtle rounded-3 text-success d-inline-flex"><i class="bi bi-person"></i></span>
                        {{ __('pos.customer_details') }}
                    </h5>
                </div>
                
                @if($warranty->customer)
                    <div class="customer-avatar">
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="text-center mb-4 pb-2 border-bottom">
                        <h4 class="fw-bold text-indigo mb-1">{{ $warranty->customer->name }}</h4>
                        <span class="text-muted small">{{ __('pos.warranty_holder_info') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pos.phone') }}</span>
                        <span class="info-value font-monospace">{{ $warranty->customer->phone ?: __('pos.not_available') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pos.email') }}</span>
                        <span class="info-value">{{ $warranty->customer->email ?: __('pos.not_available') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pos.address') }}</span>
                        <span class="info-value">{{ $warranty->customer->address ?: __('pos.not_available') }}</span>
                    </div>
                @else
                    <div class="customer-avatar">
                        <i class="bi bi-person-x"></i>
                    </div>
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-muted mb-1">{{ __('pos.walk_in_label') }}</h4>
                        <span class="text-muted small">{{ __('pos.warranty_holder_info') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Claims & Actions Section --}}
    <div class="saas-table-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-indigo mb-0 d-flex align-items-center gap-2">
                <span class="p-1.5 bg-warning-subtle text-warning rounded-3 d-inline-flex"><i class="bi bi-tools"></i></span>
                {{ __('pos.warranty_claims') }}
            </h5>
            <button class="cat-btn-apply" data-bs-toggle="modal" data-bs-target="#newClaimModal">
                <i class="bi bi-plus-lg"></i> {{ __('pos.new_claim') }}
            </button>
        </div>

        @php
            $claims = $warranty->claims;
            $total = $claims->count();
            $pending = $claims->where('status', 'Pending')->count();
            $completed = $claims->where('status', 'Completed')->count();
            $rejected = $claims->where('status', 'Rejected')->count();
        @endphp

        {{-- Statistics Row matching the screenshot --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="p-3 border rounded-4 bg-light d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block">{{ __('pos.total_claims') }}</small>
                        <h4 class="fw-bold text-indigo mb-0 mt-1">{{ $total }}</h4>
                    </div>
                    <span class="p-2 rounded-3 bg-white text-indigo border"><i class="bi bi-file-earmark-text"></i></span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 border rounded-4 bg-success-subtle bg-opacity-25 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block">{{ __('pos.claim_completed') }}</small>
                        <h4 class="fw-bold text-success mb-0 mt-1">{{ $completed }}</h4>
                    </div>
                    <span class="p-2 rounded-3 bg-white text-success border"><i class="bi bi-check-circle"></i></span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 border rounded-4 bg-warning-subtle bg-opacity-25 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block">{{ __('pos.claim_pending') }}</small>
                        <h4 class="fw-bold text-warning mb-0 mt-1">{{ $pending }}</h4>
                    </div>
                    <span class="p-2 rounded-3 bg-white text-warning border"><i class="bi bi-clock"></i></span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 border rounded-4 bg-danger-subtle bg-opacity-25 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block">{{ __('pos.claim_rejected') }}</small>
                        <h4 class="fw-bold text-danger mb-0 mt-1">{{ $rejected }}</h4>
                    </div>
                    <span class="p-2 rounded-3 bg-white text-danger border"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>

        @if($total > 0)
            <div class="table-responsive">
                <table class="saas-table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('pos.claim_date') }}</th>
                            <th>{{ __('pos.issue_description') }}</th>
                            <th>{{ __('pos.status') }}</th>
                            <th>{{ __('pos.resolution') }}</th>
                            <th class="text-center">{{ __('pos.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($claims as $claim)
                            @php
                                $claimStatusMap = [
                                    'Pending'   => ['label' => __('pos.claim_pending'),   'class' => 'bg-warning-subtle text-warning border border-warning-subtle'],
                                    'Approved'  => ['label' => __('pos.claim_approved'),  'class' => 'bg-primary-subtle text-primary border border-primary-subtle'],
                                    'Rejected'  => ['label' => __('pos.claim_rejected'),  'class' => 'bg-danger-subtle text-danger border border-danger-subtle'],
                                    'Completed' => ['label' => __('pos.claim_completed'), 'class' => 'bg-success-subtle text-success border border-success-subtle'],
                                ];
                                $cs = $claimStatusMap[$claim->status] ?? ['label' => $claim->status, 'class' => 'bg-secondary-subtle text-secondary'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $claim->claim_date->format('d M, Y') }}</div>
                                    <small class="text-muted font-monospace">#CLM-{{ $claim->id }}</small>
                                </td>
                                <td>{{ Str::limit($claim->issue_description, 60) }}</td>
                                <td><span class="badge rounded-pill {{ $cs['class'] }} px-2.5 py-1.5">{{ $cs['label'] }}</span></td>
                                <td class="text-muted">{{ $claim->resolution ?: __('pos.pending_resolution') }}</td>
                                <td class="text-center">
                                    <button class="btn-action-edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editClaimModal{{ $claim->id }}" 
                                            title="{{ __('pos.edit') }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('warranties.claims.destroy', $claim->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-delete" title="{{ __('pos.delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Claim Modal --}}
                            <div class="modal fade pm-modal-premium" id="editClaimModal{{ $claim->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4 shadow">
                                        <form action="{{ route('warranties.claims.update', $claim->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="pm-modal-header-premium">
                                                <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                                                <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                                                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width:100%;">
                                                    <div class="pm-modal-icon-premium"><i class="bi bi-pencil-square"></i></div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="pm-modal-title-premium">{{ __('pos.update') }} {{ __('pos.status') }}</h5>
                                                        <p class="pm-modal-sub-premium">{{ __('pos.update') }} #CLM-{{ $claim->id }}</p>
                                                    </div>
                                                    <button type="button" class="pm-modal-close-premium ms-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.issue_description') }} (العربية)</label>
                                                    <textarea class="cat-input" id="edit_desc_ar_{{ $claim->id }}" name="issue_description[ar]" rows="2" required oninput="syncFields('edit_desc_ar_{{ $claim->id }}', 'edit_desc_en_{{ $claim->id }}')">{{ $claim->getTranslation('issue_description', 'ar') }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.issue_description') }} (English)</label>
                                                    <textarea class="cat-input" id="edit_desc_en_{{ $claim->id }}" name="issue_description[en]" rows="2" required oninput="syncFields('edit_desc_en_{{ $claim->id }}', 'edit_desc_ar_{{ $claim->id }}')">{{ $claim->getTranslation('issue_description', 'en') }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.status') }}</label>
                                                    <select name="status" class="cat-select">
                                                        <option value="Pending" {{ $claim->status == 'Pending' ? 'selected' : '' }}>{{ __('pos.claim_pending') }}</option>
                                                        <option value="Approved" {{ $claim->status == 'Approved' ? 'selected' : '' }}>{{ __('pos.claim_approved') }}</option>
                                                        <option value="Rejected" {{ $claim->status == 'Rejected' ? 'selected' : '' }}>{{ __('pos.claim_rejected') }}</option>
                                                        <option value="Completed" {{ $claim->status == 'Completed' ? 'selected' : '' }}>{{ __('pos.claim_completed') }}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.resolution') }} (العربية)</label>
                                                    <textarea class="cat-input" id="edit_res_ar_{{ $claim->id }}" name="resolution[ar]" rows="2" placeholder="{{ __('pos.describe_issue') }}..." oninput="syncFields('edit_res_ar_{{ $claim->id }}', 'edit_res_en_{{ $claim->id }}')">{{ $claim->getTranslation('resolution', 'ar') }}</textarea>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.resolution') }} (English)</label>
                                                    <textarea class="cat-input" id="edit_res_en_{{ $claim->id }}" name="resolution[en]" rows="2" placeholder="Describe the resolution..." oninput="syncFields('edit_res_en_{{ $claim->id }}', 'edit_res_ar_{{ $claim->id }}')">{{ $claim->getTranslation('resolution', 'en') }}</textarea>
                                                </div>
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <button type="button" class="cat-btn-cancel" data-bs-dismiss="modal">{{ __('pos.cancel') }}</button>
                                                    <button type="submit" class="cat-btn-apply"><i class="bi bi-check-circle me-1"></i> {{ __('pos.save_changes') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted bg-light rounded-4 border">
                <i class="bi bi-file-earmark-text d-block mb-3 text-primary-subtle" style="font-size:3rem;"></i>
                <h5 class="fw-bold text-indigo mb-1">{{ __('pos.no_claims_submitted') }}</h5>
                <p class="small text-muted mb-0">{{ __('pos.describe_issue') }}</p>
            </div>
        @endif
    </div>
</div>

{{-- New Claim Modal --}}
<div class="modal fade pm-modal-premium" id="newClaimModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('warranties.claims.store', $warranty->id) }}" method="POST">
                @csrf
                <div class="pm-modal-header-premium">
                    <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                    <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                    <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width:100%;">
                        <div class="pm-modal-icon-premium"><i class="bi bi-tools"></i></div>
                        <div class="flex-grow-1">
                            <h5 class="pm-modal-title-premium">{{ __('pos.submit_new_claim') }}</h5>
                            <p class="pm-modal-sub-premium">{{ __('pos.add_claim_for') }} {{ $warranty->warranty_number }}</p>
                        </div>
                        <button type="button" class="pm-modal-close-premium ms-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.claim_date') }}</label>
                        <input type="date" class="cat-input" name="claim_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.issue_description') }} (العربية)</label>
                        <textarea class="cat-input" id="new_desc_ar" name="issue_description[ar]" rows="2" required placeholder="{{ __('pos.describe_issue') }}" oninput="syncFields('new_desc_ar', 'new_desc_en')"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.issue_description') }} (English)</label>
                        <textarea class="cat-input" id="new_desc_en" name="issue_description[en]" rows="2" required placeholder="Describe the issue..." oninput="syncFields('new_desc_en', 'new_desc_ar')"></textarea>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="cat-btn-cancel" data-bs-dismiss="modal">{{ __('pos.cancel') }}</button>
                        <button type="submit" class="cat-btn-apply"><i class="bi bi-check-circle me-1"></i> {{ __('pos.submit_claim') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Warranty Modal --}}
<div class="modal fade pm-modal-premium" id="editWarrantyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('warranties.update', $warranty->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="pm-modal-header-premium">
                    <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                    <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                    <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width:100%;">
                        <div class="pm-modal-icon-premium"><i class="bi bi-pencil-square"></i></div>
                        <div class="flex-grow-1">
                            <h5 class="pm-modal-title-premium">{{ __('pos.edit_warranty_data') }}</h5>
                            <p class="pm-modal-sub-premium">{{ $warranty->warranty_number }}</p>
                        </div>
                        <button type="button" class="pm-modal-close-premium ms-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.warranty_type_label') }}</label>
                        <select name="warranty_type" class="cat-select">
                            <option value="">{{ __('pos.other') }}</option>
                            <option value="Manufacturer Warranty" {{ $warranty->warranty_type == 'Manufacturer Warranty' ? 'selected' : '' }}>{{ __('pos.manufacturer_warranty') }}</option>
                            <option value="Store Warranty" {{ $warranty->warranty_type == 'Store Warranty' ? 'selected' : '' }}>{{ __('pos.store_warranty') }}</option>
                            <option value="Extended Warranty" {{ $warranty->warranty_type == 'Extended Warranty' ? 'selected' : '' }}>{{ __('pos.extended_warranty') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">{{ __('pos.serial_number') }}</label>
                        <input type="text" class="cat-input" name="serial_number" value="{{ $warranty->serial_number }}" placeholder="{{ __('pos.enter_serial_placeholder') }}">
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="cat-btn-cancel" data-bs-dismiss="modal">{{ __('pos.cancel') }}</button>
                        <button type="submit" class="cat-btn-apply"><i class="bi bi-check-circle me-1"></i> {{ __('pos.save_changes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Keep track of which fields were manually edited by the user to avoid overwriting them
    const userEditedFields = {};

    function syncFields(srcId, destId) {
        const srcInput = document.getElementById(srcId);
        const destInput = document.getElementById(destId);
        
        if (!srcInput || !destInput) return;

        // Mark the source field as manually touched/edited by the user
        userEditedFields[srcId] = true;

        // Auto-fill the destination field only if it hasn't been manually edited by the user yet
        if (!userEditedFields[destId] || destInput.value.trim() === '') {
            destInput.value = srcInput.value;
        }
    }
</script>
@endsection

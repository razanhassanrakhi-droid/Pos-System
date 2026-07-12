@extends('layouts.app')

@section('title', __('purchases.purchase_history'))

@push('styles')
<style>
    .pm-add-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 12px 24px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.2);
        white-space: nowrap;
        font-family: inherit;
        text-decoration: none;
    }
    .pm-add-btn:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        transform: translateY(-2px);
        color: #fff;
    }
    .pm-add-btn:active { transform: translateY(0); }

    /* Custom Table & Form Styling */
    .table td, .table th {
        padding: 16px 12px !important;
    }
    .badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-block;
    }
    .badge-cash {
        background-color: rgba(16, 185, 129, 0.15) !important;
        color: #10b981 !important;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .badge-card {
        background-color: rgba(99, 102, 241, 0.15) !important;
        color: #6366f1 !important;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }
    .badge-transfer, .badge-bank_transfer {
        background-color: rgba(14, 165, 233, 0.15) !important;
        color: #0ea5e9 !important;
        border: 1px solid rgba(14, 165, 233, 0.3);
    }
    
    /* Dark Theme Custom Overrides */
    html[data-app-theme="dark"] .card {
        background-color: #1e293b !important;
        border: 1px solid #334155 !important;
    }
    html[data-app-theme="dark"] .card-header {
        background-color: #1e293b !important;
        border-bottom: 1px solid #334155 !important;
        color: #f8fafc !important;
    }
    html[data-app-theme="dark"] .table {
        color: #f8fafc !important;
    }
    html[data-app-theme="dark"] .table thead th {
        background-color: #0f172a !important;
        color: #94a3b8 !important;
        border-bottom: 1px solid #334155 !important;
    }
    html[data-app-theme="dark"] .table td {
        border-bottom: 1px solid #334155 !important;
    }
    html[data-app-theme="dark"] .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }
    html[data-app-theme="dark"] .form-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    html[data-app-theme="dark"] .form-control::placeholder {
        color: #64748b !important;
    }
    html[data-app-theme="dark"] .btn-outline-primary {
        color: #3b82f6 !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .btn-outline-primary:hover {
        background-color: #3b82f6 !important;
        color: #fff !important;
    }
    html[data-app-theme="dark"] .btn-outline-secondary {
        color: #94a3b8 !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .btn-outline-secondary:hover {
        background-color: #475569 !important;
        color: #fff !important;
    }
    html[data-app-theme="dark"] .card-footer {
        background-color: #1e293b !important;
        border-top: 1px solid #334155 !important;
    }
    
    /* Premium Table Header (like Products) */
    .table thead th {
        background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: .9px;
        padding: 14px 20px !important;
        border-bottom: none !important;
        white-space: nowrap;
        text-align: center !important;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <h5 class="mb-0 fw-bold">{{ __('purchases.purchase_history') }}</h5>
            </div>
            <div class="col-md-5">
                <form action="{{ route('purchases.index') }}" method="GET" class="input-group">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="{{ __('pos.search') ?? 'Search invoice or supplier...' }}" value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-3 text-{{ app()->getLocale() == 'ar' ? 'start' : 'end' }}">
                @can('create-purchases')
                <a href="{{ route('purchases.create') }}" class="pm-add-btn">
                    <i class="bi bi-plus-lg"></i> <span>{{ __('purchases.add_purchase') }}</span>
                </a>
                @endcan
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center">{{ __('purchases.invoice_number') }}</th>
                        <th class="text-center">{{ __('purchases.supplier') }}</th>
                        <th class="text-center">{{ __('pos.branch') }}</th>
                        <th class="text-center">{{ app()->getLocale() == 'ar' ? 'تاريخ الشراء' : 'Purchase Date' }}</th>
                        <th class="text-center">{{ __('purchases.net_total') }}</th>
                        <th class="text-center">{{ __('purchases.paid_amount') }}</th>
                        <th class="text-center">{{ __('purchases.remaining_balance') }}</th>
                        <th class="text-center">{{ __('purchases.payment_method') }}</th>
                        <th class="text-center">{{ __('pos.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $purchase->short_number }}</td>
                        <td>{{ $purchase->supplier->name }}</td>
                        <td>{{ $purchase->branch->name ?? '-' }}</td>
                        <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                        <td class="fw-bold">{{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="text-success fw-bold">{{ number_format($purchase->paid_amount, 2) }}</td>
                        <td class="text-danger fw-bold">{{ number_format($purchase->remaining_amount, 2) }}</td>
                        <td>
                            @php
                                $pm = str_replace(' ', '_', strtolower($purchase->payment_method));
                                $badgeClass = 'badge-' . $pm;
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ __('pos.' . $pm) ?? $purchase->payment_method }}</span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('pos.view') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('purchases.print', $purchase->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="{{ __('pos.print') ?? 'Print' }}">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <a href="{{ route('purchases.pdf', $purchase->id) }}" class="btn btn-sm btn-outline-danger" title="{{ __('pos.download_pdf') ?? 'Download PDF' }}">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                @can('delete-purchases')
                                <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('pos.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            {{ __('pos.no_data_available') ?? 'No purchases found.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($purchases->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $purchases->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        let timeout = null;

        if (searchInput) {
            if (searchInput.value) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    searchInput.closest('form').submit();
                }, 800);
            });
        }
    });
</script>
@endpush

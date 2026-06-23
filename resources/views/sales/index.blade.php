@extends('layouts.app')

@section('title', __('pos.sales'))

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

    /* Premium Table Header (like Products & Purchases) */
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
                <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>{{ __('pos.sales_history') }}</h5>
            </div>
            <div class="col-md-5">
                <form action="{{ route('sales.index') }}" method="GET" class="input-group">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="{{ __('pos.search') ?? 'Search invoice or customer...' }}" value="{{ $search ?? '' }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-3 text-{{ app()->getLocale() == 'ar' ? 'start' : 'end' }}">
                <a href="{{ route('sales.create') }}" class="pm-add-btn">
                    <i class="bi bi-plus-lg"></i> <span>{{ __('pos.add_sale') }}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">{{ __('pos.invoice_number') }}</th>
                        <th class="text-center">{{ __('pos.customer') }}</th>
                        <th class="text-center">{{ __('pos.branch') }}</th>
                        <th class="text-center">{{ __('pos.user') }}</th>
                        <th class="text-center">{{ __('pos.total') }}</th>
                        <th class="text-center">{{ __('pos.status') }}</th>
                        <th class="text-center">{{ __('pos.date') }}</th>
                        <th class="text-center">{{ __('pos.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                        <td class="text-center fw-bold">{{ $sale->short_number }}</td>
                        <td class="text-center">{{ $sale->customer ? $sale->customer->name : __('pos.walk_in_customer') }}</td>
                        <td class="text-center">{{ $sale->branch->name }}</td>
                        <td class="text-center">{{ $sale->user->username ?? $sale->user->name }}</td>
                        <td class="text-center fw-bold text-primary">{{ number_format($sale->total, 2) }}</td>
                        <td class="text-center">
                            @php
                                $statusClass = [
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-warning',
                                    'pending' => 'bg-danger'
                                ][$sale->status] ?? 'bg-secondary';
                                
                                // Calculate return status dynamically
                                $totalSoldQty = $sale->items->sum('quantity');
                                $totalReturnedQty = $sale->returns->sum('quantity');
                                $returnStatus = 'completed';
                                if ($totalReturnedQty > 0) {
                                    $returnStatus = $totalReturnedQty >= $totalSoldQty ? 'fully_returned' : 'partially_returned';
                                }
                            @endphp
                            <div class="d-flex flex-column gap-1 align-items-center">
                                <span class="badge {{ $statusClass }}">{{ __('pos.' . $sale->status) }}</span>
                                @if($returnStatus != 'completed')
                                    <span class="badge {{ $returnStatus == 'fully_returned' ? 'bg-danger' : 'bg-info' }}">
                                        {{ __('pos.' . $returnStatus) }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-info me-1" title="{{ __('pos.view') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="btn btn-sm btn-outline-success me-1" title="{{ __('pos.print') }}">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="{{ route('sales.pdf', $sale->id) }}" class="btn btn-sm btn-outline-danger me-1" title="{{ __('pos.download_pdf') ?? 'Download PDF' }}">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('pos.delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
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

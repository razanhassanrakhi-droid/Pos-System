@extends('layouts.app')

@section('title', __('pos.sales_returns'))

@push('styles')
<style>
    .pm-add-btn {
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
</style>
@endpush

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-return-left me-2 text-primary"></i>{{ __('pos.sales_returns') }}</h5>
            </div>
            <div class="col-md-6 text-{{ app()->getLocale() == 'ar' ? 'start' : 'end' }}">
                <a href="{{ route('sales_returns.create') }}" class="pm-add-btn">
                    <i class="bi bi-plus-lg"></i> <span>{{ __('pos.add') }} {{ __('pos.sales_returns') }}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('pos.return_number') ?? 'Return Number' }}</th>
                        <th>{{ __('pos.invoice_number') }}</th>
                        <th>{{ __('pos.product_name') }}</th>
                        <th>{{ __('pos.quantity') }}</th>
                        <th>{{ __('pos.reason') }}</th>
                        <th>{{ __('pos.user') }}</th>
                        <th>{{ __('pos.date') }}</th>
                        <th class="text-center pe-4">{{ __('pos.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $return->short_number }}</td>
                        <td>
                            <a href="{{ route('sales.show', $return->sale_id) }}" class="fw-bold text-decoration-none">
                                {{ $return->sale->short_number }}
                            </a>
                        </td>
                        <td>{{ $return->product->getTranslation('name') }}</td>
                        <td class="fw-bold">{{ (float)$return->quantity }}</td>
                        <td><small class="text-muted">{{ $return->translated_reason }}</small></td>
                        <td>{{ $return->creator->username ?? $return->creator->name }}</td>
                        <td>{{ $return->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-center pe-4">
                            <form action="{{ route('sales_returns.destroy', $return->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('pos.delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            {{ __('pos.no_returns_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', __('purchases.purchase_invoice') . ': ' . $purchase->invoice_number)

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card shadow-sm border-0 mb-4" id="printableArea">
            <div class="card-body p-3 p-md-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h2 class="fw-bold text-primary mb-1">{{ __('purchases.purchase_invoice') }}</h2>
                        <p class="text-muted mb-0">{{ $purchase->short_number }}</p>
                    </div>
                    <div class="text-end">
                        <h4 class="fw-bold mb-0">{{ $purchase->branch->getTranslation('name') }}</h4>
                        <p class="text-muted mb-0">{{ $purchase->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Info Section -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <h6 class="text-uppercase fw-bold text-muted mb-3">{{ __('purchases.supplier') }}</h6>
                        <h5 class="fw-bold mb-1">{{ $purchase->supplier->name }}</h5>
                        <p class="mb-0 text-muted">{{ $purchase->supplier->phone }}</p>
                        <p class="mb-0 text-muted">{{ $purchase->supplier->email }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-uppercase fw-bold text-muted mb-3">{{ __('purchases.payment_method') }}</h6>
                        <h5 class="fw-bold mb-1">{{ __('pos.' . strtolower($purchase->payment_method)) }}</h5>
                        <p class="mb-0 text-muted">{{ __('pos.user') }}: {{ $purchase->user->full_name }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-5">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 text-center">#</th>
                                <th class="py-3 text-center">{{ __('purchases.product') }}</th>
                                <th class="py-3 text-center">{{ __('purchases.quantity') }}</th>
                                <th class="py-3 text-center">{{ __('purchases.purchase_price') }}</th>
                                <th class="py-3 text-center">{{ __('purchases.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $item->product->name }}</span>
                                    @if($item->batch)
                                        <br><small class="text-muted">{{ __('purchases.batch_number') }}: {{ $item->batch->batch_number }}</small>
                                    @endif
                                    @if($item->expiry_date)
                                        <br><small class="text-muted">{{ __('purchases.expiry_date') }}: {{ $item->expiry_date->format('Y-m-d') }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-end">{{ number_format($item->purchase_price, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals & Payments -->
                @php
                    $setting = \App\Models\Setting::first();
                    $isRtl = app()->getLocale() == 'ar';
                @endphp
                <div class="row">
                    <!-- Left: Payments History -->
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="card border-0 shadow-sm rounded-4 h-100 pm-sub-card">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="text-primary fw-bold mb-3">
                                        <i class="bi bi-wallet2 me-2"></i>{{ $isRtl ? 'سجل الدفعات' : 'Payments History' }}
                                    </h6>
                                    @if($purchase->payments && $purchase->payments->count() > 0)
                                    <div class="table-responsive rounded-3 border-0 mb-3">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-3 border-0 text-muted small text-uppercase">{{ __('pos.method') ?? 'Method' }}</th>
                                                    <th class="border-0 text-muted small text-uppercase">{{ __('pos.amount') ?? 'Amount' }}</th>
                                                    <th class="pe-3 border-0 text-muted small text-uppercase text-end">{{ __('pos.date') ?? 'Date' }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($purchase->payments as $payment)
                                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                                                    <td class="ps-3 border-0">
                                                        <span class="badge bg-blue-light text-primary px-2 py-1 rounded-pill small" style="background-color: #eff6ff; color: #3b82f6 !important;">
                                                            {{ __('pos.' . str_replace(' ', '_', strtolower($payment->payment_method))) ?? $payment->payment_method }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold border-0">{{ number_format($payment->amount, 2) }}</td>
                                                    <td class="pe-3 border-0 text-muted small text-end">{{ $payment->created_at->format('Y-m-d') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-light py-3 border-0 rounded-4 text-center mb-3" style="background: #f8fafc; color: #64748b;">
                                        <i class="bi bi-exclamation-circle fs-4 d-block mb-2 text-muted"></i>
                                        <span class="small">{{ __('pos.no_payments_yet') ?? 'No payments recorded yet' }}</span>
                                    </div>
                                    @endif
                                </div>
                                
                                <div>
                                    <div class="d-flex justify-content-between p-3 rounded-4 mb-2" style="background: #f0fdf4; border: 1px solid rgba(34, 197, 94, 0.1);">
                                        <span class="text-success fw-bold">{{ __('purchases.paid_amount') }}</span>
                                        <span class="fw-bold text-success">{{ number_format($purchase->paid_amount, 2) }} {{ $setting->currency ?? 'SAR' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between p-3 rounded-4" style="background: #fef2f2; border: 1px solid rgba(239, 68, 68, 0.1);">
                                        <span class="text-danger fw-bold">{{ __('purchases.remaining_balance') }}</span>
                                        <span class="fw-bold text-danger">{{ number_format($purchase->remaining_amount, 2) }} {{ $setting->currency ?? 'SAR' }}</span>
                                    </div>
                                    
                                    @if($purchase->remaining_amount > 0)
                                    <button class="btn btn-primary w-100 mt-3 py-2-5 rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal" style="border-radius: 12px;">
                                        <i class="bi bi-plus-circle me-1"></i> {{ __('pos.record_payment') ?? 'Record Payment' }}
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right: Totals -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 pm-sub-card">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <h6 class="text-secondary fw-bold mb-3">
                                    <i class="bi bi-calculator me-2 text-primary"></i>{{ $isRtl ? 'ملخص الحسابات' : 'Summary' }}
                                </h6>
                                
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">{{ __('purchases.subtotal') }}</span>
                                        <span class="fw-semibold text-dark">{{ number_format($purchase->subtotal, 2) }} {{ $setting->currency ?? '' }}</span>
                                    </div>
                                    @if($purchase->discount > 0)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">{{ __('purchases.discount') }}</span>
                                        <span class="fw-bold text-danger">-{{ number_format($purchase->discount, 2) }} {{ $setting->currency ?? '' }}</span>
                                    </div>
                                    @endif
                                    @if($purchase->shipping_cost > 0)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">{{ __('purchases.shipping_cost') }}</span>
                                        <span class="fw-semibold text-dark">+{{ number_format($purchase->shipping_cost, 2) }} {{ $setting->currency ?? '' }}</span>
                                    </div>
                                    @endif
                                    @if($purchase->tax_amount > 0)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">{{ __('purchases.tax_rate') }} ({{ number_format($purchase->tax_rate, 2) }}%)</span>
                                        <span class="fw-semibold text-dark">+{{ number_format($purchase->tax_amount, 2) }} {{ $setting->currency ?? '' }}</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="p-3 rounded-4" style="background: linear-gradient(135deg, #f0f7ff 0%, #e0efff 100%); border: 1px solid rgba(59, 130, 246, 0.1);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fs-6 fw-bold text-primary">{{ __('purchases.net_total') }}</span>
                                        <span class="fs-4 fw-bold text-primary">{{ number_format($purchase->total_amount, 2) }} {{ $setting->currency ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($purchase->notes)
                <div class="mt-5">
                    <h6 class="text-uppercase fw-bold text-muted mb-2">{{ __('purchases.notes') }}</h6>
                    <p class="text-muted">{{ $purchase->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex gap-2 justify-content-center no-print mb-5">
            <a href="{{ route('purchases.print', $purchase->id) }}" target="_blank" class="btn btn-primary px-4">
                <i class="bi bi-printer me-1"></i> {{ __('purchases.print_invoice') }}
            </a>
            <a href="{{ route('purchases.pdf', $purchase->id) }}" class="btn btn-danger px-4">
                <i class="bi bi-file-earmark-pdf me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحميل PDF' : 'Download PDF' }}
            </a>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> {{ __('pos.back_to_list') ?? 'Back' }}
            </a>
        </div>
    </div>
</div>

<!-- Payment Modal -->
@if($purchase->remaining_amount > 0)
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">{{ __('pos.record_payment') ?? 'Record Payment' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small d-block">{{ $isRtl ? 'المبلغ المتبقي للدفع' : 'Remaining Balance' }}</label>
                        <div class="fs-4 fw-bold text-danger">
                            {{ number_format($purchase->remaining_amount, 2) }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('pos.payment_amount') ?? 'Payment Amount' }}</label>
                        <input type="number" name="amount" id="payment_amount" class="form-control form-control-lg border-2 border-primary border-opacity-25" 
                               value="{{ number_format($purchase->remaining_amount, 2, '.', '') }}" step="0.01" max="{{ number_format($purchase->remaining_amount, 2, '.', '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('pos.payment_method') ?? 'Payment Method' }}</label>
                        <select name="payment_method" class="form-select">
                            <option value="cash">{{ __('pos.cash') ?? 'Cash' }}</option>
                            <option value="card">{{ __('pos.card') ?? 'Card' }}</option>
                            <option value="transfer">{{ __('pos.transfer') ?? 'Transfer' }}</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">{{ __('pos.reference_number') ?? 'Reference Number' }} ({{ __('pos.optional') ?? 'Optional' }})</label>
                        <input type="text" name="reference_number" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="submit" id="submitPayment" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> {{ __('pos.record_payment') ?? 'Record Payment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
@media print {
    .no-print { display: none !important; }
    #sidebar { display: none !important; }
    #content { margin: 0 !important; width: 100% !important; }
    .top-navbar { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .container-fluid { padding: 0 !important; }
}

@media (max-width: 576px) {
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-responsive table {
        font-size: 0.82rem !important;
        min-width: 600px !important;
    }
}

[data-pm-theme="dark"] hr,
[data-pm-theme="dark"] .border-bottom,
[data-pm-theme="dark"] .border-top,
[data-pm-theme="dark"] .table,
[data-pm-theme="dark"] .table th,
[data-pm-theme="dark"] .table td,
[data-pm-theme="dark"] .card {
    border-color: rgba(255, 255, 255, 0.15) !important;
}

[data-pm-theme="dark"] hr {
    opacity: 1 !important;
}

[data-pm-theme="dark"] .bg-light,
[data-pm-theme="dark"] thead.bg-light,
[data-pm-theme="dark"] thead.bg-light th {
    background-color: rgba(255, 255, 255, 0.04) !important;
    color: #e2e8f0 !important;
}

[data-pm-theme="dark"] .text-muted,
[data-pm-theme="dark"] small,
[data-pm-theme="dark"] .small {
    color: rgba(255, 255, 255, 0.75) !important;
}

.pm-sub-card {
    background: #ffffff !important;
    border: 1px solid rgba(0,0,0,0.05) !important;
}

[data-pm-theme="dark"] .pm-sub-card {
    background: var(--pm-surface-2) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-pm-theme="dark"] .text-dark {
    color: #e2e8f0 !important;
}

[data-pm-theme="dark"] .text-secondary {
    color: #cbd5e1 !important;
}
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();
            
            const $btn = $('#submitPayment');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                url: "{{ route('purchases.payments.store', $purchase->id) }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ $isRtl ? "تم بنجاح" : "Success" }}',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> {{ __("pos.record_payment") }}');
                    Swal.fire({
                        icon: 'error',
                        title: '{{ $isRtl ? "خطأ" : "Error" }}',
                        text: xhr.responseJSON.message || 'Something went wrong'
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection

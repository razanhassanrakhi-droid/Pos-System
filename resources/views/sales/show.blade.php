@extends('layouts.app')

@section('title', __('pos.sale_details') . ': ' . $sale->invoice_number)

@push('styles')
<style>
    .invoice-card {
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        border: none;
    }
    .zatca-header {
        background: linear-gradient(135deg, var(--bs-primary) 0%, #1a252f 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
    }
    .company-logo-ui {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
    }
    .table-zatca th {
        background-color: #f8f9fa !important;
        font-weight: 700;
        color: #2c3e50;
    }
    .info-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .info-value {
        font-weight: 600;
        font-size: 14px;
        color: #2b2b2b;
    }
</style>
@endpush

@section('content')
@php
    $setting = \App\Models\Setting::first();
    $isRtl = app()->getLocale() == 'ar';
    $alignText = $isRtl ? 'text-end' : 'text-start';
    $taxRate = $sale->subtotal > 0 ? round(($sale->tax / $sale->subtotal) * 100) : 0;
@endphp
<div class="row">
    <div class="col-xl-9 mx-auto">
        <!-- Actions Row -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>{{ __('pos.sale_details') }}</h4>
            <div class="d-flex flex-wrap gap-2 w-100 w-sm-auto">
                <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="btn btn-success shadow-sm flex-fill flex-sm-grow-0 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-printer me-1"></i> {{ __('pos.print') }}
                </a>
                <a href="{{ route('sales.pdf', $sale->id) }}" class="btn btn-danger shadow-sm flex-fill flex-sm-grow-0 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-file-pdf me-1"></i> {{ __('pos.download_pdf') ?? 'Download PDF' }}
                </a>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary shadow-sm flex-fill flex-sm-grow-0 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('pos.back') }}
                </a>
            </div>
        </div>

        <div class="card invoice-card mb-4">
            <!-- Professional ZATCA Header -->
            <div class="zatca-header d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    @if($setting && $setting->logo)
                        <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" class="company-logo-ui">
                    @elseif($setting)
                        <div class="company-logo-ui d-flex align-items-center justify-content-center bg-light text-primary fs-3 fw-bold">
                            {{ mb_substr($setting->getTranslation('company_name', app()->getLocale()), 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="mb-1 fw-bold">{{ $setting ? $setting->getTranslation('company_name', app()->getLocale()) : 'Company Name' }}</h4>
                        @if($setting && $setting->tax_number)
                            <div class="small opacity-75"><i class="bi bi-building me-1"></i><strong>{{ __('pos.tax_number') }}:</strong> {{ $setting->tax_number }}</div>
                        @endif
                        <div class="small opacity-75"><i class="bi bi-geo-alt me-1"></i>{{ $setting ? $setting->getTranslation('company_address', app()->getLocale()) : '' }}</div>
                    </div>
                </div>
                <div class="text-{{ $isRtl ? 'start' : 'end' }} mt-3 mt-md-0">
                    <h5 class="mb-1 text-white opacity-75">{{ __('pos.invoice_number') }}</h5>
                    <h3 class="mb-0 fw-bold text-white">{{ $sale->short_number }}</h3>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Info Section -->
                <div class="row mb-4 g-4 pt-2">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded h-100 border start-primary">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">{{ __('pos.invoice_info') }}</h6>
                            <div class="mb-2">
                                <div class="info-label">{{ __('pos.date') }} &amp; {{ __('pos.time') }}</div>
                                <div class="info-value">{{ $sale->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="info-label">{{ __('pos.status') }}</div>
                                <div class="info-value">
                                    <span class="badge {{ $sale->status == 'paid' ? 'bg-success' : ($sale->status == 'partial' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ __('pos.' . $sale->status) }}
                                    </span>
                                    @if($sale->return_status != 'completed')
                                        <span class="badge {{ $sale->return_status == 'fully_returned' ? 'bg-danger' : 'bg-info' }}">
                                            {{ __('pos.' . $sale->return_status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-0">
                                <div class="info-label">{{ __('pos.branch') }} & {{ __('pos.cashier') }}</div>
                                <div class="info-value">{{ $sale->branch->name }} - {{ $sale->user->username ?? $sale->user->name }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded h-100 border">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">{{ __('pos.customer_info') }}</h6>
                            <div class="mb-2">
                                <div class="info-label">{{ __('pos.customer_name') }}</div>
                                <div class="info-value">{{ $sale->customer->name ?? __('pos.walk_in_customer') }}</div>
                            </div>
                            @if($sale->customer && $sale->customer->phone)
                            <div class="mb-2">
                                <div class="info-label">{{ __('pos.phone') }}</div>
                                <div class="info-value">{{ $sale->customer->phone }}</div>
                            </div>
                            @endif
                            @if($sale->customer && $sale->customer->tax_number)
                            <div class="mb-0">
                                <div class="info-label">{{ __('pos.tax_number') }}</div>
                                <div class="info-value">{{ $sale->customer->tax_number }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($sale->return_status != 'completed')
                <div class="alert alert-info py-2 mb-4 border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>{{ __('pos.invoice_includes_returns') }}</div>
                </div>
                @endif

                <!-- Products Table -->
                <div class="table-responsive mb-4 shadow-sm rounded border">
                    <table class="table table-hover table-zatca align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="{{ $alignText }}">{{ __('pos.product') }}</th>
                                <th>{{ __('pos.quantity') }}</th>
                                <th>{{ __('pos.returned_qty') }}</th>
                                <th>{{ __('pos.net_qty') }}</th>
                                <th>{{ __('pos.unit_price') }}</th>
                                <th>{{ __('pos.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                            @php
                                $factor = $item->conversion_factor ?: 1;
                                $displayQty = $item->quantity / $factor;
                                $displayReturnedQty = $item->returned_qty / $factor;
                                $displayNetQty = $item->net_qty / $factor;
                                $displayPrice = $item->price * $factor;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td class="{{ $alignText }}">
                                    <div class="fw-bold">{{ $item->product->name }}</div>
                                    @if($item->unit_name)
                                        <small class="text-muted d-block mt-1"><i class="bi bi-box-seam me-1"></i>{{ __('pos.unit') }}: {{ $item->unit_name }}</small>
                                    @endif
                                    @if($item->product->has_warranty)
                                        <div class="mt-2 p-2 bg-light rounded border border-info border-start-0 border-end-0 border-bottom-0">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <small class="fw-bold text-info"><i class="bi bi-shield-check me-1"></i>{{ __('pos.warranty') }} ({{ $item->product->warranty_period_months }} {{ __('pos.months') }})</small>
                                                @php $warranty = \App\Models\Warranty::where('sale_item_id', $item->id)->first(); @endphp
                                                @if($warranty)
                                                    <span class="badge bg-success small rounded-pill">{{ __('pos.active_warranty') }}</span>
                                                @else
                                                    <span class="badge bg-secondary small rounded-pill">{{ __('pos.no_warranty_record') ?? 'No Record' }}</span>
                                                @endif
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control serial-update-input" 
                                                       placeholder="{{ __('pos.enter_serial_number') }}" 
                                                       value="{{ $warranty->serial_number ?? '' }}" 
                                                       data-item-id="{{ $item->id }}">
                                                <button class="btn btn-primary btn-save-serial" type="button">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{ (float)$displayQty }}
                                    @if($factor != 1)
                                        <small class="text-muted d-block" style="font-size:0.75rem;">({{ (float)$item->quantity }} {{ $item->product->base_unit_name ?? 'Base' }})</small>
                                    @endif
                                </td>
                                <td class="{{ $item->returned_qty > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ (float)$displayReturnedQty }}
                                </td>
                                <td class="fw-bold {{ $item->net_qty == 0 ? 'text-muted' : 'text-primary' }}">
                                    {{ (float)$displayNetQty }}
                                </td>
                                <td>{{ number_format($displayPrice, 2) }}</td>
                                <td class="fw-bold text-dark">{{ number_format($item->net_total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals & Payments -->
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-wallet2 me-2"></i>{{ __('pos.payments') }}</h6>
                                @if($sale->payments && $sale->payments->count() > 0)
                                <div class="table-responsive rounded border bg-white mb-3">
                                    <table class="table table-sm table-borderless mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-3">{{ __('pos.method') }}</th>
                                                <th>{{ __('pos.amount') }}</th>
                                                <th>{{ __('pos.date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sale->payments as $payment)
                                            <tr class="border-top">
                                                <td class="ps-3"><span class="badge bg-secondary">{{ __('pos.' . $payment->payment_method) }}</span></td>
                                                <td class="fw-bold">{{ number_format($payment->amount, 2) }}</td>
                                                <td class="text-muted small">{{ $payment->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-warning py-2 mb-3"><i class="bi bi-exclamation-triangle me-2"></i>{{ __('pos.no_payments_yet') ?? 'No Payments yet' }}</div>
                                @endif
                                
                                <div class="d-flex justify-content-between p-2 rounded bg-white border border-success border-opacity-25 mb-2">
                                    <span class="text-success fw-bold">{{ __('pos.paid_amount') }}:</span>
                                    <span class="fw-bold text-success">{{ number_format($sale->paid_amount, 2) }} {{ $setting->currency ?? 'SAR' }}</span>
                                </div>
                                <div class="d-flex justify-content-between p-2 rounded bg-white border border-danger border-opacity-25">
                                    <span class="text-danger fw-bold">{{ __('pos.remaining_amount') }}:</span>
                                    <span class="fw-bold text-danger">{{ number_format($sale->net_total - $sale->paid_amount, 2) }} {{ $setting->currency ?? 'SAR' }}</span>
                                </div>
                                
                                @if($sale->status != 'paid')
                                <button class="btn btn-primary w-100 mt-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="bi bi-plus-circle me-1"></i> {{ __('pos.record_payment') }}
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-primary border-opacity-25 shadow-sm h-100">
                            <div class="card-body">
                                <table class="table table-borderless mb-0 fs-6">
                                    <tr>
                                        <td class="text-muted">{{ __('pos.subtotal') }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($sale->subtotal, 2) }}</td>
                                    </tr>
                                    @if($sale->returned_subtotal > 0)
                                    <tr>
                                        <td class="text-danger">{{ __('pos.returned_amount') }}</td>
                                        <td class="text-end text-danger fw-semibold">-{{ number_format($sale->returned_subtotal, 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="fw-bold">{{ __('pos.net_subtotal') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($sale->net_subtotal, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($sale->discount > 0)
                                    <tr>
                                        <td class="text-danger">{{ __('pos.discount') }}</td>
                                        <td class="text-end text-danger fw-semibold">-{{ number_format($sale->discount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="text-muted">{{ __('pos.tax_with_percent', ['percent' => $taxRate]) }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($sale->net_tax, 2) }}</td>
                                    </tr>
                                    <tr class="border-top border-2 border-dark">
                                        <td class="pt-3 pb-0 fs-5 fw-bold text-primary">{{ __('pos.grand_total') }}</td>
                                        <td class="pt-3 pb-0 text-end fs-5 fw-bold text-primary">{{ number_format($sale->net_total, 2) }} {{ $setting->currency ?? '' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if($sale->returns->count() > 0)
                <div class="mt-5">
                    <h5 class="fw-bold mb-3"><i class="bi bi-arrow-return-left me-2 text-danger"></i>{{ __('pos.returns_history') }}</h5>
                    <div class="table-responsive shadow-sm rounded border">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('pos.date') }}</th>
                                    <th class="{{ $alignText }}">{{ __('pos.product') }}</th>
                                    <th>{{ __('pos.quantity') }}</th>
                                    <th>{{ __('pos.reason') }}</th>
                                    <th>{{ __('pos.user') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->returns as $return)
                                <tr>
                                    <td class="small">{{ $return->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="{{ $alignText }} fw-bold">{{ $return->product->name }}</td>
                                    <td class="text-danger fw-bold">{{ (float)$return->quantity }}</td>
                                    <td><small>{{ $return->reason }}</small></td>
                                    <td>{{ $return->creator->username ?? $return->creator->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
                @if($sale->notes)
                <div class="mt-4 p-3 bg-light rounded border-start border-4 border-warning">
                    <h6 class="text-secondary fw-bold mb-1"><i class="bi bi-pencil-square me-2"></i>{{ __('pos.notes') }}</h6>
                    <p class="mb-0 text-dark">{{ $sale->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
@if($sale->status != 'paid')
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">{{ __('pos.record_payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small d-block">{{ __('pos.remaining_to_pay') }}</label>
                        <div class="fs-4 fw-bold text-danger">
                            {{ number_format($sale->net_total - $sale->paid_amount, 2) }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('pos.payment_amount') }}</label>
                        <input type="number" name="amount" id="payment_amount" class="form-control form-control-lg border-2 border-primary border-opacity-25" 
                               value="{{ number_format($sale->net_total - $sale->paid_amount, 2, '.', '') }}" step="0.01" max="{{ number_format($sale->net_total - $sale->paid_amount, 2, '.', '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('pos.payment_method') }}</label>
                        <select name="payment_method" class="form-select">
                            <option value="cash">{{ __('pos.cash') }}</option>
                            <option value="card">{{ __('pos.card') }}</option>
                            <option value="transfer">{{ __('pos.transfer') }}</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">{{ __('pos.reference_number') }} ({{ __('pos.optional') }})</label>
                        <input type="text" name="reference_number" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="submit" id="submitPayment" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> {{ __('pos.record_payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();
            
            const $btn = $('#submitPayment');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                url: "{{ route('sales.payments.store', $sale->id) }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
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
                        title: 'Error',
                        text: xhr.responseJSON.message || 'Something went wrong'
                    });
                }
            });
        });

        $('.btn-save-serial').on('click', function() {
            const $btn = $(this);
            const $input = $btn.closest('.input-group').find('.serial-update-input');
            const itemId = $input.data('item-id');
            const serial = $input.val();

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: "{{ route('warranties.upsert') }}", // Ensure this route is correct in your system
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    sale_item_id: itemId,
                    serial_number: serial
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i>');
                    Swal.fire('Error', xhr.responseJSON.message || 'Error updating serial', 'error');
                }
            });
        });
    });
</script>
@endpush

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('purchases.purchase_invoice') }} - {{ $purchase->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --font-family: 'Cairo', sans-serif;
        }
        body {
            font-family: var(--font-family);
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.5;
            font-size: 14px;
        }
        .invoice-wrapper {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        /* Header Info */
        .company-logo {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #eaeaea;
        }
        .invoice-title {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .company-details p {
            margin: 0;
            font-size: 13px;
            color: #555;
        }
        /* Tables */
        .table th {
            background-color: #f1f4f8 !important;
            color: var(--primary-color);
            font-size: 13px;
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
        }
        .table td {
            font-size: 13px;
            vertical-align: middle;
        }
        /* Summary Section */
        .totals-table th, .totals-table td {
            font-size: 14px;
            padding: 6px 10px;
        }
        .totals-table .grand-total {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            border-top: 2px solid var(--primary-color);
        }
        /* Print Optimization */
        @media print {
            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }
            .invoice-wrapper {
                margin: 0 auto;
                padding: 10px;
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            @page {
                margin: 0;
            }
            .table th, .table td {
                padding: 4px;
            }
        }
    </style>
</head>
<body onload="window.print()">
    @php
        $setting = \App\Models\Setting::first();
        $isRtl = app()->getLocale() == 'ar';
        $alignText = $isRtl ? 'text-end' : 'text-start';
        $alignValue = $isRtl ? 'text-start' : 'text-end';
    @endphp

    <div class="no-print text-center py-3 bg-white shadow-sm mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer mb-1" viewBox="0 0 16 16">
              <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
              <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg> {{ __('pos.print') ?? 'Print' }}
        </button>
        <button onclick="window.history.back()" class="btn btn-outline-secondary px-4">
            {{ __('pos.back') ?? 'Back' }}
        </button>
    </div>

    <div class="invoice-wrapper" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        
        <!-- Header -->
        <div class="row align-items-center border-bottom pb-4 mb-4 text-center text-sm-{{ $isRtl ? 'end' : 'start' }}">
            <div class="col-sm-3 text-center mb-3 mb-sm-0">
                @if($setting && $setting->logo)
                    <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" class="company-logo">
                @elseif($setting)
                    <div class="company-logo d-flex align-items-center justify-content-center bg-light fs-2 fw-bold text-secondary mx-auto">
                        {{ mb_substr($setting->getTranslation('company_name', app()->getLocale()), 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="col-sm-9 company-details">
                @if($setting)
                    <h2 class="invoice-title">{{ $setting->getTranslation('company_name', app()->getLocale()) }}</h2>
                    <p>{{ $setting->getTranslation('company_address', app()->getLocale()) }}</p>
                    @if($setting->phone) <p><strong>{{ __('pos.phone') ?? 'Phone' }}:</strong> {{ $setting->phone }}</p> @endif
                    @if($setting->email) <p><strong>{{ __('pos.email') ?? 'Email' }}:</strong> {{ $setting->email }}</p> @endif
                @endif
            </div>
        </div>

        <!-- Invoice & Supplier Data -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">{{ __('purchases.purchase_invoice') }}</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="45%">{{ __('purchases.invoice_number') }}</td>
                            <td class="fw-bold">{{ $purchase->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ app()->getLocale() == 'ar' ? 'تاريخ الشراء' : 'Purchase Date' }}</td>
                            <td class="fw-bold">{{ $purchase->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('pos.branch') ?? 'Branch' }}</td>
                            <td class="fw-bold">{{ $purchase->branch->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('pos.user') ?? 'User' }}</td>
                            <td class="fw-bold">{{ $purchase->user->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">{{ __('purchases.supplier') }}</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="45%">{{ __('purchases.supplier') }}</td>
                            <td class="fw-bold">{{ $purchase->supplier->name }}</td>
                        </tr>
                        @if($purchase->supplier->phone)
                        <tr>
                            <td class="text-muted">{{ __('pos.phone') ?? 'Phone' }}</td>
                            <td class="fw-bold">{{ $purchase->supplier->phone }}</td>
                        </tr>
                        @endif
                        @if($purchase->supplier->email)
                        <tr>
                            <td class="text-muted">{{ __('pos.email') ?? 'Email' }}</td>
                            <td class="fw-bold">{{ $purchase->supplier->email }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped text-center align-middle mb-0">
                <thead>
                    <tr>
                        <th class="{{ $alignText }}">{{ __('purchases.product') }}</th>
                        <th>{{ __('purchases.quantity') }}</th>
                        <th>{{ __('purchases.purchase_price') }}</th>
                        <th class="{{ $alignValue }}">{{ __('purchases.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                    <tr>
                        <td class="{{ $alignText }} fw-semibold">
                            {{ $item->product->name }}
                            @if($item->unit_name)
                                <span class="badge bg-secondary text-white ms-1">{{ $item->unit_name }}</span>
                            @endif
                            @if($item->expiry_date)
                                <br><small class="text-muted">{{ __('purchases.expiry_date') }}: {{ $item->expiry_date->format('Y-m-d') }}</small>
                            @endif
                        </td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ number_format($item->purchase_price, 2) }} {{ $setting->currency ?? '' }}</td>
                        <td class="{{ $alignValue }} fw-bold">{{ number_format($item->total, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="row align-items-end mb-4">
            <div class="col-sm-6 mb-4 mb-sm-0">
                @if($setting && $setting->tax_number)
                <div class="p-3 bg-light rounded text-center text-sm-start border border-secondary border-opacity-25 mb-3">
                    <strong>{{ __('pos.tax_number') ?? 'Tax Number' }}:</strong> {{ $setting->tax_number }}
                </div>
                @endif
                
                <div>
                    <h6 class="fw-bold text-muted mb-2 small">{{ __('purchases.payment_method') }} / {{ $isRtl ? 'سجل الدفعات' : 'Payments' }}</h6>
                    @if($purchase->payments && $purchase->payments->count() > 0)
                        @foreach($purchase->payments as $payment)
                            <div class="mb-1">
                                <span class="badge border border-secondary text-secondary px-2 py-1 bg-white">
                                    {{ __('pos.' . str_replace(' ', '_', strtolower($payment->payment_method))) ?? $payment->payment_method }}: 
                                    {{ number_format($payment->amount, 2) }} {{ $setting->currency ?? 'SAR' }}
                                </span>
                                <small class="text-muted">({{ $payment->created_at->format('Y-m-d') }})</small>
                            </div>
                        @endforeach
                    @else
                        <span class="badge border border-secondary text-secondary px-3 py-2 bg-white">
                            {{ __('pos.' . str_replace(' ', '_', strtolower($purchase->payment_method))) }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-sm-6">
                <table class="table table-sm table-borderless totals-table mb-0">
                    <tr>
                        <th class="{{ $alignText }}">{{ __('purchases.subtotal') }}</th>
                        <td class="{{ $alignValue }}">{{ number_format($purchase->subtotal, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @if($purchase->discount > 0)
                    <tr>
                        <th class="{{ $alignText }} text-danger">{{ __('purchases.discount') }}</th>
                        <td class="{{ $alignValue }} text-danger">-{{ number_format($purchase->discount, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @endif
                    @if($purchase->shipping_cost > 0)
                    <tr>
                        <th class="{{ $alignText }}">{{ __('purchases.shipping_cost') }}</th>
                        <td class="{{ $alignValue }}">{{ number_format($purchase->shipping_cost, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @endif
                    @if($purchase->tax_amount > 0)
                    <tr>
                        <th class="{{ $alignText }}">{{ __('purchases.tax_rate') }} ({{ number_format($purchase->tax_rate, 2) }}%)</th>
                        <td class="{{ $alignValue }}">{{ number_format($purchase->tax_amount, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total border-top border-2">
                        <th class="{{ $alignText }} pt-3">{{ __('purchases.net_total') }}</th>
                        <td class="{{ $alignValue }} pt-3">{{ number_format($purchase->total_amount, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="{{ $alignText }} text-success">{{ __('purchases.paid_amount') }}</th>
                        <td class="{{ $alignValue }} fw-bold text-success">{{ number_format($purchase->paid_amount, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="{{ $alignText }} text-danger">{{ __('purchases.remaining_balance') }}</th>
                        <td class="{{ $alignValue }} fw-bold text-danger">{{ number_format($purchase->remaining_amount, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Notes -->
        @if($purchase->notes)
        <div class="border-top pt-3 mb-4">
            <h6 class="fw-bold text-muted mb-2 small">{{ __('purchases.notes') }}</h6>
            <p class="text-muted mb-0">{{ $purchase->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-5 pt-3 border-top border-light">
            <p class="text-muted mb-0" style="font-size: 11px;">
                {{ __('pos.auto_generated') ?? 'Auto generated' }} &bull; {{ now()->format('Y-m-d H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>

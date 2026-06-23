<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('pos.invoice') }} - {{ $sale->invoice_number }}</title>
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
        /* Print Optimization for 80mm Thermal & A4 */
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
            /* Adjustments for generic receipt printers */
            @page {
                margin: 0;
            }
            .table th, .table td {
                padding: 4px;
            }
            /* Unstacking Bootstrap grids to linear if needed */
        }
    </style>
</head>
<body>
    @php
        $setting = \App\Models\Setting::first();
        $isRtl = app()->getLocale() == 'ar';
        $alignText = $isRtl ? 'text-end' : 'text-start';
        $alignValue = $isRtl ? 'text-start' : 'text-end';
        $taxRate = $sale->subtotal > 0 ? round(($sale->tax / $sale->subtotal) * 100) : 0;
    @endphp

    <div class="no-print text-center py-3 bg-white shadow-sm mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer mb-1" viewBox="0 0 16 16">
              <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
              <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg> {{ __('pos.print') }}
        </button>
        <button onclick="if(window.opener) { window.close(); } else { window.location.href = '{{ route('sales.index') }}'; }" class="btn btn-outline-secondary px-4">
            {{ __('pos.back') }}
        </button>
    </div>

    <div class="invoice-wrapper" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        
        <!-- Header -->
        <div class="row align-items-center border-bottom pb-4 mb-4 text-center text-sm-{{ $isRtl ? 'end' : 'start' }}">
            <div class="col-sm-3 text-center mb-3 mb-sm-0">
                @if($setting->logo)
                    <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" class="company-logo">
                @else
                    <div class="company-logo d-flex align-items-center justify-content-center bg-light fs-2 fw-bold text-secondary mx-auto">
                        {{ mb_substr($setting->getTranslation('company_name', app()->getLocale()), 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="col-sm-9 company-details">
                <h2 class="invoice-title">{{ $setting->getTranslation('company_name', app()->getLocale()) }}</h2>
                <p>{{ $setting->getTranslation('company_address', app()->getLocale()) }}</p>
                @if($setting->phone) <p><strong>{{ __('pos.phone') }}:</strong> {{ $setting->phone }}</p> @endif
                @if($setting->email) <p><strong>{{ __('pos.email') }}:</strong> {{ $setting->email }}</p> @endif
            </div>
        </div>

        <!-- Invoice & Customer Data -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">{{ __('pos.invoice_info') }}</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="45%">{{ __('pos.invoice_number') }}</td>
                            <td class="fw-bold">{{ $sale->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('pos.date') }} &amp; {{ __('pos.time') }}</td>
                            <td class="fw-bold">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('pos.branch') }}</td>
                            <td class="fw-bold">{{ $sale->branch->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('pos.cashier') }}</td>
                            <td class="fw-bold">{{ $sale->user->username ?? $sale->user->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">{{ __('pos.customer_info') }}</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="45%">{{ __('pos.customer_name') }}</td>
                            <td class="fw-bold">{{ $sale->customer->name ?? __('pos.walk_in_customer') }}</td>
                        </tr>
                        @if($sale->customer && $sale->customer->phone)
                        <tr>
                            <td class="text-muted">{{ __('pos.phone') }}</td>
                            <td class="fw-bold">{{ $sale->customer->phone }}</td>
                        </tr>
                        @endif
                        @if($sale->customer && $sale->customer->tax_number)
                        <tr>
                            <td class="text-muted">{{ __('pos.tax_number') }}</td>
                            <td class="fw-bold">{{ $sale->customer->tax_number }}</td>
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
                        <th class="{{ $alignText }}">{{ __('pos.product') }}</th>
                        <th>{{ __('pos.quantity') }}</th>
                        <th>{{ __('pos.returned_qty') }}</th>
                        <th>{{ __('pos.net_qty') }}</th>
                        <th>{{ __('pos.unit_price') }}</th>
                        <th class="{{ $alignValue }}">{{ __('pos.total') }}</th>
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
                        $unitLabel = $item->unit_name ?: ($item->product->base_unit_name ?: 'Piece');
                    @endphp
                    <tr>
                        <td class="{{ $alignText }} fw-semibold">{{ $item->product->name }}</td>
                        <td>{{ (float)$displayQty }} <span class="small text-muted">({{ $unitLabel }})</span></td>
                        <td class="{{ $item->returned_qty > 0 ? 'text-danger fw-bold' : '' }}">
                            {{ (float)$displayReturnedQty }}
                        </td>
                        <td>{{ (float)$displayNetQty }}</td>
                        <td>{{ number_format($displayPrice, 2) }} {{ $setting->currency ?? '' }}</td>
                        <td class="{{ $alignValue }} fw-bold">{{ number_format($item->net_total, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="row align-items-end mb-4">
            <div class="col-sm-6 mb-4 mb-sm-0">
                @if($setting->tax_number)
                <div class="p-3 bg-light rounded text-center text-sm-start border border-secondary border-opacity-25 mb-3">
                    <strong>{{ __('pos.tax_number') }}:</strong> {{ $setting->tax_number }}
                </div>
                @endif

                @if($sale->return_status != 'completed')
                <div class="alert alert-info py-2 small mb-3 border">
                    <strong>{{ __('pos.note') }}:</strong> {{ __('pos.invoice_includes_returns') }}
                </div>
                @endif
                
                @if($sale->payments && $sale->payments->count() > 0)
                <div class="">
                    <h6 class="fw-bold text-muted mb-2 small">{{ __('pos.payment_method') }}</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($sale->payments as $payment)
                            <span class="badge border border-secondary text-secondary px-3 py-2 bg-white">
                                {{ __('pos.' . $payment->payment_method) }}: {{ number_format($payment->amount, 2) }} {{ __('pos.sar') }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="col-sm-6">
                <table class="table table-sm table-borderless totals-table mb-0">
                    <tr>
                        <th class="{{ $alignText }}">{{ __('pos.subtotal') }}</th>
                        <td class="{{ $alignValue }}">{{ number_format($sale->subtotal, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @if($sale->returned_subtotal > 0)
                    <tr>
                        <th class="{{ $alignText }} text-danger">{{ __('pos.returned_amount') }}</th>
                        <td class="{{ $alignValue }} text-danger">-{{ number_format($sale->returned_subtotal, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    <tr class="border-top">
                        <th class="{{ $alignText }}">{{ __('pos.net_subtotal') }}</th>
                        <td class="{{ $alignValue }}">{{ number_format($sale->net_subtotal, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @endif
                    @if($sale->discount > 0)
                    <tr>
                        <th class="{{ $alignText }} text-danger">{{ __('pos.discount') }}</th>
                        <td class="{{ $alignValue }} text-danger">-{{ number_format($sale->discount, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="{{ $alignText }}">{{ __('pos.tax_with_percent', ['percent' => $taxRate]) }}</th>
                        <td class="{{ $alignValue }}">{{ number_format($sale->net_tax, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    <tr class="grand-total border-top border-2">
                        <th class="{{ $alignText }} pt-3">{{ __('pos.grand_total_after_return') }}</th>
                        <td class="{{ $alignValue }} pt-3">{{ number_format($sale->net_total, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="{{ $alignText }} text-success">{{ __('pos.paid_amount') }}</th>
                        <td class="{{ $alignValue }} fw-bold text-success">{{ number_format($sale->paid_amount, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="{{ $alignText }} text-danger">{{ __('pos.remaining_amount') }}</th>
                        <td class="{{ $alignValue }} fw-bold text-danger">{{ number_format($sale->net_total - $sale->paid_amount, 2) }} {{ $setting->currency ?? '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5 pt-3 border-top border-light">
            <h6 class="fw-bold mb-1">{{ __('pos.thank_you_for_shopping') }}</h6>
            @if($setting->getTranslation('return_policy', app()->getLocale()))
                <p class="small text-muted mb-2">{{ $setting->getTranslation('return_policy', app()->getLocale()) }}</p>
            @endif
            <p class="text-muted mb-0" style="font-size: 11px;">
                {{ __('pos.auto_generated') }} &bull; {{ now()->format('Y-m-d H:i:s') }}
            </p>
        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>

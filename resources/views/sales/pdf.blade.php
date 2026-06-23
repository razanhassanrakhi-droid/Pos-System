<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('pos.sale_details') }} #{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 13px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #f97316;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
        }
        .header td {
            vertical-align: top;
        }
        .title {
            font-size: 24px;
            color: #f97316;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            text-transform: uppercase;
            font-weight: bold;
            color: #94a3b8;
            font-size: 11px;
            margin-bottom: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            font-weight: bold;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        .items-table td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            vertical-align: top;
        }
        .text-center {
            text-align: center !important;
        }
        .text-end {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }} !important;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 0;
        }
        .totals-table .label {
            font-weight: bold;
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
            padding-right: 15px;
        }
        .totals-table .value {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
            width: 120px;
        }
        .totals-wrapper {
            float: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
            width: 300px;
        }
        .notes-section {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="title">{{ __('pos.sale_details') }}</div>
                        <div style="margin-top: 5px; color: #64748b;">#{{ $sale->invoice_number }}</div>
                    </td>
                    <td class="text-end">
                        <div style="font-weight: bold; font-size: 16px;">{{ $sale->branch->name }}</div>
                        <div style="margin-top: 5px; color: #64748b;">{{ $sale->created_at->format('Y-m-d H:i') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td>
                    <div class="section-title">{{ __('pos.customer') }}</div>
                    <div style="font-weight: bold; font-size: 15px;">{{ $sale->customer->name ?? __('pos.walk_in_customer') }}</div>
                    @if($sale->customer && $sale->customer->phone)
                        <div style="color: #64748b; margin-top: 3px;">{{ $sale->customer->phone }}</div>
                    @endif
                    @if($sale->customer && $sale->customer->tax_number)
                        <div style="color: #64748b;">{{ __('pos.tax_number') }}: {{ $sale->customer->tax_number }}</div>
                    @endif
                </td>
                <td class="text-end">
                    <div class="section-title">{{ __('pos.payment_method') }}</div>
                    <div style="font-weight: bold; font-size: 15px;">{{ __('pos.' . str_replace(' ', '_', strtolower($sale->payment_method))) }}</div>
                    <div style="color: #64748b; margin-top: 3px;">{{ __('pos.user') }}: {{ $sale->user->name }}</div>
                    @if($sale->payments && $sale->payments->count() > 0)
                        <div style="margin-top: 10px;" class="section-title">{{ app()->getLocale() == 'ar' ? 'سجل الدفعات' : 'Payments History' }}</div>
                        @foreach($sale->payments as $payment)
                            <div style="font-size: 11px; color: #475569;">
                                {{ __('pos.' . str_replace(' ', '_', strtolower($payment->payment_method))) ?? $payment->payment_method }}: 
                                {{ number_format($payment->amount, 2) }} {{ $setting->currency ?? 'SAR' }} 
                                <span style="color: #94a3b8;">({{ $payment->created_at->format('Y-m-d') }})</span>
                            </div>
                        @endforeach
                    @endif
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">#</th>
                    <th>{{ __('pos.product') }}</th>
                    <th style="width: 90px;" class="text-center">{{ __('pos.quantity') }}</th>
                    <th style="width: 110px;" class="text-end">{{ __('pos.unit_price') }}</th>
                    <th style="width: 120px;" class="text-end">{{ __('pos.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                @php
                    $factor = $item->conversion_factor ?: 1;
                    $displayQty = $item->quantity / $factor;
                    $displayPrice = $item->price * $factor;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $item->product->name }}</div>
                        @if($item->unit_name)
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ __('pos.unit') ?? 'Unit' }}: {{ $item->unit_name }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ (float)$displayQty }}
                        @if($factor != 1)
                            <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">({{ (float)$item->quantity }} {{ $item->product->base_unit_name ?? 'Base' }})</div>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($displayPrice, 2) }}</td>
                    <td class="text-end" style="font-weight: bold;">{{ number_format($item->net_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width: 100%; margin-top: 20px; border: none; border-collapse: collapse;">
            <tr>
                <td style="width: 55%; border: none;"></td>
                <td style="width: 45%; border: none; padding: 0;">
                    <table class="totals-table">
                        <tr>
                            <td class="label" style="border: none;">{{ __('pos.subtotal') }}:</td>
                            <td class="value" style="border: none;">{{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        @if($sale->discount > 0)
                        <tr>
                            <td class="label" style="border: none;">{{ __('pos.discount') }}:</td>
                            <td class="value" style="border: none; color: #ef4444;">-{{ number_format($sale->discount, 2) }}</td>
                        </tr>
                        @endif
                        @if($sale->tax > 0)
                        <tr>
                            <td class="label" style="border: none;">{{ __('pos.tax') ?? 'VAT' }}:</td>
                            <td class="value" style="border: none;">{{ number_format($sale->tax, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="border-top: 1px solid #e2e8f0; font-size: 15px; font-weight: bold; color: #f97316;">
                            <td class="label" style="border: none; padding-top: 10px;">{{ __('pos.net_total') }}:</td>
                            <td class="value" style="border: none; padding-top: 10px;">{{ number_format($sale->net_total, 2) }}</td>
                        </tr>
                        <tr style="font-weight: bold; color: #10b981;">
                            <td class="label" style="border: none;">{{ __('pos.paid_amount') }}:</td>
                            <td class="value" style="border: none;">{{ number_format($sale->paid_amount, 2) }}</td>
                        </tr>
                        <tr style="font-weight: bold; color: #ef4444;">
                            <td class="label" style="border: none;">{{ __('pos.remaining_amount') }}:</td>
                            <td class="value" style="border: none;">{{ number_format($sale->net_total - $sale->paid_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if($sale->notes)
        <div class="notes-section">
            <div class="section-title">{{ __('pos.notes') }}</div>
            <div style="color: #64748b;">{{ $sale->notes }}</div>
        </div>
        @endif
    </div>
</body>
</html>

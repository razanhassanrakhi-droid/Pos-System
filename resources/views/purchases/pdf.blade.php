<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('purchases.purchase_invoice') }} #{{ $purchase->invoice_number }}</title>
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
            border-bottom: 2px solid #3b82f6;
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
            color: #3b82f6;
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
        .text-start {
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;
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
            width: 60%;
            text-align: right !important;
            padding-right: 15px;
            padding-left: 15px;
        }
        .totals-table .value {
            text-align: left !important;
            width: 40%;
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
                    @if(app()->getLocale() == 'ar')
                        <td style="text-align: right;">
                            <div class="title">{{ __('purchases.purchase_invoice') }}</div>
                            <div style="margin-top: 5px; color: #64748b;" dir="ltr">&#x200E;#{{ $purchase->invoice_number }}</div>
                        </td>
                        <td style="text-align: left;">
                            <div style="font-weight: bold; font-size: 16px;">{{ $purchase->branch->name }}</div>
                            <div style="margin-top: 5px; color: #64748b;">{{ $purchase->created_at->format('Y-m-d H:i') }}</div>
                        </td>
                    @else
                        <td style="text-align: left;">
                            <div class="title">{{ __('purchases.purchase_invoice') }}</div>
                            <div style="margin-top: 5px; color: #64748b;" dir="ltr">&#x200E;#{{ $purchase->invoice_number }}</div>
                        </td>
                        <td style="text-align: right;">
                            <div style="font-weight: bold; font-size: 16px;">{{ $purchase->branch->name }}</div>
                            <div style="margin-top: 5px; color: #64748b;">{{ $purchase->created_at->format('Y-m-d H:i') }}</div>
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td style="text-align: right;">
                    <div class="section-title">{{ __('purchases.supplier') }}</div>
                    <div style="font-weight: bold; font-size: 15px;">{{ $purchase->supplier->name }}</div>
                    @if($purchase->supplier->phone)
                        <div style="color: #64748b; margin-top: 3px;" dir="ltr">&#x200E;{{ $purchase->supplier->phone }}</div>
                    @endif
                    @if($purchase->supplier->email)
                        <div style="color: #64748b;">{{ $purchase->supplier->email }}</div>
                    @endif
                </td>
                <td style="text-align: left;">
                    <div class="section-title">{{ __('purchases.payment_method') }}</div>
                    <div style="font-weight: bold; font-size: 15px;">{{ __('pos.' . str_replace(' ', '_', strtolower($purchase->payment_method))) }}</div>
                    <div style="color: #64748b; margin-top: 3px;">{{ __('pos.user') }}: {{ $purchase->user->name }}</div>
                    @if($purchase->payments && $purchase->payments->count() > 0)
                        <div style="margin-top: 10px;" class="section-title">{{ app()->getLocale() == 'ar' ? 'سجل الدفعات' : 'Payments History' }}</div>
                        @foreach($purchase->payments as $payment)
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
                    <th>{{ __('purchases.product') }}</th>
                    <th style="width: 90px;" class="text-center">{{ __('purchases.quantity') }}</th>
                    <th style="width: 110px;" class="text-end">{{ __('purchases.purchase_price') }}</th>
                    <th style="width: 120px;" class="text-end">{{ __('purchases.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $item->product->name }}</div>
                        @if($item->unit_name)
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ __('Unit') }}: {{ $item->unit_name }}</div>
                        @endif
                        @if($item->expiry_date)
                            <div style="font-size: 11px; color: #64748b;">{{ __('purchases.expiry_date') }}: {{ $item->expiry_date->format('Y-m-d') }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-end">{{ number_format($item->purchase_price, 2) }}</td>
                    <td class="text-end" style="font-weight: bold;">{{ number_format($item->total, 2) }}</td>
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
                            <td class="label" style="border: none;">{{ __('purchases.subtotal') }}:</td>
                            <td class="value" style="border: none;">{{ number_format($purchase->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">{{ __('purchases.discount') }}:</td>
                            <td class="value" style="border: none; color: #ef4444;">-{{ number_format($purchase->discount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">{{ __('purchases.shipping_cost') }}:</td>
                            <td class="value" style="border: none;">{{ number_format($purchase->shipping_cost, 2) }}</td>
                        </tr>
                        @if($purchase->tax_rate > 0)
                        <tr>
                            <td class="label" style="border: none;">{{ __('purchases.tax_rate') }} ({{ number_format($purchase->tax_rate, 2) }}%):</td>
                            <td class="value" style="border: none;">{{ number_format($purchase->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="border-top: 1px solid #e2e8f0; font-size: 15px; font-weight: bold; color: #3b82f6;">
                            <td class="label" style="border: none; padding-top: 10px;">{{ __('purchases.net_total') }}:</td>
                            <td class="value" style="border: none; padding-top: 10px;">{{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                        <tr style="font-weight: bold; color: #10b981;">
                            <td class="label" style="border: none;">{{ __('purchases.paid_amount') }}:</td>
                            <td class="value" style="border: none;">{{ number_format($purchase->paid_amount, 2) }}</td>
                        </tr>
                        <tr style="font-weight: bold; color: #ef4444;">
                            <td class="label" style="border: none;">{{ __('purchases.remaining_balance') }}:</td>
                            <td class="value" style="border: none;">{{ number_format($purchase->remaining_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if($purchase->notes)
        <div class="notes-section">
            <div class="section-title">{{ __('purchases.notes') }}</div>
            <div style="color: #64748b;">{{ $purchase->notes }}</div>
        </div>
        @endif
    </div>
</body>
</html>

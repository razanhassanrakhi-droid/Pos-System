@extends('reports.pdf.layout')

@section('title', __('pos.sales_report'))

@section('content')
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>{{ __('pos.total_sales') }}:</strong> {{ number_format($report['total_sales'], 2) }} {{ $setting->currency }}</td>
                <td><strong>{{ __('pos.invoice_count') }}:</strong> {{ $report['invoice_count'] }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('pos.total_vat') }}:</strong> {{ number_format($report['total_tax'], 2) }} {{ $setting->currency }}</td>
                <td><strong>{{ __('pos.total_discounts') }}:</strong> {{ number_format($report['total_discount'], 2) }} {{ $setting->currency }}</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>{{ __('pos.invoice_number') }}</th>
                <th>{{ __('pos.customer') }}</th>
                <th>{{ __('pos.total') }}</th>
                <th>{{ __('pos.paid') }}</th>
                <th>{{ __('pos.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['invoices'] as $invoice)
            <tr>
                <td>#{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->customer->name ?? __('pos.walk_in_customer') }}</td>
                <td class="fw-bold">{{ number_format($invoice->total, 2) }}</td>
                <td class="text-success">{{ number_format($invoice->paid_amount, 2) }}</td>
                <td>{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

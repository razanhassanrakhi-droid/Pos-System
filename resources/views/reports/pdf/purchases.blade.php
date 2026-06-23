@extends('reports.pdf.layout')

@section('title', __('pos.purchase_report'))

@section('content')
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>{{ __('pos.total_purchases') }}:</strong> {{ number_format($report['total_purchases'], 2) }} {{ $setting->currency }}</td>
                <td><strong>{{ __('pos.total_paid') }}:</strong> {{ number_format($report['total_paid'], 2) }} {{ $setting->currency }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('pos.total_remaining') }}:</strong> {{ number_format($report['total_remaining'], 2) }} {{ $setting->currency }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>{{ __('pos.invoice_number') }}</th>
                <th>{{ __('pos.supplier') }}</th>
                <th>{{ __('pos.total') }}</th>
                <th>{{ __('pos.paid') }}</th>
                <th>{{ __('pos.remaining') }}</th>
                <th>{{ __('pos.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['purchases'] as $p)
            <tr>
                <td>#{{ $p->invoice_number }}</td>
                <td>{{ $p->supplier->name ?? '-' }}</td>
                <td class="fw-bold">{{ number_format($p->total_amount, 2) }}</td>
                <td class="text-success">{{ number_format($p->paid_amount, 2) }}</td>
                <td class="text-danger">{{ number_format($p->remaining_amount, 2) }}</td>
                <td>{{ $p->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

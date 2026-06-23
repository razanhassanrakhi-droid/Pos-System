@extends('reports.pdf.layout')

@section('title', __('pos.supplier_report'))

@section('content')
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>{{ __('pos.total_suppliers') }}:</strong> {{ $report['total_suppliers'] }}</td>
                <td><strong>{{ __('pos.total_purchases') }}:</strong> {{ number_format($report['total_purchases'], 2) }} {{ $setting->currency }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('pos.total_paid') }}:</strong> {{ number_format($report['total_paid'], 2) }} {{ $setting->currency }}</td>
                <td><strong>{{ __('pos.total_remaining') }}:</strong> {{ number_format($report['total_remaining'], 2) }} {{ $setting->currency }}</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>{{ __('pos.supplier_number') }}</th>
                <th>{{ __('pos.supplier_name') }}</th>
                <th>{{ __('pos.email') }}</th>
                <th>{{ __('pos.address') }}</th>
                <th>{{ __('pos.total_purchases') }}</th>
                <th>{{ __('pos.paid') }}</th>
                <th>{{ __('pos.remaining') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['suppliers'] as $s)
            <tr>
                <td>{{ $s->supplier_number ?? '#'.$s->id }}</td>
                <td class="fw-bold">{{ $s->name }}</td>
                <td>{{ $s->email ?? '-' }}</td>
                <td>{{ $s->address ?? '-' }}</td>
                <td>{{ number_format($s->total_purchases, 2) }}</td>
                <td>{{ number_format($s->total_paid, 2) }}</td>
                <td class="text-danger">{{ number_format($s->total_remaining, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

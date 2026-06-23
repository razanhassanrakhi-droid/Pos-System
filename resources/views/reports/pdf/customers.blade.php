@extends('reports.pdf.layout')

@section('title', __('pos.customer_report'))

@section('content')
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>{{ __('pos.total_customers') }}:</strong> {{ $report['total_customers'] }}</td>
                <td><strong>{{ __('pos.collection_rate') }}:</strong> {{ number_format($report['collection_rate'], 1) }}%</td>
            </tr>
            <tr>
                <td><strong>{{ __('pos.total_purchases') }}:</strong> {{ number_format($report['total_purchases'], 2) }} {{ $setting->currency }}</td>
                <td><strong>{{ __('pos.total_remaining') }}:</strong> {{ number_format($report['total_remaining'], 2) }} {{ $setting->currency }}</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>{{ __('pos.customer_id') }}</th>
                <th>{{ __('pos.customer_name') }}</th>
                <th>{{ __('pos.phone') }}</th>
                <th>{{ __('pos.email') }}</th>
                <th>{{ __('pos.address') }}</th>
                <th>{{ __('pos.visits') }}</th>
                <th>{{ __('pos.total_purchases') }}</th>
                <th>{{ __('pos.paid') }}</th>
                <th>{{ __('pos.balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['customers'] as $c)
            <tr>
                <td>#{{ $c->id }}</td>
                <td class="fw-bold">{{ $c->name }}</td>
                <td>{{ $c->phone }}</td>
                <td>{{ $c->email ?? '-' }}</td>
                <td>{{ $c->address ?? '-' }}</td>
                <td>{{ $c->visits }}</td>
                <td>{{ number_format($c->total_purchases, 2) }}</td>
                <td>{{ number_format($c->total_paid, 2) }}</td>
                <td class="text-danger">{{ number_format($c->balance, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

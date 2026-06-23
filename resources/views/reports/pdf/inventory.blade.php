@extends('reports.pdf.layout')

@section('title', __('pos.inventory_report'))

@section('content')
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>{{ __('pos.total_products') }}:</strong> {{ $report['total_products'] }}</td>
                <td><strong>{{ __('pos.inventory_value') }}:</strong> {{ number_format($report['inventory_value'], 2) }} {{ $setting->currency }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('pos.low_stock_items') }}:</strong> {{ $report['low_stock_count'] }}</td>
                <td><strong>{{ __('pos.expired_products') }}:</strong> {{ $report['expired_count'] }}</td>
            </tr>
        </table>
    </div>

    <h3 style="margin-top: 20px;">{{ __('pos.current_stock_status') }}</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th>{{ __('pos.product') }}</th>
                <th>{{ __('pos.stock_quantity') }}</th>
                <th>{{ __('pos.minimum_stock') }}</th>
                <th>{{ __('pos.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['products'] as $p)
            <tr>
                <td class="fw-bold">{{ $p->name }}</td>
                <td>{{ $p->current_stock }}</td>
                <td>{{ $p->minimum_stock }}</td>
                <td>
                    @if($p->current_stock <= 0) {{ __('pos.out_of_stock') }}
                    @elseif($p->current_stock <= $p->minimum_stock) {{ __('pos.low_stock') }}
                    @else {{ __('pos.sufficient') }} @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

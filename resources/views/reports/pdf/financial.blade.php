@extends('reports.pdf.layout')

@section('title', __('pos.profit_loss_summary'))

@section('content')
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>{{ __('pos.total_sales') }}:</strong> {{ number_format($report['total_sales'], 2) }} {{ $setting->currency }}</td>
                <td><strong>{{ __('pos.total_purchases') }}:</strong> {{ number_format($report['total_purchases'], 2) }} {{ $setting->currency }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('pos.total_expenses') }}:</strong> {{ number_format($report['total_expenses'], 2) }} {{ $setting->currency }}</td>
                <td style="font-size: 1.2em; font-weight: bold;"><strong>{{ __('pos.net_profit') }}:</strong> {{ number_format($report['net_profit'], 2) }} {{ $setting->currency }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">{{ __('pos.expense_breakdown') }}</h3>
        <table class="report-table">
            <thead>
                <tr>
                    <th>{{ __('pos.category') }}</th>
                    <th>{{ __('pos.total_amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['expense_breakdown'] as $eb)
                <tr>
                    <td>{{ $eb->type }}</td>
                    <td class="fw-bold">{{ number_format($eb->total, 2) }} {{ $setting->currency }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa;">
                    <td><strong>{{ __('pos.total') }}</strong></td>
                    <td class="fw-bold">{{ number_format($report['total_expenses'], 2) }} {{ $setting->currency }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div style="margin-top: 40px; padding: 20px; background-color: #f8f9fa; border-radius: 5px;">
        <h4 style="margin-top: 0;">{{ __('pos.financial_summary') }}</h4>
        <p>{{ __('pos.net_profit_calculation_note') }}</p>
    </div>
@endsection

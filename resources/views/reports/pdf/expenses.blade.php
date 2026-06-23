@extends('reports.pdf.layout')

@section('title', __('pos.expenses_report'))

@section('content')
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td><strong>{{ __('pos.total_expenses') }}:</strong> {{ number_format($report['total_expenses'], 2) }} {{ $setting->currency }}</td>
                <td><strong>{{ __('pos.expense_count') }}:</strong> {{ $report['expense_count'] }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('pos.average_expense') }}:</strong> {{ number_format($report['average_expense'], 2) }} {{ $setting->currency }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>{{ __('pos.date') }}</th>
                <th>{{ __('pos.category') }}</th>
                <th>{{ __('pos.amount') }}</th>
                <th>{{ __('pos.description') }}</th>
                <th>{{ __('pos.branch') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['expenses'] as $e)
            <tr>
                <td>{{ $e->expense_date }}</td>
                <td>{{ $e->type }}</td>
                <td class="fw-bold">{{ number_format($e->amount, 2) }}</td>
                <td><small>{{ $e->description_ar ?: $e->description_en }}</small></td>
                <td>{{ $e->branch->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@extends('layouts.app')

@section('title', __('pos.manage', ['page' => __('pos.daily_expenses')]))

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-cash-coin me-2 text-primary"></i>{{ __('pos.daily_expenses') }}</h5>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> {{ __('pos.add') }}
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="expensesTable" class="table table-hover table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('pos.expense_type') }}</th>
                        <th>{{ __('pos.amount') }} ({{ $setting->currency }})</th>
                        <th>{{ __('pos.description') }}</th>
                        <th>{{ __('pos.expense_date') }}</th>
                        <th>{{ __('pos.branch') }}</th>
                        <th>{{ __('pos.status') }}</th>
                        <th>{{ __('pos.created_by') }}</th>
                        <th>{{ __('pos.created_at') }}</th>
                        <th class="text-center">{{ __('pos.settings') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ __('pos.' . $expense->type) }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ app()->getLocale() == 'ar' ? $expense->description_ar : $expense->description_en }}</td>
                        <td>{{ $expense->expense_date }}</td>
                        <td>{{ $expense->branch->name ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $expense->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $expense->status_label }}
                            </span>
                        </td>
                        <td>{{ $expense->user->full_name ?? '-' }}</td>
                        <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('pos.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('pos.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#expensesTable').DataTable({
            language: {
                url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
            }
        });
    });
</script>
@endpush

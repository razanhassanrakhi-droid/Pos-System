@extends('layouts.app')

@section('title', __('pos.manage', ['page' => __('pos.branches')]))

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-building me-2 text-primary"></i>{{ __('pos.branches') }}</h5>
        <a href="{{ route('branches.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> {{ __('pos.add') }}
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="branchesTable" class="table table-hover table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('pos.branch_code') }}</th>
                        <th>{{ __('pos.branch_name') }}</th>
                        <th>{{ __('pos.city') }}</th>
                        <th>{{ __('pos.phone') }}</th>
                        <th>{{ __('pos.address') }}</th>
                        <th>{{ __('pos.is_active') }}</th>
                        <th>{{ __('pos.created_at') }}</th>
                        <th class="text-center">{{ __('pos.settings') }}</th>
                    </tr>
                </thead>
<tbody>
    @foreach($branches as $branch)
        <tr>
            <td>{{ $branch->code }}</td>
            <td>{{ $branch->getTranslation('name') }}</td>
            <td>{{ $branch->getTranslation('city') }}</td>
            <td>{{ $branch->phone }}</td>
            <td>{{ $branch->getTranslation('address') }}</td>
            <td>
                @if($branch->is_active)
                    <span class="badge bg-success">{{ __('pos.active') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('pos.inactive') }}</span>
                @endif
            </td>
            <td>{{ $branch->created_at->format('Y-m-d') }}</td>
            <td class="text-center">
                <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل انت متأكد من الحذف؟')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
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
        $('#branchesTable').DataTable({
            language: {
                url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
            }
        });
    });
</script>
@endpush

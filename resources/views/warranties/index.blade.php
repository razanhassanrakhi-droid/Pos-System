@extends('layouts.app')

@section('title', __('pos.warranty_management'))

@section('content')
<div class="card shadow-sm border-0 mb-4 rounded-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom border-light">
        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-primary"></i>{{ __('pos.warranty_management') }}</h5>
    </div>
    <div class="card-body p-4">
        <!-- Search and Filter -->
        <form action="{{ route('warranties.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('pos.search_by_serial_product_customer') }}" value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('pos.all_statuses') }}</option>
                    <option value="ACTIVE" {{ $status == 'ACTIVE' ? 'selected' : '' }}>{{ __('pos.active_warranty') }}</option>
                    <option value="EXPIRED" {{ $status == 'EXPIRED' ? 'selected' : '' }}>{{ __('pos.expired_warranty') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 shadow-sm rounded-pill">
                    <i class="bi bi-filter me-1"></i> {{ __('pos.filter') }}
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('warranties.index') }}" class="btn btn-outline-secondary w-100 shadow-sm rounded-pill">
                    {{ __('pos.clear') }}
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('pos.serial_number') }}</th>
                        <th>{{ __('pos.product_name') }}</th>
                        <th>{{ __('pos.customer_name') }}</th>
                        <th>{{ __('pos.warranty_start_date') }}</th>
                        <th>{{ __('pos.warranty_end_date') }}</th>
                        <th>{{ __('pos.status') }}</th>
                        <th class="text-center">{{ __('pos.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warranties as $warranty)
                        <tr>
                            <td>{{ $warranty->id }}</td>
                            <td><code class="text-primary fw-bold text-uppercase">{{ $warranty->serial_number ?: '-' }}</code></td>
                            <td>{{ $warranty->product->name }}</td>
                            <td>{{ $warranty->customer->name ?? __('pos.walk_in_customer') }}</td>
                            <td>{{ $warranty->warranty_start_date->format('Y-m-d') }}</td>
                            <td>{{ $warranty->warranty_end_date->format('Y-m-d') }}</td>
                            <td>
                                @if($warranty->is_active)
                                    <span class="badge bg-success shadow-sm rounded-pill px-3">{{ __('pos.active_warranty') }}</span>
                                @else
                                    <span class="badge bg-danger shadow-sm rounded-pill px-3">{{ __('pos.expired_warranty') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('warranties.print', $warranty->id) }}" class="btn btn-outline-primary btn-sm shadow-sm rounded-pill px-3" target="_blank">
                                    <i class="bi bi-printer me-1"></i> {{ __('pos.print_warranty_card') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-x fs-1 d-block mb-3"></i>
                                {{ __('pos.no_warranties_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $warranties->links() }}
        </div>
    </div>
</div>
@endsection

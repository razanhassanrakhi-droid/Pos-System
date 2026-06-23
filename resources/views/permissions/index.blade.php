@extends('layouts.app')

@section('title', __('pos.user_permissions'))

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2 text-primary"></i>{{ __('pos.user_permissions') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="permissionsTable" class="table table-hover table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('pos.username') }}</th>
                        <th>{{ __('pos.full_name') }}</th>
                        <th>{{ __('pos.role') }}</th>
                        <th>{{ __('pos.permissions') }}</th>
                        <th class="text-center">{{ __('pos.settings') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    {{ substr($user->full_name, 0, 2) }}
                                </div>
                                {{ $user->full_name }}
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                                {{ $user->role == 'admin' ? __('pos.admin') : __('pos.employee') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $perms = $user->getPermissionNames();
                            @endphp
                            @if($user->role == 'admin')
                                <span class="badge bg-info">{{ __('pos.all_permissions') }}</span>
                            @else
                                @forelse($perms->take(5) as $perm)
                                    <span class="badge bg-light text-dark border me-1">{{ __('pos.' . $perm) }}</span>
                                @empty
                                    <span class="text-muted small">-</span>
                                @endforelse
                                @if($perms->count() > 5)
                                    <span class="text-muted small">+{{ $perms->count() - 5 }} {{ __('pos.more') }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('permissions.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('pos.edit_permissions') }}">
                                <i class="bi bi-shield-check me-1"></i> {{ __('pos.edit_permissions') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#permissionsTable').DataTable({
            paging: false,
            info: false,
            language: {
                url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
            }
        });
    });
</script>
@endpush

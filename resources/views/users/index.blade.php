@extends('layouts.app')

@section('title', __('pos.manage', ['page' => __('pos.users')]))

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-primary"></i>{{ __('pos.users') }}</h5>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> {{ __('pos.add') }}
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="usersTable" class="table table-hover table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('pos.username') }}</th>
                        <th>{{ __('pos.full_name') }}</th>
                        <th>{{ __('pos.role') }}</th>
                        <th>{{ __('pos.assigned_branches') }}</th>
                        <th>{{ __('pos.is_active') }}</th>
                        <th>{{ __('pos.created_at') }}</th>
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
                                    {{ mb_substr($user->full_name, 0, 2) }}
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
                            @if($user->role == 'admin')
                                <span class="badge bg-info">{{ __('pos.all_branches') }}</span>
                            @else
                                @forelse($user->branches as $branch)
                                    <span class="badge bg-light text-dark border me-1">{{ $branch->getTranslation('name') }}</span>
                                @empty
                                    <span class="text-muted small">-</span>
                                @endforelse
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">{{ __('pos.active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('pos.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('pos.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') }}');">
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
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            paging: false, // Using Laravel pagination
            info: false,
            language: {
                url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
            }
        });
    });
</script>
@endpush

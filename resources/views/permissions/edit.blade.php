@extends('layouts.app')

@section('title', __('pos.edit_permissions'))

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-shield-check me-2 text-primary"></i>{{ __('pos.edit_permissions_for') }}: {{ $user->full_name }}
        </h5>
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> {{ __('pos.back') }}
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('permissions.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                @foreach($permissions as $group => $groupPermissions)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border bg-light">
                        <div class="card-header bg-white fw-bold">
                            {{ __('pos.' . $group) }}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($groupPermissions as $permission)
                                <div class="col-6 mb-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="{{ $permission->name }}" 
                                               id="perm_{{ $permission->id }}"
                                               {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}
                                               {{ $user->role == 'admin' ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ __('pos.' . $permission->name) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($user->role == 'admin')
            <div class="alert alert-info">
                {{ __('pos.admin_all_permissions_info') }}
            </div>
            @endif

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary px-5" {{ $user->role == 'admin' ? 'disabled' : '' }}>
                    <i class="bi bi-save me-2"></i> {{ __('pos.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

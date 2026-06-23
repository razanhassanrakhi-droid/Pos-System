@extends('layouts.app')

@section('title', __('pos.change_password'))

@section('content')
<div class="card shadow-sm border-0 max-w-600 mx-auto" style="max-width: 600px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-key me-2 text-primary"></i>{{ __('pos.change_password') }}</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('settings.password.update') }}" method="POST">
            @csrf
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4">
                <label for="old_password" class="form-label fw-semibold">{{ __('pos.old_password') }} <span class="text-danger">*</span></label>
                <input type="password" class="form-control @error('old_password') is-invalid @enderror" id="old_password" name="old_password" required>
                @error('old_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4 d-flex align-items-center">
                <hr class="flex-grow-1">
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label fw-semibold">{{ __('pos.new_password') }} <span class="text-danger">*</span></label>
                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                @error('new_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label fw-semibold">{{ __('pos.confirm_password') }} <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="confirm_password" name="new_password_confirmation" required>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ __('pos.save') }}</button>
                <a href="{{ route('settings.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ __('pos.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection

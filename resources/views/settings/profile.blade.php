@extends('layouts.app')

@section('title', __('pos.profile'))

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-4 border-0 text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        @php
                            $names = explode(' ', $user->full_name);
                            $initials = '';
                            foreach ($names as $name) {
                                $initials .= strtoupper(substr($name, 0, 1));
                            }
                        @endphp
                        {{ substr($initials, 0, 2) }}
                    </div>
                    <h4 class="fw-bold mb-1">{{ $user->full_name }}</h4>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                        {{ __('pos.' . $user->role) }}
                    </span>
                </div>
                
                <div class="card-body p-4 pt-0">
                    <hr class="opacity-10 mb-4">
                    
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">{{ __('pos.username') }}</label>
                            <div class="fw-semibold">{{ $user->username }}</div>
                        </div>
                        
                        <div class="col-sm-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">{{ __('pos.email') }}</label>
                            <div class="fw-semibold">{{ $user->email ?? '-' }}</div>
                        </div>
                        
                        <div class="col-sm-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">{{ __('pos.phone') }}</label>
                            <div class="fw-semibold">{{ $user->phone ?? '-' }}</div>
                        </div>
                        
                        <div class="col-sm-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">{{ __('pos.branches') }}</label>
                            <div>
                                @forelse($user->branches as $branch)
                                    <span class="badge bg-light text-dark border me-1">{{ $branch->getTranslation('name') }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 d-grid gap-2">
                        <a href="{{ route('settings.password') }}" class="btn btn-primary py-2 rounded-3 fw-bold">
                            <i class="bi bi-key me-2"></i>{{ __('pos.change_password') }}
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-light py-2 rounded-3">
                            {{ __('pos.back_to_list') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

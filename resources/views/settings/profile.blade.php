@extends('layouts.app')

@section('title', __('pos.profile'))

@push('styles')
<style>
    /* Premium layout design */
    .pm-card-premium {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 24px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .pm-modal-header-premium {
        background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important;
        padding: 40px 32px !important;
        position: relative;
        overflow: hidden;
        border-bottom: none !important;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .pm-modal-header-premium::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
        background-size: 20px 20px;
        pointer-events: none;
    }
    .pm-modal-header-glow {
        position: absolute;
        border-radius: 50%;
        filter: blur(50px);
        pointer-events: none;
    }
    .pm-modal-header-glow-1 { width: 180px; height: 180px; background: rgba(99, 102, 241, 0.25); top: -60px; right: -40px; }
    .pm-modal-header-glow-2 { width: 140px; height: 140px; background: rgba(59, 130, 246, 0.18); bottom: -50px; left: -30px; }
    
    .sp-avatar-lg {
        width: 80px; height: 80px; border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: #ffffff; font-weight: 800; font-size: 1.8rem;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 20px rgba(99,102,241,0.3);
        border: 2px solid rgba(255,255,255,0.2);
        margin-bottom: 16px; z-index: 2; position: relative;
    }

    .sp-name-lg {
        color: #ffffff; font-size: 1.3rem; font-weight: 800; margin-bottom: 6px; z-index: 2; position: relative;
    }
    
    .badge-premium-admin {
        background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
        color: #fff; font-weight: 700; font-size: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 114, 255, 0.2);
        z-index: 2; position: relative;
    }
    .badge-premium-employee {
        background: linear-gradient(135deg, #78ffd6 0%, #a8ff78 100%);
        color: #1e3a1e; font-weight: 700; font-size: 0.75rem;
        z-index: 2; position: relative;
    }

    /* Info rows */
    .profile-info-row {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 16px 20px;
        transition: all 0.2s ease;
    }
    .profile-info-row:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
    }

    .branch-badge {
        background: #e2e8f0;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-weight: 600;
        font-size: 0.75rem;
    }

    /* Action buttons */
    .btn-premium-save {
        background: linear-gradient(135deg, #6d5bff 0%, #8b5cf6 100%);
        border: none;
        border-radius: 14px;
        padding: 12px 36px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #fff !important;
        box-shadow: 0 4px 16px rgba(109, 91, 255, 0.3);
        transition: all 0.25s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-premium-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(109, 91, 255, 0.4);
    }
    .btn-premium-cancel {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        padding: 12px 36px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #64748b !important;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .btn-premium-cancel:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    /* Dark Mode Overrides */
    html[data-app-theme="dark"] .pm-card-premium { background: #0f172a; border-color: #334155; }
    html[data-app-theme="dark"] .profile-info-row { background: #1e293b; border-color: #334155; }
    html[data-app-theme="dark"] .profile-info-row:hover { background: #0f172a; }
    html[data-app-theme="dark"] .info-value { color: #f8fafc; }
    html[data-app-theme="dark"] .info-label { color: #94a3b8; }
    html[data-app-theme="dark"] .branch-badge { background: #0f172a; border-color: #334155; color: #94a3b8; }
    html[data-app-theme="dark"] .btn-premium-cancel { background: transparent; border-color: #334155; color: #94a3b8 !important; }
    html[data-app-theme="dark"] .btn-premium-cancel:hover { background: rgba(255,255,255,0.05); color: #fff !important; }
</style>
@endpush

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="pm-card-premium">
                {{-- Premium Header --}}
                <div class="pm-modal-header-premium p-4">
                    <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                    <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                    
                    @php
                        $names = explode(' ', $user->full_name);
                        $initials = '';
                        foreach ($names as $name) {
                            $initials .= mb_strtoupper(mb_substr($name, 0, 1));
                        }
                        $displayInitials = mb_substr($initials, 0, 2);
                    @endphp
                    
                    <div class="sp-avatar-lg">
                        {{ $displayInitials }}
                    </div>
                    <h3 class="sp-name-lg">{{ $user->full_name }}</h3>
                    <span class="badge rounded-pill {{ $user->role == 'admin' ? 'badge-premium-admin' : 'badge-premium-employee' }} px-3 py-1.5 fs-7">
                        {{ $user->role == 'admin' ? __('pos.admin') : __('pos.employee') }}
                    </span>
                </div>

                {{-- Card Content Body --}}
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="profile-info-row">
                                <div class="info-label">{{ __('pos.username') }}</div>
                                <div class="info-value">{{ $user->username }}</div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="profile-info-row">
                                <div class="info-label">{{ __('pos.email') }}</div>
                                <div class="info-value">{{ $user->email ?? '—' }}</div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="profile-info-row">
                                <div class="info-label">{{ __('pos.phone') }}</div>
                                <div class="info-value" style="direction: ltr; text-align: start;">{{ $user->phone ?? '—' }}</div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="profile-info-row">
                                <div class="info-label">{{ __('pos.branches') }}</div>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @forelse($user->branches as $branch)
                                        <span class="badge branch-badge rounded-pill px-2.5 py-1.5">{{ $branch->getTranslation('name') }}</span>
                                    @empty
                                        <span class="text-muted small">—</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions block --}}
                    <div class="mt-5 d-flex flex-column gap-2">
                        <a href="{{ route('settings.password') }}" class="btn-premium-save text-decoration-none">
                            <i class="bi bi-key"></i> {{ __('pos.change_password') }}
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn-premium-cancel">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('pos.back_to_list') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

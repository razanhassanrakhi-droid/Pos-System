@extends('layouts.app')

@section('title', __('pos.change_password'))

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
        padding: 24px 32px !important;
        position: relative;
        overflow: hidden;
        border-bottom: none !important;
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
    .pm-modal-header-glow-1 { width: 180px; height: 180px; background: rgba(99, 102, 241, 0.2); top: -60px; right: -40px; }
    .pm-modal-header-glow-2 { width: 140px; height: 140px; background: rgba(59, 130, 246, 0.15); bottom: -50px; left: -30px; }
    
    .pm-modal-icon-premium {
        width: 48px; height: 48px; border-radius: 14px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #a78bfa; font-size: 1.35rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 2; position: relative;
    }
    .pm-modal-title-premium {
        color: #ffffff; font-weight: 800; font-size: 1.15rem; margin-bottom: 2px;
        z-index: 2; position: relative;
    }
    .pm-modal-sub-premium {
        color: #94a3b8; font-size: 0.8rem; font-weight: 500;
        z-index: 2; position: relative;
    }

    /* Input styling */
    .saas-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }
    .input-group-saas {
        display: flex;
        align-items: stretch;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .input-group-saas:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .input-group-text-saas {
        background: #f8fafc;
        color: #64748b;
        border: none;
        padding: 0 16px;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }
    .saas-input {
        flex-grow: 1;
        border: none;
        padding: 11px 16px;
        font-size: 0.88rem;
        color: #0f172a;
        background: transparent;
        outline: none;
        width: 100%;
    }

    /* Save actions buttons */
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
    html[data-app-theme="dark"] .input-group-saas { background: #1e293b; border-color: #334155; }
    html[data-app-theme="dark"] .input-group-text-saas { background: #0f172a; color: #94a3b8; }
    html[data-app-theme="dark"] .saas-input { color: #f8fafc; }
    html[data-app-theme="dark"] .saas-label { color: #cbd5e1; }
    html[data-app-theme="dark"] .btn-premium-cancel { background: transparent; border-color: #334155; color: #94a3b8 !important; }
    html[data-app-theme="dark"] .btn-premium-cancel:hover { background: rgba(255,255,255,0.05); color: #fff !important; }
</style>
@endpush

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="pm-card-premium">
                {{-- Premium Header Layout --}}
                <div class="pm-modal-header-premium p-4">
                    <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                    <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                    <div class="d-flex align-items-center gap-3 position-relative" style="z-index: 2; width: 100%;">
                        <div class="pm-modal-icon-premium">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <div>
                            <h4 class="pm-modal-title-premium">{{ __('pos.change_password') }}</h4>
                            <p class="pm-modal-sub-premium mb-0">{{ app()->getLocale() == 'ar' ? 'تحديث كلمة المرور الحالية لحماية الحساب' : 'Update current password to secure account' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="p-4">
                    <form action="{{ route('settings.password.update') }}" method="POST">
                        @csrf
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: var(--saas-success);">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                    <div>{{ session('success') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: var(--saas-danger);">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="saas-label">{{ __('pos.old_password') }} <span class="text-danger">*</span></label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-shield-lock-fill"></i></div>
                                <input type="password" class="saas-input border-start-0 border-end-0 ps-0" id="old_password" name="old_password" required placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل كلمة المرور القديمة' : 'Enter old password' }}">
                                <div class="input-group-text-saas border-start-0 bg-transparent" style="cursor: pointer;" onclick="togglePassword('old_password')"><i class="bi bi-eye text-muted" id="old_password-icon"></i></div>
                            </div>
                            @error('old_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="mb-4">
                            <label class="saas-label">{{ __('pos.new_password') }} <span class="text-danger">*</span></label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-key-fill"></i></div>
                                <input type="password" class="saas-input border-start-0 border-end-0 ps-0" id="new_password" name="new_password" required placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل كلمة المرور الجديدة' : 'Enter new password' }}">
                                <div class="input-group-text-saas border-start-0 bg-transparent" style="cursor: pointer;" onclick="togglePassword('new_password')"><i class="bi bi-eye text-muted" id="new_password-icon"></i></div>
                            </div>
                            <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.78rem; opacity: 0.85;">
                                <i class="bi bi-info-circle"></i>
                                <span>{{ app()->getLocale() == 'ar' ? 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.' : 'Password must be at least 8 characters long.' }}</span>
                            </div>
                            @error('new_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="saas-label">{{ __('pos.confirm_password') }} <span class="text-danger">*</span></label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-check-circle-fill"></i></div>
                                <input type="password" class="saas-input border-start-0 border-end-0 ps-0" id="confirm_password" name="new_password_confirmation" required placeholder="{{ app()->getLocale() == 'ar' ? 'تأكيد كلمة المرور الجديدة' : 'Confirm new password' }}">
                                <div class="input-group-text-saas border-start-0 bg-transparent" style="cursor: pointer;" onclick="togglePassword('confirm_password')"><i class="bi bi-eye text-muted" id="confirm_password-icon"></i></div>
                            </div>
                        </div>

                        <div class="mt-5 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn-premium-save">
                                <i class="bi bi-check-circle-fill"></i> {{ __('pos.save') }}
                            </button>
                            <a href="{{ route('settings.profile') }}" class="btn-premium-cancel">
                                {{ __('pos.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection

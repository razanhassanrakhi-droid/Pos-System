@extends('layouts.app')

@section('title', __('pos.edit_permissions'))

@push('styles')
<style>
    /* Glassmorphic/Premium layout rules */
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
    .pm-modal-header-glow-1 { width: 180px; height: 180px; background: rgba(99, 102, 241, 0.25); top: -60px; right: -40px; }
    .pm-modal-header-glow-2 { width: 140px; height: 140px; background: rgba(59, 130, 246, 0.18); bottom: -50px; left: -30px; }
    
    .pm-modal-icon-premium {
        width: 46px; height: 46px; border-radius: 14px;
        background: rgba(255,255,255,.1); border: 1.5px solid rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; color: #60a5fa !important; flex-shrink: 0; backdrop-filter: blur(8px);
    }
    .pm-modal-title-premium { font-size: 1.15rem; font-weight: 800; color: #fff; margin: 0; }
    .pm-modal-sub-premium { font-size: .76rem; color: #93c5fd !important; margin: 2px 0 0; font-weight: 500; }

    /* Glass styled permission groups cards */
    .glass-perm-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.015);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .glass-perm-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.05);
        border-color: rgba(99, 102, 241, 0.2);
    }
    .glass-perm-header {
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
        font-weight: 700;
        color: #1e1b4b; /* Deep Indigo */
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .glass-perm-header i {
        color: #6d5bff; /* Purple Accent */
        font-size: 1.1rem;
    }

    /* Custom form switch coloring */
    .form-check-input:checked {
        background-color: #6d5bff !important;
        border-color: #6d5bff !important;
        box-shadow: 0 0 0 0.25rem rgba(109, 91, 255, 0.12) !important;
    }
    .form-check-label {
        font-size: 0.88rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        user-select: none;
    }

    /* Back button style */
    .btn-premium-back {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 12px;
        padding: 8px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #fff !important;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-premium-back:hover {
        background: rgba(255,255,255,0.25);
        transform: translateY(-1px);
    }

    /* Save changes button */
    .btn-premium-save {
        background: linear-gradient(135deg, #6d5bff 0%, #8b5cf6 100%);
        border: none;
        border-radius: 14px;
        padding: 12px 36px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #fff;
        box-shadow: 0 4px 16px rgba(109, 91, 255, 0.3);
        transition: all 0.25s;
    }
    .btn-premium-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(109, 91, 255, 0.4);
    }
    .btn-premium-save:disabled {
        background: #cbd5e1;
        box-shadow: none;
        transform: none;
        cursor: not-allowed;
    }

    /* RTL customizations */
    html[dir="rtl"] .ms-custom { margin-right: auto !important; margin-left: 0 !important; }
    html[dir="rtl"] .me-custom { margin-left: auto !important; margin-right: 0 !important; }

    /* Dark Mode Support */
    html[data-app-theme="dark"] .pm-card-premium { background: #0f172a; border-color: #334155; }
    html[data-app-theme="dark"] .glass-perm-card { background: #1e293b; border-color: #334155; }
    html[data-app-theme="dark"] .glass-perm-header { background: #0f172a; border-bottom-color: #334155; color: #f8fafc; }
    html[data-app-theme="dark"] .form-check-label { color: #94a3b8; }
    html[data-app-theme="dark"] .text-dark-theme-white { color: #fff !important; }
    .global-toggle-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }
    html[data-app-theme="dark"] .global-toggle-box {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mb-5">

    <div class="pm-card-premium">
        {{-- Premium Header Layout --}}
        <div class="pm-modal-header-premium p-4">
            <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
            <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
            <div class="d-flex align-items-center gap-3 position-relative w-100" style="z-index: 2;">
                <div class="pm-modal-icon-premium">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h4 class="pm-modal-title-premium">{{ __('pos.edit_permissions_for') }}: {{ $user->full_name }}</h4>
                    <p class="pm-modal-sub-premium mb-0">{{ __('pos.edit_permissions') }}</p>
                </div>
                <div class="ms-custom">
                    <a href="{{ session('permissions_index_url', route('permissions.index')) }}" class="btn-premium-back text-decoration-none">
                        <i class="bi bi-arrow-left"></i> {{ __('pos.back') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="p-4">
            <form action="{{ route('permissions.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Global toggle switch --}}
                <div class="global-toggle-box d-flex justify-content-between align-items-center mb-4 p-3 rounded-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-primary fs-4"></i>
                        <div>
                            <span class="fw-bold d-block text-dark-theme-white">{{ app()->getLocale() == 'ar' ? 'التحكم العام بالصلاحيات' : 'Global Permissions Control' }}</span>
                            <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'تفعيل أو تعطيل كافة الصلاحيات دفعة واحدة' : 'Enable or disable all permissions at once' }}</small>
                        </div>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" id="toggleAllPermissions" {{ $user->role == 'admin' ? 'disabled' : '' }}>
                        <label class="form-check-label fw-semibold ms-2" for="toggleAllPermissions" id="toggleAllLabel">
                            {{ app()->getLocale() == 'ar' ? 'تفعيل الكل' : 'Enable All' }}
                        </label>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($permissions as $group => $groupPermissions)
                    <div class="col-md-6">
                        <div class="glass-perm-card h-100">
                            <div class="glass-perm-header">
                                <i class="bi bi-folder-check"></i>
                                {{ __('pos.' . $group) }}
                            </div>
                            <div class="p-4">
                                <div class="row g-3">
                                    @foreach($groupPermissions as $permission)
                                    <div class="col-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                   value="{{ $permission->name }}" 
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}
                                                   {{ $user->role == 'admin' ? 'disabled' : '' }}>
                                            <label class="form-check-label ms-1" for="perm_{{ $permission->id }}">
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
                <div class="alert alert-info mt-4 rounded-4 border-0 p-3 d-flex align-items-center gap-2" style="background-color: rgba(99, 102, 241, 0.08); color: #4f46e5;">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <span class="fw-semibold">{{ __('pos.admin_all_permissions_info') }}</span>
                </div>
                @endif

                <div class="mt-5 text-center">
                    <button type="submit" class="btn btn-premium-save px-5" {{ $user->role == 'admin' ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle me-1"></i> {{ __('pos.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleAll = document.getElementById('toggleAllPermissions');
        if (!toggleAll) return;

        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
        const enabledCheckboxes = Array.from(checkboxes).filter(cb => !cb.disabled);

        function updateToggleAllState() {
            const checkedCount = enabledCheckboxes.filter(cb => cb.checked).length;
            const label = document.getElementById('toggleAllLabel');
            if (checkedCount === enabledCheckboxes.length && enabledCheckboxes.length > 0) {
                toggleAll.checked = true;
                label.textContent = "{{ app()->getLocale() == 'ar' ? 'تعطيل الكل' : 'Disable All' }}";
            } else {
                toggleAll.checked = false;
                label.textContent = "{{ app()->getLocale() == 'ar' ? 'تفعيل الكل' : 'Enable All' }}";
            }
        }

        // Initialize state
        updateToggleAllState();

        // Listen to master toggle change
        toggleAll.addEventListener('change', function() {
            const isChecked = this.checked;
            enabledCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            document.getElementById('toggleAllLabel').textContent = isChecked ? 
                "{{ app()->getLocale() == 'ar' ? 'تعطيل الكل' : 'Disable All' }}" : 
                "{{ app()->getLocale() == 'ar' ? 'تفعيل الكل' : 'Enable All' }}";
        });

        // Listen to individual checkbox changes
        enabledCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateToggleAllState);
        });
    });
</script>
@endpush

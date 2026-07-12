@extends('layouts.app')

@section('title', __('pos.edit') . ' ' . __('pos.manage', ['page' => __('pos.users')]))

@push('styles')
<style>
    /* Premium Glassmorphism Header */
    .pm-card-premium {
        border: none !important;
        border-radius: 24px !important;
        box-shadow: 0 32px 80px -12px rgba(0,0,0,.12), 0 0 0 1px rgba(226,232,240,.6) !important;
        overflow: hidden;
        background: #ffffff;
    }
    [data-pm-theme="dark"] .pm-card-premium {
        background: #0b1427 !important;
        box-shadow: 0 32px 80px -12px rgba(0,0,0,.5), 0 0 0 1px rgba(0,200,255,0.15) !important;
    }
    .pm-modal-header-premium {
        background: linear-gradient(135deg, #060d1f 0%, #0f172a 60%, #060d1f 100%) !important;
        padding: 24px 28px !important;
        position: relative;
        overflow: hidden;
        border-bottom: none !important;
    }
    .pm-modal-header-premium::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.045) 1px, transparent 1px);
        background-size: 22px 22px;
        pointer-events: none;
    }
    .pm-modal-header-glow {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
    }
    .pm-modal-header-glow-1 {
        width: 220px; height: 220px;
        background: rgba(124,58,237,.25) !important;
        top: -80px; right: -60px;
    }
    .pm-modal-header-glow-2 {
        width: 160px; height: 160px;
        background: rgba(99,102,241,.18) !important;
        bottom: -60px; left: -40px;
    }
    .pm-modal-icon-premium {
        width: 52px; height: 52px;
        border-radius: 16px;
        background: rgba(255,255,255,.1);
        border: 1.5px solid rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        color: #c4b5fd !important;
        flex-shrink: 0;
        backdrop-filter: blur(8px);
    }
    .pm-modal-title-premium {
        font-size: 1.15rem; font-weight: 800;
        color: #fff; margin: 0; letter-spacing: -.3px;
    }
    .pm-modal-sub-premium {
        font-size: .78rem; color: #a5b4fc !important;
        margin: 3px 0 0; font-weight: 500;
    }
    .pm-modal-close-premium {
        width: 36px; height: 36px; border-radius: 10px;
        background: rgba(255,255,255,.08);
        border: 1.5px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.7) !important;
        transition: all 0.2s ease;
    }
    .pm-modal-close-premium:hover {
        background: rgba(255,255,255,.16);
        color: #fff !important; transform: scale(1.05);
    }

    /* Form Styles */
    :root {
        --saas-primary: #6366F1;
        --saas-primary-hover: #4F46E5;
        --saas-primary-light: rgba(99, 102, 241, 0.1);
        --saas-secondary: #8B5CF6;
        --saas-accent: #0EA5E9;
        --saas-bg: #F8FAFC;
        --saas-surface: #FFFFFF;
        --saas-border: #E2E8F0;
        --saas-border-light: #F1F5F9;
        --saas-text-main: #0F172A;
        --saas-text-muted: #64748B;
        --saas-text-light: #94A3B8;
        --saas-shadow-sm: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.025);
        --saas-shadow-md: 0 10px 15px -3px rgba(0,0,0,0.06), 0 4px 6px -4px rgba(0,0,0,0.03);
        --saas-radius-lg: 20px;
        --saas-radius-md: 14px;
        --saas-radius-sm: 10px;
        --saas-success: #10B981;
        --saas-danger: #EF4444;
        --saas-warning: #F59E0B;
    }
    [data-pm-theme="dark"] {
        --saas-bg: #0B1120;
        --saas-surface: #1E293B;
        --saas-border: #334155;
        --saas-border-light: #1E293B;
        --saas-text-main: #F8FAFC;
        --saas-text-muted: #94A3B8;
        --saas-primary-light: rgba(99, 102, 241, 0.2);
    }

    .saas-card {
        background: var(--saas-surface);
        border: 1px solid var(--saas-border);
        border-radius: var(--saas-radius-lg);
        box-shadow: var(--saas-shadow-sm);
        padding: 28px;
        margin-bottom: 20px;
        transition: box-shadow 0.3s ease;
    }
    .saas-card:hover { box-shadow: var(--saas-shadow-md); }
    .saas-card-header {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 20px; padding-bottom: 16px;
        border-bottom: 1px solid var(--saas-border-light);
    }
    .saas-card-icon {
        width: 40px; height: 40px; border-radius: 12px;
        background: var(--saas-primary-light);
        color: var(--saas-primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
    }
    .saas-card-title { font-size: 1.1rem; font-weight: 700; color: var(--saas-text-main); margin: 0; }
    .saas-card-desc { font-size: 0.82rem; color: var(--saas-text-muted); margin: 3px 0 0; }

    .saas-label {
        font-size: 0.85rem; font-weight: 600;
        color: var(--saas-text-main); margin-bottom: 8px; display: block;
    }
    .saas-input {
        background: var(--saas-bg);
        border: 1.5px solid var(--saas-border);
        border-radius: var(--saas-radius-sm);
        padding: 11px 16px; font-size: 0.93rem;
        color: var(--saas-text-main); width: 100%;
        transition: all 0.2s ease;
    }
    .saas-input:focus {
        background: var(--saas-surface);
        border-color: var(--saas-primary);
        box-shadow: 0 0 0 4px var(--saas-primary-light); outline: none;
    }
    .input-group-saas {
        display: flex; align-items: center;
        background: var(--saas-bg);
        border: 1.5px solid var(--saas-border);
        border-radius: var(--saas-radius-sm);
        overflow: hidden; transition: all 0.2s ease;
    }
    .input-group-saas:focus-within {
        background: var(--saas-surface);
        border-color: var(--saas-primary);
        box-shadow: 0 0 0 4px var(--saas-primary-light);
    }
    .input-group-saas .saas-input { border: none; background: transparent; box-shadow: none !important; }
    .input-group-text-saas {
        padding: 11px 16px; color: var(--saas-text-muted);
        font-weight: 500;
        border-left: 1px solid var(--saas-border);
        background: var(--saas-bg);
    }
    html[dir="rtl"] .input-group-text-saas { border-left: none; border-right: 1px solid var(--saas-border); }

    .role-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    @media (max-width: 768px) {
        .role-cards { grid-template-columns: 1fr !important; }
    }
    .role-card {
        border: 2px solid var(--saas-border);
        border-radius: var(--saas-radius-md);
        padding: 20px; cursor: pointer; transition: all 0.2s;
        background: var(--saas-surface); position: relative;
    }
    .role-card:hover { border-color: var(--saas-primary-light); background: var(--saas-bg); }
    .role-card.selected { border-color: var(--saas-primary); background: var(--saas-primary-light); }
    .role-card.selected::after {
        content: '\f26a';
        font-family: 'bootstrap-icons';
        position: absolute; top: 16px; left: 16px;
        color: var(--saas-primary); font-size: 1.25rem;
    }
    html[dir="rtl"] .role-card.selected::after { left: auto; right: 16px; }
    .role-icon {
        width: 48px; height: 48px; border-radius: 12px;
        background: var(--saas-bg);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; color: var(--saas-text-muted); margin-bottom: 12px;
    }
    .role-card.selected .role-icon { color: var(--saas-primary); background: var(--saas-surface); }

    /* Avatar */
    .avatar-upload-zone {
        display: flex; align-items: center; gap: 20px; padding: 20px;
        border: 1.5px dashed var(--saas-border); border-radius: var(--saas-radius-md);
        background: var(--saas-bg); cursor: pointer; transition: all 0.2s;
    }
    .avatar-upload-zone:hover { border-color: var(--saas-primary); background: var(--saas-primary-light); }
    .avatar-preview {
        width: 64px; height: 64px; border-radius: 50%;
        background: var(--saas-border);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; color: var(--saas-text-muted); overflow: hidden;
    }

    /* Branches Grid */
    .branches-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; }
    .branch-card {
        border: 1.5px solid var(--saas-border); border-radius: var(--saas-radius-md);
        padding: 14px; display: flex; align-items: center; gap: 12px;
        transition: all 0.2s; background: var(--saas-surface); cursor: pointer;
    }
    .branch-card:hover { border-color: var(--saas-primary-light); background: var(--saas-bg); }
    .branch-card.selected { border-color: var(--saas-primary); background: var(--saas-primary-light); }
    .branch-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: var(--saas-bg); color: var(--saas-text-muted);
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    }
    .branch-card.selected .branch-icon { color: var(--saas-primary); background: var(--saas-surface); }

    /* Toggle */
    .saas-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
    .saas-switch input { opacity: 0; width: 0; height: 0; }
    .saas-slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--saas-border); transition: .3s; border-radius: 34px;
    }
    .saas-slider:before {
        position: absolute; content: ""; height: 18px; width: 18px;
        left: 3px; bottom: 3px; background-color: white; transition: .3s;
        border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    html[dir="rtl"] .saas-slider:before { left: auto; right: 3px; }
    input:checked + .saas-slider { background-color: var(--saas-success); }
    input:checked + .saas-slider:before { transform: translateX(20px); }
    html[dir="rtl"] input:checked + .saas-slider:before { transform: translateX(-20px); }

    .setting-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px; border: 1px solid var(--saas-border);
        border-radius: var(--saas-radius-md); margin-bottom: 12px;
        background: var(--saas-surface);
    }

    /* Password Strength */
    .pwd-strength { display: flex; gap: 4px; margin-top: 8px; }
    .pwd-bar { height: 4px; flex: 1; border-radius: 99px; background: var(--saas-border); transition: background 0.3s; }
    .pwd-text { font-size: 0.75rem; color: var(--saas-text-muted); margin-top: 4px; }
</style>
@endpush

@section('content')
<div class="pm-card-premium mx-auto" style="max-width: 800px; margin-top: 2rem; margin-bottom: 2rem;">
    {{-- Premium Header --}}
    <div class="pm-modal-header-premium">
        <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
        <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width: 100%;">
            <div class="pm-modal-icon-premium">
                <i class="bi bi-person-gear"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="pm-modal-title-premium">{{ app()->getLocale() == 'ar' ? 'تعديل بيانات المستخدم' : 'Edit User Details' }}</h5>
                <p class="pm-modal-sub-premium">{{ app()->getLocale() == 'ar' ? 'قم بتحديث معلومات الحساب والصلاحيات والفروع.' : 'Update account info, role, and branches.' }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="pm-modal-close-premium d-flex align-items-center justify-content-center text-decoration-none">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </div>

    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" id="editUserForm">
            @csrf
            @method('PUT')

            {{-- Card 1: Personal Info --}}
            <div class="saas-card">
                <div class="saas-card-header">
                    <div class="saas-card-icon"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <h2 class="saas-card-title">{{ app()->getLocale() == 'ar' ? 'المعلومات الشخصية' : 'Personal Information' }}</h2>
                        <p class="saas-card-desc">{{ app()->getLocale() == 'ar' ? 'البيانات الأساسية ومعلومات التواصل الخاصة بالمستخدم.' : 'Basic information and contact details of the user.' }}</p>
                    </div>
                </div>

                {{-- Avatar --}}
                <div class="mb-4">
                    <div class="avatar-upload-zone" onclick="document.getElementById('avatar').click()">
                        <div class="avatar-preview" id="avatarPreview">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="bi bi-person"></i>
                            @endif
                        </div>
                        <div>
                            <h6 style="font-weight:600;color:var(--saas-text-main);margin-bottom:4px;">{{ app()->getLocale() == 'ar' ? 'صورة المستخدم (اختياري)' : 'User Image (Optional)' }}</h6>
                            <p class="text-muted mb-0" style="font-size:0.8rem;">{{ app()->getLocale() == 'ar' ? 'JPG, PNG أو GIF (الحد الأقصى 2MB)' : 'JPG, PNG or GIF (Max 2MB)' }}</p>
                        </div>
                        <button type="button" class="btn btn-outline-secondary ms-auto" style="font-size:0.8rem;" onclick="event.stopPropagation(); document.getElementById('avatar').click()">{{ app()->getLocale() == 'ar' ? 'تغيير الصورة' : 'Change Image' }}</button>
                        <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'الاسم الكامل (عربي)' : 'Full Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <div class="input-group-saas">
                            <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-person text-muted"></i></div>
                            <input type="text" class="saas-input border-start-0 ps-0" name="full_name_ar" value="{{ old('full_name_ar', $user->getTranslation('full_name', 'ar')) }}" required placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل الاسم باللغة العربية' : 'Enter full name in Arabic' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'الاسم الكامل (إنجليزي)' : 'Full Name (English)' }}</label>
                        <div class="input-group-saas">
                            <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-fonts text-muted"></i></div>
                            <input type="text" class="saas-input border-start-0 ps-0" name="full_name_en" value="{{ old('full_name_en', $user->getTranslation('full_name', 'en')) }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل الاسم باللغة الإنجليزية' : 'Enter full name in English' }}">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'اسم المستخدم (عربي)' : 'Username (Arabic)' }} <span class="text-danger">*</span></label>
                        <div class="input-group-saas">
                            <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-at text-muted"></i></div>
                            <input type="text" class="saas-input border-start-0 ps-0" name="username_ar" value="{{ old('username_ar', $user->getTranslation('username', 'ar')) }}" placeholder="{{ app()->getLocale() == 'ar' ? 'اسم المستخدم بالعربية' : 'Username in Arabic' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'اسم المستخدم (إنجليزي)' : 'Username (English)' }} <span class="text-danger">*</span></label>
                        <div class="input-group-saas">
                            <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-at text-muted"></i></div>
                            <input type="text" class="saas-input border-start-0 ps-0" name="username_en" value="{{ old('username_en', $user->getTranslation('username', 'en')) }}" placeholder="{{ app()->getLocale() == 'ar' ? 'اسم المستخدم بالإنجليزية' : 'Username in English' }}">
                        </div>
                    </div>
                </div>
                    <div class="col-md-6">
                        <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                        <div class="input-group-saas">
                            <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-envelope text-muted"></i></div>
                            <input type="email" class="saas-input border-start-0 ps-0" name="email" value="{{ old('email', $user->email) }}" placeholder="name@company.com">
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                        <div class="input-group-saas">
                            <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-telephone text-muted"></i></div>
                            <input type="tel" class="saas-input border-start-0 ps-0" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل رقم الهاتف' : 'Enter phone number' }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Security --}}
            <div class="saas-card">
                <div class="saas-card-header">
                    <div class="saas-card-icon" style="background:rgba(16,185,129,0.1);color:var(--saas-success);"><i class="bi bi-shield-lock"></i></div>
                    <div>
                        <h2 class="saas-card-title">{{ app()->getLocale() == 'ar' ? 'إعدادات الأمان' : 'Security Settings' }}</h2>
                        <p class="saas-card-desc">{{ app()->getLocale() == 'ar' ? 'اتركها فارغة للاحتفاظ بكلمة المرور الحالية.' : 'Leave blank to keep the current password.' }}</p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }}</label>
                        <div class="input-group-saas">
                            <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-key text-muted"></i></div>
                            <input type="password" class="saas-input border-start-0 border-end-0 ps-0" id="password" name="password" placeholder="{{ app()->getLocale() == 'ar' ? 'اتركها فارغة للإبقاء على الحالية' : 'Leave blank to keep current password' }}">
                            <div class="input-group-text-saas border-start-0 bg-transparent" style="cursor:pointer;" onclick="togglePassword('password')"><i class="bi bi-eye text-muted" id="password-icon"></i></div>
                        </div>
                        <div class="pwd-strength">
                            <div class="pwd-bar" id="pwd-bar-1"></div>
                            <div class="pwd-bar" id="pwd-bar-2"></div>
                            <div class="pwd-bar" id="pwd-bar-3"></div>
                            <div class="pwd-bar" id="pwd-bar-4"></div>
                        </div>
                        <div class="pwd-text" id="pwd-text"></div>
                        <div class="text-muted small mt-2 d-flex align-items-center gap-1" style="font-size: 0.78rem; opacity: 0.85;">
                            <i class="bi bi-info-circle"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.' : 'Password must be at least 8 characters long.' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}</label>
                        <div class="input-group-saas">
                            <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-key-fill text-muted"></i></div>
                            <input type="password" class="saas-input border-start-0 border-end-0 ps-0" id="password_confirmation" name="password_confirmation" placeholder="{{ app()->getLocale() == 'ar' ? 'أعد إدخال كلمة المرور' : 'Re-enter password' }}">
                            <div class="input-group-text-saas border-start-0 bg-transparent" style="cursor:pointer;" onclick="togglePassword('password_confirmation')"><i class="bi bi-eye text-muted" id="password_confirmation-icon"></i></div>
                        </div>
                    </div>
                </div>

                <div class="setting-item">
                    <div>
                        <h6 class="fw-bold mb-1" style="color:var(--saas-text-main);">{{ app()->getLocale() == 'ar' ? 'تفعيل الحساب' : 'Activate Account' }}</h6>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">{{ app()->getLocale() == 'ar' ? 'سيتمكن المستخدم من تسجيل الدخول.' : 'User will be allowed to log in.' }}</p>
                    </div>
                    <label class="saas-switch">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <span class="saas-slider"></span>
                    </label>
                </div>
            </div>

            {{-- Card 3: Role --}}
            <div class="saas-card">
                <div class="saas-card-header">
                    <div class="saas-card-icon" style="background:rgba(245,158,11,0.1);color:var(--saas-warning);"><i class="bi bi-person-gear"></i></div>
                    <div>
                        <h2 class="saas-card-title">{{ app()->getLocale() == 'ar' ? 'الدور والصلاحيات' : 'Role & Permissions' }}</h2>
                        <p class="saas-card-desc">{{ app()->getLocale() == 'ar' ? 'حدد مستوى وصول المستخدم وصلاحياته في النظام.' : 'Specify user system access and permissions.' }}</p>
                    </div>
                </div>

                <label class="saas-label mb-3">{{ app()->getLocale() == 'ar' ? 'اختر دور المستخدم' : 'Select User Role' }} <span class="text-danger">*</span></label>
                <input type="hidden" name="role" id="role_input" value="{{ old('role', $user->role) }}">
                <div class="role-cards">
                    <div class="role-card {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}" onclick="selectRole('admin')" id="role-admin">
                        <div class="role-icon"><i class="bi bi-shield-fill"></i></div>
                        <h5 class="fw-bold mb-1" style="color:var(--saas-text-main);">{{ app()->getLocale() == 'ar' ? 'مدير نظام (Admin)' : 'System Administrator (Admin)' }}</h5>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">{{ app()->getLocale() == 'ar' ? 'صلاحيات كاملة للتحكم بالنظام وجميع الفروع.' : 'Full administrative access to the system and all branches.' }}</p>
                    </div>
                    <div class="role-card {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}" onclick="selectRole('employee')" id="role-employee">
                        <div class="role-icon"><i class="bi bi-person-badge"></i></div>
                        <h5 class="fw-bold mb-1" style="color:var(--saas-text-main);">{{ app()->getLocale() == 'ar' ? 'موظف (Employee)' : 'Employee' }}</h5>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">{{ app()->getLocale() == 'ar' ? 'وصول محدود حسب الصلاحيات والفروع المحددة.' : 'Limited access based on selected permissions and assigned branches.' }}</p>
                    </div>
                </div>
            </div>

            {{-- Card 4: Branches --}}
            <div class="saas-card">
                <div class="saas-card-header mb-0" style="border-bottom:none;padding-bottom:0;">
                    <div class="saas-card-icon" style="background:rgba(14,165,233,0.1);color:var(--saas-accent);"><i class="bi bi-shop"></i></div>
                    <div>
                        <h2 class="saas-card-title">{{ app()->getLocale() == 'ar' ? 'تخصيص الفروع' : 'Assign Branches' }}</h2>
                        <p class="saas-card-desc">{{ app()->getLocale() == 'ar' ? 'الفروع التي يمكن لهذا المستخدم الدخول إليها والعمل عليها.' : 'Branches this user can access and manage.' }}</p>
                    </div>
                </div>

                <div id="admin-branch-hint" class="mt-3 p-3 rounded-3 mb-3" style="background:var(--saas-primary-light);border:1px solid rgba(99,102,241,0.2);display:{{ old('role', $user->role) == 'admin' ? 'block' : 'none' }};">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill" style="color:var(--saas-primary);"></i>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:var(--saas-primary);">{{ app()->getLocale() == 'ar' ? 'المدير لديه وصول شامل' : 'Admin Has Global Access' }}</h6>
                            <p class="mb-0" style="font-size:0.85rem;color:var(--saas-primary);">{{ app()->getLocale() == 'ar' ? 'بصفتك مدير نظام، يمتلك هذا الحساب صلاحية الدخول لجميع الفروع تلقائياً.' : 'As a system administrator, this account automatically has access to all branches.' }}</p>
                        </div>
                    </div>
                </div>

                <div id="branches-section-content" style="display:{{ old('role', $user->role) == 'admin' ? 'none' : 'block' }};">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 mb-3">
                        <div class="input-group-saas flex-grow-1" style="min-width: 200px; max-width: 100%;">
                            <div class="input-group-text-saas border-end-0 bg-transparent py-2"><i class="bi bi-search text-muted"></i></div>
                            <input type="text" class="saas-input border-start-0 ps-0 py-2" id="branchSearch" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث عن فرع...' : 'Search branch...' }}">
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="fw-bold" style="color:var(--saas-text-main); font-size: 0.9rem; user-select: none;">{{ app()->getLocale() == 'ar' ? 'تحديد الكل' : 'Select All' }}</span>
                            <label class="saas-switch mb-0">
                                <input type="checkbox" id="selectAllBranches">
                                <span class="saas-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="branches-grid">
                        @foreach($branches as $branch)
                        @php
                            $isChecked = (is_array(old('branches')) && in_array($branch->id, old('branches')))
                                || (is_null(old('branches')) && $user->branches->contains($branch->id));
                        @endphp
                        <label class="branch-card {{ $isChecked ? 'selected' : '' }}" id="branch_card_{{ $branch->id }}" data-name="{{ strtolower($branch->getTranslation('name')) }}">
                            <div class="branch-icon"><i class="bi bi-building"></i></div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0" style="color:var(--saas-text-main);font-size:0.95rem;">{{ $branch->getTranslation('name') }}</h6>
                                <span class="text-muted" style="font-size:0.75rem;">{{ app()->getLocale() == 'ar' ? 'الكود' : 'Code' }}: {{ $branch->code }}</span>
                            </div>
                            <div class="saas-switch">
                                <input type="checkbox" name="branches[]" value="{{ $branch->id }}" class="branch-checkbox" {{ $isChecked ? 'checked' : '' }} onchange="toggleBranchCard(this, {{ $branch->id }})">
                                <span class="saas-slider"></span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="saas-card mb-0" style="padding: 20px 28px; background: var(--saas-surface); border: 1px solid var(--saas-border); border-radius: var(--saas-radius-lg); box-shadow: var(--saas-shadow-sm);">
                <div class="d-flex gap-2 action-buttons-flex">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ التعديلات' : 'Save Changes' }}</button>
                    <a href="{{ route('users.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</a>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function selectRole(role) {
        document.getElementById('role_input').value = role;
        document.getElementById('role-admin').classList.remove('selected');
        document.getElementById('role-employee').classList.remove('selected');
        document.getElementById('role-' + role).classList.add('selected');

        const hint = document.getElementById('admin-branch-hint');
        const content = document.getElementById('branches-section-content');
        if (role === 'admin') {
            hint.style.display = 'block';
            content.style.display = 'none';
        } else {
            hint.style.display = 'none';
            content.style.display = 'block';
        }
    }

    function toggleBranchCard(checkbox, id) {
        const card = document.getElementById('branch_card_' + id);
        if (checkbox.checked) card.classList.add('selected');
        else card.classList.remove('selected');
    }

    document.getElementById('selectAllBranches').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.branch-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            toggleBranchCard(cb, cb.value);
        });
    });

    document.getElementById('branchSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.branch-card').forEach(card => {
            const name = card.getAttribute('data-name');
            card.style.display = name.includes(term) ? 'flex' : 'none';
        });
    });

    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById(id + '-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    document.getElementById('password').addEventListener('input', function(e) {
        const val = e.target.value;
        let strength = 0;
        if (val.length >= 8) strength++;
        if (val.match(/[a-z]+/)) strength++;
        if (val.match(/[0-9]+/)) strength++;
        if (val.match(/[$@#&!]+/)) strength++;

        const text = document.getElementById('pwd-text');
        const bars = [1,2,3,4].map(i => document.getElementById('pwd-bar-' + i));
        bars.forEach(b => b.style.background = 'var(--saas-border)');

        const levels = ['', '{{ app()->getLocale() == "ar" ? "ضعيف" : "Weak" }}', '{{ app()->getLocale() == "ar" ? "متوسط" : "Medium" }}', '{{ app()->getLocale() == "ar" ? "جيد" : "Good" }}', '{{ app()->getLocale() == "ar" ? "قوي ممتاز" : "Excellent" }}'];
        const colors = ['', '#EF4444', '#F59E0B', '#10B981', '#10B981'];
        text.textContent = val.length === 0 ? '' : levels[strength];
        text.style.color = colors[strength];
        for (let i = 0; i < strength; i++) bars[i].style.background = colors[strength];
    });

    // Avatar preview
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('avatarPreview').innerHTML =
                    `<img src="${event.target.result}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Name Auto-fill: type in one language → fills the other if empty
    const nameAr = document.querySelector('input[name="full_name_ar"]');
    const nameEn = document.querySelector('input[name="full_name_en"]');
    let arManuallyEdited = nameAr.value.trim() !== '';
    let enManuallyEdited = nameEn.value.trim() !== '';

    nameAr.addEventListener('focus', () => arManuallyEdited = nameAr.value.trim() !== '');
    nameEn.addEventListener('focus', () => enManuallyEdited = nameEn.value.trim() !== '');

    nameAr.addEventListener('input', function() {
        arManuallyEdited = true;
        if (!enManuallyEdited) {
            nameEn.value = this.value;
        }
    });

    nameEn.addEventListener('input', function() {
        enManuallyEdited = true;
        if (!arManuallyEdited) {
            nameAr.value = this.value;
        }
    });
</script>
@endpush

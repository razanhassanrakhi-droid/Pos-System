@extends('layouts.app')

@section('title', __('pos.add') . ' ' . __('pos.manage', ['page' => __('pos.users')]))

@push('styles')
<style>
    /* =================================================================
       Enterprise SaaS 2026 UI - User Management
       ================================================================= */
       
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
        width: 220px;
        height: 220px;
        background: rgba(124,58,237,.25) !important;
        top: -80px;
        right: -60px;
    }

    .pm-modal-header-glow-2 {
        width: 160px;
        height: 160px;
        background: rgba(99,102,241,.18) !important;
        bottom: -60px;
        left: -40px;
    }

    .pm-modal-icon-premium {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: rgba(255,255,255,.1);
        border: 1.5px solid rgba(255,255,255,.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #c4b5fd !important;
        flex-shrink: 0;
        backdrop-filter: blur(8px);
    }

    .pm-modal-title-premium {
        font-size: 1.15rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        letter-spacing: -.3px;
    }

    .pm-modal-sub-premium {
        font-size: .78rem;
        color: #a5b4fc !important;
        margin: 3px 0 0;
        font-weight: 500;
    }

    .pm-modal-close-premium {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255,255,255,.08);
        border: 1.5px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.7) !important;
        transition: all 0.2s ease;
    }

    .pm-modal-close-premium:hover {
        background: rgba(255,255,255,.16);
        color: #fff !important;
        transform: scale(1.05);
    }
    
    :root {
        /* Indigo/Violet Palette */
        --saas-primary: #6366F1;
        --saas-primary-hover: #4F46E5;
        --saas-primary-light: rgba(99, 102, 241, 0.1);
        --saas-secondary: #8B5CF6;
        --saas-accent: #0EA5E9;
        
        /* Neutrals */
        --saas-bg: #F8FAFC;
        --saas-surface: #FFFFFF;
        --saas-border: #E2E8F0;
        --saas-border-light: #F1F5F9;
        --saas-text-main: #0F172A;
        --saas-text-muted: #64748B;
        --saas-text-light: #94A3B8;

        /* Shadows & Radii */
        --saas-shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.025);
        --saas-shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -4px rgba(0, 0, 0, 0.03);
        --saas-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        --saas-radius-lg: 20px;
        --saas-radius-md: 14px;
        --saas-radius-sm: 10px;

        /* Status Colors */
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
        --saas-text-muted: #cbd5e1;
        --saas-primary-light: rgba(99, 102, 241, 0.2);
    }

    body {
        background-color: var(--saas-bg);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 24px;
    }
    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--saas-text-main);
        letter-spacing: -0.02em;
        margin-bottom: 4px;
    }
    .page-subtitle {
        color: var(--saas-text-muted);
        font-size: 0.95rem;
        font-weight: 500;
    }

    /* Cards */
    .saas-card {
        background: var(--saas-surface);
        border: 1px solid var(--saas-border);
        border-radius: var(--saas-radius-lg);
        box-shadow: var(--saas-shadow-sm);
        padding: 32px;
        margin-bottom: 24px;
        transition: box-shadow 0.3s ease;
    }
    .saas-card:hover {
        box-shadow: var(--saas-shadow-md);
    }
    .saas-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--saas-border-light);
    }
    .saas-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--saas-primary-light);
        color: var(--saas-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .saas-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--saas-text-main);
        margin: 0;
    }
    .saas-card-desc {
        font-size: 0.85rem;
        color: var(--saas-text-muted);
        margin: 4px 0 0;
    }
    [data-pm-theme="dark"] .saas-card-desc,
    [data-pm-theme="dark"] .text-muted,
    [data-pm-theme="dark"] .pwd-text,
    [data-pm-theme="dark"] .role-card p.text-muted {
        color: #e2e8f0 !important;
    }

    /* Form Controls */
    .saas-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--saas-text-main);
        margin-bottom: 8px;
        display: block;
    }
    .saas-input {
        background: var(--saas-bg);
        border: 1.5px solid var(--saas-border);
        border-radius: var(--saas-radius-sm);
        padding: 12px 16px;
        font-size: 0.95rem;
        color: var(--saas-text-main);
        width: 100%;
        transition: all 0.2s ease;
    }
    .saas-input:focus {
        background: var(--saas-surface);
        border-color: var(--saas-primary);
        box-shadow: 0 0 0 4px var(--saas-primary-light);
        outline: none;
    }
    .input-group-saas {
        display: flex;
        align-items: center;
        background: var(--saas-bg);
        border: 1.5px solid var(--saas-border);
        border-radius: var(--saas-radius-sm);
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .input-group-saas:focus-within {
        background: var(--saas-surface);
        border-color: var(--saas-primary);
        box-shadow: 0 0 0 4px var(--saas-primary-light);
    }
    .input-group-saas .saas-input {
        border: none;
        background: transparent;
        box-shadow: none !important;
    }
    .input-group-text-saas {
        padding: 12px 16px;
        color: var(--saas-text-muted);
        font-weight: 500;
        border-left: 1px solid var(--saas-border);
        background: var(--saas-bg);
    }
    html[dir="rtl"] .input-group-text-saas {
        border-left: none;
        border-right: 1px solid var(--saas-border);
    }

    /* Avatar Upload */
    .avatar-upload-zone {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        border: 1.5px dashed var(--saas-border);
        border-radius: var(--saas-radius-md);
        background: var(--saas-bg);
        cursor: pointer;
        transition: all 0.2s;
    }
    .avatar-upload-zone:hover {
        border-color: var(--saas-primary);
        background: var(--saas-primary-light);
    }
    .avatar-preview {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--saas-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--saas-text-muted);
        overflow: hidden;
    }
    .avatar-text {
        font-weight: 600;
        color: var(--saas-text-main);
        margin-bottom: 4px;
    }

    .role-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    @media (max-width: 768px) {
        .role-cards {
            grid-template-columns: 1fr !important;
        }
    }
    .role-card {
        border: 2px solid var(--saas-border);
        border-radius: var(--saas-radius-md);
        padding: 20px;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--saas-surface);
        position: relative;
    }
    .role-card:hover {
        border-color: var(--saas-primary-light);
        background: var(--saas-bg);
    }
    .role-card.selected {
        border-color: var(--saas-primary);
        background: var(--saas-primary-light);
    }
    .role-card.selected::after {
        content: '\f26a'; /* bootstrap check-circle-fill */
        font-family: 'bootstrap-icons';
        position: absolute;
        top: 16px;
        left: 16px;
        color: var(--saas-primary);
        font-size: 1.25rem;
    }
    html[dir="rtl"] .role-card.selected::after {
        left: auto;
        right: 16px;
    }
    .role-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem !important;
        color: #6366f1 !important;
        margin-bottom: 12px;
        transition: all 0.2s;
    }
    .role-icon i {
        font-family: "bootstrap-icons" !important;
        font-style: normal !important;
        font-weight: normal !important;
        font-variant: normal !important;
        text-transform: none !important;
        line-height: 1 !important;
        display: inline-block !important;
        color: #6366f1 !important;
    }
    [data-pm-theme="dark"] .role-icon {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    [data-pm-theme="dark"] .role-icon i {
        color: #a5b4fc !important;
    }
    .role-card.selected .role-icon {
        background: var(--saas-surface);
        border-color: var(--saas-primary);
    }

    /* Password Strength */
    .pwd-strength {
        display: flex;
        gap: 4px;
        margin-top: 8px;
        height: 6px;
    }
    .pwd-bar {
        flex: 1;
        background: var(--saas-border);
        border-radius: 3px;
        transition: all 0.3s;
    }
    .pwd-text {
        font-size: 0.75rem;
        color: var(--saas-text-muted);
        margin-top: 6px;
        font-weight: 500;
    }

    /* Branch Cards Grid */
    .branches-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 16px;
        max-height: 400px;
        overflow-y: auto;
        padding-right: 8px;
    }
    html[dir="rtl"] .branches-grid {
        padding-right: 0;
        padding-left: 8px;
    }
    /* Custom Scrollbar */
    .branches-grid::-webkit-scrollbar { width: 6px; }
    .branches-grid::-webkit-scrollbar-track { background: transparent; }
    .branches-grid::-webkit-scrollbar-thumb { background: var(--saas-border); border-radius: 10px; }
    .branches-grid::-webkit-scrollbar-thumb:hover { background: var(--saas-text-muted); }

    .branch-card {
        border: 1.5px solid var(--saas-border);
        border-radius: var(--saas-radius-md);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
        background: var(--saas-surface);
        cursor: pointer;
    }
    .branch-card:hover {
        border-color: var(--saas-primary-light);
        background: var(--saas-bg);
    }
    .branch-card.selected {
        border-color: var(--saas-primary);
        background: var(--saas-primary-light);
    }
    .branch-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--saas-bg);
        color: var(--saas-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .branch-card.selected .branch-icon {
        color: var(--saas-primary);
        background: var(--saas-surface);
    }

    /* Custom Toggles */
    .saas-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .saas-switch input { opacity: 0; width: 0; height: 0; }
    .saas-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--saas-border);
        transition: .3s;
        border-radius: 34px;
    }
    .saas-slider:before {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 3px; bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    html[dir="rtl"] .saas-slider:before {
        left: auto; right: 3px;
    }
    input:checked + .saas-slider {
        background-color: var(--saas-success);
    }
    input:checked + .saas-slider:before {
        transform: translateX(20px);
    }
    html[dir="rtl"] input:checked + .saas-slider:before {
        transform: translateX(-20px);
    }

    /* Sidebar Sticky */
    .sidebar-sticky {
        position: sticky;
        top: 24px;
    }
    .progress-step {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        position: relative;
    }
    .progress-step:not(:last-child)::after {
        content: '';
        position: absolute;
        width: 2px;
        height: 24px;
        background: var(--saas-border);
        top: 36px;
        right: 17px;
    }
    html[dir="ltr"] .progress-step:not(:last-child)::after {
        right: auto;
        left: 17px;
    }
    .step-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--saas-bg);
        border: 2px solid var(--saas-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--saas-text-muted);
        z-index: 1;
        transition: all 0.3s;
    }
    .step-active .step-icon {
        background: var(--saas-primary);
        border-color: var(--saas-primary);
        color: white;
        box-shadow: 0 0 0 4px var(--saas-primary-light);
    }
    .step-completed .step-icon {
        background: var(--saas-success);
        border-color: var(--saas-success);
        color: white;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .btn-saas {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 24px;
        border-radius: var(--saas-radius-md);
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
    }
    .btn-saas-primary {
        background: linear-gradient(135deg, var(--saas-primary) 0%, var(--saas-secondary) 100%);
        color: white;
        box-shadow: 0 4px 12px var(--saas-primary-light);
    }
    .btn-saas-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px var(--saas-primary-light);
        color: white;
    }
    .btn-saas-outline {
        background: var(--saas-surface);
        border: 2px solid var(--saas-border);
        color: var(--saas-text-main);
    }
    .btn-saas-outline:hover {
        background: var(--saas-bg);
        border-color: var(--saas-text-muted);
    }

    /* Toggles Layout */
    .setting-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border: 1px solid var(--saas-border);
        border-radius: var(--saas-radius-md);
        margin-bottom: 12px;
        background: var(--saas-surface);
    }
    .iti { width: 100%; display: block; }
    .iti input, .iti input[type=text], .iti input[type=tel] {
        padding-left: 95px !important;
        padding-right: 16px !important;
        text-align: left;
        direction: ltr;
    }
    html[dir="rtl"] .iti input, html[dir="rtl"] .iti input[type=text], html[dir="rtl"] .iti input[type=tel] {
        padding-right: 95px !important;
        padding-left: 16px !important;
        text-align: right;
        direction: ltr;
    }
    html[dir="rtl"] .iti input::placeholder {
        text-align: right !important;
        direction: rtl !important;
    }
    .iti__country-list {
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color, #e2e8f0) !important;
        text-align: left;
        direction: ltr;
        background: var(--card-bg, #ffffff) !important;
        padding: 8px 0;
        min-width: 280px;
        z-index: 1050 !important;
    }
    html[dir="rtl"] .iti__country-list {
        right: 0 !important;
        left: auto !important;
    }
    html[dir="rtl"] .iti__flag-container {
        right: 0 !important;
        left: auto !important;
    }
    .iti__country-name {
        display: inline-block !important;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-color, #334155) !important;
    }
    .iti__dial-code {
        font-size: 0.8rem;
        color: var(--text-muted, #64748b) !important;
        font-weight: 500;
    }
    .iti__country {
        padding: 10px 16px !important;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.15s ease;
        background: var(--card-bg, #ffffff) !important;
        color: var(--text-color, #334155) !important;
    }
    .iti__country:hover {
        background-color: var(--pm-surface-3, #f1f5f9) !important;
    }
    html[data-app-theme="dark"] .iti__country:hover {
        background-color: rgba(255,255,255,0.06) !important;
    }
    .iti__country.iti__highlight {
        background-color: var(--pm-surface-3, #f1f5f9) !important;
    }
    html[data-app-theme="dark"] .iti__country.iti__highlight {
        background-color: rgba(255,255,255,0.08) !important;
    }
    .iti__selected-dial-code {
        direction: ltr !important;
        unicode-bidi: embed !important;
        display: inline-block !important;
        color: var(--text-color, #334155) !important;
        margin-inline-start: 4px;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />
@endpush

@section('content')
<div class="pm-card-premium max-w-800 mx-auto" style="max-width: 800px; margin-top: 2rem; margin-bottom: 2rem;">
    {{-- Premium Header --}}
    <div class="pm-modal-header-premium">
        <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
        <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width: 100%;">
            <div class="pm-modal-icon-premium">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="pm-modal-title-premium">{{ app()->getLocale() == 'ar' ? 'إضافة مستخدم جديد' : 'Add New User' }}</h5>
                <p class="pm-modal-sub-premium">{{ app()->getLocale() == 'ar' ? 'قم بإنشاء حساب مستخدم جديد وتخصيص الصلاحيات والفروع.' : 'Create a new user account and assign permissions and branches.' }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="pm-modal-close-premium d-flex align-items-center justify-content-center text-decoration-none">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
            @csrf

            @if ($errors->any())
                <div class="saas-card" style="border-left: 4px solid var(--saas-danger); padding: 20px;">
                    <h6 class="fw-bold" style="color: var(--saas-danger);"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ app()->getLocale() == 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}</h6>
                    <ul class="mb-0 mt-2" style="color: var(--saas-danger);">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Form -->
            <div class="col-12">
                
                <!-- Card 1: Personal Info -->
                <div class="saas-card" id="card-personal">
                    <div class="saas-card-header">
                        <div class="saas-card-icon"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <h2 class="saas-card-title">{{ app()->getLocale() == 'ar' ? 'المعلومات الشخصية' : 'Personal Information' }}</h2>
                            <p class="saas-card-desc">{{ app()->getLocale() == 'ar' ? 'البيانات الأساسية ومعلومات التواصل الخاصة بالمستخدم.' : 'Basic details and contact information of the user.' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="avatar-upload-zone">
                            <div class="avatar-preview"><i class="bi bi-person"></i></div>
                            <div>
                                <h6 class="avatar-text">{{ app()->getLocale() == 'ar' ? 'صورة المستخدم (اختياري)' : 'User Avatar (Optional)' }}</h6>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">{{ app()->getLocale() == 'ar' ? 'JPG, PNG أو GIF (الحد الأقصى 2MB)' : 'JPG, PNG or GIF (Max 2MB)' }}</p>
                            </div>
                            <button type="button" class="btn-saas btn-saas-outline ms-auto" style="padding: 8px 16px; font-size: 0.8rem;" onclick="document.getElementById('avatar').click()">{{ app()->getLocale() == 'ar' ? 'اختر صورة' : 'Choose Image' }}</button>
                            <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'الاسم الكامل (عربي)' : 'Full Name (Arabic)' }} <span class="text-danger">*</span></label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-person text-muted"></i></div>
                                <input type="text" class="saas-input border-start-0 ps-0" name="full_name_ar" value="{{ old('full_name_ar') }}" required placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل الاسم باللغة العربية' : 'Enter name in Arabic' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'الاسم الكامل (إنجليزي)' : 'Full Name (English)' }}</label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-fonts text-muted"></i></div>
                                <input type="text" class="saas-input border-start-0 ps-0" name="full_name_en" value="{{ old('full_name_en') }}" placeholder="Enter full name in English">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'اسم المستخدم للرقم السري (عربي)' : 'Username (Arabic)' }} <span class="text-danger">*</span></label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-at text-muted"></i></div>
                                <input type="text" class="saas-input border-start-0 ps-0" name="username_ar" value="{{ old('username_ar') }}" placeholder="اسم المستخدم بالعربية">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'اسم المستخدم للرقم السري (إنجليزي)' : 'Username (English)' }} <span class="text-danger">*</span></label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-at text-muted"></i></div>
                                <input type="text" class="saas-input border-start-0 ps-0" name="username_en" value="{{ old('username_en') }}" placeholder="username123">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-envelope text-muted"></i></div>
                                <input type="email" class="saas-input border-start-0 ps-0" name="email" value="{{ old('email') }}" placeholder="name@company.com">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                            <input type="tel" class="saas-input" name="phone" id="phone" value="{{ old('phone') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل رقم الهاتف' : 'Enter phone number' }}">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Account Security -->
                <div class="saas-card" id="card-security">
                    <div class="saas-card-header">
                        <div class="saas-card-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--saas-success);"><i class="bi bi-shield-lock"></i></div>
                        <div>
                            <h2 class="saas-card-title">{{ app()->getLocale() == 'ar' ? 'إعدادات الحساب والأمان' : 'Account & Security Settings' }}</h2>
                            <p class="saas-card-desc">{{ app()->getLocale() == 'ar' ? 'إعداد كلمة المرور وحالة الحساب وتأكيد البريد.' : 'Setup password strength, account activation status.' }}</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }} <span class="text-danger">*</span></label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-key text-muted"></i></div>
                                <input type="password" class="saas-input border-start-0 border-end-0 ps-0" id="password" name="password" required placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل كلمة مرور قوية' : 'Enter strong password' }}">
                                <div class="input-group-text-saas border-start-0 bg-transparent" style="cursor: pointer;" onclick="togglePassword('password')"><i class="bi bi-eye text-muted" id="password-icon"></i></div>
                            </div>
                            <div class="pwd-strength">
                                <div class="pwd-bar" id="pwd-bar-1"></div>
                                <div class="pwd-bar" id="pwd-bar-2"></div>
                                <div class="pwd-bar" id="pwd-bar-3"></div>
                                <div class="pwd-bar" id="pwd-bar-4"></div>
                            </div>
                            <div class="pwd-text" id="pwd-text">{{ app()->getLocale() == 'ar' ? 'ضعيف جداً' : 'Very Weak' }}</div>
                            <div class="text-muted small mt-2 d-flex align-items-center gap-1" style="font-size: 0.78rem; opacity: 0.85;">
                                 <i class="bi bi-info-circle"></i>
                                 <span>{{ app()->getLocale() == 'ar' ? 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.' : 'Password must be at least 8 characters long.' }}</span>
                             </div>
                        </div>
                        <div class="col-md-6">
                            <label class="saas-label">{{ app()->getLocale() == 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }} <span class="text-danger">*</span></label>
                            <div class="input-group-saas">
                                <div class="input-group-text-saas border-end-0 bg-transparent"><i class="bi bi-key-fill text-muted"></i></div>
                                <input type="password" class="saas-input border-start-0 border-end-0 ps-0" id="password_confirmation" name="password_confirmation" required placeholder="{{ app()->getLocale() == 'ar' ? 'أعادة إدخال كلمة المرور' : 'Re-enter password' }}">
                                <div class="input-group-text-saas border-start-0 bg-transparent" style="cursor: pointer;" onclick="togglePassword('password_confirmation')"><i class="bi bi-eye text-muted" id="password_confirmation-icon"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: var(--saas-text-main);">{{ app()->getLocale() == 'ar' ? 'تفعيل الحساب' : 'Activate Account' }}</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">{{ app()->getLocale() == 'ar' ? 'سيتمكن المستخدم من تسجيل الدخول فوراً.' : 'User will be allowed to log in immediately.' }}</p>
                        </div>
                        <label class="saas-switch">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span class="saas-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Card 3: Role & Permissions -->
                <div class="saas-card" id="card-role">
                    <div class="saas-card-header">
                        <div class="saas-card-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--saas-warning);"><i class="bi bi-person-gear"></i></div>
                        <div>
                            <h2 class="saas-card-title">{{ app()->getLocale() == 'ar' ? 'الدور والصلاحيات' : 'Role & Permissions' }}</h2>
                            <p class="saas-card-desc">{{ app()->getLocale() == 'ar' ? 'حدد مستوى وصول المستخدم وصلاحياته في النظام.' : 'Specify user role and system permissions.' }}</p>
                        </div>
                    </div>

                    <label class="saas-label mb-3">{{ app()->getLocale() == 'ar' ? 'اختر دور المستخدم' : 'Select User Role' }} <span class="text-danger">*</span></label>
                    <input type="hidden" name="role" id="role_input" value="{{ old('role', 'employee') }}">
                    <div class="role-cards mb-4">
                        <div class="role-card {{ old('role', 'employee') == 'admin' ? 'selected' : '' }}" onclick="selectRole('admin')" id="role-admin">
                            <div class="role-icon" style="width: 48px; height: 48px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; font-size: 1.5rem; color: #6366f1; border: 1px solid #cbd5e1;"><i class="bi bi-shield-fill" style="display: inline-block; line-height: 1;"></i></div>
                            <h5 class="fw-bold mb-1" style="color: var(--saas-text-main);">{{ app()->getLocale() == 'ar' ? 'مدير نظام (Admin)' : 'System Administrator' }}</h5>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">{{ app()->getLocale() == 'ar' ? 'صلاحيات كاملة للتحكم بالنظام وجميع الفروع.' : 'Full access control over the system and all branches.' }}</p>
                        </div>
                        <div class="role-card {{ old('role', 'employee') == 'employee' ? 'selected' : '' }}" onclick="selectRole('employee')" id="role-employee">
                            <div class="role-icon" style="width: 48px; height: 48px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; font-size: 1.5rem; color: #6366f1; border: 1px solid #cbd5e1;"><i class="bi bi-person-badge" style="display: inline-block; line-height: 1;"></i></div>
                            <h5 class="fw-bold mb-1" style="color: var(--saas-text-main);">{{ app()->getLocale() == 'ar' ? 'موظف (Employee)' : 'Standard Employee' }}</h5>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">{{ app()->getLocale() == 'ar' ? 'وصول محدود حسب الصلاحيات والفروع المحددة.' : 'Limited access depending on direct permissions and branches.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Branches -->
                <div class="saas-card" id="card-branches">
                    <div class="saas-card-header mb-0" style="border-bottom: none; padding-bottom: 0;">
                        <div class="saas-card-icon" style="background: rgba(14, 165, 233, 0.1); color: var(--saas-accent);"><i class="bi bi-shop"></i></div>
                        <div>
                            <h2 class="saas-card-title">{{ app()->getLocale() == 'ar' ? 'تخصيص الفروع' : 'Branch Assignment' }}</h2>
                            <p class="saas-card-desc">{{ app()->getLocale() == 'ar' ? 'الفروع التي يمكن لهذا المستخدم الدخول إليها والعمل عليها.' : 'Branches that this user has authority to access and manage.' }}</p>
                        </div>
                    </div>
                    
                    <div id="admin-branch-hint" class="mt-3 p-3 rounded-3 mb-3" style="background: var(--saas-primary-light); border: 1px solid rgba(99, 102, 241, 0.2); display: {{ old('role') == 'admin' ? 'block' : 'none' }};">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill" style="color: var(--saas-primary);"></i>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: var(--saas-primary);">{{ app()->getLocale() == 'ar' ? 'المدير لديه وصول شامل' : 'Administrator has full access' }}</h6>
                                <p class="mb-0" style="font-size: 0.85rem; color: var(--saas-primary);">{{ app()->getLocale() == 'ar' ? 'بصفتك مدير نظام، يمتلك هذا الحساب صلاحية الدخول لجميع الفروع تلقائياً.' : 'As a system administrator, this account has access to all branches automatically.' }}</p>
                            </div>
                        </div>
                    </div>

                    <div id="branches-section-content" style="display: {{ old('role') == 'admin' ? 'none' : 'block' }};">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 mb-3">
                            <div class="input-group-saas flex-grow-1" style="min-width: 200px; max-width: 100%;">
                                <div class="input-group-text-saas border-end-0 bg-transparent py-2"><i class="bi bi-search text-muted"></i></div>
                                <input type="text" class="saas-input border-start-0 ps-0 py-2" id="branchSearch" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث عن فرع...' : 'Search branches...' }}">
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <span class="fw-bold" style="color: var(--saas-text-main); font-size: 0.9rem; user-select: none;">{{ app()->getLocale() == 'ar' ? 'تحديد الكل' : 'Select All' }}</span>
                                <label class="saas-switch mb-0">
                                    <input type="checkbox" id="selectAllBranches">
                                    <span class="saas-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="branches-grid">
                            @foreach($branches as $branch)
                            @php
                                $isChecked = is_array(old('branches')) && in_array($branch->id, old('branches'));
                            @endphp
                            <label class="branch-card {{ $isChecked ? 'selected' : '' }}" id="branch_card_{{ $branch->id }}" data-name="{{ strtolower($branch->getTranslation('name')) }}">
                                <div class="branch-icon"><i class="bi bi-building"></i></div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0" style="color: var(--saas-text-main); font-size: 0.95rem;">{{ $branch->getTranslation('name') }}</h6>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'الكود' : 'Code' }}: {{ $branch->code }}</span>
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
            </div>

            <div class="saas-card mb-0" style="padding: 20px 28px; background: var(--saas-surface); border: 1px solid var(--saas-border); border-radius: var(--saas-radius-lg); box-shadow: var(--saas-shadow-sm);">
                <div class="d-flex gap-2 action-buttons-flex">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ وإنشاء المستخدم' : 'Save & Create User' }}</button>
                    <a href="{{ route('users.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script>
    // Initialize International Telephone Input
    const phoneInput = document.querySelector("#phone");
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "sa",
        separateDialCode: true,
        preferredCountries: ["sa", "ae", "eg", "jo"],
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
    });

    // Update the hidden or actual input with full number before submit
    document.getElementById('userForm').addEventListener('submit', function() {
        if(phoneInput.value) {
            phoneInput.value = iti.getNumber();
        }
    });
    // Role Selection Logic
    function selectRole(role) {
        document.getElementById('role_input').value = role;
        document.getElementById('role-admin').classList.remove('selected');
        document.getElementById('role-employee').classList.remove('selected');
        document.getElementById('role-' + role).classList.add('selected');

        const hint = document.getElementById('admin-branch-hint');
        const content = document.getElementById('branches-section-content');
        
        if(role === 'admin') {
            hint.style.display = 'block';
            content.style.display = 'none';
        } else {
            hint.style.display = 'none';
            content.style.display = 'block';
        }
    }

    // Branch Toggle Card Visuals
    function toggleBranchCard(checkbox, id) {
        const card = document.getElementById('branch_card_' + id);
        if(checkbox.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    }

    // Select All Branches
    document.getElementById('selectAllBranches').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.branch-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            toggleBranchCard(cb, cb.value);
        });
    });

    // Branch Search Filter
    document.getElementById('branchSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.branch-card');
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if(name.includes(term)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Password Toggle Visibility
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById(id + '-icon');
        if(input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Password Strength Meter
    document.getElementById('password').addEventListener('input', function(e) {
        const val = e.target.value;
        let strength = 0;
        
        if(val.length >= 8) strength++;
        if(val.match(/[a-z]+/)) strength++;
        if(val.match(/[0-9]+/)) strength++;
        if(val.match(/[$@#&!]+/)) strength++;

        const text = document.getElementById('pwd-text');
        const bars = [
            document.getElementById('pwd-bar-1'),
            document.getElementById('pwd-bar-2'),
            document.getElementById('pwd-bar-3'),
            document.getElementById('pwd-bar-4')
        ];

        bars.forEach(b => b.style.background = 'var(--saas-border)');

        if(val.length === 0) {
            text.textContent = 'ضعيف جداً';
            text.style.color = 'var(--saas-text-muted)';
        } else if(strength === 1) {
            text.textContent = 'ضعيف';
            text.style.color = 'var(--saas-danger)';
            bars[0].style.background = 'var(--saas-danger)';
        } else if(strength === 2) {
            text.textContent = 'متوسط';
            text.style.color = 'var(--saas-warning)';
            bars[0].style.background = 'var(--saas-warning)';
            bars[1].style.background = 'var(--saas-warning)';
        } else if(strength === 3) {
            text.textContent = 'جيد';
            text.style.color = 'var(--saas-success)';
            bars[0].style.background = 'var(--saas-success)';
            bars[1].style.background = 'var(--saas-success)';
            bars[2].style.background = 'var(--saas-success)';
        } else if(strength === 4) {
            text.textContent = 'قوي ممتاز';
            text.style.color = 'var(--saas-success)';
            bars.forEach(b => b.style.background = 'var(--saas-success)');
        }
    });


    // Avatar Preview Logic
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.querySelector('.avatar-preview');
                preview.innerHTML = `<img src="${event.target.result}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">`;
            }
            reader.readAsDataURL(file);
        }
    });

    // Name Auto-fill: type in one language → fills the other if empty
    const nameAr = document.querySelector('input[name="full_name_ar"]');
    const nameEn = document.querySelector('input[name="full_name_en"]');
    let arManuallyEdited = false;
    let enManuallyEdited = false;

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

@extends('layouts.app')

@section('title', __('pos.add') . ' ' . __('pos.manage', ['page' => __('pos.branches')]))

@push('styles')
<style>
    /* Premium ERP Styling inspired by Dynamics 365 & Oracle Cloud */
    .erp-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    .erp-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .erp-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 28px 36px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
    }
    .erp-header-title {
        color: #ffffff;
        font-size: 1.35rem;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .erp-header-subtitle {
        color: #94a3b8;
        font-size: 0.82rem;
        margin-bottom: 0;
    }
    .erp-section {
        padding: 32px 36px;
        border-bottom: 1px solid #f1f5f9;
    }
    .erp-section:last-of-type {
        border-bottom: none;
    }
    .erp-section-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .erp-section-title i {
        color: #6366f1;
        font-size: 1.2rem;
    }
    .erp-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }
    .erp-input-wrapper {
        position: relative;
        display: flex;
        align-items: stretch;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .erp-input-wrapper:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }
    .erp-input-icon {
        padding: 0 14px;
        background: #f8fafc;
        color: #64748b;
        display: flex;
        align-items: center;
        border-right: 1.5px solid #cbd5e1;
    }
    html[dir="rtl"] .erp-input-icon {
        border-right: none;
        border-left: 1.5px solid #cbd5e1;
    }
    .erp-input-wrapper[dir="ltr"] .erp-input-icon {
        border-right: 1.5px solid #cbd5e1 !important;
        border-left: none !important;
    }
    .erp-input-wrapper[dir="rtl"] .erp-input-icon {
        border-left: 1.5px solid #cbd5e1 !important;
        border-right: none !important;
    }
    .erp-input {
        flex-grow: 1;
        border: none;
        outline: none;
        padding: 10px 14px;
        font-size: 0.88rem;
        color: #0f172a;
        background: transparent;
        width: 100%;
    }
    .erp-textarea {
        width: 100%;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.88rem;
        outline: none;
        transition: all 0.2s ease;
        resize: vertical;
    }
    .erp-textarea:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .erp-footer {
        padding: 24px 36px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Modern Buttons */
    .btn-erp-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 10px;
        padding: 10px 28px;
        font-size: 0.88rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.25s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-erp-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
    }
    .btn-erp-secondary {
        background: #ffffff;
        color: #475569 !important;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 9px 24px;
        font-size: 0.88rem;
        font-weight: 700;
        transition: all 0.2s;
    }
    .btn-erp-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    /* Dark Mode Overrides */
    html[data-app-theme="dark"] .erp-card { background: #0f172a; border-color: #334155; }
    html[data-app-theme="dark"] .erp-section { border-bottom-color: #1e293b; }
    html[data-app-theme="dark"] .erp-section-title { color: #f8fafc; }
    html[data-app-theme="dark"] .erp-label { color: #cbd5e1; }
    html[data-app-theme="dark"] .erp-input-wrapper { background: #1e293b; border-color: #334155; }
    html[data-app-theme="dark"] .erp-input-icon { background: #0f172a; border-color: #334155; }
    html[data-app-theme="dark"] .erp-input { color: #f8fafc; }
    html[data-app-theme="dark"] .erp-textarea { background: #1e293b; border-color: #334155; color: #f8fafc; }
    html[data-app-theme="dark"] .erp-footer { background: #0f172a; border-top-color: #1e293b; }
    html[data-app-theme="dark"] .btn-erp-secondary { background: #1e293b; color: #cbd5e1 !important; border-color: #334155; }
    html[data-app-theme="dark"] .btn-erp-secondary:hover { background: #0f172a; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 erp-container">
    <div class="erp-card">
        {{-- Header --}}
        <div class="erp-header">
            <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
            <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
            <div class="d-flex align-items-center gap-3 position-relative" style="z-index: 2;">
                <div class="pm-modal-icon-premium">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h4 class="erp-header-title">{{ __('pos.add') }} {{ __('pos.manage', ['page' => __('pos.branches')]) }}</h4>
                    <p class="erp-header-subtitle">{{ app()->getLocale() == 'ar' ? 'إدخال بيانات الفرع الجديد وتنسيقه مع إعدادات النظام' : 'Create a new branch and configure its location and parameters' }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('branches.store') }}" method="POST" id="branchForm">
            @csrf

            <!-- SECTION 1: Branch Information -->
            <div class="erp-section">
                <h5 class="erp-section-title">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'معلومات الفرع الأساسية' : 'Branch Information' }}</span>
                </h5>

                <div class="row g-4">
                    {{-- Branch Name --}}
                    @if(app()->getLocale() == 'ar')
                        {{-- Arabic First --}}
                        <div class="col-md-6">
                            <label for="name_ar" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.branch_name_ar') }} <span class="text-danger">*</span></label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-translate"></i></span>
                                <input type="text" id="name_ar" name="name_ar" class="erp-input" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;" dir="rtl" value="{{ old('name_ar') }}" required placeholder="مثال: فرع الرياض">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="name_en" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.branch_name_en') }} <span class="text-danger">*</span></label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-alphabet"></i></span>
                                <input type="text" id="name_en" name="name_en" class="erp-input" style="text-align: left !important;" dir="ltr" value="{{ old('name_en') }}" placeholder="e.g. Riyadh Branch (Optional)">
                            </div>
                        </div>
                    @else
                        {{-- English First --}}
                        <div class="col-md-6">
                            <label for="name_en" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.branch_name_en') }} <span class="text-danger">*</span></label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-alphabet"></i></span>
                                <input type="text" id="name_en" name="name_en" class="erp-input" style="text-align: left !important;" dir="ltr" value="{{ old('name_en') }}" placeholder="e.g. Riyadh Branch (Optional)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="name_ar" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.branch_name_ar') }} <span class="text-danger">*</span></label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-translate"></i></span>
                                <input type="text" id="name_ar" name="name_ar" class="erp-input" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;" dir="rtl" value="{{ old('name_ar') }}" required placeholder="مثال: فرع الرياض">
                            </div>
                        </div>
                    @endif

                    {{-- Branch Code (Readonly) --}}
                    <div class="col-md-6">
                        <label for="code" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.branch_code') }} <span class="text-danger">*</span></label>
                        <div class="erp-input-wrapper bg-light">
                            <span class="erp-input-icon"><i class="bi bi-lock-fill"></i></span>
                            <input type="text" name="code" id="code" class="erp-input text-muted" value="{{ $nextCode }}" readonly style="cursor: not-allowed; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.7rem; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">
                            {{ app()->getLocale() == 'ar' ? 'كود تسلسلي مغلق ومولد تلقائياً.' : 'Sequential code generated automatically and locked.' }}
                        </small>
                    </div>

                    {{-- Empty spacer to align next elements nicely --}}
                    <div class="col-md-6 d-none d-md-block"></div>

                    {{-- Branch Manager --}}
                    @if(app()->getLocale() == 'ar')
                        {{-- Arabic First --}}
                        <div class="col-md-6">
                            <label for="manager_ar" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ \Illuminate\Support\Facades\Lang::has('pos.branch_manager_ar') ? __('pos.branch_manager_ar') : (app()->getLocale() == 'ar' ? 'مدير الفرع (عربي)' : 'Branch Manager (Arabic)') }}</label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-person-badge"></i></span>
                                <input type="text" id="manager_ar" name="manager_ar" class="erp-input" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;" dir="rtl" value="{{ old('manager_ar') }}" placeholder="مثال: محمد أحمد">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="manager_en" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ \Illuminate\Support\Facades\Lang::has('pos.branch_manager_en') ? __('pos.branch_manager_en') : (app()->getLocale() == 'ar' ? 'مدير الفرع (إنجليزي)' : 'Branch Manager (English)') }}</label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-person-badge-fill"></i></span>
                                <input type="text" id="manager_en" name="manager_en" class="erp-input" style="text-align: left !important;" dir="ltr" value="{{ old('manager_en') }}" placeholder="e.g. Mohamed Ahmed">
                            </div>
                        </div>
                    @else
                        {{-- English First --}}
                        <div class="col-md-6">
                            <label for="manager_en" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ \Illuminate\Support\Facades\Lang::has('pos.branch_manager_en') ? __('pos.branch_manager_en') : (app()->getLocale() == 'ar' ? 'مدير الفرع (إنجليزي)' : 'Branch Manager (English)') }}</label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-person-badge-fill"></i></span>
                                <input type="text" id="manager_en" name="manager_en" class="erp-input" style="text-align: left !important;" dir="ltr" value="{{ old('manager_en') }}" placeholder="e.g. Mohamed Ahmed">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="manager_ar" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ \Illuminate\Support\Facades\Lang::has('pos.branch_manager_ar') ? __('pos.branch_manager_ar') : (app()->getLocale() == 'ar' ? 'مدير الفرع (عربي)' : 'Branch Manager (Arabic)') }}</label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-person-badge"></i></span>
                                <input type="text" id="manager_ar" name="manager_ar" class="erp-input" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;" dir="rtl" value="{{ old('manager_ar') }}" placeholder="مثال: محمد أحمد">
                            </div>
                        </div>
                    @endif

                    {{-- Phone Number --}}
                    <div class="col-md-6">
                        <label for="phone" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.phone') }}</label>
                        <div class="erp-input-wrapper">
                            <span class="erp-input-icon"><i class="bi bi-telephone"></i></span>
                            <input type="tel" id="phone" name="phone" class="erp-input" value="{{ old('phone') }}" placeholder="+966 5XXXXXXXX" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني للفرع' : 'Branch Email' }}</label>
                        <div class="erp-input-wrapper">
                            <span class="erp-input-icon"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" class="erp-input" placeholder="branch@example.com" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Location -->
            <div class="erp-section">
                <h5 class="erp-section-title">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'موقع الفرع الجغرافي' : 'Location Details' }}</span>
                </h5>

                <div class="row g-4">
                    {{-- City --}}
                    @if(app()->getLocale() == 'ar')
                        {{-- Arabic First --}}
                        <div class="col-md-6">
                            <label for="city_ar" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.city_ar') }}</label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-map"></i></span>
                                <input type="text" id="city_ar" name="city_ar" class="erp-input" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;" dir="rtl" value="{{ old('city_ar') }}" placeholder="مثال: الرياض">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="city_en" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.city_en') }}</label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-map-fill"></i></span>
                                <input type="text" id="city_en" name="city_en" class="erp-input" style="text-align: left !important;" dir="ltr" value="{{ old('city_en') }}" placeholder="e.g. Riyadh">
                            </div>
                        </div>
                    @else
                        {{-- English First --}}
                        <div class="col-md-6">
                            <label for="city_en" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.city_en') }}</label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-map-fill"></i></span>
                                <input type="text" id="city_en" name="city_en" class="erp-input" style="text-align: left !important;" dir="ltr" value="{{ old('city_en') }}" placeholder="e.g. Riyadh">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="city_ar" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.city_ar') }}</label>
                            <div class="erp-input-wrapper">
                                <span class="erp-input-icon"><i class="bi bi-map"></i></span>
                                <input type="text" id="city_ar" name="city_ar" class="erp-input" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;" dir="rtl" value="{{ old('city_ar') }}" placeholder="مثال: الرياض">
                            </div>
                        </div>
                    @endif

                    {{-- Address --}}
                    @if(app()->getLocale() == 'ar')
                        {{-- Arabic First --}}
                        <div class="col-md-6">
                            <label for="address_ar" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.address_ar') }}</label>
                            <textarea id="address_ar" name="address_ar" class="erp-textarea" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;" dir="rtl" rows="3" placeholder="مثال: شارع العليا، حي المروج">{{ old('address_ar') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="address_en" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.address_en') }}</label>
                            <textarea id="address_en" name="address_en" class="erp-textarea" style="text-align: left !important;" dir="ltr" rows="3" placeholder="e.g. Olaya St, Muruj District">{{ old('address_en') }}</textarea>
                        </div>
                    @else
                        {{-- English First --}}
                        <div class="col-md-6">
                            <label for="address_en" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.address_en') }}</label>
                            <textarea id="address_en" name="address_en" class="erp-textarea" style="text-align: left !important;" dir="ltr" rows="3" placeholder="e.g. Olaya St, Muruj District">{{ old('address_en') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="address_ar" class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.address_ar') }}</label>
                            <textarea id="address_ar" name="address_ar" class="erp-textarea" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;" dir="rtl" rows="3" placeholder="مثال: شارع العليا، حي المروج">{{ old('address_ar') }}</textarea>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SECTION 3: Settings -->
            <div class="erp-section">
                <h5 class="erp-section-title">
                    <i class="bi bi-sliders"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'إعدادات الفرع' : 'Branch Settings' }}</span>
                </h5>

                <div class="row g-4">
                    {{-- Branch Type --}}
                    <div class="col-md-6">
                        <label class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ app()->getLocale() == 'ar' ? 'نوع الفرع' : 'Branch Type' }}</label>
                        <div class="erp-input-wrapper">
                            <span class="erp-input-icon"><i class="bi bi-diagram-3"></i></span>
                            <select name="branch_type" class="erp-input" style="cursor: pointer; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">
                                <option value="Branch">{{ app()->getLocale() == 'ar' ? 'فرع عادي' : 'Branch' }}</option>
                                <option value="Main Branch">{{ app()->getLocale() == 'ar' ? 'الفرع الرئيسي' : 'Main Branch' }}</option>
                                <option value="Warehouse">{{ app()->getLocale() == 'ar' ? 'مستودع / مخزن' : 'Warehouse' }}</option>
                                <option value="Outlet">{{ app()->getLocale() == 'ar' ? 'منفذ بيع' : 'Outlet' }}</option>
                                <option value="Kiosk">{{ app()->getLocale() == 'ar' ? 'كشك' : 'Kiosk' }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Opening Date --}}
                    <div class="col-md-6">
                        <label class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ app()->getLocale() == 'ar' ? 'تاريخ الافتتاح' : 'Opening Date' }}</label>
                        <div class="erp-input-wrapper">
                            <span class="erp-input-icon"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" name="opening_date" class="erp-input" style="cursor: pointer; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">
                        </div>
                    </div>

                    {{-- Status Toggle --}}
                    <div class="col-md-6">
                        <label class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ __('pos.active') }}</label>
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3" style="border: 1px dashed #cbd5e1; border-radius: 10px;">
                            <span class="small text-muted"><i class="bi bi-info-circle me-1"></i>{{ app()->getLocale() == 'ar' ? 'حالة تنشيط الفرع في النظام' : 'Branch active status in the system' }}</span>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked style="cursor: pointer; width: 45px; height: 22px;">
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="col-md-6">
                        <label class="erp-label" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;">{{ app()->getLocale() == 'ar' ? 'ملاحظات داخلية' : 'Internal Notes' }}</label>
                        <textarea name="notes" class="erp-textarea" rows="3" placeholder="{{ app()->getLocale() == 'ar' ? 'ملاحظات سرية تخص الفرع...' : 'Internal notes about the branch...' }}" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }} !important;"></textarea>
                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="erp-footer">
                <a href="{{ route('branches.index') }}" class="btn-erp-secondary"><i class="bi bi-x-circle me-1"></i> {{ __('pos.exit') }}</a>
                <div class="d-flex gap-2">
                    <button type="reset" class="btn-erp-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('pos.clear') }}</button>
                    <button type="submit" class="btn-erp-primary"><i class="bi bi-save2-fill"></i> {{ __('pos.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Autofill helper for fields on submit
    document.getElementById('branchForm').addEventListener('submit', function(e) {
        const nameAr = document.getElementById('name_ar').value.trim();
        const nameEn = document.getElementById('name_en').value.trim();
        if (nameAr && !nameEn) {
            document.getElementById('name_en').value = nameAr;
        } else if (!nameAr && nameEn) {
            document.getElementById('name_ar').value = nameEn;
        }

        const cityAr = document.getElementById('city_ar').value.trim();
        const cityEn = document.getElementById('city_en').value.trim();
        if (cityAr && !cityEn) {
            document.getElementById('city_en').value = cityAr;
        } else if (!cityAr && cityEn) {
            document.getElementById('city_ar').value = cityEn;
        }

        const addressAr = document.getElementById('address_ar').value.trim();
        const addressEn = document.getElementById('address_en').value.trim();
        if (addressAr && !addressEn) {
            document.getElementById('address_en').value = addressAr;
        } else if (!addressAr && addressEn) {
            document.getElementById('address_ar').value = addressEn;
        }

        const managerAr = document.getElementById('manager_ar').value.trim();
        const managerEn = document.getElementById('manager_en').value.trim();
        if (managerAr && !managerEn) {
            document.getElementById('manager_en').value = managerAr;
        } else if (!managerAr && managerEn) {
            document.getElementById('manager_ar').value = managerEn;
        }
    });
</script>
@endsection

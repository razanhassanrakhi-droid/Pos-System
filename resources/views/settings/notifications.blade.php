@extends('layouts.app')

@section('title', __('pos.notification_settings') ?? 'Notification Settings')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden main-settings-card">
                <!-- Premium Dark Header -->
                <div class="card-header border-0 p-4 position-relative premium-settings-header" style="background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%); overflow: hidden;">
                    <!-- Dotted Pattern Overlay -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px); background-size: 16px 16px; pointer-events: none; opacity: 0.8; z-index: 1;"></div>
                    
                    <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                        <div class="d-flex align-items-center">
                            <!-- Glowing Bell Icon Container -->
                            <div class="premium-bell-wrapper d-flex align-items-center justify-content-center me-3 ms-3">
                                <i class="bi bi-bell-fill fs-4 text-white"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 text-white fw-bold header-title">
                                    {{ __('pos.notification_settings') ?? 'Notification Settings' }}
                                </h4>
                                <div class="small mt-1 header-subtitle" style="color: #94a3b8; font-weight: 500;">
                                    {{ app()->getLocale() == 'ar' ? 'إدارة تفضيلات وتنبيهات المخزون الذكية' : 'Manage smart inventory preferences and alerts' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 bg-card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center p-3" role="alert" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #059669 !important;">
                            <i class="bi bi-check-circle-fill me-2 ms-2 fs-5"></i>
                            <div class="fw-semibold">{{ session('success') }}</div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(30%) sepia(30%) saturate(1000%) hue-rotate(120deg);"></button>
                        </div>
                    @endif

                    <form action="{{ route('settings.notifications.save') }}" method="POST">
                        @csrf

                        <!-- CATEGORY: INVENTORY -->
                        <div class="settings-section mb-4">
                            <div class="d-flex align-items-center mb-4 section-title-wrapper">
                                <div class="section-indicator me-2 ms-2"></div>
                                <h6 class="fw-bold mb-0 text-dark section-title" style="font-size: 1.1rem; letter-spacing: -0.2px;">
                                    {{ __('pos.inventory_notifications') ?? 'Inventory Notifications' }}
                                </h6>
                            </div>

                            <div class="card border-0 p-4 custom-inner-card" style="background: #f8fafc; border-radius: 16px; border: 1px solid #f1f5f9;">
                                <div class="row g-4">
                                    <!-- LOW STOCK -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 switch-card-item rounded-3">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div class="switch-text-content" style="flex-grow: 1; padding-left: 10px; padding-right: 10px;">
                                                    <label class="fw-bold text-dark mb-1 d-block" for="inv_low_stock" style="font-size: 0.95rem; cursor: pointer;">
                                                        {{ __('pos.low_stock') }}
                                                    </label>
                                                    <div class="text-muted small" style="font-size: 0.825rem; line-height: 1.4;">
                                                        {{ __('pos.low_stock_desc') ?? 'Receive warnings when items reach minimum stock.' }}
                                                    </div>
                                                </div>
                                                <div class="switch-input-wrapper form-check form-switch mb-0 d-flex align-items-center justify-content-end" style="flex-shrink: 0; padding-left:0;">
                                                    <input type="hidden" name="settings[inventory][low_stock]" value="0">
                                                    <input class="form-check-input custom-premium-switch m-0" type="checkbox" role="switch" id="inv_low_stock" name="settings[inventory][low_stock]" value="1" {{ ($settings['inventory']['low_stock'] ?? true) ? 'checked' : '' }} style="margin-left: 0 !important;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- OUT OF STOCK -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 switch-card-item rounded-3">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div class="switch-text-content" style="flex-grow: 1; padding-left: 10px; padding-right: 10px;">
                                                    <label class="fw-bold text-dark mb-1 d-block" for="inv_out_of_stock" style="font-size: 0.95rem; cursor: pointer;">
                                                        {{ __('pos.out_of_stock') }}
                                                    </label>
                                                    <div class="text-muted small" style="font-size: 0.825rem; line-height: 1.4;">
                                                        {{ __('pos.out_of_stock_desc') ?? 'Critical alerts when products run completely out of stock.' }}
                                                    </div>
                                                </div>
                                                <div class="switch-input-wrapper form-check form-switch mb-0 d-flex align-items-center justify-content-end" style="flex-shrink: 0; padding-left:0;">
                                                    <input type="hidden" name="settings[inventory][out_of_stock]" value="0">
                                                    <input class="form-check-input custom-premium-switch m-0" type="checkbox" role="switch" id="inv_out_of_stock" name="settings[inventory][out_of_stock]" value="1" {{ ($settings['inventory']['out_of_stock'] ?? true) ? 'checked' : '' }} style="margin-left: 0 !important;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EXPIRING SOON -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 switch-card-item rounded-3">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div class="switch-text-content" style="flex-grow: 1; padding-left: 10px; padding-right: 10px;">
                                                    <label class="fw-bold text-dark mb-1 d-block" for="inv_expiring_soon" style="font-size: 0.95rem; cursor: pointer;">
                                                        {{ __('pos.expiring_soon') }}
                                                    </label>
                                                    <div class="text-muted small" style="font-size: 0.825rem; line-height: 1.4;">
                                                        {{ __('pos.expiring_soon_desc') ?? 'Notifications for batches approaching expiry dates.' }}
                                                    </div>
                                                </div>
                                                <div class="switch-input-wrapper form-check form-switch mb-0 d-flex align-items-center justify-content-end" style="flex-shrink: 0; padding-left:0;">
                                                    <input type="hidden" name="settings[inventory][expiring_soon]" value="0">
                                                    <input class="form-check-input custom-premium-switch m-0" type="checkbox" role="switch" id="inv_expiring_soon" name="settings[inventory][expiring_soon]" value="1" {{ ($settings['inventory']['expiring_soon'] ?? true) ? 'checked' : '' }} style="margin-left: 0 !important;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EXPIRED PRODUCTS -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 switch-card-item rounded-3">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div class="switch-text-content" style="flex-grow: 1; padding-left: 10px; padding-right: 10px;">
                                                    <label class="fw-bold text-dark mb-1 d-block" for="inv_expired" style="font-size: 0.95rem; cursor: pointer;">
                                                        {{ __('pos.expired_products') ?? 'Expired Products' }}
                                                    </label>
                                                    <div class="text-muted small" style="font-size: 0.825rem; line-height: 1.4;">
                                                        {{ __('pos.expired_desc') ?? 'Critical alerts for expired batches remaining in inventory.' }}
                                                    </div>
                                                </div>
                                                <div class="switch-input-wrapper form-check form-switch mb-0 d-flex align-items-center justify-content-end" style="flex-shrink: 0; padding-left:0;">
                                                    <input type="hidden" name="settings[inventory][expired]" value="0">
                                                    <input class="form-check-input custom-premium-switch m-0" type="checkbox" role="switch" id="inv_expired" name="settings[inventory][expired]" value="1" {{ ($settings['inventory']['expired'] ?? true) ? 'checked' : '' }} style="margin-left: 0 !important;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EXPIRY WARNING PERIOD SELECT -->
                                    <div class="col-md-12 mt-4 pt-4 border-top border-light-subtle">
                                        <div class="row align-items-center">
                                            <div class="col-lg-6 mb-3 mb-lg-0">
                                                <label for="expiry_warning_period" class="form-label fw-bold text-dark mb-1 d-flex align-items-center" style="font-size: 0.95rem;">
                                                    <i class="bi bi-calendar-event me-2 ms-2 text-primary fs-5"></i>
                                                    {{ __('pos.expiry_warning_period') ?? 'Expiry Warning Period' }}
                                                </label>
                                                <div class="text-muted small" style="font-size: 0.825rem; line-height: 1.4; padding-left: 10px; padding-right: 10px;">
                                                    {{ __('pos.expiry_warning_period_desc') ?? 'Select the warning window (in days) for batches approaching expiry.' }}
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="position-relative d-flex justify-content-lg-end">
                                                    <select class="form-select custom-select-premium" id="expiry_warning_period" name="settings[inventory][expiry_warning_period]" style="max-width: 300px; width: 100%;">
                                                        <option value="7" {{ ($settings['inventory']['expiry_warning_period'] ?? 30) == 7 ? 'selected' : '' }}>7 {{ __('pos.days') ?? 'Days' }}</option>
                                                        <option value="15" {{ ($settings['inventory']['expiry_warning_period'] ?? 30) == 15 ? 'selected' : '' }}>15 {{ __('pos.days') ?? 'Days' }}</option>
                                                        <option value="30" {{ ($settings['inventory']['expiry_warning_period'] ?? 30) == 30 ? 'selected' : '' }}>30 {{ __('pos.days') ?? 'Days' }}</option>
                                                        <option value="60" {{ ($settings['inventory']['expiry_warning_period'] ?? 30) == 60 ? 'selected' : '' }}>60 {{ __('pos.days') ?? 'Days' }}</option>
                                                        <option value="90" {{ ($settings['inventory']['expiry_warning_period'] ?? 30) == 90 ? 'selected' : '' }}>90 {{ __('pos.days') ?? 'Days' }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CTA BUTTON -->
                        <div class="mt-4 pt-3 text-end d-flex justify-content-end">
                            <button type="submit" class="btn btn-save-premium">
                                <i class="bi bi-check2-circle me-1 ms-1 fs-5"></i>
                                <span>{{ __('pos.save_changes') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Header Bell Wrapper */
    .premium-bell-wrapper {
        width: 48px; 
        height: 48px; 
        border-radius: 14px; 
        background: rgba(59, 130, 246, 0.15); 
        border: 1px solid rgba(59, 130, 246, 0.3); 
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.25);
    }
    
    /* Section Title Indicator Bar */
    .section-indicator {
        width: 4px; 
        height: 18px; 
        background: #2563eb; 
        border-radius: 2px;
    }
    
    /* Premium Switch Customization */
    .custom-premium-switch {
        float: none !important;
        margin: 0 !important;
        width: 2.8em !important;
        height: 1.5em !important;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23cbd5e1' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 5a3 3 0 110 6 3 3 0 010-6z'/%3e%3c/svg%3e") !important;
    }
    .custom-premium-switch:focus {
        box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.15) !important;
        border-color: #93c5fd !important;
    }
    .custom-premium-switch:checked {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
    }

    /* Premium Select Dropdown */
    .custom-select-premium {
        height: 48px; 
        border-radius: 12px; 
        border: 1.5px solid #e2e8f0; 
        font-size: 0.95rem; 
        font-weight: 600; 
        padding: 0 1.25rem;
        background-color: #ffffff;
        color: #1e293b;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    .custom-select-premium:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
        outline: none;
    }

    /* Premium CTA Save Button */
    .btn-save-premium {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
        color: #ffffff !important;
        border: none; 
        font-size: 0.95rem; 
        font-weight: 600; 
        padding: 12px 36px; 
        border-radius: 12px; 
        box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.35); 
        transition: all 0.2s ease-in-out;
        display: inline-flex;
        align-items: center;
    }
    .btn-save-premium:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.45);
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }
    .btn-save-premium:active {
        transform: translateY(0);
        box-shadow: 0 6px 12px -4px rgba(37, 99, 235, 0.35);
    }
    
    /* Premium Hover effect on items */
    .switch-card-item {
        transition: background-color 0.2s;
    }
    .switch-card-item:hover {
        background-color: rgba(0, 0, 0, 0.015);
    }

    /* Dark Mode Theme Support */
    html[data-app-theme="dark"] .main-settings-card {
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    html[data-app-theme="dark"] .bg-card-body {
        background-color: #0f172a !important;
    }
    html[data-app-theme="dark"] .custom-inner-card {
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
    }
    html[data-app-theme="dark"] .text-dark {
        color: #f8fafc !important;
    }
    html[data-app-theme="dark"] .text-muted {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .switch-card-item:hover {
        background-color: rgba(255, 255, 255, 0.02);
    }
    html[data-app-theme="dark"] .custom-select-premium {
        background-color: #0f172a;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.15);
    }
    html[data-app-theme="dark"] .border-top {
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
</style>
@endsection

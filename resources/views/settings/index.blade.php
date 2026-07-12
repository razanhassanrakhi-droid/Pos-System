@extends('layouts.app')

@section('title', __('pos.company_information'))

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

    /* Subheadings styling */
    .saas-section-title {
        font-size: 0.88rem;
        font-weight: 800;
        color: var(--saas-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        padding-left: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    html[dir="rtl"] .saas-section-title {
        padding-left: 0;
        padding-right: 12px;
    }
    .saas-section-title::before {
        content: '';
        position: absolute;
        left: 0; top: 2px; bottom: 2px;
        width: 4px;
        border-radius: 4px;
        background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
    }
    html[dir="rtl"] .saas-section-title::before {
        left: auto;
        right: 0;
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
    .saas-textarea {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px 16px;
        font-size: 0.88rem;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
    }
    .saas-textarea:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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

    /* Dark Mode Overrides */
    html[data-app-theme="dark"] .pm-card-premium { background: #0f172a; border-color: #334155; }
    html[data-app-theme="dark"] .input-group-saas { background: #1e293b; border-color: #334155; }
    html[data-app-theme="dark"] .input-group-text-saas { background: #0f172a; color: #94a3b8; }
    html[data-app-theme="dark"] .saas-input { color: #f8fafc; }
    html[data-app-theme="dark"] .saas-textarea { background: #1e293b; border-color: #334155; color: #f8fafc; }
    html[data-app-theme="dark"] .saas-label { color: #cbd5e1; }

    /* Custom File Input Styling for Translation */
    #logo-file-input {
        color: transparent;
    }
    #logo-file-input::-webkit-file-upload-button {
        visibility: hidden;
        display: none;
    }
    #logo-file-input::before {
        content: "{{ app()->getLocale() == 'ar' ? 'اختر ملف الشعار' : 'Choose Logo File' }}";
        display: inline-block;
        background: var(--saas-primary-light, rgba(99, 102, 241, 0.08));
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: 10px;
        padding: 8px 16px;
        outline: none;
        white-space: nowrap;
        -webkit-user-select: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.8rem;
        color: var(--saas-primary, #6366f1);
        transition: all 0.2s;
        text-align: center;
        width: 100%;
        margin-bottom: 8px;
    }
    #logo-file-input:hover::before {
        background: var(--saas-primary, #6366f1);
        color: #ffffff;
        border-color: var(--saas-primary, #6366f1);
    }
    html[data-app-theme="dark"] #logo-file-input::before {
        background: rgba(99, 102, 241, 0.15);
        color: #a5b4fc;
        border-color: rgba(99, 102, 241, 0.3);
    }
    html[data-app-theme="dark"] #logo-file-input:hover::before {
        background: var(--saas-primary, #6366f1);
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <div class="pm-card-premium">
                {{-- Premium Header Layout --}}
                <div class="pm-modal-header-premium p-4">
                    <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                    <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                    <div class="d-flex align-items-center gap-3 position-relative" style="z-index: 2; width: 100%;">
                        <div class="pm-modal-icon-premium">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <h4 class="pm-modal-title-premium">{{ __('pos.company_information') }}</h4>
                            <p class="pm-modal-sub-premium mb-0">{{ app()->getLocale() == 'ar' ? 'تعديل شعار الشركة، الاسم التجاري، الرقم الضريبي والاتصال' : 'Modify logo, trade names, VAT, address and footer' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: var(--saas-success);">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Sidebar: Logo Management -->
                            <div class="col-md-3 text-center border-end pe-md-4 mb-4">
                                <label class="saas-label mb-3 text-uppercase small">
                                    <i class="bi bi-image me-1"></i>{{ __('pos.company_logo') }}
                                </label>
                                <div class="position-relative mb-3 d-inline-block" id="logo-preview-container">
                                    @if($setting->company_logo)
                                        <img src="{{ asset('storage/' . $setting->company_logo) }}" alt="Logo" class="img-thumbnail shadow-sm p-2 bg-white" style="width: 160px; height: 160px; object-fit: contain; border-radius: 20px;">
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute" style="top: -10px; right: -10px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(220,53,69,0.3);" onclick="removeCompanyLogo()" title="{{ __('pos.delete') ?? 'Delete' }}">
                                            <i class="bi bi-trash-fill fs-6"></i>
                                        </button>
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 160px; height: 160px; border: 2px dashed #cbd5e0; border-radius: 20px;">
                                            <i class="bi bi-building text-muted display-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="remove_logo" id="remove_logo_input" value="0">
                                <div class="mt-3">
                                    <input type="file" name="company_logo" id="logo-file-input" class="form-control form-control-sm @error('company_logo') is-invalid @enderror shadow-none" onchange="resetRemoveLogoInput(this)" style="border-radius: 10px;">
                                    @error('company_logo') <div class="invalid-feedback text-start">{{ $message }}</div> @enderror
                                    <div class="mt-2 text-start">
                                        <small class="text-muted d-block" style="font-size: 0.72rem;"><i class="bi bi-info-circle me-1"></i>{{ __('pos.recommended_logo_format') }}</small>
                                        <small class="text-muted d-block" style="font-size: 0.72rem;"><i class="bi bi-hdd me-1"></i>Max: 2MB</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Content: Information Details -->
                            <div class="col-md-9 ps-md-4">
                                <!-- Section: General Information -->
                                <div class="mb-4">
                                    <h6 class="saas-section-title">
                                        {{ __('pos.general_information') }}
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="saas-label">{{ __('pos.company_name_en') }} <span class="text-danger">*</span></label>
                                            <div class="input-group-saas">
                                                <span class="input-group-text-saas"><i class="bi bi-alphabet"></i></span>
                                                <input type="text" name="company_name_en" class="saas-input" value="{{ old('company_name_en', $setting->company_name['en'] ?? '') }}" placeholder="e.g. My Store (Optional)">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="saas-label">{{ __('pos.company_name_ar') }} <span class="text-danger">*</span></label>
                                            <div class="input-group-saas">
                                                <input type="text" name="company_name_ar" class="saas-input text-end" dir="rtl" value="{{ old('company_name_ar', $setting->company_name['ar'] ?? '') }}" required placeholder="مثلاً: متجري">
                                                <span class="input-group-text-saas"><i class="bi bi-translate"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="saas-label">{{ __('pos.tax_number') }} (VAT)</label>
                                            <div class="input-group-saas">
                                                <span class="input-group-text-saas"><i class="bi bi-hash"></i></span>
                                                <input type="text" name="tax_number" class="saas-input" value="{{ old('tax_number', $setting->tax_number) }}" placeholder="300000000000003">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="saas-label">{{ __('pos.registration_number') }}</label>
                                            <div class="input-group-saas">
                                                <span class="input-group-text-saas"><i class="bi bi-card-heading"></i></span>
                                                <input type="text" name="registration_number" class="saas-input" value="{{ old('registration_number', $setting->registration_number) }}" placeholder="1010000000">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="saas-label">{{ __('pos.currency_en') ?? 'Currency (EN)' }}</label>
                                            <div class="input-group-saas">
                                                <span class="input-group-text-saas"><i class="bi bi-cash"></i></span>
                                                <input type="text" name="currency_en" class="saas-input" value="{{ old('currency_en', $setting->currency_raw['en'] ?? '') }}" placeholder="e.g. SAR or Dollar">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="saas-label text-end d-block">{{ __('pos.currency_ar') ?? 'Currency (AR)' }}</label>
                                            <div class="input-group-saas">
                                                <input type="text" name="currency_ar" class="saas-input text-end" dir="rtl" value="{{ old('currency_ar', $setting->currency_raw['ar'] ?? '') }}" placeholder="مثلاً: ريال أو دولار">
                                                <span class="input-group-text-saas"><i class="bi bi-currency-exchange"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="saas-label">{{ __('pos.default_tax') }} <span class="text-danger">*</span></label>
                                            <div class="input-group-saas">
                                                <span class="input-group-text-saas"><i class="bi bi-percent"></i></span>
                                                <input type="number" name="default_tax" class="saas-input @error('default_tax') is-invalid @enderror" value="{{ old('default_tax', $setting->default_tax) }}" step="0.01" min="0" max="100" required placeholder="15.00">
                                            </div>
                                            @error('default_tax') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 opacity-25">

                                <!-- Section: Contact Details -->
                                <div class="mb-4">
                                    <h6 class="saas-section-title">
                                        {{ __('pos.contact_details') }}
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="saas-label">{{ __('pos.company_phone') }}</label>
                                            <div class="input-group-saas">
                                                <span class="input-group-text-saas"><i class="bi bi-telephone"></i></span>
                                                <input type="text" name="company_phone" class="saas-input" value="{{ old('company_phone', $setting->company_phone) }}" placeholder="+966 5XXXXXXXX">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="saas-label">{{ __('pos.company_email') }}</label>
                                            <div class="input-group-saas">
                                                <span class="input-group-text-saas"><i class="bi bi-envelope-at"></i></span>
                                                <input type="email" name="company_email" class="saas-input" value="{{ old('company_email', $setting->company_email) }}" placeholder="info@example.com">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 opacity-25">

                                <!-- Section: Localization & Footer -->
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <h6 class="saas-section-title">
                                            {{ __('pos.company_address') }}
                                        </h6>
                                        <div class="mb-3">
                                            <label class="saas-label">{{ __('pos.address_en_label') }}</label>
                                            <textarea name="company_address_en" class="saas-textarea" rows="2" placeholder="{{ __('pos.address_placeholder_en') }}">{{ old('company_address_en', $setting->company_address['en'] ?? '') }}</textarea>
                                        </div>
                                        <div>
                                            <label class="saas-label text-end d-block">{{ __('pos.address_ar_label') }}</label>
                                            <textarea name="company_address_ar" class="saas-textarea text-end" dir="rtl" rows="2" placeholder="{{ __('pos.address_placeholder_ar') }}">{{ old('company_address_ar', $setting->company_address['ar'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="saas-section-title">
                                            {{ __('pos.footer_text') }}
                                        </h6>
                                        <div class="mb-3">
                                            <label class="saas-label">{{ __('pos.footer_en_label') }}</label>
                                            <textarea name="footer_text_en" class="saas-textarea" rows="2" placeholder="{{ __('pos.footer_placeholder_en') }}">{{ old('footer_text_en', $setting->footer_text['en'] ?? '') }}</textarea>
                                        </div>
                                        <div>
                                            <label class="saas-label text-end d-block">{{ __('pos.footer_ar_label') }}</label>
                                            <textarea name="footer_text_ar" class="saas-textarea text-end" dir="rtl" rows="2" placeholder="{{ __('pos.footer_placeholder_ar') }}">{{ old('footer_text_ar', $setting->footer_text['ar'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 text-end">
                                    <button type="submit" class="btn-premium-save">
                                        <i class="bi bi-save2-fill"></i> {{ __('pos.update_settings') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function removeCompanyLogo() {
        if (confirm("{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من حذف الشعار الحالي؟' : 'Are you sure you want to delete the current logo?' }}")) {
            document.getElementById('remove_logo_input').value = '1';
            const previewContainer = document.getElementById('logo-preview-container');
            previewContainer.innerHTML = `
                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 160px; height: 160px; border: 2px dashed #cbd5e0; border-radius: 20px;">
                    <i class="bi bi-building text-muted display-4"></i>
                </div>
            `;
            document.getElementById('logo-file-input').value = '';
        }
    }
    function resetRemoveLogoInput(input) {
        document.getElementById('remove_logo_input').value = '0';
        
        // Live image preview logic
        if (input && input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.getElementById('logo-preview-container');
                previewContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Logo" class="img-thumbnail shadow-sm p-2 bg-white" style="width: 160px; height: 160px; object-fit: contain; border-radius: 20px;">
                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute" style="top: -10px; right: -10px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(220,53,69,0.3);" onclick="removeCompanyLogo()" title="{{ __('pos.delete') ?? 'Delete' }}">
                        <i class="bi bi-trash-fill fs-6"></i>
                    </button>
                `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

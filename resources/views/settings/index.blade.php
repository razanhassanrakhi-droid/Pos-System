@extends('layouts.app')

@section('title', __('pos.company_information'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="bi bi-building-fill me-2"></i>{{ __('pos.company_information') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
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
                                <label class="form-label fw-bold d-block mb-3 text-secondary text-uppercase small">
                                    <i class="bi bi-image me-1"></i>{{ __('pos.company_logo') }}
                                </label>
                                <div class="position-relative mb-3 d-inline-block">
                                    @if($setting->company_logo)
                                        <img src="{{ asset('storage/' . $setting->company_logo) }}" alt="Logo" class="img-thumbnail shadow-sm p-2 bg-white" style="width: 160px; height: 160px; object-fit: contain; border-radius: 12px;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 160px; height: 160px; border: 2px dashed #cbd5e0; border-radius: 12px;">
                                            <i class="bi bi-building text-muted display-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <input type="file" name="company_logo" class="form-control form-control-sm @error('company_logo') is-invalid @enderror shadow-none">
                                    @error('company_logo') <div class="invalid-feedback text-start">{{ $message }}</div> @enderror
                                    <div class="mt-2 text-start">
                                        <small class="text-muted d-block"><i class="bi bi-info-circle me-1"></i>{{ __('pos.recommended_logo_format') }}</small>
                                        <small class="text-muted d-block"><i class="bi bi-hdd me-1"></i>Max: 2MB</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Content: Information Details -->
                            <div class="col-md-9 ps-md-4">
                                <!-- Section: General Information -->
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3 text-primary border-start border-4 border-primary ps-2">
                                        {{ __('pos.general_information') }}
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">{{ __('pos.company_name_en') }} <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-alphabet"></i></span>
                                                <input type="text" name="company_name_en" class="form-control border-start-0 ps-0" value="{{ old('company_name_en', $setting->company_name['en'] ?? '') }}" required placeholder="e.g. My Store">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">{{ __('pos.company_name_ar') }} <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" name="company_name_ar" class="form-control border-end-0 pe-0 text-end" dir="rtl" value="{{ old('company_name_ar', $setting->company_name['ar'] ?? '') }}" required placeholder="مثلاً: متجري">
                                                <span class="input-group-text bg-light border-start-0 text-muted"><i class="bi bi-translate"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">{{ __('pos.tax_number') }} (VAT)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-hash"></i></span>
                                                <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $setting->tax_number) }}" placeholder="300000000000003">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">{{ __('pos.registration_number') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-card-heading"></i></span>
                                                <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number', $setting->registration_number) }}" placeholder="1010000000">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">{{ __('pos.currency_en') ?? 'Currency (EN)' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-cash"></i></span>
                                                <input type="text" name="currency_en" class="form-control" value="{{ old('currency_en', $setting->currency_raw['en'] ?? '') }}" placeholder="e.g. SAR or Dollar">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-end d-block">{{ __('pos.currency_ar') ?? 'Currency (AR)' }}</label>
                                            <div class="input-group">
                                                <input type="text" name="currency_ar" class="form-control text-end" dir="rtl" value="{{ old('currency_ar', $setting->currency_raw['ar'] ?? '') }}" placeholder="مثلاً: ريال أو دولار">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-currency-exchange"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">{{ __('pos.default_tax') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-percent"></i></span>
                                                <input type="number" name="default_tax" class="form-control @error('default_tax') is-invalid @enderror" value="{{ old('default_tax', $setting->default_tax) }}" step="0.01" min="0" max="100" required placeholder="15.00">
                                                @error('default_tax') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 opacity-25">

                                <!-- Section: Contact Details -->
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3 text-primary border-start border-4 border-primary ps-2">
                                        {{ __('pos.contact_details') }}
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">{{ __('pos.company_phone') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                                <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $setting->company_phone) }}" placeholder="+966 5XXXXXXXX">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">{{ __('pos.company_email') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope-at"></i></span>
                                                <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $setting->company_email) }}" placeholder="info@example.com">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 opacity-25">

                                <!-- Section: Localization & Footer -->
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3 text-primary border-start border-4 border-primary ps-2">
                                            {{ __('pos.company_address') }}
                                        </h6>
                                        <div class="mb-3">
                                            <label class="small text-muted mb-1">{{ __('pos.address_en_label') }}</label>
                                            <textarea name="company_address_en" class="form-control" rows="2" placeholder="{{ __('pos.address_placeholder_en') }}">{{ old('company_address_en', $setting->company_address['en'] ?? '') }}</textarea>
                                        </div>
                                        <div>
                                            <label class="small text-muted mb-1 d-block text-end">{{ __('pos.address_ar_label') }}</label>
                                            <textarea name="company_address_ar" class="form-control text-end" dir="rtl" rows="2" placeholder="{{ __('pos.address_placeholder_ar') }}">{{ old('company_address_ar', $setting->company_address['ar'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3 text-primary border-start border-4 border-primary ps-2">
                                            {{ __('pos.footer_text') }}
                                        </h6>
                                        <div class="mb-3">
                                            <label class="small text-muted mb-1">{{ __('pos.footer_en_label') }}</label>
                                            <textarea name="footer_text_en" class="form-control" rows="2" placeholder="{{ __('pos.footer_placeholder_en') }}">{{ old('footer_text_en', $setting->footer_text['en'] ?? '') }}</textarea>
                                        </div>
                                        <div>
                                            <label class="small text-muted mb-1 d-block text-end">{{ __('pos.footer_ar_label') }}</label>
                                            <textarea name="footer_text_ar" class="form-control text-end" dir="rtl" rows="2" placeholder="{{ __('pos.footer_placeholder_ar') }}">{{ old('footer_text_ar', $setting->footer_text['ar'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm rounded-3">
                                        <i class="bi bi-save2-fill me-2"></i>{{ __('pos.update_settings') }}
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

<style>
    .input-group-text { border-color: #dee2e6; }
    .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.1); }
    .img-thumbnail { border-color: #e2e8f0; }
    .border-start-0 { border-left: 0 !important; }
    .border-end-0 { border-right: 0 !important; }
    hr { border-top: 1px solid #e2e8f0; }
</style>
@endsection

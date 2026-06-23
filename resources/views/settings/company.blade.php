@extends('layouts.app')

@section('title', __('pos.company_information'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Company Information -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-building me-2 text-primary"></i>{{ __('pos.company_information') }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('pos.company_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="company_name" value="Global Tech Co." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('pos.tax_number') }}</label>
                            <input type="text" class="form-control" name="tax_number" value="300000000000003">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('pos.phone') }}</label>
                            <input type="text" class="form-control" name="phone" value="0112345678">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('pos.mobile') }}</label>
                            <input type="text" class="form-control" name="mobile" value="0500000000">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('pos.email') }}</label>
                            <input type="email" class="form-control" name="email" value="info@globaltech.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('pos.website') }}</label>
                            <input type="url" class="form-control" name="website" value="https://globaltech.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('pos.address') }}</label>
                        <input type="text" class="form-control" name="address" value="Riyadh, Saudi Arabia">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('pos.currency') }}</label>
                            <select class="form-select" name="currency">
                                <option value="SAR" selected>SAR - Saudi Riyal</option>
                                <option value="USD">USD - US Dollar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('pos.invoice_language') }}</label>
                            <select class="form-select" name="invoice_language">
                                <option value="ar" selected>Arabic</option>
                                <option value="en">English</option>
                                <option value="both">Bilingual (AR & EN)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('pos.company_logo') }}</label>
                        <input type="file" class="form-control" name="logo" accept="image/*">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('pos.notes') }}</label>
                        <textarea class="form-control" name="notes" rows="3">Thank you for your business!</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ __('pos.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

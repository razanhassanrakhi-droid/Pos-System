@extends('layouts.app')

@section('title', __('pos.edit') . ' ' . __('pos.manage', ['page' => __('pos.branches')]))

@section('content')
<div class="card shadow-sm border-0 max-w-800 mx-auto" style="max-width: 800px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">{{ __('pos.edit') }} {{ __('pos.manage', ['page' => __('pos.branches')]) }}</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('branches.update', $branch->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name_ar" class="form-label fw-semibold">{{ __('pos.branch_name_ar') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name_ar" name="name_ar" 
                        value="{{ old('name_ar', $branch->getTranslation('name', 'ar')) }}" dir="rtl">
                </div>
                <div class="col-md-6">
                    <label for="name_en" class="form-label fw-semibold">{{ __('pos.branch_name_en') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name_en" name="name_en" 
                        value="{{ old('name_en', $branch->getTranslation('name', 'en')) }}" dir="ltr" placeholder="(Optional)">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="code" class="form-label fw-semibold">{{ __('pos.branch_code') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" 
                        value="{{ old('code', $branch->code) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">{{ __('pos.phone') }}</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                        value="{{ old('phone', $branch->phone) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="city_ar" class="form-label fw-semibold">{{ __('pos.city_ar') }}</label>
                    <input type="text" class="form-control" id="city_ar" name="city_ar" 
                        value="{{ old('city_ar', $branch->getTranslation('city', 'ar')) }}" dir="rtl">
                </div>
                <div class="col-md-6">
                    <label for="city_en" class="form-label fw-semibold">{{ __('pos.city_en') }}</label>
                    <input type="text" class="form-control" id="city_en" name="city_en" 
                        value="{{ old('city_en', $branch->getTranslation('city', 'en')) }}" dir="ltr">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="address_ar" class="form-label fw-semibold">{{ __('pos.address_ar') }}</label>
                    <textarea class="form-control" id="address_ar" name="address_ar" rows="3" dir="rtl">{{ old('address_ar', $branch->getTranslation('address', 'ar')) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="address_en" class="form-label fw-semibold">{{ __('pos.address_en') }}</label>
                    <textarea class="form-control" id="address_en" name="address_en" rows="3" dir="ltr">{{ old('address_en', $branch->getTranslation('address', 'en')) }}</textarea>
                </div>
            </div>

            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                    {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="is_active">{{ __('pos.active') }}</label>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ __('pos.update') }}</button>
                <a href="{{ route('branches.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ __('pos.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
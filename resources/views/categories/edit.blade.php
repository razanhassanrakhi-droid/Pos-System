@extends('layouts.app')

@section('title', __('pos.edit') . ' ' . __('pos.manage', ['page' => __('pos.categories')]))

@section('content')
<div class="card shadow-sm border-0 max-w-700 mx-auto" style="max-width: 700px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">{{ __('pos.edit') }} {{ __('pos.manage', ['page' => __('pos.categories')]) }}</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_active" value="1">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name_ar" class="form-label fw-semibold">{{ __('pos.category_name_ar') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name_ar" name="name_ar" value="{{ old('name_ar', $category->getTranslation('name', 'ar')) }}" dir="rtl">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name_en" class="form-label fw-semibold">{{ __('pos.category_name_en') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name_en" name="name_en" value="{{ old('name_en', $category->getTranslation('name', 'en')) }}" dir="ltr">
                </div>
            </div>
            

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ __('pos.update') }}</button>
                <a href="{{ route('categories.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ __('pos.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection

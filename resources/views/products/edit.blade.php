@extends('layouts.app')

@section('title', __('pos.edit') . ' ' . __('pos.manage', ['page' => __('pos.products')]))

@push('styles')
<style>
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
</style>
@endpush

@section('content')
<div class="pm-card-premium max-w-800 mx-auto" style="max-width: 800px;">
    {{-- Premium Header --}}
    <div class="pm-modal-header-premium">
        <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
        <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2; width: 100%;">
            <div class="pm-modal-icon-premium">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="pm-modal-title-premium">{{ __('pos.edit') }} {{ __('pos.manage', ['page' => __('pos.products')]) }}</h5>
                <p class="pm-modal-sub-premium">{{ __('product.product_management') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="pm-modal-close-premium d-flex align-items-center justify-content-center text-decoration-none">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('products.update', 1) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">{{ __('pos.product_name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="MacBook Pro 14" required>
                </div>
                <div class="col-md-6">
                    <label for="product_code" class="form-label fw-semibold">{{ __('pos.product_code') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="product_code" name="product_code" value="MBP-14-2023" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="category_id" class="form-label fw-semibold">{{ __('pos.category') }} <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="1" selected>Electronics</option>
                        <option value="2">Clothing</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="barcode" class="form-label fw-semibold">{{ __('pos.barcode') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="barcode" name="barcode" value="123456789012">
                        <button class="btn btn-outline-secondary" type="button"><i class="bi bi-upc-scan"></i></button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="sku" class="form-label fw-semibold">{{ __('pos.sku') ?? 'SKU' }}</label>
                    <input type="text" class="form-control" id="sku" name="sku" value="{{ $product->sku ?? '' }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="cost_price" class="form-label fw-semibold">{{ __('pos.cost_price') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="2000.00" required>
                </div>
                <div class="col-md-4">
                    <label for="sale_price" class="form-label fw-semibold">{{ __('pos.sale_price') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price" value="2500.00" required>
                </div>
                <div class="col-md-4">
                    <label for="quantity" class="form-label fw-semibold">{{ __('pos.quantity') }} <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="15" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="minimum_stock" class="form-label fw-semibold">{{ __('pos.minimum_stock') }}</label>
                    <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" value="5">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="image" class="form-label fw-semibold">{{ __('product.product_image') }}</label>
                    <div class="form-control d-flex align-items-center" style="padding: 0; background: transparent; position: relative; height: calc(3.5rem + 2px);">
                        <input type="file" name="image" id="image" class="border-0 w-100" accept="image/*" style="opacity: 0; position: absolute; z-index: 2; cursor: pointer; height: 100%;" onchange="document.getElementById('image_text').innerText = this.files[0] ? this.files[0].name : '{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}'">
                        <div class="d-flex align-items-center w-100 px-3" style="position: absolute; z-index: 1;">
                            <span class="badge bg-secondary me-2 px-3 py-2 fs-6">{{ app()->getLocale() == 'ar' ? 'اختر ملف' : 'Choose File' }}</span>
                            <span id="image_text" class="text-muted text-truncate">{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <img src="https://via.placeholder.com/80" class="rounded border" alt="Current Image">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label fw-semibold">{{ __('pos.status') ?? 'Status' }} <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Active" {{ old('status', $product->status ?? '') === 'Active' ? 'selected' : '' }}>{{ __('pos.active') ?? 'Active' }}</option>
                        <option value="Inactive" {{ old('status', $product->status ?? '') === 'Inactive' ? 'selected' : '' }}>{{ __('pos.inactive') ?? 'Inactive' }}</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">{{ __('pos.description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3">Apple MacBook Pro 14-inch</textarea>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ __('pos.update') }}</button>
                <a href="{{ route('products.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ __('pos.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection

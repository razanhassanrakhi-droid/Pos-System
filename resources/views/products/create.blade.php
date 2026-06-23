@extends('layouts.app')

@section('title', __('pos.add') . ' ' . __('pos.manage', ['page' => __('pos.products')]))

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
                <i class="bi bi-plus-circle-fill"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="pm-modal-title-premium">{{ __('pos.add') }} {{ __('pos.manage', ['page' => __('pos.products')]) }}</h5>
                <p class="pm-modal-sub-premium">{{ __('product.product_management') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="pm-modal-close-premium d-flex align-items-center justify-content-center text-decoration-none">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">{{ __('pos.product_name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6">
                    <label for="product_code" class="form-label fw-semibold">{{ __('pos.product_code') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="product_code" name="product_code" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="category_id" class="form-label fw-semibold">{{ __('pos.category') }} <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <option value="1">Electronics</option>
                        <option value="2">Clothing</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="barcode" class="form-label fw-semibold">{{ __('pos.barcode') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="barcode" name="barcode">
                        <button class="btn btn-outline-secondary" type="button"><i class="bi bi-upc-scan"></i></button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="sku" class="form-label fw-semibold">{{ __('pos.sku') ?? 'SKU' }}</label>
                    <input type="text" class="form-control" id="sku" name="sku">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="cost_price" class="form-label fw-semibold">{{ __('pos.cost_price') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" required>
                </div>
                <div class="col-md-4">
                    <label for="sale_price" class="form-label fw-semibold">{{ __('pos.sale_price') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price" required>
                </div>
                <div class="col-md-4">
                    <label for="quantity" class="form-label fw-semibold">{{ __('pos.quantity') }} <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                </div>
            </div>

                <div class="col-md-4">
                    <label for="minimum_stock" class="form-label fw-semibold">{{ __('pos.minimum_stock') }}</label>
                    <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" value="5">
                </div>
                <div class="col-md-4">
                    <label for="image" class="form-label fw-semibold">{{ __('pos.image') }}</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>
            </div>

            {{-- Product Status --}}
            <div class="mb-3 p-3 border rounded-3" style="background:#f8fafc;">
                <input type="hidden" name="status" id="page_status_hidden" value="Active">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-power fs-5 text-success" id="page_status_icon"></i>
                        <div>
                            <div class="fw-semibold" id="page_status_label">{{ app()->getLocale() == 'ar' ? 'المنتج نشط' : 'Product is Active' }}</div>
                            <div id="page_status_hint" style="font-size:0.75rem;color:#10b981;margin-top:2px;">
                                {{ app()->getLocale() == 'ar' ? '✅ يظهر في نقطة البيع ويمكن بيعه' : '✅ Visible in POS and available for sale' }}
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="page_status_toggle" checked style="width:2.5em;height:1.3em;">
                    </div>
                </div>
            </div>


            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">{{ __('pos.description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ __('pos.save') }}</button>
                <button type="reset" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('pos.clear') }}</button>
                <a href="{{ route('products.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ __('pos.exit') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('page_status_toggle').addEventListener('change', function() {
    let isActive = this.checked;
    let isAr = "{{ app()->getLocale() }}" === 'ar';
    document.getElementById('page_status_hidden').value = isActive ? 'Active' : 'Inactive';
    document.getElementById('page_status_label').textContent = isActive
        ? (isAr ? 'المنتج نشط' : 'Product is Active')
        : (isAr ? 'المنتج غير نشط' : 'Product is Inactive');
    document.getElementById('page_status_hint').textContent = isActive
        ? (isAr ? '✅ يظهر في نقطة البيع ويمكن بيعه' : '✅ Visible in POS and available for sale')
        : (isAr ? '🚫 مخفي في نقطة البيع ولا يمكن بيعه' : '🚫 Hidden from POS and cannot be sold');
    document.getElementById('page_status_hint').style.color = isActive ? '#10b981' : '#f43f5e';
    document.getElementById('page_status_icon').style.color = isActive ? '#10b981' : '#f43f5e';
});
</script>
@endpush

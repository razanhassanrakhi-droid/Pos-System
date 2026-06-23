@extends('layouts.app')

@section('title', __('pos.add') . ' ' . __('pos.manage', ['page' => __('pos.categories')]))

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .premium-category-page {
        font-family: 'Inter', sans-serif;
        background-color: #F8FAFC;
        min-height: 100vh;
        padding: 2rem 1rem;
        color: #0F172A;
    }

    .premium-card {
        background: #FFFFFF;
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.5);
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    /* Glassmorphism Header */
    .premium-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
        position: relative;
    }

    .icon-container {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: linear-gradient(135deg, #6D5DFC 0%, #8B7FFF 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        box-shadow: 0 10px 20px -5px rgba(109, 93, 252, 0.3);
    }

    .header-text h2 {
        font-weight: 700;
        font-size: 1.75rem;
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.02em;
    }

    .header-text p {
        color: #64748B;
        font-size: 1rem;
        margin: 0;
    }

    .close-button {
        position: absolute;
        top: 0;
        right: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #F1F5F9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .close-button:hover {
        background: #E2E8F0;
        color: #0F172A;
    }

    /* Language Sections */
    .lang-section {
        background: #FFFFFF;
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        overflow: hidden;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .lang-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -10px rgba(15, 23, 42, 0.05);
    }

    .lang-header {
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    }

    .lang-header.ar {
        background: linear-gradient(to right, rgba(139, 127, 255, 0.05), transparent);
        color: #6D5DFC;
    }

    .lang-header.en {
        background: linear-gradient(to right, rgba(56, 189, 248, 0.05), transparent);
        color: #0284C7;
    }

    .lang-body {
        padding: 1.5rem;
    }

    /* Modern Inputs */
    .premium-field {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .premium-field label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        color: #94A3B8;
        font-size: 1.1rem;
        transition: color 0.2s ease;
    }

    .input-wrapper.ltr .input-icon {
        left: 1rem;
    }

    .input-wrapper.rtl .input-icon {
        right: 1rem;
    }

    .premium-input {
        width: 100%;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        background: #F8FAFC;
        color: #0F172A;
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .premium-input:is(input) {
        height: 56px;
    }
    
    .premium-input:is(textarea) {
        padding-top: 1rem;
        padding-bottom: 1rem;
        resize: vertical;
    }

    .input-wrapper.ltr .premium-input {
        padding: 0 1rem 0 3rem;
    }

    .input-wrapper.rtl .premium-input {
        padding: 0 3rem 0 1rem;
    }

    .premium-input:hover {
        background: #FFFFFF;
        border-color: #CBD5E1;
    }

    .premium-input:focus {
        background: #FFFFFF;
        border-color: #6D5DFC;
        box-shadow: 0 0 0 4px rgba(109, 93, 252, 0.1);
        outline: none;
    }

    .premium-input:focus + .input-icon,
    .premium-input:not(:placeholder-shown) + .input-icon {
        color: #6D5DFC;
    }

    /* Info Banner */
    .info-banner {
        background: #F0F9FF;
        border: 1px solid #BAE6FD;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
        color: #0369A1;
    }

    .info-icon {
        font-size: 1.5rem;
        color: #0EA5E9;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid #E2E8F0;
    }

    .btn-premium {
        height: 56px;
        padding: 0 2rem;
        border-radius: 16px;
        font-weight: 600;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary-gradient {
        background: linear-gradient(135deg, #6D5DFC 0%, #8B7FFF 100%);
        color: white;
        box-shadow: 0 8px 16px -4px rgba(109, 93, 252, 0.25);
    }

    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -4px rgba(109, 93, 252, 0.35);
        color: white;
    }

    .btn-secondary-soft {
        background: #F1F5F9;
        color: #475569;
        border: 1px solid #E2E8F0;
    }

    .btn-secondary-soft:hover {
        background: #E2E8F0;
        color: #0F172A;
    }

    .btn-ghost {
        background: transparent;
        color: #64748B;
    }

    .btn-ghost:hover {
        background: #F8FAFC;
        color: #0F172A;
    }

    @media (max-width: 768px) {
        .action-bar {
            flex-direction: column-reverse;
        }
        .btn-premium {
            width: 100%;
            justify-content: center;
        }
        .close-button {
            position: static;
        }
        .premium-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="premium-category-page">
    <div class="container-fluid max-w-1200 mx-auto" style="max-width: 1200px;">
        <div class="premium-card">
            
            <div class="premium-header">
                <div class="icon-container">
                    <i class="bi bi-grid-fill"></i>
                </div>
                <div class="header-text">
                    <h2>Add / Manage Category</h2>
                    <p>Create and manage product categories in multiple languages</p>
                </div>
                <a href="{{ route('categories.index') }}" class="close-button" title="Close">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="is_active" value="1">
                
                <div class="row g-4">
                    <!-- Arabic Section -->
                    <div class="col-lg-6">
                        <div class="lang-section">
                            <div class="lang-header ar" dir="rtl">
                                <i class="bi bi-globe"></i> العربية
                            </div>
                            <div class="lang-body" dir="rtl">
                                <div class="premium-field">
                                    <label for="name_ar">Category Name (Arabic) <span class="text-danger">*</span></label>
                                    <div class="input-wrapper rtl">
                                        <input type="text" class="premium-input" id="name_ar" name="name_ar" required placeholder="مثال: إلكترونيات">
                                        <i class="bi bi-tag input-icon"></i>
                                    </div>
                                </div>
                                <div class="premium-field">
                                    <label for="description_ar">Description (Arabic)</label>
                                    <div class="input-wrapper rtl">
                                        <textarea class="premium-input" id="description_ar" name="description_ar" rows="3" placeholder="وصف تفصيلي للقسم..."></textarea>
                                        <i class="bi bi-card-text input-icon" style="top: 1.2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- English Section -->
                    <div class="col-lg-6">
                        <div class="lang-section">
                            <div class="lang-header en" dir="ltr">
                                <i class="bi bi-globe2"></i> English
                            </div>
                            <div class="lang-body" dir="ltr">
                                <div class="premium-field">
                                    <label for="name_en">Category Name (English) <span class="text-danger">*</span></label>
                                    <div class="input-wrapper ltr">
                                        <input type="text" class="premium-input" id="name_en" name="name_en" required placeholder="e.g. Electronics">
                                        <i class="bi bi-tag input-icon"></i>
                                    </div>
                                </div>
                                <div class="premium-field">
                                    <label for="description_en">Description (English)</label>
                                    <div class="input-wrapper ltr">
                                        <textarea class="premium-input" id="description_en" name="description_en" rows="3" placeholder="Detailed category description..."></textarea>
                                        <i class="bi bi-card-text input-icon" style="top: 1.2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-banner" dir="ltr">
                    <i class="bi bi-info-circle-fill info-icon"></i>
                    <div>
                        <strong>Pro Tip:</strong> Adding both Arabic and English names improves category organization and searchability across different interfaces.
                    </div>
                </div>

                <div class="action-bar" dir="ltr">
                    <a href="{{ route('categories.index') }}" class="btn-premium btn-ghost">
                        Cancel
                    </a>
                    <button type="reset" class="btn-premium btn-secondary-soft">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear Form
                    </button>
                    <button type="submit" class="btn-premium btn-primary-gradient">
                        <i class="bi bi-check2-circle"></i> Save Category
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

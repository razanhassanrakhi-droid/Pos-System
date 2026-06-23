@extends('layouts.app')

@section('title', __('pos.add') . ' ' . __('pos.manage', ['page' => __('pos.users')]))

@section('content')
<div class="card shadow-sm border-0 max-w-800 mx-auto" style="max-width: 800px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">{{ __('pos.add') }} {{ __('pos.manage', ['page' => __('pos.users')]) }}</h5>
    </div>
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="full_name_ar" class="form-label fw-semibold">{{ __('pos.full_name') }} (AR) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name_ar" name="full_name_ar" value="{{ old('full_name_ar') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="full_name_en" class="form-label fw-semibold">{{ __('pos.full_name') }} (EN) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name_en" name="full_name_en" value="{{ old('full_name_en') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="username" class="form-label fw-semibold">{{ __('pos.username') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">{{ __('pos.email') }}</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">{{ __('pos.phone') }}</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">{{ __('pos.password') }} <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label fw-semibold">{{ __('pos.role') }} <span class="text-danger">*</span></label>
                    <select class="form-select" id="role" name="role" required onchange="toggleBranches(this.value)">
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>{{ __('pos.employee') }}</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('pos.admin') }}</option>
                    </select>
                </div>
            </div>

            <div class="mb-4" id="branches-section">
                <label class="form-label fw-semibold d-block">{{ __('pos.assigned_branches') }} <span class="text-danger">*</span></label>
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select_all_branches">
                                    <label class="form-check-label fw-bold" for="select_all_branches">
                                        {{ __('pos.select_all') }}
                                    </label>
                                </div>
                                <hr class="my-2">
                            </div>
                            @foreach($branches as $branch)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input branch-checkbox" type="checkbox" name="branches[]" 
                                           value="{{ $branch->id }}" id="branch_{{ $branch->id }}"
                                           {{ (is_array(old('branches')) && in_array($branch->id, old('branches'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="branch_{{ $branch->id }}">
                                        {{ $branch->getTranslation('name') }} <small class="text-muted">({{ $branch->code }})</small>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-text text-muted mt-1" id="admin-branch-hint" style="display: none;">
                    <i class="bi bi-info-circle"></i> {{ __('pos.admin_has_all_branches') }}
                </div>
            </div>

            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                <label class="form-check-label fw-semibold" for="is_active">{{ __('pos.active') }}</label>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> {{ __('pos.save') }}</button>
                <button type="reset" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('pos.clear') }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ __('pos.exit') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleBranches(role) {
        const branchesSection = document.getElementById('branches-section');
        const adminHint = document.getElementById('admin-branch-hint');
        const checkboxes = document.querySelectorAll('.branch-checkbox');
        
        if (role === 'admin') {
            // Admin gets all branches implicitly, but we can visually select all or hide logic
            // Requirement says "Admin users can have all branches automatically"
            // We'll keep checkboxes visible but maybe disable them or auto-check all?
            // Let's just show a hint and auto-check all for UX
            checkboxes.forEach(cb => {
                cb.checked = true;
                cb.disabled = true;
            });
            // We need to ensure values are sent if disabled, or handle in backend.
            // Actually, HTML forms don't send disabled inputs.
            // Backend logic for Admin handles "accessibleBranches" returning all.
            // So for creation, we don't strictly need to send branches for admin, 
            // but validation says "required_if:role,employee".
            // So if role is admin, branches input is optional.
            
            adminHint.style.display = 'block';
        } else {
            checkboxes.forEach(cb => cb.disabled = false);
            adminHint.style.display = 'none';
        }
    }

    document.getElementById('select_all_branches').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.branch-checkbox');
        checkboxes.forEach(cb => {
            if (!cb.disabled) {
                cb.checked = this.checked;
            }
        });
    });

    // Run on load
    document.addEventListener('DOMContentLoaded', function() {
        toggleBranches(document.getElementById('role').value);
    });
</script>
@endpush

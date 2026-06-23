@extends('layouts.app')

@section('title', __('pos.edit') . ' ' . __('pos.manage', ['page' => __('pos.daily_expenses')]))

@section('content')
<div class="card shadow-sm border-0 max-w-800 mx-auto" style="max-width: 800px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">{{ __('pos.edit') }} {{ __('pos.manage', ['page' => __('pos.daily_expenses')]) }}</h5>
        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light"><i class="bi bi-arrow-left me-1"></i> {{ __('pos.back') }}</a>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('expenses.update', ['expense' => $expense->id]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="expense_type" class="form-label fw-semibold">{{ __('pos.expense_type') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select class="form-select" id="expense_type" name="type" required>
                            @foreach($types as $type)
                                <option value="{{ $type->name_en }}" {{ old('type', $expense->type) == $type->name_en ? 'selected' : '' }}>
                                    {{ $type->getTranslation('name') }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="amount" class="form-label fw-semibold">{{ __('pos.amount') }} ({{ $setting->currency }}) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $expense->amount) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="expense_date" class="form-label fw-semibold">{{ __('pos.expense_date') }} <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="expense_date" name="expense_date" value="{{ old('expense_date', $expense->expense_date) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="description_ar" class="form-label fw-semibold">{{ __('pos.description_ar') }}</label>
                    <textarea class="form-control" id="description_ar" name="description_ar" rows="3">{{ old('description_ar', $expense->description_ar) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="description_en" class="form-label fw-semibold">{{ __('pos.description_en') }}</label>
                    <textarea class="form-control" id="description_en" name="description_en" rows="3">{{ old('description_en', $expense->description_en) }}</textarea>
                </div>
            </div>

            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" {{ old('status', $expense->status) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="status">{{ __('pos.active') }}</label>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-check-circle me-1"></i> {{ __('pos.update') }}</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-light"><i class="bi bi-x-circle me-1"></i> {{ __('pos.cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Adding New Expense Type -->
<div class="modal fade" id="addTypeModal" tabindex="-1" aria-labelledby="addTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addTypeModalLabel">{{ __('pos.add') }} {{ __('pos.expense_type') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addTypeForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('pos.name') }} (AR) <span class="text-danger">*</span></label>
                        <input type="text" id="new_type_name_ar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('pos.name') }} (EN) <span class="text-danger">*</span></label>
                        <input type="text" id="new_type_name_en" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('pos.cancel') }}</button>
                <button type="button" id="saveTypeBtn" class="btn btn-primary px-4">{{ __('pos.save') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('saveTypeBtn').addEventListener('click', function() {
        const nameAr = document.getElementById('new_type_name_ar').value;
        const nameEn = document.getElementById('new_type_name_en').value;

        if (!nameAr && !nameEn) {
            alert('Please fill at least one required field (Arabic or English).');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        fetch("{{ route('expense-types.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name_ar: nameAr,
                name_en: nameEn
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add to select
                const select = document.getElementById('expense_type');
                const option = document.createElement('option');
                option.value = nameEn; 
                option.text = data.name;
                option.selected = true;
                select.add(option);

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('addTypeModal')).hide();
                document.getElementById('addTypeForm').reset();
            } else {
                alert(data.message || 'Error occurred.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save expense type.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = "{{ __('pos.save') }}";
        });
    });
</script>
@endpush

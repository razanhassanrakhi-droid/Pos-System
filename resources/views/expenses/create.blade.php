@extends('layouts.app')

@section('title', __('pos.add') . ' ' . __('pos.manage', ['page' => __('pos.daily_expenses')]))

@section('content')
<div class="card shadow-sm border-0 max-w-800 mx-auto" style="max-width: 800px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">{{ __('pos.add') }} {{ __('pos.manage', ['page' => __('pos.daily_expenses')]) }}</h5>
        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light"><i class="bi bi-arrow-left me-1"></i> {{ __('pos.back') }}</a>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('expenses.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="expense_type" class="form-label fw-semibold">{{ __('pos.expense_type') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select class="form-select" id="expense_type" name="type" required>
                            <option value="">{{ __('pos.select') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type->name_en }}" data-id="{{ $type->id }}">{{ $type->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addTypeModal" title="{{ __('pos.add') }}">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        <button class="btn btn-outline-danger" type="button" id="deleteTypeBtn" title="{{ __('pos.delete') ?? 'Delete' }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="amount" class="form-label fw-semibold">{{ __('pos.amount') }} ({{ $setting->currency }}) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                </div>
                <div class="col-md-6">
                    <label for="expense_date" class="form-label fw-semibold">{{ __('pos.expense_date') }} <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="expense_date" name="expense_date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="description_ar" class="form-label fw-semibold">{{ __('pos.description_ar') }}</label>
                    <textarea class="form-control" id="description_ar" name="description_ar" rows="3">{{ old('description_ar') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="description_en" class="form-label fw-semibold">{{ __('pos.description_en') }}</label>
                    <textarea class="form-control" id="description_en" name="description_en" rows="3">{{ old('description_en') }}</textarea>
                </div>
            </div>

            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                <label class="form-check-label fw-semibold" for="status">{{ __('pos.active') }}</label>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-check-circle me-1"></i> {{ __('pos.save') }}</button>
                <button type="reset" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('pos.clear') }}</button>
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
                        <input type="text" id="new_type_name_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('pos.name') }} (EN) <span class="text-danger">*</span></label>
                        <input type="text" id="new_type_name_en" class="form-control" required>
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

        if (!nameAr || !nameEn) {
            alert('Please fill all required fields.');
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
                const select = document.getElementById('expense_type');
                const option = document.createElement('option');
                option.value = nameEn; // using name_en as value to match existing logic if needed, or we could use ID
                option.text = data.name;
                option.dataset.id = data.id;
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

    document.getElementById('deleteTypeBtn').addEventListener('click', function() {
        const select = document.getElementById('expense_type');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            Swal.fire("{{ __('pos.error') ?? 'Error' }}", "Please select an expense type to delete.", "warning");
            return;
        }
        
        const typeId = selectedOption.dataset.id;
        if (!typeId) {
            Swal.fire("{{ __('pos.error') ?? 'Error' }}", "Cannot delete this expense type.", "warning");
            return;
        }

        Swal.fire({
            title: "{{ __('pos.are_you_sure') }}",
            text: "{{ __('pos.remove_expense_type_warning') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('pos.delete') ?? 'Delete' }}",
            cancelButtonText: "{{ __('pos.cancel') ?? 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = this;
                btn.disabled = true;
                
                fetch(`/expense-types/${typeId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        selectedOption.remove();
                        Swal.fire("{{ __('pos.success') ?? 'Deleted!' }}", data.message, "success");
                    } else {
                        Swal.fire("{{ __('pos.error') ?? 'Error' }}", data.message || "Failed to delete.", "error");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire("{{ __('pos.error') ?? 'Error' }}", "Failed to delete expense type.", "error");
                })
                .finally(() => {
                    btn.disabled = false;
                });
            }
        });
    });
</script>
@endpush

@extends('layouts.app')

@section('title', __('pos.manage', ['page' => __('pos.branches')]))

@push('styles')
<style>
    /* Premium SaaS Style Base matching Users page */
    .cat-toolbar {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: #f8fafc;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        border-left: 1px solid rgba(226, 232, 240, 0.8);
        border-right: 1px solid rgba(226, 232, 240, 0.8);
        border-top: 1px solid rgba(226, 232, 240, 0.8);
    }
    .cat-toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
        flex-wrap: wrap;
    }
    .cat-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    .cat-search-wrap { position: relative; min-width: 220px; max-width: 340px; flex: 1; }
    .cat-search-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; font-size: 0.88rem; z-index: 2; }
    html[dir="rtl"] .cat-search-icon { left: auto; right: 14px; }
    .cat-search-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 14px 10px 38px; font-size: 0.85rem; background: #ffffff; color: #0f172a; outline: none; transition: all 0.2s; }
    html[dir="rtl"] .cat-search-input { padding: 10px 38px 10px 14px; }
    .cat-search-input:focus { border-color: #00C8FF; box-shadow: 0 0 0 3px rgba(0,200,255,0.1); }
    
    .saas-table-card {
        background: white;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-top: none;
        overflow: hidden;
    }
    .saas-table { width: 100%; border-collapse: collapse; }
    .saas-table th { background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important; color: #ffffff !important; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; border-bottom: none !important; white-space: nowrap; text-align: center !important; vertical-align: middle; }
    .saas-table td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #F8FAFC; font-size: 0.9rem; color: #0F172A; font-weight: 500; white-space: nowrap; text-align: center !important; }
    
    .cat-add-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #fff !important;
        border: none;
        border-radius: 14px;
        padding: 12px 24px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.2);
        white-space: nowrap;
        text-decoration: none;
    }
    .cat-add-btn:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        transform: translateY(-2px);
        color: #fff;
    }
    
    .status-active { background-color: #d1e7dd; color: #0f5132; }
    .status-inactive { background-color: #f8d7da; color: #842029; }

    /* Dark Mode Overrides */
    html[data-app-theme="dark"] .cat-toolbar {
        background: #0f172a;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .saas-table-card {
        background: #1e293b;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .saas-table td {
        color: #ffffff !important;
        border-bottom-color: #334155;
    }
    html[data-app-theme="dark"] .saas-table tr:hover td {
        background: #0f172a;
    }
    html[data-app-theme="dark"] .cat-search-input {
        background: #1e293b;
        color: #ffffff;
        border-color: #334155;
    }
    html[data-app-theme="dark"] .cat-toolbar-left h5 {
        color: #ffffff !important;
    }
    
    @media (max-width: 767.98px) {
        .cat-toolbar {
            flex-direction: column;
            align-items: stretch;
            padding: 12px;
        }
        .cat-toolbar-left, .cat-toolbar-right {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            flex-wrap: nowrap;
        }
        .cat-search-wrap {
            max-width: 100%;
            width: 100%;
        }
        .cat-add-btn {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
        .saas-table-card {
            border-radius: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="cat-toolbar">
    <div class="cat-toolbar-left">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2" style="margin: 0; color: #0f172a;">
            <i class="bi bi-building text-primary" style="font-size: 1.25rem;"></i> {{ __('pos.branches') }}
        </h5>
        <div class="cat-search-wrap ms-md-4">
            <i class="bi bi-search cat-search-icon"></i>
            <input type="text" id="customSearchInput" class="cat-search-input" placeholder="{{ __('pos.search') ?? 'بحث' }}...">
        </div>
    </div>
    <div class="cat-toolbar-right">
        @can('create-branches')
        <a href="{{ route('branches.create') }}" class="cat-add-btn">
            <i class="bi bi-plus-lg"></i> {{ __('pos.add') }}
        </a>
        @endcan
    </div>
</div>

<div class="saas-table-card">
    <div class="table-responsive">
        <table id="branchesTable" class="saas-table w-100">
            <thead>
                <tr>
                    <th>{{ __('pos.branch_code') }}</th>
                    <th>{{ __('pos.branch_name') }}</th>
                    <th>{{ __('pos.city') }}</th>
                    <th>{{ __('pos.phone') }}</th>
                    <th>{{ __('pos.address') }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'مدير الفرع' : 'Branch Manager' }}</th>
                    <th>{{ __('pos.is_active') }}</th>
                    <th>{{ __('pos.created_at') }}</th>
                    <th class="text-center">{{ __('pos.settings') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                <tr>
                    <td><span class="fw-semibold text-dark">{{ $branch->code }}</span></td>
                    <td><span class="text-dark">{{ $branch->getTranslation('name') }}</span></td>
                    <td><span class="text-dark">{{ $branch->getTranslation('city') ?: '-' }}</span></td>
                    <td class="text-muted">{{ $branch->phone ?: '-' }}</td>
                    <td><span class="text-dark">{{ $branch->getTranslation('address') ?: '-' }}</span></td>
                    <td><span class="text-dark">{{ $branch->getTranslation('manager') ?: '-' }}</span></td>
                    <td>
                        @if($branch->is_active)
                            <span class="badge status-active px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i> {{ __('pos.active') }}</span>
                        @else
                            <span class="badge status-inactive px-2 py-1 rounded-pill"><i class="bi bi-dash-circle me-1"></i> {{ __('pos.inactive') }}</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $branch->created_at->format('Y-m-d') }}</td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            @can('edit-branches')
                            <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-sm btn-light border" style="border-radius: 8px;" title="{{ __('pos.edit') }}">
                                <i class="bi bi-pencil text-primary"></i>
                            </a>
                            @endcan
                            @can('delete-branches')
                            <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border" style="border-radius: 8px;" title="{{ __('pos.delete') }}">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#branchesTable').DataTable({
            paging: true,
            info: true,
            searching: true,
            lengthChange: false,
            pageLength: 10,
            dom: 'lrtip', // Hide default search input
            language: {
                @if(app()->getLocale() == 'ar')
                    search: "البحث:",
                    lengthMenu: "عرض _MENU_ فروع",
                    info: "صفحة _PAGE_ من _PAGES_",
                    infoEmpty: "صفحة 0 من 0",
                    infoFiltered: "(تصفية من مجموع _MAX_ فرع)",
                    zeroRecords: "لم يتم العثور على أية فروع",
                    emptyTable: "لا توجد فروع متاحة في الجدول",
                    paginate: {
                        first: "الأول",
                        previous: "السابق",
                        next: "التالي",
                        last: "الأخير"
                    }
                @else
                    search: "Search:",
                    lengthMenu: "Show _MENU_ branches",
                    info: "Page _PAGE_ of _PAGES_",
                    infoEmpty: "Page 0 of 0",
                    infoFiltered: "(filtered from _MAX_ total branches)",
                    zeroRecords: "No matching branches found",
                    emptyTable: "No branches available in table",
                    paginate: {
                        first: "First",
                        previous: "Previous",
                        next: "Next",
                        last: "Last"
                    }
                @endif
            }
        });

        // Link custom search input
        $('#customSearchInput').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush

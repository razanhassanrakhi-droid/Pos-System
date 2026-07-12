@extends('layouts.app')

@section('title', __('pos.user_permissions'))

@push('styles')
<style>
    /* Premium layout design */
    .pm-card-premium {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 24px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .pm-modal-header-premium {
        background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important;
        padding: 24px 32px !important;
        position: relative;
        overflow: hidden;
        border-bottom: none !important;
    }
    .pm-modal-header-premium::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
        background-size: 20px 20px;
        pointer-events: none;
    }
    .pm-modal-header-glow {
        position: absolute;
        border-radius: 50%;
        filter: blur(50px);
        pointer-events: none;
    }
    .pm-modal-header-glow-1 { width: 180px; height: 180px; background: rgba(99, 102, 241, 0.25); top: -60px; right: -40px; }
    .pm-modal-header-glow-2 { width: 140px; height: 140px; background: rgba(59, 130, 246, 0.18); bottom: -50px; left: -30px; }
    
    .pm-modal-icon-premium {
        width: 46px; height: 46px; border-radius: 14px;
        background: rgba(255,255,255,.1); border: 1.5px solid rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; color: #60a5fa !important; flex-shrink: 0; backdrop-filter: blur(8px);
    }
    .pm-modal-title-premium { font-size: 1.15rem; font-weight: 800; color: #fff; margin: 0; }
    .pm-modal-sub-premium { font-size: .76rem; color: #93c5fd !important; margin: 2px 0 0; font-weight: 500; }

    /* SaaS Premium table layout */
    .saas-table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }
    .saas-table { width: 100%; border-collapse: collapse; }
    .saas-table th { background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important; color: #ffffff !important; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.5rem; border-bottom: none !important; white-space: nowrap; text-align: center; vertical-align: middle; }
    .saas-table td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #F8FAFC; font-size: 0.9rem; color: #0F172A; font-weight: 500; white-space: nowrap; text-align: center; }
    .saas-table tr:hover td { background: #F8FAFC; }
    .saas-table tr:last-child td { border-bottom: none; }

    /* Custom beautiful badge pill overrides */
    .badge-premium-admin {
        background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
        color: #fff;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0, 114, 255, 0.2);
    }
    .badge-premium-employee {
        background: linear-gradient(135deg, #78ffd6 0%, #a8ff78 100%);
        color: #1e3a1e;
        font-weight: 700;
    }
    .badge-all-perms {
        background: linear-gradient(135deg, #ec008c 0%, #fc6767 100%);
        color: #fff;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(236, 0, 140, 0.2);
    }
    .badge-single-perm {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        font-weight: 600;
        font-size: 0.76rem;
    }

    /* Modern action button for edit permissions */
    .btn-edit-perm {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1.5px solid #6d5bff;
        color: #6d5bff !important;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 8px 16px;
        border-radius: 12px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-edit-perm:hover {
        background: #6d5bff;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(109, 91, 255, 0.25);
    }

    /* Custom search box formatting */
    .cat-search-wrap { position: relative; min-width: 220px; max-width: 340px; }
    .cat-search-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; font-size: 0.88rem; z-index: 2; }
    .cat-search-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 14px 10px 38px; font-size: 0.85rem; background: #ffffff; color: #0f172a; outline: none; transition: all 0.2s; }
    .cat-search-input:focus { border-color: #00C8FF; box-shadow: 0 0 0 3px rgba(0,200,255,0.1); }

    /* RTL adaptations */
    html[dir="rtl"] .cat-search-icon { left: auto; right: 14px; }
    html[dir="rtl"] .cat-search-input { padding: 10px 38px 10px 14px; }

    /* Dark Mode Overrides */
    html[data-app-theme="dark"] .pm-card-premium { background: #0f172a; border-color: #334155; }
    html[data-app-theme="dark"] .saas-table-card { background: #1e293b; border-color: #334155; }
    html[data-app-theme="dark"] .saas-table td { color: #f8fafc; border-bottom-color: #334155; }
    html[data-app-theme="dark"] .saas-table tr:hover td { background: #0f172a; }
    html[data-app-theme="dark"] .badge-single-perm { background: #0f172a; border-color: #334155; color: #94a3b8; }
    html[data-app-theme="dark"] .btn-edit-perm { background: transparent; border-color: #6d5bff; color: #c7d2fe !important; }
    html[data-app-theme="dark"] .btn-edit-perm:hover { background: #6d5bff; color: #ffffff !important; }
    html[data-app-theme="dark"] .cat-search-input { background-color: #1e293b; color: #fff; border-color: #334155; }
</style>
@endpush

@section('content')
<div class="container-fluid mb-5">

    <div class="pm-card-premium">
        {{-- Premium Header Layout --}}
        <div class="pm-modal-header-premium p-4">
            <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
            <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
            <div class="d-flex align-items-center justify-content-between position-relative w-100 flex-wrap gap-3" style="z-index: 2;">
                <div class="d-flex align-items-center gap-3">
                    <div class="pm-modal-icon-premium">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div>
                        <h4 class="pm-modal-title-premium">{{ __('pos.user_permissions') }}</h4>
                        <p class="pm-modal-sub-premium mb-0">{{ app()->getLocale() == 'ar' ? 'إدارة الأدوار وملفات تعريف الصلاحيات لجميع الموظفين' : 'Manage roles and permissions profiles for all employees' }}</p>
                    </div>
                </div>
                <div>
                    <div class="cat-search-wrap">
                        <i class="bi bi-search cat-search-icon"></i>
                        <input type="text" id="customSearchInput" class="cat-search-input" placeholder="{{ __('pos.search') ?? 'Search' }}...">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="saas-table-card">
                <div class="table-responsive">
                    <table id="permissionsTable" class="saas-table align-middle w-100">
                        <thead>
                            <tr>
                                <th>{{ __('pos.username') }}</th>
                                <th>{{ __('pos.full_name') }}</th>
                                <th>{{ __('pos.role') }}</th>
                                <th>{{ __('pos.permissions') }}</th>
                                <th class="text-center">{{ __('pos.settings') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="fw-bold">{{ $user->username }}</td>
                                <td>{{ $user->full_name }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $user->role == 'admin' ? 'badge-premium-admin' : 'badge-premium-employee' }} px-3 py-1.5 fs-7">
                                        {{ $user->role == 'admin' ? __('pos.admin') : __('pos.employee') }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $perms = $user->getPermissionNames();
                                    @endphp
                                    @if($user->role == 'admin')
                                        <span class="badge rounded-pill badge-all-perms px-3 py-1.5 fs-7">
                                            <i class="bi bi-check-all me-1"></i> {{ __('pos.all_permissions') }}
                                        </span>
                                    @else
                                        <div class="d-flex flex-wrap justify-content-center gap-1">
                                            @forelse($perms->take(5) as $perm)
                                                <span class="badge badge-single-perm rounded-pill px-2.5 py-1.5">{{ __('pos.' . $perm) }}</span>
                                            @empty
                                                <span class="text-muted small">-</span>
                                            @endforelse
                                            @if($perms->count() > 5)
                                                <span class="text-muted small align-self-center font-monospace">+{{ $perms->count() - 5 }} {{ __('pos.more') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('permissions.edit', $user->id) }}" class="btn-edit-perm">
                                        <i class="bi bi-shield-check"></i> {{ __('pos.edit_permissions') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div class="px-4 py-3 border-top border-light" style="background:#fff;">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#permissionsTable').DataTable({
            paging: false,
            info: false,
            dom: 't', // Only show table, hide default search
            language: {
                @if(app()->getLocale() == 'ar')
                    search: "البحث:",
                    lengthMenu: "عرض _MENU_ صلاحيات",
                    info: "صفحة _PAGE_ من _PAGES_",
                    infoEmpty: "صفحة 0 من 0",
                    infoFiltered: "(تصفية من مجموع _MAX_ صلاحية)",
                    zeroRecords: "لم يتم العثور على أية صلاحيات",
                    emptyTable: "لا توجد صلاحيات متاحة في الجدول",
                    paginate: {
                        first: "الأول",
                        previous: "السابق",
                        next: "التالي",
                        last: "الأخير"
                    }
                @else
                    search: "Search:",
                    lengthMenu: "Show _MENU_ permissions",
                    info: "Page _PAGE_ of _PAGES_",
                    infoEmpty: "Page 0 of 0",
                    infoFiltered: "(filtered from _MAX_ total permissions)",
                    zeroRecords: "No matching permissions found",
                    emptyTable: "No permissions available in table",
                    paginate: {
                        first: "First",
                        previous: "Previous",
                        next: "Next",
                        last: "Last"
                    }
                @endif
            }
        });

        // Link custom search input to DataTable
        $('#customSearchInput').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush

<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <script>
        (function() {
            const saved = localStorage.getItem('da_app_theme') || 'light';
            document.documentElement.setAttribute('data-app-theme', saved);
            document.documentElement.setAttribute('data-pm-theme', saved);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS System')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #1E88E5;
            --sidebar-bg: #1F2937;
            --secondary-color: #0D47A1;
            --success-color: #00C853;
            --warning-color: #FFAB00;
            --danger-color: #D50000;
            --bg-color: #F4F6F9;
            --card-bg: #ffffff;
            --text-color: #212529;
            --border-color: rgba(0,0,0,0.08);
            --navbar-bg: #ffffff;
            --text-muted: #6c757d;
        }

        html[data-app-theme="dark"] {
            --bg-color: #090f1c;
            --card-bg: #0f172a;
            --text-color: #e2e8f0;
            --border-color: rgba(255,255,255,0.08);
            --navbar-bg: #0f172a;
            --sidebar-bg: #070d19;
            --text-muted: #94a3b8;
        }

        /* Prevent transitions on page load */
        .no-transition,
        .no-transition * {
            -webkit-transition: none !important;
            -moz-transition: none !important;
            -ms-transition: none !important;
            -o-transition: none !important;
            transition: none !important;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background-color: var(--bg-color);
            color: var(--text-color);
            overflow-x: hidden;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Top Navbar */
        .top-navbar {
            background: var(--navbar-bg);
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s, border-color 0.3s;
            position: relative;
            z-index: 10 !important;
        }
        @media (max-width: 576px) {
            .top-navbar {
                padding: 10px 15px;
                margin-bottom: 15px;
            }
        }
        .top-navbar h4 {
            color: var(--text-color) !important;
        }

        /* Card override */
        .card {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            transition: background-color 0.3s, border-color 0.3s, color 0.3s;
        }
        .card-header {
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
        }

        /* Tables dark mode overrides */
        html[data-app-theme="dark"] .table {
            color: var(--text-color) !important;
        }
        html[data-app-theme="dark"] .table th,
        html[data-app-theme="dark"] .table td {
            border-color: var(--border-color) !important;
            background-color: transparent !important;
            color: var(--text-color) !important;
        }
        html[data-app-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(255,255,255,0.03) !important;
        }
        
        /* Modal dark mode overrides */
        html[data-app-theme="dark"] .modal-content {
            background-color: var(--card-bg) !important;
            color: var(--text-color) !important;
            border: 1px solid var(--border-color) !important;
        }
        html[data-app-theme="dark"] .modal-header,
        html[data-app-theme="dark"] .modal-footer {
            border-color: var(--border-color) !important;
            background-color: rgba(0,0,0,0.2) !important;
        }

        /* Forms dark mode overrides */
        html[data-app-theme="dark"] .form-control,
        html[data-app-theme="dark"] .form-select {
            background-color: #1e293b !important;
            border-color: var(--border-color) !important;
            color: var(--text-color) !important;
        }
        html[data-app-theme="dark"] .form-control:focus,
        html[data-app-theme="dark"] .form-select:focus {
            background-color: #1e293b !important;
            color: var(--text-color) !important;
        }

        /* Dropdowns */
        html[data-app-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-color) !important;
        }
        html[data-app-theme="dark"] .dropdown-item {
            color: var(--text-color) !important;
        }
        html[data-app-theme="dark"] .dropdown-item:hover {
            background-color: rgba(255,255,255,0.05) !important;
        }

        /* 2026 Modern Sidebar Design */
        #sidebar {
            width: 280px;
            position: fixed;
            top: 0;
            bottom: 0;
            margin-left: 0;
            background-color: #0b1120;
            border-radius: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            color: #94a3b8;
            transition: all 0.4s cubic-bezier(0.2, 0, 0, 1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-right: 1px solid rgba(255,255,255,0.03);
        }

        html[dir="rtl"] #sidebar {
            margin-left: 0;
            margin-right: 0;
            border-right: none;
            border-left: 1px solid rgba(255,255,255,0.03);
        }

        /* Sidebar content container (scrollable) */
        .sidebar-content-scroll {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0 16px 20px;
        }

        .sidebar-content-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-content-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-content-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .sidebar-content-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        #sidebar .sidebar-header {
            padding: 24px 20px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #sidebar .sidebar-header h4 {
            font-size: 1.25rem;
            letter-spacing: -0.5px;
        }

        .sidebar-collapse-icon {
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s;
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
            background: rgba(255,255,255,0.03);
        }
        .sidebar-collapse-icon:hover { color: #fff; background: rgba(255,255,255,0.08); }

        /* Branch Selector */
        .sidebar-branch-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: background 0.2s;
            user-select: none;
        }
        .sidebar-branch-card:hover {
            background: rgba(255,255,255,0.06);
        }
        .sidebar-branch-card .icon-box {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.08);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            margin-right: 12px;
        }
        html[dir="rtl"] .sidebar-branch-card .icon-box { margin-right: 0; margin-left: 12px; }
        
        .branch-info p { margin: 0; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; }
        .branch-info h6 { margin: 2px 0 0; color: #f8fafc; font-size: 0.9rem; font-weight: 600; }

        /* Category Header */
        .nav-category-header {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: 700;
            margin: 20px 0 8px 12px;
        }
        html[dir="rtl"] .nav-category-header { margin: 20px 12px 8px 0; }

        /* Nav Items */
        #sidebar ul.components { padding: 0; margin: 0; list-style: none; }
        #sidebar ul li { margin-bottom: 4px; }

        #sidebar ul li a {
            padding: 12px 16px;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
        }

        #sidebar ul li a i.main-icon {
            margin-right: 14px;
            font-size: 1.15rem;
            color: #64748b;
            transition: color 0.3s;
        }
        html[dir="rtl"] #sidebar ul li a i.main-icon { margin-right: 0; margin-left: 14px; }

        #sidebar ul li a:hover {
            background: rgba(255,255,255,0.05);
            color: #f8fafc;
        }
        #sidebar ul li a:hover i.main-icon { color: #f8fafc; }

        /* Active State */
        #sidebar ul li.active > a {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
        }
        #sidebar ul li.active > a i.main-icon { color: #ffffff; }
        
        /* Chevron for active */
        .nav-chevron {
            margin-left: auto;
            font-size: 0.8rem;
            opacity: 0;
            transform: translateX(-5px);
            transition: all 0.3s;
        }
        html[dir="rtl"] .nav-chevron { margin-left: 0; margin-right: auto; transform: translateX(5px); }
        
        #sidebar ul li.active > a .nav-chevron {
            opacity: 1;
            transform: translateX(0);
        }

        /* Submenus */
        #sidebar ul li ul { padding-left: 0; list-style: none; margin-top: 4px; }
        #sidebar ul li ul li a {
            padding: 10px 16px 10px 48px;
            font-size: 0.85rem;
            border-radius: 10px;
        }
        html[dir="rtl"] #sidebar ul li ul li a { padding: 10px 48px 10px 16px; }
        #sidebar ul li ul li.active > a {
            background: rgba(255,255,255,0.08);
            color: #fff;
            box-shadow: none;
        }

        /* Inventory Health Widget */
        .inventory-health-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 16px;
            margin-top: 24px;
            position: relative;
            overflow: hidden;
        }
        .inventory-health-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.5), transparent);
        }
        .ih-header {
            display: flex; align-items: center; gap: 8px; color: #fff; font-size: 0.9rem; font-weight: 600; margin-bottom: 12px;
        }
        .ih-header i { color: #a78bfa; font-size: 1.1rem; }
        .ih-item {
            display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 8px;
        }
        .ih-item-left { display: flex; align-items: center; gap: 8px; }
        .ih-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .ih-dot.orange { background: #fb923c; box-shadow: 0 0 8px rgba(251, 146, 60, 0.4); }
        .ih-dot.red { background: #f87171; box-shadow: 0 0 8px rgba(248, 113, 113, 0.4); }
        .ih-count { font-weight: 700; color: #fff; background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 6px;}
        .ih-link { display: block; text-align: center; color: #8b5cf6; font-size: 0.8rem; font-weight: 600; text-decoration: none; margin-top: 12px; transition: color 0.2s;}
        .ih-link:hover { color: #a78bfa; }

        /* Collapse Bottom Bar */
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s, background 0.2s;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .sidebar-footer:hover { color: #fff; background: rgba(255,255,255,0.02);}
        .sidebar-footer i {
            width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: center;
        }

        /* Collapsed State classes (for JS toggle) */
        body.sidebar-collapsed #sidebar { width: 88px; }
        body.sidebar-collapsed #sidebar .sidebar-header h4 span,
        body.sidebar-collapsed #sidebar .sidebar-branch-card .branch-info,
        body.sidebar-collapsed #sidebar .sidebar-branch-card .bi-chevron-down,
        body.sidebar-collapsed #sidebar .nav-category-header,
        body.sidebar-collapsed #sidebar ul li a span,
        body.sidebar-collapsed #sidebar ul li a .nav-chevron,
        body.sidebar-collapsed #sidebar .inventory-health-card,
        body.sidebar-collapsed #sidebar .sidebar-footer span {
            display: none !important;
        }
        body.sidebar-collapsed #sidebar .sidebar-header { justify-content: center; padding: 24px 0 16px; }
        body.sidebar-collapsed #sidebar .sidebar-header .sidebar-collapse-icon { display: none; }
        body.sidebar-collapsed #sidebar .sidebar-branch-card { justify-content: center; padding: 12px; border-radius: 12px; }
        body.sidebar-collapsed #sidebar .sidebar-branch-card .icon-box { margin: 0; }
        body.sidebar-collapsed #sidebar ul li a { justify-content: center; padding: 12px; }
        body.sidebar-collapsed #sidebar ul li a i.main-icon { margin: 0; font-size: 1.3rem; }
        body.sidebar-collapsed #sidebar .sidebar-footer { justify-content: center; padding: 16px 0; }

        /* Main Content Styling */
        #content {
            width: calc(100% - 280px);
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.2, 0, 0, 1);
        }
        html[dir="rtl"] #content { margin-left: 0; margin-right: 280px; }

        body.sidebar-collapsed #content {
            width: calc(100% - 88px);
            margin-left: 88px;
        }
        html[dir="rtl"] body.sidebar-collapsed #content { margin-left: 0; margin-right: 88px; }

        /* Navbar Styling */
        .top-navbar {
            background: var(--navbar-bg);
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
            position: relative;
            z-index: 10 !important;
        }

        /* Cards & General Elements */
        .card {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.04);
            margin-bottom: 25px;
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--text-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: -260px;
            }
            #sidebar.active {
                margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 0;
            }
            #content {
                width: 100%;
                margin: 0;
            }
            #sidebar .close-sidebar {
                display: block !important;
            }
        }

        #sidebarOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: all 0.3s;
        }

        #sidebarOverlay.active {
            display: block;
            opacity: 1;
        }

        #sidebar .close-sidebar {
            display: none;
            position: absolute;
            top: 15px;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 15px;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1001;
        }

        /* Dark Mode Utility Overrides */
        html[data-app-theme="dark"] .bg-white,
        html[data-app-theme="dark"] .bg-light,
        html[data-app-theme="dark"] .btn-light,
        html[data-app-theme="dark"] .card-header.bg-white,
        html[data-app-theme="dark"] .card-header.bg-light,
        html[data-app-theme="dark"] .payment-summary,
        html[data-app-theme="dark"] .quick-add-btn,
        html[data-app-theme="dark"] .quick-pick-item .btn-light,
        html[data-app-theme="dark"] .bg-light.p-3.rounded,
        html[data-app-theme="dark"] .bg-light.p-3.rounded-3,
        html[data-app-theme="dark"] .text-center.py-3.my-3.bg-white.rounded-3 {
            background-color: var(--card-bg) !important;
            color: var(--text-color) !important;
            border-color: var(--border-color) !important;
        }

        html[data-app-theme="dark"] .btn-light:hover {
            background-color: #1e293b !important;
            color: #fff !important;
        }

        html[data-app-theme="dark"] .text-dark,
        html[data-app-theme="dark"] .text-black,
        html[data-app-theme="dark"] .card-header h5.text-dark,
        html[data-app-theme="dark"] .card-header h5,
        html[data-app-theme="dark"] .cart-table .text-dark {
            color: var(--text-color) !important;
        }

        html[data-app-theme="dark"] .text-secondary {
            color: var(--text-muted) !important;
        }

        html[data-app-theme="dark"] .border-light,
        html[data-app-theme="dark"] .border,
        html[data-app-theme="dark"] .border-bottom,
        html[data-app-theme="dark"] .border-top,
        html[data-app-theme="dark"] .border-start,
        html[data-app-theme="dark"] .border-end {
            border-color: var(--border-color) !important;
        }

        html[data-app-theme="dark"] .input-group-text.bg-white,
        html[data-app-theme="dark"] .input-group-text {
            background-color: #1e293b !important;
            border-color: var(--border-color) !important;
            color: var(--text-color) !important;
        }

        html[data-app-theme="dark"] .table-light,
        html[data-app-theme="dark"] .table-light th,
        html[data-app-theme="dark"] .table-light td {
            background-color: #1e293b !important;
            color: var(--text-color) !important;
            border-color: var(--border-color) !important;
        }

        html[data-app-theme="dark"] .form-control[readonly],
        html[data-app-theme="dark"] .form-control.bg-white[readonly] {
            background-color: #1e293b !important;
            color: var(--text-color) !important;
            border-color: var(--border-color) !important;
        }

        /* ═══════════════════ GLOBAL PAGINATION STYLES ═══════════════════ */
        /* Applied to both Laravel Paginator and DataTables to match the specific rounded purple pill style */
        .pagination,
        .dataTables_wrapper .dataTables_paginate {
            display: flex !important;
            gap: 0.35rem;
            margin: 0;
            padding: 0;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            justify-content: flex-end;
            align-items: center;
        }

        .page-item .page-link, 
        .dataTables_wrapper .dataTables_paginate .paginate_button:not(.page-item) {
            min-width: 36px !important;
            height: 36px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 10px !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #64748B !important;
            background: #ffffff !important;
            border: 1px solid #E2E8F0 !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            padding: 0 0.5rem !important;
            margin: 0 !important;
            box-shadow: none !important;
            box-sizing: border-box;
        }
        
        /* Dark Mode adjustments for pills */
        html[data-app-theme="dark"] .page-item .page-link,
        html[data-app-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button:not(.page-item) {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }

        .page-item.active .page-link, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        html[data-app-theme="dark"] .page-item.active .page-link, 
        html[data-app-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #6D5DFC !important;
            color: #ffffff !important;
            border-color: #6D5DFC !important;
        }

        .page-item.disabled .page-link,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:not(.page-item) {
            opacity: 0.5;
            cursor: not-allowed !important;
            background: #f8fafc !important;
        }
        html[data-app-theme="dark"] .page-item.disabled .page-link,
        html[data-app-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:not(.page-item) {
            background: #0f172a !important;
        }

        .page-item:not(.disabled):not(.active) .page-link:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):not(.disabled):not(.page-item):hover {
            background: #F1F5F9 !important;
            color: #6D5DFC !important;
            border-color: #cbd5e1 !important;
        }
        html[data-app-theme="dark"] .page-item:not(.disabled):not(.active) .page-link:hover,
        html[data-app-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):not(.disabled):not(.page-item):hover {
            background: #334155 !important;
            color: #e2e8f0 !important;
            border-color: #475569 !important;
        }

        /* Pagination Info Text */
        .pagination-info p,
        .dataTables_wrapper .dataTables_info,
        .showing-text {
            font-size: 0.9rem !important;
            color: #64748B !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-block;
        }
        html[data-app-theme="dark"] .pagination-info p,
        html[data-app-theme="dark"] .dataTables_wrapper .dataTables_info,
        html[data-app-theme="dark"] .showing-text {
            color: #94a3b8 !important;
        }
        
        /* Remove DataTables default previous/next padding and margins */
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            font-weight: bold !important;
        }

        /* DataTables Footer Layout (Matching Categories .saas-footer) */
        .dataTables_wrapper .row:last-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 1.5rem;
            margin: 0;
            background: #ffffff;
            border-top: 1px solid #F1F5F9;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        html[data-app-theme="dark"] .dataTables_wrapper .row:last-child {
            background: var(--card-bg);
            border-top-color: var(--border-color);
        }
        .dataTables_wrapper .row:last-child [class*="col-"] {
            width: auto !important;
            padding: 0 !important;
            flex: 0 0 auto !important;
            max-width: none !important;
        }

        /* Global Table Header Styling (SaaS Style) */
        .table thead th,
        table.dataTable thead th,
        .dataTables_wrapper .dataTables_scrollHeadInner table thead th {
            background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: .9px;
            padding: 14px 20px !important;
            border: none !important;
            white-space: nowrap;
            vertical-align: middle;
        }

        .table thead th:first-child,
        table.dataTable thead th:first-child { 
            border-top-left-radius: 12px !important; 
            border-bottom-left-radius: 0 !important;
        }
        .table thead th:last-child,
        table.dataTable thead th:last-child { 
            border-top-right-radius: 12px !important; 
            border-bottom-right-radius: 0 !important;
        }

        [dir="rtl"] .table thead th:first-child,
        [dir="rtl"] table.dataTable thead th:first-child { 
            border-radius: 0 12px 0 0 !important; 
        }
        [dir="rtl"] .table thead th:last-child,
        [dir="rtl"] table.dataTable thead th:last-child { 
            border-radius: 12px 0 0 0 !important; 
        }
        
        /* Remove internal borders in table body for cleaner look */
        .table>tbody>tr>td, .table>tbody>tr>th, 
        .table>tfoot>tr>td, .table>tfoot>tr>th, 
        .table>thead>tr>td, .table>thead>tr>th {
            border-bottom-width: 1px;
            border-color: rgba(226, 232, 240, 0.8);
        }
        
        /* Ensure table header sits perfectly */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
    </style>
    @stack('styles')
</head>
<body class="no-transition">

    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width: 32px; height: 32px; object-fit: contain;" onerror="this.outerHTML='<i class=\'bi bi-shop fs-3 text-primary\'></i>'">
                    <h4 class="fw-bold m-0" style="font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};">
                        <span style="color: #46bfa3;">{{ __('pos.company_name_part1') }}</span><span style="color: #c21460;">{{ __('pos.company_name_part2') }}</span>
                    </h4>
                </div>
                <div class="sidebar-collapse-icon" onclick="toggleSidebar()">
                    <i class="bi bi-chevron-left"></i>
                </div>
            </div>

            <div class="sidebar-content-scroll">
                <!-- Branch Selector -->
                @if(isset($current_branch))
                <div class="sidebar-branch-card" onclick="document.querySelector('.top-navbar .dropdown-toggle').click()">
                    <div class="d-flex align-items-center">
                        <div class="icon-box">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div class="branch-info">
                            <p>{{ app()->getLocale() == 'ar' ? 'الفرع' : 'Branch' }}</p>
                            <h6>{{ Str::limit($current_branch->getTranslation('name'), 15) }}</h6>
                        </div>
                    </div>
                    <i class="bi bi-chevron-down text-muted"></i>
                </div>
                @endif
                
                <ul class="components">
                    <!-- OVERVIEW -->
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'نظرة عامة' : 'OVERVIEW' }}</div>
                    
                    @can('view-dashboard')
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="bi bi-house-door main-icon"></i>
                            <span>{{ __('pos.dashboard') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    <!-- INVENTORY -->
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'المخزون' : 'INVENTORY' }}</div>

                    @can('view-products')
                    <li class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <a href="{{ route('products.index') }}">
                            <i class="bi bi-box-seam main-icon"></i>
                            <span>{{ __('pos.products') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-purchases')
                    <li class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <a href="{{ route('purchases.index') }}">
                            <i class="bi bi-cart3 main-icon"></i>
                            <span>{{ __('pos.purchases') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    @can('view-adjustments')
                    <li class="{{ request()->routeIs('adjustments.*') ? 'active' : '' }}">
                        <a href="{{ route('adjustments.index') }}">
                            <i class="bi bi-exclamation-triangle main-icon"></i>
                            <span>{{ __('purchases.waste_management') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    <!-- SALES -->
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'المبيعات' : 'SALES' }}</div>

                    @can('view-sales')
                    <li class="{{ request()->routeIs('sales.*', 'sales_returns.*') ? 'active' : '' }}">
                        <a href="javascript:void(0)" data-bs-target="#salesSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('sales.*', 'sales_returns.*') ? 'true' : 'false' }}">
                            <i class="bi bi-shop-window main-icon"></i>
                            <span>{{ __('pos.sales') }}</span>
                            <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: auto;"></i>
                        </a>
                        <ul class="collapse {{ request()->routeIs('sales.*', 'sales_returns.*') ? 'show' : '' }}" id="salesSubmenu">
                            <li class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
                                <a href="{{ route('sales.index') }}"><i class="bi bi-list-ul me-2"></i> <span>{{ __('pos.sales_history') }}</span></a>
                            </li>
                            <li class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">
                                <a href="{{ route('sales.create') }}"><i class="bi bi-plus-circle me-2"></i> <span>{{ __('pos.add_sale') }}</span></a>
                            </li>
                            @can('view-sales-returns')
                            <li class="{{ request()->routeIs('sales_returns.*') ? 'active' : '' }}">
                                <a href="{{ route('sales_returns.index') }}"><i class="bi bi-arrow-return-left me-2"></i> <span>{{ __('pos.sales_returns') }}</span></a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcan

                    <!-- PEOPLE -->
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'الأشخاص' : 'PEOPLE' }}</div>

                    @can('view-suppliers')
                    <li class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}">
                            <i class="bi bi-truck main-icon"></i>
                            <span>{{ __('pos.suppliers') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    @can('view-customers')
                    <li class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}">
                            <i class="bi bi-people main-icon"></i>
                            <span>{{ __('pos.customers') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan
                    
                    @canany(['view-users', 'manage-permissions'])
                    <li>
                        <a href="javascript:void(0)" data-bs-target="#userManagementSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('users.*', 'permissions.*') ? 'true' : 'false' }}">
                            <i class="bi bi-person-badge main-icon"></i>
                            <span>{{ __('pos.management_user') }}</span>
                            <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: auto;"></i>
                        </a>
                        <ul class="collapse {{ request()->routeIs('users.*', 'permissions.*') ? 'show' : '' }}" id="userManagementSubmenu">
                            @can('view-users')
                            <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <a href="{{ route('users.index') }}"><i class="bi bi-person me-2"></i> <span>{{ __('pos.users') }}</span></a>
                            </li>
                            @endcan
                            @can('manage-permissions')
                            <li class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                <a href="{{ route('permissions.index') }}"><i class="bi bi-shield-lock me-2"></i> <span>{{ __('pos.permissions') }}</span></a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany

                    <!-- FINANCE -->
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'المالية' : 'FINANCE' }}</div>

                    @can('view-expenses')
                    <li class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <a href="{{ route('expenses.index') }}">
                            <i class="bi bi-wallet2 main-icon"></i>
                            <span>{{ __('pos.daily_expenses') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    @can('view-reports')
                    <li>
                        <a href="{{ route('reports.index') }}">
                            <i class="bi bi-bar-chart main-icon"></i>
                            <span>{{ __('pos.reports') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    <!-- SETTINGS -->
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'الإعدادات' : 'SETTINGS' }}</div>

                    @can('view-categories')
                    <li class="{{ request()->request->get('path') == 'categories' ? 'active' : '' }}">
                        <a href="{{ route('categories.index') }}">
                            <i class="bi bi-tags main-icon"></i>
                            <span>{{ __('pos.categories') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-warranties')
                    <li class="{{ request()->routeIs('warranties.*') ? 'active' : '' }}">
                        <a href="{{ route('warranties.index') }}">
                            <i class="bi bi-shield-check main-icon"></i>
                            <span>{{ __('pos.warranty_management') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    @can('manage-settings')
                    <li>
                        <a href="javascript:void(0)" data-bs-target="#settingsSubmenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-gear main-icon"></i>
                            <span>{{ __('pos.settings') }}</span>
                            <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: auto;"></i>
                        </a>
                        <ul class="collapse" id="settingsSubmenu">
                            <li><a href="{{ route('settings.index') }}"><i class="bi bi-building me-2"></i> <span>{{ __('pos.company_information') }}</span></a></li>
                            @can('view-license')
                            <li><a href="{{ route('settings.license') }}"><i class="bi bi-shield-check me-2"></i> <span>{{ __('pos.license_information') }}</span></a></li>
                            @endcan
                            @can('manage-license')
                            <li><a href="{{ route('settings.license.manager') }}"><i class="bi bi-shield-lock me-2"></i> <span>{{ __('pos.license_manager') }}</span></a></li>
                            @endcan
                            <li><a href="{{ route('settings.notifications') }}"><i class="bi bi-bell me-2"></i> <span>{{ __('pos.notification_settings') }}</span></a></li>
                            <li><a href="{{ route('settings.password') }}"><i class="bi bi-key me-2"></i> <span>{{ __('pos.change_password') }}</span></a></li>
                        </ul>
                    </li>
                    @else
                    <li class="{{ request()->routeIs('settings.password') ? 'active' : '' }}">
                        <a href="{{ route('settings.password') }}">
                            <i class="bi bi-key main-icon"></i>
                            <span>{{ __('pos.change_password') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan
                    
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();" style="color: #ef4444;">
                                <i class="bi bi-box-arrow-right main-icon" style="color: #ef4444;"></i>
                                <span>{{ __('pos.logout') }}</span>
                            </a>
                        </form>
                    </li>
                </ul>

                <!-- Inventory Health -->
                <div class="inventory-health-card">
                    <div class="ih-header">
                        <i class="bi bi-activity"></i>
                        <span>Inventory Health</span>
                    </div>
                    <div class="ih-item">
                        <div class="ih-item-left">
                            <span class="ih-dot orange"></span>
                            <span>Low Stock</span>
                        </div>
                        <span class="ih-count">3</span>
                    </div>
                    <div class="ih-item">
                        <div class="ih-item-left">
                            <span class="ih-dot red"></span>
                            <span>Expired</span>
                        </div>
                        <span class="ih-count">2</span>
                    </div>
                    <a href="{{ route('products.index') }}" class="ih-link">View All Alerts &rarr;</a>
                </div>
            </div>

            <div class="sidebar-footer">
                <a href="javascript:void(0)" onclick="toggleSidebar()" class="d-flex align-items-center text-decoration-none">
                    <i class="bi bi-layout-sidebar-inset main-icon"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'طي القائمة' : 'Collapse' }}</span>
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg top-navbar">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-light d-md-none">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <h4 class="mb-0 ms-3 fw-bold text-dark d-none d-md-block">@yield('title')</h4>
                    
                    <div class="d-flex ms-auto align-items-center gap-2 gap-md-3">
                        <!-- Branch Selector -->
@if(isset($current_branch) && isset($user_branches))
<div class="dropdown">
    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-building"></i> {{ $current_branch->getTranslation('name') }}
    </button>

    <ul class="dropdown-menu dropdown-menu-start dropdown-menu-md-end">
        <li><h6 class="dropdown-header">{{ __('pos.select_branch') ?? 'Select Branch' }}</h6></li>

        @if(auth()->user()->isAdmin())
            <li>
                <a class="dropdown-item {{ is_null(session('branch_id')) ? 'active' : '' }}" href="{{ route('branches.switch', 0) }}">
                    <i class="bi bi-globe me-2"></i>
                    {{ __('pos.all_branches') ?? 'All Branches' }}
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
        @endif

        @foreach($user_branches as $branch)
            <li>
                <a class="dropdown-item {{ $branch->id == $current_branch->id ? 'active' : '' }}" href="{{ route('branches.switch', $branch->id) }}">
                    {{ $branch->getTranslation('name') }}
                </a>
            </li>
        @endforeach

        @if(auth()->user()->isAdmin())
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('branches.index') }}">
                <i class="bi bi-gear me-2"></i>
                {{ __('pos.manage_branches') }}
            </a>
        </li>
        @endif
    </ul>
</div>
@endif                        <!-- Theme Switcher -->
                        <button class="btn btn-light btn-sm d-flex align-items-center gap-2" id="globalThemeToggle" onclick="toggleGlobalTheme()" type="button" style="border-radius: 8px; padding: 6px 12px; font-weight: 600;">
                            <i class="bi bi-moon-stars-fill" id="globalThemeIcon"></i>
                            <span class="d-none d-md-inline" id="globalThemeLabel">{{ app()->getLocale() == 'ar' ? 'الوضع الداكن' : 'Dark Mode' }}</span>
                        </button>

                        <!-- Language Switch -->
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-globe"></i> <span class="d-none d-sm-inline">{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start dropdown-menu-md-end">
                                <li><a class="dropdown-item" href="{{ url('/change-language/ar') }}">العربية</a></li>
                                <li><a class="dropdown-item" href="{{ url('/change-language/en') }}">English</a></li>
                            </ul>
                        </div>
                        
                        <!-- Notifications -->
                        <button id="bell-btn"
                            class="btn btn-light btn-sm position-relative"
                            data-label="{{ __('pos.notifications') ?? 'Notifications' }}"
                            data-no-alerts="{{ __('pos.no_notifications') ?? 'No notifications' }}">
                            <i class="bi bi-bell-fill text-primary"></i>
                            <span id="bell-badge" style="
                                display:none;
                                position:absolute;
                                top:-4px;
                                right:-4px;
                                background:#dc3545;
                                color:#fff;
                                border-radius:50%;
                                width:18px;
                                height:18px;
                                font-size:0.65rem;
                                font-weight:700;
                                align-items:center;
                                justify-content:center;
                                line-height:1;
                                border:2px solid #fff;
                            ">0</span>
                        </button>
                        
                        <!-- User Dropdown -->
                        @auth
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <div class="avatar-circle" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    @php
                                        $names = explode(' ', auth()->user()->full_name);
                                        $initials = '';
                                        foreach ($names as $name) {
                                            $initials .= mb_strtoupper(mb_substr($name, 0, 1));
                                        }
                                    @endphp
                                    {{ mb_substr($initials, 0, 2) }}
                                </div>
                                <span class="d-none d-md-inline">{{ auth()->user()->full_name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('settings.profile') }}"><i class="bi bi-person me-2"></i>{{ __('pos.profile') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('pos.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-4 rounded-pill">Login</a>
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="container-fluid px-4 pb-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay"></div>

    <!-- Notification System Container -->
    <div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Real-time Alerts JS -->
    @auth
        <script src="{{ asset('js/alerts.js') }}?v=20260617_v4"></script>
    @endauth

    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function () {
            function toggleSidebar() {
                $('#sidebar').toggleClass('active');
                $('#sidebarOverlay').toggleClass('active');
                
                if ($('#sidebar').hasClass('active')) {
                    $('body').css('overflow', 'hidden');
                } else {
                    $('body').css('overflow', 'auto');
                }
            }

            $('#sidebarCollapse, #sidebarOverlay, .close-sidebar').on('click', function () {
                toggleSidebar();
            });

            // Close sidebar when clicking outside on mobile devices
            $(document).on('click', function (event) {
                if ($(window).width() <= 768 && $('#sidebar').hasClass('active')) {
                    if (!$(event.target).closest('#sidebar').length && 
                        !$(event.target).closest('#sidebarCollapse').length && 
                        !$(event.target).closest('#sidebarOverlay').length) {
                        toggleSidebar();
                    }
                }
            });
            
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    paginate: {
                        next: '<i class="bi bi-chevron-right"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>'
                    }
                }
            });

            @if(app()->getLocale() == 'ar')
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    search: "بحث:",
                    lengthMenu: "عرض _MENU_ مدخلات",
                    info: "عرض _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                    infoEmpty: "لا توجد مدخلات",
                    infoFiltered: "(تمت التصفية من أصل _MAX_ إجمالي مدخلات)",
                    zeroRecords: "لم يتم العثور على أي سجلات مطابقة",
                    paginate: {
                        first: "الأول",
                        last: "الأخير",
                        next: '<i class="bi bi-chevron-left"></i>',
                        previous: '<i class="bi bi-chevron-right"></i>'
                    }
                }
            });
            @endif
        });
    </script>
    @stack('scripts')
    <script>
        const APP_THEME_KEY = 'da_app_theme';
        const globalThemeLabels = {
            dark: { ar: 'الوضع الفاتح', en: 'Light Mode' },
            light: { ar: 'الوضع الداكن', en: 'Dark Mode' }
        };
        const isAppAr = document.documentElement.dir === 'rtl';

        function applyGlobalTheme(theme) {
            document.documentElement.setAttribute('data-app-theme', theme);
            document.documentElement.setAttribute('data-pm-theme', theme);
            localStorage.setItem(APP_THEME_KEY, theme);
            
            const icon = document.getElementById('globalThemeIcon');
            const label = document.getElementById('globalThemeLabel');
            const btn = document.getElementById('globalThemeToggle');
            
            if (theme === 'dark') {
                if (icon) icon.className = 'bi bi-sun-fill text-warning';
                if (label) label.textContent = isAppAr ? globalThemeLabels.dark.ar : globalThemeLabels.dark.en;
                if (btn) {
                    btn.classList.remove('btn-light');
                    btn.classList.add('btn-dark');
                    btn.style.borderColor = '#374151';
                }
            } else {
                if (icon) icon.className = 'bi bi-moon-stars-fill text-primary';
                if (label) label.textContent = isAppAr ? globalThemeLabels.light.ar : globalThemeLabels.light.en;
                if (btn) {
                    btn.classList.remove('btn-dark');
                    btn.classList.add('btn-light');
                    btn.style.borderColor = '';
                }
            }

            // Sync page-specific themes if defined
            if (typeof applyPmTheme === 'function') {
                applyPmTheme(theme);
            }
        }

        function toggleGlobalTheme() {
            const current = document.documentElement.getAttribute('data-app-theme') || 'light';
            applyGlobalTheme(current === 'dark' ? 'light' : 'dark');
        }

        // Auto-load theme on load
        (function() {
            const saved = localStorage.getItem(APP_THEME_KEY) || 'light';
            applyGlobalTheme(saved);
        })();

        // Sidebar Toggle Logic
        const SIDEBAR_STATE_KEY = 'da_sidebar_collapsed';
        
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem(SIDEBAR_STATE_KEY, isCollapsed ? 'true' : 'false');
        }

        // Auto-load sidebar state
        (function() {
            const savedState = localStorage.getItem(SIDEBAR_STATE_KEY);
            if (savedState === 'true') {
                document.body.classList.add('sidebar-collapsed');
            }
        })();

        // Remove no-transition class after layout/theme is applied to prevent transition flash on load
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                document.body.classList.remove('no-transition');
            }, 50);
        });
    </script>
</body>
</html>

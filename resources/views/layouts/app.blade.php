<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}" class="notranslate" translate="no">
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
    /* Global Dark Mode Muted Text Corrections */
    html[data-app-theme="dark"] .text-muted {
        color: #a0aec0 !important;
    }
    html[data-app-theme="dark"] .text-muted h1,
    html[data-app-theme="dark"] .text-muted h2,
    html[data-app-theme="dark"] .text-muted h3,
    html[data-app-theme="dark"] .text-muted h4,
    html[data-app-theme="dark"] .text-muted h5,
    html[data-app-theme="dark"] .text-muted h6 {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .text-muted i {
        color: #a0aec0 !important;
    }

    /* Premium Branch Dropdown Selector Styles */
    .branch-dropdown-menu {
        border-radius: 16px !important;
        padding: 12px !important;
        min-width: 210px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background: #ffffff !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        margin-top: 8px !important;
    }
    html[data-app-theme="dark"] .branch-dropdown-menu {
        background: #0f172a !important;
        border-color: #334155 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }
    .branch-dropdown-menu .dropdown-header {
        font-size: 0.7rem !important;
        font-weight: 800 !important;
        color: #94a3b8 !important;
        padding: 4px 12px 8px !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
    }
    .branch-dropdown-menu .dropdown-item {
        border-radius: 10px !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        color: #475569 !important;
        padding: 8px 16px !important;
        transition: all 0.2s !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    html[data-app-theme="dark"] .branch-dropdown-menu .dropdown-item {
        color: #cbd5e1 !important;
    }
    .branch-dropdown-menu .dropdown-item:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        transform: translateX(2px);
    }
    html[dir="rtl"] .branch-dropdown-menu .dropdown-item:hover {
        transform: translateX(-2px);
    }
    html[data-app-theme="dark"] .branch-dropdown-menu .dropdown-item:hover {
        background: #1e293b !important;
        color: #ffffff !important;
    }
    .branch-dropdown-menu .dropdown-item.active {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
        color: #ffffff !important;
    }
    .branch-dropdown-menu .dropdown-divider {
        margin: 8px 0 !important;
        opacity: 0.08 !important;
    }

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

        /* Hide default browser password reveal eye icon (like in MS Edge) */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background-color: var(--bg-color);
            color: var(--text-color);
            overflow-x: hidden;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Disable Double-tap Zoom & iOS tap highlight color across all buttons/links globally */
        .btn, button, a, .pm-icon-btn {
            touch-action: manipulation !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        /* Center modal close button vertically in premium headers globally */
        .pm-modal-close-premium {
            top: 50% !important;
            transform: translateY(-50%) !important;
        }
        .pm-modal-close-premium:hover {
            transform: translateY(-50%) scale(1.08) !important;
        }

        /* Stack action buttons vertically on mobile */
        @media (max-width: 576px) {
            .action-buttons-flex {
                flex-direction: column !important;
            }
            .action-buttons-flex > .btn,
            .action-buttons-flex > a {
                width: 100% !important;
                display: inline-flex !important;
                justify-content: center !important;
                align-items: center !important;
            }
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
                padding: 8px 12px !important;
                margin-bottom: 15px;
            }
            .top-navbar .container-fluid {
                display: flex !important;
                flex-flow: row nowrap !important;
                align-items: center !important;
                justify-content: space-between !important;
                padding: 0 !important;
            }
            .top-navbar .d-flex.ms-auto {
                gap: 6px !important;
            }
            .top-navbar .btn,
            .top-navbar .dropdown button,
            .top-navbar #sidebarCollapse {
                padding: 6px 10px !important;
                font-size: 0.78rem !important;
                height: 34px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .top-navbar #bell-btn {
                width: 34px !important;
                height: 34px !important;
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

        /* ═══════════════════ GLOBAL ACTION BUTTONS STYLES (AS REQUESTED) ═══════════════════ */
        /* Soft Red trash button & Soft Blue edit button styling with 12px rounded borders */
        .btn-action-edit,
        .saas-table td a .bi-eye,
        .saas-table td button .bi-pencil-square,
        .btn-icon-action-only {
            /* Styling for Edit / View Action Buttons (Icons Only) */
            background-color: #eff6ff !important; /* Soft Blue */
            color: #3b82f6 !important; /* Deep Blue icon/text */
            border: 1px solid #dbeafe !important;
            border-radius: 12px !important;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-action-edit:hover,
        .saas-table td a .bi-eye:parent:hover {
            background-color: #dbeafe !important;
            transform: scale(1.05);
        }

        .btn-action-delete,
        .btn-icon-delete-only {
            /* Styling for Delete Action Buttons */
            background-color: #fef2f2 !important; /* Soft Red */
            color: #ef4444 !important; /* Deep Red icon/text */
            border: 1px solid #fee2e2 !important;
            border-radius: 12px !important;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-action-delete:hover {
            background-color: #fee2e2 !important;
            transform: scale(1.05);
        }

        /* Dark Mode overrides for Action Buttons */
        html[data-app-theme="dark"] .btn-action-edit {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #60a5fa !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
        }
        html[data-app-theme="dark"] .btn-action-delete {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
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

        /* Inventory Health Widget / User Profile Widget */
        .sidebar-profile-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 16px;
            margin-top: 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }
        .sidebar-profile-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.4), transparent);
        }
        .sidebar-profile-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(99, 102, 241, 0.3);
            transform: translateY(-2px);
        }
        .sp-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
            flex-shrink: 0;
            border: 1.5px solid rgba(255,255,255,0.15);
        }
        .sp-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .sp-name {
            color: #f8fafc;
            font-size: 0.88rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sp-role {
            color: #94a3b8;
            font-size: 0.72rem;
            font-weight: 600;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sp-arrow {
            margin-left: auto;
            color: #64748b;
            font-size: 0.9rem;
            transition: transform 0.2s;
        }
        html[dir="rtl"] .sp-arrow {
            margin-left: 0;
            margin-right: auto;
            transform: scaleX(-1);
        }
        .sidebar-profile-card:hover .sp-arrow {
            color: #fff;
            transform: translateX(3px);
        }
        html[dir="rtl"] .sidebar-profile-card:hover .sp-arrow {
            transform: scaleX(-1) translateX(3px);
        }

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
        body.sidebar-collapsed #sidebar .sidebar-profile-card { justify-content: center; padding: 12px; margin-top: 16px; }
        body.sidebar-collapsed #sidebar .sidebar-profile-card .sp-info,
        body.sidebar-collapsed #sidebar .sidebar-profile-card .sp-arrow { display: none !important; }

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
                margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: -280px !important;
            }
            #sidebar.active {
                margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 0 !important;
            }
            
            /* Force sidebar to be large (280px) on mobile */
            body.sidebar-collapsed #sidebar,
            #sidebar {
                width: 280px !important;
            }
            
            /* Show all text elements on mobile */
            body.sidebar-collapsed #sidebar .sidebar-header h4 span,
            body.sidebar-collapsed #sidebar .sidebar-branch-card .branch-info,
            body.sidebar-collapsed #sidebar .sidebar-branch-card .bi-chevron-down,
            body.sidebar-collapsed #sidebar .nav-category-header,
            body.sidebar-collapsed #sidebar ul li a span,
            body.sidebar-collapsed #sidebar ul li a .nav-chevron,
            body.sidebar-collapsed #sidebar .inventory-health-card,
            body.sidebar-collapsed #sidebar .sidebar-footer span,
            body.sidebar-collapsed #sidebar .sidebar-profile-card .sp-info,
            body.sidebar-collapsed #sidebar .sidebar-profile-card .sp-arrow {
                display: inline !important;
            }
            
            body.sidebar-collapsed #sidebar ul li a span {
                display: inline-block !important;
            }
            
            body.sidebar-collapsed #sidebar .sidebar-profile-card {
                justify-content: flex-start !important;
                padding: 16px !important;
            }
            
            body.sidebar-collapsed #sidebar ul li a {
                justify-content: flex-start !important;
                padding: 12px 24px !important;
            }
            
            body.sidebar-collapsed #sidebar ul li a i.main-icon {
                margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 12px !important;
            }
            
            body.sidebar-collapsed #sidebar .sidebar-branch-card {
                justify-content: space-between !important;
                padding: 16px !important;
            }
            
            body.sidebar-collapsed #sidebar .sidebar-branch-card .icon-box {
                margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 12px !important;
            }
            
            body.sidebar-collapsed #sidebar .sidebar-footer {
                justify-content: flex-start !important;
                padding: 16px 20px !important;
            }
            
            /* Hide collapsed sidebar off-screen on mobile */
            body.sidebar-collapsed #sidebar {
                margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: -280px !important;
            }
            
            body.sidebar-collapsed #sidebar.active {
                margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 0 !important;
            }
            
            body.sidebar-collapsed #content,
            html[dir="rtl"] body.sidebar-collapsed #content,
            #content {
                width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                margin: 0 !important;
            }
            #sidebar .close-sidebar {
                display: block !important;
            }
            .top-navbar .container-fluid {
                flex-wrap: nowrap !important;
            }
            .container-fluid.px-4 {
                padding-left: 10px !important;
                padding-right: 10px !important;
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

        /* Mobile Responsive DataTables Footer overrides */
        @media (max-width: 768px) {
            .dataTables_wrapper .row:last-child {
                flex-direction: column !important;
                gap: 12px !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 1.25rem 1rem !important;
            }
            .dataTables_wrapper .row:last-child [class*="col-"] {
                width: 100% !important;
                flex: 1 1 auto !important;
                display: flex !important;
                justify-content: center !important;
                text-align: center !important;
            }
            .dataTables_wrapper .dataTables_info {
                margin-bottom: 4px !important;
            }
            .dataTables_wrapper .dataTables_paginate {
                justify-content: center !important;
                width: 100% !important;
                flex-wrap: wrap !important;
                gap: 4px !important;
            }
            /* Make pagination buttons slightly smaller on mobile to fit nicely */
            .page-item .page-link, 
            .dataTables_wrapper .dataTables_paginate .paginate_button:not(.page-item) {
                min-width: 32px !important;
                height: 32px !important;
                font-size: 0.8rem !important;
                border-radius: 8px !important;
            }
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
    
        /* ===== intl-tel-input Dark Mode ===== */
        html[data-app-theme="dark"] .iti__country-list {
            background: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4) !important;
        }
        html[data-app-theme="dark"] .iti__country {
            background: var(--card-bg) !important;
            color: var(--text-color) !important;
        }
        html[data-app-theme="dark"] .iti__country:hover,
        html[data-app-theme="dark"] .iti__country.iti__highlight {
            background-color: rgba(255,255,255,0.06) !important;
        }
        html[data-app-theme="dark"] .iti__country-name {
            color: var(--text-color) !important;
        }
        html[data-app-theme="dark"] .iti__dial-code {
            color: var(--text-muted) !important;
        }
        html[data-app-theme="dark"] .iti__selected-dial-code {
            color: var(--text-color) !important;
        }
        html[data-app-theme="dark"] .iti__divider {
            border-bottom-color: var(--border-color) !important;
        }
        html[data-app-theme="dark"] .iti__search-input {
            background: var(--card-bg) !important;
            color: var(--text-color) !important;
            border-color: var(--border-color) !important;
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
                    @if(isset($setting) && $setting->company_logo)
                        <img src="{{ asset('storage/' . $setting->company_logo) }}" alt="Logo" style="width: 48px; height: 48px; object-fit: contain; border-radius: 10px; background: rgba(255,255,255,0.08); padding: 4px; border: 1px solid rgba(255,255,255,0.15);">
                    @endif
                    <h4 class="fw-bold m-0" style="font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }}; color: #f8fafc; font-size: 1.15rem; letter-spacing: -0.5px;">
                        <span>{{ isset($setting) ? $setting->getTranslation('company_name') : __('pos.company_name') }}</span>
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
                    @can('view-dashboard')
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'نظرة عامة' : 'OVERVIEW' }}</div>
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="bi bi-house-door main-icon"></i>
                            <span>{{ __('pos.dashboard') }}</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    <!-- CASHIER / POS -->
                    @can('create-sales')
                    <li class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">
                        <a href="{{ route('sales.create') }}" style="color: #ff7a00 !important;">
                            <i class="bi bi-cash-coin main-icon" style="color: #ff7a00 !important;"></i>
                            <span class="fw-bold">{{ app()->getLocale() == 'ar' ? 'واجهة الكاشير (POS)' : 'Cashier Interface (POS)' }}</span>
                            <i class="bi bi-chevron-right nav-chevron" style="color: #ff7a00 !important;"></i>
                        </a>
                    </li>
                    @endcan

                    <!-- INVENTORY -->
                    @if(auth()->user()->can('view-products') || auth()->user()->can('view-categories') || auth()->user()->can('view-warranties') || auth()->user()->can('view-purchases') || auth()->user()->can('view-adjustments'))
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

                    @can('view-categories')
                    <li class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <a href="{{ route('categories.index') }}">
                            <i class="bi bi-tags main-icon"></i>
                            <span>{{ __('pos.categories') }}</span>
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

                    @can('view-warranties')
                    <li class="{{ request()->routeIs('warranties.*') ? 'active' : '' }}">
                        <a href="{{ route('warranties.index') }}">
                            <i class="bi bi-shield-check main-icon"></i>
                            <span>{{ __('pos.warranty_management') }}</span>
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
                    @endif

                    <!-- SALES -->
                    @can('view-sales')
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'المبيعات' : 'SALES' }}</div>

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
                            
                            @can('view-sales-returns')
                            <li class="{{ request()->routeIs('sales_returns.*') ? 'active' : '' }}">
                                <a href="{{ route('sales_returns.index') }}"><i class="bi bi-arrow-return-left me-2"></i> <span>{{ __('pos.sales_returns') }}</span></a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcan

                    <!-- PEOPLE -->
                    @if(auth()->user()->can('view-suppliers') || auth()->user()->can('view-customers'))
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
                    @endif
                    <!-- FINANCE -->
                    @if(auth()->user()->can('view-expenses') || auth()->user()->can('view-reports'))
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
                    @endif

                    @if(auth()->user()->can('view-users') || auth()->user()->can('manage-permissions') || auth()->user()->can('manage-settings'))
                    <div class="nav-category-header">{{ app()->getLocale() == 'ar' ? 'الإعدادات' : 'SETTINGS' }}</div>
                    @endif

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

                    @can('manage-settings')
                    <li>
                        <a href="javascript:void(0)" data-bs-target="#settingsSubmenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-gear main-icon"></i>
                            <span>{{ __('pos.settings') }}</span>
                            <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: auto;"></i>
                        </a>
                        <ul class="collapse" id="settingsSubmenu">
                            <li><a href="{{ route('settings.index') }}"><i class="bi bi-building me-2"></i> <span>{{ __('pos.company_information') }}</span></a></li>
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

                <!-- User Profile Card -->
                @auth
                @php
                    $names = explode(' ', auth()->user()->full_name);
                    $initials = '';
                    foreach ($names as $name) {
                        $initials .= mb_strtoupper(mb_substr($name, 0, 1));
                    }
                    $displayInitials = mb_substr($initials, 0, 2);
                @endphp
                <a href="{{ route('settings.profile') }}" class="sidebar-profile-card">
                    <div class="sp-avatar">
                        {{ $displayInitials }}
                    </div>
                    <div class="sp-info">
                        <span class="sp-name">{{ auth()->user()->full_name }}</span>
                        <span class="sp-role">{{ auth()->user()->role == 'admin' ? __('pos.admin') : __('pos.employee') }}</span>
                    </div>
                    <i class="bi bi-chevron-right sp-arrow"></i>
                </a>
                @endauth
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
                            <button class="btn btn-sm d-flex align-items-center gap-2 dropdown-toggle px-3 py-2" type="button" data-bs-toggle="dropdown" style="border-radius: 12px; background: rgba(99, 102, 241, 0.08); border: 1.5px solid rgba(99, 102, 241, 0.2); color: #6366f1; font-weight: 700; font-size: 0.82rem; transition: all 0.2s;">
                                <i class="bi bi-building"></i> {{ $current_branch->getTranslation('name') }}
                            </button>
                        
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg branch-dropdown-menu">
                                <li><h6 class="dropdown-header">{{ __('pos.select_branch') ?? 'Select Branch' }}</h6></li>
                        
                                @if(auth()->user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item {{ is_null(session('branch_id')) ? 'active' : '' }}" href="{{ route('branches.switch', 0) }}">
                                            <i class="bi bi-globe"></i>
                                            {{ __('pos.all_branches') ?? 'All Branches' }}
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                        
                                @foreach($user_branches as $branch)
                                    <li>
                                        <a class="dropdown-item {{ $branch->id == $current_branch->id ? 'active' : '' }}" href="{{ route('branches.switch', $branch->id) }}">
                                            <i class="bi bi-shop"></i>
                                            {{ $branch->getTranslation('name') }}
                                        </a>
                                    </li>
                                @endforeach
                        
                                @if(auth()->user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('branches.index') }}" style="color: #6366f1 !important;">
                                        <i class="bi bi-gear"></i>
                                        {{ __('pos.manage_branches') }}
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                        @endif

                        <!-- Theme Switcher -->
                        <button class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2" id="globalThemeToggle" onclick="toggleGlobalTheme()" type="button" style="border-radius: 12px; background: rgba(99, 102, 241, 0.04); border: 1.5px solid rgba(226, 232, 240, 0.8); color: var(--text-color); font-weight: 700; font-size: 0.82rem; transition: all 0.2s;">
                            <i class="bi bi-moon-stars-fill" id="globalThemeIcon" style="color: #6366f1;"></i>
                            <span class="d-none d-md-inline" id="globalThemeLabel">{{ app()->getLocale() == 'ar' ? 'الوضع الداكن' : 'Dark Mode' }}</span>
                        </button>
                        
                        <!-- Language Switch -->
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2 px-3 py-2" type="button" data-bs-toggle="dropdown" style="border-radius: 12px; background: rgba(99, 102, 241, 0.04); border: 1.5px solid rgba(226, 232, 240, 0.8); color: var(--text-color); font-weight: 700; font-size: 0.82rem; transition: all 0.2s;">
                                <i class="bi bi-globe" style="color: #6366f1;"></i> <span class="d-none d-sm-inline">{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="border-radius: 16px; padding: 8px;">
                                <li><a class="dropdown-item py-2 px-3" href="{{ url('/change-language/ar') }}" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem;">العربية</a></li>
                                <li><a class="dropdown-item py-2 px-3" href="{{ url('/change-language/en') }}" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem;">English</a></li>
                            </ul>
                        </div>
                        
                        <!-- Notifications -->
                        @can('view-notifications')
                        <button id="bell-btn"
                            class="btn btn-sm position-relative d-flex align-items-center justify-content-center"
                            data-label="{{ __('pos.notifications') ?? 'Notifications' }}"
                            data-no-alerts="{{ __('pos.no_notifications') ?? 'No notifications' }}"
                            style="width: 38px; height: 38px; border-radius: 12px; background: rgba(99, 102, 241, 0.04); border: 1.5px solid rgba(226, 232, 240, 0.8); color: var(--text-color); transition: all 0.2s;">
                            <i class="bi bi-bell-fill" style="color: #6366f1;"></i>
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
                        @endcan
                        

                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="container-fluid px-4 pb-4">
                {{-- Global Premium Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2) !important; color: #10b981; backdrop-filter: blur(8px);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div class="fw-semibold">{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(0.4);"></button>
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2) !important; color: #3b82f6; backdrop-filter: blur(8px);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                            <div class="fw-semibold">{{ session('info') }}</div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(0.4);"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2) !important; color: #ef4444; backdrop-filter: blur(8px);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div class="fw-semibold">{{ session('error') }}</div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(0.4);"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2) !important; color: #ef4444; backdrop-filter: blur(8px);">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-octagon-fill fs-5 mt-0.5"></i>
                            <div>
                                <div class="fw-bold mb-1">{{ app()->getLocale() == 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}</div>
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li class="fw-semibold small">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(0.4);"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay"></div>

    <!-- Notification System Container -->
    @can('view-notifications')
    <div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
    @endcan

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Real-time Alerts JS -->
    @auth
        @can('view-notifications')
        <script src="{{ asset('js/alerts.js') }}?v=20260617_v4"></script>
        @endcan
    @endauth

    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function () {

            $('#sidebarCollapse, #sidebarOverlay, .close-sidebar').on('click', function () {
                toggleSidebar();
            });

            // Close sidebar when clicking outside on mobile devices
            $(document).on('click', function (event) {
                if (window.matchMedia("(max-width: 768px)").matches && $('#sidebar').hasClass('active')) {
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
            if (window.matchMedia("(max-width: 768px)").matches) {
                // Mobile behavior: toggle active drawer
                $('#sidebar').toggleClass('active');
                $('#sidebarOverlay').toggleClass('active');
                if ($('#sidebar').hasClass('active')) {
                    $('body').css('overflow', 'hidden');
                } else {
                    $('body').css('overflow', 'auto');
                }
            } else {
                // Desktop behavior: toggle collapse
                document.body.classList.toggle('sidebar-collapsed');
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                localStorage.setItem(SIDEBAR_STATE_KEY, isCollapsed ? 'true' : 'false');
            }
        }

        // Auto-load sidebar state
        (function() {
            const savedState = localStorage.getItem(SIDEBAR_STATE_KEY);
            if (savedState === 'true' && !window.matchMedia("(max-width: 768px)").matches) {
                document.body.classList.add('sidebar-collapsed');
            }
        })();

        // Sidebar click behaviors
        $(document).ready(function() {
            // Real-time Bilingual Fields Mirroring/Autofill
            $(document).on('input', 'input, textarea', function() {
                const currentInput = $(this);
                const currentName = currentInput.attr('name');
                if (!currentName) return;

                let targetName;
                if (currentName.endsWith('_ar')) {
                    targetName = currentName.replace(/_ar$/, '_en');
                } else if (currentName.endsWith('_en')) {
                    targetName = currentName.replace(/_en$/, '_ar');
                } else {
                    return;
                }

                const form = currentInput.closest('form');
                if (!form.length) return;
                
                const targetInput = form.find(`[name="${targetName}"]`);
                if (!targetInput.length) return;

                const currentVal = currentInput.val();
                
                // Mirror the value if the target hasn't been manually typed in, or if it is currently cleared
                if (!targetInput.data('manual-change') || targetInput.val().trim() === '') {
                    targetInput.val(currentVal);
                    targetInput.trigger('change');
                }
            });

            // Mark when user typed manually in a field
            $(document).on('keyup change', 'input, textarea', function(e) {
                const name = $(this).attr('name');
                if (!name || (!name.endsWith('_ar') && !name.endsWith('_en'))) return;
                
                // If this is a direct user event (e.g. keyup/change triggered by typing/pasting)
                if (e.originalEvent) {
                    if ($(this).val().trim() !== '') {
                        $(this).data('manual-change', true);
                    } else {
                        $(this).data('manual-change', false);
                    }
                }
            });

            // Automatically expand collapsed sidebar when clicking a submenu toggle link
            $(document).on('click', '#sidebar ul li a[data-bs-toggle="collapse"]', function() {
                if (!window.matchMedia("(max-width: 768px)").matches && document.body.classList.contains('sidebar-collapsed')) {
                    toggleSidebar();
                }
            });

            // Automatically close mobile sidebar drawer immediately when clicking any navigation link to prevent transition delay flash
            $(document).on('click', '#sidebar ul li a:not([data-bs-toggle="collapse"]):not([href="#"])', function() {
                if (window.matchMedia("(max-width: 768px)").matches && $('#sidebar').hasClass('active')) {
                    $('#sidebar').removeClass('active');
                    $('#sidebarOverlay').removeClass('active');
                    $('body').css('overflow', 'auto');
                }
            });
        });

        // Remove no-transition class after layout/theme is applied to prevent transition flash on load
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                document.body.classList.remove('no-transition');
            }, 50);

            // Sidebar Scroll State Retention Logic
            const sidebarScrollContainer = document.querySelector('.sidebar-content-scroll');
            if (sidebarScrollContainer) {
                // Restore scroll position
                const savedScrollTop = localStorage.getItem('sidebar_scroll_position');
                if (savedScrollTop) {
                    sidebarScrollContainer.scrollTop = parseInt(savedScrollTop, 10);
                }

                // Save scroll position on scroll
                let scrollTimeout;
                sidebarScrollContainer.addEventListener('scroll', function() {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(function() {
                        localStorage.setItem('sidebar_scroll_position', sidebarScrollContainer.scrollTop);
                    }, 100);
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

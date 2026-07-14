@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'الأقسام' : 'Categories')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .saas-page {
        font-family: 'Inter', sans-serif;
        background-color: #F8FAFC;
        min-height: 100vh;
        padding: 2rem;
        color: #0F172A;
    }

    @media (max-width: 576px) {
        .saas-page {
            padding: 12px 0px !important;
        }
        .top-stats-row {
            gap: 10px !important;
            margin-bottom: 15px !important;
            grid-template-columns: 1fr !important;
        }
        .kpi-card {
            padding: 12px !important;
            gap: 12px !important;
        }
        .cat-toolbar {
            padding: 12px 10px !important;
            gap: 10px !important;
        }
        .cat-search-wrap {
            width: 100% !important;
        }
        .cat-toolbar-left {
            width: 100% !important;
        }
    }

    /* Top Header Section */
    .saas-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .saas-header-left {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .saas-header-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: #F3F0FF;
        color: #6D5DFC;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .saas-header-text h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: #0F172A;
        letter-spacing: -0.02em;
    }

    .saas-header-text p {
        font-size: 0.95rem;
        color: #64748B;
        margin: 0;
    }

    .saas-header-actions {
        display: flex;
        gap: 1rem;
    }

    .btn-saas {
        height: 44px;
        padding: 0 1.25rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-saas-outline {
        background: white;
        border: 1px solid #E2E8F0;
        color: #475569;
    }

    .btn-saas-outline:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #0F172A;
    }

    .btn-saas-primary {
        background: #6D5DFC;
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(109, 93, 252, 0.2);
    }

    .btn-saas-primary:hover {
        background: #5B4EED;
        color: white;
        box-shadow: 0 6px 16px rgba(109, 93, 252, 0.3);
        transform: translateY(-1px);
    }

    /* Filters Section */
    .saas-filters {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .entries-select {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #64748B;
        font-size: 0.95rem;
    }

    .entries-select select {
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 0.4rem 2rem 0.4rem 0.75rem;
        background: white;
        color: #0F172A;
        font-weight: 500;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748B' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 10px;
    }

    .status-toggle {
        display: inline-flex;
        background: white;
        border-radius: 100px;
        padding: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        border: 1px solid #E2E8F0;
    }

    .status-btn {
        padding: 0.5rem 1.25rem;
        border-radius: 100px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748B;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .status-btn.active {
        background: #F3F0FF;
        color: #6D5DFC;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-dot.green { background: #22C55E; }
    .status-dot.gray { background: #94A3B8; }

    .status-count {
        font-size: 0.8rem;
        background: white;
        padding: 2px 6px;
        border-radius: 10px;
        color: #64748B;
    }

    .search-bar-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .saas-search {
        position: relative;
    }

    .saas-search input {
        height: 44px;
        width: 260px;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0 1rem 0 2.5rem;
        font-size: 0.95rem;
        background: white;
        transition: all 0.2s ease;
    }

    .saas-search input:focus {
        outline: none;
        border-color: #6D5DFC;
        box-shadow: 0 0 0 3px rgba(109, 93, 252, 0.1);
    }

    .saas-search i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
    }

    .filter-btn {
        width: 44px;
        height: 44px;
        background: white;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background: #F8FAFC;
        color: #0F172A;
    }

    /* Table Wrapper */
    .saas-table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }

    .saas-table {
        width: 100%;
        border-collapse: collapse;
    }

    .saas-table th {
        background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem;
        border-bottom: none !important;
        white-space: nowrap;
        text-align: start;
        vertical-align: middle;
    }

    .saas-table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #F8FAFC;
        font-size: 0.9rem;
        color: #0F172A;
        font-weight: 500;
        white-space: nowrap;
        text-align: start;
    }

    .saas-table tr:hover td {
        background: #F8FAFC;
    }

    .saas-table tr:last-child td {
        border-bottom: none;
    }

    /* Category Column */
    .cat-wrapper {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.75rem;
    }

    .cat-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        width: auto;
        padding: 0 6px;
        height: 28px;
        background: #F1F5F9;
        color: #6D5DFC;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .cat-icon-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: bold;
    }

    /* Avatars */
    .avatar-wrapper {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.5rem;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.85rem;
        color: white;
    }

    .avatar-circle.purple { background: #D8B4FE; color: #6B21A8; }
    .avatar-circle.green { background: #BBF7D0; color: #166534; }
    .avatar-circle.blue { background: #BFDBFE; color: #1E3A8A; }
    .avatar-circle.orange { background: #FED7AA; color: #9A3412; }

    /* Actions */
    .action-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-icon.edit { color: #8B7FFF; border-color: #E2E8F0; }
    .btn-icon.edit:hover { background: #F3F0FF; border-color: #8B7FFF; color: #6D5DFC; }

    .btn-icon.delete { color: #F87171; border-color: #E2E8F0; }
    .btn-icon.delete:hover { background: #FEF2F2; border-color: #F87171; color: #DC2626; }

    .btn-icon.more { color: #94A3B8; border-color: transparent; }
    .btn-icon.more:hover { background: #F1F5F9; color: #475569; }

        /* Footer Stats & Pagination */
    .saas-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 1.5rem;
        border-top: 1px solid #F1F5F9;
    }

    .pagination-wrapper {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .showing-text {
        font-size: 0.9rem;
        color: #64748B;
    }

    .pagination-pills {
        display: flex;
        gap: 0.25rem;
    }

    .page-pill {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748B;
        background: white;
        border: 1px solid #E2E8F0;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .page-pill:hover {
        background: #F1F5F9;
    }

    .page-pill.active {
        background: #6D5DFC;
        color: white;
        border-color: #6D5DFC;
    }

    /* Top Stats Cards (Matched to Damages KPI) */
    .top-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.08);
    }

    .top-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .icon-primary {
        background: #F3F0FF;
        color: #6D5DFC;
    }

    .icon-success {
        background: #DCFCE7;
        color: #16A34A;
    }

    .icon-info {
        background: #E0F2FE;
        color: #0284C7;
    }

    .top-stat-text h6 {
        font-size: 0.8rem;
        color: #64748B;
        margin: 0 0 0.35rem 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .top-stat-text p {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0F172A;
        margin: 0;
    }

    /* ═══════════════════ TOOLBAR ═══════════════════ */
    .cat-toolbar {
        padding: 18px 24px;
        border-bottom: 1px solid var(--pm-border, #e2e8f0);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: var(--pm-surface-2, #f8fafc);
        transition: background 0.3s, border-color 0.3s;
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

    /* Search */
    .cat-search-wrap {
        position: relative;
        min-width: 220px;
        max-width: 340px;
        flex: 1;
    }
    .cat-search-icon {
        position: absolute;
        top: 50%; left: 14px;
        transform: translateY(-50%);
        color: var(--pm-text-muted, #94a3b8);
        font-size: 0.88rem;
        pointer-events: none;
        z-index: 2;
        transition: color 0.3s;
    }
    [dir="rtl"] .cat-search-icon { left: auto; right: 14px; }

    .cat-search-input {
        width: 100%;
        border: 1.5px solid var(--pm-border, #e2e8f0);
        border-radius: 12px;
        padding: 10px 14px 10px 38px;
        font-size: 0.85rem;
        background: var(--pm-surface, #ffffff);
        color: var(--pm-text-1, #0f172a);
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }
    [dir="rtl"] .cat-search-input { padding: 10px 38px 10px 14px; }
    .cat-search-input:focus {
        border-color: var(--da-cyan, #00C8FF);
        box-shadow: 0 0 0 3px var(--da-cyan-soft, rgba(0,200,255,0.1));
        background: var(--pm-surface, #ffffff);
    }
    .cat-search-input::placeholder { color: var(--pm-text-muted, #94a3b8); }

    /* Length select */
    .cat-length-select {
        border: 1.5px solid var(--pm-border, #e2e8f0);
        border-radius: 11px;
        padding: 9px 14px;
        font-size: 0.82rem;
        background: var(--pm-surface, #ffffff);
        color: var(--pm-text-1, #0f172a);
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .cat-length-select:focus {
        border-color: var(--da-cyan, #00C8FF);
        box-shadow: 0 0 0 3px var(--da-cyan-soft, rgba(0,200,255,0.1));
    }

    /* Add button */
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
    .cat-add-btn:active { transform: translateY(0); }

    /* Fix table border radius top when toolbar is present */
    .saas-table-card {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-top: none;
    }

    /* ══ Premium Create Modal (From Products) ══ */
    .pm-modal-premium .modal-content {
        border: none;
        border-radius: 24px;
        box-shadow: 0 32px 80px -12px rgba(0,0,0,.18), 0 0 0 1px rgba(226,232,240,.6);
        overflow: hidden;
    }

    .pm-modal-header-premium {
        background: linear-gradient(135deg, #060d1f 0%, #0f172a 60%, #060d1f 100%) !important;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        border-bottom: none !important;
        display: block !important;
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
    .pm-modal-header-glow-1 { width:220px; height:220px; background:rgba(99,102,241,.25); top:-80px; right:-60px; }
    .pm-modal-header-glow-2 { width:160px; height:160px; background:rgba(59,130,246,.18); bottom:-60px; left:-40px; }

    .pm-modal-icon-premium {
        width: 52px; height: 52px;
        border-radius: 16px;
        background: rgba(255,255,255,.1);
        border: 1.5px solid rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        color: #a5b4fc;
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
        color: #60a5fa !important;
        margin: 3px 0 0;
        font-weight: 500;
    }
    .pm-modal-close-premium {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: rgba(255,255,255,.08);
        border: 1.5px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.7);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all .2s;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .pm-modal-close-premium:hover {
        background: rgba(255,255,255,.16);
        color: #fff;
    }

    .pm-modal-body-premium {
        padding: 28px 32px;
        background: #fff;
    }

    .pm-section-label {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .9px;
        color: #6366f1;
        margin-bottom: 16px;
        margin-top: 8px;
    }
    .pm-section-label::after {
        content: '';
        flex: 1;
        height: 1.5px;
        background: linear-gradient(90deg, rgba(99,102,241,.2) 0%, transparent 100%);
        border-radius: 99px;
    }

    .pm-modal-footer-premium {
        padding: 20px 32px;
        background: #f8faff;
        border-top: 1px solid #e8edf5;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .pm-btn-cancel {
        display: inline-flex; align-items: center; gap: 7px;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 11px 22px;
        font-size: .875rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
    }
    .pm-btn-cancel:hover { background: #f1f5f9; border-color: #cbd5e1; color: #0f172a; }

    .pm-btn-save {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 28px;
        font-size: .875rem;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        transition: all .25s;
        box-shadow: 0 4px 16px rgba(99,102,241,.3);
        position: relative;
        overflow: hidden;
    }
    .pm-btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(99,102,241,.38); }

    .pm-form-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: .6px;
    }
    .pm-form-control {
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 11px 16px;
        font-size: 0.875rem;
        width: 100%;
        background: #f8fafc;
        color: #0f172a;
        outline: none;
        transition: all 0.2s;
    }
    .pm-form-control:focus {
        border-color: #3b82f6;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }

    /* Dark Mode Overrides for Categories Index */
    html[data-app-theme="dark"] .saas-page {
        background-color: #0f172a !important;
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .saas-header-text h1 {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .saas-header-text p {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .kpi-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2) !important;
    }
    html[data-app-theme="dark"] .kpi-card .text-muted {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .kpi-card h3 {
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .cat-toolbar {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .cat-search-input {
        background: #1e293b !important;
        color: #ffffff !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .cat-search-input:focus {
        background: #1e293b !important;
    }
    html[data-app-theme="dark"] .cat-length-select {
        background: #1e293b !important;
        color: #ffffff !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .saas-table-card {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .saas-table td {
        color: #ffffff !important;
        border-bottom-color: #334155 !important;
    }
    html[data-app-theme="dark"] .saas-table tr:hover td {
        background: #0f172a !important;
    }
    html[data-app-theme="dark"] .pm-modal-premium .modal-content {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .pm-modal-body-premium {
        background: #0f172a !important;
    }
    html[data-app-theme="dark"] .pm-modal-footer-premium {
        background: #1e293b !important;
        border-top-color: #334155 !important;
    }
    html[data-app-theme="dark"] .pm-form-control {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #ffffff !important;
    }
    html[data-app-theme="dark"] .pm-form-control:focus {
        background: #0f172a !important;
    }
    html[data-app-theme="dark"] .pm-form-label {
        color: #cbd5e1 !important;
    }
    html[data-app-theme="dark"] .pm-section-label {
        color: #818cf8 !important;
    }
    html[data-app-theme="dark"] .pm-btn-cancel {
        background: #1e293b !important;
        color: #cbd5e1 !important;
        border-color: #334155 !important;
    }
    html[data-app-theme="dark"] .pm-btn-cancel:hover {
        background: #0f172a !important;
        color: #ffffff !important;
    }

    html[data-app-theme="dark"] .saas-table td .text-muted {
        color: #94a3b8 !important;
    }
    html[data-app-theme="dark"] .saas-table td .text-muted i {
        color: #94a3b8 !important;
    }
</style>

<div class="saas-page">
    


    <!-- Top Stats Cards -->
    <div class="top-stats-row">
        <div class="kpi-card p-3 d-flex align-items-center gap-3">
            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="bi bi-folder2-open fs-3"></i>
            </div>
            <div>
                <div class="small text-muted fw-bold text-uppercase mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الأقسام' : 'Total Categories' }}</div>
                <h3 class="fw-bold m-0 text-primary">{{ $categories->count() }}</h3>
            </div>
        </div>
        <div class="kpi-card p-3 d-flex align-items-center gap-3">
            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="bi bi-check-circle fs-3"></i>
            </div>
            <div>
                <div class="small text-muted fw-bold text-uppercase mb-1">{{ app()->getLocale() == 'ar' ? 'الأقسام النشطة' : 'Active Categories' }}</div>
                <h3 class="fw-bold m-0 text-success">{{ $categories->count() }}</h3>
            </div>
        </div>
        <div class="kpi-card p-3 d-flex align-items-center gap-3">
            <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="bi bi-clock-history fs-3"></i>
            </div>
            <div>
                <div class="small text-muted fw-bold text-uppercase mb-1">{{ app()->getLocale() == 'ar' ? 'آخر تحديث' : 'Last Updated' }}</div>
                <h3 class="fw-bold m-0 text-info fs-4">{{ app()->getLocale() == 'ar' ? 'الآن' : 'Just now' }}</h3>
            </div>
        </div>
    </div>

    <!-- Toolbar (Products Style) -->
    <div class="cat-toolbar">
        <div class="cat-toolbar-left">
            <div class="cat-search-wrap">
                <i class="bi bi-search cat-search-icon"></i>
                <input type="text" class="cat-search-input" placeholder="{{ app()->getLocale() == 'ar' ? 'البحث عن الأقسام...' : 'Search categories...' }}">
            </div>
            @can('create-categories')
            <button type="button" class="cat-add-btn" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-lg"></i> {{ app()->getLocale() == 'ar' ? 'إضافة قسم جديد' : 'Add New Category' }}
            </button>
            @endcan

        </div>
    </div>

    <!-- Main Table Card -->
    <div class="saas-table-card">
        <div class="table-responsive">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>ID <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ app()->getLocale() == 'ar' ? 'القسم' : 'Category' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الإنشاء' : 'Created At' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ app()->getLocale() == 'ar' ? 'أنشئ بواسطة' : 'Created By' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ التحديث' : 'Updated At' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th>{{ app()->getLocale() == 'ar' ? 'حدث بواسطة' : 'Updated By' }} <i class="bi bi-chevron-expand ms-1 text-white-50"></i></th>
                        <th class="text-center">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Helper arrays for colors
                        $avatarColors = ['purple', 'green', 'blue', 'orange'];
                        $iconBgColors = ['#E0E7FF', '#FEE2E2', '#DCFCE7', '#FEF3C7', '#F3E8FF', '#E0F2FE'];
                        $iconTextColors = ['#4338CA', '#B91C1C', '#15803D', '#B45309', '#7E22CE', '#0369A1'];
                    @endphp

                    @foreach ($categories as $index => $category)
                    @php
                        // Generate consistent colors based on ID
                        $avatarColor1 = $avatarColors[($category->id) % 4];
                        $avatarColor2 = $avatarColors[($category->id + 1) % 4];
                        $iconBg = $iconBgColors[($category->id) % 6];
                        $iconText = $iconTextColors[($category->id) % 6];
                        
                        $catName = $category->getTranslation('name');
                        $firstLetter = mb_substr($catName, 0, 1, 'UTF-8') ?: 'C';
                        
                        $createdBy = optional($category->createdBy)->full_name ?? '—';
                        $updatedBy = optional($category->updatedBy)->full_name ?? '—';
                        
                        $creatorInitial = $createdBy !== '—' ? mb_substr($createdBy, 0, 1, 'UTF-8') : '?';
                        $updaterInitial = $updatedBy !== '—' ? mb_substr($updatedBy, 0, 1, 'UTF-8') : '?';
                    @endphp
                    <tr class="category-row" data-status="{{ $category->is_active ? 'active' : 'inactive' }}">
                        <td>
                            <span class="cat-id-badge">{{ $category->id }}</span>
                        </td>
                        <td>
                            <div class="cat-wrapper">
                                <div class="cat-icon-box" style="background-color: {{ $iconBg }}; color: {{ $iconText }};">
                                    {{ mb_strtoupper($firstLetter, 'UTF-8') }}
                                </div>
                                {{ $catName }}
                            </div>
                        </td>
                        <td>
                            <span class="text-muted fw-normal"><i class="bi bi-calendar3 me-2"></i>{{ $category->created_at?->format('Y-m-d') }}</span>
                        </td>
                        <td>
                            <div class="avatar-wrapper">
                                <span class="fw-normal text-muted">{{ $createdBy }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted fw-normal"><i class="bi bi-calendar3 me-2"></i>{{ $category->updated_at?->format('Y-m-d') }}</span>
                        </td>
                        <td>
                            <div class="avatar-wrapper">
                                <span class="fw-normal text-muted">{{ $updatedBy }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="action-group justify-content-center">
                                @can('edit-categories')
                                <button type="button" class="btn-action-edit" 
                                    onclick="editCategory(this)"
                                    data-id="{{ $category->id }}"
                                    data-name-ar="{{ htmlspecialchars($category->getTranslation('name', 'ar'), ENT_QUOTES) }}"
                                    data-name-en="{{ htmlspecialchars($category->getTranslation('name', 'en'), ENT_QUOTES) }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endcan

                                @can('delete-categories')
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('pos.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete">
                                        <i class="bi bi-trash"></i>
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

        <!-- Footer Pagination -->
        <div class="saas-footer">
            <div class="pagination-wrapper">
                <span class="showing-text">
                    @if(app()->getLocale() == 'ar')
                        صفحة 1 من 1
                    @else
                        Page 1 of 1
                    @endif
                </span>
                <div class="pagination-pills">
                    <a href="#" class="page-pill"><i class="bi bi-chevron-left"></i></a>
                    <a href="#" class="page-pill active">1</a>
                    <a href="#" class="page-pill"><i class="bi bi-chevron-right"></i></a>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- CREATE CATEGORY MODAL --}}
<div class="modal fade pm-modal" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered pm-modal-premium">
        <form action="{{ route('categories.store') }}" method="POST" class="modal-content">
            @csrf
            
            {{-- Premium Header --}}
            <div class="pm-modal-header-premium modal-header">
                <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium">
                        <i class="bi bi-tags-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium" id="createCategoryModalLabel">{{ __('pos.add') . ' ' . __('pos.categories') }}</h5>
                        <p class="pm-modal-sub-premium">{{ app()->getLocale() == 'ar' ? 'إنشاء وإدارة أقسام المنتجات' : 'Create and manage product categories' }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="pm-modal-body-premium modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-info-circle-fill"></i> {{ app()->getLocale() == 'ar' ? 'معلومات القسم' : 'Category Information' }}</div>
                    </div>

                    {{-- Arabic Name --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'اسم القسم (عربي)' : 'Category Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="pm-form-control" dir="rtl" placeholder="مثال: إلكترونيات">
                    </div>
                    {{-- English Name --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'اسم القسم (إنجليزي)' : 'Category Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="pm-form-control" dir="ltr" placeholder="e.g. Electronics (Optional)">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pm-modal-footer-premium modal-footer">
                <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <button type="submit" class="pm-btn-save">
                    <i class="bi bi-check2"></i> {{ app()->getLocale() == 'ar' ? 'حفظ' : 'Save' }}
                </button>
            </div>
        </form>
    </div>
</div>
</div>

{{-- EDIT CATEGORY MODAL --}}
<div class="modal fade pm-modal" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered pm-modal-premium">
        <form id="editCategoryForm" action="" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            
            {{-- Premium Header --}}
            <div class="pm-modal-header-premium modal-header">
                <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium">
                        <i class="bi bi-pencil-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium" id="editCategoryModalLabel">{{ __('pos.edit') . ' ' . __('pos.categories') }}</h5>
                        <p class="pm-modal-sub-premium">{{ app()->getLocale() == 'ar' ? 'تحديث قسم المنتج' : 'Update product category' }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="pm-modal-body-premium modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-info-circle-fill"></i> {{ app()->getLocale() == 'ar' ? 'معلومات القسم' : 'Category Information' }}</div>
                    </div>

                    {{-- Arabic Name --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'اسم القسم (عربي)' : 'Category Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name_ar" name="name_ar" class="pm-form-control" dir="rtl" placeholder="مثال: إلكترونيات">
                    </div>
                    {{-- English Name --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'اسم القسم (إنجليزي)' : 'Category Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name_en" name="name_en" class="pm-form-control" dir="ltr" placeholder="e.g. Electronics (Optional)">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pm-modal-footer-premium modal-footer">
                <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <button type="submit" class="pm-btn-save">
                    <i class="bi bi-check2"></i> {{ app()->getLocale() == 'ar' ? 'تحديث' : 'Update' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // We removed DataTables to maintain the exact pixel-perfect design requested,
    // as DataTables injects its own DOM elements that break this highly custom layout.
    // If client-side search/pagination is needed, it can be implemented via JS manually.

    function editCategory(btn) {
        var id = $(btn).data('id');
        var nameAr = $(btn).data('name-ar');
        var nameEn = $(btn).data('name-en');

        // Set action URL
        var updateUrl = "{{ url('categories') }}/" + id;
        $('#editCategoryForm').attr('action', updateUrl);

        // Populate fields
        $('#edit_name_ar').val(nameAr);
        $('#edit_name_en').val(nameEn);

        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    }

    function applyFilters() {
        var statusFilter = $('#statusFilter');
        var status = statusFilter.length ? statusFilter.val() : 'all';
        var searchVal = $('.cat-search-input').val().toLowerCase().trim();

        $('.category-row').each(function() {
            var matchesStatus = (status === 'all') || ($(this).data('status') === status);
            
            var catName = $(this).find('.cat-wrapper').text().toLowerCase();
            var catId = $(this).find('.cat-id-badge').text().toLowerCase();
            var matchesSearch = (catName.indexOf(searchVal) > -1) || (catId.indexOf(searchVal) > -1);
            
            if (matchesStatus && matchesSearch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        updateVisibleCount();
    }

    $('.cat-search-input').on('keyup', applyFilters);

    function updateVisibleCount() {
        if ('{{ app()->getLocale() }}' === 'ar') {
            $('.showing-text').text('صفحة 1 من 1');
        } else {
            $('.showing-text').text('Page 1 of 1');
        }
    }
</script>
@endpush

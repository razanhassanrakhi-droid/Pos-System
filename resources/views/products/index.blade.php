@extends('layouts.app')

@section('title', __('product.product_management'))

@push('styles')
<style>
    /* ============================================================
       DIGITAL AGE POS — Enterprise Inventory UI
       Premium SaaS · Dark/Light Mode · Glassmorphism
    ============================================================ */

    /* ═══════════════════ CSS VARIABLES ═══════════════════ */
    :root,
    [data-pm-theme="light"] {
        --da-cyan:          #00C8FF;
        --da-cyan-dark:     #0099CC;
        --da-cyan-soft:     rgba(0,200,255,0.10);
        --da-cyan-glow:     rgba(0,200,255,0.25);
        --da-pink:          #FF1493;
        --da-pink-soft:     rgba(255,20,147,0.10);
        --da-pink-glow:     rgba(255,20,147,0.20);
        --da-grad:          linear-gradient(135deg, #00C8FF 0%, #8B5CF6 50%, #FF1493 100%);

        /* Semantic */
        --pm-primary:       #3b82f6;
        --pm-success:       #10b981;
        --pm-warning:       #f59e0b;
        --pm-danger:        #f43f5e;
        --pm-info:          #06b6d4;
        --pm-neutral:       #64748b;

        /* Surfaces */
        --pm-bg:            #f0f4ff;
        --pm-surface:       #ffffff;
        --pm-surface-2:     #f8fafc;
        --pm-surface-3:     #f1f5f9;
        --pm-border:        #e2e8f0;
        --pm-border-soft:   rgba(226,232,240,0.6);

        /* Text */
        --pm-text-1:        #0f172a;
        --pm-text-2:        #334155;
        --pm-text-3:        #475569;
        --pm-text-muted:    #94a3b8;

        /* Shadows */
        --pm-shadow-xs:     0 1px 3px rgba(0,0,0,0.04);
        --pm-shadow-sm:     0 4px 12px rgba(0,0,0,0.05);
        --pm-shadow-md:     0 8px 24px rgba(0,0,0,0.07);
        --pm-shadow-lg:     0 20px 60px rgba(0,0,0,0.10);

        --pm-radius:        16px;
        --pm-radius-sm:     10px;
        --pm-radius-xs:     6px;

        /* Badge soft colors */
        --pm-success-soft:  rgba(16,185,129,0.08);
        --pm-warning-soft:  rgba(245,158,11,0.08);
        --pm-danger-soft:   rgba(244,63,94,0.08);
        --pm-info-soft:     rgba(6,182,212,0.08);
        --pm-primary-soft:  rgba(59,130,246,0.08);
        --pm-neutral-soft:  #f8fafc;
    }

    [data-pm-theme="dark"] {
        --da-cyan:          #00D4FF;
        --da-cyan-dark:     #00AACC;
        --da-cyan-soft:     rgba(0,212,255,0.12);
        --da-cyan-glow:     rgba(0,212,255,0.30);
        --da-pink:          #FF1493;
        --da-pink-soft:     rgba(255,20,147,0.12);
        --da-pink-glow:     rgba(255,20,147,0.25);
        --da-grad:          linear-gradient(135deg, #00D4FF 0%, #8B5CF6 50%, #FF1493 100%);

        --pm-primary:       #60a5fa;
        --pm-success:       #34d399;
        --pm-warning:       #fbbf24;
        --pm-danger:        #fb7185;
        --pm-info:          #22d3ee;
        --pm-neutral:       #94a3b8;

        --pm-bg:            #040b18;
        --pm-surface:       #0b1427;
        --pm-surface-2:     #0f1e35;
        --pm-surface-3:     #162035;
        --pm-border:        rgba(0,200,255,0.15);
        --pm-border-soft:   rgba(0,200,255,0.08);

        --pm-text-1:        #e8f4ff;
        --pm-text-2:        #cbd5e1;
        --pm-text-3:        #94b8d4;
        --pm-text-muted:    #4a6b8a;

        --pm-shadow-xs:     0 1px 3px rgba(0,0,0,0.30);
        --pm-shadow-sm:     0 4px 12px rgba(0,0,0,0.40);
        --pm-shadow-md:     0 8px 24px rgba(0,0,0,0.50);
        --pm-shadow-lg:     0 20px 60px rgba(0,0,0,0.60);

        --pm-success-soft:  rgba(52,211,153,0.12);
        --pm-warning-soft:  rgba(251,191,36,0.12);
        --pm-danger-soft:   rgba(251,113,133,0.12);
        --pm-info-soft:     rgba(34,211,238,0.12);
        --pm-primary-soft:  rgba(96,165,250,0.12);
        --pm-neutral-soft:  rgba(148,163,184,0.08);
    }

    /* ─── Products Dark Mode Overrides ─── */
    [data-pm-theme="dark"] .pm-card {
        background: var(--pm-surface) !important;
        border-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-card-header {
        background: var(--pm-surface) !important;
        border-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-header-title {
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-toolbar {
        background: var(--pm-surface-2) !important;
        border-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-search-input {
        background: var(--pm-surface-3) !important;
        border-color: var(--pm-border) !important;
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-search-input:focus {
        border-color: var(--da-cyan) !important;
        box-shadow: 0 0 0 4px var(--da-cyan-soft) !important;
        background: var(--pm-surface-3) !important;
    }
    [data-pm-theme="dark"] .pm-length-select {
        background: var(--pm-surface-3) !important;
        border-color: var(--pm-border) !important;
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-dt-inner {
        background: var(--pm-surface) !important;
        border-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-table thead th {
        background: var(--pm-surface-2) !important;
        color: var(--pm-text-2) !important;
        border-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-table tbody tr {
        background: var(--pm-surface) !important;
    }
    [data-pm-theme="dark"] .pm-table tbody td {
        background: var(--pm-surface) !important;
        color: var(--pm-text-2) !important;
        border-color: var(--pm-border-soft) !important;
    }
    [data-pm-theme="dark"] .pm-table tbody tr:hover td {
        background: var(--pm-surface-2) !important;
    }
    [data-pm-theme="dark"] .pm-product-name {
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-product-img {
        background: var(--pm-surface-2) !important;
        border-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-product-img-placeholder {
        background: var(--pm-surface-2) !important;
        border-color: var(--pm-border) !important;
        color: var(--pm-text-muted) !important;
    }
    [data-pm-theme="dark"] .pm-row-expired td  { background: rgba(244, 63, 94, 0.08) !important; }
    [data-pm-theme="dark"] .pm-row-expiring td { background: rgba(245, 158, 11, 0.08) !important; }
    [data-pm-theme="dark"] .pm-row-expired:hover td  { background: rgba(244, 63, 94, 0.12) !important; }
    [data-pm-theme="dark"] .pm-row-expiring:hover td { background: rgba(245, 158, 11, 0.12) !important; }

    /* ═══════════════════ PAGE BACKGROUND ═══════════════════ */
    [data-pm-theme="light"] #content {
        background:
            radial-gradient(ellipse 80% 60% at 10% 0%, rgba(0,200,255,.06) 0%, transparent 60%),
            radial-gradient(ellipse 60% 50% at 90% 10%, rgba(99,102,241,.06) 0%, transparent 55%),
            radial-gradient(ellipse 50% 40% at 50% 80%, rgba(16,185,129,.04) 0%, transparent 55%),
            linear-gradient(160deg, #f0f6ff 0%, #f7f9fe 40%, #f0fdf7 100%);
        background-attachment: fixed;
        min-height: 100vh;
    }

    [data-pm-theme="dark"] #content {
        background:
            radial-gradient(ellipse 80% 60% at 10% 0%, rgba(0,200,255,.07) 0%, transparent 60%),
            radial-gradient(ellipse 60% 50% at 90% 10%, rgba(255,20,147,.05) 0%, transparent 55%),
            radial-gradient(ellipse 50% 40% at 50% 90%, rgba(139,92,246,.04) 0%, transparent 55%),
            linear-gradient(160deg, #040b18 0%, #060d1f 50%, #040b18 100%);
        background-attachment: fixed;
        min-height: 100vh;
    }

    /* Dot grid */
    #content::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image: radial-gradient(var(--pm-border) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
        z-index: 0;
        opacity: 0.5;
    }
    #content > .container-fluid { position: relative; z-index: 1; }

    /* ═══════════════════ HERO HEADER ═══════════════════ */
    .da-hero {
        background: var(--pm-surface);
        border: 1px solid var(--pm-border);
        border-radius: 24px;
        padding: 28px 36px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--pm-shadow-md);
        transition: background 0.3s, border-color 0.3s;
    }
    .da-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--da-grad);
    }
    .da-hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(70px);
        pointer-events: none;
    }
    .da-hero-blob.b1 {
        width: 200px; height: 200px;
        background: var(--da-cyan-glow);
        top: -60px; right: 10%;
    }
    .da-hero-blob.b2 {
        width: 160px; height: 160px;
        background: var(--da-pink-glow);
        bottom: -50px; right: 25%;
        opacity: 0.5;
    }

    .da-hero-left {
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        z-index: 2;
    }

    .da-hero-logo {
        width: 54px; height: 54px;
        border-radius: 16px;
        object-fit: contain;
        filter: drop-shadow(0 0 10px var(--da-cyan-glow));
        flex-shrink: 0;
    }

    .da-hero-title {
        font-size: 1.5rem;
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -0.5px;
        color: var(--pm-text-1);
        margin: 0;
        transition: color 0.3s;
    }
    .da-hero-title .cyan { color: var(--da-cyan); }
    .da-hero-title .pink { color: var(--da-pink); }
    .da-hero-sub {
        font-size: 0.78rem;
        color: var(--pm-text-muted);
        margin-top: 4px;
        font-weight: 500;
        transition: color 0.3s;
    }

    .da-hero-right {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 2;
        flex-wrap: wrap;
    }

    /* ═══════════════════ STATS STRIP ═══════════════════ */
    .pm-stats-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 991px) { .pm-stats-strip { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .pm-stats-strip { grid-template-columns: 1fr; } }

    .pm-stat-card {
        background: var(--pm-surface);
        border: 1px solid var(--pm-border);
        border-radius: var(--pm-radius);
        padding: 20px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--pm-shadow-xs);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .pm-stat-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 4px; height: 100%;
        border-radius: 0 4px 4px 0;
        background: transparent;
        transition: background 0.3s;
    }
    .pm-stat-card:hover {
        box-shadow: var(--pm-shadow-md);
        transform: translateY(-3px);
        border-color: var(--da-cyan);
    }
    .pm-stat-card:hover::after { background: var(--da-grad); }

    .pm-stat-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .pm-stat-card:hover .pm-stat-icon { transform: scale(1.1) rotate(5deg); }
    .pm-stat-icon.blue   { background: var(--pm-primary-soft);  color: var(--pm-primary); }
    .pm-stat-icon.green  { background: var(--pm-success-soft);  color: var(--pm-success); }
    .pm-stat-icon.amber  { background: var(--pm-warning-soft);  color: var(--pm-warning); }
    .pm-stat-icon.red    { background: var(--pm-danger-soft);   color: var(--pm-danger);  }

    .pm-stat-value { font-size: 1.55rem; font-weight: 800; line-height: 1.1; color: var(--pm-text-1); letter-spacing: -0.5px; transition: color 0.3s; }
    .pm-stat-label { font-size: 0.78rem; font-weight: 500; color: var(--pm-text-muted); margin-top: 4px; transition: color 0.3s; }

    /* ═══════════════════ MAIN CARD ═══════════════════ */
    .pm-card {
        background: var(--pm-surface);
        border: 1px solid var(--pm-border);
        border-radius: 24px;
        box-shadow: var(--pm-shadow-sm);
        overflow: hidden;
        transition: background 0.3s, border-color 0.3s, box-shadow 0.3s;
    }

    /* ═══════════════════ TOOLBAR ═══════════════════ */
    .pm-toolbar {
        padding: 18px 24px;
        border-bottom: 1px solid var(--pm-border);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: var(--pm-surface-2);
        transition: background 0.3s, border-color 0.3s;
    }

    .pm-toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
        flex-wrap: wrap;
    }
    .pm-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    /* Search */
    .pm-search-wrap {
        position: relative;
        min-width: 220px;
        max-width: 340px;
        flex: 1;
    }
    .pm-search-icon {
        position: absolute;
        top: 50%; left: 14px;
        transform: translateY(-50%);
        color: var(--pm-text-muted);
        font-size: 0.88rem;
        pointer-events: none;
        z-index: 2;
        transition: color 0.3s;
    }
    [dir="rtl"] .pm-search-icon { left: auto; right: 14px; }

    .pm-search-input {
        width: 100%;
        border: 1.5px solid var(--pm-border);
        border-radius: 12px;
        padding: 10px 14px 10px 38px;
        font-size: 0.85rem;
        background: var(--pm-surface);
        color: var(--pm-text-1);
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }
    [dir="rtl"] .pm-search-input { padding: 10px 38px 10px 14px; }
    .pm-search-input:focus {
        border-color: var(--da-cyan);
        box-shadow: 0 0 0 3px var(--da-cyan-soft);
        background: var(--pm-surface);
    }
    .pm-search-input::placeholder { color: var(--pm-text-muted); }

    /* Toolbar buttons */
    .pm-tb-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 11px;
        border: 1.5px solid var(--pm-border);
        background: var(--pm-surface);
        color: var(--pm-text-3);
        font-size: 0.8rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .pm-tb-btn:hover {
        border-color: var(--da-cyan);
        color: var(--da-cyan);
        background: var(--da-cyan-soft);
    }
    .pm-tb-btn i { font-size: 0.88rem; }

    /* Length select */
    .pm-length-select {
        border: 1.5px solid var(--pm-border);
        border-radius: 11px;
        padding: 9px 14px;
        font-size: 0.82rem;
        background: var(--pm-surface);
        color: var(--pm-text-1);
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .pm-length-select:focus {
        border-color: var(--da-cyan);
        box-shadow: 0 0 0 3px var(--da-cyan-soft);
    }

    /* Theme toggle inside toolbar */
    .pm-theme-toggle {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 14px;
        border-radius: 11px;
        border: 1.5px solid var(--pm-border);
        background: var(--pm-surface);
        color: var(--pm-text-3);
        font-size: 0.78rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .pm-theme-toggle:hover {
        border-color: var(--da-cyan);
        color: var(--da-cyan);
        background: var(--da-cyan-soft);
    }
    [data-pm-theme="dark"] .pm-theme-toggle {
        border-color: var(--da-cyan);
        color: var(--da-cyan);
        background: var(--da-cyan-soft);
    }

    /* Add button */
    .pm-add-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, var(--da-cyan) 0%, #0070aa 50%, var(--da-pink) 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 11px 22px;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 16px rgba(0,200,255,0.25), 0 2px 8px rgba(255,20,147,0.15);
        white-space: nowrap;
        font-family: inherit;
        letter-spacing: 0.2px;
        position: relative;
        overflow: hidden;
    }
    .pm-add-btn::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 60%);
        pointer-events: none;
    }
    .pm-add-btn:hover {
        box-shadow: 0 8px 28px rgba(0,200,255,0.35), 0 4px 14px rgba(255,20,147,0.25);
        transform: translateY(-2px);
        color: #fff;
        filter: brightness(1.05);
    }
    .pm-add-btn:active { transform: translateY(0); }

    /* ═══════════════════ TABLE ═══════════════════ */
    .pm-dt-inner {
        overflow-x: auto;
    }

    .pm-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px; /* Spaced rows */
        font-size: 0.875rem;
    }
    
    .pm-table thead th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .pm-table thead th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
    [dir="rtl"] .pm-table thead th:first-child { border-radius: 0 12px 12px 0; }
    [dir="rtl"] .pm-table thead th:last-child { border-radius: 12px 0 0 12px; }

    .pm-table thead th {
        background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: .9px;
        padding: 14px 20px;
        border: none !important; /* Remove all borders to make it a continuous bar */
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
        text-align: center !important;
        transition: background 0.3s, color 0.3s, border-color 0.3s;
    }

    .pm-table tbody tr {
        background: var(--pm-surface);
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border-radius: 16px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pm-table tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    }
    .pm-table tbody tr:hover td {
        background: var(--pm-surface) !important;
    }
    [data-pm-theme="dark"] .pm-table tbody tr {
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    [data-pm-theme="dark"] .pm-table tbody tr:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
    }
    [data-pm-theme="dark"] .pm-table tbody tr:hover td {
        background: var(--pm-surface) !important;
    }

    .pm-table tbody td {
        padding: 16px 20px;
        color: var(--pm-text-2);
        vertical-align: middle;
        background: transparent;
        border-bottom: none !important;
        border-top: none !important;
        transition: background 0.18s, color 0.3s;
    }
    .pm-table tbody td:first-child {
        border-top-right-radius: 16px;
        border-bottom-right-radius: 16px;
    }
    .pm-table tbody td:last-child {
        border-top-left-radius: 16px;
        border-bottom-left-radius: 16px;
    }

    /* ── Product cell ── */
    .pm-product-cell { display: flex; align-items: center; gap: 14px; }

    .pm-product-img {
        width: 52px; height: 52px;
        border-radius: 14px;
        object-fit: cover;
        border: 1.5px solid var(--pm-border);
        flex-shrink: 0;
        background: var(--pm-surface-2);
        transition: all 0.2s;
        cursor: pointer;
    }
    .pm-product-img:hover {
        transform: scale(1.06);
        box-shadow: 0 4px 16px var(--da-cyan-glow);
        border-color: var(--da-cyan);
    }

    .pm-product-img-placeholder {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: var(--pm-surface-3);
        border: 1.5px solid var(--pm-border);
        display: flex; align-items: center; justify-content: center;
        color: var(--pm-text-muted); font-size: 1.1rem;
        flex-shrink: 0;
    }

    .pm-product-name {
        font-weight: 700;
        color: var(--pm-text-1);
        line-height: 1.35;
        font-size: 0.9rem;
        transition: color 0.3s;
    }
    .pm-product-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
    .pm-product-id { font-size: 0.72rem; color: var(--pm-text-muted); font-weight: 500; transition: color 0.3s; }
    .pm-product-desc { font-size: 0.7rem; color: var(--pm-text-muted); max-width: 200px; margin-top: 2px; transition: color 0.3s; }

    /* ── Badges ── */
    .pm-badge {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: var(--pm-radius-xs);
        padding: 3px 9px;
        font-size: 0.7rem;
        font-weight: 600;
        line-height: 1.4;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .pm-badge:hover { transform: translateY(-1px); }
    .pm-badge-cat    { background: var(--pm-neutral-soft); color: var(--pm-text-3); border-color: var(--pm-border); }
    .pm-badge-bc     { background: transparent; color: var(--pm-text-muted); font-size: 0.72rem; font-weight: 600; padding: 0; border: none; }
    .pm-badge-bc:hover { transform: none; }
    .pm-badge-success{ background: var(--pm-success-soft); color: var(--pm-success); border-color: rgba(16,185,129,0.2); }
    .pm-badge-danger { background: var(--pm-danger-soft);  color: var(--pm-danger);  border-color: rgba(244,63,94,0.2); }
    .pm-badge-warning{ background: var(--pm-warning-soft); color: var(--pm-warning); border-color: rgba(245,158,11,0.2); }
    .pm-badge-info   { background: var(--pm-info-soft);    color: var(--pm-info);    border-color: rgba(6,182,212,0.2); }
    .pm-badge-neutral{ background: var(--pm-neutral-soft); color: var(--pm-neutral); border-color: var(--pm-border); }
    .pm-badge-warranty-none { background: var(--pm-neutral-soft); color: var(--pm-text-muted); border-color: var(--pm-border); }

    /* Pulse dot in badge */
    .pm-badge .dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: currentColor;
        display: inline-block;
        flex-shrink: 0;
    }

    /* ── Stock cell ── */
    .pm-stock-val { font-weight: 800; font-size: 1.05rem; color: var(--pm-text-1); transition: color 0.3s; }
    .pm-stock-min { font-size: 0.7rem; color: var(--pm-text-muted); margin-top: 3px; font-weight: 500; transition: color 0.3s; }
    .pm-stock-bar {
        height: 5px;
        border-radius: 99px;
        background: var(--pm-surface-3);
        margin-top: 7px;
        overflow: hidden;
        width: 100px;
    }
    .pm-stock-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, var(--pm-success) 0%, #34d399 100%);
        transition: width .4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pm-stock-fill.low { background: linear-gradient(90deg, var(--pm-warning) 0%, #fbbf24 100%); }
    .pm-stock-fill.out { background: linear-gradient(90deg, var(--pm-danger) 0%, #fb7185 100%); }

    /* ── Price cell ── */
    .pm-price { font-weight: 800; font-size: 1rem; color: var(--pm-text-1); transition: color 0.3s; }
    .pm-price-currency { font-size: 0.7rem; color: var(--pm-text-muted); font-weight: 600; margin-top: 1px; transition: color 0.3s; }

    /* ── Batches button ── */
    .pm-batch-btn {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--pm-surface);
        color: var(--pm-primary);
        border: 1.5px solid var(--pm-border);
        border-radius: var(--pm-radius-sm);
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        font-family: inherit;
    }
    .pm-batch-btn:hover {
        background: var(--da-cyan-soft);
        border-color: var(--da-cyan);
        color: var(--da-cyan);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px var(--da-cyan-glow);
    }

    .pm-batch-count {
        background: var(--da-cyan);
        color: #fff;
        border-radius: 99px;
        font-size: 0.66rem;
        font-weight: 800;
        padding: 2px 7px;
        line-height: 1.3;
    }

    /* ── Action buttons ── */
    .pm-actions-group {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        justify-content: center;
    }

    .pm-icon-btn {
        width: 34px; height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid transparent;
        border-radius: var(--pm-radius-sm);
        background: var(--pm-surface-2);
        cursor: pointer;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 0.9rem;
        position: relative;
        text-decoration: none;
        flex-shrink: 0;
    }
    .pm-icon-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .pm-icon-btn.sell   { color: var(--pm-success); border-color: rgba(16,185,129,0.25); background: var(--pm-success-soft); }
    .pm-icon-btn.sell:hover { background: rgba(16,185,129,0.18); border-color: rgba(16,185,129,0.5); }
    .pm-icon-btn.history { color: var(--pm-info); border-color: rgba(6,182,212,0.25); background: var(--pm-info-soft); }
    .pm-icon-btn.history:hover { background: rgba(6,182,212,0.18); border-color: rgba(6,182,212,0.5); }
    .pm-icon-btn.edit   { color: var(--pm-primary); border-color: rgba(59,130,246,0.25); background: var(--pm-primary-soft); }
    .pm-icon-btn.edit:hover { background: rgba(59,130,246,0.18); border-color: rgba(59,130,246,0.5); }
    .pm-icon-btn.delete { color: var(--pm-danger); border-color: rgba(244,63,94,0.25); background: var(--pm-danger-soft); }
    .pm-icon-btn.delete:hover { background: rgba(244,63,94,0.18); border-color: rgba(244,63,94,0.5); }

    /* Tooltip */
    .pm-icon-btn[data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: calc(100% + 7px);
        left: 50%;
        transform: translateX(-50%);
        background: var(--pm-text-1);
        color: var(--pm-surface);
        font-size: 0.68rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 7px;
        white-space: nowrap;
        pointer-events: none;
        z-index: 99;
        letter-spacing: .3px;
        box-shadow: var(--pm-shadow-sm);
    }
    .pm-icon-btn[data-tooltip]:hover::before {
        content: '';
        position: absolute;
        bottom: calc(100% + 2px);
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: var(--pm-text-1);
        pointer-events: none;
        z-index: 99;
    }

    /* ── DataTables override ── */
    .pm-dt-wrapper .dataTables_filter,
    .pm-dt-wrapper .dataTables_length { display: none !important; }

    /* Custom DataTables Pagination */
    .pm-dt-wrapper .row:last-child {
        padding: 12px 20px;
        background-color: var(--pm-surface-alt, #f8fafc);
        border-top: 1px solid var(--pm-border);
        align-items: center;
        margin: 0;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }
    .pm-dt-wrapper .dataTables_info {
        padding-top: 0 !important;
        color: var(--pm-text-muted) !important;
        font-size: 0.85rem;
    }
    .pm-dt-wrapper .dataTables_paginate {
        margin: 0 !important;
    }
    .pm-dt-wrapper .pagination {
        margin-bottom: 0;
        gap: 0.25rem;
        justify-content: flex-end;
    }
    html[dir="rtl"] .pm-dt-wrapper .pagination {
        justify-content: flex-start;
    }
    .pm-dt-wrapper .page-item .page-link {
        border-radius: 8px !important;
        border: none;
        color: var(--pm-text-2);
        background-color: transparent;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .pm-dt-wrapper .page-item:not(.active):not(.disabled) .page-link:hover {
        background-color: var(--pm-border);
    }
    .pm-dt-wrapper .page-item.active .page-link {
        background-color: var(--pm-primary) !important;
        color: white !important;
        box-shadow: 0 4px 10px rgba(109, 93, 252, 0.2);
    }
    [data-pm-theme="dark"] .pm-dt-wrapper .row:last-child {
        background-color: rgba(30, 41, 59, 0.5);
    }
    [data-pm-theme="dark"] .pm-dt-wrapper .page-item:not(.active):not(.disabled) .page-link:hover {
        background-color: rgba(255,255,255,0.05);
    }

    /* ── Expiry row tints ── */
    .pm-row-expired td  { background: rgba(244,63,94,0.04) !important; }
    .pm-row-expiring td { background: rgba(245,158,11,0.04) !important; }
    .pm-row-expired:hover td  { background: rgba(244,63,94,0.08) !important; }
    .pm-row-expiring:hover td { background: rgba(245,158,11,0.08) !important; }
    [data-pm-theme="dark"] .pm-row-expired td  { background: rgba(251,113,133,0.07) !important; }
    [data-pm-theme="dark"] .pm-row-expiring td { background: rgba(251,191,36,0.06) !important; }

    /* ── Modals ── */
    .pm-modal .modal-content {
        border: 1px solid var(--pm-border);
        border-radius: 20px;
        box-shadow: var(--pm-shadow-lg);
        background: var(--pm-surface);
        overflow: hidden;
        transition: background 0.3s, border-color 0.3s;
    }
    .pm-modal .modal-header {
        background: var(--pm-surface);
        border-bottom: 1px solid var(--pm-border);
        padding: 20px 28px;
        transition: background 0.3s, border-color 0.3s;
    }
    .pm-modal .modal-footer {
        background: var(--pm-surface-2);
        border-top: 1px solid var(--pm-border);
        padding: 16px 28px;
        transition: background 0.3s, border-color 0.3s;
    }
    .pm-modal .modal-body { padding: 28px; }

    .pm-modal-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .pm-modal-title { font-size: 1.05rem; font-weight: 800; color: var(--pm-text-1); margin: 0; transition: color 0.3s; }
    .pm-modal-sub   { font-size: 0.78rem; color: var(--pm-text-muted); margin: 3px 0 0; font-weight: 500; transition: color 0.3s; }

    .pm-form-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--pm-text-3);
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: .6px;
        transition: color 0.3s;
    }
    .pm-form-control {
        border: 1.5px solid var(--pm-border);
        border-radius: 12px;
        padding: 11px 16px;
        font-size: 0.875rem;
        width: 100%;
        background: var(--pm-surface-2);
        color: var(--pm-text-1);
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }
    .pm-form-control:focus {
        border-color: var(--da-cyan);
        background: var(--pm-surface);
        box-shadow: 0 0 0 3px var(--da-cyan-soft);
    }
    select.pm-form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 16px center;
        background-repeat: no-repeat;
        background-size: 18px;
        padding-right: 40px;
    }
    html[dir="rtl"] select.pm-form-control {
        background-position: left 16px center;
        padding-left: 40px;
        padding-right: 16px;
    }
    .pm-form-control[type="file"] { padding: 8px 12px; }
    .pm-form-control[type="file"]::file-selector-button {
        background: var(--pm-surface-3);
        border: none;
        border-radius: 8px;
        padding: 5px 12px;
        margin-right: 12px;
        color: var(--pm-text-2);
        font-weight: 600;
        font-size: 0.82rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    html[dir="rtl"] .pm-form-control[type="file"]::file-selector-button {
        margin-right: 0; margin-left: 12px;
    }

    .pm-input-group {
        display: flex;
        align-items: stretch;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid var(--pm-border);
        background: var(--pm-surface-2);
        transition: all 0.2s;
    }
    .pm-input-group:focus-within {
        border-color: var(--da-cyan);
        background: var(--pm-surface);
        box-shadow: 0 0 0 3px var(--da-cyan-soft);
    }
    .pm-input-group-text {
        background: var(--pm-surface-3);
        padding: 0 16px;
        color: var(--pm-text-3);
        font-size: 0.875rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        user-select: none;
        border-right: 1.5px solid var(--pm-border);
        transition: background 0.3s, border-color 0.3s, color 0.3s;
    }
    html[dir="rtl"] .pm-input-group-text {
        border-right: none;
        border-left: 1.5px solid var(--pm-border);
    }
    .pm-input-group .pm-form-control {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        flex: 1;
        border-radius: 0 !important;
    }
    .pm-input-group .pm-form-control:focus {
        background: transparent !important;
        box-shadow: none !important;
    }

    .pm-warranty-card {
        background: var(--da-cyan-soft);
        border: 1.5px solid rgba(0,200,255,0.2);
        border-radius: 16px;
        padding: 16px 20px;
        transition: all 0.2s;
    }

    .pm-batch-row { transition: background 0.2s; }
    .pm-batch-row:hover { background: var(--pm-surface-2) !important; }
    .pm-batch-expired  { background: var(--pm-danger-soft) !important; }
    .pm-batch-expiring { background: var(--pm-warning-soft) !important; }

    /* ── History meta card ── */
    .pm-meta-card {
        background: var(--pm-surface-2);
        border: 1px solid var(--pm-border);
        border-radius: 14px;
        padding: 16px 20px;
        transition: background 0.3s, border-color 0.3s;
    }

    /* ── Loading spinner ── */
    .pm-spinner-cell { padding: 40px !important; text-align: center; }

    /* ── Empty state ── */
    .pm-empty {
        padding: 60px 24px;
        text-align: center;
        color: var(--pm-text-muted);
        transition: color 0.3s;
    }
    .pm-empty i { font-size: 3.5rem; margin-bottom: 16px; display: block; opacity: .4; }
    .pm-empty p { font-size: 0.95rem; font-weight: 500; }

    /* ── Action dropdown backward compat ── */
    .pm-action-btn::after { display: none !important; }
    .pm-dropdown-divider { border-top: 1px solid var(--pm-border); margin: 6px 0; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .pm-table thead th, .pm-table tbody td { padding: 11px 14px; font-size: 0.82rem; }
        .pm-product-img, .pm-product-img-placeholder { width: 40px; height: 40px; }
        .pm-toolbar { padding: 14px 16px; }
        .pm-modal .modal-body { padding: 20px; }
    }
    @media (max-width: 960px) {
        .pm-toolbar-left { flex: 1 1 100%; }
    }

    /* Modal offset */
    @media (min-width: 769px) {
        html[dir="ltr"] .modal-dialog { margin-left: auto !important; margin-right: auto !important; padding-left: 260px; }
        html[dir="rtl"] .modal-dialog { margin-left: auto !important; margin-right: auto !important; padding-right: 260px; padding-left: 0; }
    }
</style>

<style>
    /* ══ Premium Create/Edit Modal ══ */
    .pm-modal-premium .modal-content {
        border: 1px solid var(--pm-border);
        border-radius: 24px;
        box-shadow: var(--pm-shadow-lg);
        background: var(--pm-surface);
        overflow: hidden;
        transition: background 0.3s, border-color 0.3s;
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
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
        background-size: 22px 22px;
        pointer-events: none;
    }
    .pm-modal-header-glow {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
    }
    .pm-modal-header-glow-1 { width:220px; height:220px; background:rgba(0,200,255,.25); top:-80px; right:-60px; }
    .pm-modal-header-glow-2 { width:160px; height:160px; background:rgba(255,20,147,.18); bottom:-60px; left:-40px; }

    .pm-modal-icon-premium {
        width: 52px; height: 52px;
        border-radius: 16px;
        background: rgba(255,255,255,.1);
        border: 1.5px solid rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        color: var(--da-cyan);
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
    .pm-modal-close-premium:hover { background: rgba(255,255,255,.16); color: #fff; }

    .pm-modal-body-premium {
        padding: 28px 32px;
        background: var(--pm-surface);
        transition: background 0.3s;
    }

    .pm-section-label {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .9px;
        color: var(--da-cyan);
        margin-bottom: 16px;
        margin-top: 8px;
    }
    .pm-section-label::after {
        content: '';
        flex: 1;
        height: 1.5px;
        background: linear-gradient(90deg, var(--da-cyan-soft) 0%, transparent 100%);
        border-radius: 99px;
    }
    .pm-section-label i { font-size: .88rem; }

    .pm-modal-footer-premium {
        padding: 20px 32px;
        background: var(--pm-surface-2);
        border-top: 1px solid var(--pm-border);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        transition: background 0.3s, border-color 0.3s;
    }
    .pm-btn-cancel {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--pm-surface);
        border: 1.5px solid var(--pm-border);
        border-radius: 12px;
        padding: 11px 22px;
        font-size: .875rem;
        font-weight: 600;
        color: var(--pm-text-3);
        cursor: pointer;
        transition: all .2s;
        font-family: inherit;
        text-decoration: none;
    }
    .pm-btn-cancel:hover { background: var(--pm-surface-2); border-color: var(--pm-border); color: var(--pm-text-1); }

    .pm-btn-save {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, var(--da-cyan) 0%, #0070aa 50%, var(--da-pink) 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 28px;
        font-size: .875rem;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        transition: all .25s;
        box-shadow: 0 4px 16px var(--da-cyan-glow);
        font-family: inherit;
        letter-spacing: .2px;
        position: relative;
        overflow: hidden;
    }
    .pm-btn-save::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.12) 0%, transparent 100%);
        opacity: 0;
        transition: opacity .2s;
    }
    .pm-btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px var(--da-cyan-glow), 0 4px 12px var(--da-pink-glow);
    }
    .pm-btn-save:hover::before { opacity: 1; }
    .pm-btn-save:active { transform: translateY(0); }

    :root {
        --pm-primary:      #3b82f6;
        --pm-primary-hover:#2563eb;
        --pm-primary-soft: rgba(59, 130, 246, 0.06);
        --pm-success:      #10b981;
        --pm-success-soft: rgba(16, 185, 129, 0.06);
        --pm-warning:      #f59e0b;
        --pm-warning-soft: rgba(245, 158, 11, 0.06);
        --pm-danger:       #f43f5e;
        --pm-danger-soft:  rgba(244, 63, 94, 0.06);
        --pm-info:         #06b6d4;
        --pm-info-soft:    rgba(6, 182, 212, 0.06);
        --pm-neutral:      #64748b;
        --pm-neutral-soft: #f8fafc;
        --pm-border:       #e2e8f0;
        --pm-radius:       16px;
        --pm-shadow-sm:    0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        --pm-shadow-md:    0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        --pm-shadow-lg:    0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
    }

    /* ══ Modern 2026 Page Background ══ */
    #content {
        background:
            radial-gradient(ellipse 80% 60% at 10% 0%, rgba(99,102,241,.07) 0%, transparent 60%),
            radial-gradient(ellipse 60% 50% at 90% 10%, rgba(59,130,246,.07) 0%, transparent 55%),
            radial-gradient(ellipse 50% 40% at 50% 80%, rgba(16,185,129,.05) 0%, transparent 55%),
            radial-gradient(ellipse 40% 30% at 80% 90%, rgba(245,158,11,.04) 0%, transparent 50%),
            linear-gradient(160deg, #f0f4ff 0%, #f7f9fe 40%, #f0fdf7 100%);
        background-attachment: fixed;
        min-height: 100vh;
    }

    /* Dot grid overlay */
    #content::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image: radial-gradient(rgba(99,102,241,.09) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
        z-index: 0;
    }

    /* Content sits above the dot grid */
    #content > .container-fluid { position: relative; z-index: 1; }

    /* ── Stats strip ── */
    .pm-stats-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    @media (max-width: 991px) { .pm-stats-strip { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .pm-stats-strip { grid-template-columns: 1fr; } }

    .pm-stat-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: var(--pm-radius);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--pm-shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .pm-stat-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 4px; height: 100%;
        background: transparent;
        transition: background-color 0.3s;
    }
    .pm-stat-card:hover {
        box-shadow: var(--pm-shadow-lg);
        transform: translateY(-3px);
        border-color: rgba(203, 213, 225, 0.8);
    }
    .pm-stat-card:hover::after {
        background: currentColor;
    }

    .pm-stat-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .pm-stat-card:hover .pm-stat-icon {
        transform: scale(1.1) rotate(5deg);
    }
    .pm-stat-icon.blue   { background: var(--pm-primary-soft);  color: var(--pm-primary); }
    .pm-stat-icon.green  { background: var(--pm-success-soft);  color: var(--pm-success); }
    .pm-stat-icon.amber  { background: var(--pm-warning-soft);  color: var(--pm-warning); }
    .pm-stat-icon.red    { background: var(--pm-danger-soft);   color: var(--pm-danger);  }

    .pm-stat-value { font-size: 1.625rem; font-weight: 800; line-height: 1.1; color: #0f172a; letter-spacing: -0.5px; }
    .pm-stat-label { font-size: 0.82rem; font-weight: 500; color: var(--pm-neutral); margin-top: 4px; }

    /* ── Main card ── */
    .pm-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, 0.7);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        transition: box-shadow 0.3s;
    }
    .pm-card:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.04);
    }

    .pm-card-header {
        padding: 28px 32px;
        border-bottom: 1px solid var(--pm-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        background: #fff;
    }

    .pm-header-title { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px; }
    .pm-header-subtitle { font-size: 0.82rem; color: var(--pm-neutral); margin-top: 4px; }

    .pm-add-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 12px 24px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.2);
        white-space: nowrap;
    }
    .pm-add-btn:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        transform: translateY(-2px);
        color: #fff;
    }
    .pm-add-btn:active { transform: translateY(0); }

    /* ── Search toolbar ── */
    .pm-toolbar {
        padding: 20px 32px;
        border-bottom: 1px solid var(--pm-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        background: #fafbfc;
    }

    .pm-search-wrap {
        position: relative;
        flex: 1;
        min-width: 260px;
        max-width: 400px;
    }
    .pm-search-icon {
        position: absolute;
        top: 50%; left: 16px;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.95rem;
        pointer-events: none;
    }
    [dir="rtl"] .pm-search-icon { left: auto; right: 16px; }

    .pm-search-input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px 16px 12px 42px;
        font-size: 0.875rem;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01) inset;
    }
    [dir="rtl"] .pm-search-input { padding: 12px 42px 12px 16px; }
    .pm-search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
        background: #fff;
    }
    .pm-search-input::placeholder { color: #cbd5e1; }

    .pm-length-select {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 11px 16px;
        font-size: 0.875rem;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pm-length-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08); }

    /* ── Main card ── */
    .pm-card {
        background: transparent;
        border: none;
        box-shadow: none;
        overflow: visible;
    }

    /* ── Table ── */
    .pm-dt-inner {
        overflow-x: auto;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        margin-bottom: 16px;
    }

    .pm-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0;
        font-size: 0.875rem;
    }

    .pm-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .8px;
        padding: 16px 24px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }

    .pm-table tbody tr {
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .pm-table tbody tr:hover td {
        background: #f8fafc;
    }

    .pm-table tbody td {
        padding: 16px 24px;
        color: #334155;
        vertical-align: middle;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
    }
    .pm-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    html[dir="ltr"] .pm-table tbody td:first-child,
    html[dir="ltr"] .pm-table thead th:first-child {
        border-radius: 0;
    }
    html[dir="ltr"] .pm-table tbody td:last-child,
    html[dir="ltr"] .pm-table thead th:last-child {
        border-radius: 0;
    }
    [dir="rtl"] .pm-table tbody td:first-child,
    [dir="rtl"] .pm-table thead th:first-child {
        border-radius: 0;
    }
    [dir="rtl"] .pm-table tbody td:last-child,
    [dir="rtl"] .pm-table thead th:last-child {
        border-radius: 0;
    }

    /* ── Product cell ── */
    .pm-product-cell { display: flex; align-items: center; gap: 14px; }

    .pm-product-img {
        width: 52px; height: 52px;
        border-radius: 14px;
        object-fit: cover;
        border: 1px solid rgba(0, 0, 0, 0.05);
        flex-shrink: 0;
        background: #f8fafc;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .pm-product-img:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

    .pm-product-img-placeholder {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: #f1f5f9;
        border: 1px solid rgba(0, 0, 0, 0.05);
        display: flex; align-items: center; justify-content: center;
        color: #cbd5e1; font-size: 1.1rem;
        flex-shrink: 0;
    }

    .pm-product-name { font-weight: 600; color: #0f172a; line-height: 1.35; font-size: 0.925rem; }
    .pm-product-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 5px; }
    .pm-product-id { font-size: 0.75rem; color: #94a3b8; font-weight: 500; }
    .pm-product-desc { font-size: 0.72rem; color: #94a3b8; max-width: 200px; margin-top: 3px; }

    /* ── Badges ── */
    .pm-badge {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.7rem;
        font-weight: 500;
        line-height: 1.4;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .pm-badge:hover { transform: translateY(-1px); }
    .pm-badge-cat    { background: rgba(241, 245, 249, 0.6); color: #475569; border-color: #e2e8f0; }
    .pm-badge-bc     { background: transparent; color: #94a3b8; font-size: 0.7rem; font-weight: 500; padding: 0; border: none; }
    .pm-badge-bc:hover { transform: none; }
    .pm-badge-success{ background: rgba(16, 185, 129, 0.05); color: #10b981; border-color: rgba(16, 185, 129, 0.15); }
    .pm-badge-danger { background: rgba(244, 63, 94, 0.05);  color: #f43f5e;  border-color: rgba(244, 63, 94, 0.15); }
    .pm-badge-warning{ background: rgba(245, 158, 11, 0.05); color: #f59e0b; border-color: rgba(245, 158, 11, 0.15); }
    .pm-badge-info   { background: rgba(6, 182, 212, 0.05);    color: #06b6d4;    border-color: rgba(6, 182, 212, 0.15); }
    .pm-badge-neutral{ background: rgba(241, 245, 249, 0.6); color: #64748b; border-color: #e2e8f0; }
    .pm-badge-warranty-none { background: rgba(241, 245, 249, 0.4); color: #94a3b8; border-color: #e2e8f0; }

    /* ── Stock cell ── */
    .pm-stock-val { font-weight: 800; font-size: 1.05rem; color: #0f172a; }
    .pm-stock-min { font-size: 0.72rem; color: #94a3b8; margin-top: 4px; font-weight: 500; }
    .pm-stock-bar {
        height: 6px;
        border-radius: 99px;
        background: #e2e8f0;
        margin-top: 8px;
        overflow: hidden;
        width: 100px;
    }
    .pm-stock-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        transition: width .4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pm-stock-fill.low    { background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%); }
    .pm-stock-fill.out    { background: linear-gradient(90deg, #f43f5e 0%, #fb7185 100%); }

    /* ── Price cell ── */
    .pm-price { font-weight: 800; font-size: 1rem; color: #0f172a; }
    .pm-price-currency { font-size: 0.72rem; color: #94a3b8; font-weight: 600; margin-top: 2px; }

    /* ── Batches button ── */
    .pm-batch-btn {
        display: inline-flex; align-items: center; gap: 7px;
        background: #fff;
        color: var(--pm-primary);
        border: 1.5px solid var(--pm-border);
        border-radius: 10px;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .pm-batch-btn:hover { background: var(--pm-primary-soft); border-color: var(--pm-primary); transform: translateY(-1px); }

    .pm-batch-count {
        background: var(--pm-primary);
        color: #fff;
        border-radius: 99px;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 7px;
        line-height: 1.3;
    }

    /* ── Action icon buttons ── */
    .pm-actions-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
    }

    .pm-icon-btn {
        width: 34px; height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid transparent;
        border-radius: 10px;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 0.92rem;
        position: relative;
        text-decoration: none;
        flex-shrink: 0;
    }
    .pm-icon-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Sell – green */
    .pm-icon-btn.sell {
        color: var(--pm-success);
        border-color: rgba(16, 185, 129, 0.2);
        background: rgba(16, 185, 129, 0.05);
    }
    .pm-icon-btn.sell:hover {
        background: rgba(16, 185, 129, 0.12);
        border-color: rgba(16, 185, 129, 0.4);
        color: #059669;
    }

    /* History – cyan */
    .pm-icon-btn.history {
        color: var(--pm-info);
        border-color: rgba(6, 182, 212, 0.2);
        background: rgba(6, 182, 212, 0.05);
    }
    .pm-icon-btn.history:hover {
        background: rgba(6, 182, 212, 0.12);
        border-color: rgba(6, 182, 212, 0.4);
        color: #0891b2;
    }

    /* Edit – blue */
    .pm-icon-btn.edit {
        color: var(--pm-primary);
        border-color: rgba(59, 130, 246, 0.2);
        background: rgba(59, 130, 246, 0.05);
    }
    .pm-icon-btn.edit:hover {
        background: rgba(59, 130, 246, 0.12);
        border-color: rgba(59, 130, 246, 0.4);
        color: #1d4ed8;
    }

    /* Delete – red */
    .pm-icon-btn.delete {
        color: var(--pm-danger);
        border-color: rgba(244, 63, 94, 0.2);
        background: rgba(244, 63, 94, 0.05);
    }
    .pm-icon-btn.delete:hover {
        background: rgba(244, 63, 94, 0.12);
        border-color: rgba(244, 63, 94, 0.4);
        color: #e11d48;
    }

    /* Tooltip */
    .pm-icon-btn[data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: calc(100% + 7px);
        left: 50%;
        transform: translateX(-50%);
        background: #0f172a;
        color: #fff;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 7px;
        white-space: nowrap;
        pointer-events: none;
        z-index: 99;
        letter-spacing: .3px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .pm-icon-btn[data-tooltip]:hover::before {
        content: '';
        position: absolute;
        bottom: calc(100% + 2px);
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #0f172a;
        pointer-events: none;
        z-index: 99;
    }

    /* Keep old dropdown styles for backward compat */
    .pm-action-btn::after { display: none !important; }
    .pm-dropdown-divider { border-top: 1px solid var(--pm-border); margin: 6px 0; }

    /* ── DataTables override ── */
    .pm-dt-wrapper .dataTables_filter,
    .pm-dt-wrapper .dataTables_length { display: none !important; }

    /* ── Expiry row tints ── */
    .pm-row-expired td  { background: #fff8f8 !important; }
    .pm-row-expiring td { background: #fffbf5 !important; }
    .pm-row-expired:hover td  { background: #fff3f3 !important; }
    .pm-row-expiring:hover td { background: #fff6e9 !important; }

    /* ── Modals ── */
    .pm-modal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: var(--pm-shadow-lg), 0 25px 50px -12px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .pm-modal .modal-header {
        background: #fff;
        border-bottom: 1px solid var(--pm-border);
        padding: 20px 28px;
    }
    .pm-modal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid var(--pm-border);
        padding: 16px 28px;
    }
    .pm-modal .modal-body { padding: 28px; }

    .pm-modal-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .pm-modal-title { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0; }
    .pm-modal-sub   { font-size: 0.78rem; color: #94a3b8; margin: 3px 0 0; font-weight: 500; }

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
        transition: all 0.2s ease-in-out;
        box-shadow: inset 0 2px 4px 0 rgba(15, 23, 42, 0.05);
    }
    .pm-form-control:focus {
        border-color: var(--pm-primary);
        background: #ffffff;
        box-shadow: inset 0 2px 4px 0 rgba(15, 23, 42, 0.01), 0 0 0 4px rgba(59, 130, 246, 0.12);
    }

    select.pm-form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 16px center;
        background-repeat: no-repeat;
        background-size: 18px;
        padding-right: 40px;
    }
    html[dir="rtl"] select.pm-form-control {
        background-position: left 16px center;
        padding-left: 40px;
        padding-right: 16px;
    }

    .pm-form-control[type="file"] {
        padding: 8px 12px;
    }
    .pm-form-control[type="file"]::file-selector-button {
        background-color: #e2e8f0;
        border: none;
        border-radius: 8px;
        padding: 5px 12px;
        margin-right: 12px;
        color: #334155;
        font-weight: 600;
        font-size: 0.82rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pm-form-control[type="file"]::file-selector-button:hover {
        background-color: #cbd5e1;
        color: #0f172a;
    }
    html[dir="rtl"] .pm-form-control[type="file"]::file-selector-button {
        margin-right: 0;
        margin-left: 12px;
    }

    .pm-input-group {
        display: flex;
        align-items: stretch;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid #cbd5e1;
        background: #f8fafc;
        box-shadow: inset 0 2px 4px 0 rgba(15, 23, 42, 0.05);
        transition: all 0.2s ease-in-out;
    }
    .pm-input-group:focus-within {
        border-color: var(--pm-primary);
        background: #ffffff;
        box-shadow: inset 0 2px 4px 0 rgba(15, 23, 42, 0.01), 0 0 0 4px rgba(59, 130, 246, 0.12);
    }
    .pm-input-group-text {
        background: #e2e8f0;
        padding: 0 16px;
        color: #475569;
        font-size: 0.875rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        user-select: none;
    }
    .pm-input-group .pm-input-group-text:first-child {
        border-right: 1.5px solid #cbd5e1;
    }
    html[dir="rtl"] .pm-input-group .pm-input-group-text:first-child {
        border-right: none;
        border-left: 1.5px solid #cbd5e1;
    }
    .pm-input-group .pm-input-group-text:last-child {
        border-left: 1.5px solid #cbd5e1;
    }
    html[dir="rtl"] .pm-input-group .pm-input-group-text:last-child {
        border-left: none;
        border-right: 1.5px solid #cbd5e1;
    }
    
    .pm-input-group .pm-form-control {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        flex: 1;
        width: 100%;
        padding: 11px 16px;
        border-radius: 0 !important;
    }
    .pm-input-group .pm-form-control:focus {
        background: transparent !important;
        box-shadow: none !important;
    }

    .pm-warranty-card {
        background: var(--pm-primary-soft);
        border: 1.5px solid rgba(59, 130, 246, 0.15);
        border-radius: 16px;
        padding: 16px 20px;
        transition: all 0.2s;
    }

    /* ── Batch modal ── */
    .pm-batch-row { transition: background-color 0.2s; }
    .pm-batch-row:hover { background: #f8fafc; }

    .pm-batch-expired  { background: rgba(244, 63, 94, 0.02) !important; }
    .pm-batch-expiring { background: rgba(245, 158, 11, 0.02) !important; }
    .pm-batch-expired:hover  { background: rgba(244, 63, 94, 0.05) !important; }
    .pm-batch-expiring:hover { background: rgba(245, 158, 11, 0.05) !important; }

    /* ── History / Movements modal ── */
    .pm-meta-card {
        background: #f8fafc;
        border: 1px solid var(--pm-border);
        border-radius: 14px;
        padding: 16px 20px;
    }

    /* ── Loading spinner ── */
    .pm-spinner-cell { padding: 40px !important; text-align: center; }

    /* ── Empty state ── */
    .pm-empty {
        padding: 60px 24px;
        text-align: center;
        color: #94a3b8;
    }
    .pm-empty i { font-size: 3.5rem; margin-bottom: 16px; display: block; opacity: .4; }
    .pm-empty p { font-size: 0.95rem; font-weight: 500; }

    /* ── Responsive table text ── */
    @media (max-width: 768px) {
        .pm-table thead th, .pm-table tbody td { padding: 12px 14px; font-size: 0.82rem; }
        .pm-product-img, .pm-product-img-placeholder { width: 42px; height: 42px; }
        .pm-card-header { padding: 18px 20px; }
        .pm-toolbar { padding: 14px 20px; }
        .pm-modal .modal-body { padding: 20px; }
    }

    .form-check-input { cursor: pointer; }

    /* ── Modal offset: center inside content area ─────────────────
       IMPORTANT: Do NOT use padding on .modal — it breaks backdrop
       click-to-close and focus-trap (keyboard Escape stops working).
       Instead we shift only the dialog itself via margin.
    ────────────────────────────────────────────────────────────── */
    @media (min-width: 769px) {
        html[dir="ltr"] .modal-dialog { margin-left: auto !important; margin-right: auto !important; padding-left: 260px; }
        html[dir="rtl"] .modal-dialog { margin-left: auto !important; margin-right: auto !important; padding-right: 260px; padding-left: 0; }
    }
</style>

<style>
    /* ══ Premium Create/Edit Modal ══ */
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
    .pm-section-label i { font-size: .88rem; }

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
        font-family: inherit;
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
        transition: all .25s cubic-bezier(.4,0,.2,1);
        box-shadow: 0 4px 16px rgba(99,102,241,.3);
        font-family: inherit;
        letter-spacing: .2px;
        position: relative;
        overflow: hidden;
    }
    .pm-btn-save::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.1) 0%, transparent 100%);
        opacity: 0;
        transition: opacity .2s;
    }
    .pm-btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(99,102,241,.38);
    }
    .pm-btn-save:hover::before { opacity: 1; }
    .pm-btn-save:active { transform: translateY(0); }

    /* Dark Mode Premium Modal Overrides */
    [data-pm-theme="dark"] .pm-modal-premium .modal-content {
        background: var(--pm-surface) !important;
        border-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-modal-body-premium {
        background: var(--pm-surface) !important;
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-modal-footer-premium {
        background: var(--pm-surface-2) !important;
        border-top-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-btn-cancel {
        background: var(--pm-surface) !important;
        border-color: var(--pm-border) !important;
        color: var(--pm-text-2) !important;
    }
    [data-pm-theme="dark"] .pm-btn-cancel:hover {
        background: var(--pm-surface-2) !important;
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-form-control,
    [data-pm-theme="dark"] .pm-input-group {
        background: var(--pm-surface-2) !important;
        border-color: var(--pm-border) !important;
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-form-control:focus,
    [data-pm-theme="dark"] .pm-input-group:focus-within {
        background: var(--pm-surface) !important;
        border-color: var(--da-cyan) !important;
    }
    [data-pm-theme="dark"] .pm-input-group-text {
        background: var(--pm-surface-3) !important;
        border-color: var(--pm-border) !important;
        color: var(--pm-text-2) !important;
    }
    [data-pm-theme="dark"] .pm-modal-title-premium {
        color: #fff !important;
    }
    [data-pm-theme="dark"] .pm-modal-sub-premium {
        color: var(--da-cyan) !important;
    }
    [data-pm-theme="dark"] .pm-meta-card {
        background: var(--pm-surface-2) !important;
        border-color: var(--pm-border) !important;
    }
    [data-pm-theme="dark"] .pm-meta-card span,
    [data-pm-theme="dark"] .pm-meta-card p,
    [data-pm-theme="dark"] .pm-meta-card small {
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-meta-card small span {
        color: var(--pm-text-2) !important;
    }
    [data-pm-theme="dark"] #batchesTotalRow tr,
    [data-pm-theme="dark"] #batchesTotalRow td {
        background: var(--pm-surface-2) !important;
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-table tbody td,
    [data-pm-theme="dark"] .pm-table tbody td * {
        color: var(--pm-text-2);
    }
    [data-pm-theme="dark"] .pm-stock-val,
    [data-pm-theme="dark"] .pm-price,
    [data-pm-theme="dark"] .pm-product-name,
    [data-pm-theme="dark"] .pm-table td div[style*="color:#334155"],
    [data-pm-theme="dark"] .pm-table td div[style*="color: #334155"] {
        color: var(--pm-text-1) !important;
    }
    [data-pm-theme="dark"] .pm-stock-val.text-danger {
        color: var(--pm-danger) !important;
    }
    [data-pm-theme="dark"] .pm-warranty-card label {
        color: #fff !important;
    }
    [data-pm-theme="dark"] .pm-badge-bc {
        color: var(--pm-text-2) !important;
    }

    /* ── Status Filter Select ── */
    .pm-status-filter {
        border: 1.5px solid var(--pm-border);
        border-radius: 11px;
        padding: 9px 14px;
        font-size: 0.82rem;
        background: var(--pm-surface);
        color: var(--pm-text-1);
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .pm-status-filter:focus {
        border-color: var(--da-cyan);
        box-shadow: 0 0 0 3px var(--da-cyan-soft);
    }
    [data-pm-theme="dark"] .pm-status-filter {
        background: var(--pm-surface-3) !important;
        border-color: var(--pm-border) !important;
        color: var(--pm-text-1) !important;
    }

    /* ── Bulk Action Bar ── */
    .pm-bulk-bar {
        display: none;
        align-items: center;
        gap: 10px;
        padding: 10px 24px;
        background: linear-gradient(90deg, rgba(99,102,241,0.08) 0%, rgba(0,200,255,0.05) 100%);
        border-bottom: 1px solid var(--pm-border);
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--pm-text-2);
        flex-wrap: wrap;
        transition: background 0.3s;
    }
    .pm-bulk-bar.active { display: flex; }
    [data-pm-theme="dark"] .pm-bulk-bar {
        background: linear-gradient(90deg, rgba(96,165,250,0.08) 0%, rgba(0,212,255,0.05) 100%) !important;
        border-bottom-color: var(--pm-border) !important;
        color: var(--pm-text-1) !important;
    }
    .pm-bulk-count {
        background: var(--pm-primary);
        color: #fff;
        border-radius: 99px;
        font-size: 0.7rem;
        font-weight: 800;
        padding: 2px 9px;
    }
    .pm-bulk-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px;
        border-radius: 9px;
        border: 1.5px solid transparent;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .pm-bulk-btn.activate {
        background: var(--pm-success-soft);
        color: var(--pm-success);
        border-color: rgba(16,185,129,0.25);
    }
    .pm-bulk-btn.activate:hover {
        background: rgba(16,185,129,0.18);
        border-color: rgba(16,185,129,0.5);
        transform: translateY(-1px);
    }
    .pm-bulk-btn.deactivate {
        background: var(--pm-danger-soft);
        color: var(--pm-danger);
        border-color: rgba(244,63,94,0.25);
    }
    .pm-bulk-btn.deactivate:hover {
        background: rgba(244,63,94,0.18);
        border-color: rgba(244,63,94,0.5);
        transform: translateY(-1px);
    }
    .pm-bulk-btn-clear {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 10px;
        border-radius: 7px;
        border: 1px solid var(--pm-border);
        background: transparent;
        color: var(--pm-text-muted);
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .pm-bulk-btn-clear:hover { color: var(--pm-danger); border-color: var(--pm-danger); }

    /* ── Row Checkbox ── */
    .pm-row-check {
        width: 18px; height: 18px;
        accent-color: var(--pm-primary);
        cursor: pointer;
    }
    .pm-table tbody tr.pm-selected td { background: rgba(99,102,241,0.05) !important; }
    [data-pm-theme="dark"] .pm-table tbody tr.pm-selected td { background: rgba(96,165,250,0.08) !important; }

    /* ── Product Status Badge ── */
    .pm-status-active {
        background: var(--pm-success-soft);
        color: var(--pm-success);
        border-color: rgba(16,185,129,0.2);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 6px;
        border: 1px solid;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .pm-status-inactive {
        background: var(--pm-danger-soft);
        color: var(--pm-danger);
        border-color: rgba(244,63,94,0.2);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 6px;
        border: 1px solid;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* ── Batches Modal Premium Table (Theme-Aware & Wider) ── */
    #batchesModal .modal-dialog {
        max-width: 950px !important;
    }
    #batchesModal .modal-content {
        background: var(--pm-surface) !important;
        border: 1px solid var(--pm-border) !important;
        box-shadow: var(--pm-shadow-lg) !important;
    }
    #batchesModal .pm-modal-header-premium {
        background: linear-gradient(135deg, #060d1f 0%, #0f172a 60%, #060d1f 100%) !important;
        border-bottom: none !important;
        position: relative;
    }
    #batchesModal .pm-modal-title-premium {
        color: #ffffff !important;
    }
    #batchesModal .pm-modal-close-premium {
        color: rgba(255,255,255,0.7) !important;
        background: rgba(255,255,255,0.08) !important;
        border: 1.5px solid rgba(255,255,255,0.12) !important;
    }
    #batchesModal .pm-modal-close-premium:hover {
        background: rgba(255,255,255,0.16) !important;
        color: #ffffff !important;
    }
    #batchesModal .pm-modal-body-premium {
        background: var(--pm-surface) !important;
        padding: 0 !important;
    }
    #batchesModal .pm-table-wrap {
        border: none !important;
        border-radius: 0 !important;
        overflow-x: visible !important;
    }
    #batchesModal .pm-table {
        background: var(--pm-surface) !important;
        width: 100% !important;
        border-collapse: collapse !important;
    }
    #batchesModal .pm-table thead th {
        background: var(--pm-surface-2) !important;
        color: var(--pm-text-1) !important;
        font-weight: 700;
        font-size: 0.88rem;
        text-transform: capitalize;
        border-bottom: 2px solid var(--pm-border) !important;
        padding: 16px 24px !important;
        letter-spacing: 0.5px;
        white-space: nowrap !important;
    }
    #batchesModal .pm-table tbody tr {
        background: transparent !important;
        border-bottom: 1px solid var(--pm-border-soft) !important;
        transition: background-color 0.2s ease;
    }
    #batchesModal .pm-table tbody tr:hover {
        background: var(--pm-surface-2) !important;
    }
    #batchesModal .pm-table tbody td {
        background: transparent !important;
        color: var(--pm-text-2) !important;
        border: none !important;
        border-bottom: 1px solid var(--pm-border-soft) !important;
        padding: 16px 24px !important;
        font-size: 0.9rem;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }
    #batchesModal .pm-modal-footer-premium {
        background: var(--pm-surface-2) !important;
        border-top: 1px solid var(--pm-border) !important;
        padding: 16px 32px !important;
    }
    #batchesModal .pm-btn-cancel {
        background: var(--pm-surface-3) !important;
        color: var(--pm-text-1) !important;
        border: 1px solid var(--pm-border) !important;
    }
    #batchesModal .pm-btn-cancel:hover {
        background: var(--pm-border) !important;
    }
    #batchesTotalRow tr {
        background: var(--pm-surface-2) !important;
    }
    #batchesTotalRow td {
        border-top: 2px solid var(--pm-border) !important;
        color: var(--pm-text-1) !important;
    }
    @media (max-width: 576px) {
        .pm-modal-premium.modal-dialog-centered {
            align-items: flex-start !important;
            margin: 0.5rem auto !important;
        }
        .pm-modal-header-premium {
            padding: 14px 16px !important;
        }
        .pm-modal-icon-premium {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
            font-size: 1.1rem !important;
        }
        .pm-modal-title-premium {
            font-size: 0.95rem !important;
        }
        .pm-modal-sub-premium {
            font-size: 0.7rem !important;
            margin-top: 1px !important;
        }
        .pm-modal-close-premium {
            width: 30px !important;
            height: 30px !important;
            border-radius: 8px !important;
            font-size: 0.85rem !important;
        }
    }
</style>
@endpush

@section('content')



{{-- ═══════════════════════════════════════════
     DIGITAL AGE — Enterprise Inventory Header
═══════════════════════════════════════════ --}}

{{-- Theme is stored on the page root --}}
<div id="pm-page-root">


{{-- ══ Main Card ══ --}}
<div class="pm-card">

    {{-- Toolbar --}}
    <div class="pm-toolbar">
        {{-- Left: Search + Filters --}}
        <div class="pm-toolbar-left">
            {{-- Search --}}
            <div class="pm-search-wrap">
                <i class="bi bi-search pm-search-icon"></i>
                <input type="text" id="pm-search-input" class="pm-search-input"
                       placeholder="{{ __('product.search') ?? 'Search products…' }}">
            </div>
            {{-- Add Product --}}
            <button class="pm-add-btn" data-bs-toggle="modal" data-bs-target="#createProductModal" style="padding: 9px 18px; font-size: 0.8rem; border-radius: 11px; line-height: 1.5;">
                <i class="bi bi-plus-lg"></i>
                {{ __('product.add_product') }}
            </button>
        </div>

        {{-- Right: Status Filter + Export + Per page --}}
        <div class="pm-toolbar-right">
            {{-- Status Filter --}}
            <select id="pm-status-filter" class="pm-status-filter" onchange="applyStatusFilter(this.value)">
                <option value="" {{ !$statusFilter ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? '⚡ كل الحالات' : '⚡ All Status' }}</option>
                <option value="Active" {{ $statusFilter == 'Active' ? 'selected' : '' }}>🟢 {{ app()->getLocale() == 'ar' ? 'نشط' : 'Active' }}</option>
                <option value="Inactive" {{ $statusFilter == 'Inactive' ? 'selected' : '' }}>🔴 {{ app()->getLocale() == 'ar' ? 'غير نشط' : 'Inactive' }}</option>
            </select>
            {{-- Export --}}
            <button class="pm-tb-btn" onclick="exportTable()">
                <i class="bi bi-download"></i>
                {{ app()->getLocale() == 'ar' ? 'تصدير' : 'Export' }}
            </button>
            {{-- Items per page --}}
            <select id="pm-length-select" class="pm-length-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>


    {{-- Table --}}
    <table class="pm-table" id="productsTable" style="width:100%">
                <thead>
                    <tr>

                        <th class="text-center" style="width:50px">#</th>
                        <th style="min-width:260px">{{ __('product.product_name') }}</th>
                        <th class="text-center" style="min-width:120px">{{ __('product.barcode') }}</th>
                        <th class="text-center" style="min-width:120px">SKU</th>
                        <th class="text-center" style="min-width:120px">{{ __('product.stock') }}</th>
                        <th class="text-center" style="min-width:110px">{{ __('product.sale_price') }}</th>
                        <th class="text-center" style="min-width:90px">{{ __('purchases.batches') ?? 'Batches' }}</th>
                        <th class="text-center" style="min-width:100px">{{ __('product.stock_status') }}</th>
                        <th class="text-center" style="min-width:90px">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th class="text-center" style="width:80px">{{ __('product.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        @php
                            $stockStatus      = $product->stock_status;
                            $stockBadgeClass  = 'pm-badge-success';
                            $stockFillClass   = '';
                            if ($stockStatus == 'Out of Stock') { $stockBadgeClass = 'pm-badge-neutral'; $stockFillClass = 'out'; }
                            elseif ($stockStatus == 'Low Stock')  { $stockBadgeClass = 'pm-badge-danger';  $stockFillClass = 'low'; }

                            $expiryBadgeClass = 'pm-badge-success';
                            $rowClass         = '';
                            if ($product->expiration_status == 'Expired')       { $expiryBadgeClass = 'pm-badge-danger';  $rowClass = 'pm-row-expired'; }
                            elseif ($product->expiration_status == 'Expiring Soon') { $expiryBadgeClass = 'pm-badge-warning'; $rowClass = 'pm-row-expiring'; }

                            // Stock percentage for mini-bar (capped at 100%)
                            $minStock  = max(1, $product->minimum_stock);
                            $curStock  = max(0, $product->current_stock);
                            $stockPct  = min(100, round(($curStock / max($minStock * 2, 1)) * 100));
                        @endphp
                        <tr class="{{ $rowClass }}" data-product-id="{{ $product->id }}">

                            {{-- Serial Number --}}
                            <td class="text-center fw-bold text-muted" style="font-size: 0.85rem;">
                                {{ $loop->iteration }}
                            </td>
                            {{-- Product Info --}}
                            <td>
                                <div class="pm-product-cell">
                                    @if($product->image)
                                        <img
                                            src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                            class="pm-product-img preview-product-img"
                                            data-src="{{ asset('storage/' . $product->image) }}"
                                            data-name="{{ $product->name }}"
                                            style="cursor:pointer;"
                                        >
                                    @else
                                        <div class="pm-product-img-placeholder">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                    <div class="pm-product-info-new" style="min-width:0; line-height: 1.4;">
                                        <!-- Row 1: Name and ID -->
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="product-detail-trigger fw-semibold text-dark fs-6 d-inline-flex align-items-center gap-1" 
                                                  style="font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: color 0.2s;"
                                                  onmouseover="this.style.color='var(--pm-primary, #6366f1)'" 
                                                  onmouseout="this.style.color=''"
                                                  data-id="{{ $product->id }}"
                                                  data-name-ar="{{ $product->getTranslation('name', 'ar') }}"
                                                  data-name-en="{{ $product->getTranslation('name', 'en') }}"
                                                  data-brand-ar="{{ $product->getTranslation('brand', 'ar') }}"
                                                  data-brand-en="{{ $product->getTranslation('brand', 'en') }}"
                                                  data-description-ar="{{ $product->getTranslation('description', 'ar') }}"
                                                  data-description-en="{{ $product->getTranslation('description', 'en') }}"
                                                  data-category="{{ $product->category ? $product->category->getTranslation('name') : '' }}"
                                                  data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}"
                                                  data-sale-price="{{ $product->sale_price }}"
                                                  data-minimum-stock="{{ $product->minimum_stock }}"
                                                  data-current-stock="{{ $product->current_stock }}"
                                                  data-base-unit="{{ $product->base_unit_name ?: 'Piece' }}"
                                                  data-has-warranty="{{ $product->has_warranty ? 1 : 0 }}"
                                                  data-warranty-months="{{ $product->warranty_period_months }}"
                                                  data-units="{{ $product->units->toJson() }}"
                                                  data-barcode="{{ $product->barcode }}"
                                                  data-sku="{{ $product->sku }}"
                                                  data-created-by="{{ $product->creator ? $product->creator->name : '' }}"
                                                  data-updated-by="{{ $product->updater ? $product->updater->name : '' }}"
                                                  data-created-at="{{ $product->created_at->format('Y-m-d H:i') }}"
                                                  title="{{ app()->getLocale() == 'ar' ? 'انقر لعرض التفاصيل' : 'Click to view details' }}">
                                                {{ $product->name }}
                                                <span class="detail-badge">
                                                    <i class="bi bi-layout-sidebar-reverse"></i>{{ app()->getLocale() == 'ar' ? 'التفاصيل' : 'Details' }}
                                                </span>
                                            </span>
                                        </div>
                                        
                                        <!-- Row 2: Category -->
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            @if($product->category)
                                                <span class="pm-badge pm-badge-cat-new" style="font-size: 0.72rem; padding: 2px 8px; background: rgba(59, 130, 246, 0.08); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.15); border-radius: 4px; font-weight: 500;">
                                                    <i class="bi bi-grid me-1"></i>{{ $product->category->getTranslation('name') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Barcode --}}
                            <td class="text-center">
                                @if($product->barcode)
                                    <span class="pm-badge pm-badge-bc pm-barcode-text" style="font-size: 0.82rem; font-weight: 600;">
                                        <i class="bi bi-upc-scan me-1" style="font-size:.82rem;"></i>{{ $product->barcode }}
                                    </span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- SKU --}}
                            <td class="text-center">
                                @if($product->sku)
                                    <span class="pm-badge pm-badge-bc pm-barcode-text" style="font-size: 0.82rem; font-weight: 600;">
                                        <i class="bi bi-hash me-1"></i>{{ $product->sku }}
                                    </span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Stock --}}
                            <td class="text-center">
                                <div class="pm-stock-val @if($stockStatus == 'Low Stock' || $stockStatus == 'Out of Stock') text-danger @endif">
                                    {{ intval($product->current_stock) }}
                                </div>
                                <div class="pm-stock-min">{{ __('product.minimum_stock') }}: {{ $product->minimum_stock }}</div>
                                <div class="pm-stock-bar" style="margin: 7px auto 0 auto;">
                                    <div class="pm-stock-fill {{ $stockFillClass }}" style="width:{{ $stockPct }}%"></div>
                                </div>
                            </td>

                            {{-- Sale Price --}}
                            <td class="text-center">
                                <div class="pm-price">{{ floor($product->sale_price) == $product->sale_price ? number_format($product->sale_price, 0) : number_format($product->sale_price, 2) }}</div>
                                <div class="pm-price-currency">{{ $setting->currency ?? '' }}</div>
                            </td>

                            {{-- Batches --}}
                            <td class="text-center">
                                <button class="pm-batch-btn" onclick="viewBatches({{ $product->id }})">
                                    <i class="bi bi-layers" style="font-size:.85rem"></i>
                                    <span class="pm-batch-count">
                                        {{ ($branchId ? $product->batches->where('branch_id', $branchId) : $product->batches)->where('quantity', '>', 0)->count() }}
                                    </span>
                                </button>
                            </td>



                            {{-- Stock Status --}}
                            <td class="text-center">
                                <span class="pm-badge {{ $stockBadgeClass }} d-inline-flex align-items-center gap-1">
                                    <span style="width:5px; height:5px; border-radius:50%; background-color:currentColor; display:inline-block;"></span>
                                    {{ __('product.' . str_replace(' ', '_', strtolower($product->stock_status))) }}
                                </span>
                            </td>

                            {{-- Product Status --}}
                            <td class="text-center" data-search="status-{{ ($product->status ?? 'Active') === 'Active' ? 'active' : 'inactive' }}">
                                <div class="d-flex align-items-center justify-content-center gap-2" title="{{ ($product->status ?? 'Active') === 'Active' ? (app()->getLocale() == 'ar' ? 'نشط' : 'Active') : (app()->getLocale() == 'ar' ? 'غير نشط' : 'Inactive') }}">
                                    <div class="form-check form-switch m-0" style="min-height: auto;">
                                        <input class="form-check-input mt-0" type="checkbox" role="switch" style="cursor: pointer; width: 2.8em; height: 1.4em;" 
                                            onchange="toggleProductStatus({{ $product->id }}, this.checked, this)" 
                                            {{ ($product->status ?? 'Active') === 'Active' ? 'checked' : '' }}>
                                    </div>
                                    <span class="status-label fw-bold" style="font-size: 0.8rem; color: {{ ($product->status ?? 'Active') === 'Active' ? '#10b981' : '#f43f5e' }};">
                                        {{ ($product->status ?? 'Active') === 'Active' ? (app()->getLocale() == 'ar' ? 'نشط' : 'Active') : (app()->getLocale() == 'ar' ? 'غير نشط' : 'Inactive') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                                <div class="pm-actions-group">

                                    {{-- Edit --}}
                                     <button class="pm-icon-btn edit edit-product-btn" type="button"
                                             data-id="{{ $product->id }}"
                                             data-name-ar="{{ $product->getTranslation('name', 'ar') }}"
                                             data-name-en="{{ $product->getTranslation('name', 'en') }}"
                                             data-brand-ar="{{ $product->getTranslation('brand', 'ar') }}"
                                             data-brand-en="{{ $product->getTranslation('brand', 'en') }}"
                                             data-description-ar="{{ $product->getTranslation('description', 'ar') }}"
                                             data-description-en="{{ $product->getTranslation('description', 'en') }}"
                                             data-sale-price="{{ $product->sale_price }}"
                                             data-minimum-stock="{{ $product->minimum_stock }}"
                                             data-category-id="{{ $product->category_id ?: 'null' }}"
                                             data-has-warranty="{{ $product->has_warranty ? 1 : 0 }}"
                                             data-warranty-months="{{ $product->warranty_period_months }}"
                                             data-base-unit-ar="{{ $product->base_unit_name_ar }}"
                                             data-base-unit-en="{{ $product->base_unit_name_en }}"
                                             data-units="{{ $product->units->toJson() }}"
                                             data-barcode="{{ $product->barcode }}"
                                             data-sku="{{ $product->sku }}"
                                             data-status="{{ $product->status }}"
                                             data-tooltip="{{ __('product.edit_product') }}">
                                         <i class="bi bi-pencil-square"></i>
                                     </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('{{ __('pos.confirm_delete') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="pm-icon-btn delete"
                                                data-tooltip="{{ __('pos.delete') ?? 'Delete' }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
</div>


{{-- ══════════════════════════════════════
     CREATE PRODUCT MODAL
══════════════════════════════════════ --}}
<div class="modal fade pm-modal" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable pm-modal-premium">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf

            {{-- ── Premium Header ── --}}
            <div class="pm-modal-header-premium modal-header">
                <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium" id="createProductModalLabel">{{ __('product.add_product') }}</h5>
                        <p class="pm-modal-sub-premium">{{ __('product.product_management') }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- ── Body ── --}}
            <div class="pm-modal-body-premium modal-body">
                <div class="row g-3">

                    {{-- Section: Basic Info --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-info-circle-fill"></i> {{ __('product.product_name') ?? 'Basic Information' }}</div>
                    </div>

                    {{-- Arabic Name --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.product_name_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="pm-form-control" dir="rtl" placeholder="اسم المنتج باللغة العربية">
                    </div>
                    {{-- English Name --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.product_name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="pm-form-control" dir="ltr" placeholder="Product name in English (Optional)">
                    </div>

                    {{-- Arabic Brand --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الاسم التجاري / البراند (عربي)' : 'Brand / Trade Name (Arabic)' }}</label>
                        <input type="text" name="brand_ar" class="pm-form-control" dir="rtl" placeholder="البراند أو الاسم التجاري بالعربية">
                    </div>
                    {{-- English Brand --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الاسم التجاري / البراند (إنجليزي)' : 'Brand / Trade Name (English)' }}</label>
                        <input type="text" name="brand_en" class="pm-form-control" dir="ltr" placeholder="Brand or Trade name in English">
                    </div>

                    {{-- Description AR --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.description_ar') }}</label>
                        <textarea name="description_ar" class="pm-form-control" rows="2" dir="rtl" placeholder="وصف المنتج باللغة العربية"></textarea>
                    </div>
                    {{-- Description EN --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.description_en') }}</label>
                        <textarea name="description_en" class="pm-form-control" rows="2" dir="ltr" placeholder="Product description in English"></textarea>
                    </div>

                    {{-- Section: Details --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-tag-fill"></i> {{ __('product.category') ?? 'Details & Classification' }}</div>
                    </div>

                    {{-- Image --}}
                    {{-- Image --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.product_image') }}</label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text"><i class="bi bi-image"></i></span>
                            <div class="pm-form-control d-flex align-items-center" style="padding: 0; background: transparent; position: relative;">
                                <input type="file" name="image" id="create_product_image" class="pm-form-control border-0 w-100" accept="image/*" style="opacity: 0; position: absolute; z-index: 2; cursor: pointer; height: 100%;" onchange="document.getElementById('create_image_text').innerText = this.files[0] ? this.files[0].name : '{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}'">
                                <div class="d-flex align-items-center w-100 px-2" style="position: absolute; z-index: 1;">
                                    <span class="badge bg-secondary me-2">{{ app()->getLocale() == 'ar' ? 'اختر ملف' : 'Choose File' }}</span>
                                    <span id="create_image_text" class="text-muted text-truncate" style="font-size: 0.85rem;">{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Barcode --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.barcode') }}</label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="barcode" class="pm-form-control" placeholder="{{ __('product.barcode') }}">
                        </div>
                    </div>
                    {{-- SKU --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'رمز SKU (رمز المخزون)' : 'SKU' }}</label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text"><i class="bi bi-hash"></i></span>
                            <input type="text" name="sku" class="pm-form-control" placeholder="SKU">
                        </div>
                    </div>
                    {{-- Category --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.category') }}</label>
                        <select name="category_id" class="pm-form-control">
                            <option value="">{{ __('product.select_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Section: Stock & Pricing --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-boxes"></i> {{ __('product.stock') ?? 'Stock & Pricing' }}</div>
                    </div>

                    {{-- Base Unit --}}
                    <div class="col-12 mb-3">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الوحدة الأساسية' : 'Base Unit' }} <span class="text-danger">*</span></label>
                        <div class="dropdown">
                            <button class="btn btn-sm pm-form-control dropdown-toggle w-100 text-start text-truncate" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" id="create-base-unit-btn" style="background-color: #fff; border: 1px solid #ced4da; height: 38px; display: flex; align-items: center; justify-content: space-between;">
                                {{ app()->getLocale() == 'ar' ? 'حدد الوحدة' : 'Select Unit' }}
                            </button>
                            <div class="dropdown-menu p-3 shadow w-100">
                                <div class="mb-2">
                                    <label class="form-label small mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Arabic Name' }}</label>
                                    <input type="text" name="base_unit_name_ar" class="pm-form-control form-control-sm" list="units-list" placeholder="{{ app()->getLocale() == 'ar' ? 'مثل: حبة، كرتون' : 'e.g. Bottle, Tablet' }}" oninput="updateBaseUnitLabel()">
                                </div>
                                <div>
                                    <label class="form-label small mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم بالإنجليزية' : 'English Name' }}</label>
                                    <input type="text" name="base_unit_name_en" class="pm-form-control form-control-sm" list="units-list" placeholder="{{ app()->getLocale() == 'ar' ? 'مثل: Piece, Kg' : 'e.g. Piece, Kg' }}" oninput="updateBaseUnitLabel()">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sale Price --}}
                    <div class="col-md-6 mb-3">
                        <label class="pm-form-label">{{ __('product.sale_price') }} ({{ app()->getLocale() == 'ar' ? 'للوحدة الأساسية' : 'for Base Unit' }}) <span class="text-danger">*</span></label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text fw-bold">{{ $setting->currency ?? '' }}</span>
                            <input type="number" step="0.01" name="sale_price" id="create_sale_price" class="pm-form-control" value="0.00" min="0" required>
                        </div>
                    </div>

                    {{-- Minimum Stock --}}
                    <div class="col-md-6 mb-3">
                        <label class="pm-form-label">{{ __('product.minimum_stock') }} ({{ app()->getLocale() == 'ar' ? 'للوحدة الأساسية' : 'for Base Unit' }}) <span class="text-danger">*</span></label>
                        <input type="number" name="minimum_stock" class="pm-form-control" value="0" min="0" required>
                    </div>

                    {{-- Additional Selling Units --}}
                    <div class="col-12 mt-3">
                        <div class="pm-section-label"><i class="bi bi-diagram-3-fill"></i> {{ app()->getLocale() == 'ar' ? 'وحدات البيع الإضافية (اختياري)' : 'Additional Selling Units (Optional)' }}</div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle pm-table" id="create-additional-units-table">
                                <thead>
                                    <tr class="table-light">
                                        <th>{{ app()->getLocale() == 'ar' ? 'اسم الوحدة' : 'Unit Name' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'الباركود' : 'Barcode' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'طريقة التسعير' : 'Pricing Mode' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'تحتوي على (وحدات أساسية)' : 'Contains (Base Units)' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'سعر البيع' : 'Sale Price' }}</th>
                                        <th class="text-center" style="width: 80px;">{{ app()->getLocale() == 'ar' ? 'حذف' : 'Delete' }}</th>
                                    </tr>
                                </thead>
                                <tbody id="create-additional-units-tbody">
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="pm-tb-btn btn-sm mt-2" id="create-add-selling-unit-btn">
                            <i class="bi bi-plus-lg"></i> {{ app()->getLocale() == 'ar' ? 'إضافة وحدة بيع' : 'Add Selling Unit' }}
                        </button>
                    </div>

                    {{-- Section: Warranty --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-shield-check"></i> {{ __('pos.has_warranty') ?? 'Warranty' }}</div>
                    </div>

                    {{-- Warranty toggle & period --}}
                    <div class="col-12">
                        <div class="pm-warranty-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check fs-5" style="color:var(--pm-primary)"></i>
                                    <label class="form-check-label fw-semibold mb-0" for="has_warranty" style="cursor:pointer;color:#0f172a;">
                                        {{ __('pos.has_warranty') ?? 'Has Warranty' }}
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="has_warranty" id="has_warranty" value="1" style="width:2.5em;height:1.3em;">
                                </div>
                            </div>
                            <div id="warranty_period_div" style="display:none; min-width:220px;" class="flex-shrink-0">
                                <div class="pm-input-group">
                                    <input type="number" name="warranty_period_months" class="pm-form-control" value="0" min="0" placeholder="{{ __('pos.warranty_period_months') ?? 'Warranty Period' }}">
                                    <span class="pm-input-group-text">{{ __('pos.months') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Product Status --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-toggle-on"></i> {{ app()->getLocale() == 'ar' ? 'حالة المنتج' : 'Product Status' }}</div>
                    </div>

                    {{-- Product Status Toggle --}}
                    <div class="col-12">
                        <input type="hidden" name="status" id="create_status_hidden" value="Active">
                        <div class="pm-warranty-card d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-power fs-5" style="color:var(--pm-success)"></i>
                                <div>
                                    <label class="form-check-label fw-semibold mb-0" for="create_status_toggle" style="cursor:pointer;color:#0f172a;">
                                        {{ app()->getLocale() == 'ar' ? 'المنتج نشط' : 'Product is Active' }}
                                    </label>
                                    <div id="create_status_hint" style="font-size:0.73rem;color:#10b981;margin-top:2px;font-weight:500;">
                                        {{ app()->getLocale() == 'ar' ? '✅ يظهر في نقطة البيع ويمكن بيعه' : '✅ Visible in POS and available for sale' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="create_status_toggle" checked style="width:2.5em;height:1.3em;">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── Footer ── --}}
            <div class="pm-modal-footer-premium">
                <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> {{ __('product.cancel') }}
                </button>
                <button type="submit" class="pm-btn-save">
                    <i class="bi bi-check-lg"></i> {{ __('product.save') }}
                </button>
            </div>

        </form>
    </div>
</div>


{{-- ══════════════════════════════════════
     EDIT PRODUCT MODAL
══════════════════════════════════════ --}}
<div class="modal fade pm-modal" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable pm-modal-premium">
        <form id="editProductForm" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')

            {{-- ── Premium Header ── --}}
            <div class="pm-modal-header-premium modal-header">
                <div class="pm-modal-header-glow pm-modal-header-glow-1"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium" id="editProductModalLabel">{{ __('product.edit_product') }}</h5>
                        <p class="pm-modal-sub-premium">{{ __('product.product_management') }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- ── Body ── --}}
            <div class="pm-modal-body-premium modal-body">
                <div class="row g-3">

                    {{-- Section: Basic Info --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-info-circle-fill"></i> {{ __('product.product_name') ?? 'Basic Information' }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.product_name_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="edit_name_ar" class="pm-form-control" dir="rtl">
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.product_name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="edit_name_en" class="pm-form-control" dir="ltr">
                    </div>

                    {{-- Arabic Brand --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الاسم التجاري / البراند (عربي)' : 'Brand / Trade Name (Arabic)' }}</label>
                        <input type="text" name="brand_ar" id="edit_brand_ar" class="pm-form-control" dir="rtl" placeholder="البراند أو الاسم التجاري بالعربية">
                    </div>
                    {{-- English Brand --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الاسم التجاري / البراند (إنجليزي)' : 'Brand / Trade Name (English)' }}</label>
                        <input type="text" name="brand_en" id="edit_brand_en" class="pm-form-control" dir="ltr" placeholder="Brand or Trade name in English">
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.description_ar') }}</label>
                        <textarea name="description_ar" id="edit_description_ar" class="pm-form-control" rows="2" dir="rtl"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.description_en') }}</label>
                        <textarea name="description_en" id="edit_description_en" class="pm-form-control" rows="2" dir="ltr"></textarea>
                    </div>

                    {{-- Section: Details --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-tag-fill"></i> {{ __('product.category') ?? 'Details & Classification' }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.product_image') }}</label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text"><i class="bi bi-image"></i></span>
                            <div class="pm-form-control d-flex align-items-center" style="padding: 0; background: transparent; position: relative;">
                                <input type="file" name="image" id="edit_product_image" class="pm-form-control border-0 w-100" accept="image/*" style="opacity: 0; position: absolute; z-index: 2; cursor: pointer; height: 100%;" onchange="document.getElementById('edit_image_text').innerText = this.files[0] ? this.files[0].name : '{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}'">
                                <div class="d-flex align-items-center w-100 px-2" style="position: absolute; z-index: 1;">
                                    <span class="badge bg-secondary me-2">{{ app()->getLocale() == 'ar' ? 'اختر ملف' : 'Choose File' }}</span>
                                    <span id="edit_image_text" class="text-muted text-truncate" style="font-size: 0.85rem;">{{ app()->getLocale() == 'ar' ? 'لم يتم اختيار ملف' : 'No file chosen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.category') }}</label>
                        <select name="category_id" id="edit_category_id" class="pm-form-control">
                            <option value="">{{ __('product.select_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Barcode --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ __('product.barcode') }}</label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="barcode" id="edit_barcode" class="pm-form-control" placeholder="{{ __('product.barcode') }}">
                        </div>
                    </div>
                    {{-- SKU --}}
                    <div class="col-md-6">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'رمز SKU (رمز المخزون)' : 'SKU' }}</label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text"><i class="bi bi-hash"></i></span>
                            <input type="text" name="sku" id="edit_sku" class="pm-form-control" placeholder="SKU">
                        </div>
                    </div>
                    {{-- Section: Pricing & Stock --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-boxes"></i> {{ __('product.sale_price') ?? 'Pricing & Stock' }}</div>
                    </div>

                    {{-- Base Unit --}}
                    <div class="col-12 mb-3">
                        <label class="pm-form-label">{{ app()->getLocale() == 'ar' ? 'الوحدة الأساسية' : 'Base Unit' }} <span class="text-danger">*</span></label>
                        <div class="dropdown">
                            <button class="btn btn-sm pm-form-control dropdown-toggle w-100 text-start text-truncate" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" id="edit-base-unit-btn" style="background-color: #fff; border: 1px solid #ced4da; height: 38px; display: flex; align-items: center; justify-content: space-between;">
                                {{ app()->getLocale() == 'ar' ? 'حدد الوحدة' : 'Select Unit' }}
                            </button>
                            <div class="dropdown-menu p-3 shadow w-100">
                                <div class="mb-2">
                                    <label class="form-label small mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Arabic Name' }}</label>
                                    <input type="text" id="edit_base_unit_name_ar" name="base_unit_name_ar" class="pm-form-control form-control-sm" list="units-list" placeholder="{{ app()->getLocale() == 'ar' ? 'مثل: حبة، كرتون' : 'e.g. Bottle, Tablet' }}" oninput="updateEditBaseUnitLabel()">
                                </div>
                                <div>
                                    <label class="form-label small mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم بالإنجليزية' : 'English Name' }}</label>
                                    <input type="text" id="edit_base_unit_name_en" name="base_unit_name_en" class="pm-form-control form-control-sm" list="units-list" placeholder="{{ app()->getLocale() == 'ar' ? 'مثل: Piece, Kg' : 'e.g. Piece, Kg' }}" oninput="updateEditBaseUnitLabel()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="pm-form-label">{{ __('product.sale_price') }} ({{ app()->getLocale() == 'ar' ? 'للوحدة الأساسية' : 'for Base Unit' }}) <span class="text-danger">*</span></label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text fw-bold">{{ $setting->currency ?? '' }}</span>
                            <input type="number" step="0.01" name="sale_price" id="edit_sale_price" class="pm-form-control" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="pm-form-label">{{ __('product.minimum_stock') }} ({{ app()->getLocale() == 'ar' ? 'للوحدة الأساسية' : 'for Base Unit' }}) <span class="text-danger">*</span></label>
                        <input type="number" name="minimum_stock" id="edit_minimum_stock" class="pm-form-control" min="0" required>
                    </div>

                    {{-- Additional Selling Units --}}
                    <div class="col-12 mt-3">
                        <div class="pm-section-label"><i class="bi bi-diagram-3-fill"></i> {{ app()->getLocale() == 'ar' ? 'وحدات البيع الإضافية (اختياري)' : 'Additional Selling Units (Optional)' }}</div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle pm-table" id="edit-additional-units-table">
                                <thead>
                                    <tr class="table-light">
                                        <th>{{ app()->getLocale() == 'ar' ? 'اسم الوحدة' : 'Unit Name' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'الباركود' : 'Barcode' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'طريقة التسعير' : 'Pricing Mode' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'تحتوي على (وحدات أساسية)' : 'Contains (Base Units)' }}</th>
                                        <th>{{ app()->getLocale() == 'ar' ? 'سعر البيع' : 'Sale Price' }}</th>
                                        <th class="text-center" style="width: 80px;">{{ app()->getLocale() == 'ar' ? 'حذف' : 'Delete' }}</th>
                                    </tr>
                                </thead>
                                <tbody id="edit-additional-units-tbody">
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="pm-tb-btn btn-sm mt-2" id="edit-add-selling-unit-btn">
                            <i class="bi bi-plus-lg"></i> {{ app()->getLocale() == 'ar' ? 'إضافة وحدة بيع' : 'Add Selling Unit' }}
                        </button>
                    </div>

                    {{-- Section: Warranty --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-shield-check"></i> {{ __('pos.has_warranty') ?? 'Warranty' }}</div>
                    </div>

                    <div class="col-12">
                        <div class="pm-warranty-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check fs-5" style="color:var(--pm-primary)"></i>
                                    <label class="form-check-label fw-semibold mb-0" for="edit_has_warranty" style="cursor:pointer;color:#0f172a;">
                                        {{ __('pos.has_warranty') ?? 'Has Warranty' }}
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="has_warranty" id="edit_has_warranty" value="1" style="width:2.5em;height:1.3em;">
                                </div>
                            </div>
                            <div id="edit_warranty_period_div" style="display:none; min-width:220px;" class="flex-shrink-0">
                                <div class="pm-input-group">
                                    <input type="number" name="warranty_period_months" id="edit_warranty_period_months" class="pm-form-control" min="0" placeholder="{{ __('pos.warranty_period_months') ?? 'Warranty Period' }}">
                                    <span class="pm-input-group-text">{{ __('pos.months') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Product Status --}}
                    <div class="col-12">
                        <div class="pm-section-label"><i class="bi bi-toggle-on"></i> {{ app()->getLocale() == 'ar' ? 'حالة المنتج' : 'Product Status' }}</div>
                    </div>

                    {{-- Product Status Toggle --}}
                    <div class="col-12">
                        <input type="hidden" name="status" id="edit_status" value="Active">
                        <div class="pm-warranty-card d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-power fs-5" style="color:var(--pm-success)"></i>
                                <div>
                                    <label class="form-check-label fw-semibold mb-0" for="edit_status_toggle" style="cursor:pointer;color:#0f172a;">
                                        {{ app()->getLocale() == 'ar' ? 'المنتج نشط' : 'Product is Active' }}
                                    </label>
                                    <div id="edit_status_hint" style="font-size:0.73rem;color:#10b981;margin-top:2px;font-weight:500;">
                                        {{ app()->getLocale() == 'ar' ? '✅ يظهر في نقطة البيع ويمكن بيعه' : '✅ Visible in POS and available for sale' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="edit_status_toggle" checked style="width:2.5em;height:1.3em;">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── Footer ── --}}
            <div class="pm-modal-footer-premium">
                <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> {{ __('product.cancel') }}
                </button>
                <button type="submit" class="pm-btn-save">
                    <i class="bi bi-check-lg"></i> {{ __('product.save') }}
                </button>
            </div>

        </form>
    </div>
</div>


{{-- ══════════════════════════════════════
     HISTORY / MOVEMENTS MODAL
══════════════════════════════════════ --}}
<div class="modal fade pm-modal" id="movementsModal" tabindex="-1" aria-labelledby="movementsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable pm-modal-premium">
        <div class="modal-content">

            {{-- ── Premium Header ── --}}
            <div class="pm-modal-header-premium modal-header">
                <div class="pm-modal-header-glow pm-modal-header-glow-1" style="background:rgba(6,182,212,.25);"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2" style="background:rgba(99,102,241,.18);"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium" style="color:#67e8f9;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium" id="movementsModalLabel">{{ __('product.product_history') }}</h5>
                        <p class="pm-modal-sub-premium">{{ __('product.product_management') }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="pm-modal-body-premium modal-body">
                {{-- Metadata --}}
                <div id="productMetadata" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="pm-meta-card h-100">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-plus-circle-fill text-success"></i>
                                    <span class="fw-bold" style="font-size:.85rem;color:#0f172a;">{{ __('product.created_at') }}</span>
                                </div>
                                <p class="mb-1 font-monospace" style="font-size:.83rem;color:#475569;" id="meta_created_at">—</p>
                                <small style="color:#94a3b8;">{{ __('product.created_by') }}: <span id="meta_created_by" class="fw-semibold" style="color:#475569;">—</span></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pm-meta-card h-100">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                    <span class="fw-bold" style="font-size:.85rem;color:#0f172a;">{{ __('product.updated_at') }}</span>
                                </div>
                                <p class="mb-1 font-monospace" style="font-size:.83rem;color:#475569;" id="meta_updated_at">—</p>
                                <small style="color:#94a3b8;">{{ __('product.updated_by') }}: <span id="meta_updated_by" class="fw-semibold" style="color:#475569;">—</span></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Movements Table --}}
                <div class="pm-table-wrap" style="border:1px solid var(--pm-border);border-radius:12px;overflow:hidden;">
                    <table class="pm-table" id="movementsTable" style="display:none;">
                        <thead>
                            <tr>
                                <th>{{ __('product.date') }}</th>
                                <th>{{ __('product.type') }}</th>
                                <th>{{ __('product.quantity') }}</th>
                                <th>{{ __('product.branch') }}</th>
                                <th>{{ __('product.note') }}</th>
                                <th>{{ __('product.modified_by') }}</th>
                            </tr>
                        </thead>
                        <tbody id="movementsTableBody"></tbody>
                    </table>
                </div>
                <div id="noMovementsMsg" class="pm-empty d-none">
                    <i class="bi bi-clipboard2-x"></i>
                    <p>{{ __('product.no_history') }}</p>
                </div>
            </div>

            <div class="pm-modal-footer-premium">
                <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> {{ __('product.close') }}
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     BATCHES MODAL
══════════════════════════════════════ --}}
<div class="modal fade pm-modal" id="batchesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable pm-modal-premium">
        <div class="modal-content">

            {{-- ── Premium Header ── --}}
            <div class="pm-modal-header-premium modal-header">
                <div class="pm-modal-header-glow pm-modal-header-glow-1" style="background:rgba(124,58,237,.25);"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2" style="background:rgba(99,102,241,.18);"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium" style="color:#c4b5fd;">
                        <i class="bi bi-layers-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium">{{ __('purchases.batches') }}</h5>
                        <p class="pm-modal-sub-premium">{{ __('product.product_management') }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="pm-modal-body-premium modal-body" style="padding:0;">
                <div class="pm-table-wrap">
                    <table class="pm-table">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الانتهاء' : 'Expiry' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الكمية المشتراة' : 'Purchased' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الكمية المتبقية' : 'Remaining' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'رقم التشغيلة' : 'Batch' }}</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody id="batchesTableBody"></tbody>
                        <tfoot id="batchesTotalRow" class="d-none">
                            <tr style="background:#f8fafc;border-top:2px solid var(--pm-border);">
                                <td colspan="3" class="text-end fw-bold" style="color:#94a3b8;font-size:.82rem;">{{ __('pos.total_inventory_value') }}</td>
                                <td id="batchesTotalAmount" class="fw-bold" style="color:var(--pm-success);font-size:.95rem;"></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="pm-modal-footer-premium">
                <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> {{ __('product.close') }}
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     EDIT BATCH MODAL
══════════════════════════════════════ --}}
<div class="modal fade pm-modal" id="editBatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered pm-modal-premium">
        <div class="modal-content">

            {{-- ── Premium Header ── --}}
            <div class="pm-modal-header-premium">
                <div class="pm-modal-header-glow pm-modal-header-glow-1" style="background:rgba(245,158,11,.22);"></div>
                <div class="pm-modal-header-glow pm-modal-header-glow-2" style="background:rgba(99,102,241,.18);"></div>
                <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
                    <div class="pm-modal-icon-premium" style="color:#fcd34d;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="pm-modal-title-premium">{{ __('purchases.edit_batch') }}</h5>
                        <p class="pm-modal-sub-premium">{{ __('purchases.batches') }}</p>
                    </div>
                    <button type="button" class="pm-modal-close-premium" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <form id="editBatchForm">
                @csrf
                @method('PUT')

                {{-- ── Body ── --}}
                <div class="pm-modal-body-premium modal-body">

                    <div class="pm-section-label"><i class="bi bi-tag-fill"></i> {{ __('purchases.batch_number') }}</div>

                    <div class="mb-3">
                        <label class="pm-form-label">{{ __('purchases.batch_number') }}</label>
                        <input type="text" name="batch_number" id="eb_batch_number" class="pm-form-control" required>
                    </div>

                    <div class="pm-section-label"><i class="bi bi-boxes"></i> {{ __('purchases.quantity') }} & {{ __('purchases.expiry_date') }}</div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="pm-form-label">{{ __('purchases.quantity') }}</label>
                            <input type="number" step="1" name="quantity" id="eb_quantity" class="pm-form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="pm-form-label">{{ __('purchases.expiry_date') }}</label>
                            <input type="date" name="expiry_date" id="eb_expiry_date" class="pm-form-control">
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="pm-section-label"><i class="bi bi-currency-dollar"></i> {{ __('purchases.purchase_price') }}</div>
                        <label class="pm-form-label">{{ __('purchases.purchase_price') }}</label>
                        <div class="pm-input-group">
                            <span class="pm-input-group-text fw-bold">{{ $setting->currency ?? '' }}</span>
                            <input type="number" step="0.01" name="purchase_price" id="eb_purchase_price" class="pm-form-control" required>
                        </div>
                    </div>

                </div>

                {{-- ── Footer ── --}}
                <div class="pm-modal-footer-premium">
                    <button type="button" class="pm-btn-cancel" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> {{ __('pos.cancel') }}
                    </button>
                    <button type="submit" class="pm-btn-save">
                        <i class="bi bi-check-lg"></i> {{ __('pos.save') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     IMAGE PREVIEW MODAL
══════════════════════════════════════ --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(12px); background: rgba(15, 23, 42, 0.4);">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
            <!-- Floating Header/Close -->
            <div class="d-flex justify-content-between align-items-center mb-2 px-2" style="position: absolute; top: -45px; left: 0; right: 0; z-index: 10;">
                <h5 class="text-white fw-bold m-0" id="imagePreviewTitle" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5); font-size: 1.1rem;"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="background-color: rgba(255,255,255,0.1); border-radius: 50%; padding: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); backdrop-filter: blur(4px); transition: all 0.2s;"></button>
            </div>
            <div class="modal-body p-0 text-center d-flex align-items-center justify-content-center" style="border-radius: 16px; overflow: hidden; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3); background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px);">
                <img id="previewImageSrc" src="" class="img-fluid" style="max-height: 80vh; width: 100%; object-fit: contain; vertical-align: middle; border-radius: 16px;">
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ── Side Drawer Dark / Light Mode Support ── */
    #productDetailDrawer {
        background-color: var(--pm-surface) !important;
        color: var(--pm-text-1) !important;
        border-left: 1px solid var(--pm-border) !important;
    }
    #productDetailDrawer .offcanvas-header {
        background: var(--pm-surface-2) !important;
        border-bottom: 1px solid var(--pm-border) !important;
        color: var(--pm-text-1) !important;
    }
    #productDetailDrawer .offcanvas-header .fs-5 {
        color: var(--pm-text-1) !important;
    }
    #productDetailDrawer .offcanvas-body {
        background-color: var(--pm-surface) !important;
        color: var(--pm-text-2) !important;
    }
    #productDetailDrawer #drawer_description {
        background-color: var(--pm-surface-2) !important;
        border-color: var(--pm-border) !important;
        color: var(--pm-text-2) !important;
    }
    #productDetailDrawer .table-responsive {
        border-color: var(--pm-border) !important;
    }
    #productDetailDrawer .table th {
        background-color: var(--pm-surface-2) !important;
        color: var(--pm-text-1) !important;
        border-color: var(--pm-border) !important;
    }
    #productDetailDrawer .table td {
        color: var(--pm-text-2) !important;
        border-color: var(--pm-border) !important;
    }
    #productDetailDrawer #drawer_image_placeholder {
        background-color: var(--pm-surface-2) !important;
        color: var(--pm-text-muted) !important;
    }
    #productDetailDrawer #drawer_warranty_container {
        border-color: var(--pm-border) !important;
        background-color: var(--pm-surface-2) !important;
    }
    
    /* Close Button theme support */
    [data-pm-theme="dark"] #productDetailDrawer .btn-close {
        filter: invert(1) grayscale(1) brightness(2);
    }
    
    /* Text muted override for dark mode readability */
    [data-pm-theme="dark"] #productDetailDrawer .text-muted {
        color: var(--pm-text-muted, #94a3b8) !important;
    }

    /* Make ID and audit metadata text white in dark mode */
    [data-pm-theme="dark"] .pm-product-id,
    [data-pm-theme="dark"] #productDetailDrawer .drawer-meta-section,
    [data-pm-theme="dark"] #productDetailDrawer .drawer-meta-section span,
    [data-pm-theme="dark"] #productDetailDrawer .drawer-meta-section i {
        color: #ffffff !important;
    }

    /* Premium Table Dark Mode Enhancements */
    [data-pm-theme="dark"] .product-detail-trigger {
        color: var(--pm-text-1) !important;
    }
    
    .pm-barcode-text {
        color: var(--pm-text-3);
    }
    [data-pm-theme="dark"] .pm-barcode-text {
        color: var(--pm-text-2) !important;
    }
    [data-pm-theme="dark"] .pm-barcode-text i {
        color: var(--pm-text-muted) !important;
    }

    .pm-expiry-date-text {
        color: var(--pm-text-2);
    }
    [data-pm-theme="dark"] .pm-expiry-date-text {
        color: var(--pm-text-1) !important;
    }

    /* Interactive detail-badge style representing drawer action */
    .detail-badge {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 7px;
        border-radius: 4px;
        background: rgba(59, 130, 246, 0.08) !important;
        color: var(--pm-primary, #3b82f6) !important;
        border: 1px solid rgba(59, 130, 246, 0.15) !important;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 4px;
        vertical-align: middle;
    }
    .detail-badge:hover {
        background: var(--pm-primary, #3b82f6) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
    }
    [data-pm-theme="dark"] .detail-badge {
        background: rgba(96, 165, 250, 0.12) !important;
        color: #60a5fa !important;
        border-color: rgba(96, 165, 250, 0.25) !important;
    }
    [data-pm-theme="dark"] .detail-badge:hover {
        background: #60a5fa !important;
        color: #0b1427 !important;
        box-shadow: 0 4px 10px rgba(96, 165, 250, 0.3);
    }
    [data-pm-theme="dark"] #drawer_barcode_badge {
        background: rgba(148, 163, 184, 0.12) !important;
        color: #cbd5e1 !important;
        border-color: rgba(148, 163, 184, 0.25) !important;
    }
    [data-pm-theme="dark"] #drawer_sku_badge {
        background: rgba(34, 211, 238, 0.12) !important;
        color: #22d3ee !important;
        border-color: rgba(34, 211, 238, 0.25) !important;
    }
</style>
@endpush

{{-- ══════════════════════════════════════
     PRODUCT DETAILS DRAWER (OFFCANVAS)
══════════════════════════════════════ --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="productDetailDrawer" aria-labelledby="productDetailDrawerLabel" style="width: 450px; border-left: 1px solid rgba(0,0,0,0.08); box-shadow: -5px 0 25px rgba(0,0,0,0.05); z-index: 1060;">
    <div class="offcanvas-header border-bottom py-3" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <div class="d-flex align-items-center gap-2">
            <span class="fs-5 fw-bold text-dark" id="drawer_product_name">Product Details</span>
            <span class="badge" id="drawer_product_id" style="font-size: 0.8rem; background-color: #475569 !important; color: #ffffff !important; font-weight: 600; padding: 4px 8px; border-radius: 4px;">#ID</span>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4" style="background: #ffffff; overflow-y: auto;">
        
        <!-- Image & Main Info -->
        <div class="text-center mb-4">
            <div id="drawer_image_container" class="mb-3 d-inline-block position-relative" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 3px solid #fff;">
                <img id="drawer_product_image" src="" alt="Product Image" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                <div id="drawer_image_placeholder" style="width: 100%; height: 100%; background: #f8fafc; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #cbd5e1;">
                    <i class="bi bi-image"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                <span class="badge bg-primary px-3 py-2" id="drawer_category" style="font-size: 0.82rem; font-weight: 500; background: rgba(59, 130, 246, 0.08) !important; color: #2563eb !important; border: 1px solid rgba(59, 130, 246, 0.15); border-radius: 6px;">Category</span>
                <span class="badge bg-purple px-3 py-2" id="drawer_brand" style="font-size: 0.82rem; font-weight: 500; background: rgba(139, 92, 246, 0.08) !important; color: #7c3aed !important; border: 1px solid rgba(139, 92, 246, 0.15); border-radius: 6px; display: none;">Brand</span>
            </div>
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mt-2">
                <span class="badge bg-secondary px-3 py-2" id="drawer_barcode_badge" style="font-size: 0.82rem; font-weight: 500; background: rgba(71, 85, 105, 0.08) !important; color: #475569 !important; border: 1px solid rgba(71, 85, 105, 0.15); border-radius: 6px; display: none;"><i class="bi bi-upc-scan me-1"></i><span id="drawer_barcode"></span></span>
                <span class="badge bg-info px-3 py-2" id="drawer_sku_badge" style="font-size: 0.82rem; font-weight: 500; background: rgba(8, 145, 178, 0.08) !important; color: #0891b2 !important; border: 1px solid rgba(8, 145, 178, 0.15); border-radius: 6px; display: none;"><i class="bi bi-hash me-1"></i>SKU: <span id="drawer_sku"></span></span>
            </div>
        </div>

        <hr class="my-4" style="border-top: 1px dashed #e2e8f0;">

        <!-- Description Section -->
        <div class="mb-4" id="drawer_desc_section" style="display: none;">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-justify-left me-2 text-primary"></i>{{ app()->getLocale() == 'ar' ? 'الوصف' : 'Description' }}</h6>
            <p class="text-secondary" id="drawer_description" style="font-size: 0.88rem; line-height: 1.6; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #f1f5f9; white-space: pre-line;"></p>
        </div>



        <!-- Additional Selling Units Section -->
        <div class="mb-4" id="drawer_additional_units_section" style="display: none;">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-3 me-2 text-primary"></i>{{ app()->getLocale() == 'ar' ? 'وحدات البيع الإضافية' : 'Additional Selling Units' }}</h6>
            <div class="table-responsive border rounded-3">
                <table class="table table-sm align-middle mb-0" style="font-size: 0.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th>{{ app()->getLocale() == 'ar' ? 'الرمز' : 'Unit' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'يحتوي' : 'Contains' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'السعر' : 'Price' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الباركود' : 'Barcode' }}</th>
                        </tr>
                    </thead>
                    <tbody id="drawer_units_table_body">
                        <!-- populated via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Warranty Section -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-check me-2 text-primary"></i>{{ app()->getLocale() == 'ar' ? 'الضمان' : 'Warranty' }}</h6>
            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between" id="drawer_warranty_container" style="border-color: #f1f5f9 !important;">
                <span class="text-muted" style="font-size: 0.85rem;">{{ app()->getLocale() == 'ar' ? 'فترة الضمان للمنتج' : 'Warranty details' }}</span>
                <span class="badge" id="drawer_warranty_status" style="font-size: 0.82rem;">No Warranty</span>
            </div>
        </div>

        <!-- Product Status Section -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-toggle-on me-2 text-primary"></i>{{ app()->getLocale() == 'ar' ? 'حالة المنتج' : 'Product Status' }}</h6>
            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between" id="drawer_status_container" style="border-color: #f1f5f9 !important;">
                <span class="text-muted" style="font-size: 0.85rem;">{{ app()->getLocale() == 'ar' ? 'هل المنتج متاح للبيع؟' : 'Is this product available for sale?' }}</span>
                <span id="drawer_status_badge" style="font-size: 0.82rem;">—</span>
            </div>
        </div>

        <!-- History / Movements Button in Drawer -->
        <div class="mb-4">
            <button type="button" class="btn btn-outline-primary w-100 btn-sm d-flex align-items-center justify-content-center gap-2" id="drawer_history_btn" style="border-radius: 6px; padding: 8px;">
                <i class="bi bi-clock-history"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'سجل حركة المخزون (كارت الصنف)' : 'Stock Movement History' }}</span>
            </button>
        </div>


        <hr class="my-4" style="border-top: 1px dashed #e2e8f0;">

        <!-- Metadata Section -->
        <div class="text-muted drawer-meta-section" style="font-size: 0.72rem; line-height: 1.6;">
            <div><i class="bi bi-person-plus me-1"></i>{{ app()->getLocale() == 'ar' ? 'تمت الإضافة بواسطة: ' : 'Created by: ' }} <span id="drawer_created_by" class="fw-semibold"></span></div>
            <div><i class="bi bi-pencil me-1"></i>{{ app()->getLocale() == 'ar' ? 'آخر تحديث بواسطة: ' : 'Last updated by: ' }} <span id="drawer_updated_by" class="fw-semibold"></span></div>
            <div><i class="bi bi-calendar-check me-1"></i>{{ app()->getLocale() == 'ar' ? 'تاريخ الإضافة: ' : 'Created at: ' }} <span id="drawer_created_at" class="fw-semibold"></span></div>
        </div>

    </div>
</div>

<datalist id="units-list">
    @foreach($units as $unit)
        <option value="{{ $unit }}"></option>
    @endforeach
</datalist>

@endsection


@push('scripts')
<script>
$(document).ready(function() {
    // Move modals to body to prevent stacking context and backdrop freeze issues
    $('.modal').appendTo('body');

    // ── DataTable init (hidden controls — we use custom toolbar) ──
    var table = $('#productsTable').DataTable({
        language: {
            url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
        },
        responsive: false,
        order: [],
        dom: '<"pm-dt-wrapper"<"pm-dt-inner"t><"row pm-dt-footer align-items-center"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>>',
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [0] },  // checkbox column
            { className: 'dt-body-center', targets: [0] },
        ],
    });

    // Wire up custom search input
    $('#pm-search-input').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Wire up custom length select
    $('#pm-length-select').on('change', function() {
        table.page.len(parseInt(this.value)).draw();
    });

    // ── Create Modal: Product Status Toggle — event listener only ──
    $('#create_status_toggle').on('change', function() {
        updateCreateStatusToggle($(this).is(':checked'));
    });

    // ── Edit Modal: Product Status Toggle — event listener only ──
    $('#edit_status_toggle').on('change', function() {
        updateEditStatusToggle($(this).is(':checked'));
    });

    // Add row to Create modal units table
    $('#create-add-selling-unit-btn').on('click', function() {
        let idx = $('#create-additional-units-tbody tr').length;
        $('#create-additional-units-tbody').append(`
            <tr>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm pm-form-control dropdown-toggle w-100 text-start text-truncate" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" id="unit-name-btn-${idx}" style="min-width: 160px; background-color: #fff; border: 1px solid #ced4da;">
                            ${'{{ app()->getLocale() }}' === 'ar' ? 'حدد الوحدة' : 'Select Unit'}
                        </button>
                        <div class="dropdown-menu p-3 shadow" style="min-width: 280px;">
                            <div class="mb-2">
                                <label class="form-label small mb-1">${'{{ app()->getLocale() }}' === 'ar' ? 'الاسم بالعربية' : 'Arabic Name'}</label>
                                <input type="text" name="additional_units[${idx}][unit_name_ar]" class="pm-form-control form-control-sm" list="units-list" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: صندوق، كرتون' : 'e.g. Pack, Carton'}" oninput="updateUnitNameLabel(${idx})">
                            </div>
                            <div>
                                <label class="form-label small mb-1">${'{{ app()->getLocale() }}' === 'ar' ? 'الاسم بالإنجليزية' : 'English Name'}</label>
                                <input type="text" name="additional_units[${idx}][unit_name_en]" class="pm-form-control form-control-sm" list="units-list" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: Pack, Carton' : 'e.g. Pack, Carton'}" oninput="updateUnitNameLabel(${idx})">
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="text" name="additional_units[${idx}][barcode]" class="pm-form-control form-control-sm" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'الباركود' : 'Barcode'}" style="min-width: 100px;">
                </td>
                <td>
                    <select name="additional_units[${idx}][pricing_mode]" class="pm-form-control form-control-sm pricing-mode-select" style="min-width: 100px;">
                        <option value="custom">${'{{ app()->getLocale() }}' === 'ar' ? 'مخصص' : 'Custom'}</option>
                        <option value="automatic">${'{{ app()->getLocale() }}' === 'ar' ? 'تلقائي' : 'Automatic'}</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.0001" name="additional_units[${idx}][conversion_factor]" class="pm-form-control form-control-sm conversion-factor-input" required min="0.0001" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: 6، 24' : 'e.g. 6, 24'}" style="min-width: 100px;">
                </td>
                <td>
                    <input type="number" step="0.01" name="additional_units[${idx}][sale_price]" class="pm-form-control form-control-sm sale-price-input" required min="0" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: 2800' : 'e.g. 2800'}" style="min-width: 100px;">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `);
    });

    // Add row to Edit modal units table
    $('#edit-add-selling-unit-btn').on('click', function() {
        let idx = $('#edit-additional-units-tbody tr').length;
        $('#edit-additional-units-tbody').append(`
            <tr>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm pm-form-control dropdown-toggle w-100 text-start text-truncate" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" id="edit-unit-name-btn-${idx}" style="min-width: 160px; background-color: #fff; border: 1px solid #ced4da;">
                            ${'{{ app()->getLocale() }}' === 'ar' ? 'حدد الوحدة' : 'Select Unit'}
                        </button>
                        <div class="dropdown-menu p-3 shadow" style="min-width: 280px;">
                            <div class="mb-2">
                                <label class="form-label small mb-1">${'{{ app()->getLocale() }}' === 'ar' ? 'الاسم بالعربية' : 'Arabic Name'}</label>
                                <input type="text" name="additional_units[${idx}][unit_name_ar]" class="pm-form-control form-control-sm" list="units-list" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: صندوق، كرتون' : 'e.g. Pack, Carton'}" oninput="updateEditUnitNameLabel(${idx})">
                            </div>
                            <div>
                                <label class="form-label small mb-1">${'{{ app()->getLocale() }}' === 'ar' ? 'الاسم بالإنجليزية' : 'English Name'}</label>
                                <input type="text" name="additional_units[${idx}][unit_name_en]" class="pm-form-control form-control-sm" list="units-list" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: Pack, Carton' : 'e.g. Pack, Carton'}" oninput="updateEditUnitNameLabel(${idx})">
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="text" name="additional_units[${idx}][barcode]" class="pm-form-control form-control-sm" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'الباركود' : 'Barcode'}" style="min-width: 100px;">
                </td>
                <td>
                    <select name="additional_units[${idx}][pricing_mode]" class="pm-form-control form-control-sm pricing-mode-select" style="min-width: 100px;">
                        <option value="custom">${'{{ app()->getLocale() }}' === 'ar' ? 'مخصص' : 'Custom'}</option>
                        <option value="automatic">${'{{ app()->getLocale() }}' === 'ar' ? 'تلقائي' : 'Automatic'}</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.0001" name="additional_units[${idx}][conversion_factor]" class="pm-form-control form-control-sm conversion-factor-input" required min="0.0001" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: 6، 24' : 'e.g. 6, 24'}" style="min-width: 100px;">
                </td>
                <td>
                    <input type="number" step="0.01" name="additional_units[${idx}][sale_price]" class="pm-form-control form-control-sm sale-price-input" required min="0" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: 2800' : 'e.g. 2800'}" style="min-width: 100px;">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `);
    });

    // Delete row from units table
    $(document).on('click', '.delete-row-btn', function() {
        let tbody = $(this).closest('tbody');
        $(this).closest('tr').remove();
        // Re-index names to prevent array holes on submission
        tbody.find('tr').each(function(idx, tr) {
            $(tr).find('input, select').each(function(_, input) {
                let nameAttr = $(input).attr('name');
                if (nameAttr) {
                    let newName = nameAttr.replace(/additional_units\[\d+\]/, 'additional_units[' + idx + ']');
                    $(input).attr('name', newName);
                }
            });
        });
    });

    // Helper function to update unit price in a row
    function updateRowUnitPrice(tr) {
        let pricingMode = tr.find('.pricing-mode-select').val();
        let priceInput = tr.find('.sale-price-input');
        
        if (pricingMode === 'automatic') {
            // Find base sale price
            // Check if this row is in edit or create table
            let isEdit = tr.closest('tbody').attr('id') === 'edit-additional-units-tbody';
            let basePriceInput = isEdit ? $('#edit_sale_price') : $('#create_sale_price');
            let basePrice = parseFloat(basePriceInput.val()) || 0;
            
            let factor = parseFloat(tr.find('.conversion-factor-input').val()) || 0;
            let calculatedPrice = (basePrice * factor).toFixed(2);
            
            priceInput.val(calculatedPrice);
            priceInput.attr('readonly', true);
        } else {
            priceInput.removeAttr('readonly');
        }
    }

    // Event listeners for pricing mode change
    $(document).on('change', '.pricing-mode-select', function() {
        let tr = $(this).closest('tr');
        updateRowUnitPrice(tr);
    });

    // Event listeners for conversion factor input change
    $(document).on('input change', '.conversion-factor-input', function() {
        let tr = $(this).closest('tr');
        updateRowUnitPrice(tr);
    });

    // Event listeners for base unit price change
    $(document).on('input change', '#create_sale_price, #edit_sale_price', function() {
        let isEdit = $(this).attr('id') === 'edit_sale_price';
        let tbodyId = isEdit ? '#edit-additional-units-tbody' : '#create-additional-units-tbody';
        $(tbodyId + ' tr').each(function() {
            updateRowUnitPrice($(this));
        });
    });

    // Form validations to prevent duplicate unit names and validate conversion factor > 0
    $(document).on('submit', 'form', function(e) {
        let form = $(this);
        let baseUnitInputAr = form.find('input[name="base_unit_name_ar"]');
        let baseUnitInputEn = form.find('input[name="base_unit_name_en"]');
        if (baseUnitInputAr.length === 0 && baseUnitInputEn.length === 0) return; // Not product form

        let baseUnitNameAr = (baseUnitInputAr.val() || '').trim().toLowerCase();
        let baseUnitNameEn = (baseUnitInputEn.val() || '').trim().toLowerCase();
        
        let additionalUnitNamesAr = [];
        let additionalUnitNamesEn = [];
        let hasDuplicate = false;
        let isSameAsBase = false;
        let isInvalidFactor = false;

        form.find('tbody[id$="additional-units-tbody"] tr').each(function() {
            let nameArInput = $(this).find('input[name*="[unit_name_ar]"]');
            let nameEnInput = $(this).find('input[name*="[unit_name_en]"]');
            if (nameArInput.length === 0 && nameEnInput.length === 0) return;

            let nameAr = (nameArInput.val() || '').trim().toLowerCase();
            let nameEn = (nameEnInput.val() || '').trim().toLowerCase();
            let factor = parseFloat($(this).find('.conversion-factor-input').val());

            if ((baseUnitNameAr && nameAr === baseUnitNameAr) || 
                (baseUnitNameEn && nameEn === baseUnitNameEn)) {
                isSameAsBase = true;
            }
            if ((nameAr && additionalUnitNamesAr.includes(nameAr)) || 
                (nameEn && additionalUnitNamesEn.includes(nameEn))) {
                hasDuplicate = true;
            }
            if (isNaN(factor) || factor <= 0) {
                isInvalidFactor = true;
            }
            if (nameAr) additionalUnitNamesAr.push(nameAr);
            if (nameEn) additionalUnitNamesEn.push(nameEn);
        });

        if (isSameAsBase) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ app()->getLocale() == "ar" ? "لا يمكن لوحدة البيع الإضافية أن تكون نفس الوحدة الأساسية." : "Additional selling unit cannot be the same as the base unit." }}'
            });
            return;
        }

        if (hasDuplicate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ app()->getLocale() == "ar" ? "لا يمكن تكرار وحدات البيع الإضافية للمنتج نفسه." : "Prevent duplicate unit rows for the same product." }}'
            });
            return;
        }

        if (isInvalidFactor) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ app()->getLocale() == "ar" ? "يجب أن يكون عامل التحويل أكبر من 0." : "Validate that conversion factor is greater than 0." }}'
            });
            return;
        }
    });

    // Click handler for Edit Product
    $(document).on('click', '.edit-product-btn', function() {
        let btn = $(this);
        let id = btn.data('id');
        let name_ar = btn.attr('data-name-ar');
        let name_en = btn.attr('data-name-en');
        let brand_ar = btn.attr('data-brand-ar');
        let brand_en = btn.attr('data-brand-en');
        let desc_ar = btn.attr('data-description-ar');
        let desc_en = btn.attr('data-description-en');
        let s_price = btn.data('sale-price');
        let min_stock = btn.data('minimum-stock');
        let category_id = btn.data('category-id');
        let has_warranty = btn.data('has-warranty') == 1;
        let warranty_months = btn.data('warranty-months');
        let base_unit_ar = btn.attr('data-base-unit-ar');
        let base_unit_en = btn.attr('data-base-unit-en');
        let unitsJson = btn.attr('data-units');
        let barcode = btn.attr('data-barcode');
        let sku = btn.attr('data-sku');
        let status = btn.attr('data-status') || 'Active';

        editProduct(id, name_ar, name_en, brand_ar, brand_en, desc_ar, desc_en, s_price, min_stock, category_id, has_warranty, warranty_months, base_unit_ar, base_unit_en, unitsJson, barcode, sku, status);
    });

    // ── Select All checkbox ──
    $('#pm-check-all').on('change', function() {
        let checked = $(this).is(':checked');
        $('.pm-product-check').prop('checked', checked);
        $('.pm-product-check').closest('tr').toggleClass('pm-selected', checked);
        updateBulkBar();
    });

    // ── Individual row checkbox ──
    $(document).on('change', '.pm-product-check', function() {
        $(this).closest('tr').toggleClass('pm-selected', $(this).is(':checked'));
        let total = $('.pm-product-check').length;
        let checked = $('.pm-product-check:checked').length;
        $('#pm-check-all').prop('indeterminate', checked > 0 && checked < total);
        $('#pm-check-all').prop('checked', checked === total && total > 0);
        updateBulkBar();
    });

    function updateBulkBar() {
        let count = $('.pm-product-check:checked').length;
        $('#pm-bulk-count').text(count);
        if (count > 0) {
            $('#pm-bulk-bar').addClass('active');
        } else {
            $('#pm-bulk-bar').removeClass('active');
        }
    }

    // Click handler for Image Preview
    $(document).on('click', '.preview-product-img', function() {
        let btn = $(this);
        let src = btn.data('src');
        let name = btn.attr('data-name');
        previewImage(src, name);
    });

    // Click handler for viewing Product Details in Side Drawer (Offcanvas)
    $(document).on('click', '.product-detail-trigger', function() {
        let trigger = $(this);
        let id = trigger.attr('data-id');
        let locale = "{{ app()->getLocale() }}";
        
        let name = locale === 'ar' ? trigger.attr('data-name-ar') : trigger.attr('data-name-en');
        let brand = locale === 'ar' ? trigger.attr('data-brand-ar') : trigger.attr('data-brand-en');
        let desc = locale === 'ar' ? trigger.attr('data-description-ar') : trigger.attr('data-description-en');
        let category = trigger.attr('data-category');
        let image = trigger.attr('data-image');
        let salePrice = trigger.attr('data-sale-price');
        let minStock = trigger.attr('data-minimum-stock');
        let curStock = trigger.attr('data-current-stock');
        let baseUnit = trigger.attr('data-base-unit');
        let hasWarranty = trigger.attr('data-has-warranty') == 1;
        let warrantyMonths = trigger.attr('data-warranty-months');
        let unitsJson = trigger.attr('data-units');
        let barcode = trigger.attr('data-barcode');
        let sku = trigger.attr('data-sku');
        let createdBy = trigger.attr('data-created-by');
        let updatedBy = trigger.attr('data-updated-by');
        let createdAt = trigger.attr('data-created-at');
        let productStatus = trigger.attr('data-status') || 'Active';

        // Populate Drawer fields
        $('#drawer_product_name').text(name || 'Product Details');
        $('#drawer_product_id').text('#' + id);
        
        // Image
        if (image) {
            $('#drawer_product_image').attr('src', image).show();
            $('#drawer_image_placeholder').hide();
        } else {
            $('#drawer_product_image').hide();
            $('#drawer_image_placeholder').show();
        }

        // Category
        if (category) {
            $('#drawer_category').text(category).show();
        } else {
            $('#drawer_category').hide();
        }

        // Brand
        if (brand) {
            $('#drawer_brand').text(brand).show();
        } else {
            $('#drawer_brand').hide();
        }

        // Barcode badge
        if (barcode) {
            $('#drawer_barcode').text(barcode);
            $('#drawer_barcode_badge').show();
        } else {
            $('#drawer_barcode_badge').hide();
        }

        // SKU badge
        if (sku) {
            $('#drawer_sku').text(sku);
            $('#drawer_sku_badge').show();
        } else {
            $('#drawer_sku_badge').hide();
        }

        // Description
        if (desc) {
            $('#drawer_description').text(desc);
            $('#drawer_desc_section').show();
        } else {
            $('#drawer_description').text('');
            $('#drawer_desc_section').hide();
        }



        // Additional Selling Units
        let tbody = $('#drawer_units_table_body');
        tbody.empty();
        let units = [];
        try {
            units = JSON.parse(unitsJson || '[]');
        } catch(e) {
            console.error(e);
        }

        if (units.length > 0) {
            units.forEach(function(unit) {
                let pMode = unit.pricing_mode === 'automatic' 
                    ? (locale === 'ar' ? 'تلقائي' : 'Auto') 
                    : (locale === 'ar' ? 'مخصص' : 'Custom');
                tbody.append(`
                    <tr>
                        <td><strong>${unit.unit_name}</strong></td>
                        <td>${parseFloat(unit.conversion_factor)} ${baseUnit}</td>
                        <td>${parseFloat(unit.sale_price).toFixed(2)} ${pMode !== 'Custom' ? ' (' + pMode + ')' : ''}</td>
                        <td class="text-muted">${unit.barcode || '-'}</td>
                    </tr>
                `);
            });
            $('#drawer_additional_units_section').show();
        } else {
            $('#drawer_additional_units_section').hide();
        }

        // Warranty
        if (hasWarranty) {
            let monthsText = locale === 'ar' 
                ? `${warrantyMonths} شهر` 
                : `${warrantyMonths} months`;
            $('#drawer_warranty_status')
                .text(monthsText)
                .removeClass('bg-secondary text-dark')
                .addClass('bg-success text-white');
        } else {
            $('#drawer_warranty_status')
                .text(locale === 'ar' ? 'بدون ضمان' : 'No Warranty')
                .removeClass('bg-success text-white')
                .addClass('bg-secondary text-dark');
        }

        // System details
        $('#drawer_created_by').text(createdBy || '-');
        $('#drawer_updated_by').text(updatedBy || '-');
        $('#drawer_created_at').text(createdAt || '-');

        // Product Status badge in drawer
        let locale2 = "{{ app()->getLocale() }}";
        if (productStatus === 'Active') {
            $('#drawer_status_badge')
                .html('<span style="background:rgba(16,185,129,0.12);color:#10b981;border:1px solid rgba(16,185,129,0.25);padding:3px 10px;border-radius:6px;font-size:.78rem;font-weight:700;">&#x1F7E2; ' + (locale2 === 'ar' ? 'نشط' : 'Active') + '</span>');
        } else {
            $('#drawer_status_badge')
                .html('<span style="background:rgba(244,63,94,0.12);color:#f43f5e;border:1px solid rgba(244,63,94,0.25);padding:3px 10px;border-radius:6px;font-size:.78rem;font-weight:700;">&#x1F534; ' + (locale2 === 'ar' ? 'غير نشط' : 'Inactive') + '</span>');
        }

        // Store active ID on the history button inside drawer
        $('#drawer_history_btn').data('id', id);

        // Show Offcanvas Drawer
        let drawerEl = document.getElementById('productDetailDrawer');
        let offcanvas = bootstrap.Offcanvas.getInstance(drawerEl);
        if (!offcanvas) {
            offcanvas = new bootstrap.Offcanvas(drawerEl);
        }
        offcanvas.show();
    });

    // Click handler for history button in Side Drawer
    $(document).on('click', '#drawer_history_btn', function() {
        let id = $(this).data('id');
        if (id) {
            // Close the offcanvas cleanly first
            let drawerEl = document.getElementById('productDetailDrawer');
            let offcanvas = bootstrap.Offcanvas.getInstance(drawerEl);
            if (offcanvas) {
                offcanvas.hide();
            }
            // Trigger movements history modal
            viewMovements(id);
        }
    });

    // Click handler for Edit Batch
    $(document).on('click', '.edit-batch-btn', function() {
        let btn = $(this);
        let id = btn.data('id');
        let number = btn.attr('data-number');
        let qty = btn.data('qty');
        let expiry = btn.attr('data-expiry');
        let price = btn.data('price');
        editBatch(id, number, qty, expiry, price);
    });
});

// ── Helper: safely open a modal (prevents backdrop stacking & focus-trap loss) ──
function safeShowModal(id) {
    // 1. Hide every currently-open modal cleanly (no animation wait needed)
    document.querySelectorAll('.modal.show').forEach(function(openEl) {
        var inst = bootstrap.Modal.getInstance(openEl);
        if (inst) { inst.hide(); }
    });

    // 2. Nuke any orphaned backdrops that Bootstrap may have left behind
    document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });

    // 3. Restore body scroll state
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('padding-left');
    document.body.style.removeProperty('overflow');

    // 4. Get a fresh instance and show it
    var el = document.getElementById(id);
    if (!el) return;
    // Dispose any stale instance so Bootstrap re-initialises focus trap
    var stale = bootstrap.Modal.getInstance(el);
    if (stale) { stale.dispose(); }
    var instance = new bootstrap.Modal(el, { backdrop: true, keyboard: true, focus: true });
    instance.show();
}

// ── Edit Product ──
function editProduct(id, name_ar, name_en, brand_ar, brand_en, desc_ar, desc_en, s_price, min_stock, category_id, has_warranty, warranty_months, base_unit_ar, base_unit_en, unitsJson, barcode, sku, status) {
    $('#edit_name_ar').val(name_ar);
    $('#edit_name_en').val(name_en);
    $('#edit_brand_ar').val(brand_ar);
    $('#edit_brand_en').val(brand_en);
    $('#edit_description_ar').val(desc_ar);
    $('#edit_description_en').val(desc_en);
    $('#edit_sale_price').val(s_price);
    $('#edit_minimum_stock').val(min_stock);
    $('#edit_category_id').val(category_id);
    $('#edit_base_unit_name_ar').val(base_unit_ar || '');
    $('#edit_base_unit_name_en').val(base_unit_en || '');
    updateEditBaseUnitLabel();
    $('#edit_barcode').val(barcode || '');
    $('#edit_sku').val(sku || '');

    // Set status toggle
    let isActive = (status || 'Active') === 'Active';
    $('#edit_status_toggle').prop('checked', isActive);
    updateEditStatusToggle(isActive);

    $('#edit_has_warranty').prop('checked', has_warranty);
    $('#edit_warranty_period_months').val(warranty_months);
    if (has_warranty) {
        $('#edit_warranty_period_div').show();
    } else {
        $('#edit_warranty_period_div').hide();
    }

    // Populate additional selling units
    let tbody = $('#edit-additional-units-tbody');
    tbody.empty();
    try {
        let units = JSON.parse(unitsJson || '[]');
        units.forEach(function(unit, idx) {
            let mode = unit.pricing_mode || 'custom';
            let isCustom = (mode === 'custom') ? 'selected' : '';
            let isAuto = (mode === 'automatic') ? 'selected' : '';
            let isReadonly = (mode === 'automatic') ? 'readonly' : '';

            let arName = unit.unit_name_ar || '';
            let enName = unit.unit_name_en || '';
            let btnLabel = '${{ app()->getLocale() === "ar" ? "حدد الوحدة" : "Select Unit" }}';
            if (arName && enName) btnLabel = `${enName} / ${arName}`;
            else if (arName) btnLabel = arName;
            else if (enName) btnLabel = enName;
            else if (unit.unit_name) btnLabel = unit.unit_name;

            tbody.append(`
                <tr>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm pm-form-control dropdown-toggle w-100 text-start text-truncate" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" id="edit-unit-name-btn-${idx}" style="min-width: 160px; background-color: #fff; border: 1px solid #ced4da;">
                                ${btnLabel}
                            </button>
                            <div class="dropdown-menu p-3 shadow" style="min-width: 280px;">
                                <div class="mb-2">
                                    <label class="form-label small mb-1">${'{{ app()->getLocale() }}' === 'ar' ? 'الاسم بالعربية' : 'Arabic Name'}</label>
                                    <input type="text" name="additional_units[${idx}][unit_name_ar]" class="pm-form-control form-control-sm" list="units-list" value="${arName}" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: صندوق، كرتون' : 'e.g. Pack, Carton'}" oninput="updateEditUnitNameLabel(${idx})">
                                </div>
                                <div>
                                    <label class="form-label small mb-1">${'{{ app()->getLocale() }}' === 'ar' ? 'الاسم بالإنجليزية' : 'English Name'}</label>
                                    <input type="text" name="additional_units[${idx}][unit_name_en]" class="pm-form-control form-control-sm" list="units-list" value="${enName}" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: Pack, Carton' : 'e.g. Pack, Carton'}" oninput="updateEditUnitNameLabel(${idx})">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="additional_units[${idx}][barcode]" class="pm-form-control form-control-sm" value="${unit.barcode || ''}" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'الباركود' : 'Barcode'}" style="min-width: 100px;">
                    </td>
                    <td>
                        <select name="additional_units[${idx}][pricing_mode]" class="pm-form-control form-control-sm pricing-mode-select" style="min-width: 100px;">
                            <option value="custom" ${isCustom}>${'{{ app()->getLocale() }}' === 'ar' ? 'مخصص' : 'Custom'}</option>
                            <option value="automatic" ${isAuto}>${'{{ app()->getLocale() }}' === 'ar' ? 'تلقائي' : 'Automatic'}</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.0001" name="additional_units[${idx}][conversion_factor]" class="pm-form-control form-control-sm conversion-factor-input" required min="0.0001" value="${unit.conversion_factor}" placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: 6، 24' : 'e.g. 6, 24'}" style="min-width: 100px;">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="additional_units[${idx}][sale_price]" class="pm-form-control form-control-sm sale-price-input" required min="0" value="${unit.sale_price}" ${isReadonly} placeholder="${'{{ app()->getLocale() }}' === 'ar' ? 'مثل: 2800' : 'e.g. 2800'}" style="min-width: 100px;">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `);
        });
    } catch(e) {
        console.error('Failed to parse units JSON', e);
    }

    let url = "{{ route('products.update', ':id') }}".replace(':id', id);
    $('#editProductForm').attr('action', url);

    safeShowModal('editProductModal');
}
// ── Global: Update Unit Name Label ──
function updateUnitNameLabel(idx) {
    let arInput = document.querySelector(`#addProductModal input[name="additional_units[${idx}][unit_name_ar]"]`);
    let enInput = document.querySelector(`#addProductModal input[name="additional_units[${idx}][unit_name_en]"]`);
    let btn = document.getElementById(`unit-name-btn-${idx}`);
    if(!btn || !arInput || !enInput) return;
    let arVal = arInput.value.trim();
    let enVal = enInput.value.trim();
    if (arVal && enVal) btn.textContent = `${enVal} / ${arVal}`;
    else if (arVal) btn.textContent = arVal;
    else if (enVal) btn.textContent = enVal;
    else btn.textContent = '{{ app()->getLocale() === "ar" ? "حدد الوحدة" : "Select Unit" }}';
}

function updateEditUnitNameLabel(idx) {
    let arInput = document.querySelector(`#editProductModal input[name="additional_units[${idx}][unit_name_ar]"]`);
    let enInput = document.querySelector(`#editProductModal input[name="additional_units[${idx}][unit_name_en]"]`);
    let btn = document.getElementById(`edit-unit-name-btn-${idx}`);
    if(!btn || !arInput || !enInput) return;
    let arVal = arInput.value.trim();
    let enVal = enInput.value.trim();
    if (arVal && enVal) btn.textContent = `${enVal} / ${arVal}`;
    else if (arVal) btn.textContent = arVal;
    else if (enVal) btn.textContent = enVal;
    else btn.textContent = '{{ app()->getLocale() === "ar" ? "حدد الوحدة" : "Select Unit" }}';
}

function updateBaseUnitLabel() {
    let arInput = document.querySelector(`#addProductModal input[name="base_unit_name_ar"]`);
    let enInput = document.querySelector(`#addProductModal input[name="base_unit_name_en"]`);
    let btn = document.getElementById(`create-base-unit-btn`);
    if(!btn || !arInput || !enInput) return;
    let arVal = arInput.value.trim();
    let enVal = enInput.value.trim();
    if (arVal && enVal) btn.textContent = `${enVal} / ${arVal}`;
    else if (arVal) btn.textContent = arVal;
    else if (enVal) btn.textContent = enVal;
    else btn.textContent = '{{ app()->getLocale() === "ar" ? "حدد الوحدة" : "Select Unit" }}';
}

function updateEditBaseUnitLabel() {
    let arInput = document.querySelector(`#editProductModal input[name="base_unit_name_ar"]`);
    let enInput = document.querySelector(`#editProductModal input[name="base_unit_name_en"]`);
    let btn = document.getElementById(`edit-base-unit-btn`);
    if(!btn || !arInput || !enInput) return;
    let arVal = arInput.value.trim();
    let enVal = enInput.value.trim();
    if (arVal && enVal) btn.textContent = `${enVal} / ${arVal}`;
    else if (arVal) btn.textContent = arVal;
    else if (enVal) btn.textContent = enVal;
    else btn.textContent = '{{ app()->getLocale() === "ar" ? "حدد الوحدة" : "Select Unit" }}';
}

// ── Global: Update Create Status Toggle UI ──
function updateCreateStatusToggle(isActive) {
    var locale = "{{ app()->getLocale() }}";
    var label = isActive
        ? (locale === 'ar' ? 'المنتج نشط' : 'Product is Active')
        : (locale === 'ar' ? 'المنتج غير نشط' : 'Product is Inactive');
    var hint = isActive
        ? (locale === 'ar' ? '✅ يظهر في نقطة البيع ويمكن بيعه' : '✅ Visible in POS and available for sale')
        : (locale === 'ar' ? '🚫 مخفي في نقطة البيع ولا يمكن بيعه' : '🚫 Hidden from POS and cannot be sold');
    $('label[for="create_status_toggle"]').text(label);
    $('#create_status_hint').text(hint).css('color', isActive ? '#10b981' : '#f43f5e');
    $('#create_status_toggle').closest('.pm-warranty-card').find('.bi-power').css('color', isActive ? 'var(--pm-success)' : 'var(--pm-danger)');
    $('#create_status_hidden').val(isActive ? 'Active' : 'Inactive');
}

// ── Global: Update Edit Status Toggle UI ──
function updateEditStatusToggle(isActive) {
    var locale = "{{ app()->getLocale() }}";
    var label = isActive
        ? (locale === 'ar' ? 'المنتج نشط' : 'Product is Active')
        : (locale === 'ar' ? 'المنتج غير نشط' : 'Product is Inactive');
    var hint = isActive
        ? (locale === 'ar' ? '✅ يظهر في نقطة البيع ويمكن بيعه' : '✅ Visible in POS and available for sale')
        : (locale === 'ar' ? '🚫 مخفي في نقطة البيع ولا يمكن بيعه' : '🚫 Hidden from POS and cannot be sold');
    $('label[for="edit_status_toggle"]').text(label);
    $('#edit_status_hint').text(hint).css('color', isActive ? '#10b981' : '#f43f5e');
    $('#edit_status_toggle').closest('.pm-warranty-card').find('.bi-power').css('color', isActive ? 'var(--pm-success)' : 'var(--pm-danger)');
    $('#edit_status').val(isActive ? 'Active' : 'Inactive');
}

// ── Image Preview ──
function previewImage(url, name) {
    $('#previewImageSrc').attr('src', url);
    $('#imagePreviewTitle').text(name);
    safeShowModal('imagePreviewModal');
}

// ── Toggle Product Status Directly From Table ──
function toggleProductStatus(id, isActive, element) {
    var status = isActive ? 'Active' : 'Inactive';
    var locale = "{{ app()->getLocale() }}";
    
    // Optimistically update tooltip
    $(element).closest('.d-flex').attr('title', isActive ? (locale === 'ar' ? 'نشط' : 'Active') : (locale === 'ar' ? 'غير نشط' : 'Inactive'));
    
    // Update data-search so searching still works
    $(element).closest('td').attr('data-search', 'status-' + status.toLowerCase());

    // Update label text and color
    var labelSpan = $(element).closest('.d-flex').find('.status-label');
    labelSpan.text(isActive ? (locale === 'ar' ? 'نشط' : 'Active') : (locale === 'ar' ? 'غير نشط' : 'Inactive'));
    labelSpan.css('color', isActive ? '#10b981' : '#f43f5e');

    $.ajax({
        url: '{{ route('products.bulk_status') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            ids: [id],
            status: status
        },
        success: function(res) {
            if (res.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: locale === 'ar' ? 'تم التحديث بنجاح' : 'Status updated successfully',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            } else {
                element.checked = !isActive;
            }
        },
        error: function() {
            element.checked = !isActive;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: locale === 'ar' ? 'حدث خطأ!' : 'Error occurred!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    });
}

// ── View Batches ──
function viewBatches(id) {
    $('#batchesTableBody').html(
        '<tr><td colspan="6" class="pm-spinner-cell" style="padding: 30px; text-align: center;">' +
        '<div class="spinner-border spinner-border-sm text-primary me-2"></div>' +
        '<span style="color:#94a3b8;font-size:.85rem;">Loading…</span></td></tr>'
    );
    $('#batchesTotalRow').addClass('d-none');

    $.get("/products/batches/" + id, function(batches) {
        let rows = '';
        let totalAmount = 0;
        let isAr = $('html').attr('lang') === 'ar';

        if (batches.length > 0) {
            batches.forEach(function(batch) {
                let expiry  = '-';
                let statusHtml = '';
                let rowClass   = 'pm-batch-row';

                if (batch.expiry_date) {
                    let expDate  = new Date(batch.expiry_date);
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    let day = String(expDate.getDate()).padStart(2, '0');
                    let month = monthNames[expDate.getMonth()];
                    let year = expDate.getFullYear();
                    expiry = `${month}-${year}-${day}`;

                    let today    = new Date();
                    let diffDays = (expDate - today) / (1000 * 60 * 60 * 24);

                    if (expDate < today) {
                        let statusText = isAr ? 'منتهي الصلاحية' : 'Expired';
                        statusHtml = `<span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25); padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 6px;"><i class="bi bi-x-octagon-fill" style="font-size: 0.82rem;"></i>${statusText}</span>`;
                        rowClass   = 'pm-batch-expired';
                    } else if (diffDays < 30) {
                        let statusText = isAr ? 'قرب ينتهي' : 'Expiring Soon';
                        statusHtml = `<span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25); padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 6px;"><i class="bi bi-exclamation-triangle-fill" style="font-size: 0.82rem;"></i>${statusText}</span>`;
                        rowClass   = 'pm-batch-expiring';
                    } else {
                        let statusText = isAr ? 'صالح' : 'Good';
                        statusHtml = `<span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 6px;"><i class="bi bi-check2-circle" style="font-size: 0.82rem;"></i>${statusText}</span>`;
                    }
                } else {
                    let statusText = isAr ? 'صالح' : 'Good';
                    statusHtml = `<span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 6px;"><i class="bi bi-check2-circle" style="font-size: 0.82rem;"></i>${statusText}</span>`;
                }

                // Purchased column display with highlighted unit badge
                let purchasedQty = batch.original_quantity_display ? parseFloat(batch.original_quantity_display) : parseFloat(batch.quantity);
                let purchasedQtyRounded = Math.round(purchasedQty);
                let rawPurchasedUnit = batch.purchase_unit_name ? batch.purchase_unit_name : 'Piece';
                let rawBaseUnit = batch.base_unit_name ? batch.base_unit_name : 'Piece';
                
                let purchasedUnit = rawPurchasedUnit;
                let baseUnit = rawBaseUnit;
                
                @if(app()->getLocale() == 'ar')
                const unitDict = {'pices':'حبة', 'piece':'حبة', 'psc':'حبة', 'tape':'شريط', 'tabe':'شريط', 'box':'صندوق', 'carton':'كرتون', 'kg':'كجم', 'g':'جم'};
                if(unitDict[purchasedUnit.toLowerCase().trim()]) purchasedUnit = unitDict[purchasedUnit.toLowerCase().trim()];
                if(unitDict[baseUnit.toLowerCase().trim()]) baseUnit = unitDict[baseUnit.toLowerCase().trim()];
                @endif

                let purchasedHtml = `<span style="font-weight: 700; color: var(--pm-text-1);">${purchasedQtyRounded}</span> ` +
                    `<span class="badge" style="background: var(--pm-primary-soft); color: var(--pm-primary); border: 1px solid rgba(59, 130, 246, 0.25); padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.3px; margin-inline-start: 6px; box-shadow: var(--pm-shadow-xs); display: inline-block; vertical-align: middle;"><i class="bi bi-box me-1" style="font-size: 0.72rem; color: var(--pm-primary);"></i>${purchasedUnit}</span>`;

                let remainingQty = parseFloat(batch.quantity);
                let remainingHtml = `<span style="font-weight: 700; color: var(--pm-text-1);">${Math.round(remainingQty)}</span> ` +
                    `<span class="badge" style="background: var(--pm-success-soft); color: var(--pm-success); border: 1px solid rgba(16, 185, 129, 0.25); padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.3px; margin-inline-start: 6px; box-shadow: var(--pm-shadow-xs); display: inline-block; vertical-align: middle;"><i class="bi bi-tag me-1" style="font-size: 0.72rem; color: var(--pm-success);"></i>${baseUnit}</span>`;

                let amount   = parseFloat(batch.quantity) * parseFloat(batch.purchase_price);
                totalAmount += amount;

                rows += `<tr class="${rowClass} align-middle">
                    <td>${statusHtml}</td>
                    <td><span style="font-family:monospace; font-weight:600; color: var(--pm-text-2);">${expiry}</span></td>
                    <td>${purchasedHtml}</td>
                    <td style="font-weight:700; color: var(--pm-text-1);">${remainingHtml}</td>
                    <td><span style="font-family:monospace; font-weight: 700; color: var(--pm-info); background: var(--pm-info-soft); border: 1px solid rgba(6, 182, 212, 0.25); padding: 4px 10px; border-radius: 6px; font-size: 0.82rem;">${batch.batch_number}</span></td>
                    <td class="text-end">
                        <button type="button"
                            class="pm-batch-btn edit-batch-btn"
                            style="background: var(--pm-surface-2); color:#d97706; border-color:rgba(217,119,6,0.3);"
                            data-id="${batch.id}"
                            data-number="${batch.batch_number}"
                            data-qty="${batch.quantity}"
                            data-expiry="${batch.expiry_date ? batch.expiry_date.substring(0,10) : ''}"
                            data-price="${batch.purchase_price}">
                            <i class="bi bi-pencil" style="font-size:.8rem;"></i>
                        </button>
                    </td>
                </tr>`;
            });
        } else {
            rows = '<tr><td colspan="6" class="pm-empty" style="padding: 40px; text-align: center; color: #94a3b8;"><i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>{{ __("purchases.no_batches_found") }}</td></tr>';
        }

        $('#batchesTableBody').html(rows);

        if (totalAmount > 0) {
            let currency = '{{ $setting->currency ?? "" }}';
            $('#batchesTotalAmount').text(parseFloat(totalAmount.toFixed(2)) + ' ' + currency);
            $('#batchesTotalRow tr td:first-child').attr('colspan', '3');
            $('#batchesTotalRow').removeClass('d-none');
        }

        safeShowModal('batchesModal');
    }).fail(function() {
        Swal.fire('Error', 'Failed to load batches', 'error');
    });
}

// ── Edit Batch ──
function editBatch(id, number, qty, expiry, p_price) {
    $('#eb_batch_number').val(number);
    $('#eb_quantity').val(qty);
    $('#eb_expiry_date').val(expiry);
    $('#eb_purchase_price').val(p_price);

    // Close batchesModal then open editBatchModal
    var batchesModalEl = document.getElementById('batchesModal');
    var bsInstance = bootstrap.Modal.getInstance(batchesModalEl);

    function openEditBatch() {
        // Clean up any leftover backdrops/body state
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        document.body.style.removeProperty('padding-left');
        document.body.style.removeProperty('overflow');

        var editBatchModalEl = document.getElementById('editBatchModal');
        // Dispose stale instance so focus-trap reinitialises
        var staleEb = bootstrap.Modal.getInstance(editBatchModalEl);
        if (staleEb) { staleEb.dispose(); }
        var ebModal = new bootstrap.Modal(editBatchModalEl, { backdrop: true, keyboard: true, focus: true });

        $('#editBatchForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "/products/batches/" + id,
                type: "PUT",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        ebModal.hide();
                        Toast.fire({ icon: 'success', title: response.message });
                        location.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to update batch', 'error');
                }
            });
        });

        ebModal.show();
    }

    if (bsInstance) {
        batchesModalEl.addEventListener('hidden.bs.modal', openEditBatch, { once: true });
        bsInstance.hide();
    } else {
        openEditBatch();
    }
}

// ── View Movements / History ──
function viewMovements(id) {
    $('#movementsTableBody').empty();
    $('#noMovementsMsg').addClass('d-none');
    $('#movementsTable').hide();

    $('#meta_created_at').text('—');
    $('#meta_created_by').text('—');
    $('#meta_updated_at').text('—');
    $('#meta_updated_by').text('—');

    $.ajax({
        url: "/products/" + id + "/movements",
        type: "GET",
        success: function(response) {
            if (response.metadata) {
                $('#meta_created_at').text(response.metadata.created_at);
                $('#meta_created_by').text(response.metadata.created_by);
                $('#meta_updated_at').text(response.metadata.updated_at);
                $('#meta_updated_by').text(response.metadata.updated_by);
            }

            if (response.movements && response.movements.length > 0) {
                let rows = '';
                response.movements.forEach(function(mov) {
                    let creatorName = mov.creator ? mov.creator.username : 'Unknown';
                    let branchName  = mov.branch ? (mov.branch.name.ar || mov.branch.name.en || mov.branch.name) : '—';
                    let date        = new Date(mov.created_at).toLocaleString();
                    let typeBadge   = mov.type === 'in' ? 'pm-badge-success' : 'pm-badge-danger';

                    rows += `<tr>
                        <td style="font-size:.82rem;color:#64748b;">${date}</td>
                        <td><span class="pm-badge ${typeBadge}">${mov.type.toUpperCase()}</span></td>
                        <td style="font-weight:700;color:#0f172a;">${parseFloat(mov.quantity)}</td>
                        <td style="color:#475569;">${branchName}</td>
                        <td style="color:#94a3b8;font-size:.82rem;">${mov.note || '—'}</td>
                        <td style="color:#475569;font-size:.82rem;">${creatorName}</td>
                    </tr>`;
                });
                $('#movementsTableBody').html(rows);
                $('#movementsTable').show();
            } else {
                $('#noMovementsMsg').removeClass('d-none');
            }

            safeShowModal('movementsModal');
        },
        error: function() {
            alert("Error fetching movements");
        }
    });
}

// ── Warranty toggle — Create modal ──
$('#has_warranty').on('change', function() {
    $('#warranty_period_div').toggle($(this).is(':checked'));
});

// ── Warranty toggle — Edit modal ──
$('#edit_has_warranty').on('change', function() {
    $('#edit_warranty_period_div').toggle($(this).is(':checked'));
});

// ══════════════════════════════════════
// DIGITAL AGE — Dark / Light Mode
// ══════════════════════════════════════
const PM_THEME_KEY = 'da_app_theme';
const isAr = document.documentElement.dir === 'rtl';

const themeLabels = {
    dark:  { ar: 'الوضع الفاتح',  en: 'Light Mode' },
    light: { ar: 'الوضع الداكن', en: 'Dark Mode' }
};

function applyPmTheme(theme) {
    const root = document.getElementById('pm-page-root') || document.body;
    // Apply to the content area and its parent context
    const contentEl = document.getElementById('content') || document.body;

    // Set attribute on multiple anchors so CSS selectors work
    root.setAttribute('data-pm-theme', theme);
    contentEl.setAttribute('data-pm-theme', theme);
    document.documentElement.setAttribute('data-pm-theme', theme);

    localStorage.setItem(PM_THEME_KEY, theme);

    const icon  = document.getElementById('pmThemeIcon');
    const label = document.getElementById('pmThemeLabel');

    if (icon)  icon.className = theme === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill text-primary';
    if (label) label.textContent = isAr ? themeLabels[theme].ar : themeLabels[theme].en;
}

function togglePmTheme() {
    toggleGlobalTheme();
}

// Load saved theme on page load
(function() {
    const saved = localStorage.getItem(PM_THEME_KEY) || 'light';
    applyPmTheme(saved);
})();

// ══════════════════════════════════════
// Export Table as CSV
// ══════════════════════════════════════
function exportTable() {
    var table = $('#productsTable').DataTable();
    var nodes = table.rows({ search: 'applied' }).nodes();
    var headers = [];
    $('#productsTable thead th').each(function() {
        headers.push('"' + $(this).text().trim() + '"');
    });

    var rows = [headers.join(',')];
    for (var i = 0; i < nodes.length; i++) {
        var cells = [];
        $(nodes[i]).find('td').each(function() {
            var txt = $(this).text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
            cells.push('"' + txt + '"');
        });
        rows.push(cells.join(','));
    }

    var csv = rows.join('\n');
    var blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'Digital_Age_Products_' + new Date().toISOString().slice(0,10) + '.csv';
    link.click();
}

// ══════════════════════════════════════
// Bulk Status Change
// ══════════════════════════════════════
function bulkChangeStatus(newStatus) {
    let ids = [];
    $('.pm-product-check:checked').each(function() {
        ids.push($(this).val());
    });

    if (ids.length === 0) return;

    let locale = "{{ app()->getLocale() }}";
    let label = newStatus === 'Active'
        ? (locale === 'ar' ? 'تفعيل' : 'Activate')
        : (locale === 'ar' ? 'تعطيل' : 'Deactivate');

    Swal.fire({
        title: locale === 'ar' ? 'تأكيد العملية' : 'Confirm Action',
        text: locale === 'ar'
            ? `هل تريد ${label} (${ids.length}) منتج؟`
            : `${label} ${ids.length} product(s)?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: label,
        cancelButtonText: locale === 'ar' ? 'إلغاء' : 'Cancel',
        confirmButtonColor: newStatus === 'Active' ? '#10b981' : '#f43f5e',
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("products.bulk_status") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids,
                    status: newStatus,
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: locale === 'ar' ? 'تم بنجاح' : 'Done',
                            text: response.message || (locale === 'ar' ? 'تم تحديث الحالة' : 'Status updated'),
                            timer: 1800,
                            showConfirmButton: false
                        }).then(function() { location.reload(); });
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to update status', 'error');
                }
            });
        }
    });
}

// ══════════════════════════════════════
// Clear All Selections
// ══════════════════════════════════════
function clearSelection() {
    $('.pm-product-check').prop('checked', false);
    $('.pm-product-check').closest('tr').removeClass('pm-selected');
    $('#pm-check-all').prop('checked', false).prop('indeterminate', false);
    $('#pm-bulk-bar').removeClass('active');
    $('#pm-bulk-count').text('0');
}

// ══════════════════════════════════════
// Status Filter (page reload with param)
// ══════════════════════════════════════
function applyStatusFilter(status) {
    var table = $('#productsTable').DataTable();
    if (status) {
        table.column(8).search('status-' + status.toLowerCase()).draw();
    } else {
        table.column(8).search('').draw();
    }
}
</script>
@endpush

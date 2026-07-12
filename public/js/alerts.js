class NotificationSystem {
    constructor() {
        this.container = document.getElementById('notification-container');
        this.allNotifications = { today: [], yesterday: [], last_7_days: [], older: [] };
        this.unreadCount = 0;
        this.panelOpen = false;
        this.detailPanelOpen = false;
        this.isMuted = localStorage.getItem('alerts_muted') === 'true';
        this.audioCtx = null;
        this.knownIds = new Set();
        this.isFirstLoad = true;
        this.activeFilter = null; // 'low_stock', 'out_of_stock', 'expiring_soon', 'expired'
        this.selectedNotification = null;

        // Determine locale and labels
        this.isRtl = document.documentElement.dir === 'rtl';
        this.locale = document.documentElement.lang || 'en';
        
        this.labels = {
            title: this.isRtl ? 'تنبيهات المخزون' : 'Inventory Alerts',
            markAllRead: this.isRtl ? 'تعيين الكل كمقروء' : 'Mark all read',
            noNotifications: this.isRtl ? 'لا توجد تنبيهات جديدة' : 'All caught up!',
            noNotificationsDesc: this.isRtl ? 'تنبيهات المخزون الهامة ستظهر هنا.' : 'Critical inventory alerts will appear here.',
            today: this.isRtl ? 'اليوم' : 'Today',
            yesterday: this.isRtl ? 'أمس' : 'Yesterday',
            last7Days: this.isRtl ? 'آخر 7 أيام' : 'Last 7 Days',
            older: this.isRtl ? 'أقدم' : 'Older',
            markRead: this.isRtl ? 'تحديد كمقروء' : 'Mark as read',
            markUnread: this.isRtl ? 'تحديد كغير مقروء' : 'Mark as unread',
            viewDetails: this.isRtl ? 'عرض التفاصيل' : 'View details',
            back: this.isRtl ? 'رجوع' : 'Back',
            viewProduct: this.isRtl ? 'عرض المنتج' : 'View Product',
            viewBatch: this.isRtl ? 'عرض الدفعة' : 'View Batch',
            createAdjustment: this.isRtl ? 'عمل تسوية' : 'Create Adjustment',
            dismiss: this.isRtl ? 'تجاهل' : 'Dismiss',
            dismissAll: this.isRtl ? 'تجاهل الكل' : 'Dismiss All',
            types: {
                low_stock: this.isRtl ? 'نقص المخزون' : 'Low Stock',
                out_of_stock: this.isRtl ? 'نفد من المخزون' : 'Out of Stock',
                expiring_soon: this.isRtl ? 'يقترب من الانتهاء' : 'Expiring Soon',
                expired: this.isRtl ? 'منتهي الصلاحية' : 'Expired'
            }
        };

        this.injectStyles();
        this.buildPanel();
        this.bindEvents();
        this.fetchNotifications();
        this.initAudioUnlock();

        // Polling refresh every 30 seconds
        setInterval(() => this.fetchNotificationsSilent(), 30000);
    }

    /* ───────────── Inject SaaS 2026 CSS Styles ───────────── */
    injectStyles() {
        const styleId = 'notification-system-styles';
        if (document.getElementById(styleId)) return;

        const css = `
            :root {
                --ntf-bg: #ffffff;
                --ntf-header-bg: #f8fafc;
                --ntf-border: #e2e8f0;
                --ntf-border-soft: #f1f5f9;
                --ntf-text-main: #0f172a;
                --ntf-text-muted: #64748b;
                --ntf-hover: #f8fafc;
                --ntf-active-pill-bg: #4f46e5;
                --ntf-active-pill-text: #ffffff;
                --ntf-inactive-pill-bg: #f1f5f9;
                --ntf-inactive-pill-text: #475569;
                --ntf-unread-bg: rgba(79, 70, 229, 0.03);
                --ntf-unread-dot: #4f46e5;
                --ntf-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
                --ntf-overlay: rgba(15, 23, 42, 0.3);
                --ntf-transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }

            html[data-app-theme="dark"] {
                --ntf-bg: #0b1329;
                --ntf-header-bg: #111a36;
                --ntf-border: #1e293b;
                --ntf-border-soft: #16223f;
                --ntf-text-main: #f8fafc;
                --ntf-text-muted: #94a3b8;
                --ntf-hover: #16223f;
                --ntf-active-pill-bg: #6366f1;
                --ntf-active-pill-text: #ffffff;
                --ntf-inactive-pill-bg: #1e293b;
                --ntf-inactive-pill-text: #cbd5e1;
                --ntf-unread-bg: rgba(99, 102, 241, 0.04);
                --ntf-unread-dot: #818cf8;
                --ntf-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
                --ntf-overlay: rgba(0, 0, 0, 0.5);
            }

            /* Custom Switch Toggle */
            .ntf-switch-wrapper {
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .ntf-switch {
                position: relative;
                display: inline-block;
                width: 34px;
                height: 18px;
                margin: 0;
            }

            .ntf-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .ntf-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #cbd5e1;
                transition: .3s;
                border-radius: 18px;
            }

            .ntf-slider:before {
                position: absolute;
                content: "";
                height: 12px;
                width: 12px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .3s;
                border-radius: 50%;
                box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            }

            .ntf-switch input:checked + .ntf-slider {
                background-color: #4f46e5;
            }

            html[data-app-theme="dark"] .ntf-switch input:checked + .ntf-slider {
                background-color: #6366f1;
            }

            .ntf-switch input:checked + .ntf-slider:before {
                transform: translateX(16px);
            }

            #alerts-panel, #alerts-detail-panel {
                position: fixed;
                top: 0;
                bottom: 0;
                ${this.isRtl ? 'left' : 'right'}: 0;
                width: 400px;
                max-width: 92vw;
                height: 100vh !important;
                max-height: 100vh !important;
                background: var(--ntf-bg);
                color: var(--ntf-text-main);
                box-shadow: var(--ntf-shadow);
                z-index: 10500;
                display: flex;
                flex-direction: column;
                transform: translateX(${this.isRtl ? '-110%' : '110%'});
                transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
                border-${this.isRtl ? 'right' : 'left'}: 1px solid var(--ntf-border);
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                overflow: hidden; /* Lock stretching */
            }

            #alerts-detail-panel {
                z-index: 10502;
            }

            #alerts-overlay {
                position: fixed;
                inset: 0;
                background: var(--ntf-overlay);
                backdrop-filter: blur(4px);
                z-index: 10499;
                display: none;
                animation: ntf-fade-in 0.25s ease-out;
            }

            @keyframes ntf-fade-in {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            /* Quick Summary Cards Grid */
            .ntf-summary-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                padding: 12px 16px;
                border-bottom: 1px solid var(--ntf-border-soft);
                background: var(--ntf-bg);
                flex-shrink: 0;
            }

            .ntf-summary-card {
                padding: 8px 10px;
                border-radius: 10px;
                background: var(--ntf-header-bg);
                border: 1px solid var(--ntf-border-soft);
                display: flex;
                align-items: center;
                justify-content: space-between;
                cursor: pointer;
                transition: var(--ntf-transition);
            }

            .ntf-summary-card:hover {
                background: var(--ntf-hover);
                border-color: var(--ntf-border);
            }

            .ntf-summary-card.active {
                background: var(--ntf-active-pill-bg);
                color: var(--ntf-active-pill-text);
                border-color: var(--ntf-active-pill-bg);
            }

            .ntf-summary-card.active .num, 
            .ntf-summary-card.active .label {
                color: #ffffff !important;
            }

            .ntf-summary-card .label {
                font-size: 0.72rem;
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .ntf-summary-card .num {
                font-size: 0.95rem;
                font-weight: 700;
            }

            /* Notification List Items */
            .ntf-item {
                display: flex;
                gap: 10px;
                padding: 10px 14px;
                border-bottom: 1px solid var(--ntf-border-soft);
                position: relative;
                cursor: pointer;
                transition: var(--ntf-transition);
                align-items: center; /* Vertically center content */
                background: var(--ntf-bg);
            }

            .ntf-item:hover {
                background: var(--ntf-hover);
            }

            .ntf-item.unread {
                background: var(--ntf-unread-bg);
            }

            /* Inline Unread Dot */
            .ntf-inline-dot {
                display: inline-block;
                width: 6px;
                height: 6px;
                background-color: var(--ntf-unread-dot);
                border-radius: 50%;
                margin-${this.isRtl ? 'left' : 'right'}: 6px;
                vertical-align: middle;
                animation: ntf-pulse 2s infinite;
            }

            @keyframes ntf-pulse {
                0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
                70% { box-shadow: 0 0 0 6px rgba(79, 70, 229, 0); }
                100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
            }

            /* Small Icon Container */
            .ntf-mini-icon {
                width: 28px;
                height: 28px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.95rem;
                flex-shrink: 0;
                background: var(--ntf-header-bg);
                border: 1px solid var(--ntf-border-soft);
                transition: var(--ntf-transition);
            }

            /* Content & Typography */
            .ntf-info-area {
                flex-grow: 1;
                min-width: 0;
                padding-${this.isRtl ? 'left' : 'right'}: 20px;
            }

            .ntf-item-title {
                font-size: 0.78rem;
                font-weight: 600;
                color: var(--ntf-text-main);
                line-height: 1.25;
                margin-bottom: 0px;
                display: block;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .ntf-item-desc {
                font-size: 0.72rem;
                color: var(--ntf-text-muted);
                line-height: 1.35;
                margin-bottom: 0px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .ntf-meta-row {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .ntf-time-text {
                font-size: 0.68rem;
                color: var(--ntf-text-muted);
            }

            /* Priority Badges */
            .ntf-prio-badge {
                font-size: 0.62rem;
                font-weight: 700;
                padding: 1px 6px;
                border-radius: 4px;
                text-transform: uppercase;
                letter-spacing: 0.2px;
            }

            .ntf-prio-critical {
                background: rgba(239, 68, 68, 0.1);
                color: #ef4444;
            }

            .ntf-prio-important {
                background: rgba(245, 158, 11, 0.1);
                color: #f59e0b;
            }

            /* Context Menu Options */
            .ntf-item-options {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                ${this.isRtl ? 'left' : 'right'}: 10px;
                z-index: 10;
            }

            .ntf-options-trigger {
                background: none;
                border: none;
                color: var(--ntf-text-muted);
                padding: 2px 6px;
                border-radius: 4px;
                cursor: pointer;
                transition: var(--ntf-transition);
                font-size: 0.85rem;
            }

            .ntf-options-trigger:hover {
                background: var(--ntf-border-soft);
                color: var(--ntf-text-main);
            }

            /* Date Headers */
            .ntf-date-group {
                font-size: 0.68rem;
                font-weight: 700;
                text-transform: uppercase;
                color: var(--ntf-text-muted);
                letter-spacing: 0.5px;
                padding: 14px 16px 6px 16px;
                display: flex;
                align-items: center;
                gap: 8px;
                background: var(--ntf-bg);
            }
            .ntf-date-group::after {
                content: '';
                flex: 1;
                height: 1px;
                background: var(--ntf-border-soft);
            }

            /* Details Drawer styles */
            .ntf-detail-header {
                padding: 14px 16px;
                background: var(--ntf-header-bg);
                border-bottom: 1px solid var(--ntf-border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-shrink: 0;
            }

            .ntf-detail-body {
                flex-grow: 1;
                overflow-y: auto;
                padding: 24px;
                min-height: 0;
            }

            .ntf-detail-footer {
                padding: 16px 24px;
                background: var(--ntf-header-bg);
                border-top: 1px solid var(--ntf-border);
                display: flex;
                flex-direction: column;
                gap: 8px;
                flex-shrink: 0;
            }

            .ntf-btn-cta {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 10px 16px;
                font-size: 0.8rem;
                font-weight: 600;
                border-radius: 8px;
                text-decoration: none !important;
                border: none;
                cursor: pointer;
                transition: var(--ntf-transition);
                width: 100%;
            }

            .ntf-btn-cta-primary {
                background: #4f46e5;
                color: #ffffff !important;
            }
            .ntf-btn-cta-primary:hover {
                background: #4338ca;
            }
            
            .ntf-btn-cta-secondary {
                background: var(--ntf-inactive-pill-bg);
                color: var(--ntf-inactive-pill-text) !important;
                border: 1px solid var(--ntf-border);
            }
            .ntf-btn-cta-secondary:hover {
                background: var(--ntf-hover);
            }

            /* Scrollbar and body constraint */
            #alerts-panel-body {
                flex-grow: 1;
                overflow-y: auto;
                min-height: 0;
            }
            
            .ntf-scroller::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }
            .ntf-scroller::-webkit-scrollbar-track {
                background: transparent;
            }
            .ntf-scroller::-webkit-scrollbar-thumb {
                background: var(--ntf-border);
                border-radius: 99px;
            }
            .ntf-scroller::-webkit-scrollbar-thumb:hover {
                background: var(--ntf-text-muted);
            }
        `;

        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = css;
        document.head.appendChild(style);
    }

    /* ───────────── Audio Context Unlock & Chime ───────────── */
    initAudioUnlock() {
        const unlock = () => {
            if (!this.audioCtx) {
                this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (this.audioCtx.state === 'suspended') {
                this.audioCtx.resume();
            }
            window.removeEventListener('click', unlock);
            window.removeEventListener('keydown', unlock);
            window.removeEventListener('touchstart', unlock);
        };
        window.addEventListener('click', unlock);
        window.addEventListener('keydown', unlock);
        window.addEventListener('touchstart', unlock);
    }

    playSound(type) {
        if (this.isMuted) return;
        
        try {
            if (!this.audioCtx) {
                this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (this.audioCtx.state === 'suspended') {
                this.audioCtx.resume();
            }

            const profiles = {
                alert:   { freq: [880, 660, 880], duration: 0.14, gain: 0.35, type: 'square'   },
                warning: { freq: [520, 440],       duration: 0.18, gain: 0.25, type: 'sawtooth' },
                success: { freq: [440, 550, 660],  duration: 0.11, gain: 0.18, type: 'sine'     },
                info:    { freq: [587.33, 659.25],  duration: 0.11, gain: 0.18, type: 'sine'     }
            };

            const p = profiles[type] || profiles.info;
            let time = this.audioCtx.currentTime;

            p.freq.forEach(freq => {
                const osc = this.audioCtx.createOscillator(), g = this.audioCtx.createGain();
                osc.connect(g); 
                g.connect(this.audioCtx.destination);
                
                osc.type = p.type;
                osc.frequency.setValueAtTime(freq, time);
                g.gain.setValueAtTime(p.gain, time);
                g.gain.exponentialRampToValueAtTime(0.001, time + p.duration);
                
                osc.start(time); 
                osc.stop(time + p.duration);
                time += p.duration * 0.55;
            });
        } catch(e) {
            console.error('Notification sound error:', e);
        }
    }

    toggleMute() {
        this.isMuted = !this.isMuted;
        localStorage.setItem('alerts_muted', this.isMuted);
        
        const switchEl = document.getElementById('mute-alerts-switch');
        if (switchEl) {
            switchEl.checked = !this.isMuted;
            const titleVal = this.isMuted ? (this.isRtl ? 'تفعيل الصوت' : 'Unmute') : (this.isRtl ? 'كتم الصوت' : 'Mute');
            switchEl.title = titleVal;
            const parentLabel = switchEl.parentElement;
            if (parentLabel && parentLabel.classList.contains('ntf-switch')) {
                parentLabel.title = titleVal;
            }
        }

        const icon = document.getElementById('mute-toggle-icon');
        if (icon) {
            icon.className = this.isMuted ? 'bi bi-volume-mute-fill' : 'bi bi-volume-up-fill';
        }

        if (!this.isMuted) {
            this.playSound('info');
        }
    }

    /* ───────────── Build Main Panel & Details Drawer ───────────── */
    buildPanel() {
        // Create Main Feed Panel
        const panel = document.createElement('div');
        panel.id = 'alerts-panel';
        panel.innerHTML = `
            <div class="px-4 py-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-shrink-0" style="background-color: var(--ntf-header-bg) !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold fs-6">
                        <i class="bi bi-bell-fill me-1 text-primary"></i> ${this.labels.title}
                    </span>
                    <div class="ntf-switch-wrapper" style="${this.isRtl ? 'margin-right: 12px;' : 'margin-left: 12px;'}">
                        <label class="ntf-switch" title="${this.isMuted ? (this.isRtl ? 'تفعيل الصوت' : 'Unmute') : (this.isRtl ? 'كتم الصوت' : 'Mute')}" style="cursor: pointer;">
                            <input type="checkbox" id="mute-alerts-switch" ${!this.isMuted ? 'checked' : ''}>
                            <span class="ntf-slider"></span>
                        </label>
                        <span class="text-muted d-flex align-items-center" style="cursor: pointer;" onclick="document.getElementById('mute-alerts-switch').click();">
                            <i id="mute-toggle-icon" class="bi bi-volume-${this.isMuted ? 'mute' : 'up'}-fill ms-1 me-1 fs-5"></i>
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button id="mark-all-read-btn" class="btn btn-sm btn-link text-decoration-none text-info p-0 font-size-sm fw-semibold" style="font-size: 0.78rem;" title="${this.labels.markAllRead}">
                        <i class="bi bi-check-all fs-6"></i>
                    </button>
                    <button id="dismiss-all-btn" class="btn btn-sm btn-link text-decoration-none text-danger p-0 border-0 ms-2" title="${this.labels.dismissAll}">
                        <i class="bi bi-trash fs-6"></i>
                    </button>
                    <a href="/settings/notifications" class="btn btn-sm btn-link text-muted p-0 border-0 ms-2" title="Settings">
                        <i class="bi bi-gear-fill fs-6"></i>
                    </a>
                    <button id="close-alerts-panel" class="btn btn-sm btn-link text-muted p-0 border-0 ms-2">
                        <i class="bi bi-x-lg fs-6"></i>
                    </button>
                </div>
            </div>

            <!-- Quick Summary Cards (Filters) -->
            <div class="ntf-summary-grid">
                <div class="ntf-summary-card" data-filter="low_stock" title="${this.labels.types.low_stock}">
                    <span class="label">⚠️ ${this.labels.types.low_stock}</span>
                    <span class="num text-warning" id="sum-low-stock">0</span>
                </div>
                <div class="ntf-summary-card" data-filter="out_of_stock" title="${this.labels.types.out_of_stock}">
                    <span class="label">🚫 ${this.labels.types.out_of_stock}</span>
                    <span class="num text-danger" id="sum-out-of-stock">0</span>
                </div>
                <div class="ntf-summary-card" data-filter="expiring_soon" title="${this.labels.types.expiring_soon}">
                    <span class="label">⏳ ${this.labels.types.expiring_soon}</span>
                    <span class="num text-warning" id="sum-expiring-soon">0</span>
                </div>
                <div class="ntf-summary-card" data-filter="expired" title="${this.labels.types.expired}">
                    <span class="label">❌ ${this.labels.types.expired}</span>
                    <span class="num text-danger" id="sum-expired">0</span>
                </div>
            </div>

            <!-- Main Scrollable Feed List -->
            <div id="alerts-panel-body" class="flex-grow-1 overflow-auto ntf-scroller" style="min-height: 0;"></div>
        `;
        document.body.appendChild(panel);
        this.panel = panel;

        // Create Details sliding panel
        const detailPanel = document.createElement('div');
        detailPanel.id = 'alerts-detail-panel';
        detailPanel.innerHTML = `
            <div class="ntf-detail-header">
                <button id="detail-back-btn" class="btn btn-sm btn-link text-decoration-none p-0 text-muted fw-bold font-size-sm">
                    <i class="bi bi-arrow-${this.isRtl ? 'right' : 'left'} me-1"></i> ${this.labels.back}
                </button>
                <button id="detail-close-btn" class="btn btn-sm btn-link text-muted p-0 border-0">
                    <i class="bi bi-x-lg fs-6"></i>
                </button>
            </div>
            <div class="ntf-detail-body ntf-scroller">
                <div class="d-flex gap-2 mb-3">
                    <span id="detail-category-badge" class="badge bg-secondary-subtle text-secondary py-1 px-2 font-size-sm">Category</span>
                    <span id="detail-priority-badge" class="badge py-1 px-2 font-size-sm">Priority</span>
                </div>
                <div id="detail-number" class="text-muted font-monospace small mb-2">NTF-00000000-00000</div>
                <h5 id="detail-title" class="fw-bold mb-3 text-dark-theme-main">Notification Title</h5>
                <div class="text-muted small mb-3">
                    <i class="bi bi-clock me-1"></i> <span id="detail-time">Just now</span>
                </div>
                <hr style="opacity: 0.1;">
                <p id="detail-message" class="fs-6 text-secondary" style="white-space: pre-wrap; line-height: 1.6;"></p>
            </div>
            <div class="ntf-detail-footer">
                <div id="detail-actions"></div>
                <button id="detail-mark-read-btn" class="ntf-btn-cta ntf-btn-cta-secondary"></button>
            </div>
        `;
        document.body.appendChild(detailPanel);
        this.detailPanel = detailPanel;

        // Create overlay element
        const overlay = document.createElement('div');
        overlay.id = 'alerts-overlay';
        document.body.appendChild(overlay);
        this.overlay = overlay;
    }

    /* ───────────── Bind Events ───────────── */
    bindEvents() {
        // Toggle Bell Panel
        const bellBtn = document.getElementById('bell-btn');
        if (bellBtn) {
            bellBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (this.panelOpen) this.closePanel();
                else this.openPanel();
            });
        }

        document.getElementById('close-alerts-panel')
            .addEventListener('click', () => this.closePanel());

        // Overlay Dismissal
        this.overlay.addEventListener('click', () => {
            this.closePanel();
        });

        // Detail Drawer Dismissals
        document.getElementById('detail-back-btn')
            .addEventListener('click', () => this.closeDetailPanel());
        
        document.getElementById('detail-close-btn')
            .addEventListener('click', () => this.closePanel());

        // Top Control Button Clicks
        const muteSwitch = document.getElementById('mute-alerts-switch');
        if (muteSwitch) {
            muteSwitch.addEventListener('change', () => this.toggleMute());
        }

        document.getElementById('mark-all-read-btn')
            .addEventListener('click', () => this.markAllAsRead());

        document.getElementById('dismiss-all-btn')
            .addEventListener('click', () => this.dismissAll());

        // Quick Summary Cards filtering triggers
        this.panel.querySelectorAll('.ntf-summary-card').forEach(card => {
            card.addEventListener('click', () => {
                const filter = card.dataset.filter;
                if (this.activeFilter === filter) {
                    this.activeFilter = null;
                    card.classList.remove('active');
                } else {
                    this.panel.querySelectorAll('.ntf-summary-card').forEach(c => c.classList.remove('active'));
                    this.activeFilter = filter;
                    card.classList.add('active');
                }
                this.renderPanel();
                
                // Reset scroll position to top
                const body = document.getElementById('alerts-panel-body');
                if (body) {
                    body.scrollTop = 0;
                }
            });
        });
    }

    openPanel() {
        this.panelOpen = true;
        this.panel.style.transform = 'translateX(0)';
        this.overlay.style.display = 'block';
        this.renderPanel();
        
        // Reset scroll position
        const body = document.getElementById('alerts-panel-body');
        if (body) {
            body.scrollTop = 0;
        }

        // Mark badge as hidden/0 temporarily
        const badge = document.getElementById('bell-badge');
        if (badge) badge.style.display = 'none';

        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }
    }

    closePanel() {
        this.panelOpen = false;
        this.closeDetailPanel();
        this.panel.style.transform = `translateX(${this.isRtl ? '-110%' : '110%'})`;
        this.overlay.style.display = 'none';
        this.fetchNotificationsSilent(); // Sync badge state back
    }

    openDetailPanel(n) {
        this.selectedNotification = n;
        this.detailPanelOpen = true;
        
        // Bind data values to Drawer Elements
        document.getElementById('detail-number').textContent = n.notification_number;
        document.getElementById('detail-title').textContent = n.title;
        document.getElementById('detail-time').textContent = n.time_ago;
        document.getElementById('detail-message').textContent = n.message;
        
        // Category/Type badge
        const catBadge = document.getElementById('detail-category-badge');
        catBadge.textContent = this.labels.types[n.type] || this.labels.types.low_stock;

        // Priority Badge
        const prioBadge = document.getElementById('detail-priority-badge');
        prioBadge.className = 'badge py-1 px-2 font-size-sm';
        
        const isCritical = n.priority === 'Critical';
        prioBadge.textContent = isCritical ? (this.isRtl ? 'حرج' : 'Critical') : (this.isRtl ? 'تحذير' : 'Warning');
        
        if (isCritical) {
            prioBadge.classList.add('bg-danger');
        } else {
            prioBadge.classList.add('bg-warning', 'text-dark');
        }

        // Mark Read Toggle Button
        const markReadBtn = document.getElementById('detail-mark-read-btn');
        if (n.read_status) {
            markReadBtn.innerHTML = `<i class="bi bi-envelope"></i> ${this.labels.markUnread}`;
            markReadBtn.onclick = () => this.toggleReadStatus(n.id, false);
        } else {
            markReadBtn.innerHTML = `<i class="bi bi-envelope-open"></i> ${this.labels.markRead}`;
            markReadBtn.onclick = () => this.toggleReadStatus(n.id, true);
        }

        // Dismiss button next to mark read button
        let dismissBtn = document.getElementById('detail-dismiss-btn');
        if (!dismissBtn) {
            dismissBtn = document.createElement('button');
            dismissBtn.id = 'detail-dismiss-btn';
            dismissBtn.className = 'ntf-btn-cta ntf-btn-cta-secondary text-danger mt-2';
            markReadBtn.parentNode.insertBefore(dismissBtn, markReadBtn.nextSibling);
        }
        dismissBtn.innerHTML = `<i class="bi bi-trash"></i> ${this.labels.dismiss}`;
        dismissBtn.onclick = () => this.dismissNotification(n.id);

        // Context Action CTAs (polymorphic buttons)
        const actionsContainer = document.getElementById('detail-actions');
        actionsContainer.innerHTML = '';
        if (n.actions && n.actions.length > 0) {
            n.actions.forEach(action => {
                const label = this.isRtl ? action.label_ar : action.label_en;
                const isPrimary = action.class.includes('btn-primary') || action.class.includes('btn-warning');
                const btn = document.createElement('a');
                btn.href = action.url;
                btn.className = `ntf-btn-cta ${isPrimary ? 'ntf-btn-cta-primary' : 'ntf-btn-cta-secondary'}`;
                btn.innerHTML = `${label}`;
                
                btn.addEventListener('click', () => {
                    if (!n.read_status) {
                        this.toggleReadStatus(n.id, true, false);
                    }
                });
                actionsContainer.appendChild(btn);
            });
        }

        // Slide in Detail Drawer
        this.detailPanel.style.transform = 'translateX(0)';
    }

    closeDetailPanel() {
        this.detailPanelOpen = false;
        this.selectedNotification = null;
        this.detailPanel.style.transform = `translateX(${this.isRtl ? '-110%' : '110%'})`;
    }

    /* ───────────── API Fetch Calls ───────────── */
    fetchNotifications() {
        fetch('/api/notifications')
            .then(r => r.json())
            .then(data => {
                this.allNotifications = data.notifications || { today: [], yesterday: [], last_7_days: [], older: [] };
                this.unreadCount = data.unread_count || 0;
                this.updateBadge(this.unreadCount);
                this.updateMetrics();

                const allFetched = [
                    ...(this.allNotifications.today || []),
                    ...(this.allNotifications.yesterday || []),
                    ...(this.allNotifications.last_7_days || []),
                    ...(this.allNotifications.older || [])
                ];

                if (this.isFirstLoad) {
                    allFetched.forEach(n => this.knownIds.add(n.id));
                    this.isFirstLoad = false;
                } else {
                    let hasNew = false;
                    let highestPriority = 'info';

                    allFetched.forEach(n => {
                        if (!this.knownIds.has(n.id)) {
                            this.knownIds.add(n.id);
                            if (!n.read_status) {
                                hasNew = true;
                                if (n.priority === 'Critical') highestPriority = 'alert';
                                else if (n.priority === 'Important' && highestPriority !== 'alert') highestPriority = 'warning';
                                this.showToast(n);
                            }
                        }
                    });

                    if (hasNew) {
                        this.playSound(highestPriority);
                    }
                }

                if (this.panelOpen) {
                    this.renderPanel();
                }
            })
            .catch(e => console.error('Error fetching notifications:', e));
    }

    fetchNotificationsSilent() {
        fetch('/api/notifications')
            .then(r => r.json())
            .then(data => {
                this.allNotifications = data.notifications || { today: [], yesterday: [], last_7_days: [], older: [] };
                this.unreadCount = data.unread_count || 0;
                this.updateBadge(this.unreadCount);
                this.updateMetrics();

                const allFetched = [
                    ...(this.allNotifications.today || []),
                    ...(this.allNotifications.yesterday || []),
                    ...(this.allNotifications.last_7_days || []),
                    ...(this.allNotifications.older || [])
                ];

                let hasNew = false;
                let highestPriority = 'info';

                allFetched.forEach(n => {
                    if (!this.knownIds.has(n.id)) {
                        this.knownIds.add(n.id);
                        if (!n.read_status) {
                            hasNew = true;
                            if (n.priority === 'Critical') highestPriority = 'alert';
                            else if (n.priority === 'Important' && highestPriority !== 'alert') highestPriority = 'warning';
                            this.showToast(n);
                        }
                    }
                });

                if (hasNew) {
                    this.playSound(highestPriority);
                }

                if (this.panelOpen) {
                    this.renderPanel();
                }
            })
            .catch(e => console.error('Error fetching notifications:', e));
    }

    /* ───────────── API Actions ───────────── */
    toggleReadStatus(id, readState = true, refreshFeed = true) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/api/notifications/read/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                read_status: readState
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const rawN = this.findNotificationById(id);
                if (rawN) {
                    rawN.read_status = readState;
                }
                if (this.selectedNotification && this.selectedNotification.id === id) {
                    this.selectedNotification.read_status = readState;
                }
                this.fetchNotificationsSilent();
                if (refreshFeed) {
                    this.closeDetailPanel();
                }
            }
        })
        .catch(e => console.error('Error toggling read state:', e));
    }

    dismissNotification(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/api/notifications/dismiss/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const rm = (list) => {
                    const idx = list.findIndex(n => n.id === id);
                    if (idx !== -1) list.splice(idx, 1);
                };
                rm(this.allNotifications.today);
                rm(this.allNotifications.yesterday);
                rm(this.allNotifications.last_7_days);
                rm(this.allNotifications.older);

                this.fetchNotificationsSilent();
                this.closeDetailPanel();
            }
        })
        .catch(e => console.error('Error dismissing notification:', e));
    }

    markAllAsRead() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/api/notifications/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const items = this.panel.querySelectorAll('.ntf-item');
                items.forEach(item => {
                    item.classList.remove('unread');
                    const dot = item.querySelector('.ntf-inline-dot');
                    if (dot) dot.remove();
                });
                
                this.unreadCount = 0;
                this.updateBadge(0);
                this.fetchNotificationsSilent();
                this.closeDetailPanel();
            }
        })
        .catch(e => console.error('Error marking all read:', e));
    }

    dismissAll() {
        if (!confirm(this.isRtl ? 'هل أنت متأكد من تجاهل جميع التنبيهات؟' : 'Are you sure you want to dismiss all alerts?')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/api/notifications/dismiss-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                this.allNotifications = { today: [], yesterday: [], last_7_days: [], older: [] };
                this.unreadCount = 0;
                this.updateBadge(0);
                this.fetchNotificationsSilent();
                this.closeDetailPanel();
            }
        })
        .catch(e => console.error('Error dismissing all:', e));
    }

    /* ───────────── UI Layout Rendering ───────────── */
    updateBadge(count) {
        const badge = document.getElementById('bell-badge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    updateMetrics() {
        const allFetched = [
            ...(this.allNotifications.today || []),
            ...(this.allNotifications.yesterday || []),
            ...(this.allNotifications.last_7_days || []),
            ...(this.allNotifications.older || [])
        ];

        const lowStock = allFetched.filter(n => n.type === 'low_stock').length;
        const outOfStock = allFetched.filter(n => n.type === 'out_of_stock').length;
        const expiringSoon = allFetched.filter(n => n.type === 'expiring_soon').length;
        const expired = allFetched.filter(n => n.type === 'expired').length;

        const lowEl = document.getElementById('sum-low-stock');
        const outEl = document.getElementById('sum-out-of-stock');
        const expSoonEl = document.getElementById('sum-expiring-soon');
        const expEl = document.getElementById('sum-expired');

        if (lowEl) lowEl.textContent = lowStock;
        if (outEl) outEl.textContent = outOfStock;
        if (expSoonEl) expSoonEl.textContent = expiringSoon;
        if (expEl) expEl.textContent = expired;
    }

    renderPanel() {
        const body = document.getElementById('alerts-panel-body');
        
        // Filter notifications by active type card
        const filterFn = (n) => {
            if (!this.activeFilter) return true;
            return n.type === this.activeFilter;
        };

        const todayFiltered = (this.allNotifications.today || []).filter(filterFn);
        const yesterdayFiltered = (this.allNotifications.yesterday || []).filter(filterFn);
        const last7DaysFiltered = (this.allNotifications.last_7_days || []).filter(filterFn);
        const olderFiltered = (this.allNotifications.older || []).filter(filterFn);

        const hasToday = todayFiltered.length > 0;
        const hasYesterday = yesterdayFiltered.length > 0;
        const hasLast7 = last7DaysFiltered.length > 0;
        const hasOlder = olderFiltered.length > 0;

        if (!hasToday && !hasYesterday && !hasLast7 && !hasOlder) {
            body.innerHTML = `
                <div class="text-center py-5 my-5 text-muted">
                    <i class="bi bi-bell-slash text-secondary fs-2 mb-3 d-block"></i>
                    <h6 class="fw-bold" style="color: var(--ntf-text-main) !important; font-size: 0.85rem;">${this.labels.noNotifications}</h6>
                    <small class="text-muted d-block px-4" style="font-size: 0.75rem;">${this.labels.noNotificationsDesc}</small>
                </div>`;
            return;
        }

        let html = '';

        if (hasToday) {
            html += `<div class="ntf-date-group">${this.labels.today}</div>`;
            todayFiltered.forEach(n => html += this.getItemHtml(n));
        }

        if (hasYesterday) {
            html += `<div class="ntf-date-group">${this.labels.yesterday}</div>`;
            yesterdayFiltered.forEach(n => html += this.getItemHtml(n));
        }

        if (hasLast7) {
            html += `<div class="ntf-date-group">${this.labels.last7Days}</div>`;
            last7DaysFiltered.forEach(n => html += this.getItemHtml(n));
        }

        if (hasOlder) {
            html += `<div class="ntf-date-group">${this.labels.older}</div>`;
            olderFiltered.forEach(n => html += this.getItemHtml(n));
        }

        body.innerHTML = html;

        // Bind interactive events to list elements
        body.querySelectorAll('.ntf-item').forEach(item => {
            const id = parseInt(item.dataset.id);
            const rawN = this.findNotificationById(id);

            if (!rawN) return;

            // Clicking the card opens details drawer
            item.addEventListener('click', (e) => {
                if (e.target.closest('.ntf-item-options') || e.target.closest('.dropdown-menu')) {
                    return; // Prevent details popup on menu click
                }
                this.openDetailPanel(rawN);
            });

            // Toggle Read status from options dropdown
            const toggleReadOpt = item.querySelector('.toggle-read-opt');
            if (toggleReadOpt) {
                toggleReadOpt.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleReadStatus(rawN.id, !rawN.read_status, false);
                });
            }

            // Dismiss option
            const dismissOpt = item.querySelector('.dismiss-opt');
            if (dismissOpt) {
                dismissOpt.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.dismissNotification(rawN.id);
                });
            }
        });
    }

    findNotificationById(id) {
        const all = [
            ...(this.allNotifications.today || []),
            ...(this.allNotifications.yesterday || []),
            ...(this.allNotifications.last_7_days || []),
            ...(this.allNotifications.older || [])
        ];
        return all.find(n => n.id === id);
    }

    /* ───────────── Helper: HTML Item Builder ───────────── */
    getItemHtml(n) {
        // Map alert type styles & icons
        let catColor = '#64748b';
        let catBg = 'rgba(100, 116, 139, 0.08)';
        let catIcon = 'info-circle';

        if (n.type === 'low_stock') {
            catColor = '#f59e0b';
            catBg = 'rgba(245, 158, 11, 0.08)';
            catIcon = 'exclamation-triangle';
        } else if (n.type === 'out_of_stock') {
            catColor = '#ef4444';
            catBg = 'rgba(239, 68, 68, 0.08)';
            catIcon = 'slash-circle';
        } else if (n.type === 'expiring_soon') {
            catColor = '#f59e0b';
            catBg = 'rgba(245, 158, 11, 0.08)';
            catIcon = 'hourglass-split';
        } else if (n.type === 'expired') {
            catColor = '#ef4444';
            catBg = 'rgba(239, 68, 68, 0.08)';
            catIcon = 'x-circle';
        }

        const isCritical = n.priority === 'Critical';
        const prioText = isCritical ? (this.isRtl ? 'حرج' : 'Critical') : (this.isRtl ? 'تحذير' : 'Warning');
        const prioClass = isCritical ? 'ntf-prio-critical' : 'ntf-prio-important';
        const readClass = n.read_status ? '' : 'unread';

        // Direct actions mapping in dropdown
        const viewProductUrl = `/products?search=${n.product_id || ''}`;
        const viewBatchUrl = `/products?search=${n.batch_id ? encodeURIComponent(n.batch_id) : ''}`;
        const createAdjustmentUrl = `/adjustments?product_id=${n.product_id || ''}`;

        const viewProductOpt = n.product_id ? `<li><a class="dropdown-item py-1 px-3" href="${viewProductUrl}"><i class="bi bi-box me-1"></i> ${this.labels.viewProduct}</a></li>` : '';
        const viewBatchOpt = n.batch_id ? `<li><a class="dropdown-item py-1 px-3" href="${viewBatchUrl}"><i class="bi bi-tag me-1"></i> ${this.labels.viewBatch}</a></li>` : '';
        const createAdjustmentOpt = n.product_id ? `<li><a class="dropdown-item py-1 px-3" href="${createAdjustmentUrl}"><i class="bi bi-sliders me-1"></i> ${this.labels.createAdjustment}</a></li>` : '';

        // Context Menu dropdown structure
        const contextMenu = `
            <div class="ntf-item-options dropdown">
                <button class="ntf-options-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-${this.isRtl ? 'start' : 'end'} shadow-sm py-1" style="font-size: 0.72rem; min-width: 120px; border-color: var(--ntf-border-soft); z-index: 10600;">
                    ${viewProductOpt}
                    ${viewBatchOpt}
                    ${createAdjustmentOpt}
                    <li><a class="dropdown-item toggle-read-opt py-1 px-3" href="#"><i class="bi bi-${n.read_status ? 'envelope' : 'envelope-open'} me-1"></i> ${n.read_status ? this.labels.markUnread : this.labels.markRead}</a></li>
                    <li><a class="dropdown-item dismiss-opt py-1 px-3 text-danger" href="#"><i class="bi bi-trash me-1"></i> ${this.labels.dismiss}</a></li>
                </ul>
            </div>
        `;

        const inlineDot = !n.read_status ? `<span class="ntf-inline-dot"></span>` : '';

        // Two-row compact layout: Row 1 = Title & Time, Row 2 = Description & Priority
        return `
            <div class="ntf-item ${readClass}" data-id="${n.id}">
                <div class="ntf-mini-icon" style="background-color: ${catBg}; color: ${catColor}; border-color: rgba(0,0,0,0.02);">
                    <i class="bi bi-${catIcon}"></i>
                </div>
                <div class="ntf-info-area">
                    <div class="d-flex align-items-center justify-content-between mb-1" style="gap: 12px;">
                        <span class="ntf-item-title mb-0" style="flex: 1;">
                            ${inlineDot}${n.title}
                        </span>
                        <span class="ntf-time-text text-nowrap">${n.time_ago}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                        <div class="ntf-item-desc mb-0" style="flex: 1;">${n.message}</div>
                        <span class="ntf-prio-badge ${prioClass}">${prioText}</span>
                    </div>
                </div>
                ${contextMenu}
            </div>
        `;
    }

    /* ───────────── Toast notifications for new alerts ───────────── */
    showToast(n) {
        if (!this.container) {
            this.container = document.getElementById('notification-container');
        }
        if (!this.container) return;

        const delays = { Critical: 10000, Important: 7000, Activity: 4000 };
        const delay = delays[n.priority] || 5000;
        const id = 'toast_' + Math.random().toString(36).slice(2);

        // Styling based on severity
        let themeClass = 'text-bg-primary';
        if (n.priority === 'Critical') themeClass = 'text-bg-danger';
        else if (n.priority === 'Important') themeClass = 'text-bg-warning text-dark';

        this.container.insertAdjacentHTML('beforeend', `
            <div id="${id}" class="toast align-items-center ${themeClass} border-0 mb-2 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <div class="fw-bold mb-1"><i class="bi bi-bell-fill"></i> ${n.title}</div>
                        <div style="font-size: 0.82rem;">${n.message}</div>
                    </div>
                    <button type="button" class="btn-close ${n.priority === 'Important' ? '' : 'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`);

        const el = document.getElementById(id);
        const bsToast = new bootstrap.Toast(el, { autohide: true, delay });
        bsToast.show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.POSAlerts = new NotificationSystem();
});

@extends('layouts.app')

@section('title', __('pos.activity_feed') ?? 'Activity Feed')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-10">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient bg-dark py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white fw-bold">
                        <i class="bi bi-clock-history me-2 text-info"></i>{{ __('pos.activity_feed') ?? 'Activity Feed' }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('settings.notifications') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold">
                            <i class="bi bi-gear me-1"></i>{{ __('pos.notification_settings') ?? 'Settings' }}
                        </a>
                        <button onclick="window.location.reload();" class="btn btn-sm btn-info rounded-pill px-3 fw-bold text-dark">
                            <i class="bi bi-arrow-clockwise me-1"></i>{{ __('pos.refresh') ?? 'Refresh' }}
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Filters & Category Badges -->
                    <div class="d-flex flex-wrap gap-2 mb-4 align-items-center justify-content-between">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="text-muted small fw-bold me-2">{{ __('pos.filter_by') ?? 'Filter:' }}</span>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-feed active" data-category="all">
                                {{ __('pos.all') ?? 'All' }}
                            </button>
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 filter-feed" data-category="inventory">
                                <i class="bi bi-box-seam me-1"></i>{{ __('pos.inventory') ?? 'Inventory' }}
                            </button>
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3 filter-feed" data-category="purchases">
                                <i class="bi bi-cart-plus me-1"></i>{{ __('pos.purchases') ?? 'Purchases' }}
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 filter-feed" data-category="returns">
                                <i class="bi bi-arrow-return-left me-1"></i>{{ __('pos.sales_returns') ?? 'Returns' }}
                            </button>
                            <button class="btn btn-sm btn-outline-warning rounded-pill px-3 filter-feed text-dark" data-category="sales">
                                <i class="bi bi-receipt me-1"></i>{{ __('pos.sales') ?? 'Sales' }}
                            </button>
                            <button class="btn btn-sm btn-outline-info rounded-pill px-3 filter-feed text-dark" data-category="system">
                                <i class="bi bi-cpu me-1"></i>{{ __('pos.system_administration') ?? 'System' }}
                            </button>
                        </div>
                        <div class="text-muted small">
                            {{ __('pos.showing_latest_activities') ?? 'Showing latest log events' }}
                        </div>
                    </div>

                    @if(empty($feedItems))
                        <div class="text-center py-5 my-4">
                            <div class="avatar-circle bg-light text-muted mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                                <i class="bi bi-clipboard-x"></i>
                            </div>
                            <h5 class="fw-bold text-muted">{{ __('pos.no_activities_found') ?? 'No activities recorded yet' }}</h5>
                            <p class="text-muted small">{{ __('pos.activities_helper') ?? 'System logs and notifications will appear here as transactions and security events occur.' }}</p>
                        </div>
                    @else
                        <!-- Timeline / Activity List -->
                        <div class="timeline-container">
                            @foreach($feedItems as $item)
                                @php
                                    // Assign specific styles based on category
                                    $catColor = 'secondary';
                                    $catIcon = 'info-circle';
                                    if ($item['category'] == 'inventory') {
                                        $catColor = 'primary';
                                        $catIcon = 'box-seam';
                                    } elseif ($item['category'] == 'purchases') {
                                        $catColor = 'success';
                                        $catIcon = 'cart-plus';
                                    } elseif ($item['category'] == 'returns') {
                                        $catColor = 'danger';
                                        $catIcon = 'arrow-return-left';
                                    } elseif ($item['category'] == 'sales') {
                                        $catColor = 'warning';
                                        $catIcon = 'receipt';
                                    } elseif ($item['category'] == 'system') {
                                        $catColor = 'info';
                                        $catIcon = 'cpu';
                                    }

                                    // Priority badge style
                                    $priorityBadge = 'bg-secondary-subtle text-secondary';
                                    if ($item['priority'] == 'Critical') {
                                        $priorityBadge = 'bg-danger-subtle text-danger';
                                    } elseif ($item['priority'] == 'Important') {
                                        $priorityBadge = 'bg-warning-subtle text-warning';
                                    }
                                @endphp
                                <div class="feed-item p-3 mb-3 border border-light-subtle rounded-3 transition-all hover-shadow bg-light bg-opacity-10 d-flex gap-3 align-items-start" data-category="{{ $item['category'] }}">
                                    <!-- Category Icon -->
                                    <div class="avatar-circle bg-{{ $catColor }} bg-gradient text-white flex-shrink-0" style="width: 45px; height: 45px; font-size: 1.25rem; border-radius: 12px;">
                                        <i class="bi bi-{{ $catIcon }}"></i>
                                    </div>
                                    
                                    <!-- Text & details -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $item['title'] }}</h6>
                                                <span class="badge {{ $priorityBadge }} rounded-pill px-2 py-1 small-badge">
                                                    {{ $item['priority'] }}
                                                </span>
                                            </div>
                                            <div class="text-muted small d-flex align-items-center gap-1">
                                                <i class="bi bi-clock"></i>
                                                <span>{{ $item['created_at'] }}</span>
                                                <span class="mx-1">•</span>
                                                <span class="fw-semibold text-primary">{{ $item['time_ago'] }}</span>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0 small-text">{{ $item['message'] }}</p>
                                        <div class="mt-2 d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-secondary border border-light-subtle font-monospace">
                                                {{ $item['notification_number'] }}
                                            </span>
                                            <span class="text-muted small text-capitalize">
                                                <i class="bi bi-tag-fill me-1"></i>{{ $item['category'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .feed-item {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .feed-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .small-badge {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .small-text {
        font-size: 0.85rem;
        line-height: 1.4;
    }
    html[data-app-theme="dark"] .feed-item {
        background-color: #1e293b !important;
        border-color: var(--border-color) !important;
    }
    html[data-app-theme="dark"] .feed-item .text-dark {
        color: var(--text-color) !important;
    }
    html[data-app-theme="dark"] .feed-item .text-muted {
        color: var(--text-muted) !important;
    }
    html[data-app-theme="dark"] .bg-light {
        background-color: #0f172a !important;
    }
    /* RTL adjustments for badges/filters */
    html[dir="rtl"] .border-start {
        border-left: 0 !important;
        border-right: 4px solid var(--bs-primary) !important;
        padding-left: 0 !important;
        padding-right: 8px !important;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-feed');
        const feedItems = document.querySelectorAll('.feed-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const cat = this.dataset.category;
                feedItems.forEach(item => {
                    if (cat === 'all' || item.dataset.category === cat) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection

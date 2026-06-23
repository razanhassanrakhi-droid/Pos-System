@props(['cards'])

<div class="row g-4 mb-4">
    @foreach($cards as $card)
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm border-start border-4 border-{{ $card['color'] ?? 'primary' }} dashboard-kpi-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-{{ $card['color'] ?? 'primary' }} bg-opacity-10 text-{{ $card['color'] ?? 'primary' }} rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-{{ $card['icon'] ?? 'bar-chart' }} fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase small mb-1 fw-bold text-muted">{{ $card['title'] }}</h6>
                        <h3 class="fw-bold mb-0 text-dark">
                            {{ $card['value'] }}
                            @if(isset($card['unit']))
                                <small class="fs-6 text-muted fw-normal">{{ $card['unit'] }}</small>
                            @endif
                        </h3>
                        @if(isset($card['subtitle']))
                            <div class="mt-1">
                                <span class="text-{{ $card['subtitle_color'] ?? 'muted' }} small fw-bold">
                                    {{ $card['subtitle'] }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
    .dashboard-kpi-card {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    .dashboard-kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
    }
</style>

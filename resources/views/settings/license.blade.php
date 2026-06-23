@extends('layouts.app')

@section('title', __('pos.license_request_form'))

@section('content')
<div class="row justify-content-center animate__animated animate__fadeIn">
    <div class="col-lg-8 col-xl-7">
        
        <!-- Current License Status Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: {{ $licenseStatus == 'active' ? '#e8f5e9' : ($licenseStatus == 'trial' ? '#e3f2fd' : ($licenseStatus == 'expired' ? '#ffebee' : '#fff3e0')) }}">
                    <i class="bi {{ $licenseStatus == 'active' ? 'bi-patch-check-fill text-success' : ($licenseStatus == 'trial' ? 'bi-clock-fill text-primary' : ($licenseStatus == 'expired' ? 'bi-exclamation-octagon-fill text-danger' : 'bi-shield-slash-fill text-warning')) }} fs-2"></i>
                </div>
                <div>
                    <h6 class="mb-1 text-secondary small text-uppercase fw-bold">{{ __('pos.license_status') }}</h6>
                    <h4 class="mb-0 fw-bold">
                        @if($licenseStatus == 'active')
                            <span class="text-success">{{ __('pos.active') }}</span>
                            <span class="ms-2 small fw-normal text-muted">({{ __('pos.expires_at') }}: {{ $setting->license_expires_at->format('Y-m-d') }})</span>
                        @elseif($licenseStatus == 'trial')
                            <span class="text-primary">{{ __('pos.trial_active') }}</span>
                            <span class="ms-2 small fw-normal text-muted">
                                ({{ __('pos.remaining') }}: 
                                {{ 7 - now()->diffInDays($setting->created_at ?: now()) }} 
                                {{ __('pos.days_remaining') ?? 'Days' }})
                            </span>
                        @elseif($licenseStatus == 'expired')
                            <span class="text-danger">{{ __('pos.expired') }}</span>
                        @else
                            <span class="text-warning">{{ __('pos.inactive') }}</span>
                        @endif
                    </h4>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-lg overflow-hidden mb-4" style="border-radius: 20px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <!-- Header -->
            <div class="position-relative p-5 text-center text-white" style="background: linear-gradient(135deg, #46bfa3 0%, #1E88E5 100%);">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.1;"></div>
                <h2 class="fw-bold mb-1 position-relative">{{ __('pos.license_request_form') }}</h2>
                <p class="mb-0 opacity-75 position-relative">{{ __('pos.submit_instructions') }}</p>
                
                @if(auth()->user()->isAdmin())
                <div class="mt-3 position-relative">
                    <a href="{{ route('settings.license.manager') }}" class="btn btn-outline-light btn-sm rounded-pill px-4">
                        <i class="bi bi-shield-lock me-1"></i> {{ __('pos.license_manager') }}
                    </a>
                </div>
                @endif
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="{{ route('settings.license.request') }}" method="POST">
                    @csrf
                    
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary-subtle p-2 rounded-3 me-3">
                                <i class="bi bi-laptop text-primary fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">1. {{ __('pos.device_info') }}</h5>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">{{ __('pos.device_name') }}</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-tag"></i></span>
                                    <input type="text" name="device_name" class="form-control border-start-0 ps-0 fs-6" required value="{{ gethostname() }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">{{ __('pos.current_device_id') }}</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-fingerprint"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0 fs-6 fw-bold text-primary" value="{{ $deviceId }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success-subtle p-2 rounded-3 me-3">
                                <i class="bi bi-person-badge text-success fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">2. {{ __('pos.user_info') }}</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">{{ __('pos.full_name') }}</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-person"></i></span>
                                    <input type="text" name="full_name" class="form-control border-start-0 ps-0 fs-6" required value="{{ auth()->user()->full_name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">{{ __('pos.email') }} (البريد الإلكتروني)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0 fs-6" value="{{ auth()->user()->email }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-warning-subtle p-2 rounded-3 me-3">
                                <i class="bi bi-gem text-warning fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">3. {{ __('pos.license_type') }}</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="license-option p-4 border rounded-4 text-center cursor-pointer transition-all h-100" id="trial-box" onclick="selectLicense('trial')">
                                    <input class="d-none" type="radio" name="license_type" id="trial-radio" value="trial" checked>
                                    <i class="bi bi-clock-history fs-1 text-secondary mb-3 d-block"></i>
                                    <h6 class="fw-bold mb-1">{{ __('pos.trial') }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="license-option p-4 border rounded-4 text-center cursor-pointer transition-all h-100" id="paid-box" onclick="selectLicense('paid')">
                                    <input class="d-none" type="radio" name="license_type" id="paid-radio" value="paid">
                                    <i class="bi bi-credit-card-2-front fs-1 text-secondary mb-3 d-block"></i>
                                    <h6 class="fw-bold mb-1">{{ __('pos.paid') }}</h6>
                                </div>
                            </div>
                        </div>

                        <div id="duration-container" class="mt-4" style="display: none;">
                            <label class="form-label fw-semibold small text-secondary">{{ __('pos.duration_days') }}</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-calendar-range"></i></span>
                                <input type="number" name="duration" class="form-control border-start-0 ps-0 fs-6" placeholder="365">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-4 shadow-sm fw-bold border-0" style="background: linear-gradient(135deg, #46bfa3 0%, #1E88E5 100%);">
                        <i class="bi bi-file-earmark-arrow-down me-2"></i> {{ __('pos.generate_request') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Activation Form Card -->
        <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
            <div class="card-header bg-dark p-4 border-0">
                <h5 class="mb-0 text-white fw-bold"><i class="bi bi-shield-check me-2 text-warning"></i>{{ __('pos.activate_system') }}</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('settings.license.activate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">{{ __('pos.activation_instructions') }}</label>
                        
                        <div class="upload-zone p-5 text-center border-2 border-dashed rounded-4 position-relative mb-3" id="drop-zone" style="background: #f8fafc; border-color: #cbd5e1; transition: all 0.3s ease;">
                            <input type="file" name="license_file" id="license_file" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer" accept=".lic" required onchange="updateFileName(this)">
                            
                            <div id="upload-icon-container">
                                <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark mb-1">{{ __('pos.upload_lic_file') }}</h6>
                                <p class="text-muted small mb-0">{{ __('pos.select_lic_file') }}</p>
                            </div>
                            
                            <div id="file-selected-container" style="display: none;">
                                <i class="bi bi-file-earmark-check-fill fs-1 text-success mb-3 d-block"></i>
                                <h6 class="fw-bold text-success mb-1" id="file-name-display">Filename.lic</h6>
                                <p class="text-muted small mb-0">{{ __('pos.ready_to_activate') ?? 'Ready to activate' }}</p>
                                <button type="button" class="btn btn-sm btn-link text-danger mt-2" onclick="resetUpload()">{{ __('pos.clear') }}</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg w-100 py-3 rounded-4 shadow-sm fw-bold border-0 text-dark">
                        <i class="bi bi-lightning-charge-fill me-2"></i> {{ __('pos.activate') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .license-option { border-width: 2px !important; transition: all 0.3s ease; }
    .license-option:hover { border-color: #46bfa3 !important; transform: translateY(-3px); }
    .license-option.active { border-color: #46bfa3 !important; background: rgba(70, 191, 163, 0.05); }
    .license-option.active i { color: #46bfa3 !important; }
    .cursor-pointer { cursor: pointer; }
    
    [dir="rtl"] .me-3 { margin-left: 1rem !important; margin-right: 0 !important; }
    [dir="rtl"] .ms-2 { margin-right: 0.5rem !important; margin-left: 0 !important; }
    [dir="rtl"] .border-start-0 { border-right-width: 0 !important; border-left-width: 1px !important; }
    [dir="rtl"] .border-end-0 { border-left-width: 0 !important; border-right-width: 1px !important; }
    [dir="rtl"] .ps-0 { padding-right: 0 !important; }
</style>

<script>
function selectLicense(type) {
    document.querySelectorAll('.license-option').forEach(el => el.classList.remove('active'));
    document.getElementById(type + '-box').classList.add('active');
    document.getElementById(type + '-radio').checked = true;
    
    if (type === 'paid') {
        $('#duration-container').slideDown();
    } else {
        $('#duration-container').slideUp();
    }
}

function updateFileName(input) {
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        document.getElementById('file-name-display').textContent = fileName;
        document.getElementById('upload-icon-container').style.display = 'none';
        document.getElementById('file-selected-container').style.display = 'block';
        document.getElementById('drop-zone').style.borderColor = '#46bfa3';
        document.getElementById('drop-zone').style.background = '#e8f5e9';
    }
}

function resetUpload() {
    document.getElementById('license_file').value = '';
    document.getElementById('upload-icon-container').style.display = 'block';
    document.getElementById('file-selected-container').style.display = 'none';
    document.getElementById('drop-zone').style.borderColor = '#cbd5e1';
    document.getElementById('drop-zone').style.background = '#f8fafc';
}
// Initial state
document.addEventListener('DOMContentLoaded', function() {
    selectLicense('trial');
});
</script>
@endsection

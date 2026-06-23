@extends('layouts.app')

@section('title', __('pos.license_manager'))

@section('content')
<div class="row justify-content-center animate__animated animate__fadeIn">
    <div class="col-lg-10">
        
        <!-- Header Section -->
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary rounded-4 p-3 me-3 shadow-sm">
                <i class="bi bi-shield-lock-fill text-white fs-3"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 text-primary">{{ __('pos.license_manager') }}</h3>
                <p class="text-muted mb-0 small">{{ __('pos.generate_license_keys_for_clients') ?? 'Tool for generating secure .lic files from client .req files' }}</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Side: Generator Form -->
            <div class="col-md-7">
                <div class="card border-0 shadow-lg overflow-hidden h-100" style="border-radius: 20px;">
                    <div class="card-header bg-white p-4 border-bottom border-light">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle-fill me-2 text-success"></i>{{ __('pos.generate_license') }}</h5>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('settings.license.generate') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold small text-secondary text-uppercase">{{ __('pos.client_name') }}</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-person"></i></span>
                                    <input type="text" name="client_name" class="form-control bg-light border-start-0 ps-0 fs-6" placeholder="{{ __('pos.client_name_placeholder') }}" value="{{ old('client_name') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold small text-secondary text-uppercase">{{ __('pos.device_id') }}</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-fingerprint"></i></span>
                                    <input type="text" name="device_id" id="device_id_input" class="form-control border-start-0 ps-0 fs-6 fw-bold text-primary" placeholder="XXXX-XXXX-XXXX" value="{{ old('device_id') }}">
                                </div>
                                <div class="mt-2">
                                    <label for="req_file" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i> {{ __('pos.upload_req_file') }}
                                    </label>
                                    <input type="file" name="req_file" id="req_file" class="d-none" onchange="this.form.submit()">
                                    <span class="text-muted small ms-2">{{ __('pos.auto_extract_device_id') ?? 'Or upload .req file to auto-extract' }}</span>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-semibold small text-secondary text-uppercase">{{ __('pos.expiry_date') }}</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="expiry_date" class="form-control border-start-0 ps-0 fs-6" required value="{{ old('expiry_date', date('Y-m-d', strtotime('+1 year'))) }}">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-4 shadow-sm fw-bold border-0 animate__animated animate__pulse animate__infinite" style="background: linear-gradient(135deg, #46bfa3 0%, #1E88E5 100%);">
                                <i class="bi bi-file-earmark-medical me-2"></i> {{ __('pos.generate_license') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side: Instructions & Result -->
            <div class="col-md-5">
                <!-- Instructions Card -->
                <div class="card border-0 shadow-lg mb-4" style="border-radius: 20px; background: rgba(255, 255, 255, 0.9);">
                    <div class="card-header bg-transparent p-4 border-0">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-info-circle-fill me-2 text-primary"></i>{{ __('pos.how_to_use') ?? 'How to use' }}</h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex mb-3">
                                <span class="badge bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 25px; height: 25px;">1</span>
                                <span class="small text-secondary">{{ __('pos.admin_inst_1') ?? 'Ask the client for their .req file or Device ID.' }}</span>
                            </li>
                            <li class="d-flex mb-3">
                                <span class="badge bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 25px; height: 25px;">2</span>
                                <span class="small text-secondary">{{ __('pos.admin_inst_2') ?? 'Upload the file above to auto-fill the details.' }}</span>
                            </li>
                            <li class="d-flex mb-3">
                                <span class="badge bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 25px; height: 25px;">3</span>
                                <span class="small text-secondary">{{ __('pos.admin_inst_3') ?? 'Set the expiry date and click Generate.' }}</span>
                            </li>
                            <li class="d-flex">
                                <span class="badge bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 25px; height: 25px;">4</span>
                                <span class="small text-secondary">{{ __('pos.admin_inst_4_new') ?? 'The system will download a .lic file. Send this file to the client.' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Security Note -->
                <div class="p-3 rounded-4 bg-warning-subtle text-warning-emphasis border border-warning-subtle d-flex align-items-start">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1 small">{{ __('pos.security_note') ?? 'Security Note' }}</h6>
                        <p class="mb-0 x-small" style="font-size: 0.75rem;">{{ __('pos.admin_security_warning') ?? 'Generated licenses are signed using your unique LICENSE_SECRET. Keep your secret key safe.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-mono { font-family: 'Courier New', Courier, monospace; }
    .transition-all { transition: all 0.3s ease; }
    .x-small { font-size: 0.8rem; }
    
    [dir="rtl"] .me-3 { margin-left: 1rem !important; margin-right: 0 !important; }
    [dir="rtl"] .me-2 { margin-left: 0.5rem !important; margin-right: 0 !important; }
    [dir="rtl"] .ms-2 { margin-right: 0.5rem !important; margin-left: 0 !important; }
    [dir="rtl"] .border-start-0 { border-right-width: 0 !important; border-left-width: 1px !important; }
    [dir="rtl"] .border-end-0 { border-left-width: 0 !important; border-right-width: 1px !important; }
    [dir="rtl"] .ps-0 { padding-right: 0 !important; }
    [dir="rtl"] .end-0 { left: 0 !important; right: auto !important; }
</style>

<script>
function copyKey() {
    var copyText = document.getElementById("licenseKey");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    
    $('#copyBadge').fadeIn().delay(2000).fadeOut();
    
    // Toast notification if available
    if (typeof toastr !== 'undefined') {
        toastr.success('{{ __("pos.key_copied") }}');
    }
}
</script>
@endsection

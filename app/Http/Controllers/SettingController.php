<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name_en' => 'required_without:company_name_ar|string|nullable',
            'company_name_ar' => 'required_without:company_name_en|string|nullable',
            'tax_number' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email' => 'nullable|email',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'currency_en' => 'nullable|string',
            'currency_ar' => 'nullable|string',
            'default_tax' => 'required|numeric|min:0|max:100',
        ]);

        $setting = Setting::first();
        
        $data = [
            'company_name' => [
                'en' => $request->company_name_en,
                'ar' => $request->company_name_ar,
            ],
            'company_address' => [
                'en' => $request->company_address_en,
                'ar' => $request->company_address_ar,
            ],
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email,
            'tax_number' => $request->tax_number,
            'registration_number' => $request->registration_number,
            'default_tax' => $request->default_tax,
            'footer_text' => [
                'en' => $request->footer_text_en,
                'ar' => $request->footer_text_ar,
            ],
            'currency' => [
                'en' => $request->currency_en,
                'ar' => $request->currency_ar,
            ],
        ];

        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($setting->company_logo && file_exists(public_path('storage/' . $setting->company_logo))) {
                @unlink(public_path('storage/' . $setting->company_logo));
            }
            
            $path = $request->file('company_logo')->store('company', 'public');
            $data['company_logo'] = $path;
        }

        $setting->update($data);

        return redirect()->back()->with('success', __('pos.settings_updated_successfully'));
    }

    public function showLicenseForm()
    {
        $setting = Setting::first();
        $deviceId = $this->getDeviceId();
        $licenseStatus = $this->getLicenseStatus($setting);
        return view('settings.license', compact('deviceId', 'setting', 'licenseStatus'));
    }

    public function activateLicense(Request $request)
    {
        if (!$request->hasFile('license_file')) {
            return redirect()->back()->with('error', __('pos.license_file_required'));
        }

        try {
            $file = $request->file('license_file');
            $content = file_get_contents($file->path());
            $licenseData = json_decode(base64_decode($content), true);

            if (!$licenseData || !isset($licenseData['signature']) || !isset($licenseData['data'])) {
                return redirect()->back()->with('error', __('pos.invalid_license_format'));
            }

            // Verify Signature
            if (!$this->verifySignature($licenseData['data'], $licenseData['signature'])) {
                return redirect()->back()->with('error', __('pos.license_tampered'));
            }

            $data = $licenseData['data'];
            $deviceId = $data['device_id'] ?? null;
            $expiryDate = $data['expiry_date'] ?? null;

            if ($deviceId !== $this->getDeviceId()) {
                return redirect()->back()->with('error', __('pos.license_device_mismatch'));
            }

            if (strtotime($expiryDate) < time()) {
                return redirect()->back()->with('error', __('pos.license_expired_key'));
            }

            $setting = Setting::first();
            $setting->update([
                'license_key' => $content, // Store the whole file content as the key
                'license_expires_at' => $expiryDate,
            ]);

            return redirect()->back()->with('success', __('pos.license_activated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('pos.error_reading_license_file'));
        }
    }

    private function verifySignature($data, $signature)
    {
        $secret = env('LICENSE_SECRET', 'DigitalAgePosSystemSecretKey2026!#');
        $expectedSignature = hash_hmac('sha256', json_encode($data), $secret);
        return hash_equals($expectedSignature, $signature);
    }

    private function decryptLicenseKey($key)
    {
        try {
            $secret = env('LICENSE_SECRET', 'DigitalAgePosSystemSecretKey2026!#');
            $cipher = "AES-256-CBC";
            $key_data = base64_decode($key);
            $iv_size = openssl_cipher_iv_length($cipher);
            $iv = substr($key_data, 0, $iv_size);
            $encrypted = substr($key_data, $iv_size);
            
            return openssl_decrypt($encrypted, $cipher, $secret, 0, $iv);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getLicenseStatus($setting)
    {
        if (!$setting->license_key || !$setting->license_expires_at) {
            // Check for automatic 7-day trial
            $trialDays = 7;
            $installationDate = $setting->created_at ?: now();
            if (now()->lessThan($installationDate->copy()->addDays($trialDays))) {
                return 'trial';
            }
            return 'inactive';
        }

        if ($setting->license_expires_at->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    public function generateLicenseRequest(Request $request)
    {
        $request->validate([
            'device_name' => 'required|string|max:100',
            'full_name'   => 'required|string|max:100',
            'email'       => 'nullable|email|max:100',
            'license_type' => 'required|in:trial,paid',
            'duration'    => 'required_if:license_type,paid|nullable|integer|min:1',
        ]);

        $data = [
            'request_date' => now()->toDateTimeString(),
            'device_name'  => $request->device_name,
            'device_id'    => $this->getDeviceId(),
            'user_name'    => $request->full_name,
            'user_email'   => $request->email,
            'license_type' => $request->license_type,
            'duration'     => $request->license_type === 'trial' ? 7 : $request->duration,
            'version'      => '1.0.0',
        ];

        $content = base64_encode(json_encode($data));
        $filename = 'license_request_' . str_replace(' ', '_', $request->device_name) . '_' . date('Ymd_His') . '.req';

        return response($content)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function getDeviceId()
    {
        $host = gethostname();
        $os = php_uname('s');
        $envKey = env('APP_KEY', 'default-salt');
        $hash = substr(md5($host . $os . $envKey), 0, 12);
        return strtoupper(implode('-', str_split($hash, 4)));
    }

    /**
     * Admin: Show License Management Page
     */
    public function licenseManager()
    {
        return view('settings.license_manager');
    }

    /**
     * Admin: Generate License Key
     */
    public function generateLicense(Request $request)
    {
        $deviceId = $request->device_id;
        $licenseType = 'paid';
        $duration = 365;

        // Try to extract from uploaded .req file if present
        if ($request->hasFile('req_file')) {
            try {
                $content = file_get_contents($request->file('req_file')->path());
                $decoded = json_decode(base64_decode($content), true);
                if (isset($decoded['device_id'])) {
                    $deviceId = $decoded['device_id'];
                    $licenseType = $decoded['license_type'] ?? 'paid';
                    $duration = $decoded['duration'] ?? 365;
                } else {
                    return redirect()->back()->with('error', __('pos.invalid_req_file'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('pos.error_reading_license_file'));
            }
        }

        $request->validate([
            'device_id' => $request->hasFile('req_file') ? 'nullable|string' : 'required|string',
            'expiry_date' => 'required|date|after:today',
        ]);

        if (!$deviceId) {
            return redirect()->back()->with('error', __('pos.device_id_required'));
        }

        $licenseData = [
            'device_id' => $deviceId,
            'expiry_date' => $request->expiry_date,
            'license_type' => $licenseType,
            'client_name' => $request->client_name ?? 'Digital Age Client',
            'generated_at' => now()->toDateTimeString(),
        ];

        // Sign the data
        $secret = env('LICENSE_SECRET', 'DigitalAgePosSystemSecretKey2026!#');
        $signature = hash_hmac('sha256', json_encode($licenseData), $secret);

        $finalData = [
            'data' => $licenseData,
            'signature' => $signature
        ];

        $content = base64_encode(json_encode($finalData));
        $filename = 'license_' . str_replace(' ', '_', ($request->client_name ?? 'client')) . '.lic';

        return response($content)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function encryptLicenseKey($plainText)
    {
        try {
            $secret = env('LICENSE_SECRET', 'DigitalAgePosSystemSecretKey2026!#');
            $cipher = "AES-256-CBC";
            $iv_size = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($iv_size);
            $encrypted = openssl_encrypt($plainText, $cipher, $secret, 0, $iv);
            
            return base64_encode($iv . $encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }
}

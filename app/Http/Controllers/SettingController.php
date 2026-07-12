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
        
        $company_name_en = $request->company_name_en ?: $request->company_name_ar;
        $company_name_ar = $request->company_name_ar ?: $request->company_name_en;

        $currency_en = $request->currency_en ?: $request->currency_ar;
        $currency_ar = $request->currency_ar ?: $request->currency_en;

        $company_address_en = $request->company_address_en ?: $request->company_address_ar;
        $company_address_ar = $request->company_address_ar ?: $request->company_address_en;

        $footer_text_en = $request->footer_text_en ?: $request->footer_text_ar;
        $footer_text_ar = $request->footer_text_ar ?: $request->footer_text_en;

        $data = [
            'company_name' => [
                'en' => $company_name_en,
                'ar' => $company_name_ar,
            ],
            'company_address' => [
                'en' => $company_address_en,
                'ar' => $company_address_ar,
            ],
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email,
            'tax_number' => $request->tax_number,
            'registration_number' => $request->registration_number,
            'default_tax' => $request->default_tax,
            'footer_text' => [
                'en' => $footer_text_en,
                'ar' => $footer_text_ar,
            ],
            'currency' => [
                'en' => $currency_en,
                'ar' => $currency_ar,
            ],
        ];

        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($setting->company_logo && file_exists(public_path('storage/' . $setting->company_logo))) {
                @unlink(public_path('storage/' . $setting->company_logo));
            }
            
            $path = $request->file('company_logo')->store('company', 'public');
            $data['company_logo'] = $path;
        } elseif ($request->boolean('remove_logo')) {
            // Delete logo file and database field
            if ($setting->company_logo && file_exists(public_path('storage/' . $setting->company_logo))) {
                @unlink(public_path('storage/' . $setting->company_logo));
            }
            $data['company_logo'] = null;
        }

        $setting->update($data);

        return redirect()->back()->with('success', __('pos.settings_updated_successfully'));
    }
}

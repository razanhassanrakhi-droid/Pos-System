@extends('admin.layouts.app')

@section('title', __('massage.dt_add_contact') ?? 'Add Contact Details')
@section('header', __('massage.dt_add_contact') ?? 'Add Contact Details')

@section('content')
<div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 focus-within:ring-2 focus-within:ring-blue-100 transition-all max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-800">{{ __('massage.form_org_contact_info') }}</h3>
        <a href="{{ route('admin.contacts.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
            <i class="fas {{ app()->getLocale() == 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i> <span>{{ __('massage.form_back_to_list') }}</span>
        </a>
    </div>

    <form action="{{ route('admin.contacts.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <!-- Phone Number -->
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-600">{{ __('massage.dt_phone') }} <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                        <i class="fas fa-phone-alt text-gray-400"></i>
                    </div>
                    <input type="text" name="phone_number" required placeholder="+1234567890" class="w-full {{ app()->getLocale() == 'ar' ? 'pr-10 text-left placeholder-right' : 'pl-10 text-left' }} p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" dir="ltr">
                </div>
            </div>

            <!-- Facebook URL -->
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-600">{{ __('massage.form_facebook_url') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                        <i class="fab fa-facebook text-blue-600"></i>
                    </div>
                    <input type="url" name="facebook_url" placeholder="https://facebook.com/yourpage" class="w-full {{ app()->getLocale() == 'ar' ? 'pr-10 text-left placeholder-right' : 'pl-10 text-left' }} p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" dir="ltr">
                </div>
            </div>

            <!-- TikTok URL -->
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-600">{{ __('massage.form_tiktok_url') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                        <i class="fab fa-tiktok text-black"></i>
                    </div>
                    <input type="url" name="tiktok_url" placeholder="https://tiktok.com/@yourprofile" class="w-full {{ app()->getLocale() == 'ar' ? 'pr-10 text-left placeholder-right' : 'pl-10 text-left' }} p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" dir="ltr">
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-gray-100">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> <span>{{ __('massage.form_save_contact') }}</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

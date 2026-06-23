@extends('admin.layouts.app')

@section('title', __('massage.dt_add_user') ?? 'Add System User')
@section('header', __('massage.dt_add_user') ?? 'Add System User')

@section('content')
<div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 focus-within:ring-2 focus-within:ring-blue-100 transition-all max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-800">{{ __('massage.form_user_details') ?? 'User Details' }}</h3>
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
            <i class="fas {{ app()->getLocale() == 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i> <span>{{ __('massage.form_back_to_list') }}</span>
        </a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <!-- Name -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h4 class="font-semibold text-gray-700 mb-4 border-b pb-2"><i class="fas fa-id-card {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('massage.dt_name') ?? 'Name' }}</h4>
                <div>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                </div>
                
                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-600">{{ __('massage.user_role') ?? 'User Role' }} <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('massage.role_admin') ?? 'Admin (Full Access)' }}</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>{{ __('massage.role_employee') ?? 'Employee (Content Only)' }}</option>
                    </select>
                </div>
            </div>

            <!-- Email -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h4 class="font-semibold text-gray-700 mb-4 border-b pb-2"><i class="fas fa-envelope {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('massage.dt_email') ?? 'Email Address' }}</h4>
                <div>
                    <input type="email" name="email" value="{{ old('email') }}" required dir="ltr" class="w-full p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                </div>
            </div>


            <!-- Password -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h4 class="font-semibold text-gray-700 mb-4 border-b pb-2"><i class="fas fa-lock {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('massage.form_password_section') ?? 'Password Setup' }}</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-600">{{ __('massage.form_password') ?? 'Password' }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required dir="ltr" class="w-full p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white {{ app()->getLocale() == 'ar' ? 'text-right pl-10' : 'text-left pr-10' }}">
                            <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center text-gray-400 hover:text-blue-600 focus:outline-none transition-colors">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('massage.form_password_hint') ?? 'Must be at least 8 characters.' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-600">{{ __('massage.form_confirm_password') ?? 'Confirm Password' }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required dir="ltr" class="w-full p-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white {{ app()->getLocale() == 'ar' ? 'text-right pl-10' : 'text-left pr-10' }}">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center text-gray-400 hover:text-blue-600 focus:outline-none transition-colors">
                                <i class="fas fa-eye" id="password_confirmation-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-gray-100">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-md transition flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> <span>{{ __('massage.form_save_user') ?? 'Save User' }}</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush

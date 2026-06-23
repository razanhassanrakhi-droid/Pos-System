@extends('admin.layouts.app')

@section('title', __('massage.dashboard_overview'))
@section('header', __('massage.dashboard_overview'))

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Projects Stat Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center transition hover:shadow-md">
        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-2xl mb-4">
            <i class="fas fa-project-diagram"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-1">{{ $stats['projects_count'] }}</h3>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('massage.total_projects') }}</p>
    </div>

    <!-- News Stat Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center transition hover:shadow-md">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mb-4">
            <i class="far fa-newspaper"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-1">{{ $stats['news_count'] }}</h3>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('massage.total_news') }}</p>
    </div>

    <!-- Need to pass Members and Inquiries from AdminController once their models are confirmed -->
    <!-- Members Stat Card Placeholder -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center transition hover:shadow-md">
        <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center text-2xl mb-4">
            <i class="fas fa-users"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-1">...</h3>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('massage.team_members') }}</p>
    </div>

    <!-- Inquiries Stat Card Placeholder -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center transition hover:shadow-md">
        <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-2xl mb-4">
            <i class="fas fa-envelope-open-text"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-1">...</h3>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('massage.inquiries') }}</p>
    </div>

</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-600 mt-12">
    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-laptop-code text-3xl text-gray-300"></i>
    </div>
    <h4 class="text-xl font-bold text-gray-800 mb-2">{{ __('massage.welcome_cms') }}</h4>
    <p class="max-w-md mx-auto">{{ __('massage.cms_desc') }}</p>
</div>
@endsection

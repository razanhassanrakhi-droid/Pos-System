<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('massage.title') ?? 'Admin Dashboard')</title>
    <!-- Tailwind CSS v4 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Cairo for Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Cairo', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        
        .sidebar { transition: transform 0.3s ease-in-out; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    @include('admin.layouts.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-full overflow-hidden w-full relative z-0">
        <!-- Top Header -->
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <button id="sidebarToggle" class="text-gray-600 hover:text-blue-600 focus:outline-none lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-bold text-gray-800 hidden sm:block">@yield('header', __('massage.title') ?? 'Dashboard')</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Language Toggle -->
                <div class="relative">
                    <a href="{{ url('change-language/' . (app()->getLocale() == 'ar' ? 'en' : 'ar')) }}" 
                       class="text-gray-600 hover:text-blue-600 text-sm font-semibold flex items-center gap-1">
                        <i class="fas fa-globe"></i>
                        {{ app()->getLocale() == 'ar' ? 'English' : 'عربي' }}
                    </a>
                </div>

                <!-- User Dropdown (Simulated) -->
                <div class="relative flex items-center gap-2 border-s border-gray-300 ps-4">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        A
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-semibold" title="{{ __('massage.logout') ?? 'Logout' }}">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-10 hidden lg:hidden"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const isRtl = document.documentElement.dir === 'rtl';
            
            function toggleSidebar() {
                const transformClass = isRtl ? 'translate-x-full' : '-translate-x-full';
                sidebar.classList.toggle(transformClass);
                sidebarBackdrop.classList.toggle('hidden');
            }

            if(sidebarToggle && sidebar && sidebarBackdrop) {
                sidebarToggle.addEventListener('click', toggleSidebar);
                sidebarBackdrop.addEventListener('click', toggleSidebar);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

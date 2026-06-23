<aside id="sidebar" class="bg-blue-800 text-white w-64 flex-shrink-0 flex flex-col h-full absolute lg:relative z-20 
    transition-transform duration-300 ease-in-out {{ app()->getLocale() == 'ar' ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0' }}">
    
    <!-- Logo/Brand -->
    <div class="h-16 flex items-center px-6 border-b border-blue-700 bg-blue-900 justify-between">
        <a href="{{ url('/') }}" target="_blank" class="flex {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} items-center gap-2 text-xl font-bold text-white hover:text-blue-200" title="Visit Site">
            <img src="{{ asset('Images/y.png') }}" class="w-10 h-10 object-contain bg-white p-1 rounded-md shadow-sm" alt="Mama Africa Logo">
            <span class="truncate">{{ app()->getLocale() == 'ar' ? 'ماما أفريكا' : 'Mama Africa' }}</span>
        </a>
        
        <button id="closeSidebar" class="lg:hidden text-white hover:text-blue-200 focus:outline-none" onclick="document.getElementById('sidebarToggle').click()">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.overview') }}</span>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-blue-300 uppercase tracking-wider">{{ __('massage.content_management') }}</p>
        </div>

        <a href="{{ route('admin.news.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.news.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="far fa-newspaper w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.news') }}</span>
        </a>

        <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.projects.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="fas fa-project-diagram w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.projects') }}</span>
        </a>

        <a href="{{ route('admin.testimonials.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.testimonials.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="far fa-comments w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.testimon') }}</span>
        </a>

        <a href="{{ route('admin.members.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.members.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="fas fa-users w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.team_members') }}</span>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-blue-300 uppercase tracking-wider">{{ __('massage.contacts_inquiries') }}</p>
        </div>

        <a href="{{ route('admin.contacts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.contacts.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="fas fa-address-book w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.contacts_info') }}</span>
        </a>

        <a href="{{ route('admin.inquiries.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.inquiries.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="fas fa-envelope-open-text w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.inquiries') }}</span>
        </a>

        @if(auth()->user()->isAdmin())
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-blue-300 uppercase tracking-wider">{{ __('massage.users_management') ?? 'Users' }}</p>
        </div>

        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') && !request()->routeIs('admin.users.edit') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="fas fa-user-shield w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.users') ?? 'System Users' }}</span>
        </a>
        @else
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-blue-300 uppercase tracking-wider">{{ __('massage.settings') ?? 'Settings' }}</p>
        </div>
        @endif

        <a href="{{ route('admin.users.edit', auth()->id()) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.users.edit') && request()->route('user')->id === auth()->id() ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="fas fa-user-cog w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('massage.my_profile') ?? 'My Profile' }}</span>
        </a>

        <!-- Warranty Link -->
        <a href="{{ route('warranties.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('warranties.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
            <i class="fas fa-shield-alt w-5 text-center"></i>
            <span class="font-medium flex-1">{{ __('pos.warranty_management') }}</span>
        </a>
    </nav>
    
    <!-- Footer Menu -->
    <div class="p-4 border-t border-blue-700">
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100">
            <i class="fas fa-user-shield w-5 text-center"></i>
            <span class="font-medium text-sm flex-1">
                @auth
                    {{ auth()->user()->full_name }} ({{ __('massage.role_' . auth()->user()->role) ?? auth()->user()->role }})
                @else
                    Guest
                @endauth
            </span>
        </div>
    </div>
</aside>

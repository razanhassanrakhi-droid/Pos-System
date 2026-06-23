<!-- Header -->
<header class="bg-blue-600 text-white shadow-lg py-4 px-6 mb-6">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo -->
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-12 h-12 fill-white">
                <path d="M96 96L96 544L544 544L544 96L96 96zM412.5 421.2L320 509.9L227.5 421.2L292 237.2L227.5 150.6L412.4 150.6L348 237.2L412.5 421.2z"/>
            </svg>
            <span>{{ auth()->user()->full_name ?? 'Guest' }}</span>
        </h1>
<br>
        <!-- Desktop Nav -->
<nav class="hidden lg:flex items-center gap-8 ml-auto text-white">
    <a href="{{ route('admin.projects.index') }}#projects">
        {{ __('massage.projects') }}
    </a>

    <a href="{{ route('admin.members.index')}}#members">
        {{ __('massage.members') }}
    </a>

    <!-- <a href="#donate"> 
        {{ __('massage.donate') }}
    </a>-->
   
    <a href="{{ route('inquiries.create') }}#inquiries">
     {{ __('massage.inquiries') }}

    </a>

    <a href="{{ route('admin.news.index') }}#news">
        {{ __('massage.news') }}
    </a>

    <a href="{{ route('admin.contacts.index')}}#contacts">
        {{ __('massage.conta') }}
    </a>

    <a href="{{ route('admin.testimonials.index') }}#testimonials">
        {{ __('massage.testimon') }}
    </a>
     @auth
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
            class="flex items-center gap-2 bg-white/10 hover:bg-white/20 
                   text-white text-sm font-semibold py-1.5 px-4 
                   rounded-full border border-white/40 
                   transition-all shadow-sm backdrop-blur-sm">

            <svg xmlns="http://www.w3.org/2000/svg" 
                 fill="none" viewBox="0 0 24 24" 
                 stroke-width="1.5" stroke="currentColor" 
                 class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" 
                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-7.5A2.25 2.25 0 0 0 3.75 5.25v13.5A2.25 2.25 0 0 0 6 21h7.5A2.25 2.25 0 0 0 15.75 18.75V15M9 12h12m0 0-3-3m3 3-3 3" />
            </svg>

            {{ __('pos.logout') }}
        </button>
    </form>
    @endauth


</nav>
        <!-- Mobile Button -->
        <button class="lg:hidden ml-auto text-white text-2xl" onclick="toggleMenu()">☰</button>

        <!-- Language Button -->
        <div class="flex items-center ml-4">
           <button onclick="toggleLanguage()" id="langBtn" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold py-1.5 px-4 rounded-full border border-white/30 transition-all shadow-sm backdrop-blur-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18ZM12 3v18M3 12h18M5.625 5.625c2.5 1.5 3.375 6.375 3.375 6.375s.875 4.875 3.375 6.375M18.375 5.625c-2.5 1.5-3.375-3.375 6.375-3.375 6.375"/>
    </svg>
    <span id="langText">
        {{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}
    </span>
</button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden bg-black/90 text-white px-6 py-6 space-y-4">
        <a href="{{ route('admin.projects.index') }}#projects" class="block"> {{ __('massage.projects') }}</a>
        <a href="{{ route('admin.members.index')}}#members" class="block">{{ __('massage.members') }}</a>
        <a href="{{ route('admin.news.index') }}#donate" class="block"> {{ __('massage.donate') }}</a>
        <a href="{{ route('admin.news.index') }}" class="block">  {{ __('massage.news') }}</a>
        <a href="{{ route('admin.contacts.index')}}#contacts" class="block">{{ __('massage.conta') }}</a>
        <a href="{{ route('admin.testimonials.index') }}#testimonials" class="block"> {{ __('massage.testimon') }}</a>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
        class="w-full text-left bg-white/10 hover:bg-white/20 
               text-white py-2 px-4 rounded-lg 
               border border-white/40 transition">
        {{ __('pos.logout') }}
    </button>
</form>

    </div>
</header>

<!-- Scripts -->
@push('scripts')
<script>
function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}
</script>
<script>
function changeLanguage(lang){
    window.location = "/change-language/" + lang;
}

function toggleLanguage(){
    let currentLang = "{{ app()->getLocale() }}";

    if(currentLang === 'ar'){
        changeLanguage('en');
    }else{
        changeLanguage('ar');
    }
}
</script>
@endpush

<!-- 🌍 Language Dropdown -->
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 relative">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route(Auth::user()->role . '.dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route(Auth::user()->role . '.dashboard') }}" 
                                :active="request()->routeIs(Auth::user()->role . '.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side (Language Selector + User Profile) -->
            <div class="flex items-center space-x-4">
                <!-- 🌍 Language Dropdown -->
                <div x-data="{ openLang: false }" class="relative">
                    <button @click="openLang = !openLang"
                            class="px-2 py-1 border rounded bg-white text-gray-700 text-sm">
                        {{ strtoupper(app()->getLocale()) }}
                    </button>

                    <div x-show="openLang" @click.away="openLang = false"
                         class="absolute right-0 mt-2 w-32 bg-white rounded shadow-lg z-50">
                        <a href="{{ route('language.switch', 'en') }}"
                           class="block px-4 py-2 hover:bg-gray-100 text-sm">
                            🇺🇸 English
                        </a>
                        <a href="{{ route('language.switch', 'tl') }}"
                           class="block px-4 py-2 hover:bg-gray-100 text-sm">
                            🇵🇭 Tagalog
                        </a>
                    </div>
                </div>

                <!-- 🧑 User Profile Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover" 
                                     src="{{ Auth::user()->profile_photo_url }}" 
                                     alt="{{ Auth::user()->name }}">
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Settings -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('My Profile') }}
                            </x-dropdown-link>

                            <x-dropdown-link href="{{ route('buyer.orders.history') }}">
                                {{ __('My Purchases') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" 
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" 
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu for Mobile -->
    <div :class="{ 'block': open, 'hidden': !open }" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route(Auth::user()->role . '.dashboard') }}" 
                                   :active="request()->routeIs(Auth::user()->role . '.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="shrink-0 me-3">
                    <img class="h-10 w-10 rounded-full object-cover"
                         src="{{ Auth::user()->profile_photo_url }}"
                         alt="{{ Auth::user()->name }}">
                </div>

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}">
                    {{ __('My Profile') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('buyer.orders.history') }}">
                    {{ __('My Purchases') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

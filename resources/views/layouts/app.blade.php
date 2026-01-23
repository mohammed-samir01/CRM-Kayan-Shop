<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="bg-gray-50 font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex relative">
        @auth
            <!-- Sidebar -->
            <aside 
                class="w-64 bg-indigo-900 text-white min-h-screen flex flex-col shadow-xl fixed inset-y-0 right-0 z-30 transition-transform duration-300 lg:static lg:inset-auto" 
                :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
                id="sidebar">
                <div class="p-6 border-b border-indigo-800 flex items-center justify-between lg:justify-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold tracking-wider">
                        CRM
                    </a>
                    <!-- Mobile Close Button -->
                    <button @click="sidebarOpen = false" class="lg:hidden text-indigo-300 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <nav class="flex-1 py-6 space-y-2 px-3 overflow-y-auto">
                    @can('view dashboard')
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-800 text-white shadow-sm' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        لوحة التحكم
                    </a>
                    @endcan
                    
                    @can('view leads')
                    <a href="{{ route('leads.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('leads.*') ? 'bg-indigo-800 text-white shadow-sm' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        العملاء المتوقعين
                    </a>
                    @endcan
                    
                    @can('view orders')
                    <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('orders.*') ? 'bg-indigo-800 text-white shadow-sm' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        الطلبات
                    </a>
                    @endcan
                    
                    @can('view campaigns')
                    <a href="{{ route('campaigns.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('campaigns.*') ? 'bg-indigo-800 text-white shadow-sm' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        الحملات الإعلانية
                    </a>
                    @endcan

                    @can('view products')
                    <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'bg-indigo-800 text-white shadow-sm' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        المنتجات
                    </a>
                    @endcan

                    @can('manage users')
                        <div class="pt-4 pb-2">
                            <div class="border-t border-indigo-800"></div>
                        </div>
                        <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-800 text-white shadow-sm' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            المستخدمين
                        </a>
                    @endcan
                    @role('admin')
                        <a href="{{ route('roles.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('roles.*') ? 'bg-indigo-800 text-white shadow-sm' : 'text-indigo-100 hover:bg-indigo-800 hover:text-white' }}">
                            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            الأدوار والصلاحيات
                        </a>
                    @endrole
                </nav>

                <div class="p-4 border-t border-indigo-800 bg-indigo-950">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-indigo-700 flex items-center justify-center text-white font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs text-indigo-300 hover:text-white transition-colors truncate">
                                    تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
            
            <!-- Mobile Sidebar Overlay -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 lg:hidden"
                style="display: none;">
            </div>
        @endauth

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
            @if(isset($header))
                <header class="bg-white shadow-sm z-10 sticky top-0">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="flex items-center">
                            @auth
                                <!-- Mobile Hamburger -->
                                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden ml-4 text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                            @endauth
                            {{ $header }}
                        </div>
                        
                        @auth
                            <div class="flex items-center gap-4">
                                <!-- Notifications Dropdown -->
                                <x-dropdown align="right" width="w-96">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center p-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 bg-white hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150 relative">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                            @if(auth()->user()->unreadNotifications->count() > 0)
                                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">
                                                    {{ auth()->user()->unreadNotifications->count() }}
                                                </span>
                                            @endif
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                            <span class="text-sm font-bold text-gray-700">التنبيهات</span>
                                            @if(auth()->user()->unreadNotifications->count() > 0)
                                                <form action="{{ route('notifications.readAll') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 whitespace-nowrap transition-colors">تحديد الكل كمقروء</button>
                                                </form>
                                            @endif
                                        </div>
                                        
                                        <div class="max-h-96 overflow-y-auto">
                                            @forelse(auth()->user()->unreadNotifications as $notification)
                                                <x-dropdown-link :href="route('notifications.read', $notification->id)" class="flex flex-col items-start border-b border-gray-50 py-3 hover:bg-gray-50">
                                                    <span class="font-medium text-sm leading-relaxed {{ isset($notification->data['color']) && $notification->data['color'] == 'green' ? 'text-green-600' : 'text-gray-900' }}">
                                                        {{ $notification->data['message'] ?? 'New Notification' }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                                                </x-dropdown-link>
                                            @empty
                                                <div class="px-4 py-6 text-sm text-gray-500 text-center">لا توجد تنبيهات جديدة</div>
                                            @endforelse
                                        </div>
                                    </x-slot>
                                </x-dropdown>

                                <!-- User Dropdown -->
                                <x-dropdown align="left" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold ml-2">
                                                    {{ substr(auth()->user()->name, 0, 1) }}
                                                </div>
                                                <div class="hidden sm:block">{{ auth()->user()->name }}</div>
                                            </div>

                                            <div class="mr-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">
                                            {{ __('الملف الشخصي') }}
                                        </x-dropdown-link>

                                        <!-- Authentication -->
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf

                                            <x-dropdown-link :href="route('logout')"
                                                    onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                                {{ __('تسجيل الخروج') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endauth
                    </div>
                </header>
            @endif

            <div class="flex-1 overflow-auto">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @if(session('success'))
                            <div class="mb-4 bg-green-50 border-r-4 border-green-500 p-4 rounded-md shadow-sm">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="mr-3">
                                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 bg-red-50 border-r-4 border-red-500 p-4 rounded-md shadow-sm">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="mr-3">
                                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </div>
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>

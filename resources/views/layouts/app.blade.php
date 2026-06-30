<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased font-sans">
        <div class="flex min-h-screen" x-data="{ sidebarCollapsed: false }">
            <!-- Sidebar -->
            <aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" class="bg-slate-900 text-slate-300 fixed inset-y-0 left-0 flex flex-col border-r border-slate-800 z-30 transition-all duration-300">
                <!-- Sidebar Header -->
                <div class="h-16 flex items-center bg-slate-950 border-b border-slate-800 relative transition-all duration-300" :class="sidebarCollapsed ? 'px-4 justify-center' : 'px-5 justify-between'">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="p-2 bg-[#2C5EAD] rounded-xl text-white flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                <path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/>
                                <path d="M8 15a6 6 0 0 0 6 6h0a6 6 0 0 0 6-6v-4"/>
                                <circle cx="20" cy="10" r="2"/>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-lg font-bold text-white tracking-tight whitespace-nowrap">Klinik <span class="text-[#7EB3E8]">HAE</span></span>
                    </div>

                    <!-- Toggle Button -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="absolute -right-3 top-5 bg-slate-800 border border-slate-700 text-slate-400 hover:text-white rounded-full p-1 z-40 transition-colors hidden md:block">
                        <svg x-show="!sidebarCollapsed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <svg x-cloak x-show="sidebarCollapsed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar Nav Menu -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto overflow-x-hidden">
                    <!-- Dashboard (Semua Role) -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-4'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
                    </a>

                    <!-- Manajemen Dokter (Admin) -->
                    @if(auth()->user()->isAdmin())
                    <a href="/doctors" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-4'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Manajemen Dokter</span>
                    </a>
                    @endif

                    <!-- Appointments (Admin, Receptionist, Doctor) -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isReceptionist() || auth()->user()->isDoctor())
                    <a href="/appointments" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-4'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Appointments</span>
                    </a>
                    @endif

                    <!-- Rekam Medis (Admin, Doctor) -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
                    <a href="{{ route('patients.search') }}" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('patients.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-4'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Rekam Medis</span>
                    </a>
                    @endif

                    <!-- POS / Kasir (Admin, Cashier) -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isCashier())
                    <a href="{{ route('pos.index') }}" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pos.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-4'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">POS / Kasir</span>
                    </a>
                    @endif

                    <!-- Manajemen Pengguna (Admin) -->
                    @if(auth()->user()->isAdmin())
                    <a href="/users" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-800 hover:text-white" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-4'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Manajemen Pengguna</span>
                    </a>

                    <!-- Laporan (Admin) -->
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}" :class="sidebarCollapsed ? 'justify-center px-0' : 'px-4'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Laporan</span>
                    </a>
                    @endif
                </nav>

                <!-- Sidebar Footer -->
                <div class="bg-slate-950 border-t border-slate-800 flex items-center gap-3 overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'p-4 justify-center' : 'p-4'">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div :class="sidebarCollapsed ? 'ml-20' : 'ml-64'" class="flex-1 flex flex-col min-h-screen transition-all duration-300">
                <!-- Header -->
                <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-20">
                    <div class="flex items-center gap-2.5">
                        <div class="p-1.5 bg-[#2C5EAD] rounded-xl text-white flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                <path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/>
                                <path d="M8 15a6 6 0 0 0 6 6h0a6 6 0 0 0 6-6v-4"/>
                                <circle cx="20" cy="10" r="2"/>
                            </svg>
                        </div>
                        <span class="text-base font-bold text-slate-800 tracking-tight">Klinik <span class="text-[#2C5EAD]">HAE</span></span>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-slate-700">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Content Body -->
                <main class="flex-1 p-8">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>
            </div>
        </div>

        @fluxScripts
    </body>
</html>

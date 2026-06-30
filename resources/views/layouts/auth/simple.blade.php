<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased font-sans flex flex-col items-center justify-center p-6 md:p-10">
        <!-- Logo Header -->
        <div class="mb-8 z-10">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-3" wire:navigate>
                <div class="p-3 bg-[#2C5EAD] rounded-xl text-white shadow-md shadow-[#2C5EAD]/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10">
                        <path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/>
                        <path d="M8 15a6 6 0 0 0 6 6h0a6 6 0 0 0 6-6v-4"/>
                        <circle cx="20" cy="10" r="2"/>
                    </svg>
                </div>
                <span class="text-3xl font-bold text-slate-800 tracking-tight">Klinik <span class="text-[#2C5EAD]">HAE</span></span>
            </a>
        </div>

        <!-- Main Card Area -->
        <div class="relative w-full max-w-md z-10">
            <!-- The Card -->
            <div class="bg-[#e2e4e9] rounded-2xl shadow-xl shadow-slate-300/50 border border-white/50 overflow-hidden p-8 backdrop-blur-sm">
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>



        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>

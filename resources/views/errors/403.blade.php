<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 flex flex-col justify-center items-center p-6 text-slate-900 antialiased font-sans">
        <div class="max-w-md w-full bg-white border border-slate-200 shadow-xl rounded-2xl p-8 text-center space-y-6">
            <!-- Alert Icon -->
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 text-red-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <!-- Message -->
            <div class="space-y-2">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">403</h1>
                <h2 class="text-xl font-semibold text-slate-800">Akses Ditolak</h2>
                <p class="text-sm text-slate-500">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            </div>
            
            <!-- Action Button -->
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </body>
</html>

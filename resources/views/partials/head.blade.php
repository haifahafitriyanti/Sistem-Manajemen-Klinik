<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    @php
        $routeName = request()->route()?->getName() ?? '';
        $menuName = match(true) {
            str_starts_with($routeName, 'dashboard') => '',
            str_starts_with($routeName, 'doctors') => 'Manajemen Dokter',
            str_starts_with($routeName, 'appointments') => 'Appointments',
            str_starts_with($routeName, 'patients') => 'Rekam Medis',
            str_starts_with($routeName, 'pos') => 'POS / Kasir',
            str_starts_with($routeName, 'users') => 'Manajemen Pengguna',
            str_starts_with($routeName, 'reports') => 'Laporan',
            str_starts_with($routeName, 'profile') || str_starts_with($routeName, 'settings') => 'Settings',
            default => ''
        };
        
        $pageTitle = $title ?? $menuName;
        
        if (filled($pageTitle)) {
            echo 'Klinik HAE - ' . $pageTitle;
        } else {
            echo 'Klinik HAE';
        }
    @endphp
</title>

<link rel="icon" href="/favicon.svg?v=3" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

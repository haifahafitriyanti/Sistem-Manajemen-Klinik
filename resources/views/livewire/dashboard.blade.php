<div wire:poll.30s class="space-y-8">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex items-center gap-1.5 text-xs text-slate-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Auto-refresh setiap 30 detik
        </div>
    </div>

    {{-- ===== Baris 1: Statistik Antrian Hari Ini ===== --}}
    <div>
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">Antrian Hari Ini</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total Pasien --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-11 h-11 flex items-center justify-center rounded-lg bg-indigo-100">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M17 20h5v-2a4 4 0 0 0-5-3.87M9 20H4v-2a4 4 0 0 1 5-3.87M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-slate-900">{{ $totalToday }}</p>
                    <p class="text-sm text-slate-500 mt-0.5">Total Pasien</p>
                </div>
            </div>

            {{-- Menunggu --}}
            <div class="bg-white rounded-xl border border-yellow-200 shadow-sm px-5 py-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-11 h-11 flex items-center justify-center rounded-lg bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-yellow-700">{{ $totalWaiting }}</p>
                    <p class="text-sm text-yellow-600 mt-0.5">Menunggu</p>
                </div>
            </div>

            {{-- Sedang Diperiksa --}}
            <div class="bg-white rounded-xl border border-blue-200 shadow-sm px-5 py-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-11 h-11 flex items-center justify-center rounded-lg bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M9 12h6m-3-3v6M5.5 19.5A8 8 0 1 1 12 4a8 8 0 0 1 6.5 15.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-blue-700">{{ $totalInProgress }}</p>
                    <p class="text-sm text-blue-600 mt-0.5">Sedang Diperiksa</p>
                </div>
            </div>

            {{-- Selesai --}}
            <div class="bg-white rounded-xl border border-emerald-200 shadow-sm px-5 py-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-11 h-11 flex items-center justify-center rounded-lg bg-emerald-100">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 0 0 1.946-.806 3.42 3.42 0 0 1 4.438 0 3.42 3.42 0 0 0 1.946.806 3.42 3.42 0 0 1 3.138 3.138c.12.563.378 1.087.806 1.946a3.42 3.42 0 0 1 0 4.438 3.42 3.42 0 0 0-.806 1.946 3.42 3.42 0 0 1-3.138 3.138 3.42 3.42 0 0 0-1.946.806 3.42 3.42 0 0 1-4.438 0 3.42 3.42 0 0 0-1.946-.806 3.42 3.42 0 0 1-3.138-3.138 3.42 3.42 0 0 0-.806-1.946 3.42 3.42 0 0 1 0-4.438 3.42 3.42 0 0 0 .806-1.946 3.42 3.42 0 0 1 3.138-3.138z" />
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-emerald-700">{{ $totalDone }}</p>
                    <p class="text-sm text-emerald-600 mt-0.5">Selesai</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== Baris 2: Finansial (Admin Only) ===== --}}
    @if(auth()->user()->role === 'admin' && $financialData)
    <div>
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">Finansial</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Pendapatan Bulan Ini --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-slate-500">Pendapatan Bulan Ini</p>
                    @if($financialData['change_percent'] !== null)
                        @if($financialData['change_direction'] === 'up')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                </svg>
                                {{ number_format(abs($financialData['change_percent']), 1) }}%
                            </span>
                        @elseif($financialData['change_direction'] === 'down')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                                {{ number_format(abs($financialData['change_percent']), 1) }}%
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                                0%
                            </span>
                        @endif
                    @elseif($financialData['change_direction'] === 'up')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                            </svg>
                            Baru
                        </span>
                    @endif
                </div>
                <p class="text-2xl font-bold text-slate-900">
                    Rp {{ number_format($financialData['revenue_this_month'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400 mt-1">{{ now()->translatedFormat('F Y') }}</p>
            </div>

            {{-- Pendapatan Bulan Lalu --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-5">
                <p class="text-sm font-medium text-slate-500 mb-2">Pendapatan Bulan Lalu</p>
                <p class="text-2xl font-bold text-slate-700">
                    Rp {{ number_format($financialData['revenue_last_month'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400 mt-1">{{ now()->subMonth()->translatedFormat('F Y') }}</p>
            </div>

            {{-- Invoice Belum Dibayar --}}
            <div class="bg-white rounded-xl border border-orange-200 shadow-sm px-5 py-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-slate-500">Invoice Belum Dibayar</p>
                    <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-orange-100">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-orange-700">{{ $financialData['unpaid_count'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Menunggu pembayaran</p>
            </div>

        </div>
    </div>
    @endif

    {{-- ===== Baris 3 + 4: Dua Kolom (Dokter & Antrian Terkini) ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Baris 3: Dokter Aktif Hari Ini --}}
        <div>
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">Dokter Aktif Hari Ini</h2>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                @if($activeDoctors->isNotEmpty())
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Dokter</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Spesialisasi</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-600">Pasien</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($activeDoctors as $doctor)
                                <tr wire:key="doc-{{ $doctor->id }}" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $doctor->name }}</td>
                                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $doctor->specialization }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                            {{ $doctor->patients_today > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-400' }}
                                            text-xs font-bold">
                                            {{ $doctor->patients_today }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-10 text-center text-slate-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z" />
                        </svg>
                        <p class="text-sm font-medium">Tidak ada dokter terjadwal hari ini</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Baris 4: Antrian Terkini --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Antrian Terkini</h2>
                <a href="{{ route('appointments.index') }}"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-700 hover:underline">
                    Lihat Semua →
                </a>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                @if($recentAppointments->isNotEmpty())
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-center font-semibold text-slate-600 w-10">No.</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Pasien</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Dokter</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentAppointments as $apt)
                                <tr wire:key="apt-{{ $apt->id }}" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-center">
                                        @if($apt->queue_number)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs">
                                                {{ $apt->queue_number }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-900 truncate max-w-[120px]">{{ $apt->patient_name }}</p>
                                        <p class="text-xs text-slate-400">{{ $apt->created_at->format('H:i') }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs truncate max-w-[100px]">
                                        {{ $apt->doctor->name }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($apt->status === 'waiting')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Menunggu</span>
                                        @elseif($apt->status === 'in_progress')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Diperiksa</span>
                                        @elseif($apt->status === 'done')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Selesai</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">Batal</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-10 text-center text-slate-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z" />
                        </svg>
                        <p class="text-sm font-medium">Belum ada appointment hari ini</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

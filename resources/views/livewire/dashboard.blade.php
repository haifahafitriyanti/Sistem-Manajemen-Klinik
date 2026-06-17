@extends('layouts.app')

@section('content')
<div wire:poll.30s class="space-y-8">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ now()->translatedFormat('l, d F Y') }}
                <span class="ml-2 inline-flex items-center gap-1 text-xs text-emerald-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                    Auto-refresh 30s
                </span>
            </p>
        </div>
    </div>

    {{-- ── Row 1: Today's statistics ─────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Hari Ini --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-slate-900">{{ $totalToday }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Total Pasien Hari Ini</p>
            </div>
        </div>

        {{-- Menunggu --}}
        <div class="bg-white rounded-2xl border border-yellow-200 shadow-sm px-6 py-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-yellow-600">{{ $waitingCount }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Menunggu</p>
            </div>
        </div>

        {{-- Sedang Diperiksa --}}
        <div class="bg-white rounded-2xl border border-blue-200 shadow-sm px-6 py-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-blue-600">{{ $inProgressCount }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Sedang Diperiksa</p>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm px-6 py-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-emerald-600">{{ $doneCount }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Selesai</p>
            </div>
        </div>
    </div>

    {{-- ── Row 2: Financial widgets (admin only) ─────────────────────────── --}}
    @if(auth()->user()->isAdmin())
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Pendapatan Bulan Ini --}}
        <div class="bg-white rounded-2xl border border-indigo-200 shadow-sm px-6 py-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Pendapatan Bulan Ini</p>
            <p class="text-2xl font-bold text-indigo-700">
                Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
            </p>
            @if($revenueTrend)
                <div class="mt-2 flex items-center gap-1.5">
                    @if($revenueTrend['direction'] === 'up')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            +{{ $revenueTrend['percent'] }}%
                        </span>
                    @elseif($revenueTrend['direction'] === 'down')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            -{{ $revenueTrend['percent'] }}%
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                            Sama dengan bulan lalu
                        </span>
                    @endif
                    <span class="text-xs text-slate-400">vs bulan lalu</span>
                </div>
            @endif
        </div>

        {{-- Pendapatan Bulan Lalu --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Pendapatan Bulan Lalu</p>
            <p class="text-2xl font-bold text-slate-700">
                Rp {{ number_format($revenueLastMonth, 0, ',', '.') }}
            </p>
            <p class="mt-2 text-xs text-slate-400">{{ now()->subMonth()->translatedFormat('F Y') }}</p>
        </div>

        {{-- Invoice Belum Dibayar --}}
        <div class="bg-white rounded-2xl border border-red-200 shadow-sm px-6 py-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Invoice Belum Dibayar</p>
            <p class="text-3xl font-bold text-red-600">{{ $unpaidCount }}</p>
            <a href="{{ route('pos.index') }}" class="mt-2 inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 transition-colors">
                Lihat di POS
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
    @endif

    {{-- ── Row 3 & 4: Two-column grid ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Dokter Aktif Hari Ini --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">
                    Dokter Aktif Hari Ini
                    <span class="ml-2 text-xs font-normal text-slate-400">({{ $todayDayName }})</span>
                </h2>
                <span class="text-xs text-slate-400">{{ $doctorsToday->count() }} dokter</span>
            </div>

            @if($doctorsToday->isEmpty())
                <div class="px-6 py-10 text-center text-slate-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <p class="text-sm font-medium">Tidak ada dokter yang praktik hari ini</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-6 py-2.5 text-left font-semibold">Dokter</th>
                            <th class="px-6 py-2.5 text-left font-semibold">Jam Praktik</th>
                            <th class="px-6 py-2.5 text-center font-semibold">Pasien</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($doctorsToday as $doctor)
                            <tr wire:key="dr-{{ $doctor->id }}" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <p class="font-medium text-slate-900">{{ $doctor->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $doctor->specialization }}</p>
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 font-mono text-xs">
                                    @if($doctor->schedules->first())
                                        {{ substr($doctor->schedules->first()->start_time, 0, 5) }}–{{ substr($doctor->schedules->first()->end_time, 0, 5) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold
                                        {{ $doctor->today_appointments_count > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $doctor->today_appointments_count }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Antrian Terkini --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Antrian Terkini</h2>
                <a href="{{ route('appointments.index') }}"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition-colors flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @if($recentAppointments->isEmpty())
                <div class="px-6 py-10 text-center text-slate-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm font-medium">Belum ada antrian hari ini</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-2.5 text-center font-semibold w-10">#</th>
                            <th class="px-5 py-2.5 text-left font-semibold">Pasien</th>
                            <th class="px-5 py-2.5 text-left font-semibold">Dokter</th>
                            <th class="px-5 py-2.5 text-center font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentAppointments as $apt)
                            <tr wire:key="apt-{{ $apt->id }}" class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-center">
                                    @if($apt->queue_number)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs">
                                            {{ $apt->queue_number }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-800 truncate max-w-[140px]">{{ $apt->patient_name }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $apt->time_slot }}</p>
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-600 truncate max-w-[120px]">
                                    {{ $apt->doctor->name }}
                                </td>
                                <td class="px-5 py-3 text-center">
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
            @endif
        </div>

    </div>{{-- end grid --}}

</div>{{-- end wire:poll --}}
@endsection

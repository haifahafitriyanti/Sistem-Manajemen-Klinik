@extends('layouts.app')

@section('content')
<div wire:poll.10s class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Antrian Hari Ini</h1>
            <p class="mt-1 text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        @if(in_array(auth()->user()->role, ['admin','receptionist']))
        <button wire:click="openCreate"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Appointment
        </button>
        @endif
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Cari nama atau nomor identitas pasien…"
                    class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Filter Dokter --}}
            <select wire:model.live="filterDoctor"
                class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">Semua Dokter</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                @endforeach
            </select>

            {{-- Filter Status --}}
            <select wire:model.live="filterStatus"
                class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">Semua Status</option>
                <option value="waiting">Menunggu</option>
                <option value="in_progress">Diperiksa</option>
                <option value="done">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>
    </div>

    {{-- Summary badges --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $counts = $appointments->countBy('status');
        @endphp
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4">
            <p class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Menunggu</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $counts->get('waiting', 0) }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
            <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Diperiksa</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $counts->get('in_progress', 0) }}</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-4">
            <p class="text-xs font-medium text-emerald-600 uppercase tracking-wide">Selesai</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $counts->get('done', 0) }}</p>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Dibatalkan</p>
            <p class="text-2xl font-bold text-slate-600 mt-1">{{ $counts->get('cancelled', 0) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-3 text-center font-semibold text-slate-600 w-12">#</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-600">Pasien</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-600">Dokter</th>
                    <th class="px-5 py-3 text-center font-semibold text-slate-600">Slot</th>
                    <th class="px-5 py-3 text-center font-semibold text-slate-600">Status</th>
                    <th class="px-5 py-3 text-center font-semibold text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($appointments as $apt)
                    <tr wire:key="apt-{{ $apt->id }}" class="hover:bg-slate-50 transition-colors">
                        {{-- Queue number --}}
                        <td class="px-5 py-4 text-center">
                            @if($apt->queue_number)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm">
                                    {{ $apt->queue_number }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        {{-- Patient --}}
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-900">{{ $apt->patient_name }}</p>
                            @if($apt->patient_id)
                                <p class="text-xs text-slate-400">{{ $apt->patient_id }}</p>
                            @endif
                            <p class="text-xs text-slate-400">{{ $apt->phone }}</p>
                        </td>

                        {{-- Doctor --}}
                        <td class="px-5 py-4 text-slate-700">{{ $apt->doctor->name }}</td>

                        {{-- Time slot --}}
                        <td class="px-5 py-4 text-center font-mono text-slate-700">{{ $apt->time_slot }}</td>

                        {{-- Status badge --}}
                        <td class="px-5 py-4 text-center">
                            @if($apt->status === 'waiting')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Menunggu</span>
                            @elseif($apt->status === 'in_progress')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Diperiksa</span>
                            @elseif($apt->status === 'done')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Selesai</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">Dibatalkan</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex flex-col items-center gap-2">
                                @if($apt->status === 'waiting')
                                    <button wire:click="startExamination({{ $apt->id }})"
                                        class="w-full px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors text-center">
                                        Mulai Periksa
                                    </button>

                                    @if($cancellingId === $apt->id)
                                        {{-- Inline cancellation form --}}
                                        <div class="w-full space-y-1.5">
                                            <input wire:model="cancellationReason" type="text"
                                                placeholder="Alasan (opsional)"
                                                class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-400">
                                            <div class="flex gap-1">
                                                <button wire:click="confirmCancel({{ $apt->id }})"
                                                    class="flex-1 px-2 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                                                    Konfirmasi
                                                </button>
                                                <button wire:click="abortCancel"
                                                    class="flex-1 px-2 py-1 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                                    Urung
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <button wire:click="initCancel({{ $apt->id }})"
                                            class="w-full px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors text-center">
                                            Batal
                                        </button>
                                    @endif

                                @elseif($apt->status === 'in_progress')
                                    <a href="{{ route('appointments.examine', $apt->id) }}"
                                        class="w-full px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors text-center">
                                        Periksa
                                    </a>
                                    <button wire:click="initCancel({{ $apt->id }})"
                                        class="w-full px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors text-center">
                                        Batal
                                    </button>
                                    @if($cancellingId === $apt->id)
                                        <div class="w-full space-y-1.5">
                                            <input wire:model="cancellationReason" type="text"
                                                placeholder="Alasan (opsional)"
                                                class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-400">
                                            <div class="flex gap-1">
                                                <button wire:click="confirmCancel({{ $apt->id }})"
                                                    class="flex-1 px-2 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                                                    Konfirmasi
                                                </button>
                                                <button wire:click="abortCancel"
                                                    class="flex-1 px-2 py-1 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                                    Urung
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="font-medium">Tidak ada appointment hari ini</p>
                            @if($search || $filterDoctor || $filterStatus)
                                <p class="text-sm mt-1">Coba ubah filter pencarian.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Live indicator --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-400">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        Data diperbarui otomatis setiap 10 detik
    </div>
</div>

{{-- Modal Form --}}
@if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm"
        x-data x-on:keydown.escape.window="$wire.closeForm()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-4 max-h-[92vh] overflow-y-auto"
            x-on:close-appointment-form.window="$wire.call('closeForm')">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Tambah Appointment</h2>
                <button wire:click="closeForm" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                @livewire('appointment.appointment-form', key('appointment-form-new'))
            </div>
        </div>
    </div>
@endif
@endsection

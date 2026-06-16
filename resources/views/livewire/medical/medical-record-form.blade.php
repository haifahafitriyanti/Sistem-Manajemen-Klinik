@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Flash message --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-5 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('appointments.index') }}"
            class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekam Medis</h1>
            <p class="mt-0.5 text-sm text-slate-500">Pemeriksaan pasien</p>
        </div>
    </div>

    {{-- Patient Info Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 rounded-t-xl">
            <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Informasi Pasien</h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Nama Pasien</p>
                <p class="font-semibold text-slate-900">{{ $appointment->patient_name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Nomor Identitas</p>
                <p class="font-medium text-slate-700">{{ $appointment->patient_id ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Nomor Telepon</p>
                <p class="font-medium text-slate-700">{{ $appointment->phone }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Dokter</p>
                <p class="font-medium text-slate-700">{{ $appointment->doctor->name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Tanggal</p>
                <p class="font-medium text-slate-700">{{ $appointment->date->translatedFormat('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Slot Waktu</p>
                <p class="font-mono font-medium text-slate-700">{{ $appointment->time_slot }}</p>
            </div>
            @if($appointment->complaint && !$alreadySaved)
            <div class="sm:col-span-2">
                <p class="text-xs text-slate-400 mb-0.5">Keluhan Awal</p>
                <p class="text-slate-700 text-sm">{{ $appointment->complaint }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Status guard: not in_progress --}}
    @if($appointment->status !== 'in_progress' && !$alreadySaved)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-6 py-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z" />
                </svg>
                <div>
                    <p class="font-semibold text-amber-800">Pemeriksaan tidak dapat dilakukan</p>
                    <p class="text-sm text-amber-700 mt-0.5">
                        Status pasien saat ini:
                        <span class="font-semibold">{{ $appointment->status }}</span>
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Medical Record Form / Read-only view --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 rounded-t-xl flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Rekam Medis</h2>
            @if($alreadySaved)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                    Sudah Disimpan
                </span>
            @endif
        </div>

        <div class="p-6">
            @if($alreadySaved || $appointment->status !== 'in_progress')
                {{-- Read-only mode --}}
                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5">Keluhan</p>
                        <p class="text-slate-800 text-sm bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 whitespace-pre-wrap">{{ $complaint ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5">Diagnosis</p>
                        <p class="text-slate-800 text-sm bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 whitespace-pre-wrap">{{ $diagnosis ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5">Resep</p>
                        <p class="text-slate-800 text-sm bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 whitespace-pre-wrap">{{ $prescription ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5">Catatan</p>
                        <p class="text-slate-800 text-sm bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 whitespace-pre-wrap">{{ $notes ?: '—' }}</p>
                    </div>
                </div>
            @else
                {{-- Editable form --}}
                <form wire:submit="save" class="space-y-5">

                    {{-- Keluhan --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Keluhan</label>
                        <textarea wire:model="complaint" rows="3"
                            placeholder="Keluhan pasien…"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                    </div>

                    {{-- Diagnosis --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Diagnosis <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="diagnosis" rows="4"
                            placeholder="Masukkan diagnosis…"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none @error('diagnosis') border-red-400 bg-red-50 @else border-slate-200 @enderror"></textarea>
                        @error('diagnosis')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Resep --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Resep / Obat</label>
                        <textarea wire:model="prescription" rows="3"
                            placeholder="Tuliskan resep atau obat yang diberikan (opsional)…"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan Tambahan</label>
                        <textarea wire:model="notes" rows="2"
                            placeholder="Catatan dokter (opsional)…"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                        <a href="{{ route('appointments.index') }}"
                            class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                            Kembali
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="save">
                                <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan &amp; Selesaikan Pemeriksaan
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Menyimpan…
                            </span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

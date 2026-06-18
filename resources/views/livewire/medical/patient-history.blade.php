<div class="max-w-4xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('appointments.index') }}"
            class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Riwayat Pasien</h1>
            <p class="mt-0.5 text-sm text-slate-500">
                {{ $patientName }}
                <span class="text-slate-300 mx-1">·</span>
                <span class="font-mono text-slate-400">{{ $patientId }}</span>
            </p>
        </div>
    </div>

    {{-- History list --}}
    @if($appointments->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-6 py-16 text-center">
            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="font-medium text-slate-500">Tidak ada riwayat untuk pasien ini.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($appointments as $apt)
                <div wire:key="history-{{ $apt->id }}"
                    x-data="{ open: false }"
                    class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

                    {{-- Row header (always visible) --}}
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-4 px-6 py-4 text-left hover:bg-slate-50 transition-colors">
                        {{-- Date --}}
                        <div class="w-24 shrink-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $apt->date->format('d M Y') }}</p>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $apt->time_slot }}</p>
                        </div>

                        {{-- Doctor --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 truncate">{{ $apt->doctor->name }}</p>
                            @if($apt->medicalRecord)
                                <p class="text-xs text-slate-400 mt-0.5 truncate">
                                    {{ Str::limit($apt->medicalRecord->diagnosis, 50) }}
                                </p>
                            @else
                                <p class="text-xs text-slate-300 mt-0.5 italic">Belum ada rekam medis</p>
                            @endif
                        </div>

                        {{-- Status badge --}}
                        <div class="shrink-0">
                            @if($apt->status === 'waiting')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Menunggu</span>
                            @elseif($apt->status === 'in_progress')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Diperiksa</span>
                            @elseif($apt->status === 'done')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Selesai</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">Dibatalkan</span>
                            @endif
                        </div>

                        {{-- Chevron --}}
                        <svg :class="open ? 'rotate-180' : ''"
                            class="w-4 h-4 text-slate-400 shrink-0 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Expanded details --}}
                    <div x-show="open" x-transition style="display: none;"
                        class="border-t border-slate-100 px-6 py-5 bg-slate-50 space-y-4">
                        @if($apt->complaint)
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Keluhan</p>
                                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $apt->complaint }}</p>
                            </div>
                        @endif

                        @if($apt->medicalRecord)
                            @if($apt->medicalRecord->diagnosis)
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Diagnosis</p>
                                    <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $apt->medicalRecord->diagnosis }}</p>
                                </div>
                            @endif
                            @if($apt->medicalRecord->prescription)
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Resep</p>
                                    <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $apt->medicalRecord->prescription }}</p>
                                </div>
                            @endif
                            @if($apt->medicalRecord->notes)
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Catatan</p>
                                    <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $apt->medicalRecord->notes }}</p>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-slate-400 italic">Rekam medis belum tersedia.</p>
                        @endif

                        @if($apt->cancellation_reason)
                            <div class="bg-red-50 border border-red-100 rounded-lg px-4 py-3">
                                <p class="text-xs font-semibold text-red-500 uppercase tracking-wide mb-1">Alasan Pembatalan</p>
                                <p class="text-sm text-red-700">{{ $apt->cancellation_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="max-w-2xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekam Medis</h1>
        <p class="mt-1 text-sm text-slate-500">Cari pasien untuk melihat riwayat rekam medis.</p>
    </div>

    {{-- Search Input --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <div class="relative">
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
            </svg>
            <input wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Ketik nama atau nomor identitas pasien…"
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                autofocus>
        </div>
    </div>

    {{-- Results --}}
    @if(trim($search) === '')
        <div class="text-center py-16 text-slate-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="font-medium text-sm">Ketik nama atau ID pasien untuk mencari riwayat rekam medis</p>
        </div>
    @elseif($results->isEmpty())
        <div class="text-center py-16 text-slate-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <p class="font-medium text-sm">Pasien tidak ditemukan</p>
            <p class="text-xs mt-1">Coba ubah kata kunci pencarian.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    {{ $results->count() }} hasil ditemukan
                </p>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach($results as $patient)
                    <li>
                        <a href="{{ route('patients.history', $patient->patient_id) }}"
                            class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold text-sm shrink-0">
                                    {{ strtoupper(substr($patient->patient_name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $patient->patient_name }}
                                    </p>
                                    <p class="text-xs text-slate-400">ID: {{ $patient->patient_id }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full font-medium">
                                    {{ $patient->visit_count }} kunjungan
                                </span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Laporan Statistik</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan data pasien, pendapatan, dan performa dokter.</p>
        </div>
        <button wire:click="exportReport"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 disabled:opacity-60 transition-colors">
            <svg wire:loading.remove wire:target="exportReport" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <svg wire:loading wire:target="exportReport" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Export Laporan ke Excel
        </button>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">Filter Rentang Tanggal</h2>
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
                <input wire:model="dateFrom" type="date"
                    class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('dateFrom')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
                <input wire:model="dateTo" type="date"
                    class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('dateTo')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button wire:click="applyFilter"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-60 transition-colors">
                <svg wire:loading.remove wire:target="applyFilter" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                </svg>
                <svg wire:loading wire:target="applyFilter" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Terapkan Filter
            </button>
        </div>
        <p class="text-xs text-slate-400 mt-3">
            Menampilkan data:
            <span class="font-medium text-slate-600">{{ \Illuminate\Support\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}</span>
            s/d
            <span class="font-medium text-slate-600">{{ \Illuminate\Support\Carbon::parse($dateTo)->translatedFormat('d F Y') }}</span>
        </p>
    </div>

    {{-- ═══ Section A: Laporan Pasien ══════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-base font-semibold text-slate-800">A — Laporan Pasien</h2>
            <p class="text-xs text-slate-500 mt-0.5">Total appointment dalam rentang filter</p>
        </div>
        <div class="p-6 space-y-6">

            {{-- 4 Summary Cards --}}
            @php
                $statusMap = [
                    'waiting'     => ['label' => 'Menunggu',         'color' => 'yellow'],
                    'in_progress' => ['label' => 'Sedang Diperiksa', 'color' => 'blue'],
                    'done'        => ['label' => 'Selesai',          'color' => 'emerald'],
                    'cancelled'   => ['label' => 'Dibatalkan',       'color' => 'red'],
                ];
                $statusByKey = $statusBreakdown->keyBy('status');
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total --}}
                <div class="bg-indigo-50 rounded-xl px-5 py-4 border border-indigo-100">
                    <p class="text-3xl font-bold text-indigo-700">{{ $totalAppointments }}</p>
                    <p class="text-sm text-indigo-500 mt-1 font-medium">Total Appointment</p>
                </div>

                @foreach($statusMap as $key => $meta)
                    @php $item = $statusByKey->get($key); @endphp
                    <div class="bg-{{ $meta['color'] }}-50 rounded-xl px-5 py-4 border border-{{ $meta['color'] }}-100">
                        <p class="text-3xl font-bold text-{{ $meta['color'] }}-700">{{ $item['total'] ?? 0 }}</p>
                        <p class="text-sm text-{{ $meta['color'] }}-500 mt-1 font-medium">{{ $meta['label'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Breakdown Table --}}
            @if($statusBreakdown->isNotEmpty())
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Jumlah</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($statusBreakdown as $row)
                                @php
                                    $meta = $statusMap[$row['status']] ?? ['label' => ucfirst($row['status']), 'color' => 'slate'];
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            bg-{{ $meta['color'] }}-100 text-{{ $meta['color'] }}-700">
                                            {{ $meta['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-700">{{ $row['total'] }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">{{ $row['percent'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t border-slate-200 font-semibold">
                                <td class="px-4 py-3 text-slate-700">Total</td>
                                <td class="px-4 py-3 text-right text-slate-700">{{ $totalAppointments }}</td>
                                <td class="px-4 py-3 text-right text-slate-500">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-slate-400 text-sm">Tidak ada data appointment dalam rentang ini.</div>
            @endif
        </div>
    </div>

    {{-- ═══ Section B: Laporan Pendapatan ═════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-base font-semibold text-slate-800">B — Laporan Pendapatan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Berdasarkan invoice dengan status "Lunas" (paid_at dalam rentang filter)</p>
        </div>
        <div class="p-6 space-y-6">

            {{-- Summary Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="bg-emerald-50 rounded-xl px-5 py-4 border border-emerald-100">
                    <p class="text-2xl font-bold text-emerald-700">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-emerald-500 mt-1 font-medium">Total Pendapatan Lunas</p>
                </div>
                <div class="bg-orange-50 rounded-xl px-5 py-4 border border-orange-100">
                    <p class="text-2xl font-bold text-orange-700">{{ $outstandingCount }}</p>
                    <p class="text-sm text-orange-500 mt-1 font-medium">Invoice Outstanding (Unpaid)</p>
                </div>
                <div class="bg-red-50 rounded-xl px-5 py-4 border border-red-100">
                    <p class="text-2xl font-bold text-red-700">
                        Rp {{ number_format($outstandingAmount, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-red-500 mt-1 font-medium">Nominal Outstanding</p>
                </div>
            </div>

            {{-- Breakdown by Payment Method --}}
            @if($revenueByMethod->isNotEmpty())
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Metode Pembayaran</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Jumlah Transaksi</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Total Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($revenueByMethod as $row)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-slate-700 uppercase text-xs tracking-wide">
                                        {{ $row->payment_method ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-600">{{ $row->jumlah_transaksi }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-700">
                                        Rp {{ number_format($row->total_nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t border-slate-200 font-semibold">
                                <td class="px-4 py-3 text-slate-700">Total</td>
                                <td class="px-4 py-3 text-right text-slate-700">
                                    {{ $revenueByMethod->sum('jumlah_transaksi') }}
                                </td>
                                <td class="px-4 py-3 text-right text-emerald-700">
                                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-slate-400 text-sm">Tidak ada transaksi lunas dalam rentang ini.</div>
            @endif
        </div>
    </div>

    {{-- ═══ Section C: Performa Dokter ════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-base font-semibold text-slate-800">C — Performa Dokter</h2>
            <p class="text-xs text-slate-500 mt-0.5">Jumlah pasien selesai + revenue per dokter dalam rentang filter</p>
        </div>
        <div class="p-6">
            @if($doctorPerformance->isNotEmpty())
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">No.</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama Dokter</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Spesialisasi</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Pasien Selesai</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($doctorPerformance as $i => $doctor)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-slate-400">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $doctor->name }}</td>
                                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $doctor->specialization }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $doctor->done_count > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-400' }}">
                                            {{ $doctor->done_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-700">
                                        Rp {{ number_format($doctor->revenue_sum ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-slate-400 text-sm">Tidak ada data dokter.</div>
            @endif
        </div>
    </div>

</div>

<div class="space-y-6">

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-5 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">POS & Kasir</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola pembayaran invoice pasien.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Cari nomor invoice atau nama pasien…"
                    class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Filter status --}}
            <select wire:model.live="filterStatus"
                class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">Semua Status</option>
                <option value="unpaid">Belum Bayar</option>
                <option value="partially_paid">Bayar Sebagian</option>
                <option value="fully_paid">Lunas</option>
            </select>

            {{-- Filter tanggal --}}
            <input wire:model.live="filterDate" type="date"
                class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
        </div>
    </div>

    {{-- Export Excel --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Export ke Excel</p>
        <div class="flex flex-col sm:flex-row items-end gap-3">
            <div class="flex flex-col gap-1">
                <label class="text-xs text-slate-500">Dari Tanggal</label>
                <input wire:model="exportDateFrom" type="date"
                    class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs text-slate-500">Sampai Tanggal</label>
                <input wire:model="exportDateTo" type="date"
                    class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
            </div>
            <button wire:click="exportExcel"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 disabled:opacity-60 transition-colors">
                <svg wire:loading.remove wire:target="exportExcel" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <svg wire:loading wire:target="exportExcel" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                <span wire:loading wire:target="exportExcel">Mengexport…</span>
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-3 text-left font-semibold text-slate-600">No. Invoice</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-600">Pasien</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-600">Dokter</th>
                    <th class="px-5 py-3 text-right font-semibold text-slate-600">Total</th>
                    <th class="px-5 py-3 text-center font-semibold text-slate-600">Status</th>
                    <th class="px-5 py-3 text-center font-semibold text-slate-600">Tanggal</th>
                    <th class="px-5 py-3 text-center font-semibold text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($invoices as $invoice)
                    <tr wire:key="inv-{{ $invoice->id }}" class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4 font-mono text-slate-800 text-xs">{{ $invoice->invoice_number }}</td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-900">{{ $invoice->appointment->patient_name }}</p>
                            <p class="text-xs text-slate-400">{{ $invoice->appointment->patient_id ?? '—' }}</p>
                        </td>

                        <td class="px-5 py-4 text-slate-700">{{ $invoice->appointment->doctor->name }}</td>

                        <td class="px-5 py-4 text-right font-semibold text-slate-800">
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        </td>

                        <td class="px-5 py-4 text-center">
                            @if($invoice->payment_status === 'unpaid')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Belum Bayar</span>
                            @elseif($invoice->payment_status === 'partially_paid')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Bayar Sebagian</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Lunas</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-center text-xs text-slate-500">
                            {{ $invoice->created_at->format('d M Y') }}
                        </td>

                        <td class="px-5 py-4 text-center">
                            <div class="flex flex-col items-center gap-1.5">
                                @if(in_array($invoice->payment_status, ['unpaid', 'partially_paid']))
                                    <a href="{{ route('pos.pay', $invoice->id) }}"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                                        Proses Bayar
                                    </a>
                                @endif
                                <a href="{{ route('pos.pay', $invoice->id) }}"
                                    class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <p class="font-medium">Tidak ada invoice ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($invoices->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>

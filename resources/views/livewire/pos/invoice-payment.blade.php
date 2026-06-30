<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('pos.index') }}"
            class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Proses Pembayaran</h1>
            <p class="mt-0.5 text-sm font-mono text-slate-400">{{ $invoice->invoice_number }}</p>
        </div>
    </div>

    {{-- Invoice Detail Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 rounded-t-xl">
            <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Detail Invoice</h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-2 gap-5">
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Nama Pasien</p>
                <p class="font-semibold text-slate-900">{{ $invoice->appointment->patient_name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Dokter</p>
                <p class="font-medium text-slate-700">{{ $invoice->appointment->doctor->name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Tanggal Pemeriksaan</p>
                <p class="font-medium text-slate-700">{{ $invoice->appointment->date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Tarif Konsultasi</p>
                <p class="font-semibold text-slate-800">Rp {{ number_format($invoice->appointment->doctor->consultation_fee, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Subtotal</p>
                <p class="font-semibold text-slate-800">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Status</p>
                @if($invoice->payment_status === 'unpaid')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Belum Bayar</span>
                @elseif($invoice->payment_status === 'partially_paid')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Bayar Sebagian</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Lunas</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment Form or Read-only --}}
    @if($invoice->payment_status === 'fully_paid')
        {{-- Read-only mode --}}
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-6 py-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-emerald-800">Invoice sudah lunas</p>
                    <p class="text-sm text-emerald-700 mt-0.5">
                        Dibayar pada {{ $invoice->paid_at?->format('d M Y, H:i') }} · Metode: {{ strtoupper($invoice->payment_method) }}
                    </p>
                    @if($invoice->cashier)
                        <p class="text-sm text-emerald-700">Kasir: {{ $invoice->cashier->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Show final amounts read-only --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-6 py-5 space-y-3">

            <div class="flex justify-between text-base font-bold border-t border-slate-100 pt-3">
                <span class="text-slate-800">Total Dibayar</span>
                <span class="text-emerald-700">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    @else
        {{-- Payment form --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 rounded-t-xl">
                <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Form Pembayaran</h2>
            </div>
            <form wire:submit="confirmPayment" class="p-6 space-y-6">

                {{-- Catatan tambahan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan Biaya Tambahan</label>
                    <textarea wire:model="notes" rows="2"
                        placeholder="Tindakan / obat tambahan (opsional)…"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                </div>

                {{-- Total dinamis --}}
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-5 py-4">
                    <div class="flex justify-between text-sm text-slate-600 mb-1">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-base font-bold border-t border-indigo-200 pt-2">
                        <span class="text-slate-800">Total</span>
                        <span class="text-indigo-700">Rp {{ number_format($computedTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Metode pembayaran --}}
                <div>
                    <p class="text-sm font-medium text-slate-700 mb-2">Metode Pembayaran</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['cash' => 'Cash', 'transfer' => 'Transfer'] as $value => $label)
                            <label class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg border text-sm font-medium cursor-pointer transition-colors
                                {{ $paymentMethod === $value ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300' }}">
                                <input wire:model.live="paymentMethod" type="radio" value="{{ $value }}" class="sr-only">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('paymentMethod') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('pos.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Kembali
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="confirmPayment">
                            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Konfirmasi Pembayaran
                        </span>
                        <span wire:loading wire:target="confirmPayment" class="flex items-center gap-1.5">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Memproses…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

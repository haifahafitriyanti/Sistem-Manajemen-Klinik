<div>
    <form wire:submit="save" class="space-y-5">

        {{-- Pilih Dokter --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Dokter <span class="text-red-500">*</span></label>
            <select wire:model.live="doctor_id"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('doctor_id') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                <option value="">-- Pilih Dokter --</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor['id'] }}">{{ $doctor['name'] }}</option>
                @endforeach
            </select>
            @error('doctor_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Tanggal --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
            <input wire:model.live="date" type="date" min="{{ today()->toDateString() }}"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('date') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Slot Waktu --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Slot Waktu <span class="text-red-500">*</span></label>

            @if($noScheduleMessage)
                <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                    {{ $noScheduleMessage }}
                </p>
            @elseif(!$doctor_id || !$date)
                <p class="text-sm text-slate-400 italic">Pilih dokter dan tanggal terlebih dahulu.</p>
            @else
                <select wire:model="time_slot"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('time_slot') border-red-400 bg-red-50 @else border-slate-200 @enderror"
                    @if(empty($availableSlots)) disabled @endif>
                    <option value="">-- Pilih Slot --</option>
                    @foreach($availableSlots as $slot)
                        <option value="{{ $slot }}">{{ $slot }}</option>
                    @endforeach
                </select>
            @endif
            @error('time_slot') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Nama Pasien --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Pasien <span class="text-red-500">*</span></label>
            <input wire:model="patient_name" type="text" placeholder="Nama lengkap"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('patient_name') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('patient_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Nomor Identitas --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Identitas (KTP/SIM)</label>
            <input wire:model="patient_id" type="text" placeholder="Opsional"
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        {{-- Nomor Telepon --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Telepon <span class="text-red-500">*</span></label>
            <input wire:model="phone" type="text" placeholder="08xxxxxxxxxx"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('phone') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Keluhan --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Keluhan</label>
            <textarea wire:model="complaint" rows="3" placeholder="Deskripsi keluhan pasien (opsional)"
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
            <button type="button"
                x-on:click="$dispatch('close-appointment-form')"
                class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                Batal
            </button>
            <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <span wire:loading.remove wire:target="save">Daftar</span>
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
</div>

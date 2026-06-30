<div>
    <form wire:submit="save" class="space-y-5">
        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Dokter <span class="text-red-500">*</span></label>
            <input wire:model="name" type="text" placeholder="Dr. Nama Lengkap"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Specialization --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Spesialisasi <span class="text-red-500">*</span></label>
            <input wire:model="specialization" type="text" placeholder="Misal: Spesialis Anak"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('specialization') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('specialization')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- License Number --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">No. Lisensi</label>
            <input wire:model="license_number" type="text" placeholder="DS-XXXXX-XXXX"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('license_number') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('license_number')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Telepon</label>
            <input wire:model="phone" type="text" placeholder="08xxxxxxxxxx"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('phone')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Consultation Fee --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tarif Konsultasi (Rp) <span class="text-red-500">*</span></label>
            <input wire:model="consultation_fee" type="number" min="0" step="1000" placeholder="150000"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('consultation_fee') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('consultation_fee')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Photo Upload --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Foto Dokter</label>

            {{-- Preview: new photo chosen (not yet saved) --}}
            @if($newPhoto)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ $newPhoto->temporaryUrl() }}" alt="Foto baru"
                        class="w-12 h-12 rounded-full object-cover border-2 border-indigo-300">
                    <span class="text-xs text-indigo-500 font-medium">Foto baru dipilih — belum disimpan.</span>
                </div>
            {{-- Preview: existing photo from database --}}
            @elseif($existingPhotoPath)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ Storage::url($existingPhotoPath) }}" alt="Foto saat ini"
                        class="w-12 h-12 rounded-full object-cover border border-slate-200">
                    <span class="text-xs text-slate-400">Foto saat ini — upload baru untuk mengganti.</span>
                </div>
            @endif

            <input wire:model.live="newPhoto" type="file" accept="image/*"
                class="w-full text-sm text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 @error('newPhoto') border border-red-400 rounded-lg bg-red-50 @enderror">
            @error('newPhoto')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Is Active --}}
        <div class="flex items-center gap-3">
            <input wire:model="is_active" type="checkbox" id="is_active"
                class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
            <label for="is_active" class="text-sm font-medium text-slate-700">Dokter Aktif</label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
            <button type="button"
                x-on:click="$dispatch('close-doctor-form')"
                class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                Batal
            </button>
            <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <span wire:loading.remove wire:target="save">Simpan</span>
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

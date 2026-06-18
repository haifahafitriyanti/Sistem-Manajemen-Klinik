<div>
    <form wire:submit="save" class="space-y-5">
        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Nama <span class="text-red-500">*</span>
            </label>
            <input wire:model="name" type="text" placeholder="Nama lengkap"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Email <span class="text-red-500">*</span>
            </label>
            <input wire:model="email" type="email" placeholder="email@klinik.com"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Password {{ $userId ? '(kosongkan jika tidak diubah)' : '*' }}
            </label>
            <input wire:model="password" type="password"
                placeholder="{{ $userId ? 'Isi untuk mengubah password' : 'Min. 8 karakter' }}"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-400 bg-red-50 @else border-slate-200 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Role <span class="text-red-500">*</span>
            </label>
            <select wire:model="role"
                @if($userId && $userId === auth()->id()) disabled @endif
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white @error('role') border-red-400 @else border-slate-200 @enderror
                    @if($userId && $userId === auth()->id()) opacity-60 cursor-not-allowed @endif">
                <option value="admin">Admin</option>
                <option value="doctor">Dokter</option>
                <option value="cashier">Kasir</option>
                <option value="receptionist">Resepsionis</option>
            </select>
            @if($userId && $userId === auth()->id())
                <p class="mt-1 text-xs text-amber-600">Role Anda sendiri tidak dapat diubah.</p>
            @endif
            @error('role')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Is Active --}}
        <div class="flex items-center gap-3">
            <input wire:model="isActive" type="checkbox" id="is_active"
                class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
            <label for="is_active" class="text-sm font-medium text-slate-700">Akun Aktif</label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
            <button type="button"
                wire:click="$dispatch('close-user-form')"
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

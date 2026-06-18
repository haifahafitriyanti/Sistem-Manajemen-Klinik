<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Pengguna</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola akun staf dan role akses klinik.</p>
        </div>
        <button wire:click="openCreate"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Pengguna
        </button>
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
                    placeholder="Cari nama atau email…"
                    class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            {{-- Filter Role --}}
            <select wire:model.live="filterRole"
                class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">Semua Role</option>
                <option value="admin">Admin</option>
                <option value="doctor">Dokter</option>
                <option value="cashier">Kasir</option>
                <option value="receptionist">Resepsionis</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-3 text-left font-semibold text-slate-600">No.</th>
                    <th class="px-6 py-3 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-6 py-3 text-left font-semibold text-slate-600">Email</th>
                    <th class="px-6 py-3 text-center font-semibold text-slate-600">Role</th>
                    <th class="px-6 py-3 text-center font-semibold text-slate-600">Status</th>
                    <th class="px-6 py-3 text-center font-semibold text-slate-600">Dibuat</th>
                    <th class="px-6 py-3 text-center font-semibold text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    @php $isSelf = $user->id === auth()->id(); @endphp
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-slate-400">{{ $loop->iteration }}</td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold text-xs">
                                    {{ $user->initials() }}
                                </div>
                                <span class="font-medium text-slate-900">{{ $user->name }}</span>
                                @if($isSelf)
                                    <span class="text-xs text-slate-400">(Anda)</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>

                        <td class="px-6 py-4 text-center">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Admin</span>
                            @elseif($user->role === 'doctor')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Dokter</span>
                            @elseif($user->role === 'cashier')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Kasir</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Resepsionis</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Nonaktif</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center text-xs text-slate-500">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">

                                {{-- Edit --}}
                                @if($isSelf)
                                    <button disabled title="Tidak dapat mengubah akun Anda sendiri"
                                        class="p-1.5 text-slate-300 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                @else
                                    <button wire:click="openEdit({{ $user->id }})" title="Edit"
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                @endif

                                {{-- Toggle Aktif --}}
                                @if($isSelf)
                                    <button disabled title="Tidak dapat mengubah akun Anda sendiri"
                                        class="p-1.5 text-slate-300 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    </button>
                                @else
                                    <button wire:click="toggleActive({{ $user->id }})"
                                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="p-1.5 rounded-lg transition-colors {{ $user->is_active ? 'text-slate-400 hover:text-amber-600 hover:bg-amber-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50' }}">
                                        @if($user->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </button>
                                @endif

                                {{-- Reset Password --}}
                                @if($isSelf)
                                    <button disabled title="Tidak dapat mengubah akun Anda sendiri"
                                        class="p-1.5 text-slate-300 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </button>
                                @else
                                    <button wire:click="resetPassword({{ $user->id }})"
                                        wire:confirm="Reset password untuk {{ $user->name }}? Password baru akan ditampilkan sekali."
                                        title="Reset Password"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="font-medium">Tidak ada pengguna ditemukan</p>
                            @if($search || $filterRole)
                                <p class="text-sm mt-1">Coba ubah filter pencarian.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm"
            x-data x-on:keydown.escape.window="$wire.closeForm()"
            x-on:close-user-form.window="$wire.closeForm()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h2 class="text-lg font-semibold text-slate-800">
                        {{ $editingUserId ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                    </h2>
                    <button wire:click="closeForm"
                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    @livewire('user.user-form', ['userId' => $editingUserId], key('user-form-'.($editingUserId ?? 'new')))
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Reset Password Result --}}
    @if($showResetModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h2 class="text-lg font-semibold text-slate-800">Password Baru</h2>
                    <button wire:click="closeResetModal"
                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm text-slate-600">
                        Password untuk <span class="font-semibold text-slate-800">{{ $resetUserName }}</span> telah direset.
                        Catat password berikut sebelum menutup jendela ini.
                    </p>
                    <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-lg px-4 py-3">
                        <span class="font-mono text-lg font-semibold text-indigo-700 tracking-widest select-all">
                            {{ $newPassword }}
                        </span>
                        <button type="button"
                            x-data
                            x-on:click="navigator.clipboard.writeText('{{ $newPassword }}').then(() => { $el.innerText = 'Disalin!'; setTimeout(() => $el.innerText = 'Salin', 1500) })"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors ml-3">
                            Salin
                        </button>
                    </div>
                    <p class="text-xs text-amber-600">
                        Password ini tidak akan ditampilkan lagi. Pastikan telah dicatat.
                    </p>
                    <div class="flex justify-end pt-1">
                        <button wire:click="closeResetModal"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

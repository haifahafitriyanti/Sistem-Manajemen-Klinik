@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('doctors.index') }}"
            class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Jadwal Praktik</h1>
            <p class="mt-0.5 text-sm text-slate-500">
                {{ $doctor->name }} &mdash; <span class="text-indigo-600">{{ $doctor->specialization }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Schedule Table --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-800">Jadwal Terdaftar</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Hari</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Mulai</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Selesai</th>
                            <th class="px-5 py-3 text-center font-semibold text-slate-600">Durasi Slot</th>
                            <th class="px-5 py-3 text-center font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($schedules as $schedule)
                            <tr wire:key="schedule-{{ $schedule->id }}" class="hover:bg-slate-50">
                                <td class="px-5 py-3.5 font-medium text-slate-800">{{ $schedule->day_of_week }}</td>
                                <td class="px-5 py-3.5 text-slate-600">{{ substr($schedule->start_time, 0, 5) }}</td>
                                <td class="px-5 py-3.5 text-slate-600">{{ substr($schedule->end_time, 0, 5) }}</td>
                                <td class="px-5 py-3.5 text-center text-slate-600">{{ $schedule->slot_duration_minutes }} menit</td>
                                <td class="px-5 py-3.5 text-center">
                                    <button wire:click="deleteSchedule({{ $schedule->id }})"
                                        wire:confirm="Yakin hapus jadwal {{ $schedule->day_of_week }} ini?"
                                        class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus jadwal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="font-medium text-sm">Belum ada jadwal</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add Schedule Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-800">Tambah Jadwal</h2>
                </div>
                <form wire:submit="save" class="p-6 space-y-5">
                    {{-- Day of Week --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Hari <span class="text-red-500">*</span></label>
                        <select wire:model="day_of_week"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('day_of_week') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                            <option value="">-- Pilih Hari --</option>
                            @foreach($daysOfWeek as $day)
                                <option value="{{ $day }}">{{ $day }}</option>
                            @endforeach
                        </select>
                        @error('day_of_week')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Start Time --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
                        <input wire:model="start_time" type="time"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('start_time') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                        @error('start_time')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- End Time --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jam Selesai <span class="text-red-500">*</span></label>
                        <input wire:model="end_time" type="time"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('end_time') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                        @error('end_time')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slot Duration --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Durasi Slot (menit) <span class="text-red-500">*</span></label>
                        <input wire:model="slot_duration_minutes" type="number" min="5" max="120" step="5"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('slot_duration_minutes') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                        @error('slot_duration_minutes')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <span wire:loading.remove wire:target="save">
                            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Jadwal
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Menyimpan…
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

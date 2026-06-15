@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-lg text-slate-700">Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span></p>
    </div>
</div>
@endsection

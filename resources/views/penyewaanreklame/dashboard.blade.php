@extends('penyewaanreklame.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold tracking-tight">Dashboard Penyewaan Reklame</h1>
    <p class="mt-1 text-sm text-slate-500">Sistem Informasi Manajemen Penyewaan Lokasi Reklame Restu Guru Promosindo.</p>

    <div class="mt-5 flex flex-wrap gap-3">
        <a href="{{ route('penyewaanreklame.titik-reklame.index') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-700">
            <i class="ph ph-map-pin-line text-base"></i>
            Daftar Titik Reklame
        </a>
        <a href="{{ route('penyewaanreklame.katalog') }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="ph ph-globe text-base text-violet-600"></i>
            Buka Katalog Publik
        </a>
    </div>

    <dl class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <dt class="text-xs font-semibold uppercase text-slate-500">Controller</dt>
            <dd class="mt-1 font-mono text-sm">app/Http/Controllers/PenyewaanReklame/</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <dt class="text-xs font-semibold uppercase text-slate-500">Views</dt>
            <dd class="mt-1 font-mono text-sm">resources/views/penyewaanreklame/</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <dt class="text-xs font-semibold uppercase text-slate-500">Routes</dt>
            <dd class="mt-1 font-mono text-sm">routes/penyewaanreklame.php</dd>
        </div>
    </dl>
@endsection
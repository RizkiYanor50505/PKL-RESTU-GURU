@extends('pemeliharaanalat.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold tracking-tight">Dashboard Pelaporan & Pemeliharaan Alat</h1>
    <p class="mt-1 text-sm text-slate-500">Halaman ini masih kosong dan siap diisi.</p>

    <dl class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <dt class="text-xs font-semibold uppercase text-slate-500">Controller</dt>
            <dd class="mt-1 font-mono text-sm">app/Http/Controllers/PemeliharaanAlat/</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <dt class="text-xs font-semibold uppercase text-slate-500">Views</dt>
            <dd class="mt-1 font-mono text-sm">resources/views/pemeliharaanalat/</dd>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <dt class="text-xs font-semibold uppercase text-slate-500">Routes</dt>
            <dd class="mt-1 font-mono text-sm">routes/pemeliharaanalat.php</dd>
        </div>
    </dl>
@endsection
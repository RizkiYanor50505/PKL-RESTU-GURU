@extends('penyewaanreklame.layouts.app')

@section('title', 'Detail Klien ' . $klien->nama_klien)

@section('content')
    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-violet-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('penyewaanreklame.klien.index') }}" class="hover:text-violet-600">Data Klien</a>
                <span>/</span>
                <span class="text-slate-700">Detail</span>
            </div>
            <div class="mt-1 flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $klien->nama_klien }}</h1>
                <span class="rounded-full px-3 py-0.5 text-xs font-semibold {{ $klien->kategori_klien === 'perusahaan' ? 'bg-sky-100 text-sky-700' : 'bg-violet-100 text-violet-700' }}">
                    {{ ucfirst($klien->kategori_klien) }}
                </span>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $klien->no_wa) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                <i class="ph ph-whatsapp-logo text-base"></i>
                Chat WhatsApp
            </a>
            <a href="{{ route('penyewaanreklame.klien.edit', $klien) }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-violet-700">
                <i class="ph ph-pencil-simple text-sm"></i>
                Edit Klien
            </a>
        </div>
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-700">Identitas &amp; Dokumen Pajak</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">PIC</dt>
                    <dd class="font-semibold text-slate-900">{{ $klien->nama_pic ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">No. WhatsApp</dt>
                    <dd class="font-medium text-slate-900">{{ $klien->no_wa }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Email</dt>
                    <dd class="font-medium text-slate-900">{{ $klien->email ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">NPWP</dt>
                    <dd class="font-mono text-xs font-semibold text-slate-900">{{ $klien->npwp ?? 'Belum diisi' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Alamat</dt>
                    <dd class="mt-1 text-slate-900">{{ $klien->alamat ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-700">Riwayat Penawaran &amp; Kontrak</h2>
            @forelse ($klien->kontrakSewas as $kontrak)
                <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                   class="mt-3 block rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-xs hover:border-violet-200 hover:bg-violet-50/40">
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-bold text-slate-900">{{ $kontrak->no_kontrak ?? $kontrak->no_penawaran }}</span>
                        <span class="rounded-full bg-violet-100 px-2.5 py-0.5 font-semibold uppercase text-violet-700">
                            {{ $kontrak->status_kontrak }}
                        </span>
                    </div>
                    <div class="mt-1 font-semibold text-slate-700">
                        {{ $kontrak->titikReklame->kode_titik ?? '-' }} &mdash; {{ $kontrak->titikReklame->wilayah ?? '-' }}
                    </div>
                    <div class="mt-0.5 text-slate-500">
                        Rp {{ number_format((float) $kontrak->nilai_kontrak, 0, ',', '.') }}
                        &middot; {{ $kontrak->durasi_bulan }} bulan
                    </div>
                </a>
            @empty
                <p class="mt-4 text-sm text-slate-500">Klien ini belum memiliki riwayat penawaran atau kontrak.</p>
            @endforelse
        </div>
    </div>
@endsection

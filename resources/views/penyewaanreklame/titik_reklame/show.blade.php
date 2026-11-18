@extends('penyewaanreklame.layouts.app')

@section('title', 'Detail Titik ' . $titikReklame->kode_titik)

@section('content')
    @include('penyewaanreklame.partials.flash')

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-violet-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('penyewaanreklame.titik-reklame.index') }}" class="hover:text-violet-600">Titik Reklame</a>
                <span>/</span>
                <span class="text-slate-700">{{ $titikReklame->kode_titik }}</span>
            </div>
            <div class="mt-1.5 flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ $titikReklame->kode_titik }}</h1>
                <span class="rounded-full px-3 py-0.5 text-xs font-bold {{ match ($titikReklame->status_titik) { 'Tersedia' => 'bg-emerald-100 text-emerald-700', 'Tersewa' => 'bg-violet-100 text-violet-700', default => 'bg-amber-100 text-amber-700' } }}">
                    {{ $titikReklame->status_titik }}
                </span>
                <span class="rounded-lg px-2.5 py-0.5 text-xs font-semibold {{ $titikReklame->kategori_space === 'internal' ? 'bg-slate-900 text-white' : 'bg-sky-100 text-sky-700' }}">
                    {{ $titikReklame->kategori_space === 'internal' ? 'Space Internal RGP' : 'Space Mitra RGP' }}
                </span>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('penyewaanreklame.kontrak.create', ['titik_reklame_id' => $titikReklame->id]) }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-violet-200 bg-violet-50 px-3.5 py-2 text-xs font-semibold text-violet-700 transition hover:bg-violet-100">
                <i class="ph ph-file-plus text-sm"></i>
                Buat Penawaran
            </a>
            <a href="{{ route('penyewaanreklame.katalog.show', $titikReklame) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i class="ph ph-globe text-sm text-violet-600"></i>
                Pratinjau Publik
            </a>
            <a href="{{ route('penyewaanreklame.titik-reklame.edit', $titikReklame) }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-violet-200 transition hover:bg-violet-700">
                <i class="ph ph-pencil-simple text-sm"></i>
                Edit Titik
            </a>
        </div>
    </div>

    <div class="mt-6">
        @include('penyewaanreklame.partials.leaflet_map', ['titik' => $titikReklame])
    </div>

    {{-- Informasi Detail Spesifikasi & Izin --}}
    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-slate-700">
                <i class="ph ph-sliders-horizontal text-base text-violet-600"></i>
                Spesifikasi &amp; Perizinan
            </h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2.5">
                    <dt class="text-slate-500">Wilayah</dt>
                    <dd class="font-semibold text-slate-900">{{ $titikReklame->wilayah }}</dd>
                </div>
                <div class="border-b border-slate-100 pb-2.5">
                    <dt class="text-slate-500">Alamat &amp; Patokan</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $titikReklame->alamat_lokasi }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2.5">
                    <dt class="text-slate-500">Ukuran Papan</dt>
                    <dd class="font-semibold text-slate-900">{{ $titikReklame->ukuran }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2.5">
                    <dt class="text-slate-500">Penerangan</dt>
                    <dd class="font-medium text-slate-900">{{ $titikReklame->spesifikasi_lampu ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2.5">
                    <dt class="text-slate-500">No. Izin Daerah</dt>
                    <dd class="font-mono text-xs font-semibold text-slate-900">{{ $titikReklame->no_izin ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2.5">
                    <dt class="text-slate-500">Masa Berlaku Izin</dt>
                    <dd class="font-medium text-slate-900">{{ optional($titikReklame->tgl_izin_berakhir)->format('d M Y') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Masa Berlaku Pajak</dt>
                    <dd class="font-medium text-slate-900">{{ optional($titikReklame->tgl_pajak_berakhir)->format('d M Y') ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-slate-700">
                <i class="ph ph-clock-counter-clockwise text-base text-violet-600"></i>
                Riwayat &amp; Status Kontrak Sewa
            </h2>
            @forelse ($titikReklame->kontrakSewas as $kontrak)
                <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                   class="mt-3 block rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-xs transition hover:border-violet-200 hover:bg-violet-50/40">
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-bold text-slate-900">{{ $kontrak->no_kontrak ?? $kontrak->no_penawaran }}</span>
                        <span class="rounded-full bg-violet-100 px-2.5 py-0.5 font-semibold uppercase text-violet-700">
                            {{ $kontrak->status_kontrak }}
                        </span>
                    </div>
                    <div class="mt-1.5 font-semibold text-slate-900">
                        {{ $kontrak->klien->nama_klien ?? '-' }}
                        <span class="font-normal text-slate-500">({{ ucfirst($kontrak->klien->kategori_klien ?? '-') }})</span>
                    </div>
                    <div class="mt-1 text-slate-600">
                        Periode: {{ optional($kontrak->tgl_mulai)->format('d M Y') ?? '-' }}
                        &ndash; {{ optional($kontrak->tgl_berakhir)->format('d M Y') ?? '-' }}
                        @if ($kontrak->durasi_bulan)
                            ({{ $kontrak->durasi_bulan }} bulan)
                        @endif
                    </div>
                    @if ($kontrak->materi_iklan)
                        <div class="mt-1 text-slate-500">Materi: {{ $kontrak->materi_iklan }}</div>
                    @endif
                </a>
            @empty
                <div class="flex flex-col items-center justify-center py-10 text-center text-slate-400">
                    <i class="ph ph-files text-3xl"></i>
                    <p class="mt-2 text-sm text-slate-500">Belum ada riwayat penawaran atau kontrak sewa pada titik ini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

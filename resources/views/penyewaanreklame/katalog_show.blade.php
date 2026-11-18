@extends('penyewaanreklame.layouts.app')

@section('title', 'Detail Lokasi ' . $titikReklame->kode_titik)

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('penyewaanreklame.katalog') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-violet-600 hover:text-violet-700">
                <i class="ph ph-arrow-left text-sm"></i>
                Kembali ke Katalog Titik Reklame
            </a>
            <div class="mt-2 flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $titikReklame->wilayah }}</h1>
                <span class="rounded-full px-3 py-0.5 text-xs font-semibold {{ match ($titikReklame->status_titik) { 'Tersedia' => 'bg-emerald-100 text-emerald-700', 'Tersewa' => 'bg-violet-100 text-violet-700', default => 'bg-amber-100 text-amber-700' } }}">
                    {{ $titikReklame->status_titik }}
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-600">{{ $titikReklame->alamat_lokasi }}</p>
        </div>
    </div>

    <div class="mt-6">
        @include('penyewaanreklame.partials.leaflet_map', ['titik' => $titikReklame])
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-700">Informasi Spesifikasi Titik</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Kode Titik</dt>
                    <dd class="font-mono font-semibold text-slate-900">{{ $titikReklame->kode_titik }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Kepemilikan Space</dt>
                    <dd class="font-semibold text-slate-900">
                        {{ $titikReklame->kategori_space === 'internal' ? 'Restu Guru Promosindo' : 'Mitra Restu Guru Promosindo' }}
                    </dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Ukuran Papan</dt>
                    <dd class="font-semibold text-slate-900">{{ $titikReklame->ukuran }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Spesifikasi Penerangan</dt>
                    <dd class="font-medium text-slate-900">{{ $titikReklame->spesifikasi_lampu ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-700">Status Ketersediaan</h2>
            @if ($titikReklame->status_titik === 'Tersewa' && $titikReklame->kontrakAktif)
                <div class="mt-4 rounded-xl border border-violet-100 bg-violet-50/70 p-4 text-sm">
                    <div class="font-semibold text-violet-900">Sedang Tersewa</div>
                    <div class="mt-2 text-slate-700">
                        <span class="text-slate-500">Penyewa:</span>
                        <span class="font-semibold">{{ $titikReklame->kontrakAktif->klien->nama_klien ?? '-' }}</span>
                    </div>
                    <div class="mt-1 text-slate-700">
                        <span class="text-slate-500">Durasi berjalan:</span>
                        <span class="font-medium">
                            {{ optional($titikReklame->kontrakAktif->tgl_mulai)->format('d M Y') }}
                            &ndash; {{ optional($titikReklame->kontrakAktif->tgl_berakhir)->format('d M Y') }}
                            ({{ $titikReklame->kontrakAktif->durasi_bulan }} bulan)
                        </span>
                    </div>
                </div>
            @elseif ($titikReklame->status_titik === 'Tersedia')
                <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50/70 p-4 text-sm text-emerald-800">
                    <div class="font-semibold">Titik Tersedia untuk Disewa</div>
                    <p class="mt-1 text-xs text-emerald-700">
                        Silakan hubungi tim Advertising Restu Guru Promosindo untuk permintaan penawaran lokasi ini (minimal sewa 1 bulan hingga 1 tahun).
                    </p>
                </div>
            @else
                <div class="mt-4 rounded-xl border border-amber-100 bg-amber-50/70 p-4 text-sm text-amber-800">
                    <div class="font-semibold">Sedang Dalam Maintenance</div>
                    <p class="mt-1 text-xs text-amber-700">Titik reklame ini sedang dalam pemeliharaan teknis.</p>
                </div>
            @endif
        </div>
    </div>
@endsection


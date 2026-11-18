@extends('penyewaanreklame.layouts.app')

@section('title', 'Katalog Titik Reklame Publik')

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                <i class="ph ph-megaphone"></i>
                Katalog Lokasi Reklame Publik
            </span>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                Restu Guru Promosindo &mdash; Sebaran Titik Reklame
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Informasi ketersediaan papan reklame wilayah Kalimantan Selatan beserta spesifikasi dan titik lokasi.
            </p>
        </div>
    </div>

    {{-- Filter Publik --}}
    <form method="GET" action="{{ route('penyewaanreklame.katalog') }}"
          class="mt-6 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama jalan / wilayah..."
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">

        <select name="wilayah" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
            <option value="">Semua Wilayah</option>
            @foreach ($daftarWilayah as $wilayah)
                <option value="{{ $wilayah }}" @selected(request('wilayah') === $wilayah)>{{ $wilayah }}</option>
            @endforeach
        </select>

        <select name="status_titik" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
            <option value="">Semua Status</option>
            <option value="Tersedia" @selected(request('status_titik') === 'Tersedia')>Tersedia</option>
            <option value="Tersewa" @selected(request('status_titik') === 'Tersewa')>Tersewa</option>
            <option value="Maintenance" @selected(request('status_titik') === 'Maintenance')>Maintenance</option>
        </select>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-700">
                Cari Titik
            </button>
            <a href="{{ route('penyewaanreklame.katalog') }}"
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    {{-- Grid Katalog Titik Reklame --}}
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($titikReklames as $titik)
            @php
                $badgeColor = match ($titik->status_titik) {
                    'Tersedia' => 'bg-emerald-100 text-emerald-700',
                    'Tersewa' => 'bg-violet-100 text-violet-700',
                    default => 'bg-amber-100 text-amber-700',
                };
                $mapsUrl = $titik->google_maps_url
                    ?? ($titik->latitude && $titik->longitude
                        ? "https://maps.google.com/?q={$titik->latitude},{$titik->longitude}"
                        : null);
            @endphp
            <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                {{-- Foto Fisik Papan --}}
                <div class="relative aspect-video bg-slate-100">
                    @if ($titik->foto_papan)
                        <img src="{{ asset('storage/' . $titik->foto_papan) }}" alt="{{ $titik->kode_titik }}"
                             class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full flex-col items-center justify-center text-slate-400">
                            <i class="ph ph-image text-3xl"></i>
                            <span class="mt-1 text-xs">Foto fisik belum diunggah</span>
                        </div>
                    @endif
                    <span class="absolute right-3 top-3 rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeColor }}">
                        {{ $titik->status_titik }}
                    </span>
                </div>

                <div class="flex flex-1 flex-col p-4">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span class="font-mono font-bold text-slate-700">{{ $titik->kode_titik }}</span>
                        <span>{{ $titik->kategori_space === 'internal' ? 'Space Restu Guru' : 'Space Mitra RGP' }}</span>
                    </div>

                    <h2 class="mt-1.5 text-base font-bold text-slate-900">{{ $titik->wilayah }}</h2>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $titik->alamat_lokasi }}</p>

                    <dl class="mt-3 grid grid-cols-2 gap-2 rounded-lg bg-slate-50 p-2.5 text-xs">
                        <div>
                            <dt class="text-slate-400">Ukuran</dt>
                            <dd class="font-semibold text-slate-700">{{ $titik->ukuran }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Penerangan</dt>
                            <dd class="font-semibold text-slate-700">{{ $titik->spesifikasi_lampu ?? '-' }}</dd>
                        </div>
                    </dl>

                    @if ($titik->status_titik === 'Tersewa' && $titik->kontrakAktif)
                        <div class="mt-3 rounded-lg border border-violet-100 bg-violet-50/70 p-2.5 text-xs">
                            <div class="font-semibold text-violet-900">
                                Penyewa: {{ $titik->kontrakAktif->klien->nama_klien ?? '-' }}
                            </div>
                            <div class="mt-0.5 text-violet-700">
                                Durasi berjalan: {{ optional($titik->kontrakAktif->tgl_mulai)->format('d M Y') }}
                                &ndash; {{ optional($titik->kontrakAktif->tgl_berakhir)->format('d M Y') }}
                                ({{ $titik->kontrakAktif->durasi_bulan }} bulan)
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 flex gap-2 pt-2">
                        <a href="{{ route('penyewaanreklame.katalog.show', $titik) }}"
                           class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-violet-600 px-3 py-2 text-xs font-semibold text-white hover:bg-violet-700">
                            <i class="ph ph-eye text-sm"></i>
                            Detail &amp; Peta
                        </a>
                        @if ($mapsUrl)
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                               title="Buka Google Maps">
                                <i class="ph ph-map-pin text-sm text-rose-500"></i>
                                Maps
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-500">
                Belum ada titik reklame yang tersedia untuk filter ini.
            </div>
        @endforelse
    </div>
@endsection

@extends('penyewaanreklame.layouts.app')

@section('title', 'Katalog Titik Reklame Publik')

@section('content')
    @php
        $totalTitik = $titikReklames->count();
        $tersediaCount = $titikReklames->where('status_titik', 'Tersedia')->count();
        $tersewaCount = $titikReklames->where('status_titik', 'Tersewa')->count();
    @endphp

    {{-- Public Hero Section --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-violet-950 to-indigo-900 p-6 text-white shadow-md sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-violet-200 ring-1 ring-white/20">
                    <i class="ph ph-megaphone text-sm"></i>
                    Restu Guru Promosindo &middot; Divisi Advertising
                </span>
                <h1 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">
                    Katalog Lokasi Titik Reklame Kalimantan Selatan
                </h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-300">
                    Informasi transparan ketersediaan titik billboard, baliho, dan bando jalan lengkap dengan spesifikasi papan, foto lokasi, serta koordinat peta. Durasi sewa fleksibel 1 bulan hingga 1 tahun penuh.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15 backdrop-blur">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-slate-300">Total Titik</div>
                    <div class="mt-0.5 text-2xl font-extrabold">{{ $totalTitik }}</div>
                </div>
                <div class="rounded-2xl bg-emerald-500/20 px-4 py-3 ring-1 ring-emerald-400/30 backdrop-blur">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-emerald-200">Tersedia</div>
                    <div class="mt-0.5 text-2xl font-extrabold text-emerald-300">{{ $tersediaCount }}</div>
                </div>
                <div class="rounded-2xl bg-violet-500/20 px-4 py-3 ring-1 ring-violet-300/30 backdrop-blur">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-violet-200">Tersewa</div>
                    <div class="mt-0.5 text-2xl font-extrabold text-violet-200">{{ $tersewaCount }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Publik --}}
    <form method="GET" action="{{ route('penyewaanreklame.katalog') }}"
          class="mt-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama jalan, kode, wilayah..."
                   class="w-full rounded-xl border border-slate-200 py-2.5 pl-9 pr-3 text-sm focus:border-violet-500 focus:outline-none">
        </div>

        <select name="wilayah" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-violet-500 focus:outline-none">
            <option value="">Semua Wilayah</option>
            @foreach ($daftarWilayah as $wilayah)
                <option value="{{ $wilayah }}" @selected(request('wilayah') === $wilayah)>{{ $wilayah }}</option>
            @endforeach
        </select>

        <select name="status_titik" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-violet-500 focus:outline-none">
            <option value="">Semua Status</option>
            <option value="Tersedia" @selected(request('status_titik') === 'Tersedia')>Tersedia</option>
            <option value="Tersewa" @selected(request('status_titik') === 'Tersewa')>Tersewa</option>
            <option value="Maintenance" @selected(request('status_titik') === 'Maintenance')>Maintenance</option>
        </select>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700">
                Cari Titik
            </button>
            <a href="{{ route('penyewaanreklame.katalog') }}"
               class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    {{-- Grid Katalog Titik Reklame --}}
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($titikReklames as $titik)
            @php
                $badgeColor = match ($titik->status_titik) {
                    'Tersedia' => 'bg-emerald-500 text-white shadow-sm',
                    'Tersewa' => 'bg-violet-600 text-white shadow-sm',
                    default => 'bg-amber-500 text-white shadow-sm',
                };
                $mapsUrl = $titik->google_maps_url
                    ?? ($titik->latitude && $titik->longitude
                        ? "https://maps.google.com/?q={$titik->latitude},{$titik->longitude}"
                        : null);
            @endphp
            <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                {{-- Foto Fisik Papan --}}
                <a href="{{ route('penyewaanreklame.katalog.show', $titik) }}" class="relative block aspect-video overflow-hidden bg-slate-100">
                    @if ($titik->foto_papan)
                        <img src="{{ asset('storage/' . $titik->foto_papan) }}" alt="{{ $titik->kode_titik }}"
                             class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full flex-col items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200/70 text-slate-400">
                            <i class="ph ph-image text-4xl"></i>
                            <span class="mt-1.5 text-xs font-medium">Foto fisik belum tersedia</span>
                        </div>
                    @endif
                    <span class="absolute right-3 top-3 rounded-full px-3 py-1 text-[11px] font-bold tracking-wide {{ $badgeColor }}">
                        {{ $titik->status_titik }}
                    </span>
                    <span class="absolute left-3 top-3 rounded-lg bg-slate-900/75 px-2.5 py-1 font-mono text-[11px] font-semibold text-white backdrop-blur">
                        {{ $titik->kode_titik }}
                    </span>
                </a>

                <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-wide">
                        <span class="text-violet-600">
                            {{ $titik->kategori_space === 'internal' ? 'Space Restu Guru' : 'Space Mitra RGP' }}
                        </span>
                        @if ($mapsUrl)
                            <span class="inline-flex items-center gap-1 text-emerald-600">
                                <i class="ph ph-map-pin"></i> Titik Maps Ada
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-1.5 text-lg font-bold text-slate-900">{{ $titik->wilayah }}</h2>
                    <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-600">{{ $titik->alamat_lokasi }}</p>

                    <dl class="mt-4 grid grid-cols-2 gap-2.5 rounded-xl bg-slate-50 p-3 text-xs ring-1 ring-slate-100">
                        <div>
                            <dt class="text-[11px] text-slate-400">Ukuran Papan</dt>
                            <dd class="mt-0.5 font-semibold text-slate-800">{{ $titik->ukuran }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] text-slate-400">Penerangan</dt>
                            <dd class="mt-0.5 font-semibold text-slate-800">{{ $titik->spesifikasi_lampu ?? '-' }}</dd>
                        </div>
                    </dl>

                    @if ($titik->status_titik === 'Tersewa' && $titik->kontrakAktif)
                        <div class="mt-3 rounded-xl border border-violet-100 bg-violet-50/70 p-3 text-xs">
                            <div class="font-bold text-violet-900">
                                Penyewa: {{ $titik->kontrakAktif->klien->nama_klien ?? '-' }}
                            </div>
                            <div class="mt-0.5 text-violet-700">
                                {{ optional($titik->kontrakAktif->tgl_mulai)->format('d M Y') }}
                                &ndash; {{ optional($titik->kontrakAktif->tgl_berakhir)->format('d M Y') }}
                                ({{ $titik->kontrakAktif->durasi_bulan }} bln)
                            </div>
                        </div>
                    @endif

                    <div class="mt-auto flex gap-2 pt-4">
                        <a href="{{ route('penyewaanreklame.katalog.show', $titik) }}"
                           class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-violet-600 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-violet-700">
                            <i class="ph ph-eye text-sm"></i>
                            Detail &amp; Peta
                        </a>
                        @if ($mapsUrl)
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                               title="Buka Google Maps">
                                <i class="ph ph-map-pin text-sm text-rose-500"></i>
                                Maps
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <i class="ph ph-magnifying-glass text-4xl text-slate-300"></i>
                <p class="mt-2 text-sm font-semibold text-slate-700">Titik reklame tidak ditemukan</p>
                <p class="mt-1 text-xs text-slate-500">Coba ubah filter wilayah atau kata kunci pencarian.</p>
            </div>
        @endforelse
    </div>
@endsection

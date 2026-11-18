@extends('penyewaanreklame.layouts.app')

@section('title', 'Katalog Titik Reklame Publik')

@section('content')
    @php
        $totalTitik = $titikReklames->count();
        $tersediaCount = $titikReklames->where('status_titik', 'Tersedia')->count();
        $tersewaCount = $titikReklames->where('status_titik', 'Tersewa')->count();
    @endphp

    {{-- Header Katalog Publik (Gaya Obsidian Selaras Halaman Login) --}}
    <div class="rounded-3xl bg-obsidian p-6 text-white sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span>RESTU GURU PROMOSINDO &middot; DIVISI ADVERTISING</span>
                </div>
                <h1 class="mt-2.5 text-2xl font-extrabold tracking-[-0.03em] sm:text-3xl">
                    Katalog Lokasi Titik Reklame Kalimantan Selatan
                </h1>
                <p class="mt-2 text-sm leading-relaxed text-white/75">
                    Daftar ketersediaan titik billboard, baliho, dan bando jalan lengkap dengan ukuran papan, foto lokasi, serta koordinat Google Maps. Durasi sewa fleksibel 1 bulan hingga 1 tahun penuh.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="rounded-2xl bg-white/[0.07] px-4 py-3">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-white/60">Total Titik</div>
                    <div class="mt-0.5 font-mono text-2xl font-bold tabular-nums">{{ $totalTitik }}</div>
                </div>
                <div class="rounded-2xl bg-white/[0.07] px-4 py-3">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-emerald-300">Tersedia</div>
                    <div class="mt-0.5 font-mono text-2xl font-bold text-emerald-400 tabular-nums">{{ $tersediaCount }}</div>
                </div>
                <div class="rounded-2xl bg-white/[0.07] px-4 py-3">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-white/60">Tersewa</div>
                    <div class="mt-0.5 font-mono text-2xl font-bold tabular-nums">{{ $tersewaCount }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Publik --}}
    <form method="GET" action="{{ route('penyewaanreklame.katalog') }}"
          class="mt-6 grid gap-3 rounded-2xl border border-slate-200/90 bg-white p-4 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama jalan, kode, wilayah..."
               class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:border-slate-900 focus:outline-none">

        <select name="wilayah" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Semua Wilayah</option>
            @foreach ($daftarWilayah as $wilayah)
                <option value="{{ $wilayah }}" @selected(request('wilayah') === $wilayah)>{{ $wilayah }}</option>
            @endforeach
        </select>

        <select name="status_titik" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Semua Status</option>
            <option value="Tersedia" @selected(request('status_titik') === 'Tersedia')>Tersedia</option>
            <option value="Tersewa" @selected(request('status_titik') === 'Tersewa')>Tersewa</option>
            <option value="Maintenance" @selected(request('status_titik') === 'Maintenance')>Maintenance</option>
        </select>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 rounded-xl bg-obsidian px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                Cari Titik
            </button>
            <a href="{{ route('penyewaanreklame.katalog') }}"
               class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    {{-- Grid Katalog Titik Reklame --}}
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($titikReklames as $titik)
            @php
                $badgeColor = match ($titik->status_titik) {
                    'Tersedia' => 'bg-emerald-600 text-white',
                    'Tersewa' => 'bg-obsidian text-white',
                    default => 'bg-amber-500 text-white',
                };
                $mapsUrl = $titik->google_maps_url
                    ?? ($titik->latitude && $titik->longitude
                        ? "https://maps.google.com/?q={$titik->latitude},{$titik->longitude}"
                        : null);
            @endphp
            <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white transition hover:border-slate-300">
                <a href="{{ route('penyewaanreklame.katalog.show', $titik) }}" class="relative block aspect-video overflow-hidden bg-slate-100">
                    @if ($titik->foto_papan)
                        <img src="{{ asset('storage/' . $titik->foto_papan) }}" alt="{{ $titik->kode_titik }}"
                             class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full flex-col items-center justify-center text-slate-400">
                            <i class="ph ph-image text-3xl"></i>
                            <span class="mt-1 text-xs">Foto fisik belum diunggah</span>
                        </div>
                    @endif
                    <span class="absolute right-3 top-3 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $badgeColor }}">
                        {{ $titik->status_titik }}
                    </span>
                    <span class="absolute left-3 top-3 rounded-md bg-white/90 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-900">
                        {{ $titik->kode_titik }}
                    </span>
                </a>

                <div class="flex flex-1 flex-col p-5">
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                        {{ $titik->kategori_space === 'internal' ? 'Space Restu Guru' : 'Space Mitra RGP' }}
                    </div>

                    <h2 class="mt-1 text-base font-bold text-slate-900">{{ $titik->wilayah }}</h2>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $titik->alamat_lokasi }}</p>

                    <dl class="mt-4 grid grid-cols-2 gap-2.5 rounded-xl bg-canvas p-3 text-xs">
                        <div>
                            <dt class="text-[11px] text-slate-500">Ukuran</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">{{ $titik->ukuran }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] text-slate-500">Penerangan</dt>
                            <dd class="mt-0.5 font-semibold text-slate-900">{{ $titik->spesifikasi_lampu ?? '-' }}</dd>
                        </div>
                    </dl>

                    @if ($titik->status_titik === 'Tersewa' && $titik->kontrakAktif)
                        <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs">
                            <div class="font-semibold text-slate-900">
                                Penyewa: {{ $titik->kontrakAktif->klien->nama_klien ?? '-' }}
                            </div>
                            <div class="mt-0.5 font-mono text-[11px] text-slate-600">
                                {{ optional($titik->kontrakAktif->tgl_mulai)->format('d M Y') }}
                                &ndash; {{ optional($titik->kontrakAktif->tgl_berakhir)->format('d M Y') }}
                                ({{ $titik->kontrakAktif->durasi_bulan }} bln)
                            </div>
                        </div>
                    @endif

                    <div class="mt-auto flex gap-2 pt-4">
                        <a href="{{ route('penyewaanreklame.katalog.show', $titik) }}"
                           class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-obsidian px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                            Detail &amp; Peta
                        </a>
                        @if ($mapsUrl)
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                               title="Buka Google Maps">
                                <i class="ph ph-map-pin text-sm text-emerald-600"></i>
                                Maps
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-500">
                Belum ada titik reklame yang sesuai filter ini.
            </div>
        @endforelse
    </div>
@endsection

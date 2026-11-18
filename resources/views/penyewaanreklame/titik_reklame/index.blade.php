@extends('penyewaanreklame.layouts.app')

@section('title', 'Daftar Titik Reklame')

@section('content')
    @include('penyewaanreklame.partials.flash')

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Master Titik Reklame</span>
            </div>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Daftar Titik Reklame</h1>
            <p class="mt-1 text-sm text-slate-500">
                Data operasional lokasi reklame Restu Guru Promosindo (Internal &amp; Mitra).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('penyewaanreklame.titik-reklame.import') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700  transition hover:bg-slate-50">
                <i class="ph ph-upload-simple text-base text-emerald-600"></i>
                Import CSV
            </a>
            <a href="{{ route('penyewaanreklame.titik-reklame.create') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-obsidian px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                <i class="ph ph-plus text-base"></i>
                Tambah Titik Reklame
            </a>
        </div>
    </div>

    {{-- Kartu Ringkasan Status Titik --}}
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 ">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Titik</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/90 bg-white p-4 ">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Tersedia</p>
            <p class="mt-2 text-2xl font-extrabold text-emerald-700">{{ $summary['tersedia'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 ">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-900">Tersewa</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $summary['tersewa'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/90 bg-white p-4 ">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Maintenance</p>
            <p class="mt-2 text-2xl font-extrabold text-amber-700">{{ $summary['maintenance'] }}</p>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <form method="GET" action="{{ route('penyewaanreklame.titik-reklame.index') }}"
          class="mt-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4  sm:grid-cols-2 lg:grid-cols-5">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode, alamat, ukuran..."
                   class="w-full rounded-xl border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-slate-900 focus:outline-none">
        </div>

        <select name="wilayah" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Semua Wilayah</option>
            @foreach ($daftarWilayah as $wilayah)
                <option value="{{ $wilayah }}" @selected(request('wilayah') === $wilayah)>{{ $wilayah }}</option>
            @endforeach
        </select>

        <select name="kategori_space" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Semua Kategori Space</option>
            <option value="internal" @selected(request('kategori_space') === 'internal')>Internal (Restu Guru)</option>
            <option value="mitra" @selected(request('kategori_space') === 'mitra')>Mitra Restu Guru</option>
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
                Filter
            </button>
            <a href="{{ route('penyewaanreklame.titik-reklame.index') }}"
               class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    {{-- Tabel Data Titik Reklame --}}
    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white ">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5">Foto &amp; Kode</th>
                        <th class="px-4 py-3.5">Wilayah &amp; Lokasi</th>
                        <th class="px-4 py-3.5">Spesifikasi</th>
                        <th class="px-4 py-3.5">Status &amp; Penyewa Aktif</th>
                        <th class="px-4 py-3.5">Izin / Pajak</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($titikReklames as $titik)
                        @php
                            $statusBadge = match ($titik->status_titik) {
                                'Tersedia' => 'bg-emerald-100 text-emerald-700',
                                'Tersewa' => 'bg-slate-900 text-white',
                                default => 'bg-amber-100 text-amber-700',
                            };
                            $mapsUrl = $titik->google_maps_url
                                ?? ($titik->latitude && $titik->longitude
                                    ? "https://maps.google.com/?q={$titik->latitude},{$titik->longitude}"
                                    : null);
                            $izinDekat = $titik->tgl_izin_berakhir && $titik->tgl_izin_berakhir->lte(now()->addDays(30));
                            $pajakDekat = $titik->tgl_pajak_berakhir && $titik->tgl_pajak_berakhir->lte(now()->addDays(30));
                        @endphp
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3.5 align-top">
                                <div class="flex items-start gap-3">
                                    @if ($titik->foto_papan)
                                        <img src="{{ asset('storage/' . $titik->foto_papan) }}" alt="{{ $titik->kode_titik }}"
                                             class="h-11 w-14 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
                                    @else
                                        <div class="flex h-11 w-14 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                            <i class="ph ph-image text-lg"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('penyewaanreklame.titik-reklame.show', $titik) }}"
                                           class="font-mono text-xs font-bold text-slate-900 hover:underline">
                                            {{ $titik->kode_titik }}
                                        </a>
                                        <div>
                                            <span class="mt-1 inline-flex rounded-md px-2 py-0.5 text-[10px] font-semibold {{ $titik->kategori_space === 'internal' ? 'bg-slate-900 text-white' : 'bg-sky-100 text-sky-700' }}">
                                                {{ $titik->kategori_space === 'internal' ? 'Internal RGP' : 'Mitra RGP' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-semibold text-slate-900">{{ $titik->wilayah }}</div>
                                <div class="mt-0.5 max-w-sm text-xs leading-relaxed text-slate-600">{{ $titik->alamat_lokasi }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 align-top text-xs text-slate-700">
                                <div class="font-semibold">{{ $titik->ukuran }}</div>
                                <div class="mt-0.5 text-slate-500">{{ $titik->spesifikasi_lampu ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusBadge }}">
                                    {{ $titik->status_titik }}
                                </span>
                                @if ($titik->status_titik === 'Tersewa' && $titik->kontrakAktif)
                                    <div class="mt-1.5 text-xs">
                                        <div class="font-semibold text-slate-800">
                                            {{ $titik->kontrakAktif->klien->nama_klien ?? '-' }}
                                        </div>
                                        <div class="text-slate-500">
                                            {{ optional($titik->kontrakAktif->tgl_mulai)->format('d M Y') }}
                                            &ndash;
                                            {{ optional($titik->kontrakAktif->tgl_berakhir)->format('d M Y') }}
                                            ({{ $titik->kontrakAktif->durasi_bulan }} bln)
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 align-top text-xs">
                                <div class="{{ $izinDekat ? 'font-semibold text-rose-600' : 'text-slate-600' }}">
                                    Izin: {{ optional($titik->tgl_izin_berakhir)->format('d M Y') ?? '-' }}
                                </div>
                                <div class="mt-0.5 {{ $pajakDekat ? 'font-semibold text-rose-600' : 'text-slate-600' }}">
                                    Pajak: {{ optional($titik->tgl_pajak_berakhir)->format('d M Y') ?? '-' }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right align-top">
                                <div class="inline-flex items-center gap-1.5">
                                    @if ($mapsUrl)
                                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" title="Buka Google Maps"
                                           class="inline-flex items-center rounded-lg border border-slate-200 p-1.5 text-slate-600 transition hover:bg-slate-50">
                                            <i class="ph ph-map-pin text-sm text-rose-500"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('penyewaanreklame.titik-reklame.show', $titik) }}"
                                       class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                        Detail
                                    </a>
                                    <a href="{{ route('penyewaanreklame.titik-reklame.edit', $titik) }}"
                                       class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-900 transition hover:bg-slate-100">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('penyewaanreklame.titik-reklame.destroy', $titik) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus titik {{ $titik->kode_titik }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                                Belum ada data titik reklame yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

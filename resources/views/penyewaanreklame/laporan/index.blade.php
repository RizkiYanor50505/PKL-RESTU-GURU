@extends('penyewaanreklame.layouts.app')

@section('title', 'Laporan & Rekap Reklame')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Laporan &amp; Rekap</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Laporan Operasional &amp; Sewa Reklame</h1>
            <p class="mt-1 text-sm text-slate-500">
                Rekapitulasi sebaran titik reklame, masa sewa berjalan, izin daerah, dan realisasi pembayaran.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('penyewaanreklame.laporan.export-csv', request()->query()) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                <i class="ph ph-file-csv text-base"></i>
                Ekspor CSV (Excel)
            </a>
            <a href="{{ route('penyewaanreklame.laporan.cetak', request()->query()) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white hover:bg-slate-700">
                <i class="ph ph-printer text-base"></i>
                Cetak / PDF
            </a>
        </div>
    </div>

    {{-- Filter Laporan --}}
    <form method="GET" action="{{ route('penyewaanreklame.laporan.index') }}"
          class="mt-6 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-6">
        <select name="wilayah" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Wilayah</option>
            @foreach ($daftarWilayah as $wilayah)
                <option value="{{ $wilayah }}" @selected(request('wilayah') === $wilayah)>{{ $wilayah }}</option>
            @endforeach
        </select>

        <select name="kategori_space" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Space</option>
            <option value="internal" @selected(request('kategori_space') === 'internal')>Internal RGP</option>
            <option value="mitra" @selected(request('kategori_space') === 'mitra')>Mitra RGP</option>
        </select>

        <select name="status_titik" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="Tersedia" @selected(request('status_titik') === 'Tersedia')>Tersedia</option>
            <option value="Tersewa" @selected(request('status_titik') === 'Tersewa')>Tersewa</option>
            <option value="Maintenance" @selected(request('status_titik') === 'Maintenance')>Maintenance</option>
        </select>

        <input type="date" name="dari_tgl" value="{{ request('dari_tgl') }}" title="Dari tanggal penawaran"
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <input type="date" name="sampai_tgl" value="{{ request('sampai_tgl') }}" title="Sampai tanggal penawaran"
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm">

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 rounded-lg bg-obsidian px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                Terapkan
            </button>
            <a href="{{ route('penyewaanreklame.laporan.index') }}"
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    {{-- Kartu Rekap Keuangan & Okupansi --}}
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">Titik Sesuai Filter</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $rekap['total_titik'] }}</p>
            <p class="mt-0.5 text-xs text-slate-500">{{ $rekap['tersedia'] }} Tersedia &middot; {{ $rekap['tersewa'] }} Tersewa</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-slate-900">Total Kontrak (Aktif/Selesai)</p>
            <p class="mt-2 text-xl font-bold text-slate-900">Rp {{ number_format($rekap['total_nilai'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-emerald-700">Total Pembayaran Masuk</p>
            <p class="mt-2 text-xl font-bold text-emerald-700">Rp {{ number_format($rekap['total_terbayar'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">Jumlah Transaksi</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $kontraks->count() }}</p>
        </div>
    </div>

    {{-- Tabel Rekap Titik Reklame --}}
    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-4 py-3 text-sm font-bold text-slate-800">
            Rekap Titik Reklame &amp; Penyewa Berjalan
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Kode &amp; Space</th>
                        <th class="px-4 py-3">Wilayah &amp; Lokasi</th>
                        <th class="px-4 py-3">Ukuran &amp; Lampu</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Penyewa &amp; Periode</th>
                        <th class="px-4 py-3">Izin / Pajak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($titikReklames as $titik)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 align-top">
                                <div class="font-mono font-bold text-slate-900">{{ $titik->kode_titik }}</div>
                                <div class="text-slate-500">{{ $titik->kategori_space === 'internal' ? 'Internal' : 'Mitra' }}</div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="font-semibold text-slate-900">{{ $titik->wilayah }}</div>
                                <div class="max-w-xs text-slate-600">{{ $titik->alamat_lokasi }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-top text-slate-700">
                                <div>{{ $titik->ukuran }}</div>
                                <div class="text-slate-500">{{ $titik->spesifikasi_lampu ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-top font-semibold">{{ $titik->status_titik }}</td>
                            <td class="px-4 py-3 align-top">
                                @if ($titik->kontrakAktif)
                                    <div class="font-semibold text-slate-900">{{ $titik->kontrakAktif->klien->nama_klien ?? '-' }}</div>
                                    <div class="text-slate-500">
                                        {{ optional($titik->kontrakAktif->tgl_mulai)->format('d/m/Y') }} - {{ optional($titik->kontrakAktif->tgl_berakhir)->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-top text-slate-600">
                                <div>Izin: {{ optional($titik->tgl_izin_berakhir)->format('d/m/Y') ?? '-' }}</div>
                                <div>Pajak: {{ optional($titik->tgl_pajak_berakhir)->format('d/m/Y') ?? '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Tidak ada data untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

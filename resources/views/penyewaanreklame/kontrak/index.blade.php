@extends('penyewaanreklame.layouts.app')

@section('title', 'Penawaran & Kontrak Sewa')

@section('content')
    @include('penyewaanreklame.partials.flash')

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Penawaran &amp; Kontrak Sewa</span>
            </div>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Transaksi Penawaran &amp; Kontrak Sewa</h1>
            <p class="mt-1 text-sm text-slate-500">
                Alur tanpa holding awal: Penawaran &rarr; ACC Klien &rarr; Pembayaran / Pemasangan &rarr; Monitoring Jatuh Tempo &amp; Penurunan Materi.
            </p>
        </div>

        <a href="{{ route('penyewaanreklame.kontrak.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-obsidian px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
            <i class="ph ph-plus text-base"></i>
            Buat Surat Penawaran
        </a>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 ">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Penawaran / ACC</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $summary['penawaran'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 ">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-900">Kontrak Aktif</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $summary['aktif'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/90 bg-white p-4 ">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Jatuh Tempo &le; 14 Hari</p>
            <p class="mt-2 text-2xl font-extrabold text-amber-700">{{ $summary['jatuh_tempo'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/90 bg-white p-4 ">
            <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Perlu Pencopotan</p>
            <p class="mt-2 text-2xl font-extrabold text-rose-700">{{ $summary['perlu_dicopot'] }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('penyewaanreklame.kontrak.index') }}"
          class="mt-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4  sm:grid-cols-2 lg:grid-cols-5">
        <div class="relative">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. surat, klien, kode..."
                   class="w-full rounded-xl border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-slate-900 focus:outline-none">
        </div>

        <select name="status_kontrak" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Semua Status Kontrak</option>
            <option value="penawaran" @selected(request('status_kontrak') === 'penawaran')>Penawaran</option>
            <option value="acc" @selected(request('status_kontrak') === 'acc')>ACC</option>
            <option value="aktif" @selected(request('status_kontrak') === 'aktif')>Aktif</option>
            <option value="selesai" @selected(request('status_kontrak') === 'selesai')>Selesai</option>
            <option value="batal" @selected(request('status_kontrak') === 'batal')>Batal</option>
        </select>

        <select name="status_materi" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Semua Status Materi</option>
            <option value="belum_pasang" @selected(request('status_materi') === 'belum_pasang')>Belum Pasang</option>
            <option value="terpasang" @selected(request('status_materi') === 'terpasang')>Terpasang</option>
            <option value="perlu_dicopot" @selected(request('status_materi') === 'perlu_dicopot')>Perlu Dicopot</option>
            <option value="sudah_dicopot" @selected(request('status_materi') === 'sudah_dicopot')>Sudah Dicopot</option>
        </select>

        <select name="kategori_klien" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Semua Kategori Klien</option>
            <option value="perusahaan" @selected(request('kategori_klien') === 'perusahaan')>Perusahaan</option>
            <option value="perorangan" @selected(request('kategori_klien') === 'perorangan')>Perorangan</option>
        </select>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 rounded-xl bg-obsidian px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                Filter
            </button>
            <a href="{{ route('penyewaanreklame.kontrak.index') }}"
               class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white ">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5">No. Surat / Kontrak</th>
                        <th class="px-4 py-3.5">Titik Reklame</th>
                        <th class="px-4 py-3.5">Klien</th>
                        <th class="px-4 py-3.5">Masa Sewa</th>
                        <th class="px-4 py-3.5">Nilai &amp; Terbayar</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kontraks as $kontrak)
                        @php
                            $sisaHari = $kontrak->tgl_berakhir
                                ? (int) now()->startOfDay()->diffInDays($kontrak->tgl_berakhir, false)
                                : null;
                            $badgeKontrak = match ($kontrak->status_kontrak) {
                                'penawaran' => 'bg-slate-100 text-slate-700',
                                'acc' => 'bg-sky-100 text-sky-700',
                                'aktif' => 'bg-slate-900 text-white',
                                'selesai' => 'bg-emerald-100 text-emerald-700',
                                default => 'bg-rose-100 text-rose-700',
                            };
                        @endphp
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3.5 align-top">
                                <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                                   class="font-mono text-xs font-bold text-slate-900 hover:underline">
                                    {{ $kontrak->no_kontrak ?? $kontrak->no_penawaran }}
                                </a>
                                @if ($kontrak->no_kontrak)
                                    <div class="mt-0.5 font-mono text-[11px] text-slate-400">{{ $kontrak->no_penawaran }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 align-top text-xs">
                                <div class="font-bold text-slate-900">{{ $kontrak->titikReklame->kode_titik ?? '-' }}</div>
                                <div class="text-slate-600">{{ $kontrak->titikReklame->wilayah ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-xs">
                                <div class="font-semibold text-slate-900">{{ $kontrak->klien->nama_klien ?? '-' }}</div>
                                <span class="mt-0.5 inline-flex rounded px-1.5 py-0.5 text-[10px] font-semibold uppercase {{ ($kontrak->klien->kategori_klien ?? '') === 'perusahaan' ? 'bg-sky-100 text-sky-700' : 'bg-slate-900 text-white' }}">
                                    {{ $kontrak->klien->kategori_klien ?? '-' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 align-top text-xs text-slate-700">
                                @if ($kontrak->tgl_mulai && $kontrak->tgl_berakhir)
                                    <div>{{ $kontrak->tgl_mulai->format('d M Y') }} &ndash; {{ $kontrak->tgl_berakhir->format('d M Y') }}</div>
                                    @if ($kontrak->status_kontrak === 'aktif' && $sisaHari !== null)
                                        <div class="mt-0.5 font-semibold {{ $sisaHari <= 0 ? 'text-rose-600' : ($sisaHari <= 14 ? 'text-amber-600' : 'text-slate-500') }}">
                                            {{ $sisaHari <= 0 ? 'Lewat jatuh tempo' : "Sisa {$sisaHari} hari" }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400">Rencana {{ $kontrak->durasi_bulan }} bulan</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 align-top text-xs">
                                <div class="font-semibold text-slate-900">
                                    Rp {{ number_format((float) $kontrak->nilai_kontrak, 0, ',', '.') }}
                                </div>
                                <div class="mt-0.5 text-slate-500">
                                    Terbayar: Rp {{ number_format((float) ($kontrak->pembayarans_sum_jumlah_bayar ?? 0), 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 align-top">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase {{ $badgeKontrak }}">
                                    {{ $kontrak->status_kontrak }}
                                </span>
                                @if ($kontrak->status_materi === 'perlu_dicopot')
                                    <div class="mt-1 text-[11px] font-semibold text-rose-600">Materi perlu dicopot</div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right align-top">
                                <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                                   class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-900 transition hover:bg-slate-100">
                                    Kelola Alur
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">
                                Belum ada data penawaran atau kontrak sewa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

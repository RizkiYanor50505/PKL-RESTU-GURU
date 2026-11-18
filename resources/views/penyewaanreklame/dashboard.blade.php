@extends('penyewaanreklame.layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('penyewaanreklame.partials.flash')

    @php
        $okupansiPct = $summary['total'] > 0 ? round(($summary['tersewa'] / $summary['total']) * 100) : 0;
    @endphp

    <div class="space-y-6">
        {{-- Row 1: Executive Header (7 cols) + 2x2 Metric Grid (5 cols) --}}
        <div class="grid grid-cols-1 items-stretch gap-6 lg:grid-cols-12">
            <section class="flex flex-col justify-between gap-6 rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-7 lg:col-span-7">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-2 rounded-lg border border-slate-200/70 bg-slate-100/90 px-3 py-1.5 text-xs font-semibold text-slate-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>DIVISI ADVERTISING &middot; RESTU GURU PROMOSINDO</span>
                        </div>
                        <span class="rounded-md border border-slate-200/60 bg-slate-50 px-2.5 py-1 font-mono text-xs text-slate-500 tabular-nums">
                            {{ now()->locale('id')->translatedFormat('l, j M Y') }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                            Ringkasan Titik Reklame &amp; Kontrak Sewa
                        </h1>
                        <p class="max-w-xl text-xs leading-relaxed text-slate-600 sm:text-sm">
                            Saat ini <span class="font-semibold text-slate-900">{{ $summary['tersewa'] }} dari {{ $summary['total'] }} titik reklame sedang tersewa</span> ({{ $okupansiPct }}% okupansi), dengan {{ $summary['tersedia'] }} lokasi siap ditawarkan kepada calon klien.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5 pt-1 sm:grid-cols-2">
                        <div class="flex items-center gap-2.5 rounded-lg border border-slate-100 bg-slate-50 p-2.5 text-xs">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md border border-amber-200/70 bg-amber-50 text-amber-700">
                                <i class="ph ph-clock-countdown text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium text-slate-800">{{ $kontrakAkanHabis->count() }} Kontrak Jatuh Tempo</div>
                                <div class="truncate text-[11px] text-slate-500">Pengingat konfirmasi &le; 14 hari</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 rounded-lg border border-slate-100 bg-slate-50 p-2.5 text-xs">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md border border-rose-200/70 bg-rose-50 text-rose-700">
                                <i class="ph ph-scissors text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium text-slate-800">{{ $materiPerluDicopot->count() }} Materi Perlu Dicopot</div>
                                <div class="truncate text-[11px] text-slate-500">Menunggu penurunan tim lapangan</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 border-t border-slate-100 pt-4">
                    <a href="{{ route('penyewaanreklame.kontrak.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-obsidian px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800">
                        <i class="ph ph-file-plus text-sm text-emerald-400"></i>
                        <span>Buat Surat Penawaran</span>
                    </a>
                    <a href="{{ route('penyewaanreklame.titik-reklame.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="ph ph-plus text-sm text-slate-500"></i>
                        <span>Tambah Titik</span>
                    </a>
                    <a href="{{ route('penyewaanreklame.laporan.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="ph ph-export text-sm text-slate-500"></i>
                        <span>Laporan &amp; Ekspor</span>
                    </a>
                </div>
            </section>

            <div class="grid grid-cols-2 gap-4 lg:col-span-5">
                <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5">
                    <div class="text-xs font-semibold text-slate-500">Titik Tersedia</div>
                    <div class="my-2">
                        <span class="font-mono text-2xl font-bold text-slate-900 tabular-nums sm:text-3xl">{{ $summary['tersedia'] }}</span>
                        <span class="font-mono text-xs font-medium text-slate-400 tabular-nums">/ {{ $summary['total'] }}</span>
                    </div>
                    <div class="text-[11px] font-medium text-emerald-700">{{ $summary['internal'] }} Internal &middot; {{ $summary['mitra'] }} Mitra</div>
                </div>

                <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5">
                    <div class="text-xs font-semibold text-slate-500">Titik Tersewa</div>
                    <div class="my-2">
                        <span class="font-mono text-2xl font-bold text-slate-900 tabular-nums sm:text-3xl">{{ $summary['tersewa'] }}</span>
                        <span class="text-xs font-medium text-slate-400">titik</span>
                    </div>
                    <div class="text-[11px] font-medium text-slate-600">Okupansi {{ $okupansiPct }}% berjalan</div>
                </div>

                <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5">
                    <div class="text-xs font-semibold text-slate-500">Maintenance</div>
                    <div class="my-2">
                        <span class="font-mono text-2xl font-bold text-slate-900 tabular-nums sm:text-3xl">{{ $summary['maintenance'] }}</span>
                        <span class="text-xs font-medium text-slate-400">titik</span>
                    </div>
                    <div class="text-[11px] font-medium {{ $summary['maintenance'] > 0 ? 'text-amber-700' : 'text-slate-500' }}">
                        {{ $summary['maintenance'] > 0 ? 'Perlu perbaikan teknis' : 'Semua rangka normal' }}
                    </div>
                </div>

                <div class="flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5">
                    <div class="text-xs font-semibold text-slate-500">Kontrak Aktif</div>
                    <div class="my-2">
                        <span class="font-mono text-lg font-bold text-slate-900 tabular-nums sm:text-xl">
                            Rp {{ number_format($summary['nilai_kontrak_aktif'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="font-mono text-[11px] font-medium text-emerald-700 tabular-nums">
                        Masuk: Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: 3 Panel Monitoring Operasional --}}
        <div class="grid gap-5 lg:grid-cols-3">
            <div class="flex flex-col rounded-2xl border border-slate-200/90 bg-white p-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-slate-700">Jatuh Tempo (&le; 14 Hari)</h2>
                    <span class="font-mono text-xs font-semibold text-amber-700">{{ $kontrakAkanHabis->count() }} kontrak</span>
                </div>

                <div class="mt-3 flex-1 space-y-2.5">
                    @forelse ($kontrakAkanHabis as $kontrak)
                        @php
                            $sisa = $kontrak->tgl_berakhir ? (int) now()->startOfDay()->diffInDays($kontrak->tgl_berakhir, false) : null;
                        @endphp
                        <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-200/80 bg-slate-50/70 p-3.5 text-xs">
                            <div>
                                <div class="font-mono font-bold text-slate-900">
                                    {{ $kontrak->titikReklame->kode_titik ?? '-' }}
                                    <span class="font-sans font-semibold text-slate-600">&middot; {{ $kontrak->titikReklame->wilayah ?? '-' }}</span>
                                </div>
                                <div class="mt-0.5 text-slate-700">{{ $kontrak->klien->nama_klien ?? '-' }}</div>
                                <div class="mt-1 font-mono text-[11px] font-medium text-amber-700">
                                    {{ optional($kontrak->tgl_berakhir)->format('d M Y') }}
                                    @if ($sisa !== null)
                                        ({{ $sisa <= 0 ? 'Sudah habis' : "Sisa {$sisa} hari" }})
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                               class="shrink-0 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-slate-100">
                                Kelola
                            </a>
                        </div>
                    @empty
                        <p class="py-6 text-xs text-slate-500">Belum ada kontrak sewa yang berakhir dalam 14 hari ke depan.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex flex-col rounded-2xl border border-slate-200/90 bg-white p-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-slate-700">Penurunan Materi Iklan</h2>
                    <span class="font-mono text-xs font-semibold text-rose-700">{{ $materiPerluDicopot->count() }} titik</span>
                </div>

                <div class="mt-3 flex-1 space-y-2.5">
                    @forelse ($materiPerluDicopot as $kontrak)
                        <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-200/80 bg-slate-50/70 p-3.5 text-xs">
                            <div>
                                <div class="font-mono font-bold text-slate-900">
                                    {{ $kontrak->titikReklame->kode_titik ?? '-' }}
                                    <span class="font-sans font-semibold text-slate-600">&middot; {{ $kontrak->titikReklame->wilayah ?? '-' }}</span>
                                </div>
                                <div class="mt-0.5 text-slate-700">{{ $kontrak->materi_iklan ?? '-' }}</div>
                                <div class="mt-1 font-mono text-[11px] font-medium text-rose-700">
                                    Habis {{ optional($kontrak->tgl_berakhir)->format('d M Y') }} &mdash; siap dicopot
                                </div>
                            </div>
                            <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                               class="shrink-0 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-slate-100">
                                Copot
                            </a>
                        </div>
                    @empty
                        <p class="py-6 text-xs text-slate-500">Belum ada materi iklan yang menunggu pencopotan lapangan.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex flex-col rounded-2xl border border-slate-200/90 bg-white p-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-slate-700">Izin &amp; Pajak (&le; 30 Hari)</h2>
                    <span class="font-mono text-xs font-semibold text-slate-600">{{ $izinPajakSegeraHabis->count() }} titik</span>
                </div>

                <div class="mt-3 flex-1 space-y-2.5">
                    @forelse ($izinPajakSegeraHabis as $titik)
                        <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-200/80 bg-slate-50/70 p-3.5 text-xs">
                            <div>
                                <div class="font-mono font-bold text-slate-900">
                                    {{ $titik->kode_titik }}
                                    <span class="font-sans font-semibold text-slate-600">&middot; {{ $titik->wilayah }}</span>
                                </div>
                                <div class="mt-0.5 text-slate-600">Izin: {{ optional($titik->tgl_izin_berakhir)->format('d M Y') ?? '-' }}</div>
                                <div class="text-slate-600">Pajak: {{ optional($titik->tgl_pajak_berakhir)->format('d M Y') ?? '-' }}</div>
                            </div>
                            <a href="{{ route('penyewaanreklame.titik-reklame.edit', $titik) }}"
                               class="shrink-0 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-slate-100">
                                Perbarui
                            </a>
                        </div>
                    @empty
                        <p class="py-6 text-xs text-slate-500">Seluruh izin dan pajak titik masih berlaku &gt; 30 hari.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

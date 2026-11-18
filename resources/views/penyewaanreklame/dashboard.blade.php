@extends('penyewaanreklame.layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('penyewaanreklame.partials.flash')

    @php
        $totalTitik = max(1, $summary['total']);
        $okupansiPct = $summary['total'] > 0 ? round(($summary['tersewa'] / $totalTitik) * 100) : 0;
        $tersediaPct = $summary['total'] > 0 ? round(($summary['tersedia'] / $totalTitik) * 100) : 0;
        $maintPct = $summary['total'] > 0 ? max(0, 100 - $okupansiPct - $tersediaPct) : 0;
    @endphp

    {{-- Header / Hero Banner --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700 ring-1 ring-inset ring-violet-200">
                    <i class="ph ph-pulse text-sm"></i>
                    Ringkasan Operasional Advertising
                </span>
                <h1 class="mt-2.5 text-2xl font-extrabold tracking-tight text-slate-900">
                    Dashboard Penyewaan Reklame
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Pantau okupansi papan reklame, pengingat kontrak jatuh tempo, penurunan materi, serta izin &amp; pajak daerah.
                </p>
            </div>

            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('penyewaanreklame.kontrak.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm shadow-violet-200 transition hover:bg-violet-700">
                    <i class="ph ph-file-plus text-base"></i>
                    Buat Penawaran
                </a>
                <a href="{{ route('penyewaanreklame.titik-reklame.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                    <i class="ph ph-plus-circle text-base text-violet-600"></i>
                    Tambah Titik Reklame
                </a>
            </div>
        </div>

        {{-- Bar Proporsi Okupansi --}}
        <div class="mt-6 border-t border-slate-100 pt-5">
            <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                <span class="font-semibold text-slate-700">
                    Tingkat Okupansi Titik Reklame: <span class="text-violet-700">{{ $okupansiPct }}% Tersewa</span>
                </span>
                <div class="flex flex-wrap items-center gap-4 text-slate-600">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Tersedia ({{ $summary['tersedia'] }})</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-violet-600"></span> Tersewa ({{ $summary['tersewa'] }})</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span> Maintenance ({{ $summary['maintenance'] }})</span>
                </div>
            </div>
            <div class="mt-2.5 flex h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                @if ($summary['total'] > 0)
                    <div class="bg-emerald-500 transition-all" style="width: {{ $tersediaPct }}%"></div>
                    <div class="bg-violet-600 transition-all" style="width: {{ $okupansiPct }}%"></div>
                    <div class="bg-amber-400 transition-all" style="width: {{ $maintPct }}%"></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kartu Statistik Ringkasan --}}
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Titik</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                    <i class="ph ph-map-trifold text-base"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $summary['total'] }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $summary['internal'] }} Internal &middot; {{ $summary['mitra'] }} Mitra</p>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Tersedia</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="ph ph-check-circle text-base"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-emerald-700">{{ $summary['tersedia'] }}</p>
            <p class="mt-1 text-xs text-emerald-600">Siap ditawarkan</p>
        </div>

        <div class="rounded-2xl border border-violet-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Tersewa</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <i class="ph ph-handshake text-base"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-violet-700">{{ $summary['tersewa'] }}</p>
            <p class="mt-1 text-xs text-violet-600">Kontrak berjalan</p>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Maintenance</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i class="ph ph-wrench text-base"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-amber-700">{{ $summary['maintenance'] }}</p>
            <p class="mt-1 text-xs text-amber-600">Perawatan teknis</p>
        </div>

        <div class="col-span-2 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 p-5 text-white shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Kontrak Aktif</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-violet-200">
                    <i class="ph ph-currency-circle-dollar text-base"></i>
                </span>
            </div>
            <p class="mt-3 text-xl font-extrabold">Rp {{ number_format($summary['nilai_kontrak_aktif'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-300">Terbayar: Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- 3 Panel Peringatan Monitoring --}}
    <div class="mt-6 grid gap-5 lg:grid-cols-3">
        {{-- Jatuh Tempo --}}
        <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <i class="ph ph-clock-countdown text-lg"></i>
                    </span>
                    <h2 class="text-xs font-bold uppercase tracking-wide text-slate-800">Jatuh Tempo (&le; 14 Hari)</h2>
                </div>
                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                    {{ $kontrakAkanHabis->count() }}
                </span>
            </div>

            <div class="mt-3 flex-1 space-y-2.5">
                @forelse ($kontrakAkanHabis as $kontrak)
                    @php
                        $sisa = $kontrak->tgl_berakhir ? (int) now()->startOfDay()->diffInDays($kontrak->tgl_berakhir, false) : null;
                    @endphp
                    <div class="flex items-start justify-between gap-3 rounded-xl border border-amber-200/80 bg-amber-50/50 p-3.5 text-xs">
                        <div>
                            <div class="font-bold text-slate-900">
                                {{ $kontrak->titikReklame->kode_titik ?? '-' }} &middot; {{ $kontrak->titikReklame->wilayah ?? '-' }}
                            </div>
                            <div class="mt-0.5 text-slate-700">{{ $kontrak->klien->nama_klien ?? '-' }}</div>
                            <div class="mt-1 font-semibold text-amber-800">
                                {{ optional($kontrak->tgl_berakhir)->format('d M Y') }}
                                @if ($sisa !== null)
                                    ({{ $sisa <= 0 ? 'Sudah habis' : "Sisa {$sisa} hari" }})
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                           class="shrink-0 rounded-lg border border-amber-200 bg-white px-2.5 py-1.5 font-semibold text-amber-800 transition hover:bg-amber-100">
                            Kelola
                        </a>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-center text-slate-400">
                        <i class="ph ph-check-circle text-3xl text-emerald-400"></i>
                        <p class="mt-2 text-xs text-slate-500">Tidak ada kontrak jatuh tempo dalam 14 hari ke depan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Penurunan Materi --}}
        <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                        <i class="ph ph-scissors text-lg"></i>
                    </span>
                    <h2 class="text-xs font-bold uppercase tracking-wide text-slate-800">Penurunan Materi</h2>
                </div>
                <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-700">
                    {{ $materiPerluDicopot->count() }}
                </span>
            </div>

            <div class="mt-3 flex-1 space-y-2.5">
                @forelse ($materiPerluDicopot as $kontrak)
                    <div class="flex items-start justify-between gap-3 rounded-xl border border-rose-200/80 bg-rose-50/50 p-3.5 text-xs">
                        <div>
                            <div class="font-bold text-slate-900">
                                {{ $kontrak->titikReklame->kode_titik ?? '-' }} &middot; {{ $kontrak->titikReklame->wilayah ?? '-' }}
                            </div>
                            <div class="mt-0.5 text-slate-700">{{ $kontrak->materi_iklan ?? '-' }}</div>
                            <div class="mt-1 font-semibold text-rose-700">
                                Habis {{ optional($kontrak->tgl_berakhir)->format('d M Y') }} &mdash; siap dicopot
                            </div>
                        </div>
                        <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                           class="shrink-0 rounded-lg border border-rose-200 bg-white px-2.5 py-1.5 font-semibold text-rose-700 transition hover:bg-rose-100">
                            Copot
                        </a>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-center text-slate-400">
                        <i class="ph ph-check-circle text-3xl text-emerald-400"></i>
                        <p class="mt-2 text-xs text-slate-500">Belum ada materi iklan yang menunggu pencopotan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Izin & Pajak --}}
        <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                        <i class="ph ph-seal-warning text-lg"></i>
                    </span>
                    <h2 class="text-xs font-bold uppercase tracking-wide text-slate-800">Izin &amp; Pajak (&le; 30 Hari)</h2>
                </div>
                <span class="rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-bold text-sky-700">
                    {{ $izinPajakSegeraHabis->count() }}
                </span>
            </div>

            <div class="mt-3 flex-1 space-y-2.5">
                @forelse ($izinPajakSegeraHabis as $titik)
                    <div class="flex items-start justify-between gap-3 rounded-xl border border-sky-200/80 bg-sky-50/50 p-3.5 text-xs">
                        <div>
                            <div class="font-bold text-slate-900">{{ $titik->kode_titik }} &middot; {{ $titik->wilayah }}</div>
                            <div class="mt-0.5 text-slate-700">Izin: {{ optional($titik->tgl_izin_berakhir)->format('d M Y') ?? '-' }}</div>
                            <div class="text-slate-700">Pajak: {{ optional($titik->tgl_pajak_berakhir)->format('d M Y') ?? '-' }}</div>
                        </div>
                        <a href="{{ route('penyewaanreklame.titik-reklame.edit', $titik) }}"
                           class="shrink-0 rounded-lg border border-sky-200 bg-white px-2.5 py-1.5 font-semibold text-sky-700 transition hover:bg-sky-100">
                            Perbarui
                        </a>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-center text-slate-400">
                        <i class="ph ph-shield-check text-3xl text-emerald-400"></i>
                        <p class="mt-2 text-xs text-slate-500">Seluruh izin dan pajak titik masih berlaku aman &gt; 30 hari.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

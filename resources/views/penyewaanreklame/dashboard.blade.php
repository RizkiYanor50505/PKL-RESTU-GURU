@extends('penyewaanreklame.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Penyewaan Reklame</h1>
            <p class="mt-1 text-sm text-slate-500">
                Monitoring ketersediaan titik reklame dan masa berlaku kontrak sewa Restu Guru Promosindo.
            </p>
        </div>

        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('penyewaanreklame.titik-reklame.index') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-700">
                <i class="ph ph-map-pin-line text-base"></i>
                Titik Reklame
            </a>
            <a href="{{ route('penyewaanreklame.klien.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="ph ph-users text-base text-violet-600"></i>
                Data Klien
            </a>
            <a href="{{ route('penyewaanreklame.kontrak.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="ph ph-file-text text-base text-violet-600"></i>
                Kontrak Sewa
            </a>
            <a href="{{ route('penyewaanreklame.katalog') }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="ph ph-globe text-base text-violet-600"></i>
                Katalog Publik
            </a>
        </div>
    </div>

    {{-- Kartu Statistik Ringkasan --}}
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Titik Reklame</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['total'] }}</p>
            <p class="mt-1 text-xs text-slate-500">
                {{ $summary['internal'] }} Internal &middot; {{ $summary['mitra'] }} Mitra
            </p>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Titik Tersedia</p>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $summary['tersedia'] }}</p>
            <p class="mt-1 text-xs text-emerald-600">Siap ditawarkan ke klien</p>
        </div>

        <div class="rounded-2xl border border-violet-200 bg-violet-50/50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Titik Tersewa</p>
            <p class="mt-2 text-3xl font-bold text-violet-700">{{ $summary['tersewa'] }}</p>
            <p class="mt-1 text-xs text-violet-600">Sedang berjalan aktif</p>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Maintenance</p>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ $summary['maintenance'] }}</p>
            <p class="mt-1 text-xs text-amber-600">Dalam perawatan teknis</p>
        </div>
    </div>

    {{-- Pengingat Jatuh Tempo (1-2 Minggu Sebelum Habis) & Pencopotan Materi --}}
    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-800">
                    Pengingat Jatuh Tempo (&le; 14 Hari)
                </h2>
                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                    {{ $kontrakAkanHabis->count() }} Kontrak
                </span>
            </div>

            @forelse ($kontrakAkanHabis as $kontrak)
                <div class="mt-3 flex items-start justify-between gap-3 rounded-xl border border-amber-100 bg-amber-50/60 p-3.5 text-xs">
                    <div>
                        <div class="font-bold text-slate-900">
                            {{ $kontrak->titikReklame->kode_titik ?? '-' }} &mdash; {{ $kontrak->titikReklame->wilayah ?? '-' }}
                        </div>
                        <div class="mt-0.5 text-slate-700">
                            Klien: <span class="font-semibold">{{ $kontrak->klien->nama_klien ?? '-' }}</span>
                        </div>
                        <div class="mt-0.5 text-amber-800">
                            Berakhir: {{ optional($kontrak->tgl_berakhir)->format('d M Y') }}
                            ({{ ucfirst(str_replace('_', ' ', $kontrak->status_perpanjangan)) }})
                        </div>
                    </div>
                    <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                       class="shrink-0 rounded-lg border border-amber-200 bg-white px-2.5 py-1.5 font-semibold text-amber-800 hover:bg-amber-100">
                        Kelola Kontrak
                    </a>
                </div>
            @empty
                <p class="mt-4 text-sm text-slate-500">Tidak ada kontrak sewa yang akan berakhir dalam 14 hari ke depan.</p>
            @endforelse
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-800">
                    Pengingat Penurunan Materi Iklan
                </h2>
                <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-700">
                    {{ $materiPerluDicopot->count() }} Titik
                </span>
            </div>

            @forelse ($materiPerluDicopot as $kontrak)
                <div class="mt-3 flex items-start justify-between gap-3 rounded-xl border border-rose-100 bg-rose-50/60 p-3.5 text-xs">
                    <div>
                        <div class="font-bold text-slate-900">
                            {{ $kontrak->titikReklame->kode_titik ?? '-' }} &mdash; {{ $kontrak->titikReklame->wilayah ?? '-' }}
                        </div>
                        <div class="mt-0.5 text-slate-700">Materi: {{ $kontrak->materi_iklan ?? '-' }}</div>
                        <div class="mt-0.5 text-rose-700">Kontrak berakhir {{ optional($kontrak->tgl_berakhir)->format('d M Y') }} &mdash; perlu pencopotan</div>
                    </div>
                    <a href="{{ route('penyewaanreklame.kontrak.show', $kontrak) }}"
                       class="shrink-0 rounded-lg border border-rose-200 bg-white px-2.5 py-1.5 font-semibold text-rose-700 hover:bg-rose-100">
                        Konfirmasi Copot
                    </a>
                </div>
            @empty
                <p class="mt-4 text-sm text-slate-500">Belum ada materi iklan yang menunggu pencopotan oleh tim lapangan.</p>
            @endforelse
        </div>
    </div>
@endsection

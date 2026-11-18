@extends('penyewaanreklame.layouts.app')

@section('title', 'Data Klien')

@section('content')
    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-violet-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Data Klien</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Master Data Klien &amp; Penyewa</h1>
            <p class="mt-1 text-sm text-slate-500">
                Kategori klien dibedakan antara Perusahaan (faktur pajak/NPWP &amp; penagihan pasca Berita Acara) dan Perorangan (bayar di awal).
            </p>
        </div>

        <a href="{{ route('penyewaanreklame.klien.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-violet-700">
            <i class="ph ph-plus text-base"></i>
            Tambah Klien
        </a>
    </div>

    <div class="mt-6 grid grid-cols-3 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Klien</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Perusahaan</p>
            <p class="mt-2 text-2xl font-bold text-sky-700">{{ $summary['perusahaan'] }}</p>
        </div>
        <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Perorangan</p>
            <p class="mt-2 text-2xl font-bold text-violet-700">{{ $summary['perorangan'] }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('penyewaanreklame.klien.index') }}"
          class="mt-6 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama klien, PIC, WA, NPWP..."
               class="sm:col-span-2 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">

        <select name="kategori_klien" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
            <option value="">Semua Kategori</option>
            <option value="perusahaan" @selected(request('kategori_klien') === 'perusahaan')>Perusahaan</option>
            <option value="perorangan" @selected(request('kategori_klien') === 'perorangan')>Perorangan</option>
        </select>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-700">
                Filter
            </button>
            <a href="{{ route('penyewaanreklame.klien.index') }}"
               class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Nama Klien &amp; Kategori</th>
                        <th class="px-4 py-3">PIC &amp; Kontak WA</th>
                        <th class="px-4 py-3">NPWP</th>
                        <th class="px-4 py-3">Kontrak</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kliens as $klien)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3.5 align-top">
                                <a href="{{ route('penyewaanreklame.klien.show', $klien) }}"
                                   class="font-semibold text-slate-900 hover:text-violet-700">
                                    {{ $klien->nama_klien }}
                                </a>
                                <div>
                                    <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $klien->kategori_klien === 'perusahaan' ? 'bg-sky-100 text-sky-700' : 'bg-violet-100 text-violet-700' }}">
                                        {{ ucfirst($klien->kategori_klien) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-xs text-slate-700">
                                <div class="font-medium">{{ $klien->nama_pic ?? '-' }}</div>
                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $klien->no_wa) }}" target="_blank" rel="noopener"
                                   class="mt-0.5 inline-flex items-center gap-1 text-emerald-700 hover:underline">
                                    <i class="ph ph-whatsapp-logo"></i>
                                    {{ $klien->no_wa }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 align-top font-mono text-xs text-slate-600">
                                {{ $klien->npwp ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 align-top text-xs text-slate-700">
                                {{ $klien->kontrak_sewas_count }} transaksi
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right align-top">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('penyewaanreklame.klien.show', $klien) }}"
                                       class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Detail
                                    </a>
                                    <a href="{{ route('penyewaanreklame.klien.edit', $klien) }}"
                                       class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-violet-700 hover:bg-violet-50">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('penyewaanreklame.klien.destroy', $klien) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus klien {{ $klien->nama_klien }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data klien.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

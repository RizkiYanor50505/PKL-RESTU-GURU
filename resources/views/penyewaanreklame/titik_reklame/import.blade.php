@extends('penyewaanreklame.layouts.app')

@section('title', 'Import Data CSV Titik Reklame')

@section('content')
    @if (session('error'))
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <ul class="list-disc pl-5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-violet-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('penyewaanreklame.titik-reklame.index') }}" class="hover:text-violet-600">Titik Reklame</a>
                <span>/</span>
                <span class="text-slate-700">Import CSV</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Import Data Titik Reklame (CSV)</h1>
            <p class="mt-1 text-sm text-slate-500">
                Memasukkan data operasional dari spreadsheet perusahaan langsung ke database lokal tanpa menyimpannya di riwayat GitHub.
            </p>
        </div>

        <a href="{{ route('penyewaanreklame.titik-reklame.template') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
            <i class="ph ph-download-simple text-base text-violet-600"></i>
            Download Template CSV
        </a>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('penyewaanreklame.titik-reklame.import.store') }}" enctype="multipart/form-data"
              class="rounded-2xl border border-slate-200 bg-white p-6">
            @csrf
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-800">Unggah File CSV</h2>
            <p class="mt-1 text-xs text-slate-500">
                Mendukung pemisah koma (<code>,</code>) maupun titik koma (<code>;</code>). Jika <code>kode_titik</code> sudah ada, datanya akan diperbarui otomatis.
            </p>

            <div class="mt-4">
                <label for="file_csv" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Pilih File CSV <span class="text-rose-500">*</span>
                </label>
                <input id="file_csv" name="file_csv" type="file" accept=".csv,.txt" required
                       class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-violet-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('penyewaanreklame.titik-reklame.index') }}"
                   class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Kembali
                </a>
                <button type="submit"
                        class="rounded-lg bg-violet-600 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-700">
                    Mulai Import Data
                </button>
            </div>
        </form>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-xs text-slate-600">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-800">Panduan Kolom Header CSV</h2>
            <ul class="mt-3 space-y-1.5 list-disc pl-4">
                <li><strong>Wajib diisi:</strong> <code>kode_titik</code>, <code>wilayah</code>, <code>alamat_lokasi</code>.</li>
                <li><strong>Opsional (otomatis disesuaikan):</strong> <code>kategori_space</code> (<code>internal</code> atau <code>mitra</code>), <code>ukuran</code>, <code>spesifikasi_lampu</code>, <code>status_titik</code> (<code>Tersedia</code>, <code>Tersewa</code>, <code>Maintenance</code>).</li>
                <li><strong>Lokasi &amp; Izin (opsional/nullable):</strong> <code>latitude</code>, <code>longitude</code>, <code>google_maps_url</code>, <code>no_izin</code>, <code>tgl_izin_berakhir</code> (YYYY-MM-DD), <code>tgl_pajak_berakhir</code> (YYYY-MM-DD), <code>keterangan</code>.</li>
            </ul>
        </div>
    </div>
@endsection

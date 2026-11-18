@extends('penyewaanreklame.layouts.app')

@section('title', 'Buat Surat Penawaran')

@section('content')
    <div>
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-violet-600">Dashboard</a>
            <span>/</span>
            <a href="{{ route('penyewaanreklame.kontrak.index') }}" class="hover:text-violet-600">Kontrak Sewa</a>
            <span>/</span>
            <span class="text-slate-700">Buat Penawaran</span>
        </div>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Buat Surat Penawaran Reklame</h1>
        <p class="mt-1 text-sm text-slate-500">
            Penawaran awal tidak menahan/mengubah status titik reklame (tanpa holding/booking berbayar).
        </p>
    </div>

    @if ($errors->any())
        <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <div class="font-semibold">Terdapat kesalahan input:</div>
            <ul class="mt-1 list-disc pl-5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('penyewaanreklame.kontrak.store') }}"
          class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="titik_reklame_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Titik Reklame <span class="text-rose-500">*</span>
                </label>
                <select id="titik_reklame_id" name="titik_reklame_id" required
                        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
                    <option value="">-- Pilih Titik Reklame --</option>
                    @foreach ($titikReklames as $titik)
                        <option value="{{ $titik->id }}"
                            @selected((int) old('titik_reklame_id', $selectedTitikId) === $titik->id)>
                            {{ $titik->kode_titik }} &mdash; {{ $titik->wilayah }} ({{ $titik->ukuran }}) [{{ $titik->status_titik }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="klien_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Klien / Calon Penyewa <span class="text-rose-500">*</span>
                </label>
                <select id="klien_id" name="klien_id" required
                        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
                    <option value="">-- Pilih Klien --</option>
                    @foreach ($kliens as $klien)
                        <option value="{{ $klien->id }}" @selected((int) old('klien_id') === $klien->id)>
                            {{ $klien->nama_klien }} ({{ ucfirst($klien->kategori_klien) }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-[11px] text-slate-500">
                    Belum ada di daftar? <a href="{{ route('penyewaanreklame.klien.create') }}" class="font-semibold text-violet-600 hover:underline">Tambah Klien Baru</a>
                </p>
            </div>

            <div>
                <label for="no_penawaran" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Nomor Surat Penawaran <span class="text-rose-500">*</span>
                </label>
                <input id="no_penawaran" name="no_penawaran" type="text" required
                       value="{{ old('no_penawaran', 'PNW/RGP/' . date('Y/m') . '/' . str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT)) }}"
                       class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
                <p class="mt-1 text-[11px] text-slate-500">Nomor surat penawaran yang sama dapat digunakan untuk beberapa titik sekaligus.</p>
            </div>

            <div>
                <label for="tgl_penawaran" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Tanggal Penawaran <span class="text-rose-500">*</span>
                </label>
                <input id="tgl_penawaran" name="tgl_penawaran" type="date" required
                       value="{{ old('tgl_penawaran', date('Y-m-d')) }}"
                       class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
            </div>

            <div>
                <label for="durasi_bulan" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Rencana Durasi Sewa (Bulan) <span class="text-rose-500">*</span>
                </label>
                <input id="durasi_bulan" name="durasi_bulan" type="number" min="1" max="36" required
                       value="{{ old('durasi_bulan', 3) }}"
                       class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
                <p class="mt-1 text-[11px] text-slate-500">Fleksibel minimal 1 bulan hingga 1 tahun penuh (12 bulan).</p>
            </div>

            <div>
                <label for="nilai_kontrak" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Nilai / Biaya Sewa (Rp) <span class="text-rose-500">*</span>
                </label>
                <input id="nilai_kontrak" name="nilai_kontrak" type="number" min="0" step="1000" required
                       value="{{ old('nilai_kontrak', 0) }}"
                       class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label for="materi_iklan" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Rencana Materi Iklan
                </label>
                <textarea id="materi_iklan" name="materi_iklan" rows="2"
                          placeholder="Deskripsi tema/konten visual reklame yang ditawarkan..."
                          class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">{{ old('materi_iklan') }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label for="catatan" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Catatan Tambahan
                </label>
                <textarea id="catatan" name="catatan" rows="2"
                          class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-violet-500 focus:outline-none">{{ old('catatan') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('penyewaanreklame.kontrak.index') }}"
               class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-violet-600 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-700">
                Simpan Surat Penawaran
            </button>
        </div>
    </form>
@endsection

@extends('penyewaanreklame.layouts.app')

@section('title', 'Detail Transaksi ' . ($kontrak->no_kontrak ?? $kontrak->no_penawaran))

@section('content')
    @include('penyewaanreklame.partials.flash')

    @php
        $isPerorangan = ($kontrak->klien->kategori_klien ?? '') === 'perorangan';
        $sisaBayar = max(0, (float) $kontrak->nilai_kontrak - $totalDibayar);
        $sisaHari = $kontrak->tgl_berakhir
            ? (int) now()->startOfDay()->diffInDays($kontrak->tgl_berakhir, false)
            : null;
        $stepIndex = match ($kontrak->status_kontrak) {
            'penawaran' => 1,
            'acc' => 2,
            'aktif' => 3,
            'selesai' => 4,
            default => 0,
        };
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-violet-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('penyewaanreklame.kontrak.index') }}" class="hover:text-violet-600">Kontrak Sewa</a>
                <span>/</span>
                <span class="text-slate-700">{{ $kontrak->no_kontrak ?? $kontrak->no_penawaran }}</span>
            </div>
            <div class="mt-1.5 flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    {{ $kontrak->no_kontrak ?? $kontrak->no_penawaran }}
                </h1>
                <span class="rounded-full bg-violet-100 px-3 py-0.5 text-xs font-bold uppercase text-violet-700">
                    {{ $kontrak->status_kontrak }}
                </span>
                <span class="rounded-full px-3 py-0.5 text-xs font-semibold {{ $kontrak->status_materi === 'perlu_dicopot' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                    Materi: {{ str_replace('_', ' ', $kontrak->status_materi) }}
                </span>
            </div>
        </div>

        @if (in_array($kontrak->status_kontrak, ['penawaran', 'acc', 'aktif'], true))
            <form method="POST" action="{{ route('penyewaanreklame.kontrak.batal', $kontrak) }}"
                  onsubmit="return confirm('Batalkan penawaran/kontrak ini?')">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="rounded-xl border border-rose-200 bg-white px-3.5 py-2 text-xs font-semibold text-rose-600 shadow-sm transition hover:bg-rose-50">
                    Batalkan Transaksi
                </button>
            </form>
        @endif
    </div>

    {{-- Stepper Alur Kontrak --}}
    @if ($stepIndex > 0)
        <div class="mt-6 grid grid-cols-2 gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-4">
            @foreach ([1 => '1. Penawaran', 2 => '2. ACC Klien', 3 => '3. Aktif & Terpasang', 4 => '4. Selesai / Copot'] as $idx => $label)
                @php
                    $done = $stepIndex > $idx;
                    $current = $stepIndex === $idx;
                @endphp
                <div class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold {{ $current ? 'bg-violet-600 text-white shadow-sm' : ($done ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-50 text-slate-400') }}">
                    <i class="ph {{ $done ? 'ph-check-circle' : ($current ? 'ph-dot-outline' : 'ph-circle') }} text-base"></i>
                    <span>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Ringkasan Klien, Titik & Nilai --}}
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Titik Reklame</div>
            <a href="{{ route('penyewaanreklame.titik-reklame.show', $kontrak->titikReklame) }}"
               class="mt-2 block text-lg font-bold text-violet-700 hover:underline">
                {{ $kontrak->titikReklame->kode_titik }}
            </a>
            <div class="text-sm font-semibold text-slate-800">{{ $kontrak->titikReklame->wilayah }}</div>
            <p class="mt-1 text-xs text-slate-500">{{ $kontrak->titikReklame->alamat_lokasi }}</p>
            <div class="mt-2 text-xs font-medium text-slate-600">
                Status Papan: <span class="font-bold">{{ $kontrak->titikReklame->status_titik }}</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Klien &amp; Skema Bayar</div>
            <a href="{{ route('penyewaanreklame.klien.show', $kontrak->klien) }}"
               class="mt-2 block text-lg font-bold text-slate-900 hover:text-violet-700">
                {{ $kontrak->klien->nama_klien }}
            </a>
            <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $isPerorangan ? 'bg-violet-100 text-violet-700' : 'bg-sky-100 text-sky-700' }}">
                Kategori: {{ ucfirst($kontrak->klien->kategori_klien) }}
            </span>
            <p class="mt-2 text-xs text-slate-600">
                @if ($isPerorangan)
                    Aturan: Wajib pembayaran di awal sebelum produksi &amp; pemasangan.
                @else
                    Aturan: Produksi &amp; pemasangan pasca ACC, penagihan setelah Berita Acara &amp; NPWP lengkap.
                @endif
            </p>
            <div class="mt-1 text-xs text-slate-500">NPWP: {{ $kontrak->klien->npwp ?? 'Belum diisi' }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Nilai &amp; Pembayaran</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">
                Rp {{ number_format((float) $kontrak->nilai_kontrak, 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-emerald-700">
                Sudah dibayar: <span class="font-bold">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
            </div>
            <div class="text-xs text-slate-500">
                Sisa tagihan: Rp {{ number_format($sisaBayar, 0, ',', '.') }}
            </div>
            @if ($kontrak->file_berita_acara)
                <a href="{{ asset('storage/' . $kontrak->file_berita_acara) }}" target="_blank" rel="noopener"
                   class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-violet-600 hover:underline">
                    <i class="ph ph-file-text text-sm"></i>
                    Lihat File Berita Acara
                </a>
            @endif
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Kolom Kiri: Aksi Tahapan Alur Kontrak --}}
        <div class="space-y-5">
            @if ($kontrak->status_kontrak === 'penawaran')
                <form method="POST" action="{{ route('penyewaanreklame.kontrak.acc', $kontrak) }}"
                      class="rounded-2xl border border-sky-200 bg-white p-5 shadow-sm">
                    @csrf
                    @method('PATCH')
                    <h2 class="text-sm font-bold uppercase tracking-wide text-sky-800">
                        Tahap Berikutnya: Persetujuan (ACC) Klien
                    </h2>
                    <p class="mt-1 text-xs text-slate-500">
                        Catat tanggal ACC dan nomor perjanjian/kontrak (opsional) setelah surat penawaran disetujui klien.
                    </p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600">Tanggal ACC</label>
                            <input type="date" name="tgl_acc" value="{{ date('Y-m-d') }}" required
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600">No. Kontrak (Opsional)</label>
                            <input type="text" name="no_kontrak" value="{{ old('no_kontrak') }}"
                                   placeholder="CTR/RGP/..."
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                    </div>
                    <button type="submit"
                            class="mt-4 rounded-xl bg-sky-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-sky-700">
                        Tandai Penawaran ACC
                    </button>
                </form>
            @endif

            @if (in_array($kontrak->status_kontrak, ['acc', 'aktif'], true))
                <form method="POST" action="{{ route('penyewaanreklame.kontrak.pasang', $kontrak) }}" enctype="multipart/form-data"
                      class="rounded-2xl border border-violet-200 bg-white p-5 shadow-sm">
                    @csrf
                    @method('PATCH')
                    <h2 class="text-sm font-bold uppercase tracking-wide text-violet-800">
                        {{ $kontrak->status_kontrak === 'acc' ? 'Aktivasi Kontrak, Pemasangan & Berita Acara' : 'Perbarui Data Pemasangan & Berita Acara' }}
                    </h2>

                    @if ($isPerorangan && $totalDibayar <= 0)
                        <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-medium text-amber-800">
                            Klien <strong>Perorangan</strong> wajib mencatat pembayaran di awal (pada panel kanan) sebelum mengaktifkan produksi &amp; pemasangan.
                        </div>
                    @endif

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600">No. Kontrak</label>
                            <input type="text" name="no_kontrak" value="{{ old('no_kontrak', $kontrak->no_kontrak) }}"
                                   placeholder="CTR/RGP/..."
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600">Durasi (Bulan)</label>
                            <input type="number" name="durasi_bulan" min="1" max="36" required
                                   value="{{ old('durasi_bulan', $kontrak->durasi_bulan ?? 3) }}"
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600">Tanggal Mulai Sewa</label>
                            <input type="date" name="tgl_mulai" required
                                   value="{{ old('tgl_mulai', optional($kontrak->tgl_mulai)->format('Y-m-d') ?? date('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600">Tanggal Berakhir Sewa</label>
                            <input type="date" name="tgl_berakhir" required
                                   value="{{ old('tgl_berakhir', optional($kontrak->tgl_berakhir)->format('Y-m-d') ?? now()->addMonths($kontrak->durasi_bulan ?? 3)->format('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600">Tanggal Pemasangan</label>
                            <input type="date" name="tgl_pemasangan" required
                                   value="{{ old('tgl_pemasangan', optional($kontrak->tgl_pemasangan)->format('Y-m-d') ?? date('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600">Upload Berita Acara (PDF/JPG/PNG)</label>
                            <input type="file" name="file_berita_acara" accept=".pdf,image/jpeg,image/png"
                                   class="mt-1 block w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-violet-50 file:px-2.5 file:py-1.5 file:font-semibold file:text-violet-700">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600">Materi Iklan Terpasang</label>
                            <textarea name="materi_iklan" rows="2"
                                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('materi_iklan', $kontrak->materi_iklan) }}</textarea>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-4 rounded-xl bg-violet-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-violet-700">
                        Simpan Pemasangan &amp; Aktifkan Sewa
                    </button>
                </form>
            @endif

            @if ($kontrak->status_kontrak === 'aktif')
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-800">
                            Konfirmasi Perpanjangan &amp; Penurunan Materi
                        </h2>
                        @if ($sisaHari !== null)
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sisaHari <= 0 ? 'bg-rose-100 text-rose-700' : ($sisaHari <= 14 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ $sisaHari <= 0 ? 'Kontrak telah berakhir' : "Sisa {$sisaHari} hari" }}
                            </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('penyewaanreklame.kontrak.perpanjangan', $kontrak) }}" class="mt-4 flex flex-wrap gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status_perpanjangan" class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium">
                            <option value="belum_konfirmasi" @selected($kontrak->status_perpanjangan === 'belum_konfirmasi')>Belum Konfirmasi (Pengingat 1-2 Minggu)</option>
                            <option value="diperpanjang" @selected($kontrak->status_perpanjangan === 'diperpanjang')>Klien Akan Perpanjang</option>
                            <option value="tidak_diperpanjang" @selected($kontrak->status_perpanjangan === 'tidak_diperpanjang')>Tidak Diperpanjang</option>
                        </select>
                        <button type="submit"
                                class="rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-slate-700">
                            Simpan Status
                        </button>
                    </form>

                    <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                        @if ($kontrak->status_materi === 'terpasang')
                            <form method="POST" action="{{ route('penyewaanreklame.kontrak.perlu-dicopot', $kontrak) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100">
                                    Tandai Perlu Pencopotan Materi
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('penyewaanreklame.kontrak.turunkan-materi', $kontrak) }}"
                              onsubmit="return confirm('Konfirmasi materi sudah dicopot dan kembalikan status papan ke Tersedia?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                Konfirmasi Materi Sudah Dicopot &amp; Selesai
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Pencatatan & Riwayat Pembayaran --}}
        <div class="space-y-5">
            <form method="POST" action="{{ route('penyewaanreklame.pembayaran.store', $kontrak) }}" enctype="multipart/form-data"
                  class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                @csrf
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-800">Catat Pembayaran Baru</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">Tanggal Bayar</label>
                        <input type="date" name="tgl_bayar" value="{{ date('Y-m-d') }}" required
                               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">Jumlah Bayar (Rp)</label>
                        <input type="number" name="jumlah_bayar" min="1" step="1000" required
                               value="{{ $sisaBayar > 0 ? (int) $sisaBayar : '' }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            @if ($isPerorangan)
                                <option value="bayar_awal">Bayar Awal (Perorangan)</option>
                            @else
                                <option value="tagihan_perusahaan">Tagihan Perusahaan (Pasca BA)</option>
                            @endif
                            <option value="dp">DP</option>
                            <option value="pelunasan">Pelunasan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">Metode Pembayaran</label>
                        <input type="text" name="metode_pembayaran" placeholder="Transfer Bank / Tunai"
                               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600">Upload Bukti Pembayaran (JPG/PNG/PDF)</label>
                        <input type="file" name="bukti_pembayaran" accept="image/jpeg,image/png,image/webp,.pdf"
                               class="mt-1 block w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-violet-50 file:px-2.5 file:py-1.5 file:font-semibold file:text-violet-700">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600">Catatan</label>
                        <input type="text" name="catatan" placeholder="Opsional"
                               class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                </div>

                <button type="submit"
                        class="mt-4 rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700">
                    Simpan Pembayaran
                </button>
            </form>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-800">Riwayat Pembayaran</h2>
                @forelse ($kontrak->pembayarans as $bayar)
                    <div class="mt-3 flex items-start justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-xs">
                        <div>
                            <div class="font-bold text-slate-900">
                                Rp {{ number_format((float) $bayar->jumlah_bayar, 0, ',', '.') }}
                                <span class="ml-1 rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-emerald-700">
                                    {{ str_replace('_', ' ', $bayar->jenis_pembayaran) }}
                                </span>
                            </div>
                            <div class="mt-0.5 text-slate-600">
                                {{ $bayar->tgl_bayar->format('d M Y') }} &middot; {{ $bayar->metode_pembayaran ?? '-' }}
                            </div>
                            @if ($bayar->catatan)
                                <div class="mt-0.5 text-slate-500">{{ $bayar->catatan }}</div>
                            @endif
                            @if ($bayar->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $bayar->bukti_pembayaran) }}" target="_blank" rel="noopener"
                                   class="mt-1 inline-flex items-center gap-1 font-semibold text-violet-600 hover:underline">
                                    <i class="ph ph-receipt"></i> Lihat Bukti Bayar
                                </a>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('penyewaanreklame.pembayaran.destroy', $bayar) }}"
                              onsubmit="return confirm('Hapus catatan pembayaran ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                @empty
                    <p class="mt-3 text-sm text-slate-500">Belum ada pembayaran yang tercatat pada transaksi ini.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

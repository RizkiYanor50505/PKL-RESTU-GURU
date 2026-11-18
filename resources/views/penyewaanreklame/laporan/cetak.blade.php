<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Reklame - Restu Guru Promosindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white p-6 text-slate-900 antialiased">
    <div class="no-print mb-4 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-3">
        <span class="text-xs text-slate-600">Pratinjau Cetak Laporan (format A4 Landscape). Gunakan Save as PDF pada dialog cetak untuk menyimpan PDF.</span>
        <div class="flex gap-2">
            <button onclick="window.print()"
                    class="rounded-lg bg-slate-900 px-4 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700">
                Tutup
            </button>
        </div>
    </div>

    <header class="border-b-2 border-slate-900 pb-3">
        <div class="flex items-end justify-between">
            <div>
                <h1 class="text-lg font-bold uppercase tracking-wide">PT / CV Restu Guru Promosindo &mdash; Divisi Advertising</h1>
                <p class="text-xs text-slate-600">Laporan Operasional Sebaran Titik Reklame &amp; Status Penyewaan</p>
            </div>
            <div class="text-right text-xs text-slate-600">
                <div>Tanggal Cetak: {{ now()->format('d M Y, H:i') }}</div>
                <div>Total Titik: {{ $titikReklames->count() }} Lokasi</div>
            </div>
        </div>
    </header>

    <table class="mt-4 w-full border-collapse border border-slate-300 text-left text-[11px]">
        <thead class="bg-slate-100 font-bold uppercase">
            <tr>
                <th class="border border-slate-300 px-2 py-1.5">No</th>
                <th class="border border-slate-300 px-2 py-1.5">Kode</th>
                <th class="border border-slate-300 px-2 py-1.5">Space</th>
                <th class="border border-slate-300 px-2 py-1.5">Wilayah &amp; Alamat Lokasi</th>
                <th class="border border-slate-300 px-2 py-1.5">Ukuran &amp; Lampu</th>
                <th class="border border-slate-300 px-2 py-1.5">Status</th>
                <th class="border border-slate-300 px-2 py-1.5">Penyewa &amp; Masa Sewa</th>
                <th class="border border-slate-300 px-2 py-1.5">Materi Iklan</th>
                <th class="border border-slate-300 px-2 py-1.5">Izin / Pajak</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($titikReklames as $idx => $titik)
                <tr>
                    <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 px-2 py-1.5 font-mono font-semibold">{{ $titik->kode_titik }}</td>
                    <td class="border border-slate-300 px-2 py-1.5">{{ $titik->kategori_space === 'internal' ? 'Internal' : 'Mitra' }}</td>
                    <td class="border border-slate-300 px-2 py-1.5">
                        <div class="font-semibold">{{ $titik->wilayah }}</div>
                        <div>{{ $titik->alamat_lokasi }}</div>
                    </td>
                    <td class="border border-slate-300 px-2 py-1.5">
                        <div>{{ $titik->ukuran }}</div>
                        <div class="text-slate-600">{{ $titik->spesifikasi_lampu ?? '-' }}</div>
                    </td>
                    <td class="border border-slate-300 px-2 py-1.5 font-semibold">{{ $titik->status_titik }}</td>
                    <td class="border border-slate-300 px-2 py-1.5">
                        @if ($titik->kontrakAktif)
                            <div class="font-semibold">{{ $titik->kontrakAktif->klien->nama_klien ?? '-' }}</div>
                            <div>{{ optional($titik->kontrakAktif->tgl_mulai)->format('d/m/Y') }} &ndash; {{ optional($titik->kontrakAktif->tgl_berakhir)->format('d/m/Y') }}</div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="border border-slate-300 px-2 py-1.5">{{ $titik->kontrakAktif->materi_iklan ?? '-' }}</td>
                    <td class="border border-slate-300 px-2 py-1.5">
                        <div>I: {{ optional($titik->tgl_izin_berakhir)->format('d/m/Y') ?? '-' }}</div>
                        <div>P: {{ optional($titik->tgl_pajak_berakhir)->format('d/m/Y') ?? '-' }}</div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-8 grid grid-cols-2 text-xs">
        <div></div>
        <div class="text-center">
            <div>Banjarbaru, {{ now()->format('d M Y') }}</div>
            <div class="mt-1 font-semibold">SPV Advertising / Penanggung Jawab</div>
            <div class="mt-14 font-semibold underline">Mahruzi, S.H.</div>
        </div>
    </div>
</body>
</html>

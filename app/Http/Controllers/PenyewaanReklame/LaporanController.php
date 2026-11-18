<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Models\KontrakSewa;
use App\Models\TitikReklame;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $titikReklames = $this->filteredTitik($request);
        $kontraks = $this->filteredKontrak($request);
        $daftarWilayah = TitikReklame::query()->select('wilayah')->distinct()->orderBy('wilayah')->pluck('wilayah');

        $rekap = [
            'total_titik' => $titikReklames->count(),
            'tersedia' => $titikReklames->where('status_titik', 'Tersedia')->count(),
            'tersewa' => $titikReklames->where('status_titik', 'Tersewa')->count(),
            'total_nilai' => (float) $kontraks->whereIn('status_kontrak', ['aktif', 'selesai'])->sum('nilai_kontrak'),
            'total_terbayar' => (float) $kontraks->sum('pembayarans_sum_jumlah_bayar'),
        ];

        return view('penyewaanreklame.laporan.index', compact(
            'titikReklames',
            'kontraks',
            'daftarWilayah',
            'rekap'
        ));
    }

    public function cetak(Request $request): View
    {
        $titikReklames = $this->filteredTitik($request);
        $kontraks = $this->filteredKontrak($request);

        return view('penyewaanreklame.laporan.cetak', compact('titikReklames', 'kontraks'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $titikReklames = $this->filteredTitik($request);
        $filename = 'laporan-titik-reklame-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($titikReklames): void {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM agar Excel langsung membaca karakter dengan rapi
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Titik',
                'Kategori Space',
                'Wilayah',
                'Alamat & Patokan',
                'Ukuran',
                'Spesifikasi Lampu',
                'Status Titik',
                'Penyewa Aktif',
                'Periode Mulai',
                'Periode Berakhir',
                'Materi Iklan',
                'Izin Berakhir',
                'Pajak Berakhir',
                'Google Maps URL',
            ]);

            foreach ($titikReklames as $titik) {
                $kontrak = $titik->kontrakAktif;
                $mapsUrl = $titik->google_maps_url
                    ?? ($titik->latitude && $titik->longitude
                        ? "https://maps.google.com/?q={$titik->latitude},{$titik->longitude}"
                        : '');

                fputcsv($handle, [
                    $titik->kode_titik,
                    $titik->kategori_space === 'internal' ? 'Restu Guru Promosindo' : 'Mitra RGP',
                    $titik->wilayah,
                    $titik->alamat_lokasi,
                    $titik->ukuran,
                    $titik->spesifikasi_lampu ?? '-',
                    $titik->status_titik,
                    $kontrak?->klien?->nama_klien ?? '-',
                    optional($kontrak?->tgl_mulai)->format('Y-m-d') ?? '-',
                    optional($kontrak?->tgl_berakhir)->format('Y-m-d') ?? '-',
                    $kontrak?->materi_iklan ?? '-',
                    optional($titik->tgl_izin_berakhir)->format('Y-m-d') ?? '-',
                    optional($titik->tgl_pajak_berakhir)->format('Y-m-d') ?? '-',
                    $mapsUrl,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return Collection<int, TitikReklame>
     */
    private function filteredTitik(Request $request): Collection
    {
        $query = TitikReklame::query()->with(['kontrakAktif.klien']);

        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->string('wilayah'));
        }

        if ($request->filled('kategori_space')) {
            $query->where('kategori_space', $request->string('kategori_space'));
        }

        if ($request->filled('status_titik')) {
            $query->where('status_titik', $request->string('status_titik'));
        }

        return $query->orderBy('wilayah')->orderBy('kode_titik')->get();
    }

    /**
     * @return Collection<int, KontrakSewa>
     */
    private function filteredKontrak(Request $request): Collection
    {
        $query = KontrakSewa::query()
            ->with(['titikReklame', 'klien'])
            ->withSum('pembayarans', 'jumlah_bayar');

        if ($request->filled('wilayah')) {
            $wilayah = $request->string('wilayah');
            $query->whereHas('titikReklame', fn ($q) => $q->where('wilayah', $wilayah));
        }

        if ($request->filled('dari_tgl')) {
            $query->whereDate('tgl_penawaran', '>=', $request->string('dari_tgl'));
        }

        if ($request->filled('sampai_tgl')) {
            $query->whereDate('tgl_penawaran', '<=', $request->string('sampai_tgl'));
        }

        return $query->latest('tgl_penawaran')->get();
    }
}

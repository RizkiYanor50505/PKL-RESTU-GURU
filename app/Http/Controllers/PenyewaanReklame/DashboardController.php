<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Models\KontrakSewa;
use App\Models\Pembayaran;
use App\Models\TitikReklame;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            'total' => 0,
            'tersedia' => 0,
            'tersewa' => 0,
            'maintenance' => 0,
            'internal' => 0,
            'mitra' => 0,
            'nilai_kontrak_aktif' => 0.0,
            'total_terbayar' => 0.0,
        ];
        $kontrakAkanHabis = collect();
        $materiPerluDicopot = collect();
        $izinPajakSegeraHabis = collect();

        if (Schema::hasTable('adv_titik_reklame')) {
            $summary['total'] = TitikReklame::count();
            $summary['tersedia'] = TitikReklame::where('status_titik', 'Tersedia')->count();
            $summary['tersewa'] = TitikReklame::where('status_titik', 'Tersewa')->count();
            $summary['maintenance'] = TitikReklame::where('status_titik', 'Maintenance')->count();
            $summary['internal'] = TitikReklame::where('kategori_space', 'internal')->count();
            $summary['mitra'] = TitikReklame::where('kategori_space', 'mitra')->count();

            $batasIzinPajak = now()->addDays(30)->toDateString();
            $izinPajakSegeraHabis = TitikReklame::query()
                ->where(function ($q) use ($batasIzinPajak): void {
                    $q->where(function ($sub) use ($batasIzinPajak): void {
                        $sub->whereNotNull('tgl_izin_berakhir')
                            ->whereDate('tgl_izin_berakhir', '<=', $batasIzinPajak);
                    })->orWhere(function ($sub) use ($batasIzinPajak): void {
                        $sub->whereNotNull('tgl_pajak_berakhir')
                            ->whereDate('tgl_pajak_berakhir', '<=', $batasIzinPajak);
                    });
                })
                ->orderByRaw('COALESCE(tgl_izin_berakhir, tgl_pajak_berakhir)')
                ->get();
        }

        if (Schema::hasTable('adv_kontrak_sewa')) {
            $batasPengingat = now()->addDays(14)->toDateString();

            $summary['nilai_kontrak_aktif'] = (float) KontrakSewa::where('status_kontrak', 'aktif')->sum('nilai_kontrak');

            $kontrakAkanHabis = KontrakSewa::query()
                ->with(['titikReklame', 'klien'])
                ->where('status_kontrak', 'aktif')
                ->whereNotNull('tgl_berakhir')
                ->whereDate('tgl_berakhir', '<=', $batasPengingat)
                ->orderBy('tgl_berakhir')
                ->get();

            $materiPerluDicopot = KontrakSewa::query()
                ->with(['titikReklame', 'klien'])
                ->where('status_materi', 'perlu_dicopot')
                ->orderBy('tgl_berakhir')
                ->get();
        }

        if (Schema::hasTable('adv_pembayaran')) {
            $summary['total_terbayar'] = (float) Pembayaran::sum('jumlah_bayar');
        }

        return view('penyewaanreklame.dashboard', compact(
            'summary',
            'kontrakAkanHabis',
            'materiPerluDicopot',
            'izinPajakSegeraHabis'
        ));
    }
}

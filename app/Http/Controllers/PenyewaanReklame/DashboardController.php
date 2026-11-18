<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Models\KontrakSewa;
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
        ];
        $kontrakAkanHabis = collect();
        $materiPerluDicopot = collect();

        if (Schema::hasTable('adv_titik_reklame')) {
            $summary = [
                'total' => TitikReklame::count(),
                'tersedia' => TitikReklame::where('status_titik', 'Tersedia')->count(),
                'tersewa' => TitikReklame::where('status_titik', 'Tersewa')->count(),
                'maintenance' => TitikReklame::where('status_titik', 'Maintenance')->count(),
                'internal' => TitikReklame::where('kategori_space', 'internal')->count(),
                'mitra' => TitikReklame::where('kategori_space', 'mitra')->count(),
            ];
        }

        if (Schema::hasTable('adv_kontrak_sewa')) {
            $batasPengingat = now()->addDays(14)->toDateString();

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

        return view('penyewaanreklame.dashboard', compact(
            'summary',
            'kontrakAkanHabis',
            'materiPerluDicopot'
        ));
    }
}

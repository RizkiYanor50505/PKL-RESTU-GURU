<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Models\TitikReklame;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TitikReklameController extends Controller
{
    public function index(Request $request): View
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

        if ($request->filled('q')) {
            $keyword = '%' . trim($request->string('q')) . '%';
            $query->where(function ($sub) use ($keyword): void {
                $sub->where('kode_titik', 'like', $keyword)
                    ->orWhere('alamat_lokasi', 'like', $keyword)
                    ->orWhere('wilayah', 'like', $keyword)
                    ->orWhere('ukuran', 'like', $keyword);
            });
        }

        $titikReklames = $query->orderBy('kode_titik')->get();

        $summary = [
            'total' => TitikReklame::count(),
            'tersedia' => TitikReklame::where('status_titik', 'Tersedia')->count(),
            'tersewa' => TitikReklame::where('status_titik', 'Tersewa')->count(),
            'maintenance' => TitikReklame::where('status_titik', 'Maintenance')->count(),
        ];

        $daftarWilayah = TitikReklame::query()
            ->select('wilayah')
            ->distinct()
            ->orderBy('wilayah')
            ->pluck('wilayah');

        return view('penyewaanreklame.titik_reklame.index', compact(
            'titikReklames',
            'summary',
            'daftarWilayah'
        ));
    }

    public function katalog(Request $request): View
    {
        $query = TitikReklame::query()->with(['kontrakAktif.klien']);

        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->string('wilayah'));
        }

        if ($request->filled('status_titik')) {
            $query->where('status_titik', $request->string('status_titik'));
        }

        if ($request->filled('q')) {
            $keyword = '%' . trim($request->string('q')) . '%';
            $query->where(function ($sub) use ($keyword): void {
                $sub->where('kode_titik', 'like', $keyword)
                    ->orWhere('alamat_lokasi', 'like', $keyword)
                    ->orWhere('wilayah', 'like', $keyword);
            });
        }

        $titikReklames = $query->orderBy('kode_titik')->get();

        $daftarWilayah = TitikReklame::query()
            ->select('wilayah')
            ->distinct()
            ->orderBy('wilayah')
            ->pluck('wilayah');

        return view('penyewaanreklame.katalog', compact(
            'titikReklames',
            'daftarWilayah'
        ));
    }
}

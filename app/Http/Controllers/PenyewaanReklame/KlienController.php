<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Models\Klien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KlienController extends Controller
{
    public function index(Request $request): View
    {
        $query = Klien::query()->withCount('kontrakSewas');

        if ($request->filled('kategori_klien')) {
            $query->where('kategori_klien', $request->string('kategori_klien'));
        }

        if ($request->filled('q')) {
            $keyword = '%' . trim($request->string('q')) . '%';
            $query->where(function ($sub) use ($keyword): void {
                $sub->where('nama_klien', 'like', $keyword)
                    ->orWhere('nama_pic', 'like', $keyword)
                    ->orWhere('no_wa', 'like', $keyword)
                    ->orWhere('npwp', 'like', $keyword);
            });
        }

        $kliens = $query->orderBy('nama_klien')->get();

        $summary = [
            'total' => Klien::count(),
            'perusahaan' => Klien::where('kategori_klien', 'perusahaan')->count(),
            'perorangan' => Klien::where('kategori_klien', 'perorangan')->count(),
        ];

        return view('penyewaanreklame.klien.index', compact('kliens', 'summary'));
    }

    public function create(): View
    {
        $klien = new Klien(['kategori_klien' => 'perusahaan']);

        return view('penyewaanreklame.klien.create', compact('klien'));
    }

    public function store(Request $request): RedirectResponse
    {
        $klien = Klien::create($this->validatedData($request));

        return redirect()
            ->route('penyewaanreklame.klien.show', $klien)
            ->with('success', "Data klien {$klien->nama_klien} berhasil ditambahkan.");
    }

    public function show(Klien $klien): View
    {
        $klien->load([
            'kontrakSewas' => fn ($q) => $q->with('titikReklame')->latest('tgl_penawaran'),
        ]);

        return view('penyewaanreklame.klien.show', compact('klien'));
    }

    public function edit(Klien $klien): View
    {
        return view('penyewaanreklame.klien.edit', compact('klien'));
    }

    public function update(Request $request, Klien $klien): RedirectResponse
    {
        $klien->update($this->validatedData($request));

        return redirect()
            ->route('penyewaanreklame.klien.show', $klien)
            ->with('success', "Data klien {$klien->nama_klien} berhasil diperbarui.");
    }

    public function destroy(Klien $klien): RedirectResponse
    {
        if ($klien->kontrakSewas()->exists()) {
            return redirect()
                ->route('penyewaanreklame.klien.index')
                ->with('error', "Klien {$klien->nama_klien} tidak dapat dihapus karena masih memiliki riwayat kontrak sewa.");
        }

        $nama = $klien->nama_klien;
        $klien->delete();

        return redirect()
            ->route('penyewaanreklame.klien.index')
            ->with('success', "Data klien {$nama} berhasil dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama_klien' => ['required', 'string', 'max:255'],
            'kategori_klien' => ['required', Rule::in(['perusahaan', 'perorangan'])],
            'nama_pic' => ['nullable', 'string', 'max:255'],
            'no_wa' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'npwp' => ['nullable', 'string', 'max:30'],
        ]);
    }
}

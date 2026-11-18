<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Models\TitikReklame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TitikReklameController extends Controller
{
    public function index(Request $request): View
    {
        $titikReklames = $this->filteredQuery($request)->orderBy('kode_titik')->get();

        $summary = [
            'total' => TitikReklame::count(),
            'tersedia' => TitikReklame::where('status_titik', 'Tersedia')->count(),
            'tersewa' => TitikReklame::where('status_titik', 'Tersewa')->count(),
            'maintenance' => TitikReklame::where('status_titik', 'Maintenance')->count(),
        ];

        $daftarWilayah = $this->distinctWilayah();

        return view('penyewaanreklame.titik_reklame.index', compact(
            'titikReklames',
            'summary',
            'daftarWilayah'
        ));
    }

    public function create(): View
    {
        $titikReklame = new TitikReklame([
            'kategori_space' => 'internal',
            'status_titik' => 'Tersedia',
        ]);
        $wilayahOptions = $this->wilayahOptions();

        return view('penyewaanreklame.titik_reklame.create', compact('titikReklame', 'wilayahOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['foto_papan'] = $this->handleFotoUpload($request);

        $titik = TitikReklame::create($validated);

        return redirect()
            ->route('penyewaanreklame.titik-reklame.show', $titik)
            ->with('success', "Titik reklame {$titik->kode_titik} berhasil ditambahkan.");
    }

    public function show(TitikReklame $titikReklame): View
    {
        $titikReklame->load([
            'kontrakAktif.klien',
            'kontrakSewas' => fn ($q) => $q->with('klien')->latest('tgl_penawaran'),
        ]);

        return view('penyewaanreklame.titik_reklame.show', compact('titikReklame'));
    }

    public function edit(TitikReklame $titikReklame): View
    {
        $wilayahOptions = $this->wilayahOptions();

        return view('penyewaanreklame.titik_reklame.edit', compact('titikReklame', 'wilayahOptions'));
    }

    public function update(Request $request, TitikReklame $titikReklame): RedirectResponse
    {
        $validated = $this->validatedData($request, $titikReklame);
        $validated['foto_papan'] = $this->handleFotoUpload($request, $titikReklame);

        $titikReklame->update($validated);

        return redirect()
            ->route('penyewaanreklame.titik-reklame.show', $titikReklame)
            ->with('success', "Data titik reklame {$titikReklame->kode_titik} berhasil diperbarui.");
    }

    public function destroy(TitikReklame $titikReklame): RedirectResponse
    {
        if ($titikReklame->kontrakSewas()->exists()) {
            return redirect()
                ->route('penyewaanreklame.titik-reklame.index')
                ->with('error', "Titik {$titikReklame->kode_titik} tidak dapat dihapus karena masih memiliki riwayat kontrak sewa.");
        }

        if ($titikReklame->foto_papan && Storage::disk('public')->exists($titikReklame->foto_papan)) {
            Storage::disk('public')->delete($titikReklame->foto_papan);
        }

        $kode = $titikReklame->kode_titik;
        $titikReklame->delete();

        return redirect()
            ->route('penyewaanreklame.titik-reklame.index')
            ->with('success', "Titik reklame {$kode} berhasil dihapus.");
    }

    public function katalog(Request $request): View
    {
        $titikReklames = $this->filteredQuery($request)->orderBy('kode_titik')->get();
        $daftarWilayah = $this->distinctWilayah();

        return view('penyewaanreklame.katalog', compact(
            'titikReklames',
            'daftarWilayah'
        ));
    }

    public function katalogShow(TitikReklame $titikReklame): View
    {
        $titikReklame->load('kontrakAktif.klien');

        return view('penyewaanreklame.katalog_show', compact('titikReklame'));
    }

    public function importForm(): View
    {
        return view('penyewaanreklame.titik_reklame.import');
    }

    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'kode_titik',
                'kategori_space',
                'wilayah',
                'alamat_lokasi',
                'ukuran',
                'spesifikasi_lampu',
                'status_titik',
                'latitude',
                'longitude',
                'google_maps_url',
                'no_izin',
                'tgl_izin_berakhir',
                'tgl_pajak_berakhir',
                'keterangan',
            ]);

            fputcsv($handle, [
                'RGP-BJB-001',
                'internal',
                'Banjarbaru',
                'Jl. A. Yani Km. 34 (Patokan: Dekat Bundaran Simpang Empat)',
                '4m x 8m',
                'Frontlite LED 4x100W',
                'Tersedia',
                '-3.440512',
                '114.832145',
                'https://maps.google.com/?q=-3.440512,114.832145',
                '',
                '2027-06-30',
                '2027-06-30',
                'Contoh baris data impor CSV',
            ]);

            fclose($handle);
        }, 'template-import-titik-reklame.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importStore(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $path = $request->file('file_csv')->getRealPath();
        $probe = fopen($path, 'r');
        $firstLine = $probe ? (fgets($probe) ?: '') : '';
        if ($probe) {
            fclose($probe);
        }
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $handle = fopen($path, 'r');
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (! $rawHeaders) {
            fclose($handle);

            return back()->with('error', 'File CSV kosong atau format header tidak terbaca.');
        }

        $headers = array_map(function (?string $col): string {
            $clean = preg_replace('/^\xEF\xBB\xBF/', '', (string) $col);

            return strtolower(trim($clean));
        }, $rawHeaders);

        if (! in_array('kode_titik', $headers, true) || ! in_array('wilayah', $headers, true) || ! in_array('alamat_lokasi', $headers, true)) {
            fclose($handle);

            return back()->with('error', 'Header CSV wajib memiliki kolom kode_titik, wilayah, dan alamat_lokasi.');
        }

        $imported = 0;

        DB::transaction(function () use ($handle, $delimiter, $headers, &$imported): void {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                    continue;
                }

                $assoc = [];
                foreach ($headers as $idx => $key) {
                    $assoc[$key] = isset($row[$idx]) ? trim((string) $row[$idx]) : null;
                }

                $kode = $assoc['kode_titik'] ?? '';
                if ($kode === '' || ($assoc['wilayah'] ?? '') === '' || ($assoc['alamat_lokasi'] ?? '') === '') {
                    continue;
                }

                $rawSpace = strtolower($assoc['kategori_space'] ?? 'internal');
                $kategoriSpace = str_contains($rawSpace, 'mitra') ? 'mitra' : 'internal';

                $rawStatus = strtolower($assoc['status_titik'] ?? 'tersedia');
                $statusTitik = match (true) {
                    str_contains($rawStatus, 'sewa') => 'Tersewa',
                    str_contains($rawStatus, 'maint') => 'Maintenance',
                    default => 'Tersedia',
                };

                TitikReklame::updateOrCreate(
                    ['kode_titik' => $kode],
                    [
                        'kategori_space' => $kategoriSpace,
                        'wilayah' => $assoc['wilayah'],
                        'alamat_lokasi' => $assoc['alamat_lokasi'],
                        'ukuran' => ($assoc['ukuran'] ?? '') !== '' ? $assoc['ukuran'] : '-',
                        'spesifikasi_lampu' => ($assoc['spesifikasi_lampu'] ?? '') !== '' ? $assoc['spesifikasi_lampu'] : null,
                        'status_titik' => $statusTitik,
                        'latitude' => is_numeric($assoc['latitude'] ?? null) ? (float) $assoc['latitude'] : null,
                        'longitude' => is_numeric($assoc['longitude'] ?? null) ? (float) $assoc['longitude'] : null,
                        'google_maps_url' => ($assoc['google_maps_url'] ?? '') !== '' ? $assoc['google_maps_url'] : null,
                        'no_izin' => ($assoc['no_izin'] ?? '') !== '' ? $assoc['no_izin'] : null,
                        'tgl_izin_berakhir' => $this->parseNullableDate($assoc['tgl_izin_berakhir'] ?? null),
                        'tgl_pajak_berakhir' => $this->parseNullableDate($assoc['tgl_pajak_berakhir'] ?? null),
                        'keterangan' => ($assoc['keterangan'] ?? '') !== '' ? $assoc['keterangan'] : null,
                    ]
                );

                $imported++;
            }
        });

        fclose($handle);

        return redirect()
            ->route('penyewaanreklame.titik-reklame.index')
            ->with('success', "Berhasil mengimpor {$imported} data titik reklame dari file CSV.");
    }

    private function parseNullableDate(?string $value): ?string
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '' || $trimmed === '-') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($trimmed)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return Builder<TitikReklame>
     */
    private function filteredQuery(Request $request): Builder
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
            $query->where(function (Builder $sub) use ($keyword): void {
                $sub->where('kode_titik', 'like', $keyword)
                    ->orWhere('alamat_lokasi', 'like', $keyword)
                    ->orWhere('wilayah', 'like', $keyword)
                    ->orWhere('ukuran', 'like', $keyword);
            });
        }

        return $query;
    }

    /**
     * @return array<int, string>
     */
    private function distinctWilayah(): array
    {
        return TitikReklame::query()
            ->select('wilayah')
            ->distinct()
            ->orderBy('wilayah')
            ->pluck('wilayah')
            ->all();
    }

    /**
     * @return list<string>
     */
    private function wilayahOptions(): array
    {
        return [
            'Banjarbaru',
            'Kab. Banjar (Martapura)',
            'Kab. Banjar (Gambut)',
            'Banjarmasin',
            'Kab. Tapin',
            'Kab. Tanah Laut',
            'Kab. Tanah Bumbu',
            'Kab. Balangan',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?TitikReklame $titik = null): array
    {
        return $request->validate([
            'kode_titik' => [
                'required',
                'string',
                'max:30',
                Rule::unique('adv_titik_reklame', 'kode_titik')->ignore($titik?->id),
            ],
            'kategori_space' => ['required', Rule::in(['internal', 'mitra'])],
            'wilayah' => ['required', 'string', 'max:100'],
            'alamat_lokasi' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'url', 'max:255'],
            'ukuran' => ['required', 'string', 'max:50'],
            'spesifikasi_lampu' => ['nullable', 'string', 'max:100'],
            'status_titik' => ['required', Rule::in(['Tersedia', 'Tersewa', 'Maintenance'])],
            'no_izin' => ['nullable', 'string', 'max:100'],
            'tgl_izin_berakhir' => ['nullable', 'date'],
            'tgl_pajak_berakhir' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string'],
            'foto_papan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function handleFotoUpload(Request $request, ?TitikReklame $titik = null): ?string
    {
        $currentFoto = $titik?->foto_papan;

        if ($request->boolean('hapus_foto') && $currentFoto) {
            Storage::disk('public')->delete($currentFoto);
            $currentFoto = null;
        }

        if ($request->hasFile('foto_papan')) {
            if ($currentFoto) {
                Storage::disk('public')->delete($currentFoto);
            }

            return $request->file('foto_papan')->store('penyewaanreklame/titik', 'public');
        }

        return $currentFoto;
    }
}

<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PenyewaanReklame\Concerns\ResolvesSessionUser;
use App\Models\Klien;
use App\Models\KontrakSewa;
use App\Models\TitikReklame;
use Carbon\Carbon;
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
    use ResolvesSessionUser;
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
        $delimiter = $this->detectCsvDelimiter($path);
        $rows = $this->readCsvRows($path, $delimiter);

        if ($rows === []) {
            return back()->with('error', 'File CSV kosong atau tidak terbaca.');
        }

        $firstHeaders = $this->normalizeCsvHeaders($rows[0]);
        $isStandardFormat = in_array('kode_titik', $firstHeaders, true)
            && in_array('wilayah', $firstHeaders, true)
            && in_array('alamat_lokasi', $firstHeaders, true);

        if ($isStandardFormat) {
            $result = $this->importStandardCsvRows($rows, $firstHeaders);
        } elseif ($this->looksLikeRestuGuruSheet($rows)) {
            $result = $this->importRestuGuruSheetRows($rows);
        } else {
            return back()->with(
                'error',
                'Format CSV belum sesuai. Gunakan header template (kode_titik, wilayah, alamat_lokasi) atau format spreadsheet Space Baliho Restu Guru (kolom TITIK BALIHO & UKURAN).'
            );
        }

        $message = "Berhasil mengimpor {$result['titik']} data titik reklame dari file CSV.";
        if ($result['kontrak'] > 0) {
            $message .= " ({$result['kontrak']} kontrak sewa & data klien otomatis dicatat).";
        }

        return redirect()
            ->route('penyewaanreklame.titik-reklame.index')
            ->with('success', $message);
    }

    private function detectCsvDelimiter(string $path): string
    {
        $probe = fopen($path, 'r');
        if (! $probe) {
            return ',';
        }

        $sample = '';
        while (($line = fgets($probe)) !== false) {
            if (trim($line) !== '') {
                $sample = $line;
                break;
            }
        }
        fclose($probe);

        return substr_count($sample, ';') > substr_count($sample, ',') ? ';' : ',';
    }

    /**
     * @return list<list<string>>
     */
    private function readCsvRows(string $path, string $delimiter): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            $cleaned = array_map(function (?string $cell): string {
                $noBom = preg_replace('/^\xEF\xBB\xBF/', '', (string) $cell);

                return trim((string) $noBom);
            }, $row);

            if (count(array_filter($cleaned, fn (string $v): bool => $v !== '')) === 0) {
                continue;
            }

            $rows[] = $cleaned;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * @param list<string> $rawHeaders
     * @return list<string>
     */
    private function normalizeCsvHeaders(array $rawHeaders): array
    {
        return array_map(function (string $col): string {
            $normalized = strtolower(trim($col));
            $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized) ?? $normalized;

            return trim($normalized, '_');
        }, $rawHeaders);
    }

    /**
     * @param list<list<string>> $rows
     */
    private function looksLikeRestuGuruSheet(array $rows): bool
    {
        foreach ($rows as $row) {
            $headers = $this->normalizeCsvHeaders($row);
            if (in_array('titik_baliho', $headers, true) && in_array('ukuran', $headers, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<list<string>> $rows
     * @param list<string> $headers
     * @return array{titik: int, kontrak: int}
     */
    private function importStandardCsvRows(array $rows, array $headers): array
    {
        $imported = 0;

        DB::transaction(function () use ($rows, $headers, &$imported): void {
            foreach (array_slice($rows, 1) as $row) {
                $assoc = [];
                foreach ($headers as $idx => $key) {
                    $assoc[$key] = $row[$idx] ?? null;
                }

                $kode = $assoc['kode_titik'] ?? '';
                if ($kode === '' || ($assoc['wilayah'] ?? '') === '' || ($assoc['alamat_lokasi'] ?? '') === '') {
                    continue;
                }

                $rawSpace = strtolower($assoc['kategori_space'] ?? 'internal');
                $kategoriSpace = str_contains($rawSpace, 'mitra') ? 'mitra' : 'internal';

                TitikReklame::updateOrCreate(
                    ['kode_titik' => $kode],
                    [
                        'kategori_space' => $kategoriSpace,
                        'wilayah' => $assoc['wilayah'],
                        'alamat_lokasi' => $assoc['alamat_lokasi'],
                        'ukuran' => ($assoc['ukuran'] ?? '') !== '' ? $assoc['ukuran'] : '-',
                        'spesifikasi_lampu' => ($assoc['spesifikasi_lampu'] ?? '') !== '' ? $assoc['spesifikasi_lampu'] : null,
                        'status_titik' => $this->normalizeStatusTitik($assoc['status_titik'] ?? 'tersedia'),
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

        return ['titik' => $imported, 'kontrak' => 0];
    }

    /**
     * Impor format spreadsheet operasional Restu Guru Promosindo (Internal & Mitra).
     *
     * @param list<list<string>> $rows
     * @return array{titik: int, kontrak: int}
     */
    private function importRestuGuruSheetRows(array $rows): array
    {
        $imported = 0;
        $kontrakCount = 0;
        $kategoriSpace = 'internal';
        $activeHeaders = null;
        $sequenceBySpace = ['internal' => 0, 'mitra' => 0];
        $userId = $this->resolveSessionUserId();

        DB::transaction(function () use (
            $rows,
            &$imported,
            &$kontrakCount,
            &$kategoriSpace,
            &$activeHeaders,
            &$sequenceBySpace,
            $userId
        ): void {
            foreach ($rows as $row) {
                $rowText = strtolower(implode(' ', array_filter($row, fn (string $v): bool => $v !== '')));
                if (str_contains($rowText, 'space baliho')) {
                    $kategoriSpace = str_contains($rowText, 'mitra') ? 'mitra' : 'internal';
                    continue;
                }

                $normalized = $this->normalizeCsvHeaders($row);
                if (in_array('titik_baliho', $normalized, true) && in_array('ukuran', $normalized, true)) {
                    $activeHeaders = $normalized;
                    continue;
                }

                if ($activeHeaders === null) {
                    continue;
                }

                $assoc = [];
                foreach ($activeHeaders as $idx => $key) {
                    if ($key !== '') {
                        $assoc[$key] = $row[$idx] ?? '';
                    }
                }

                $alamat = $assoc['titik_baliho'] ?? ($assoc['alamat_lokasi'] ?? '');
                if ($alamat === '') {
                    continue;
                }

                $sequenceBySpace[$kategoriSpace]++;
                $rawNo = $assoc['no'] ?? '';
                $nomorUrut = ctype_digit($rawNo) ? (int) $rawNo : $sequenceBySpace[$kategoriSpace];
                $kode = ($assoc['kode_titik'] ?? '') !== ''
                    ? substr($assoc['kode_titik'], 0, 30)
                    : sprintf('%s-%02d', $kategoriSpace === 'mitra' ? 'RGP-MTR' : 'RGP-INT', $nomorUrut);

                $statusTitik = $this->normalizeStatusTitik($assoc['status_titik'] ?? ($assoc['keterangan'] ?? 'ready'));
                $wilayah = ($assoc['wilayah'] ?? '') !== ''
                    ? $assoc['wilayah']
                    : $this->inferWilayahFromAlamat($alamat);
                $spesifikasi = $assoc['spesifikasi_lampu'] ?? ($assoc['spesifikasi'] ?? '');
                $tanggalSewa = $this->cleanSpreadsheetPlaceholder($assoc['tanggal_sewa'] ?? '');
                $materiSewa = $this->cleanSpreadsheetPlaceholder($assoc['materi_sewa'] ?? '');

                $keteranganParts = array_filter([
                    $materiSewa !== null ? "Materi: {$materiSewa}" : null,
                    $tanggalSewa !== null ? "Periode Sewa: {$tanggalSewa}" : null,
                ]);

                $titik = TitikReklame::updateOrCreate(
                    ['kode_titik' => $kode],
                    [
                        'kategori_space' => $kategoriSpace,
                        'wilayah' => $wilayah,
                        'alamat_lokasi' => $alamat,
                        'ukuran' => ($assoc['ukuran'] ?? '') !== '' ? $assoc['ukuran'] : '-',
                        'spesifikasi_lampu' => $spesifikasi !== '' ? $spesifikasi : null,
                        'status_titik' => $statusTitik,
                        'keterangan' => $keteranganParts !== [] ? implode(' | ', $keteranganParts) : null,
                    ]
                );

                $imported++;

                if ($statusTitik === 'Tersewa' && ($materiSewa !== null || $tanggalSewa !== null)) {
                    $kontrakCount += $this->syncImportedRentalContract($titik, $materiSewa, $tanggalSewa, $userId);
                }
            }
        });

        return ['titik' => $imported, 'kontrak' => $kontrakCount];
    }

    private function normalizeStatusTitik(string $rawStatus): string
    {
        $status = strtolower(trim($rawStatus));

        return match (true) {
            str_contains($status, 'sewa') => 'Tersewa',
            str_contains($status, 'maint') => 'Maintenance',
            default => 'Tersedia',
        };
    }

    private function cleanSpreadsheetPlaceholder(string $value): ?string
    {
        $trimmed = trim($value);

        return ($trimmed === '' || $trimmed === '-') ? null : $trimmed;
    }

    private function inferWilayahFromAlamat(string $alamat): string
    {
        // Hapus keterangan arah seperti "(Arah Banjarmasin)" agar tidak salah mendeteksi kota tujuan.
        $cleaned = preg_replace('/\(\s*arah\b[^)]*\)/i', '', $alamat) ?? $alamat;
        $lower = strtolower($cleaned);

        return match (true) {
            str_contains($lower, 'banjarbaru') => 'Banjarbaru',
            str_contains($lower, 'banjarmasin') => 'Banjarmasin',
            str_contains($lower, 'gambut') => 'Kab. Banjar (Gambut)',
            str_contains($lower, 'martapura') || str_contains($lower, 'kab. banjar') || str_contains($lower, 'kabupaten banjar') => 'Kab. Banjar (Martapura)',
            str_contains($lower, 'tanah laut') || str_contains($lower, 'pelaihari') => 'Kab. Tanah Laut',
            str_contains($lower, 'tanah bumbu') || str_contains($lower, 'batulicin') => 'Kab. Tanah Bumbu',
            str_contains($lower, 'tapin') || str_contains($lower, 'rantau') => 'Kab. Tapin',
            str_contains($lower, 'balangan') || str_contains($lower, 'batu mandi') || str_contains($lower, 'paringin') => 'Kab. Balangan',
            str_contains($lower, 'barito kuala') || str_contains($lower, 'batola') || str_contains($lower, 'marabahan') => 'Kab. Barito Kuala',
            default => 'Kalimantan Selatan',
        };
    }

    private function syncImportedRentalContract(
        TitikReklame $titik,
        ?string $materiSewa,
        ?string $tanggalSewa,
        ?int $userId
    ): int {
        $namaKlien = $materiSewa ?? "Penyewa {$titik->kode_titik}";
        $klien = Klien::firstOrCreate(
            ['nama_klien' => $namaKlien],
            [
                'kategori_klien' => 'perusahaan',
                'no_wa' => '-',
            ]
        );

        [$tglMulai, $tglBerakhir] = $this->parseDateRange($tanggalSewa);
        $durasiBulan = ($tglMulai && $tglBerakhir)
            ? (int) max(1, min(36, round($tglMulai->diffInMonths($tglBerakhir))))
            : 1;

        KontrakSewa::updateOrCreate(
            [
                'titik_reklame_id' => $titik->id,
                'no_penawaran' => "IMP-{$titik->kode_titik}",
            ],
            [
                'klien_id' => $klien->id,
                'user_id' => $userId,
                'tgl_penawaran' => $tglMulai?->toDateString() ?? now()->toDateString(),
                'tgl_acc' => $tglMulai?->toDateString() ?? now()->toDateString(),
                'tgl_mulai' => $tglMulai?->toDateString(),
                'tgl_berakhir' => $tglBerakhir?->toDateString(),
                'durasi_bulan' => $durasiBulan,
                'nilai_kontrak' => 0,
                'materi_iklan' => $materiSewa,
                'tgl_pemasangan' => $tglMulai?->toDateString(),
                'status_kontrak' => 'aktif',
                'status_perpanjangan' => 'belum_konfirmasi',
                'status_materi' => 'terpasang',
                'catatan' => 'Diimpor otomatis dari spreadsheet daftar baliho.',
            ]
        );

        return 1;
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function parseDateRange(?string $range): array
    {
        if ($range === null) {
            return [null, null];
        }

        $parts = preg_split('/\s+s\/?d\s+|\s+to\s+/i', $range) ?: [];
        $start = $this->parseIndoDate($parts[0] ?? null);
        $end = $this->parseIndoDate($parts[1] ?? null);

        return [$start, $end];
    }

    private function parseIndoDate(?string $value): ?Carbon
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '' || $trimmed === '-') {
            return null;
        }

        foreach (['!d/m/Y', '!d-m-Y', '!Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $trimmed);
            } catch (\Throwable) {
                // Coba format berikutnya.
            }
        }

        return null;
    }

    private function parseNullableDate(?string $value): ?string
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '' || $trimmed === '-') {
            return null;
        }

        try {
            return Carbon::parse($trimmed)->format('Y-m-d');
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

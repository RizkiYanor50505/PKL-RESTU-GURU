<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PenyewaanReklame\Concerns\ResolvesSessionUser;
use App\Models\Klien;
use App\Models\KontrakSewa;
use App\Models\TitikReklame;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KontrakSewaController extends Controller
{
    use ResolvesSessionUser;

    public function index(Request $request): View
    {
        $query = KontrakSewa::query()
            ->with(['titikReklame', 'klien'])
            ->withSum('pembayarans', 'jumlah_bayar');

        if ($request->filled('status_kontrak')) {
            $query->where('status_kontrak', $request->string('status_kontrak'));
        }

        if ($request->filled('status_materi')) {
            $query->where('status_materi', $request->string('status_materi'));
        }

        if ($request->filled('kategori_klien')) {
            $kategori = $request->string('kategori_klien');
            $query->whereHas('klien', fn ($q) => $q->where('kategori_klien', $kategori));
        }

        if ($request->filled('q')) {
            $keyword = '%' . trim($request->string('q')) . '%';
            $query->where(function ($sub) use ($keyword): void {
                $sub->where('no_penawaran', 'like', $keyword)
                    ->orWhere('no_kontrak', 'like', $keyword)
                    ->orWhere('materi_iklan', 'like', $keyword)
                    ->orWhereHas('klien', fn ($k) => $k->where('nama_klien', 'like', $keyword))
                    ->orWhereHas('titikReklame', fn ($t) => $t->where('kode_titik', 'like', $keyword)
                        ->orWhere('wilayah', 'like', $keyword));
            });
        }

        $kontraks = $query->latest('tgl_penawaran')->latest('id')->get();

        $summary = [
            'penawaran' => KontrakSewa::whereIn('status_kontrak', ['penawaran', 'acc'])->count(),
            'aktif' => KontrakSewa::where('status_kontrak', 'aktif')->count(),
            'jatuh_tempo' => KontrakSewa::where('status_kontrak', 'aktif')
                ->whereNotNull('tgl_berakhir')
                ->whereDate('tgl_berakhir', '<=', now()->addDays(14)->toDateString())
                ->count(),
            'perlu_dicopot' => KontrakSewa::where('status_materi', 'perlu_dicopot')->count(),
        ];

        return view('penyewaanreklame.kontrak.index', compact('kontraks', 'summary'));
    }

    public function create(Request $request): View
    {
        $titikReklames = TitikReklame::orderByRaw("FIELD(status_titik, 'Tersedia', 'Tersewa', 'Maintenance')")
            ->orderBy('kode_titik')
            ->get();
        $kliens = Klien::orderBy('nama_klien')->get();
        $selectedTitikId = $request->integer('titik_reklame_id') ?: null;

        return view('penyewaanreklame.kontrak.create', compact(
            'titikReklames',
            'kliens',
            'selectedTitikId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titik_reklame_id' => ['required', 'exists:adv_titik_reklame,id'],
            'klien_id' => ['required', 'exists:adv_klien,id'],
            'no_penawaran' => ['required', 'string', 'max:60'],
            'tgl_penawaran' => ['required', 'date'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:36'],
            'nilai_kontrak' => ['required', 'numeric', 'min:0'],
            'materi_iklan' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
        ]);

        // Penawaran awal tidak mengunci status papan (tanpa holding/booking berbayar).
        $kontrak = KontrakSewa::create(array_merge($validated, [
            'user_id' => $this->resolveSessionUserId(),
            'status_kontrak' => 'penawaran',
            'status_perpanjangan' => 'belum_konfirmasi',
            'status_materi' => 'belum_pasang',
        ]));

        return redirect()
            ->route('penyewaanreklame.kontrak.show', $kontrak)
            ->with('success', "Surat penawaran {$kontrak->no_penawaran} berhasil dicatat. Status titik tetap Tersedia sampai proses pemasangan.");
    }

    public function show(KontrakSewa $kontrak): View
    {
        $kontrak->load([
            'titikReklame',
            'klien',
            'user',
            'pembayarans' => fn ($q) => $q->with('user')->latest('tgl_bayar')->latest('id'),
        ]);

        $totalDibayar = (float) $kontrak->pembayarans->sum('jumlah_bayar');

        return view('penyewaanreklame.kontrak.show', compact('kontrak', 'totalDibayar'));
    }

    public function tandaiAcc(Request $request, KontrakSewa $kontrak): RedirectResponse
    {
        if ($kontrak->status_kontrak !== 'penawaran') {
            return back()->with('error', 'Hanya penawaran yang belum diproses yang dapat ditandai ACC.');
        }

        $validated = $request->validate([
            'tgl_acc' => ['required', 'date'],
            'no_kontrak' => [
                'nullable',
                'string',
                'max:60',
                Rule::unique('adv_kontrak_sewa', 'no_kontrak')->ignore($kontrak->id),
            ],
        ]);

        $kontrak->update([
            'tgl_acc' => $validated['tgl_acc'],
            'no_kontrak' => ($validated['no_kontrak'] ?? null) ?: null,
            'status_kontrak' => 'acc',
        ]);

        return back()->with('success', 'Penawaran berhasil ditandai ACC oleh klien.');
    }

    public function aktivasiPemasangan(Request $request, KontrakSewa $kontrak): RedirectResponse
    {
        $kontrak->loadMissing(['klien', 'titikReklame']);

        if (! in_array($kontrak->status_kontrak, ['acc', 'aktif'], true)) {
            return back()->with('error', 'Penawaran harus berstatus ACC terlebih dahulu sebelum pemasangan.');
        }

        // Aturan hasil wawancara: klien perorangan wajib payment di awal sebelum produksi & pemasangan.
        if ($kontrak->klien?->kategori_klien === 'perorangan' && ! $kontrak->pembayarans()->exists()) {
            return back()->with('error', 'Klien kategori Perorangan wajib melakukan pembayaran di awal terlebih dahulu sebelum produksi & pemasangan.');
        }

        $validated = $request->validate([
            'no_kontrak' => [
                'nullable',
                'string',
                'max:60',
                Rule::unique('adv_kontrak_sewa', 'no_kontrak')->ignore($kontrak->id),
            ],
            'tgl_mulai' => ['required', 'date'],
            'tgl_berakhir' => ['required', 'date', 'after_or_equal:tgl_mulai'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:36'],
            'tgl_pemasangan' => ['required', 'date'],
            'materi_iklan' => ['nullable', 'string'],
            'file_berita_acara' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $beritaAcaraPath = $kontrak->file_berita_acara;
        if ($request->hasFile('file_berita_acara')) {
            if ($beritaAcaraPath) {
                Storage::disk('public')->delete($beritaAcaraPath);
            }
            $beritaAcaraPath = $request->file('file_berita_acara')->store('penyewaanreklame/berita-acara', 'public');
        }

        DB::transaction(function () use ($kontrak, $validated, $beritaAcaraPath): void {
            $kontrak->update([
                'no_kontrak' => ($validated['no_kontrak'] ?? null) ?: $kontrak->no_kontrak,
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_berakhir' => $validated['tgl_berakhir'],
                'durasi_bulan' => $validated['durasi_bulan'],
                'tgl_pemasangan' => $validated['tgl_pemasangan'],
                'materi_iklan' => $validated['materi_iklan'] ?? $kontrak->materi_iklan,
                'file_berita_acara' => $beritaAcaraPath,
                'status_kontrak' => 'aktif',
                'status_materi' => 'terpasang',
            ]);

            $kontrak->titikReklame()->update(['status_titik' => 'Tersewa']);
        });

        return back()->with('success', 'Kontrak sewa aktif, materi ditandai Terpasang, dan status papan diubah menjadi Tersewa.');
    }

    public function updatePerpanjangan(Request $request, KontrakSewa $kontrak): RedirectResponse
    {
        $validated = $request->validate([
            'status_perpanjangan' => ['required', Rule::in(['belum_konfirmasi', 'diperpanjang', 'tidak_diperpanjang'])],
        ]);

        $updates = ['status_perpanjangan' => $validated['status_perpanjangan']];

        if (
            $validated['status_perpanjangan'] === 'tidak_diperpanjang'
            && $kontrak->tgl_berakhir
            && $kontrak->tgl_berakhir->isPast()
            && $kontrak->status_materi === 'terpasang'
        ) {
            $updates['status_materi'] = 'perlu_dicopot';
        }

        $kontrak->update($updates);

        return back()->with('success', 'Status konfirmasi perpanjangan sewa berhasil diperbarui.');
    }

    public function tandaiPerluDicopot(KontrakSewa $kontrak): RedirectResponse
    {
        if ($kontrak->status_materi !== 'terpasang') {
            return back()->with('error', 'Penanda pencopotan hanya berlaku untuk materi yang masih terpasang.');
        }

        $kontrak->update([
            'status_perpanjangan' => 'tidak_diperpanjang',
            'status_materi' => 'perlu_dicopot',
        ]);

        return back()->with('success', 'Kontrak ditandai perlu pencopotan materi oleh tim lapangan.');
    }

    public function konfirmasiTurunMateri(KontrakSewa $kontrak): RedirectResponse
    {
        DB::transaction(function () use ($kontrak): void {
            $kontrak->update([
                'status_materi' => 'sudah_dicopot',
                'status_kontrak' => 'selesai',
            ]);

            $kontrak->titikReklame()->update(['status_titik' => 'Tersedia']);
        });

        return back()->with('success', 'Materi iklan dikonfirmasi sudah dicopot, kontrak selesai, dan status titik kembali Tersedia.');
    }

    public function batalkan(KontrakSewa $kontrak): RedirectResponse
    {
        DB::transaction(function () use ($kontrak): void {
            $wasAktif = $kontrak->status_kontrak === 'aktif';
            $kontrak->update(['status_kontrak' => 'batal']);

            if ($wasAktif) {
                $adaKontrakAktifLain = KontrakSewa::where('titik_reklame_id', $kontrak->titik_reklame_id)
                    ->where('id', '!=', $kontrak->id)
                    ->where('status_kontrak', 'aktif')
                    ->exists();

                if (! $adaKontrakAktifLain) {
                    $kontrak->titikReklame()->update(['status_titik' => 'Tersedia']);
                }
            }
        });

        return back()->with('success', 'Penawaran/kontrak telah dibatalkan.');
    }

    public function destroy(KontrakSewa $kontrak): RedirectResponse
    {
        if (! in_array($kontrak->status_kontrak, ['penawaran', 'batal'], true) || $kontrak->pembayarans()->exists()) {
            return redirect()
                ->route('penyewaanreklame.kontrak.index')
                ->with('error', 'Hanya penawaran/kontrak batal tanpa riwayat pembayaran yang dapat dihapus.');
        }

        if ($kontrak->file_berita_acara && Storage::disk('public')->exists($kontrak->file_berita_acara)) {
            Storage::disk('public')->delete($kontrak->file_berita_acara);
        }

        $nomor = $kontrak->no_penawaran;
        $kontrak->delete();

        return redirect()
            ->route('penyewaanreklame.kontrak.index')
            ->with('success', "Data penawaran {$nomor} berhasil dihapus.");
    }
}

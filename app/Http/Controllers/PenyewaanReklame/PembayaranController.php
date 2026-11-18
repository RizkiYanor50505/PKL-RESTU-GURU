<?php

namespace App\Http\Controllers\PenyewaanReklame;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PenyewaanReklame\Concerns\ResolvesSessionUser;
use App\Models\KontrakSewa;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranController extends Controller
{
    use ResolvesSessionUser;

    public function store(Request $request, KontrakSewa $kontrak): RedirectResponse
    {
        $validated = $request->validate([
            'tgl_bayar' => ['required', 'date'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'jenis_pembayaran' => ['required', Rule::in(['bayar_awal', 'dp', 'pelunasan', 'tagihan_perusahaan'])],
            'metode_pembayaran' => ['nullable', 'string', 'max:50'],
            'bukti_pembayaran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'catatan' => ['nullable', 'string'],
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('penyewaanreklame/pembayaran', 'public');
        }

        $kontrak->pembayarans()->create([
            'user_id' => $this->resolveSessionUserId(),
            'tgl_bayar' => $validated['tgl_bayar'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'jenis_pembayaran' => $validated['jenis_pembayaran'],
            'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
            'bukti_pembayaran' => $buktiPath,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return back()->with('success', 'Data pembayaran berhasil dicatat.');
    }

    public function destroy(Pembayaran $pembayaran): RedirectResponse
    {
        $kontrakId = $pembayaran->kontrak_sewa_id;

        if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
            Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
        }

        $pembayaran->delete();

        return redirect()
            ->route('penyewaanreklame.kontrak.show', $kontrakId)
            ->with('success', 'Catatan pembayaran berhasil dihapus.');
    }
}

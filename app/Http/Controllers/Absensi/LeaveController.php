<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LeaveController extends Controller
{
    private const PROOF_DISK = 'local';

    private const PROOF_DIRECTORY = 'bukti-izin';

    /**
     * Show the leave form together with the signed-in employee's stored requests.
     */
    public function index(Request $request): View
    {
        $requests = PengajuanIzin::query()
            ->with('approver')
            ->where('user_id', $this->employee($request)->id)
            ->latest()
            ->get();

        $usedAnnualDays = $requests
            ->where('jenis', 'Cuti Tahunan')
            ->where('status_izin', PengajuanIzin::STATUS_APPROVED)
            ->filter(fn (PengajuanIzin $item) => $item->tgl_mulai->year === now()->year)
            ->sum(fn (PengajuanIzin $item) => $item->durationDays());

        return view('absensi.karyawan.izin', [
            'requests' => $requests,
            'pendingCount' => $requests->where('status_izin', PengajuanIzin::STATUS_PENDING)->count(),
            'approvedCount' => $requests->where('status_izin', PengajuanIzin::STATUS_APPROVED)->count(),
            'rejectedCount' => $requests->where('status_izin', PengajuanIzin::STATUS_REJECTED)->count(),
            'remainingAnnualLeave' => max(0, PengajuanIzin::ANNUAL_LEAVE_QUOTA - $usedAnnualDays),
            'annualQuota' => PengajuanIzin::ANNUAL_LEAVE_QUOTA,
        ]);
    }

    /**
     * Persist a new leave request from the signed-in employee.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', Rule::in(PengajuanIzin::TYPES)],
            'tgl_mulai' => ['required', 'date'],
            'tgl_selesai' => ['required', 'date', 'after_or_equal:tgl_mulai'],
            'alasan' => ['required', 'string', 'max:1000'],
            'jam_tiba' => ['nullable', 'date_format:H:i'],
            'foto_bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $reason = $validated['alasan'];
        if ($validated['jenis'] === 'Izin Terlambat' && ! empty($validated['jam_tiba'])) {
            $reason .= ' (Estimasi tiba '.$validated['jam_tiba'].' WIB)';
        }

        $path = $request->hasFile('foto_bukti')
            ? $request->file('foto_bukti')->store(self::PROOF_DIRECTORY, self::PROOF_DISK)
            : null;

        $leave = PengajuanIzin::create([
            'user_id' => $this->employee($request)->id,
            'jenis' => $validated['jenis'],
            'tgl_mulai' => $validated['tgl_mulai'],
            'tgl_selesai' => $validated['tgl_selesai'],
            'alasan' => $reason,
            'foto_bukti' => $path,
            'status_izin' => PengajuanIzin::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan izin Anda telah masuk ke antrean verifikasi HRD.',
            'id' => $leave->id,
        ], 201);
    }

    /**
     * Approve or reject a pending request as the signed-in manager.
     */
    public function decide(Request $request, PengajuanIzin $izin): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([PengajuanIzin::STATUS_APPROVED, PengajuanIzin::STATUS_REJECTED])],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $izin->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini sudah diputuskan sebelumnya.',
            ], 409);
        }

        $izin->update([
            'status_izin' => $validated['status'],
            'approved_by' => User::query()->where('nik', $request->session()->get('auth_user.nik'))->value('id'),
            'catatan_admin' => $validated['catatan_admin'] ?? null,
        ]);

        $approved = $validated['status'] === PengajuanIzin::STATUS_APPROVED;

        return response()->json([
            'success' => true,
            'status' => $izin->status_izin,
            'message' => 'Izin '.$izin->user->name.($approved ? ' telah disetujui.' : ' telah ditolak.'),
            'pending_count' => PengajuanIzin::query()->where('status_izin', PengajuanIzin::STATUS_PENDING)->count(),
        ]);
    }

    /**
     * Stream the stored proof attachment to authorized personnel.
     */
    public function proof(Request $request, PengajuanIzin $izin): BinaryFileResponse
    {
        abort_unless($izin->foto_bukti !== null, 404);

        $role = $request->session()->get('auth_role');
        if ($role === 'karyawan') {
            $user = User::query()->where('nik', $request->session()->get('auth_user.nik'))->first();
            abort_unless($user !== null && $izin->user_id === $user->id, 403, 'Anda tidak berhak melihat berkas ini.');
        }

        return response()->file(Storage::disk(self::PROOF_DISK)->path($izin->foto_bukti));
    }

    /**
     * Resolve the stored employee behind the current session NIK.
     */
    private function employee(Request $request): User
    {
        $nik = $request->session()->get('auth_user.nik');
        abort_unless(is_string($nik) && $nik !== '', 403, 'Sesi karyawan tidak valid.');

        return User::query()->firstOrCreate(
            ['nik' => $nik],
            [
                'name' => (string) ($request->session()->get('auth_user.name') ?? 'Karyawan'),
                'email' => (string) ($request->session()->get('auth_user.email') ?? strtolower($nik).'@perusahaan.co.id'),
                'password' => bcrypt('password'),
                'role' => 'karyawan',
                'division' => (string) ($request->session()->get('auth_user.division') ?? 'Divisi Umum'),
                'is_active' => true,
            ]
        );
    }
}

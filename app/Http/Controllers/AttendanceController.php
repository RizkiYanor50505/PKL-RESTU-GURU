<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private const EARTH_RADIUS_METERS = 6371000;

    /**
     * Show today's attendance summary and monthly indicators for the signed-in employee.
     */
    public function dashboard(Request $request): View
    {
        $nik = $this->employeeNik($request);
        $user = User::query()->where('nik', $nik)->first();
        $now = now(config('attendance.timezone'));

        $monthRecords = Presensi::query()
            ->where('nik', $nik)
            ->whereDate('attendance_date', '>=', $now->copy()->startOfMonth()->toDateString())
            ->whereDate('attendance_date', '<=', $now->copy()->endOfMonth()->toDateString())
            ->orderByDesc('attendance_date')
            ->get();

        $usedAnnualDays = $user !== null
            ? (int) PengajuanIzin::query()
                ->where('user_id', $user->id)
                ->where('jenis', 'Cuti Tahunan')
                ->where('status_izin', PengajuanIzin::STATUS_APPROVED)
                ->whereYear('tgl_mulai', $now->year)
                ->get()
                ->sum(fn (PengajuanIzin $item) => $item->durationDays())
            : 0;
        $remainingAnnualLeave = max(0, PengajuanIzin::ANNUAL_LEAVE_QUOTA - $usedAnnualDays);

        return view('karyawan.dashboard', [
            'today' => $monthRecords->first(fn (Presensi $record) => $record->attendance_date->toDateString() === $now->toDateString()),
            'monthName' => $now->copy()->locale('id')->translatedFormat('F Y'),
            'monthShort' => $now->copy()->locale('id')->translatedFormat('M'),
            'presentDays' => $monthRecords->count(),
            'workingDays' => $this->countWorkingDays($now),
            'lateRecords' => $monthRecords->filter(fn (Presensi $record) => $record->isLate()),
            'latestRecord' => Presensi::query()->where('nik', $nik)->latest('masuk_at')->first(),
            'usedAnnualDays' => $usedAnnualDays,
            'remainingAnnualLeave' => $remainingAnnualLeave,
        ]);
    }

    /**
     * List every stored attendance record for the signed-in employee.
     */
    public function history(Request $request): View
    {
        $nik = $this->employeeNik($request);
        $user = User::query()->where('nik', $nik)->first();

        $records = Presensi::query()
            ->where('nik', $nik)
            ->orderByDesc('attendance_date')
            ->get();

        $leaveRecords = $user !== null
            ? PengajuanIzin::query()
                ->where('user_id', $user->id)
                ->with('approver')
                ->orderByDesc('tgl_mulai')
                ->get()
            : collect();

        $approvedLeaves = $leaveRecords->where('status_izin', PengajuanIzin::STATUS_APPROVED);
        $totalLeaveDays = (int) $approvedLeaves->sum(fn (PengajuanIzin $item) => $item->durationDays());

        $completed = $records->filter(fn (Presensi $record) => $record->pulang_at !== null);
        $averageMinutes = $completed->isEmpty()
            ? 0
            : (int) round($completed->avg(fn (Presensi $record) => $record->masuk_at->diffInMinutes($record->pulang_at)));

        return view('karyawan.riwayat', [
            'records' => $records,
            'leaveRecords' => $leaveRecords,
            'approvedLeaves' => $approvedLeaves,
            'totalLeaveDays' => $totalLeaveDays,
            'lateRecords' => $records->filter(fn (Presensi $record) => $record->isLate()),
            'totalMinutes' => (int) $completed->sum(fn (Presensi $record) => $record->masuk_at->diffInMinutes($record->pulang_at)),
            'averageMinutes' => $averageMinutes,
        ]);
    }

    /**
     * Show the manager command center with the interactive geofence map configurator.
     */
    public function adminDashboard(): View
    {
        $today = now(config('attendance.timezone'))->toDateString();

        return view('admin.dashboard', [
            'lokasi' => $this->activeLokasi(),
            'pendingLeaves' => PengajuanIzin::query()
                ->with('user')
                ->where('status_izin', PengajuanIzin::STATUS_PENDING)
                ->oldest()
                ->get(),
            'onLeaveTodayCount' => PengajuanIzin::query()
                ->where('status_izin', '!=', PengajuanIzin::STATUS_REJECTED)
                ->whereDate('tgl_mulai', '<=', $today)
                ->whereDate('tgl_selesai', '>=', $today)
                ->count(),
        ]);
    }

    /**
     * Persist updated office coordinates, geofence radius, QR token, and allowed IPs.
     */
    public function updateLokasi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_lokasi' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'qr_token' => ['required', 'string', 'max:100'],
            'allowed_ips' => ['nullable', 'string', 'max:500'],
        ]);

        $ips = collect(explode(',', (string) ($validated['allowed_ips'] ?? '')))
            ->map(fn (string $ip) => trim($ip))
            ->filter(fn (string $ip) => $ip !== '')
            ->values()
            ->all();

        $attributes = [
            'nama_lokasi' => trim($validated['nama_lokasi']),
            'latitude' => round((float) $validated['latitude'], 7),
            'longitude' => round((float) $validated['longitude'], 7),
            'radius_meters' => (int) $validated['radius_meters'],
            'qr_token' => trim($validated['qr_token']),
            'allowed_ips' => $ips,
        ];

        $lokasi = Lokasi::query()->first();

        if ($lokasi === null) {
            $lokasi = Lokasi::create($attributes);
        } else {
            $lokasi->update($attributes);
        }

        return response()->json([
            'success' => true,
            'message' => 'Titik koordinat dan radius geofence berhasil diperbarui.',
            'lokasi' => [
                'id' => $lokasi->id,
                'nama_lokasi' => $lokasi->nama_lokasi,
                'latitude' => $lokasi->latitude,
                'longitude' => $lokasi->longitude,
                'radius_meters' => $lokasi->radius_meters,
                'qr_token' => $lokasi->qr_token,
                'allowed_ips' => $lokasi->allowed_ips ?? [],
            ],
        ]);
    }

    /**
     * Validate a scanned QR token and the browser GPS reading, then store the attendance.
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['required', 'numeric', 'min:0'],
        ]);

        $lokasi = $this->activeLokasi();
        $now = now(config('attendance.timezone'));
        $accuracy = (float) $validated['accuracy'];
        $distance = $this->haversineDistanceMeters(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            $lokasi->latitude,
            $lokasi->longitude,
        );

        $result = [
            'accuracy' => round($accuracy, 1),
            'distance' => round($distance, 1),
            'max_accuracy' => config('attendance.max_accuracy_meters'),
            'radius' => $lokasi->radius_meters,
            'ip' => $request->ip(),
            'server_time' => $now->format('H:i:s'),
        ];

        if (! hash_equals($lokasi->qr_token, $validated['qr_token'])) {
            return $this->reject($result, 1, 'Token QR Tidak Valid', 'QR yang dipindai bukan QR resmi lokasi kantor.');
        }

        if ($accuracy > config('attendance.max_accuracy_meters')) {
            return $this->reject($result, 5, 'Sinyal GPS Lemah', 'Akurasi GPS terlalu rendah. Pindah ke area terbuka lalu coba lagi.');
        }

        if ($distance > $lokasi->radius_meters) {
            return $this->reject($result, 6, 'Di Luar Radius Kantor', 'Pastikan Anda berada di area gedung kantor dan mengaktifkan akurasi lokasi tinggi.');
        }

        $nik = $this->employeeNik($request);
        $presensi = Presensi::query()
            ->where('nik', $nik)
            ->whereDate('attendance_date', $now->toDateString())
            ->first();

        $location = [
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'accuracy' => round($accuracy, 1),
            'distance' => round($distance, 1),
            'ip' => $request->ip(),
        ];

        if ($presensi === null) {
            $presensi = $this->recordCheckIn($nik, $validated['qr_token'], $now, $location);
            $type = 'masuk';
        } elseif ($presensi->pulang_at !== null) {
            return $this->reject($result, 7, 'Presensi Hari Ini Lengkap', 'Presensi masuk dan pulang hari ini sudah tercatat.');
        } elseif ($now->lt($now->copy()->setTimeFromTimeString(config('attendance.checkout_opens_at')))) {
            return $this->reject($result, 7, 'Sesi Pulang Belum Dibuka', 'Presensi pulang dibuka pukul '.config('attendance.checkout_opens_at').' WIB.');
        } else {
            $presensi->update([
                'pulang_at' => $now->copy()->utc(),
                'pulang_latitude' => $location['latitude'],
                'pulang_longitude' => $location['longitude'],
                'pulang_accuracy' => $location['accuracy'],
                'pulang_distance' => $location['distance'],
                'pulang_ip' => $location['ip'],
            ]);
            $type = 'pulang';
        }

        return response()->json($result + [
            'success' => true,
            'type' => $type,
            'type_label' => $type === 'masuk' ? 'Presensi Masuk Tercatat' : 'Presensi Pulang Tercatat',
            'status' => $presensi->status,
            'late_minutes' => $presensi->late_minutes,
        ]);
    }

    /**
     * @param  array{latitude: mixed, longitude: mixed, accuracy: float, distance: float, ip: string|null}  $location
     */
    private function recordCheckIn(string $nik, string $qrToken, CarbonInterface $now, array $location): Presensi
    {
        $isLate = $now->gt($now->copy()->setTimeFromTimeString(config('attendance.late_tolerance')));
        $lateMinutes = $isLate
            ? (int) $now->copy()->setTimeFromTimeString(config('attendance.shift_start'))->diffInMinutes($now)
            : 0;

        $userId = User::query()->where('nik', $nik)->value('id');
        $lokasiId = Lokasi::query()->where('qr_token', $qrToken)->value('id');

        return Presensi::create([
            'user_id' => $userId,
            'lokasi_id' => $lokasiId,
            'nik' => $nik,
            'attendance_date' => $now->toDateString(),
            'status' => $isLate ? Presensi::STATUS_LATE : Presensi::STATUS_ON_TIME,
            'late_minutes' => $lateMinutes,
            'masuk_at' => $now->copy()->utc(),
            'masuk_latitude' => $location['latitude'],
            'masuk_longitude' => $location['longitude'],
            'masuk_accuracy' => $location['accuracy'],
            'masuk_distance' => $location['distance'],
            'masuk_ip' => $location['ip'],
        ]);
    }

    private function employeeNik(Request $request): string
    {
        $nik = $request->session()->get('auth_user.nik');

        abort_unless(is_string($nik) && $nik !== '', 403, 'Sesi karyawan tidak valid.');

        return $nik;
    }

    /**
     * Number of Monday–Friday days in the month of the given date.
     */
    private function countWorkingDays(CarbonInterface $date): int
    {
        $count = 0;

        for ($day = $date->copy()->startOfMonth(); $day->lte($date->copy()->endOfMonth()); $day->addDay()) {
            if ($day->isWeekday()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Great-circle distance between two coordinates using the Haversine formula.
     */
    private function haversineDistanceMeters(float $latFrom, float $lngFrom, float $latTo, float $lngTo): float
    {
        $deltaLat = deg2rad($latTo - $latFrom);
        $deltaLng = deg2rad($lngTo - $lngFrom);

        $a = sin($deltaLat / 2) ** 2
            + cos(deg2rad($latFrom)) * cos(deg2rad($latTo)) * sin($deltaLng / 2) ** 2;

        return self::EARTH_RADIUS_METERS * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function activeLokasi(): Lokasi
    {
        return Lokasi::query()->first() ?? new Lokasi([
            'nama_lokasi' => 'Kantor Pusat Jakarta',
            'latitude' => (float) config('attendance.office_latitude'),
            'longitude' => (float) config('attendance.office_longitude'),
            'radius_meters' => (int) config('attendance.radius_meters'),
            'qr_token' => (string) config('attendance.qr_token'),
            'allowed_ips' => ['103.14.88.19', '127.0.0.1'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function reject(array $result, int $layer, string $title, string $message): JsonResponse
    {
        return response()->json($result + [
            'success' => false,
            'layer' => $layer,
            'title' => $title,
            'message' => $message,
        ], 422);
    }
}

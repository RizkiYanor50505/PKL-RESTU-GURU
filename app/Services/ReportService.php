<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Asset;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Logika bisnis laporan kendala: pembuatan, penanganan, penghapusan,
 * serta sinkronisasi status aset terkait.
 */
class ReportService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?User $reporter = null): Report
    {
        return DB::transaction(function () use ($data, $reporter) {
            $asset = Asset::findOrFail($data['asset_id']);

            $photoPath = null;
            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
                $photoPath = $data['photo']->store('reports/photos', 'public');
            }

            $report = Report::create([
                'report_number' => Report::nextNumber(),
                'branch_id' => $asset->branch_id,
                'asset_id' => $asset->id,
                'user_id' => $reporter?->id,
                'reporter_name' => $data['reporter_name'],
                'issue_description' => $data['issue_description'],
                'photo_path' => $photoPath,
                'status' => ReportStatus::Menunggu,
                'reported_at' => now(),
            ]);

            $asset->syncStatus();

            return $report;
        });
    }

    /**
     * Perbarui penanganan oleh teknisi/admin dan jaga konsistensi linimasa.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateHandling(Report $report, array $data): Report
    {
        return DB::transaction(function () use ($report, $data) {
            $status = ReportStatus::from($data['status']);

            $report->fill([
                'status' => $status,
                'technician_id' => $data['technician_id'] ?? null,
                'repair_notes' => $data['repair_notes'] ?? null,
                'replaced_parts' => $data['replaced_parts'] ?? null,
                'repair_cost' => $data['repair_cost'] ?? null,
            ]);

            $this->syncTimeline($report, $status);

            $report->save();
            $report->asset->syncStatus();

            return $report;
        });
    }

    public function delete(Report $report): void
    {
        DB::transaction(function () use ($report) {
            $asset = $report->asset;

            if ($report->photo_path && Storage::disk('public')->exists($report->photo_path)) {
                Storage::disk('public')->delete($report->photo_path);
            }

            $report->delete();
            $asset->syncStatus();
        });
    }

    /**
     * Bersihkan file foto dokumentasi laporan yang telah berusia lebih dari $days hari (default 90 hari / 3 bulan).
     * Data laporan, histori kerusakan, dan rekap biaya tetap utuh, hanya file foto yang direset untuk hemat penyimpanan.
     *
     * @return array{deleted_count: int, freed_bytes: int}
     */
    public function pruneOldPhotos(int $days = 90): array
    {
        $cutoff = now()->subDays($days);
        $reports = Report::query()
            ->whereNotNull('photo_path')
            ->where('reported_at', '<=', $cutoff)
            ->get();

        $deletedCount = 0;
        $freedBytes = 0;
        $disk = Storage::disk('public');

        foreach ($reports as $report) {
            if ($report->photo_path && $disk->exists($report->photo_path)) {
                $freedBytes += $disk->size($report->photo_path);
                $disk->delete($report->photo_path);
            }

            $report->update([
                'photo_path' => null,
                'photo_cleaned_at' => now(),
            ]);

            $deletedCount++;
        }

        return [
            'deleted_count' => $deletedCount,
            'freed_bytes' => $freedBytes,
        ];
    }

    /**
     * Atur processed_at / resolved_at sesuai status baru.
     */
    private function syncTimeline(Report $report, ReportStatus $status): void
    {
        match ($status) {
            ReportStatus::Menunggu => $report->fill([
                'processed_at' => null,
                'resolved_at' => null,
            ]),
            ReportStatus::Diproses => $report->fill([
                'processed_at' => $report->processed_at ?? now(),
                'resolved_at' => null,
            ]),
            ReportStatus::Selesai => $report->fill([
                'processed_at' => $report->processed_at ?? now(),
                'resolved_at' => $report->resolved_at ?? now(),
            ]),
            ReportStatus::Dibatalkan => $report->fill([
                'resolved_at' => null,
            ]),
        };
    }
}

<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Logika bisnis laporan kendala: pembuatan, penanganan, penghapusan,
 * serta sinkronisasi status aset terkait.
 */
class ReportService
{
    /**
     * @param  array{asset_id: int|string, reporter_name: string, issue_description: string}  $data
     */
    public function create(array $data, ?User $reporter = null): Report
    {
        return DB::transaction(function () use ($data, $reporter) {
            $asset = \App\Models\Asset::findOrFail($data['asset_id']);

            $report = Report::create([
                'report_number' => Report::nextNumber(),
                'branch_id' => $asset->branch_id,
                'asset_id' => $asset->id,
                'user_id' => $reporter?->id,
                'reporter_name' => $data['reporter_name'],
                'issue_description' => $data['issue_description'],
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

            $report->delete();
            $asset->syncStatus();
        });
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

<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reports:prune-photos {--days=90 : Batas usia foto dalam hari sebelum dibersihkan (default: 90 hari / 3 bulan)}')]
#[Description('Reset dan bersihkan file foto dokumentasi laporan yang lebih dari 3 bulan (90 hari) untuk menghemat penyimpanan.')]
class PruneReportPhotos extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ReportService $reportService): int
    {
        $days = (int) ($this->option('days') ?: 90);
        $this->info("Memulai pembersihan foto dokumentasi laporan yang berusia > {$days} hari...");

        $result = $reportService->pruneOldPhotos($days);
        $freedMb = round($result['freed_bytes'] / 1024 / 1024, 2);

        $this->info("Selesai! {$result['deleted_count']} foto lama berhasil dibersihkan.");
        $this->info("Ruang penyimpanan yang dihemat: {$freedMb} MB.");

        return self::SUCCESS;
    }
}

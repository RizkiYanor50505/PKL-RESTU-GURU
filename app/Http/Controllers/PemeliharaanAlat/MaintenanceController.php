<?php

namespace App\Http\Controllers\PemeliharaanAlat;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $branches = Branch::orderBy('name')->get();

        $query = $this->query($filters);

        $totalCost = (float) (clone $query)->sum('repair_cost');
        $totalServices = (clone $query)->count();

        // Rekap per cabang sesuai filter tanggal & aset yang aktif
        $branchSummaries = $branches->map(function (Branch $b) use ($filters) {
            $q = Report::completed()->where('branch_id', $b->id);

            if (! empty($filters['date_from'])) {
                $q->whereDate('resolved_at', '>=', $filters['date_from']);
            }
            if (! empty($filters['date_to'])) {
                $q->whereDate('resolved_at', '<=', $filters['date_to']);
            }
            if (! empty($filters['asset_id'])) {
                $q->where('asset_id', $filters['asset_id']);
            }

            return [
                'branch' => $b,
                'cost' => (float) (clone $q)->sum('repair_cost'),
                'count' => (clone $q)->count(),
            ];
        });

        $records = $query
            ->with(['branch', 'asset.branch', 'technician'])
            ->orderByDesc('resolved_at')
            ->paginate(10)
            ->withQueryString();

        return view('pemeliharaanalat.maintenance.index', [
            'records' => $records,
            'assets' => Asset::with('branch')->orderBy('name')->get(['id', 'code', 'name', 'branch_id']),
            'branches' => $branches,
            'branchSummaries' => $branchSummaries,
            'filters' => $filters,
            'totalCost' => $totalCost,
            'totalServices' => $totalServices,
        ]);
    }

    /** Ekspor CSV (dibuka langsung di Excel) sesuai filter yang aktif. */
    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);

        $records = $this->query($filters)
            ->with(['branch', 'asset.branch', 'technician'])
            ->orderByDesc('resolved_at')
            ->get();

        $filename = 'riwayat-pemeliharaan-'.Date::now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($records) {
            $out = fopen('php://output', 'w');

            // BOM agar Excel membaca UTF-8; pemisah titik koma sesuai pengaturan regional Indonesia.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['No. Laporan', 'Cabang', 'Tanggal Servis', 'Kode Aset', 'Nama Mesin', 'Kendala', 'Tindakan', 'Suku Cadang', 'Teknisi', 'Biaya (Rp)'], ';');

            foreach ($records as $report) {
                fputcsv($out, [
                    $report->report_number,
                    $report->branch?->name ?? $report->asset->branch?->name ?? '—',
                    $report->resolved_at?->format('d/m/Y'),
                    $report->asset->code,
                    $report->asset->name,
                    $report->issue_description,
                    $report->repair_notes,
                    $report->replaced_parts,
                    $report->technician?->name,
                    (int) $report->repair_cost,
                ], ';');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array{date_from: ?string, date_to: ?string, asset_id: ?int, branch_id: ?int}
     */
    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'asset_id' => ['nullable', 'integer'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        return [
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'asset_id' => $validated['asset_id'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
        ];
    }

    /**
     * Riwayat pemeliharaan = laporan kendala yang sudah berstatus "selesai".
     */
    private function query(array $filters): Builder
    {
        return Report::query()
            ->completed()
            ->when($filters['branch_id'], fn (Builder $q, $b) => $q->where('branch_id', $b))
            ->when($filters['date_from'], fn (Builder $q, $date) => $q->whereDate('resolved_at', '>=', $date))
            ->when($filters['date_to'], fn (Builder $q, $date) => $q->whereDate('resolved_at', '<=', $date))
            ->when($filters['asset_id'], fn (Builder $q, $id) => $q->where('asset_id', $id));
    }
}

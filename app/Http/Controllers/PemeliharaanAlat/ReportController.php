<?php

namespace App\Http\Controllers\PemeliharaanAlat;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $status = $request->query('status');
        $status = in_array($status, ReportStatus::values(), true) ? $status : null;
        $mine = $request->boolean('mine');
        $branchId = $request->filled('branch_id') ? (int) $request->query('branch_id') : null;

        $baseQuery = Report::query()
            ->when($mine, fn ($q) => $q->where('user_id', $user->id))
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');

                $q->where(function ($q) use ($term) {
                    $q->where('report_number', 'like', "%{$term}%")
                        ->orWhere('reporter_name', 'like', "%{$term}%")
                        ->orWhere('issue_description', 'like', "%{$term}%")
                        ->orWhereHas('asset', fn ($a) => $a->where('name', 'like', "%{$term}%")->orWhere('code', 'like', "%{$term}%"));
                });
            });

        // Jumlah per status untuk tab filter (mengikuti pencarian & filter cabang, bukan filter status).
        $counts = (clone $baseQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $reports = $baseQuery
            ->with(['branch', 'asset.branch', 'technician'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('reported_at')
            ->paginate(10)
            ->withQueryString();

        return view('pemeliharaanalat.reports.index', [
            'reports' => $reports,
            'counts' => $counts,
            'currentStatus' => $status,
            'mine' => $mine,
            'branches' => Branch::orderBy('name')->get(['id', 'name', 'code', 'city']),
            'selectedBranchId' => $branchId,
        ]);
    }

    public function create(Request $request): View
    {
        return view('pemeliharaanalat.reports.create', [
            'assets' => Asset::with('branch')->orderBy('location')->orderBy('name')->get(),
            'branches' => Branch::with(['assets' => fn ($q) => $q->orderBy('name')])->orderBy('name')->get(),
            'selectedAsset' => $request->integer('asset_id') ?: null,
        ]);
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $report = $this->reports->create($request->validated(), $request->user());

        return redirect()->route('pemeliharaanalat.reports.show', $report)
            ->with('success', "Laporan {$report->report_number} berhasil dikirim. Tim teknisi akan segera menindaklanjuti.");
    }

    public function show(Report $report): View
    {
        $report->load(['branch', 'asset.branch', 'reporter', 'technician']);

        return view('pemeliharaanalat.reports.show', [
            'report' => $report,
            'technicians' => User::query()->orderBy('name')->get(),
            'statuses' => ReportStatus::cases(),
        ]);
    }

    public function update(UpdateReportRequest $request, Report $report): RedirectResponse
    {
        $this->reports->updateHandling($report, $request->validated());

        return redirect()->route('pemeliharaanalat.reports.show', $report)
            ->with('success', "Penanganan laporan {$report->report_number} berhasil diperbarui.");
    }

    public function destroy(Report $report): RedirectResponse
    {
        $number = $report->report_number;

        $this->reports->delete($report);

        return redirect()->route('pemeliharaanalat.reports.index')
            ->with('success', "Laporan {$number} berhasil dihapus.");
    }

    /** Lembar SPK / Laporan Servis format A4 siap cetak. */
    public function print(Report $report): View
    {
        $report->load(['branch', 'asset.branch', 'reporter', 'technician']);

        return view('pemeliharaanalat.reports.print', compact('report'));
    }
}

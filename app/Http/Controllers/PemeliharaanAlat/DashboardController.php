<?php

namespace App\Http\Controllers\PemeliharaanAlat;

use App\Enums\AssetStatus;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $selectedBranchId = $request->filled('branch_id') ? (int) $request->query('branch_id') : null;
        $branches = Branch::orderBy('name')->get();
        $selectedBranch = $selectedBranchId ? $branches->firstWhere('id', $selectedBranchId) : null;

        // Query dasar aset dengan filter cabang jika dipilih
        $assetQuery = Asset::query()
            ->when($selectedBranchId, fn ($q) => $q->where('branch_id', $selectedBranchId));

        $assetCounts = (clone $assetQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $normal = (int) ($assetCounts[AssetStatus::Normal->value] ?? 0);
        $totalAssets = (int) $assetCounts->sum();

        $stats = [
            'total_assets' => $totalAssets,
            'normal_assets' => $normal,
            'problem_assets' => $totalAssets - $normal,
            'waiting_reports' => Report::where('status', ReportStatus::Menunggu->value)
                ->when($selectedBranchId, fn ($q) => $q->where('branch_id', $selectedBranchId))
                ->count(),
            'completed_reports' => Report::where('status', ReportStatus::Selesai->value)
                ->when($selectedBranchId, fn ($q) => $q->where('branch_id', $selectedBranchId))
                ->count(),
        ];

        $activeReports = Report::query()
            ->with(['asset.branch', 'technician'])
            ->active()
            ->when($selectedBranchId, fn ($q) => $q->where('branch_id', $selectedBranchId))
            ->latest('reported_at')
            ->limit(8)
            ->get();

        // Rekap biaya perbaikan bulan ini (Admin & Teknisi)
        $monthlyCost = null;
        $monthlyServices = null;
        $branchBreakdowns = collect();
        $grandTotalMonthlyCost = 0;
        $grandTotalMonthlyServices = 0;

        $canHandleReports = ($user?->canHandleReports() ?? false) || ($request->session()->get('auth_role') === 'manajer');

        if ($canHandleReports) {
            if ($selectedBranchId) {
                // Biaya untuk cabang terpilih
                $completedThisMonth = Report::completed()
                    ->where('branch_id', $selectedBranchId)
                    ->whereBetween('resolved_at', [now()->startOfMonth(), now()->endOfMonth()]);

                $monthlyCost = (float) (clone $completedThisMonth)->sum('repair_cost');
                $monthlyServices = (clone $completedThisMonth)->count();
            } else {
                // Rincian per cabang + Grand Total gabungan seluruh cabang
                $branchBreakdowns = $branches->map(function (Branch $b) {
                    $completedThisMonth = Report::completed()
                        ->where('branch_id', $b->id)
                        ->whereBetween('resolved_at', [now()->startOfMonth(), now()->endOfMonth()]);

                    $cost = (float) (clone $completedThisMonth)->sum('repair_cost');
                    $servicesCount = (clone $completedThisMonth)->count();
                    $activeReportsCount = Report::active()->where('branch_id', $b->id)->count();
                    $assetsCount = Asset::where('branch_id', $b->id)->count();

                    return [
                        'branch' => $b,
                        'assets_count' => $assetsCount,
                        'active_reports' => $activeReportsCount,
                        'services_count' => $servicesCount,
                        'monthly_cost' => $cost,
                    ];
                });

                $grandTotalMonthlyCost = (float) $branchBreakdowns->sum('monthly_cost');
                $grandTotalMonthlyServices = (int) $branchBreakdowns->sum('services_count');

                $monthlyCost = $grandTotalMonthlyCost;
                $monthlyServices = $grandTotalMonthlyServices;
            }
        }

        return view('pemeliharaanalat.dashboard', compact(
            'stats',
            'activeReports',
            'monthlyCost',
            'monthlyServices',
            'branches',
            'selectedBranch',
            'selectedBranchId',
            'branchBreakdowns',
            'grandTotalMonthlyCost',
            'grandTotalMonthlyServices'
        ));
    }
}

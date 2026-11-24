<?php

namespace App\Http\Controllers\PemeliharaanAlat;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::query()
            ->search($request->query('q'))
            ->withCount('assets')
            ->orderBy('name')
            ->get();

        // Rekap biaya per cabang bulan ini & total keseluruhan
        $branchStats = $branches->map(function (Branch $b) {
            $monthlyCost = $b->monthlyCost();
            $monthlyServices = $b->monthlyServicesCount();
            $activeReports = $b->activeReportsCount();
            $totalCost = $b->totalCost();

            return [
                'branch' => $b,
                'assets_count' => $b->assets_count,
                'active_reports' => $activeReports,
                'monthly_services' => $monthlyServices,
                'monthly_cost' => $monthlyCost,
                'total_cost' => $totalCost,
            ];
        });

        $grandTotalMonthlyCost = (float) $branchStats->sum('monthly_cost');
        $grandTotalMonthlyServices = (int) $branchStats->sum('monthly_services');
        $grandTotalAllTimeCost = (float) $branchStats->sum('total_cost');
        $grandTotalAssets = (int) $branchStats->sum('assets_count');
        $grandTotalActiveReports = (int) $branchStats->sum('active_reports');

        return view('pemeliharaanalat.branches.index', compact(
            'branches',
            'branchStats',
            'grandTotalMonthlyCost',
            'grandTotalMonthlyServices',
            'grandTotalAllTimeCost',
            'grandTotalAssets',
            'grandTotalActiveReports'
        ));
    }

    public function create(): View
    {
        return view('pemeliharaanalat.branches.create', ['branch' => new Branch]);
    }

    public function store(BranchRequest $request): RedirectResponse
    {
        $branch = Branch::create($request->validated());

        return redirect()->route('pemeliharaanalat.branches.show', $branch)
            ->with('success', "Cabang {$branch->name} berhasil ditambahkan.");
    }

    public function show(Branch $branch): View
    {
        $assets = $branch->assets()
            ->withCount('reports')
            ->orderBy('name')
            ->get();

        $recentReports = $branch->reports()
            ->with(['asset', 'technician'])
            ->latest('reported_at')
            ->limit(10)
            ->get();

        $monthlyCost = $branch->monthlyCost();
        $monthlyServices = $branch->monthlyServicesCount();
        $totalCost = $branch->totalCost();
        $activeReports = $branch->activeReportsCount();

        return view('pemeliharaanalat.branches.show', compact(
            'branch',
            'assets',
            'recentReports',
            'monthlyCost',
            'monthlyServices',
            'totalCost',
            'activeReports'
        ));
    }

    public function edit(Branch $branch): View
    {
        return view('pemeliharaanalat.branches.edit', compact('branch'));
    }

    public function update(BranchRequest $request, Branch $branch): RedirectResponse
    {
        $branch->update($request->validated());

        return redirect()->route('pemeliharaanalat.branches.show', $branch)
            ->with('success', "Data cabang {$branch->name} berhasil diperbarui.");
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $name = $branch->name;

        \Illuminate\Support\Facades\DB::transaction(function () use ($branch) {
            $branch->users()->update(['branch_id' => null]);
            $branch->reports()->delete();
            $branch->assets()->delete();
            $branch->delete();
        });

        return redirect()->route('pemeliharaanalat.branches.index')
            ->with('success', "Cabang {$name} berhasil dihapus.");
    }
}

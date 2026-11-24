<?php

namespace App\Http\Controllers\PemeliharaanAlat;

use App\Enums\AssetStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $assets = Asset::query()
            ->with(['branch'])
            ->withCount('reports')
            ->search($request->query('q'))
            ->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->query('branch_id')))
            ->when($request->filled('location'), fn ($q) => $q->where('location', $request->query('location')))
            ->when(
                in_array($request->query('status'), AssetStatus::values(), true),
                fn ($q) => $q->where('status', $request->query('status'))
            )
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('pemeliharaanalat.assets.index', [
            'assets' => $assets,
            'branches' => Branch::orderBy('name')->get(['id', 'name', 'code', 'city']),
            'locations' => Asset::query()->distinct()->orderBy('location')->pluck('location'),
            'statuses' => AssetStatus::options(),
        ]);
    }

    public function create(): View
    {
        return view('pemeliharaanalat.assets.create', [
            'asset' => new Asset,
            'branches' => Branch::orderBy('name')->get(['id', 'name', 'code', 'city']),
        ]);
    }

    public function store(AssetRequest $request): RedirectResponse
    {
        $asset = Asset::create($request->validated());

        return redirect()->route('pemeliharaanalat.assets.show', $asset)
            ->with('success', "Aset {$asset->name} berhasil ditambahkan di {$asset->branch->name}.");
    }

    public function show(Asset $asset): View
    {
        $asset->load('branch');

        $reports = $asset->reports()
            ->with('technician')
            ->latest('reported_at')
            ->limit(10)
            ->get();

        $totalCost = (float) $asset->reports()->completed()->sum('repair_cost');

        return view('pemeliharaanalat.assets.show', compact('asset', 'reports', 'totalCost'));
    }

    public function edit(Asset $asset): View
    {
        return view('pemeliharaanalat.assets.edit', [
            'asset' => $asset,
            'branches' => Branch::orderBy('name')->get(['id', 'name', 'code', 'city']),
        ]);
    }

    public function update(AssetRequest $request, Asset $asset): RedirectResponse
    {
        $asset->update($request->validated());

        return redirect()->route('pemeliharaanalat.assets.show', $asset)
            ->with('success', "Data aset {$asset->name} berhasil diperbarui.");
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        if ($asset->reports()->exists()) {
            return back()->with('error', "Aset {$asset->name} tidak dapat dihapus karena sudah memiliki riwayat laporan kendala.");
        }

        $asset->delete();

        return redirect()->route('pemeliharaanalat.assets.index')
            ->with('success', "Aset {$asset->name} berhasil dihapus.");
    }
}

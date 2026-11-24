@extends('pemeliharaanalat.layouts.app')

@section('title', 'Riwayat Pemeliharaan')

@section('content')
    <div class="page-head">
        <div>
            <h1>Riwayat Pemeliharaan &amp; Biaya</h1>
            <p>Histori perbaikan mesin yang telah selesai beserta rekap biaya servis.</p>
        </div>
        <a href="{{ route('pemeliharaanalat.maintenance.export', request()->query()) }}" class="btn btn-gold" id="btn-export">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor CSV
        </a>
    </div>

    <div class="row g-3 mb-4">
        @foreach($branchSummaries as $summary)
            <div class="col-sm-6 col-lg-4">
                <div class="metric tone-info">
                    <div class="icon"><i class="bi bi-buildings"></i></div>
                    <div class="value" style="font-size: 1.35rem">{{ rupiah($summary['cost']) }}</div>
                    <div class="label fw-semibold text-dark">{{ $summary['branch']->name }}</div>
                    <div class="small text-muted">{{ $summary['count'] }} kali servis selesai · {{ $summary['branch']->city }}</div>
                </div>
            </div>
        @endforeach
        <div class="col-12">
            <div class="p-3 shadow-sm" style="background: linear-gradient(135deg, var(--forest-900) 0%, var(--forest-800) 55%, var(--accent) 100%); color: #fff; border-radius: 12px; border: 1px solid rgba(214, 168, 75, .35);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="text-uppercase small opacity-75 fw-bold" style="letter-spacing: .05em">Grand Total Biaya Pemeliharaan (Gabungan Seluruh Cabang)</div>
                        <div class="h2 mb-0 fw-bold" style="color: #f7dfa5; text-shadow: 0 2px 6px rgba(0,0,0,.3);">{{ rupiah($totalCost) }}</div>
                    </div>
                    <div class="text-end">
                        <span class="bdg bdg-gold fs-6 px-3 py-2 fw-semibold">
                            <i class="bi bi-check-circle-fill me-1" style="color: var(--gold-dark);"></i> Total {{ $totalServices }} kali perbaikan dari {{ $branches->count() }} cabang
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="card-panel">
        <div class="panel-head">
            <form method="GET" class="row g-2 w-100 align-items-end" id="form-maintenance-filter">
                <div class="col-md-3">
                    <label class="form-label" for="branch_id">Cabang</label>
                    <select name="branch_id" id="branch_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Cabang (Gabungan)</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" @selected((int) $filters['branch_id'] === $b->id)>
                                {{ $b->name }} ({{ $b->city }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="date_from">Dari tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] }}" class="form-control @error('date_from') is-invalid @enderror">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="date_to">Sampai tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] }}" class="form-control @error('date_to') is-invalid @enderror">
                    @error('date_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="asset_id">Mesin</label>
                    <select name="asset_id" id="asset_id" class="form-select">
                        <option value="">Semua mesin</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" @selected((int) $filters['asset_id'] === $asset->id)>
                                {{ $asset->code }} — {{ $asset->name }} ({{ $asset->branch?->name ?? 'Cabang' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                    @if(array_filter($filters))
                        <a href="{{ route('pemeliharaanalat.maintenance.index') }}" class="btn btn-light border" title="Reset"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>

        @if($records->isEmpty())
            <div class="empty-state"><i class="bi bi-clipboard-check"></i>Belum ada riwayat servis pada filter ini.</div>
        @else
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Tanggal Servis</th>
                            <th>Cabang</th>
                            <th>Mesin</th>
                            <th>Komponen Diganti</th>
                            <th>Teknisi</th>
                            <th class="text-end">Biaya</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td class="text-nowrap">
                                    <div class="cell-title">{{ $record->resolved_at->translatedFormat('d M Y') }}</div>
                                    <div class="cell-sub mono">{{ $record->report_number }}</div>
                                </td>
                                <td>
                                    <span class="badge text-bg-light border text-dark fw-medium">
                                        <i class="bi bi-buildings text-primary me-1"></i>{{ $record->branch?->name ?? $record->asset->branch?->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $record->asset->name }}</div>
                                    <div class="cell-sub">{{ $record->asset->location }}</div>
                                </td>
                                <td style="max-width:260px">
                                    @forelse($record->partsList() as $part)
                                        <span class="bdg bdg-secondary mb-1">{{ $part }}</span>
                                    @empty
                                        <span class="text-muted">—</span>
                                    @endforelse
                                </td>
                                <td>{{ $record->technician?->name ?? '—' }}</td>
                                <td class="text-end fw-semibold text-nowrap">{{ $record->repair_cost !== null ? rupiah($record->repair_cost) : '—' }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('pemeliharaanalat.reports.show', $record) }}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('pemeliharaanalat.reports.print', $record) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak SPK"><i class="bi bi-printer"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Grand Total Biaya (Seluruh Cabang)</td>
                            <td class="text-end fw-bold text-dark fs-6">{{ rupiah($totalCost) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($records->hasPages())
                <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">Menampilkan {{ $records->firstItem() }}–{{ $records->lastItem() }} dari {{ $records->total() }} servis</div>
                    {{ $records->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection

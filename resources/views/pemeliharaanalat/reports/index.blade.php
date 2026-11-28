@extends('pemeliharaanalat.layouts.app')

@section('title', 'Daftar Laporan')

@section('content')
    @php
        $tabs = [
            null => ['Semua', $counts->sum()],
            'menunggu' => ['Menunggu', $counts['menunggu'] ?? 0],
            'diproses' => ['Diproses', $counts['diproses'] ?? 0],
            'selesai' => ['Selesai', $counts['selesai'] ?? 0],
        ];
        // Tab "Dibatalkan" hanya muncul jika ada datanya, agar filter tetap sederhana.
        if (($counts['dibatalkan'] ?? 0) > 0 || $currentStatus === 'dibatalkan') {
            $tabs['dibatalkan'] = ['Dibatalkan', $counts['dibatalkan'] ?? 0];
        }
    @endphp

    <div class="page-head">
        <div>
            <h1>Daftar Laporan Kendala</h1>
        </div>
        <div>
            <a href="{{ route('pemeliharaanalat.reports.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Lapor Kendala</a>
        </div>
    </div>

    <section class="card-panel">
        <div class="panel-head">
            <div class="pill-tabs">
                @foreach($tabs as $key => [$label, $total])
                    <a href="{{ route('pemeliharaanalat.reports.index', array_filter(['status' => $key, 'q' => request('q'), 'mine' => $mine ? 1 : null, 'branch_id' => $selectedBranchId])) }}"
                       class="pill-tab {{ (string) $currentStatus === (string) $key ? 'active' : '' }}"
                       id="tab-{{ $key ?: 'semua' }}">
                        {{ $label }} <span class="n">{{ $total }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" class="d-flex gap-2 flex-wrap align-items-center" id="form-filter">
                @if($currentStatus)<input type="hidden" name="status" value="{{ $currentStatus }}">@endif
                <div class="dropdown">
                    <button class="btn btn-filter-dropdown dropdown-toggle" type="button" id="reportsBranchDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-buildings text-teal"></i>
                        <span>{{ $selectedBranchId && ($sb = $branches->firstWhere('id', $selectedBranchId)) ? $sb->name : 'Semua Cabang' }}</span>
                    </button>
                    <ul class="dropdown-menu shadow" aria-labelledby="reportsBranchDropdown" style="min-width: 220px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between {{ !$selectedBranchId ? 'active' : '' }}"
                               href="{{ route('pemeliharaanalat.reports.index', array_filter(['status' => $currentStatus, 'q' => request('q'), 'mine' => $mine ? 1 : null])) }}">
                                <span>Semua Cabang</span>
                                @if(!$selectedBranchId)<i class="bi bi-check2"></i>@endif
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1" style="border-color: var(--sage-border);"></li>
                        @foreach($branches as $b)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ (string) $selectedBranchId === (string) $b->id ? 'active' : '' }}"
                                   href="{{ route('pemeliharaanalat.reports.index', array_filter(['status' => $currentStatus, 'q' => request('q'), 'mine' => $mine ? 1 : null, 'branch_id' => $b->id])) }}">
                                    <span>{{ $b->name }}</span>
                                    @if((string) $selectedBranchId === (string) $b->id)<i class="bi bi-check2"></i>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="mine" name="mine" value="1"
                           @checked($mine) onchange="this.form.submit()">
                    <label class="form-check-label small" for="mine">Laporan saya</label>
                </div>
                <div class="input-group input-group-sm" style="width:230px">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari no. laporan / mesin...">
                    <button class="btn btn-primary" type="submit" aria-label="Cari"><i class="bi bi-search"></i></button>
                </div>
                @if(request()->hasAny(['branch_id', 'q', 'mine']))
                    <a href="{{ route('pemeliharaanalat.reports.index', array_filter(['status' => $currentStatus])) }}" class="btn btn-sm btn-light border" title="Reset filter"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>

        @if($reports->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                Tidak ada laporan yang cocok dengan filter ini.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>No. Laporan</th>
                            <th>Mesin / Cabang</th>
                            <th>Kendala</th>
                            <th>Status</th>
                            <th>Teknisi</th>
                            <th>Dilaporkan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td class="mono fw-semibold text-nowrap">{{ $report->report_number }}</td>
                                <td>
                                    <div class="cell-title">{{ $report->asset->name }}</div>
                                    <div class="cell-sub d-flex align-items-center gap-1 mt-1">
                                        <span class="badge text-bg-light border text-dark">
                                            <i class="bi bi-buildings text-primary me-1"></i>{{ $report->branch?->name ?? $report->asset->branch?->name ?? '—' }}
                                        </span>
                                        <span>• {{ $report->asset->location }}</span>
                                    </div>
                                </td>
                                <td style="max-width:280px">
                                    {{ \Illuminate\Support\Str::limit($report->issue_description, 70) }}
                                    <div class="cell-sub">oleh {{ $report->reporter_name }}</div>
                                </td>
                                <td><x-badge :enum="$report->status" /></td>
                                <td>{{ $report->technician?->name ?? '—' }}</td>
                                <td class="text-nowrap">
                                    {{ $report->reported_at->translatedFormat('d M Y') }}
                                    <div class="cell-sub">{{ $report->reported_at->format('H:i') }} WITA</div>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('pemeliharaanalat.reports.show', $report) }}" class="btn btn-sm btn-detail" title="Detail">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    @if(auth()->user()?->isAdmin())
                                        <form action="{{ route('pemeliharaanalat.reports.destroy', $report) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus laporan {{ $report->report_number }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan</div>
                    {{ $reports->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection

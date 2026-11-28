@extends('pemeliharaanalat.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-head">
        <div>
            <h1>Halo, {{ explode(' ', (string) (auth()->user()?->name ?? session('auth_user.name', 'Pengguna')))[0] }} 👋</h1>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Filter Pilih Cabang (Dropdown Elegan Sesuai Tema) --}}
            <div class="dropdown">
                <button class="btn btn-filter-dropdown dropdown-toggle" type="button" id="branchDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-buildings text-teal"></i>
                    <span>{{ $selectedBranch ? ($selectedBranch->name . ' (' . $selectedBranch->city . ')') : 'Semua Cabang (Konsolidasi)' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="branchDropdown" style="min-width: 270px;">
                    <li class="dropdown-header text-uppercase small fw-bold px-3 py-1" style="color: var(--teal); letter-spacing: .05em;">Pilih Cabang</li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ !$selectedBranchId ? 'active' : '' }}"
                           href="{{ route('pemeliharaanalat.dashboard') }}">
                            <span>Semua Cabang (Konsolidasi)</span>
                            @if(!$selectedBranchId)<i class="bi bi-check2"></i>@endif
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1" style="border-color: var(--sage-border);"></li>
                    @foreach($branches as $b)
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between {{ $selectedBranchId === $b->id ? 'active' : '' }}"
                               href="{{ route('pemeliharaanalat.dashboard', ['branch_id' => $b->id]) }}">
                                <span>{{ $b->name }} <span class="small opacity-75">({{ $b->city }})</span></span>
                                @if($selectedBranchId === $b->id)<i class="bi bi-check2"></i>@endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if($selectedBranchId)
                <a href="{{ route('pemeliharaanalat.dashboard') }}" class="btn btn-sm btn-outline-secondary" title="Kembali ke Semua Cabang">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif

            <a href="{{ route('pemeliharaanalat.reports.create') }}" class="btn btn-primary" id="btn-new-report">
                <i class="bi bi-plus-lg me-1"></i> Lapor Kendala
            </a>
        </div>
    </div>

    {{-- Kartu metrik --}}
    <div class="row g-3 row-cols-2 row-cols-lg-3 row-cols-xxl-5 mb-4">
        <div class="col">
            <div class="metric tone-primary">
                <div class="icon"><i class="bi bi-hdd-rack"></i></div>
                <div class="value">{{ $stats['total_assets'] }}</div>
                <div class="label">{{ $selectedBranch ? 'Alat Cabang' : 'Total Aset' }}</div>
            </div>
        </div>
        <div class="col">
            <div class="metric tone-success">
                <div class="icon"><i class="bi bi-check-circle"></i></div>
                <div class="value">{{ $stats['normal_assets'] }}</div>
                <div class="label">Beroperasi Normal</div>
            </div>
        </div>
        <div class="col">
            <div class="metric tone-danger">
                <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="value">{{ $stats['problem_assets'] }}</div>
                <div class="label">Sedang Diperbaiki / Kendala</div>
            </div>
        </div>
        <div class="col">
            <div class="metric tone-warning">
                <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="value">{{ $stats['waiting_reports'] }}</div>
                <div class="label">Laporan Menunggu</div>
            </div>
        </div>
        <div class="col">
            <div class="metric tone-info">
                <div class="icon"><i class="bi bi-check2-circle"></i></div>
                <div class="value">{{ $stats['completed_reports'] }}</div>
                <div class="label">Laporan Selesai</div>
            </div>
        </div>
    </div>

    {{-- Rekap Biaya Per Cabang Bulan Ini (Jika Mode Semua Cabang) --}}
    @if(!$selectedBranchId && (auth()->user()?->canHandleReports() ?? false) && $branchBreakdowns->isNotEmpty())
        <section class="card-panel mb-4">
            <div class="panel-head">
                <div>
                    <h2><i class="bi bi-diagram-3-fill text-primary me-2"></i>Rekap Biaya Perbaikan Per Cabang (Bulan Ini)</h2>
                </div>
                <a href="{{ route('pemeliharaanalat.branches.index') }}" class="btn btn-sm btn-outline-primary">Kelola Data Cabang</a>
            </div>

            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Cabang</th>
                            <th>Kota &amp; Alamat</th>
                            <th class="text-center">Alat / Mesin</th>
                            <th class="text-center">Kendala Aktif</th>
                            <th class="text-center">Servis Selesai Bulan Ini</th>
                            <th class="text-end">Biaya Bulan Ini</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branchBreakdowns as $row)
                            @php $b = $row['branch']; @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="bdg bdg-primary mono">{{ $b->code }}</span>
                                        <a href="{{ route('pemeliharaanalat.branches.show', $b) }}" class="cell-title text-decoration-none">
                                            {{ $b->name }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $b->city }}</div>
                                    <div class="cell-sub">{{ \Illuminate\Support\Str::limit($b->address ?? '—', 35) }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $row['assets_count'] }} unit</span>
                                </td>
                                <td class="text-center">
                                    @if($row['active_reports'] > 0)
                                        <span class="bdg bdg-warning">{{ $row['active_reports'] }} kendala</span>
                                    @else
                                        <span class="bdg bdg-success">Aman</span>
                                    @endif
                                </td>
                                <td class="text-center fw-medium">{{ $row['services_count'] }} servis</td>
                                <td class="text-end fw-bold text-dark fs-6">{{ rupiah($row['monthly_cost']) }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('pemeliharaanalat.branches.show', $b) }}" class="btn btn-sm btn-detail py-1 px-3">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold text-uppercase" style="letter-spacing:.05em">
                                <i class="bi bi-calculator-fill text-primary me-1"></i> Total Keseluruhan (Semua Cabang Dijadikan 1)
                            </td>
                            <td class="text-center fw-bold">{{ $stats['total_assets'] }} unit</td>
                            <td class="text-center fw-bold">{{ $activeReports->count() }} kendala</td>
                            <td class="text-center fw-bold">{{ $grandTotalMonthlyServices }} servis</td>
                            <td class="text-end fw-extrabold text-dark fs-6">{{ rupiah($grandTotalMonthlyCost) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    @endif

    <div class="row g-4">
        @php
            $showCostBanner = (bool) (auth()->user()?->canHandleReports() || session('auth_role') === 'manajer');
        @endphp
        <div class="{{ $showCostBanner ? 'col-xl-8' : 'col-12' }}">
            <section class="card-panel">
                <div class="panel-head">
                    <h2><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Kendala Aktif Terkini {{ $selectedBranch ? '· ' . $selectedBranch->name : '' }}</h2>
                    <a href="{{ route('pemeliharaanalat.reports.index', array_filter(['branch_id' => $selectedBranchId])) }}" class="btn btn-sm btn-outline-primary">Lihat semua</a>
                </div>

                @if($activeReports->isEmpty())
                    <div class="empty-state">
                        <i class="bi bi-emoji-smile"></i>
                        Tidak ada kendala aktif{{ $selectedBranch ? ' di ' . $selectedBranch->name : '' }}. Semua mesin berjalan lancar!
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Laporan</th>
                                    <th>Mesin &amp; Cabang</th>
                                    <th>Status</th>
                                    <th>Dilaporkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeReports as $report)
                                    <tr>
                                        <td>
                                            <a href="{{ route('pemeliharaanalat.reports.show', $report) }}" class="cell-title text-decoration-none">{{ $report->report_number }}</a>
                                            <div class="cell-sub">{{ \Illuminate\Support\Str::limit($report->issue_description, 55) }}</div>
                                        </td>
                                        <td>
                                            <div class="cell-title">{{ $report->asset->name }}</div>
                                            <div class="cell-sub">
                                                <span class="bdg bdg-primary mono" style="font-size:.65rem;padding:.1rem .4rem;">{{ $report->asset->branch?->code }}</span>
                                                {{ $report->asset->branch?->name }} · {{ $report->asset->location }}
                                            </div>
                                        </td>
                                        <td><x-badge :enum="$report->status" /></td>
                                        <td>
                                            <div>{{ $report->reported_at->diffForHumans() }}</div>
                                            <div class="cell-sub">oleh {{ $report->reporter_name }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        @if($showCostBanner)
            <div class="col-xl-4">
                <div class="cost-banner">
                    <i class="bi bi-cash-stack deco"></i>
                    <div class="small text-uppercase fw-semibold" style="opacity:.75;letter-spacing:.08em">
                        {{ $selectedBranch ? 'Biaya ' . $selectedBranch->name : 'Total Biaya Seluruh Cabang' }} (Bulan Ini)
                    </div>
                    <div class="amount my-1" id="monthly-cost">{{ rupiah($monthlyCost) }}</div>
                    <div class="small" style="opacity:.8">
                        {{ $monthlyServices }} servis selesai · {{ now()->translatedFormat('F Y') }}
                        @if(!$selectedBranchId) ({{ $branches->count() }} cabang digabung) @endif
                    </div>
                    <a href="{{ route('pemeliharaanalat.maintenance.index', array_filter(['branch_id' => $selectedBranchId, 'date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->endOfMonth()->toDateString()])) }}"
                       class="btn btn-sm btn-gold mt-3 fw-semibold">
                        Lihat rincian servis <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

@extends('pemeliharaanalat.layouts.app')

@section('title', 'Detail Cabang - ' . $branch->name)

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.branches.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Cabang
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1 class="d-flex align-items-center gap-2 flex-wrap">
                <span class="mono bdg bdg-primary">{{ $branch->code }}</span>
                <span>{{ $branch->name }}</span>
            </h1>
            <p>{{ $branch->city }} · {{ $branch->address ?? 'Alamat belum diatur' }} · Telp: {{ $branch->phone ?? '—' }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('pemeliharaanalat.assets.create') }}?branch_id={{ $branch->id }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Alat di Cabang Ini
            </a>
            @if(auth()->user()?->isAdmin() || session('auth_role') === 'manajer')
                <a href="{{ route('pemeliharaanalat.branches.edit', $branch) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i> Edit Cabang
                </a>
                <form action="{{ route('pemeliharaanalat.branches.destroy', $branch) }}" method="POST"
                      onsubmit="return confirm('Hapus cabang {{ $branch->name }}? Data aset dan histori terkait akan dibersihkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Kartu Metrik Khusus Cabang Ini --}}
    <div class="row g-3 row-cols-2 row-cols-md-4 mb-4">
        <div class="col">
            <div class="metric tone-primary">
                <div class="icon"><i class="bi bi-printer"></i></div>
                <div class="value">{{ $assets->count() }}</div>
                <div class="label">Total Alat / Mesin di Cabang</div>
            </div>
        </div>
        <div class="col">
            <div class="metric tone-warning">
                <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="value">{{ $activeReports }}</div>
                <div class="label">Kendala Aktif Cabang</div>
            </div>
        </div>
        <div class="col">
            <div class="metric tone-success">
                <div class="icon"><i class="bi bi-cash-stack"></i></div>
                <div class="value fs-4">{{ rupiah($monthlyCost) }}</div>
                <div class="label">Biaya Perbaikan Bulan Ini ({{ $monthlyServices }} servis)</div>
            </div>
        </div>
        <div class="col">
            <div class="metric tone-info">
                <div class="icon"><i class="bi bi-wallet2"></i></div>
                <div class="value fs-4">{{ rupiah($totalCost) }}</div>
                <div class="label">Total Biaya Servis (Histori)</div>
            </div>
        </div>
    </div>

    {{-- Daftar Mesin / Alat di Cabang Ini --}}
    <section class="card-panel mb-4">
        <div class="panel-head">
            <h2><i class="bi bi-hdd-rack text-primary me-2"></i>Daftar Alat &amp; Mesin di {{ $branch->name }}</h2>
            <span class="badge rounded-pill bg-primary px-3 py-2">{{ $assets->count() }} unit mesin terdaftar</span>
        </div>

        @if($assets->isEmpty())
            <div class="empty-state">
                <i class="bi bi-printer"></i>
                Belum ada mesin atau alat yang didaftarkan untuk cabang ini.
                <div class="mt-3">
                    <a href="{{ route('pemeliharaanalat.assets.create') }}?branch_id={{ $branch->id }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Mesin Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th>Kode Aset</th>
                            <th>Nama Mesin / Alat</th>
                            <th>Penempatan (Lokasi)</th>
                            <th>Merek / Model</th>
                            <th>Status Kondisi</th>
                            <th class="text-center">Riwayat Laporan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                            <tr>
                                <td class="mono fw-semibold">{{ $asset->code }}</td>
                                <td>
                                    <a href="{{ route('pemeliharaanalat.assets.show', $asset) }}" class="cell-title text-decoration-none">
                                        {{ $asset->name }}
                                    </a>
                                    <div class="cell-sub">{{ $asset->subtitle }}</div>
                                </td>
                                <td>
                                    <i class="bi bi-geo-alt me-1 text-muted"></i>{{ $asset->location }}
                                </td>
                                <td>{{ $asset->brand ?? '—' }}</td>
                                <td><x-badge :enum="$asset->status" /></td>
                                <td class="text-center">{{ $asset->reports_count }} kali</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('pemeliharaanalat.reports.create', ['asset_id' => $asset->id]) }}" class="btn btn-sm btn-outline-primary" title="Lapor Kerusakan">
                                        <i class="bi bi-megaphone"></i> Lapor
                                    </a>
                                    <a href="{{ route('pemeliharaanalat.assets.show', $asset) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Laporan Kendala & Servis Terkini di Cabang Ini --}}
    <section class="card-panel">
        <div class="panel-head">
            <h2><i class="bi bi-clock-history text-primary me-2"></i>Histori Kendala &amp; Servis Terkini</h2>
            <a href="{{ route('pemeliharaanalat.reports.index', ['branch_id' => $branch->id]) }}" class="btn btn-sm btn-outline-primary">
                Lihat Semua Laporan Cabang Ini
            </a>
        </div>

        @if($recentReports->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shield-check"></i>
                Belum ada laporan kendala pada cabang ini.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>No. Laporan</th>
                            <th>Mesin / Alat</th>
                            <th>Uraian Kendala</th>
                            <th>Status</th>
                            <th>Teknisi</th>
                            <th class="text-end">Biaya Servis</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReports as $report)
                            <tr>
                                <td>
                                    <a href="{{ route('pemeliharaanalat.reports.show', $report) }}" class="mono fw-semibold text-decoration-none">
                                        {{ $report->report_number }}
                                    </a>
                                </td>
                                <td>{{ $report->asset->name }}</td>
                                <td style="max-width:280px">{{ \Illuminate\Support\Str::limit($report->issue_description, 60) }}</td>
                                <td><x-badge :enum="$report->status" /></td>
                                <td>{{ $report->technician?->name ?? '—' }}</td>
                                <td class="text-end fw-semibold">
                                    {{ $report->repair_cost !== null ? rupiah($report->repair_cost) : '—' }}
                                </td>
                                <td class="text-nowrap">{{ $report->reported_at->translatedFormat('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection

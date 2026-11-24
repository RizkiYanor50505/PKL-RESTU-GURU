@extends('pemeliharaanalat.layouts.app')

@section('title', 'Detail Aset')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.assets.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Aset
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1 class="d-flex align-items-center gap-2 flex-wrap">{{ $asset->name }} <x-badge :enum="$asset->status" /></h1>
            <p class="mono">{{ $asset->code }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('pemeliharaanalat.reports.create', ['asset_id' => $asset->id]) }}" class="btn btn-primary">
                <i class="bi bi-megaphone me-1"></i> Lapor Kendala
            </a>
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('pemeliharaanalat.assets.edit', $asset) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i> Edit</a>
                <form action="{{ route('pemeliharaanalat.assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Hapus aset {{ $asset->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <section class="card-panel">
                <div class="panel-head"><h2>Informasi Aset</h2></div>
                <div class="panel-body">
                    <dl class="detail-list">
                        <dt>Cabang</dt>
                        <dd>
                            @if($asset->branch)
                                <a href="{{ route('pemeliharaanalat.branches.show', $asset->branch) }}" class="fw-semibold text-decoration-none">
                                    <i class="bi bi-buildings text-primary me-1"></i>{{ $asset->branch->name }} ({{ $asset->branch->city }})
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>
                        <dt>Tipe</dt><dd>{{ $asset->subtitle ?? '—' }}</dd>
                        <dt>Lokasi</dt><dd>{{ $asset->location }}</dd>
                        <dt>Merek / Model</dt><dd>{{ $asset->brand ?? '—' }}</dd>
                        <dt>Status</dt><dd><x-badge :enum="$asset->status" /></dd>
                        <dt>Total Biaya Servis</dt><dd class="fw-bold">{{ rupiah($totalCost) }}</dd>
                        <dt>Keterangan</dt><dd style="white-space:pre-line">{{ $asset->description ?? '—' }}</dd>
                    </dl>
                </div>
            </section>
        </div>

        <div class="col-lg-7">
            <section class="card-panel">
                <div class="panel-head"><h2>Riwayat Laporan Terakhir</h2></div>
                @if($reports->isEmpty())
                    <div class="empty-state"><i class="bi bi-shield-check"></i>Belum ada laporan kendala untuk aset ini.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead><tr><th>No. Laporan</th><th>Kendala</th><th>Status</th><th>Tanggal</th></tr></thead>
                            <tbody>
                                @foreach($reports as $report)
                                    <tr>
                                        <td><a href="{{ route('pemeliharaanalat.reports.show', $report) }}" class="mono fw-semibold text-decoration-none">{{ $report->report_number }}</a></td>
                                        <td>{{ \Illuminate\Support\Str::limit($report->issue_description, 60) }}</td>
                                        <td><x-badge :enum="$report->status" /></td>
                                        <td class="text-nowrap">{{ $report->reported_at->translatedFormat('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection

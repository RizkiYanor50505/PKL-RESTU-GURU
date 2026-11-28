@extends('pemeliharaanalat.layouts.app')

@section('title', 'Data Cabang')

@section('content')
    <div class="page-head">
        <div>
            <h1>Daftar Cabang &amp; Rekap Biaya</h1>
        </div>
        @if(auth()->user()?->isAdmin() || session('auth_role') === 'manajer')
            <a href="{{ route('pemeliharaanalat.branches.create') }}" class="btn btn-primary" id="btn-add-branch">
                <i class="bi bi-plus-lg me-1"></i> Tambah Cabang
            </a>
        @endif
    </div>

    {{-- Kartu Konsolidasi Seluruh Cabang (Hasil Dijadikan 1) --}}
    <div class="card-panel mb-4 p-4" style="background: linear-gradient(135deg, var(--forest-900), var(--forest-800)); color: #fff; border: 1px solid rgba(20, 188, 173, .35);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <span class="bdg bdg-gold mb-2">
                    <i class="bi bi-diagram-3-fill me-1"></i> Konsolidasi Seluruh Cabang
                </span>
                <h2 class="h4 fw-bold mb-1 text-white">Total Biaya Perbaikan Seluruh Cabang Bulan Ini</h2>
                <p class="mb-0 text-white-50 small">Akumulasi biaya servis realisasi dari {{ $branches->count() }} cabang ({{ now()->translatedFormat('F Y') }})</p>
            </div>
            <div class="text-md-end">
                <div class="small text-uppercase fw-semibold text-white-50" style="letter-spacing:.05em">Grand Total Biaya (Dijadikan 1)</div>
                <div class="display-6 fw-extrabold my-1" id="grand-total-monthly-cost" style="color: #14bcad; text-shadow: 0 2px 6px rgba(0,0,0,.3);">
                    {{ rupiah($grandTotalMonthlyCost) }}
                </div>
                <div class="small text-white-50">
                    {{ $grandTotalMonthlyServices }} servis selesai · {{ $grandTotalActiveReports }} kendala aktif · {{ $grandTotalAssets }} total aset
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel / List Cabang --}}
    <section class="card-panel">
        <div class="panel-head">
            <h2><i class="bi bi-buildings text-primary me-2"></i>Rincian Biaya &amp; Aset Per Cabang</h2>
            <form method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm" style="width:260px">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari cabang atau kota...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>Kode &amp; Cabang</th>
                        <th>Kota &amp; Lokasi</th>
                        <th>Penanggung Jawab</th>
                        <th class="text-center">Jumlah Alat / Aset</th>
                        <th class="text-center">Kendala Aktif</th>
                        <th class="text-center">Servis Bulan Ini</th>
                        <th class="text-end">Biaya Bulan Ini</th>
                        <th class="text-end">Total Biaya (Histori)</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branchStats as $stat)
                        @php($b = $stat['branch'])
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="bdg bdg-primary mono fw-bold">{{ $b->code }}</span>
                                    <div>
                                        <a href="{{ route('pemeliharaanalat.branches.show', $b) }}" class="cell-title text-decoration-none">
                                            {{ $b->name }}
                                        </a>
                                        <div class="cell-sub">{{ $b->phone ?? 'Tidak ada telepon' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $b->city }}</div>
                                <div class="cell-sub">{{ \Illuminate\Support\Str::limit($b->address ?? '—', 35) }}</div>
                            </td>
                            <td>
                                <div><i class="bi bi-person me-1 text-muted"></i>{{ $b->manager_name ?? '—' }}</div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('pemeliharaanalat.branches.show', $b) }}" class="badge rounded-pill bg-light text-dark text-decoration-none px-3 py-2 border">
                                    <i class="bi bi-printer me-1 text-primary"></i><strong>{{ $stat['assets_count'] }}</strong> alat
                                </a>
                            </td>
                            <td class="text-center">
                                @if($stat['active_reports'] > 0)
                                    <span class="bdg bdg-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $stat['active_reports'] }} kendala
                                    </span>
                                @else
                                    <span class="bdg bdg-success">
                                        <i class="bi bi-check-circle me-1"></i>Aman
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold">{{ $stat['monthly_services'] }} servis</span>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-dark fs-6">{{ rupiah($stat['monthly_cost']) }}</div>
                                <div class="cell-sub">{{ now()->translatedFormat('M Y') }}</div>
                            </td>
                            <td class="text-end">
                                <div class="fw-semibold text-muted">{{ rupiah($stat['total_cost']) }}</div>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('pemeliharaanalat.branches.show', $b) }}" class="btn btn-sm btn-detail py-1 px-3" title="Lihat Alat & Servis">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                @if(auth()->user()?->isAdmin() || session('auth_role') === 'manajer')
                                    <a href="{{ route('pemeliharaanalat.branches.edit', $b) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('pemeliharaanalat.branches.destroy', $b) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus cabang {{ $b->name }}? Data aset dan histori terkait akan dibersihkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="bi bi-buildings"></i>
                                    Tidak ada data cabang yang cocok.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="3" class="text-uppercase" style="letter-spacing:.05em;">
                            <i class="bi bi-calculator me-1 text-primary"></i> Total Keseluruhan (Semua Cabang Dijadikan 1)
                        </td>
                        <td class="text-center">{{ $grandTotalAssets }} alat</td>
                        <td class="text-center">{{ $grandTotalActiveReports }} kendala</td>
                        <td class="text-center">{{ $grandTotalMonthlyServices }} servis</td>
                        <td class="text-end text-dark fw-bold fs-6">{{ rupiah($grandTotalMonthlyCost) }}</td>
                        <td class="text-end text-muted">{{ rupiah($grandTotalAllTimeCost) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
@endsection

@extends('pemeliharaanalat.layouts.app')

@section('title', 'Data Aset')

@section('content')
    <div class="page-head">
        <div>
            <h1>Data Aset</h1>
            <p>Mesin percetakan dan perangkat pendukung di seluruh cabang Restu Guru Promosindo.</p>
        </div>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('pemeliharaanalat.assets.create') }}" class="btn btn-primary" id="btn-add-asset"><i class="bi bi-plus-lg me-1"></i> Tambah Aset</a>
        @endif
    </div>

    <section class="card-panel">
        <div class="panel-head">
            <form method="GET" class="row g-2 w-100 align-items-center" id="form-asset-filter">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode, nama, atau merek...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="branch_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>
                                {{ $branch->name }} ({{ $branch->city }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="location" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit">Cari</button>
                    @if(request()->hasAny(['q', 'branch_id', 'location', 'status']))
                        <a href="{{ route('pemeliharaanalat.assets.index') }}" class="btn btn-light border" title="Reset"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>

        @if($assets->isEmpty())
            <div class="empty-state"><i class="bi bi-hdd-rack"></i>Tidak ada aset yang cocok dengan pencarian.</div>
        @else
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Aset</th>
                            <th>Cabang</th>
                            <th>Lokasi</th>
                            <th>Merek / Tipe</th>
                            <th>Status</th>
                            <th class="text-center">Laporan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                            <tr>
                                <td class="mono fw-semibold">{{ $asset->code }}</td>
                                <td>
                                    <a href="{{ route('pemeliharaanalat.assets.show', $asset) }}" class="cell-title text-decoration-none">{{ $asset->name }}</a>
                                    <div class="cell-sub">{{ $asset->subtitle }}</div>
                                </td>
                                <td>
                                    @if($asset->branch)
                                        <a href="{{ route('pemeliharaanalat.branches.show', $asset->branch) }}" class="text-decoration-none badge text-bg-light border text-dark fw-medium">
                                            <i class="bi bi-buildings text-primary me-1"></i>{{ $asset->branch->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $asset->location }}</td>
                                <td>{{ $asset->brand ?? '—' }}</td>
                                <td><x-badge :enum="$asset->status" /></td>
                                <td class="text-center">{{ $asset->reports_count }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('pemeliharaanalat.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()?->isAdmin())
                                        <a href="{{ route('pemeliharaanalat.assets.edit', $asset) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('pemeliharaanalat.assets.destroy', $asset) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus aset {{ $asset->name }}?')">
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

            @if($assets->hasPages())
                <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">Menampilkan {{ $assets->firstItem() }}–{{ $assets->lastItem() }} dari {{ $assets->total() }} aset</div>
                    {{ $assets->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection

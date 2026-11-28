@extends('pemeliharaanalat.layouts.app')

@section('title', 'Data Aset')

@section('content')
    <div class="page-head">
        <div>
            <h1>Data Aset</h1>
        </div>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('pemeliharaanalat.assets.create') }}" class="btn btn-primary" id="btn-add-asset"><i class="bi bi-plus-lg me-1"></i> Tambah Aset</a>
        @endif
    </div>

    <section class="card-panel">
        <div class="panel-head">
            <form method="GET" class="row g-2 w-100 align-items-center" id="form-asset-filter">
                <div class="col-lg-3 col-md-6">
                    <div class="input-group input-group-pill">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode, nama, merek...">
                    </div>
                </div>

                {{-- Filter Cabang --}}
                <div class="col-lg-3 col-md-6">
                    @php $curBranch = $branches->firstWhere('id', request('branch_id')); @endphp
                    <div class="dropdown">
                        <button class="btn btn-filter-dropdown dropdown-toggle w-100 justify-content-between text-truncate" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-inline-flex align-items-center gap-2 text-truncate">
                                <i class="bi bi-buildings text-teal"></i>
                                <span class="text-truncate">{{ $curBranch ? $curBranch->name : 'Semua Cabang' }}</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu shadow w-100" style="min-width: 220px; max-height: 280px; overflow-y: auto;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ !request('branch_id') ? 'active' : '' }}"
                                   href="{{ route('pemeliharaanalat.assets.index', array_filter(['q' => request('q'), 'location' => request('location'), 'status' => request('status')])) }}">
                                    <span>Semua Cabang</span>
                                    @if(!request('branch_id'))<i class="bi bi-check2"></i>@endif
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            @foreach($branches as $branch)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between {{ (string) request('branch_id') === (string) $branch->id ? 'active' : '' }}"
                                       href="{{ route('pemeliharaanalat.assets.index', array_filter(['q' => request('q'), 'branch_id' => $branch->id, 'location' => request('location'), 'status' => request('status')])) }}">
                                        <span>{{ $branch->name }}</span>
                                        @if((string) request('branch_id') === (string) $branch->id)<i class="bi bi-check2"></i>@endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Filter Lokasi --}}
                <div class="col-lg-2 col-md-4">
                    <div class="dropdown">
                        <button class="btn btn-filter-dropdown dropdown-toggle w-100 justify-content-between text-truncate" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-inline-flex align-items-center gap-2 text-truncate">
                                <i class="bi bi-geo-alt text-teal"></i>
                                <span class="text-truncate">{{ request('location') ?: 'Semua lokasi' }}</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu shadow w-100" style="min-width: 200px; max-height: 280px; overflow-y: auto;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ !request('location') ? 'active' : '' }}"
                                   href="{{ route('pemeliharaanalat.assets.index', array_filter(['q' => request('q'), 'branch_id' => request('branch_id'), 'status' => request('status')])) }}">
                                    <span>Semua lokasi</span>
                                    @if(!request('location'))<i class="bi bi-check2"></i>@endif
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            @foreach($locations as $loc)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between {{ request('location') === $loc ? 'active' : '' }}"
                                       href="{{ route('pemeliharaanalat.assets.index', array_filter(['q' => request('q'), 'branch_id' => request('branch_id'), 'location' => $loc, 'status' => request('status')])) }}">
                                        <span>{{ $loc }}</span>
                                        @if(request('location') === $loc)<i class="bi bi-check2"></i>@endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Filter Status --}}
                <div class="col-lg-2 col-md-4">
                    <div class="dropdown">
                        <button class="btn btn-filter-dropdown dropdown-toggle w-100 justify-content-between text-truncate" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-inline-flex align-items-center gap-2 text-truncate">
                                <i class="bi bi-activity text-teal"></i>
                                <span class="text-truncate">{{ request('status') && isset($statuses[request('status')]) ? $statuses[request('status')] : 'Semua status' }}</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu shadow w-100" style="min-width: 200px;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ !request('status') ? 'active' : '' }}"
                                   href="{{ route('pemeliharaanalat.assets.index', array_filter(['q' => request('q'), 'branch_id' => request('branch_id'), 'location' => request('location')])) }}">
                                    <span>Semua status</span>
                                    @if(!request('status'))<i class="bi bi-check2"></i>@endif
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            @foreach($statuses as $val => $lbl)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between {{ request('status') === $val ? 'active' : '' }}"
                                       href="{{ route('pemeliharaanalat.assets.index', array_filter(['q' => request('q'), 'branch_id' => request('branch_id'), 'location' => request('location'), 'status' => $val])) }}">
                                        <span>{{ $lbl }}</span>
                                        @if(request('status') === $val)<i class="bi bi-check2"></i>@endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit">Cari</button>
                    @if(request()->hasAny(['q', 'branch_id', 'location', 'status']))
                        <a href="{{ route('pemeliharaanalat.assets.index') }}" class="btn btn-outline-secondary" title="Reset filter"><i class="bi bi-x-lg"></i></a>
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

@extends('pemeliharaanalat.layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
    <div class="page-head">
        <div>
            <h1>Kelola Pengguna</h1>
        </div>
        <a href="{{ route('pemeliharaanalat.users.create') }}" class="btn btn-primary" id="btn-add-user"><i class="bi bi-person-plus me-1"></i> Tambah Pengguna</a>
    </div>

    <section class="card-panel">
        <div class="panel-head">
            <form method="GET" class="d-flex gap-2 flex-wrap w-100 align-items-center">
                <div class="input-group input-group-pill" style="max-width:280px">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama atau email...">
                </div>

                {{-- Filter Cabang --}}
                @php $curBranch = $branches->firstWhere('id', request('branch_id')); @endphp
                <div class="dropdown">
                    <button class="btn btn-filter-dropdown dropdown-toggle text-truncate" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 220px;">
                        <span class="d-inline-flex align-items-center gap-2 text-truncate">
                            <i class="bi bi-buildings text-teal"></i>
                            <span class="text-truncate">{{ $curBranch ? $curBranch->name : 'Semua Cabang' }}</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu shadow" style="min-width: 220px; max-height: 280px; overflow-y: auto;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between {{ !request('branch_id') ? 'active' : '' }}"
                               href="{{ route('pemeliharaanalat.users.index', array_filter(['q' => request('q'), 'role' => request('role')])) }}">
                                <span>Semua Cabang</span>
                                @if(!request('branch_id'))<i class="bi bi-check2"></i>@endif
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        @foreach($branches as $branch)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ (string) request('branch_id') === (string) $branch->id ? 'active' : '' }}"
                                   href="{{ route('pemeliharaanalat.users.index', array_filter(['q' => request('q'), 'branch_id' => $branch->id, 'role' => request('role')])) }}">
                                    <span>{{ $branch->name }}</span>
                                    @if((string) request('branch_id') === (string) $branch->id)<i class="bi bi-check2"></i>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Filter Peran --}}
                <div class="dropdown">
                    <button class="btn btn-filter-dropdown dropdown-toggle text-truncate" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 200px;">
                        <span class="d-inline-flex align-items-center gap-2 text-truncate">
                            <i class="bi bi-shield-lock text-teal"></i>
                            <span class="text-truncate">{{ request('role') && isset($roles[request('role')]) ? $roles[request('role')] : 'Semua peran' }}</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu shadow" style="min-width: 200px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between {{ !request('role') ? 'active' : '' }}"
                               href="{{ route('pemeliharaanalat.users.index', array_filter(['q' => request('q'), 'branch_id' => request('branch_id')])) }}">
                                <span>Semua peran</span>
                                @if(!request('role'))<i class="bi bi-check2"></i>@endif
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        @foreach($roles as $value => $label)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ request('role') === $value ? 'active' : '' }}"
                                   href="{{ route('pemeliharaanalat.users.index', array_filter(['q' => request('q'), 'branch_id' => request('branch_id'), 'role' => $value])) }}">
                                    <span>{{ $label }}</span>
                                    @if(request('role') === $value)<i class="bi bi-check2"></i>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <button class="btn btn-primary" type="submit">Cari</button>
                @if(request()->hasAny(['q', 'role', 'branch_id']))
                    <a href="{{ route('pemeliharaanalat.users.index') }}" class="btn btn-outline-secondary" title="Reset filter"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Cabang</th>
                        <th>Jabatan</th>
                        <th>Peran</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">{{ $user->initials() }}</div>
                                    <div>
                                        <div class="cell-title">{{ $user->name }}
                                            @if($user->is(auth()->user()))<span class="bdg bdg-gold ms-1"><i class="bi bi-star-fill"></i> Anda</span>@endif
                                        </div>
                                        <div class="cell-sub">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->branch)
                                    <span class="badge text-bg-light border text-dark">
                                        <i class="bi bi-buildings text-primary me-1"></i>{{ $user->branch->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">Semua Cabang / Pusat</span>
                                @endif
                            </td>
                            <td>{{ $user->jabatan ?? '—' }}</td>
                            <td><x-badge :enum="$user->role" /></td>
                            <td><x-badge :enum="$user->status" /></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('pemeliharaanalat.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit / Reset password"><i class="bi bi-pencil"></i></a>

                                @unless($user->is(auth()->user()))
                                    <form action="{{ route('pemeliharaanalat.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $user->isActive() ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $user->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi {{ $user->isActive() ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('pemeliharaanalat.users.destroy', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-people"></i>Tidak ada pengguna ditemukan.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna</div>
                {{ $users->links() }}
            </div>
        @endif
    </section>
@endsection

@php
    $sessionUser = session('auth_user', []);
    $sessionRole = session('auth_role', 'karyawan');
    $userName = auth()->user()?->name ?? $sessionUser['name'] ?? 'Staf Operasional';
    $userFirstName = explode(' ', trim($userName))[0] ?? 'User';
    $userEmail = auth()->user()?->email ?? $sessionUser['email'] ?? 'staf@restuguru.id';
    $userJabatan = auth()->user()?->jabatan ?? $sessionUser['division'] ?? ($sessionRole === 'manajer' ? 'Manajer Operasional' : 'Operator');
    $isManager = ($sessionRole === 'manajer') || (auth()->user()?->isAdmin() ?? false);
    $canHandleReports = ($sessionRole === 'manajer') || (auth()->user()?->canHandleReports() ?? false);
    $isAdmin = ($sessionRole === 'manajer') || (auth()->user()?->isAdmin() ?? false);
    $waitingCount = \App\Models\Report::where('status', \App\Enums\ReportStatus::Menunggu->value)->count();
@endphp

<header class="capsule-header">
    <div class="capsule-container">
        {{-- Kapsul 1 (Kiri): Logo Brand & Modul --}}
        <div class="nav-capsule brand-capsule">
            <a href="{{ route('pemeliharaanalat.dashboard') }}" class="capsule-brand-link">
                <span class="capsule-brand-icon">
                    <i class="bi bi-tools"></i>
                </span>
                <span class="capsule-brand-text">Pemeliharaan Alat</span>
            </a>
        </div>

        {{-- Kapsul 2 (Tengah): Menu Navigasi Utama (Desktop) --}}
        <nav class="nav-capsule menu-capsule d-none d-lg-flex" aria-label="Menu Navigasi Utama">
            <a href="{{ route('pemeliharaanalat.dashboard') }}"
               class="capsule-nav-item {{ request()->routeIs('pemeliharaanalat.dashboard') ? 'active' : '' }}"
               id="nav-dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('pemeliharaanalat.reports.create') }}"
               class="capsule-nav-item {{ request()->routeIs('pemeliharaanalat.reports.create') ? 'active' : '' }}"
               id="nav-report-create">
                <i class="bi bi-plus-circle"></i>
                <span>Lapor Kendala</span>
            </a>

            <a href="{{ route('pemeliharaanalat.reports.index') }}"
               class="capsule-nav-item {{ request()->routeIs('pemeliharaanalat.reports.index', 'pemeliharaanalat.reports.show') ? 'active' : '' }}"
               id="nav-reports">
                <i class="bi bi-clipboard2-pulse"></i>
                <span>Laporan</span>
                @if($waitingCount > 0)
                    <span class="capsule-badge">{{ $waitingCount }}</span>
                @endif
            </a>

            @if($canHandleReports)
                <a href="{{ route('pemeliharaanalat.branches.index') }}"
                   class="capsule-nav-item {{ request()->routeIs('pemeliharaanalat.branches.*') ? 'active' : '' }}"
                   id="nav-branches">
                    <i class="bi bi-buildings"></i>
                    <span>Cabang</span>
                </a>

                <a href="{{ route('pemeliharaanalat.assets.index') }}"
                   class="capsule-nav-item {{ request()->routeIs('pemeliharaanalat.assets.*') ? 'active' : '' }}"
                   id="nav-assets">
                    <i class="bi bi-hdd-rack"></i>
                    <span>Aset</span>
                </a>

                <a href="{{ route('pemeliharaanalat.maintenance.index') }}"
                   class="capsule-nav-item {{ request()->routeIs('pemeliharaanalat.maintenance.*') ? 'active' : '' }}"
                   id="nav-maintenance">
                    <i class="bi bi-tools"></i>
                    <span>Riwayat</span>
                </a>
            @endif

            @if($isAdmin)
                <a href="{{ route('pemeliharaanalat.users.index') }}"
                   class="capsule-nav-item {{ request()->routeIs('pemeliharaanalat.users.*') ? 'active' : '' }}"
                   id="nav-users">
                    <i class="bi bi-people"></i>
                    <span>Pengguna</span>
                </a>
            @endif
        </nav>

        {{-- Kapsul 3 (Kanan): Info Pengguna & Tombol Keluar Oranye --}}
        <div class="nav-capsule user-capsule">
            <div class="dropdown">
                <button class="capsule-user-btn" type="button" id="capsuleUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="{{ $userName }} ({{ $userEmail }})">
                    <span class="user-status-dot"></span>
                    <span class="user-name-text">{{ $userFirstName }}</span>
                    <span class="user-role-chip d-none d-sm-inline-flex">{{ $isManager ? 'Manajer' : 'Karyawan' }}</span>
                    <i class="bi bi-chevron-down ms-1" style="font-size: .7rem; opacity: .8;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="capsuleUserDropdown" style="min-width: 230px;">
                    <li class="px-3 py-2">
                        <div class="fw-bold text-dark">{{ $userName }}</div>
                        <div class="text-muted small">{{ $userEmail }}</div>
                        @if($userJabatan)
                            <div class="badge bg-light text-dark border mt-1">{{ $userJabatan }}</div>
                        @endif
                        <div class="text-muted small mt-1">
                            <i class="bi bi-geo-alt"></i> {{ auth()->user()?->branch ? auth()->user()->branch->name : 'Pusat & Semua Cabang' }}
                        </div>
                    </li>
                </ul>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="capsule-btn-logout" id="btn-capsule-logout">
                    <i class="bi bi-box-arrow-right"></i> <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</header>

{{-- Mobile Bottom Dock (< 992px) Mengambang Sesuai Design System --}}
<div class="mobile-bottom-dock d-lg-none" aria-label="Navigasi Bawah Mobile">
    <div class="mobile-dock-capsule">
        <a href="{{ route('pemeliharaanalat.dashboard') }}"
           class="dock-item {{ request()->routeIs('pemeliharaanalat.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('pemeliharaanalat.reports.create') }}"
           class="dock-item {{ request()->routeIs('pemeliharaanalat.reports.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i>
            <span>Lapor</span>
        </a>

        <a href="{{ route('pemeliharaanalat.reports.index') }}"
           class="dock-item {{ request()->routeIs('pemeliharaanalat.reports.index', 'pemeliharaanalat.reports.show') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-pulse"></i>
            <span>Laporan</span>
            @if($waitingCount > 0)
                <span class="dock-badge">{{ $waitingCount }}</span>
            @endif
        </a>

        @if($canHandleReports)
            <a href="{{ route('pemeliharaanalat.branches.index') }}"
               class="dock-item {{ request()->routeIs('pemeliharaanalat.branches.*') ? 'active' : '' }}">
                <i class="bi bi-buildings"></i>
                <span>Cabang</span>
            </a>

            <a href="{{ route('pemeliharaanalat.assets.index') }}"
               class="dock-item {{ request()->routeIs('pemeliharaanalat.assets.*') ? 'active' : '' }}">
                <i class="bi bi-hdd-rack"></i>
                <span>Aset</span>
            </a>

            <a href="{{ route('pemeliharaanalat.maintenance.index') }}"
               class="dock-item {{ request()->routeIs('pemeliharaanalat.maintenance.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i>
                <span>Riwayat</span>
            </a>
        @endif

        @if($isAdmin)
            <a href="{{ route('pemeliharaanalat.users.index') }}"
               class="dock-item {{ request()->routeIs('pemeliharaanalat.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Pengguna</span>
            </a>
        @endif
    </div>
</div>

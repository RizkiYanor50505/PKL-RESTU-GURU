@php
    $sessionRole = session('auth_role', 'karyawan');
    $waitingCount = \App\Models\Report::where('status', \App\Enums\ReportStatus::Menunggu->value)->count();
    $canHandleReports = ($sessionRole === 'manajer') || (auth()->user()?->canHandleReports() ?? false);
    $isAdmin = ($sessionRole === 'manajer') || (auth()->user()?->isAdmin() ?? false);
@endphp

<aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
    <a href="{{ route('pemeliharaanalat.dashboard') }}" class="sidebar-brand">
        <span class="brand-mark"><i class="bi bi-printer-fill"></i></span>
        <span>
            <span class="brand-title d-block">Restu Guru Promosindo</span>
            <span class="brand-sub">{{ auth()->user()?->branch ? auth()->user()->branch->name : 'Pusat & Semua Cabang' }}</span>
        </span>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-section">Utama</div>
        <a href="{{ route('pemeliharaanalat.dashboard') }}" id="nav-dashboard" class="side-link {{ request()->routeIs('pemeliharaanalat.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section">Pelaporan Kendala</div>
        <a href="{{ route('pemeliharaanalat.reports.create') }}" id="nav-report-create" class="side-link {{ request()->routeIs('pemeliharaanalat.reports.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Lapor Kendala
        </a>
        <a href="{{ route('pemeliharaanalat.reports.index') }}" id="nav-reports" class="side-link {{ request()->routeIs('pemeliharaanalat.reports.index', 'pemeliharaanalat.reports.show') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-pulse"></i> Daftar Laporan
            @if($waitingCount > 0)
                <span class="count">{{ $waitingCount }}</span>
            @endif
        </a>

        @if($canHandleReports)
            <div class="nav-section">Aset & Pemeliharaan</div>
            <a href="{{ route('pemeliharaanalat.branches.index') }}" id="nav-branches" class="side-link {{ request()->routeIs('pemeliharaanalat.branches.*') ? 'active' : '' }}">
                <i class="bi bi-buildings"></i> Data Cabang
            </a>
            <a href="{{ route('pemeliharaanalat.assets.index') }}" id="nav-assets" class="side-link {{ request()->routeIs('pemeliharaanalat.assets.*') ? 'active' : '' }}">
                <i class="bi bi-hdd-rack"></i> Data Aset
            </a>
            <a href="{{ route('pemeliharaanalat.maintenance.index') }}" id="nav-maintenance" class="side-link {{ request()->routeIs('pemeliharaanalat.maintenance.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Riwayat Pemeliharaan
            </a>
        @endif

        @if($isAdmin)
            <div class="nav-section">Administrasi</div>
            <a href="{{ route('pemeliharaanalat.users.index') }}" id="nav-users" class="side-link {{ request()->routeIs('pemeliharaanalat.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Kelola Pengguna
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        © {{ date('Y') }} Restu Guru Promosindo<br>Sistem Pengelolaan Aset &amp; Kendala
    </div>
</aside>

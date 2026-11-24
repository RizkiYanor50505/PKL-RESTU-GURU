<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Pengelolaan Aset & Pelaporan Kendala Mesin Percetakan — Restu Guru Promosindo Cabang Banjarbaru.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Restu Guru Promosindo Banjarbaru</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/rgp.css') }}?v={{ file_exists(public_path('css/rgp.css')) ? filemtime(public_path('css/rgp.css')) : time() }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
@php
    $sessionUser = session('auth_user', []);
    $sessionRole = session('auth_role', 'karyawan');
    $userName = auth()->user()?->name ?? $sessionUser['name'] ?? 'Staf Operasional';
    $userEmail = auth()->user()?->email ?? $sessionUser['email'] ?? 'staf@restuguru.id';
    $userJabatan = auth()->user()?->jabatan ?? $sessionUser['division'] ?? ($sessionRole === 'manajer' ? 'Manajer Operasional' : 'Operator');
    $userInitials = collect(explode(' ', trim($userName)))->filter()->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');
    $isManager = ($sessionRole === 'manajer') || (auth()->user()?->isAdmin() ?? false);
@endphp

<div class="app-shell">
    @include('pemeliharaanalat.partials.sidebar')
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu">
                <i class="bi bi-list"></i>
            </button>

            <div class="me-auto">
                <p class="topbar-title">@yield('title', 'Dashboard')</p>
                <div class="topbar-sub d-none d-sm-block">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>

            <div class="dropdown">
                <div class="user-chip" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                    <div class="avatar">{{ $userInitials ?: 'ST' }}</div>
                    <div class="name-wrap d-flex flex-column">
                        <span class="name">{{ $userName }}</span>
                        <span class="bdg {{ $isManager ? 'bdg-gold' : 'bdg-primary' }} mt-1" style="font-size:.64rem;padding:.1rem .5rem;width:fit-content">
                            <i class="bi {{ $isManager ? 'bi-shield-check' : 'bi-person' }}"></i> {{ $isManager ? 'Manajer' : 'Karyawan' }}
                        </span>
                    </div>
                    <i class="bi bi-chevron-down caret"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:240px" aria-labelledby="userMenu">
                    <li class="px-3 py-2">
                        <div class="fw-semibold">{{ $userName }}</div>
                        <div class="text-muted small">{{ $userEmail }}</div>
                        @if($userJabatan)
                            <div class="text-muted small">{{ $userJabatan }}</div>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger" id="btn-logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="content">
            @include('pemeliharaanalat.partials.flash')
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        const body = document.body;
        document.getElementById('menuToggle')?.addEventListener('click', () => body.classList.toggle('sidebar-open'));
        document.getElementById('sidebarBackdrop')?.addEventListener('click', () => body.classList.remove('sidebar-open'));
    })();
</script>
@stack('scripts')
</body>
</html>

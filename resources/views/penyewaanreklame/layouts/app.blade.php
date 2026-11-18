<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Penyewaan Reklame') &middot; Restu Guru Promosindo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 antialiased">
    @php
        $isLoggedIn = is_string(session('auth_role')) && session('auth_role') !== '';
        $isPublicView = request()->routeIs('penyewaanreklame.katalog*');
        $navItems = [
            [
                'label' => 'Dashboard',
                'route' => 'penyewaanreklame.dashboard',
                'active' => request()->routeIs('penyewaanreklame.dashboard'),
                'icon' => 'ph-squares-four',
            ],
            [
                'label' => 'Titik Reklame',
                'route' => 'penyewaanreklame.titik-reklame.index',
                'active' => request()->routeIs('penyewaanreklame.titik-reklame.*'),
                'icon' => 'ph-map-pin-line',
            ],
            [
                'label' => 'Data Klien',
                'route' => 'penyewaanreklame.klien.index',
                'active' => request()->routeIs('penyewaanreklame.klien.*'),
                'icon' => 'ph-users',
            ],
            [
                'label' => 'Kontrak Sewa',
                'route' => 'penyewaanreklame.kontrak.index',
                'active' => request()->routeIs('penyewaanreklame.kontrak.*'),
                'icon' => 'ph-file-text',
            ],
            [
                'label' => 'Laporan',
                'route' => 'penyewaanreklame.laporan.index',
                'active' => request()->routeIs('penyewaanreklame.laporan.*'),
                'icon' => 'ph-chart-bar',
            ],
        ];
    @endphp

    <header class="sticky top-0 z-30 border-b border-slate-200/90 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <div class="flex items-center gap-6">
                <a href="{{ $isLoggedIn ? route('penyewaanreklame.dashboard') : route('penyewaanreklame.katalog') }}"
                   class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 text-white shadow-sm shadow-violet-200">
                        <i class="ph ph-megaphone text-xl"></i>
                    </span>
                    <div>
                        <span class="block text-sm font-bold leading-tight text-slate-900">Penyewaan Reklame</span>
                        <span class="block text-[11px] font-medium text-slate-500">Restu Guru Promosindo</span>
                    </div>
                </a>

                @if ($isLoggedIn)
                    <nav class="hidden items-center gap-1 lg:flex">
                        @foreach ($navItems as $item)
                            <a href="{{ route($item['route']) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition {{ $item['active'] ? 'bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i class="ph {{ $item['icon'] }} text-base"></i>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                @endif
            </div>

            <div class="flex items-center gap-2.5">
                @if ($isLoggedIn)
                    <a href="{{ route('penyewaanreklame.katalog') }}" target="_blank" rel="noopener"
                       class="hidden items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 sm:inline-flex">
                        <i class="ph ph-globe text-sm text-violet-600"></i>
                        Katalog Publik
                    </a>

                    <div class="hidden items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 md:flex">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-violet-600 text-[11px] font-bold text-white">
                            {{ strtoupper(substr((string) session('auth_user.name', 'A'), 0, 1)) }}
                        </span>
                        <div class="leading-tight">
                            <div class="text-[11px] font-semibold text-slate-800">{{ session('auth_user.name', 'Admin') }}</div>
                            <div class="text-[10px] uppercase tracking-wide text-slate-500">{{ session('auth_role') }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                            <i class="ph ph-sign-out text-sm"></i>
                            Keluar
                        </button>
                    </form>
                @else
                    @if ($isPublicView)
                        <span class="hidden rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200 sm:inline-flex">
                            Kalimantan Selatan
                        </span>
                    @endif
                    <a href="{{ route('login', ['module' => 'penyewaanreklame']) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                        <i class="ph ph-lock-key text-sm"></i>
                        Login Admin
                    </a>
                @endif
            </div>
        </div>

        @if ($isLoggedIn)
            <nav class="flex gap-1 overflow-x-auto border-t border-slate-100 px-4 py-2 lg:hidden">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold {{ $item['active'] ? 'bg-violet-50 text-violet-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="ph {{ $item['icon'] }} text-sm"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        @endif
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-7 sm:px-6">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white py-4">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 text-xs text-slate-500 sm:flex-row sm:px-6">
            <span>&copy; {{ date('Y') }} Restu Guru Promosindo &middot; Divisi Advertising</span>
            <span>Sistem Informasi Manajemen Penyewaan Lokasi Reklame</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

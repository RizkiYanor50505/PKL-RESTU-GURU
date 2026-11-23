<!DOCTYPE html>
<html lang="id" class="h-full antialiased selection:bg-emerald-500 selection:text-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Penyewaan Reklame') &middot; Restu Guru Promosindo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                    },
                    colors: {
                        canvas: '#F4F4F0',
                        obsidian: '#0B0F14',
                        ink: '#0F1419',
                        muted: '#53606E',
                    },
                },
            },
        };
    </script>
    <style>
        .tabular-nums { font-variant-numeric: tabular-nums; }
    </style>
    @stack('styles')
</head>
<body class="flex min-h-screen flex-col bg-canvas font-sans text-ink antialiased">
    @php
        $isLoggedIn = is_string(session('auth_role')) && session('auth_role') !== '';
        $isPublicView = request()->routeIs('penyewaanreklame.katalog*');
        $navItems = [
            [
                'label' => 'Dashboard',
                'route' => 'penyewaanreklame.dashboard',
                'active' => request()->routeIs('penyewaanreklame.dashboard'),
            ],
            [
                'label' => 'Titik Reklame',
                'route' => 'penyewaanreklame.titik-reklame.index',
                'active' => request()->routeIs('penyewaanreklame.titik-reklame.*'),
            ],
            [
                'label' => 'Klien',
                'route' => 'penyewaanreklame.klien.index',
                'active' => request()->routeIs('penyewaanreklame.klien.*'),
            ],
            [
                'label' => 'Kontrak Sewa',
                'route' => 'penyewaanreklame.kontrak.index',
                'active' => request()->routeIs('penyewaanreklame.kontrak.*'),
            ],
            [
                'label' => 'Laporan',
                'route' => 'penyewaanreklame.laporan.index',
                'active' => request()->routeIs('penyewaanreklame.laporan.*'),
            ],
        ];
    @endphp

    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <div class="flex items-center gap-6">
                <a href="{{ $isLoggedIn ? route('penyewaanreklame.dashboard') : route('penyewaanreklame.katalog') }}"
                   class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-obsidian text-emerald-400">
                        <i class="ph-fill ph-megaphone text-lg"></i>
                    </span>
                    <div>
                        <span class="block text-sm font-extrabold leading-tight tracking-tight text-slate-900">Penyewaan Reklame</span>
                        <span class="block text-[11px] font-medium text-slate-500">Restu Guru Promosindo</span>
                    </div>
                </a>

                @if ($isLoggedIn)
                    <nav class="hidden items-center gap-1 lg:flex">
                        @foreach ($navItems as $item)
                            <a href="{{ route($item['route']) }}"
                               class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $item['active'] ? 'bg-obsidian text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
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
                        <i class="ph ph-globe text-sm text-emerald-600"></i>
                        Katalog Publik
                    </a>

                    <div class="hidden items-center gap-2 border-l border-slate-200 pl-3 md:flex">
                        <div class="text-right leading-tight">
                            <div class="text-xs font-semibold text-slate-800">{{ session('auth_user.name', 'Admin') }}</div>
                            <div class="font-mono text-[10px] uppercase text-slate-500">{{ session('auth_role') }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-obsidian px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800">
                            Keluar
                        </button>
                    </form>
                @else
                    @if ($isPublicView)
                        <span class="hidden font-mono text-[11px] font-medium text-slate-500 sm:inline">Kalimantan Selatan</span>
                    @endif
                    <a href="{{ route('login', ['module' => 'penyewaanreklame']) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-obsidian px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800">
                        Login Admin
                    </a>
                @endif
            </div>
        </div>

        @if ($isLoggedIn)
            <nav class="flex gap-1 overflow-x-auto border-t border-slate-100 px-4 py-2 lg:hidden">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="shrink-0 rounded-lg px-3 py-1.5 text-xs font-semibold {{ $item['active'] ? 'bg-obsidian text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        @endif
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200/80 bg-white/70 py-4">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 text-xs text-slate-500 sm:flex-row sm:px-6">
            <span>&copy; {{ date('Y') }} Restu Guru Promosindo &middot; Divisi Advertising</span>
            <span class="font-mono text-[11px]">SIPR &middot; Sistem Informasi Penyewaan Reklame</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

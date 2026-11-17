<!DOCTYPE html>
<html lang="id" class="h-full antialiased selection:bg-emerald-500 selection:text-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#F4F4F0">
    <title>@yield('title', 'Presensi Karyawan') · hadir.in</title>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Phosphor Icons (Light & Regular) -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/light/style.css" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css" />

    <!-- Tailwind CSS via CDN -->
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
                        carbon: '#121820',
                        surface: '#FFFFFF',
                        subtle: '#F8F8F6',
                        ink: '#0F1419',
                        muted: '#53606E',
                        accent: '#0D9488',
                        emerald: {
                            400: '#34D399',
                            500: '#10B981',
                            600: '#059669',
                        }
                    },
                    transitionTimingFunction: {
                        'spring': 'cubic-bezier(0.32, 0.72, 0, 1)',
                    }
                }
            }
        }
    </script>
    <script>
        window.addEventListener('error', function (e) {
            console.error('JS ERROR:', e);
            let b = document.getElementById('debugJsErrorBanner');
            if (!b) {
                b = document.createElement('div');
                b.id = 'debugJsErrorBanner';
                b.style.cssText = 'position:fixed;bottom:16px;left:16px;z-index:999999;background:#991B1B;color:#FFF;padding:10px 16px;border-radius:12px;font-family:monospace;font-size:11px;max-width:85vw;box-shadow:0 8px 24px rgba(0,0,0,0.4);border:1px solid #EF4444;display:flex;align-items:flex-start;gap:12px;';
                document.body ? document.body.appendChild(b) : document.addEventListener('DOMContentLoaded', () => document.body.appendChild(b));
            }
            b.innerHTML = '<div style="flex:1;"><strong>⚠️ JavaScript Error:</strong> ' + (e.message || 'Error') + '<br><small>' + (e.filename ? e.filename.split('/').pop() : '') + ' (baris ' + (e.lineno || '?') + ')</small></div>' +
                '<button type="button" onclick="this.parentElement.remove()" style="background:transparent;border:none;color:#FFF;font-size:16px;cursor:pointer;line-height:1;padding:0 4px;">&times;</button>';
        });
    </script>

    <style>
        :root {
            --ease-spring: cubic-bezier(0.32, 0.72, 0, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #F4F4F0;
            background-image:
                radial-gradient(at 15% 0%, rgba(16, 185, 129, 0.06) 0px, transparent 50%),
                radial-gradient(at 85% 10%, rgba(13, 148, 136, 0.05) 0px, transparent 45%);
            color: #0F1419;
            min-height: 100dvh;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        /* Fixed Film-Grain Overlay (GPU-Safe) */
        .film-grain {
            position: fixed;
            inset: 0;
            z-index: 40;
            pointer-events: none;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        /* =========================================================
           HAPTIC DOUBLE-BEZEL ARCHITECTURE (DOPPELRAND)
           ========================================================= */
        .bezel-shell {
            background: rgba(15, 20, 25, 0.035);
            padding: 5px;
            border-radius: 1.5rem;
            box-shadow:
                inset 0 1px 2px rgba(15, 20, 25, 0.05),
                0 1px 0 rgba(255, 255, 255, 0.85);
            transition: transform 0.6s var(--ease-spring), box-shadow 0.6s var(--ease-spring);
        }

        .bezel-core {
            background: #FFFFFF;
            border-radius: calc(1.5rem - 5px);
            border: 1px solid rgba(15, 20, 25, 0.06);
            box-shadow:
                0 18px 40px -16px rgba(15, 20, 25, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 1);
            height: 100%;
        }

        /* Obsidian Machined Telemetry Core */
        .bezel-shell-dark {
            background: rgba(11, 15, 20, 0.12);
            padding: 5px;
            border-radius: 1.5rem;
            box-shadow:
                inset 0 1px 2px rgba(11, 15, 20, 0.25),
                0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .bezel-core-dark {
            background-color: #0B0F14;
            background-image:
                radial-gradient(120% 100% at 90% 0%, rgba(16, 185, 129, 0.18) 0%, rgba(13, 148, 136, 0.06) 45%, transparent 100%),
                radial-gradient(80% 80% at 0% 100%, rgba(56, 189, 248, 0.08) 0%, transparent 70%);
            border-radius: calc(1.5rem - 5px);
            border: 1px solid rgba(255, 255, 255, 0.09);
            box-shadow:
                0 24px 48px -16px rgba(11, 15, 20, 0.35),
                inset 0 1px 1px rgba(255, 255, 255, 0.14);
            color: #F8FAFC;
            height: 100%;
        }

        @media (min-width: 640px) {
            .bezel-shell,
            .bezel-shell-dark {
                padding: 6px;
                border-radius: 2rem;
            }
            .bezel-core,
            .bezel-core-dark {
                border-radius: calc(2rem - 6px);
            }
        }

        /* Compact Nested Sub-Tray */
        .sub-tray {
            background: #F7F7F4;
            border: 1px solid rgba(15, 20, 25, 0.055);
            border-radius: 1.25rem;
            box-shadow: inset 0 1px 2px rgba(15, 20, 25, 0.025);
        }

        .sub-tray-dark {
            background: rgba(255, 255, 255, 0.045);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.25rem;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.06);
        }

        /* =========================================================
           ISLAND CTA & BUTTON-IN-BUTTON ARCHITECTURE
           ========================================================= */
        .btn-island-primary {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 46px;
            padding: 6px 6px 6px 20px;
            border-radius: 9999px;
            background: #0B0F14;
            color: #FFFFFF;
            font-size: 13.5px;
            font-weight: 600;
            letter-spacing: -0.01em;
            box-shadow:
                0 10px 24px -8px rgba(11, 15, 20, 0.35),
                inset 0 1px 1px rgba(255, 255, 255, 0.18);
            transition: transform 0.5s var(--ease-spring), background-color 0.5s var(--ease-spring), box-shadow 0.5s var(--ease-spring);
            cursor: pointer;
        }

        .btn-island-primary:hover {
            background: #161E27;
            box-shadow:
                0 14px 30px -8px rgba(11, 15, 20, 0.45),
                inset 0 1px 1px rgba(255, 255, 255, 0.25);
        }

        .btn-island-primary:active {
            transform: scale(0.97);
        }

        .btn-island-primary .icon-nest {
            width: 34px;
            height: 34px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.5s var(--ease-spring), background-color 0.5s var(--ease-spring), color 0.5s var(--ease-spring);
            flex-shrink: 0;
        }

        .btn-island-primary:hover .icon-nest {
            transform: translate(2px, -1px) scale(1.06);
            background: #34D399;
            color: #052E16;
        }

        /* Emerald Accent Island Button (For dark surfaces) */
        .btn-island-emerald {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 46px;
            padding: 6px 6px 6px 20px;
            border-radius: 9999px;
            background: #10B981;
            color: #042F2E;
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: -0.01em;
            box-shadow:
                0 12px 28px -8px rgba(16, 185, 129, 0.45),
                inset 0 1px 1px rgba(255, 255, 255, 0.4);
            transition: transform 0.5s var(--ease-spring), background-color 0.5s var(--ease-spring);
            cursor: pointer;
        }

        .btn-island-emerald:hover {
            background: #34D399;
        }

        .btn-island-emerald:active {
            transform: scale(0.97);
        }

        .btn-island-emerald .icon-nest {
            width: 34px;
            height: 34px;
            border-radius: 9999px;
            background: rgba(4, 47, 46, 0.14);
            color: #042F2E;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.5s var(--ease-spring), background-color 0.5s var(--ease-spring);
            flex-shrink: 0;
        }

        .btn-island-emerald:hover .icon-nest {
            transform: translate(2px, -1px) scale(1.06);
            background: #042F2E;
            color: #34D399;
        }

        /* Secondary Pill Button */
        .btn-island-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 44px;
            padding: 6px 6px 6px 18px;
            border-radius: 9999px;
            background: #FFFFFF;
            color: #0F1419;
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid rgba(15, 20, 25, 0.09);
            box-shadow: 0 6px 16px -6px rgba(15, 20, 25, 0.06);
            transition: transform 0.5s var(--ease-spring), background-color 0.5s var(--ease-spring), border-color 0.5s var(--ease-spring);
            cursor: pointer;
        }

        .btn-island-secondary:hover {
            background: #F8F8F6;
            border-color: rgba(15, 20, 25, 0.18);
        }

        .btn-island-secondary:active {
            transform: scale(0.97);
        }

        .btn-island-secondary .icon-nest {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            background: rgba(15, 20, 25, 0.05);
            color: #0F1419;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.5s var(--ease-spring), background-color 0.5s var(--ease-spring);
            flex-shrink: 0;
        }

        .btn-island-secondary:hover .icon-nest {
            transform: translate(2px, -1px) scale(1.05);
            background: rgba(15, 20, 25, 0.1);
        }

        /* Eyebrow Micro-Badge */
        .eyebrow-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-weight: 700;
            background: rgba(15, 20, 25, 0.05);
            color: #3E4C59;
            border: 1px solid rgba(15, 20, 25, 0.06);
        }

        .eyebrow-tag-dark {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.07);
            color: #A7F3D0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Semantic Status Pills */
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 11px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        .badge-emerald {
            background: rgba(16, 185, 129, 0.12);
            color: #065F46;
            border: 1px solid rgba(16, 185, 129, 0.22);
        }

        .badge-amber {
            background: rgba(245, 158, 11, 0.13);
            color: #92400E;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .badge-rose {
            background: rgba(244, 63, 94, 0.12);
            color: #9F1239;
            border: 1px solid rgba(244, 63, 94, 0.22);
        }

        .badge-neutral {
            background: rgba(15, 20, 25, 0.06);
            color: #334155;
            border: 1px solid rgba(15, 20, 25, 0.08);
        }

        /* =========================================================
           FLUID ISLAND NAVIGATION BAR
           ========================================================= */
        .fluid-island-nav {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(15, 20, 25, 0.075);
            box-shadow:
                0 20px 40px -15px rgba(15, 20, 25, 0.07),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            border-radius: 9999px;
            padding: 6px 8px;
        }

        .island-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            color: #53606E;
            transition: all 0.45s var(--ease-spring);
        }

        .island-link:hover {
            color: #0F1419;
            background: rgba(15, 20, 25, 0.04);
        }

        .island-link.active {
            background: #0B0F14;
            color: #FFFFFF;
            box-shadow: 0 6px 16px -4px rgba(11, 15, 20, 0.28);
        }

        /* =========================================================
           INPUTS & TABLES (16px on mobile to prevent iOS auto-zoom)
           ========================================================= */
        .haptic-input {
            width: 100%;
            height: 46px;
            padding: 0 16px;
            border-radius: 1rem;
            background: #F7F7F4;
            border: 1px solid rgba(15, 20, 25, 0.09);
            color: #0F1419;
            font-size: 16px;
            font-weight: 500;
            box-shadow: inset 0 1px 2px rgba(15, 20, 25, 0.03);
            transition: all 0.4s var(--ease-spring);
        }

        .haptic-textarea {
            width: 100%;
            padding: 12px 16px;
            border-radius: 1rem;
            background: #F7F7F4;
            border: 1px solid rgba(15, 20, 25, 0.09);
            color: #0F1419;
            font-size: 16px;
            font-weight: 500;
            box-shadow: inset 0 1px 2px rgba(15, 20, 25, 0.03);
            transition: all 0.4s var(--ease-spring);
        }

        @media (min-width: 768px) {
            .haptic-input,
            .haptic-textarea {
                font-size: 14px;
            }
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .haptic-textarea:focus {
            outline: none;
            background: #FFFFFF;
            border-color: #0B0F14;
            box-shadow: 0 0 0 4px rgba(15, 20, 25, 0.06);
        }

        /* Staggered Entry Animation (GPU-Safe transform & opacity) */
        .reveal-item {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.7s var(--ease-spring), transform 0.7s var(--ease-spring);
            will-change: transform, opacity;
        }

        .reveal-item.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.85); opacity: 0.65; }
            70% { transform: scale(1.35); opacity: 0; }
            100% { transform: scale(1.35); opacity: 0; }
        }

        .radar-pulse {
            animation: pulse-ring 2.8s var(--ease-spring) infinite;
        }
    </style>
    @stack('styles')
</head>
<body class="relative flex flex-col justify-between">

    <!-- Fixed Film-Grain Texture Overlay -->
    <div class="film-grain" aria-hidden="true"></div>

    @php
        $authRole = session('auth_role');
        $authUser = session('auth_user', []);
        $isManager = $authRole === 'manajer';
        $isEmployee = $authRole === 'karyawan';
        $isAuthPage = request()->routeIs('login');
        $homeRoute = $isManager ? route('admin.dashboard') : ($isEmployee ? route('karyawan.dashboard') : route('login'));
    @endphp

    @if(!$isAuthPage && $authRole)
        <!-- Floating Fluid Island Top Navigation -->
        <header class="sticky top-4 z-30 px-4 md:px-8 pt-2">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">

                <!-- Brand Pill -->
                <a href="{{ $homeRoute }}" class="fluid-island-nav inline-flex items-center gap-3 px-4 py-2 shrink-0 group">
                    <span class="w-8 h-8 rounded-full bg-obsidian text-emerald-400 flex items-center justify-center shadow-sm transition-transform duration-500 ease-spring group-hover:scale-105">
                        <i class="ph-fill ph-fingerprint text-lg"></i>
                    </span>
                    <div class="pr-1">
                        <span class="block text-sm font-extrabold tracking-tight text-ink leading-none">hadir.in</span>
                        <span class="block font-mono text-[10px] text-muted mt-0.5">
                            {{ $isManager ? 'EXECUTIVE HRD' : 'GEOFENCE v2.4' }}
                        </span>
                    </div>
                </a>

                <!-- Center Navigation Island (Desktop, Isolated by Role) -->
                <nav class="hidden md:flex items-center gap-1 fluid-island-nav" aria-label="Navigasi Utama">
                    <a href="{{ route('portal') }}" class="island-link {{ request()->routeIs('portal') ? 'active' : '' }}" title="Portal Modul Kelompok">
                        <i class="ph-light ph-squares-four text-base text-emerald-600"></i>
                        <span>Portal Modul</span>
                    </a>
                    @if($isEmployee)
                        <a href="{{ route('karyawan.dashboard') }}" class="island-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                            <i class="ph-light ph-squares-four text-base"></i>
                            <span>Beranda</span>
                        </a>
                        <a href="{{ route('karyawan.scan') }}" class="island-link {{ request()->routeIs('karyawan.scan') ? 'active' : '' }}">
                            <i class="ph-light ph-qr-code text-base"></i>
                            <span>Scan Presensi</span>
                        </a>
                        <a href="{{ route('karyawan.izin') }}" class="island-link {{ request()->routeIs('karyawan.izin') ? 'active' : '' }}">
                            <i class="ph-light ph-file-text text-base"></i>
                            <span>Pengajuan Izin</span>
                        </a>
                        <a href="{{ route('karyawan.riwayat') }}" class="island-link {{ request()->routeIs('karyawan.riwayat') ? 'active' : '' }}">
                            <i class="ph-light ph-clock-counter-clockwise text-base"></i>
                            <span>Riwayat Log</span>
                        </a>
                    @elseif($isManager)
                        <a href="{{ route('admin.dashboard') }}" class="island-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="ph-light ph-Broadcast text-base"></i>
                            <span>Command Center</span>
                        </a>
                        <a href="{{ route('admin.dashboard') }}#geofenceMapCard" class="island-link">
                            <i class="ph-light ph-map-pin-area text-base"></i>
                            <span>Peta &amp; Geofence</span>
                        </a>
                        <a href="{{ route('admin.dashboard') }}#leaveApprovalCard" class="island-link">
                            <i class="ph-light ph-check-circle text-base"></i>
                            <span>Persetujuan Izin</span>
                            @if(($pendingLeaveCount ?? 0) > 0)
                                <span id="navLeaveBadge" class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-800">{{ $pendingLeaveCount }}</span>
                            @endif
                        </a>
                        <button type="button" onclick="if (typeof openQrModal === 'function') { openQrModal(); } else { window.location.href = '{{ route('admin.dashboard') }}#modalQr'; }" class="island-link">
                            <i class="ph-light ph-shield-check text-base"></i>
                            <span>Otorisasi QR Statis</span>
                        </button>
                    @endif
                </nav>

                <!-- Right Profile & Logout Island -->
                <div class="fluid-island-nav flex items-center gap-2.5 pl-3.5 pr-1.5 py-1">
                    <div class="hidden sm:flex items-center gap-2 pr-2 border-r border-slate-900/10">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="font-mono text-xs font-medium text-ink tabular-nums">103.14.88.19</span>
                    </div>

                    <div class="hidden lg:block text-right pr-1">
                        <div class="text-xs font-bold text-ink leading-tight">
                            {{ $authUser['name'] ?? ($isManager ? 'Budi Santoso' : 'Ahmad Fauzi') }}
                        </div>
                        <div class="font-mono text-[10px] text-muted">
                            {{ $authUser['nik'] ?? ($isManager ? 'MGR-2026-014' : 'PKL-2026-081') }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-slate-900/[0.05] hover:bg-rose-500/10 text-ink hover:text-rose-700 text-xs font-semibold transition-all duration-500 ease-spring active:scale-95"
                                title="Keluar Sesi">
                            <span>Keluar</span>
                            <i class="ph-light ph-sign-out text-sm"></i>
                        </button>
                    </form>
                </div>

            </div>
        </header>
    @endif

    <!-- Main Workspace Canvas -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 md:px-8 {{ $isAuthPage ? 'py-8 md:py-16 flex items-center justify-center' : 'pt-6 pb-28 md:pb-16' }}">
        @yield('content')
    </main>

    <!-- Mobile Floating Bottom Dock (Role-Isolated) -->
    @if($authRole && !$isAuthPage)
        @include('layouts.bottom-nav')
    @endif

    <!-- Floating Haptic Toast Notification -->
    <div id="toastNotification" role="status" aria-live="polite" class="fixed bottom-24 md:bottom-8 right-4 md:right-8 z-50 hidden w-full max-w-sm px-4 md:px-0">
        <div class="bezel-shell-dark">
            <div class="bezel-core-dark p-4 flex items-start justify-between gap-3.5">
                <div class="flex items-start gap-3">
                    <span id="toastIconWrap" class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 flex items-center justify-center shrink-0 mt-0.5">
                        <i id="toastIconGlyph" class="ph-fill ph-check-circle text-lg"></i>
                    </span>
                    <div>
                        <p id="toastTitle" class="text-sm font-bold text-white tracking-tight">Pemberitahuan Sistem</p>
                        <p id="toastMessage" class="text-xs text-slate-300 mt-0.5 leading-relaxed">Operasi berhasil diverifikasi.</p>
                    </div>
                </div>
                <button type="button" onclick="hideToast()" class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 flex items-center justify-center shrink-0 transition-colors" aria-label="Tutup">
                    <i class="ph-light ph-x text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        let toastTimer = null;
        function showToast(title, message, isSuccess = true) {
            const toast = document.getElementById('toastNotification');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            const toastIconWrap = document.getElementById('toastIconWrap');
            const toastIconGlyph = document.getElementById('toastIconGlyph');

            if (!toast) return;
            toastTitle.textContent = title;
            toastMessage.textContent = message;

            if (isSuccess) {
                toastIconWrap.className = 'w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 flex items-center justify-center shrink-0 mt-0.5';
                toastIconGlyph.className = 'ph-fill ph-check-circle text-lg';
            } else {
                toastIconWrap.className = 'w-8 h-8 rounded-full bg-rose-500/20 text-rose-400 border border-rose-400/30 flex items-center justify-center shrink-0 mt-0.5';
                toastIconGlyph.className = 'ph-fill ph-warning-octagon text-lg';
            }

            toast.classList.remove('hidden');
            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(hideToast, 3600);
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) toast.classList.add('hidden');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                hideToast();
            }
        });

        // GPU-Safe IntersectionObserver for Staggered Entry Choreography
        document.addEventListener('DOMContentLoaded', function () {
            const items = document.querySelectorAll('.reveal-item');
            if (!('IntersectionObserver' in window)) {
                items.forEach(el => el.classList.add('is-visible'));
                return;
            }
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.06 });

            items.forEach((el, index) => {
                el.style.transitionDelay = `${Math.min(index * 55, 280)}ms`;
                observer.observe(el);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

@php
    $mobileRole = session('auth_role');
@endphp

<!-- Floating Fluid Island Bottom Dock (<768px) with Safe-Area Support & 48px Touch Targets -->
<nav class="md:hidden fixed inset-x-3 z-30 flex justify-center pointer-events-none"
     style="bottom: max(0.75rem, env(safe-area-inset-bottom, 0.75rem));"
     aria-label="Navigasi Utama Mobile">
    <div class="fluid-island-nav pointer-events-auto w-full max-w-md px-1.5 py-1.5 shadow-[0_20px_44px_-10px_rgba(11,15,20,0.22)]">
        @if($mobileRole === 'manajer')
            <div class="grid grid-cols-4 items-center gap-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex flex-col items-center justify-center min-h-[48px] px-2 rounded-full text-xs font-semibold transition-all duration-500 ease-spring active:scale-95 {{ request()->routeIs('admin.dashboard') ? 'bg-obsidian text-white shadow-sm' : 'text-muted hover:text-ink' }}">
                    <i class="ph-light ph-broadcast text-lg leading-none"></i>
                    <span class="text-[10px] mt-1 leading-none">Command</span>
                </a>

                <a href="{{ route('admin.dashboard') }}#geofenceMapCard"
                   class="flex flex-col items-center justify-center min-h-[48px] px-2 rounded-full text-xs font-semibold text-muted hover:text-ink transition-all duration-500 ease-spring active:scale-95">
                    <i class="ph-light ph-map-pin-area text-lg leading-none"></i>
                    <span class="text-[10px] mt-1 leading-none">Peta</span>
                </a>

                <a href="{{ route('admin.dashboard') }}#leaveApprovalCard"
                   class="relative flex flex-col items-center justify-center min-h-[48px] px-2 rounded-full text-xs font-semibold text-muted hover:text-ink transition-all duration-500 ease-spring active:scale-95">
                    <div class="relative flex items-center justify-center">
                        <i class="ph-light ph-check-circle text-lg leading-none"></i>
                        @if(($pendingLeaveCount ?? 0) > 0)
                            <span id="mobileLeaveDot" class="absolute -top-1 -right-1.5 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
                        @endif
                    </div>
                    <span id="mobileLeaveText" class="text-[10px] mt-1 leading-none">Izin{{ ($pendingLeaveCount ?? 0) > 0 ? ' ('.$pendingLeaveCount.')' : '' }}</span>
                </a>

                <button type="button" onclick="openQrModal();"
                        class="flex flex-col items-center justify-center min-h-[48px] px-2 rounded-full text-xs font-semibold text-muted hover:text-ink transition-all duration-500 ease-spring active:scale-95">
                    <i class="ph-light ph-qr-code text-lg leading-none"></i>
                    <span class="text-[10px] mt-1 leading-none">Token QR</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-4 items-center gap-1">
                <a href="{{ route('karyawan.dashboard') }}"
                   class="flex flex-col items-center justify-center min-h-[48px] px-2 rounded-full text-xs font-semibold transition-all duration-500 ease-spring active:scale-95 {{ request()->routeIs('karyawan.dashboard') ? 'bg-obsidian text-white shadow-sm' : 'text-muted hover:text-ink' }}">
                    <i class="ph-light ph-house-line text-lg leading-none"></i>
                    <span class="text-[10px] mt-1 leading-none">Absensi</span>
                </a>

                <a href="{{ route('karyawan.scan') }}"
                   class="flex flex-col items-center justify-center min-h-[48px] px-2 rounded-full text-xs font-semibold transition-all duration-500 ease-spring active:scale-95 {{ request()->routeIs('karyawan.scan') ? 'bg-obsidian text-white shadow-sm' : 'text-muted hover:text-ink' }}">
                    <i class="ph-light ph-qr-code text-lg leading-none"></i>
                    <span class="text-[10px] mt-1 leading-none">Scan</span>
                </a>

                <a href="{{ route('karyawan.izin') }}"
                   class="flex flex-col items-center justify-center min-h-[48px] px-2 rounded-full text-xs font-semibold transition-all duration-500 ease-spring active:scale-95 {{ request()->routeIs('karyawan.izin') ? 'bg-obsidian text-white shadow-sm' : 'text-muted hover:text-ink' }}">
                    <i class="ph-light ph-file-text text-lg leading-none"></i>
                    <span class="text-[10px] mt-1 leading-none">Izin</span>
                </a>

                <a href="{{ route('karyawan.riwayat') }}"
                   class="flex flex-col items-center justify-center min-h-[48px] px-2 rounded-full text-xs font-semibold transition-all duration-500 ease-spring active:scale-95 {{ request()->routeIs('karyawan.riwayat') ? 'bg-obsidian text-white shadow-sm' : 'text-muted hover:text-ink' }}">
                    <i class="ph-light ph-clock-counter-clockwise text-lg leading-none"></i>
                    <span class="text-[10px] mt-1 leading-none">Riwayat</span>
                </a>
            </div>
        @endif
    </div>
</nav>

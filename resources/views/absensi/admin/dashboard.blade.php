@extends('absensi.layouts.app')

@section('title', 'Command Center HRD')

@section('content')
<div class="space-y-6">

    <!-- =========================================================
         ROW 1: EXECUTIVE COMMAND HEADER + KPI METRIC PODS
         ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- Left (7 cols): Clean Executive HRD Command Card -->
        <section class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 sm:p-7 flex flex-col justify-between gap-6 reveal-item">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 bg-slate-100/90 px-3 py-1.5 rounded-lg border border-slate-200/70">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span id="bannerOfficeLabel">EXECUTIVE HRD · {{ strtoupper($lokasi->nama_lokasi) }} ({{ $lokasi->radius_meters }}M)</span>
                    </div>
                    <span id="adminCurrentDate" class="font-mono text-xs text-slate-500 bg-slate-50 px-2.5 py-1 rounded-md border border-slate-200/60 tabular-nums">
                        {{ now(config('attendance.timezone'))->locale('id')->translatedFormat('l, j M Y') }}
                    </span>
                </div>

                <div class="space-y-2">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Ringkasan Kehadiran &amp; Wilayah Kerja
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-xl">
                        Presensi hari ini mencatat <span class="font-semibold text-slate-900">38 dari 48 karyawan hadir</span>. Sistem memvalidasi koordinat GPS dalam radius {{ $lokasi->radius_meters }}m dan memverifikasi perizinan kerja.
                    </p>
                </div>

                <!-- Structured Mini Operational Indicators -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                    <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                        <div class="w-7 h-7 rounded-md bg-rose-50 border border-rose-200/60 text-rose-600 flex items-center justify-center shrink-0">
                            <i class="ph-bold ph-shield-warning text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-medium text-slate-800">2 Percobaan Ditolak</div>
                            <div class="text-[11px] text-slate-500 truncate">Presensi dari luar radius kantor</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                        <div class="w-7 h-7 rounded-md bg-amber-50 border border-amber-200/60 text-amber-600 flex items-center justify-center shrink-0">
                            <i class="ph-bold ph-file-text text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <div id="heroPendingLeave" class="font-medium text-slate-800">{{ $pendingLeaves->isEmpty() ? '0 Berkas Izin Menunggu' : $pendingLeaves->count().' Berkas Izin Menunggu' }}</div>
                            <div class="text-[11px] text-slate-500 truncate">Perlu keputusan persetujuan manajer</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operational Action Bar -->
            <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('geofenceMapCard').scrollIntoView({behavior: 'smooth'})"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white text-xs sm:text-sm font-semibold transition-all shadow-xs">
                    <i class="ph-bold ph-map-pin text-sm text-emerald-400"></i>
                    <span>Atur Titik Peta &amp; Geofence</span>
                </button>

                <button type="button" onclick="openQrModal()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 active:scale-[0.98] border border-slate-200 text-slate-700 text-xs sm:text-sm font-medium transition-all shadow-2xs">
                    <i class="ph-bold ph-qr-code text-sm text-slate-500"></i>
                    <span>Kelola QR Statis</span>
                </button>

                <button type="button" onclick="showToast('Ekspor Laporan CSV', 'Berkas rekap_kehadiran_bulanan.csv berhasil diunduh.', true)"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 active:scale-[0.98] border border-slate-200 text-slate-700 text-xs sm:text-sm font-medium transition-all shadow-2xs">
                    <i class="ph-bold ph-export text-sm text-slate-500"></i>
                    <span>Ekspor CSV</span>
                </button>
            </div>
        </section>

        <!-- Right (5 cols): Clean 2x2 Metric Grid -->
        <div class="lg:col-span-5 grid grid-cols-2 gap-4">

            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs reveal-item">
                <div class="text-xs font-semibold text-slate-500">Tepat Waktu</div>
                <div class="my-2">
                    <span class="text-2xl sm:text-3xl font-bold font-mono text-slate-900 tabular-nums">38</span>
                    <span class="text-xs font-medium text-slate-400 tabular-nums">/ 48</span>
                </div>
                <div class="text-[11px] font-medium text-emerald-700">79,1% Hadir &lt; 08:15</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs reveal-item">
                <div class="text-xs font-semibold text-slate-500">Terlambat</div>
                <div class="my-2">
                    <span class="text-2xl sm:text-3xl font-bold font-mono text-slate-900 tabular-nums">4</span>
                    <span class="text-xs font-medium text-slate-400">orang</span>
                </div>
                <div class="text-[11px] font-medium text-amber-700">Lewat Toleransi 15m</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs reveal-item">
                <div class="text-xs font-semibold text-slate-500">Izin &amp; Cuti</div>
                <div class="my-2">
                    <span id="kpiLeaveCount" class="text-2xl sm:text-3xl font-bold font-mono text-slate-900 tabular-nums">{{ $onLeaveTodayCount }}</span>
                    <span class="text-xs font-medium text-slate-400">orang</span>
                </div>
                <div id="kpiLeaveSub" class="text-[11px] font-medium {{ $pendingLeaves->isEmpty() ? 'text-emerald-700' : 'text-amber-700' }}">{{ $pendingLeaves->count() }} Menunggu Keputusan</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs reveal-item">
                <div class="text-xs font-semibold text-slate-500">Ditolak Geofence</div>
                <div class="my-2">
                    <span class="text-2xl sm:text-3xl font-bold font-mono text-rose-600 tabular-nums">2</span>
                    <span class="text-xs font-medium text-slate-400">blokir</span>
                </div>
                <div class="text-[11px] font-medium text-rose-700">Luar Radius &amp; IP Asing</div>
            </div>

        </div>

    </div>

    <!-- =========================================================
         ROW 1.5: INTERACTIVE GEOFENCE MAP & OFFICE PIN CONFIGURATOR (GOJEK / GMAPS STYLE)
         ========================================================= -->
    <section id="geofenceMapCard" class="bezel-shell">
        <div class="bezel-core p-6 sm:p-8 space-y-6">

            <!-- Header Strip -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-slate-900/[0.06]">
                <div>
                    <span class="eyebrow-tag">Geofence &amp; Titik Koordinat Kantor</span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight mt-1">
                        Peta Interaktif Penentu Lokasi &amp; Radius Presensi
                    </h2>
                    <p class="text-xs text-muted mt-1">
                        Cari alamat gedung, geser pin secara langsung seperti Gojek/Google Maps, atau klik titik mana saja di peta untuk menetapkan pusat radius Haversine.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span id="liveCoordPill" class="badge-pill badge-emerald font-mono tabular-nums">
                        <i class="ph-fill ph-map-pin text-sm"></i>
                        <span id="liveCoordBadgeText">{{ number_format($lokasi->latitude, 6, '.', '') }}, {{ number_format($lokasi->longitude, 6, '.', '') }}</span>
                    </span>
                    <span id="liveRadiusPill" class="badge-pill badge-neutral font-mono tabular-nums">
                        Radius: {{ $lokasi->radius_meters }} m
                    </span>
                </div>
            </div>

            <!-- 12-Col Bento: Left Interactive Map (7 cols) + Right Controls Form (5 cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Left (7 cols): Interactive Leaflet Map & Search Bar -->
                <div class="lg:col-span-7 space-y-3">

                    <!-- Search Bar & Map Layer Switcher -->
                    <div class="relative z-20 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <div class="relative flex-1">
                            <input type="search" id="mapSearchInput"
                                   placeholder="Cari gedung, jalan, atau kota (misal: Monas, Gedung Sate, Surabaya)..."
                                   autocomplete="off"
                                   class="haptic-input h-10 pl-9 pr-20 text-xs">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-muted pointer-events-none">
                                <i class="ph-light ph-magnifying-glass text-sm"></i>
                            </span>
                            <button type="button" id="btnSearchPlace"
                                    class="absolute inset-y-1 right-1 px-3 rounded-lg bg-obsidian text-white text-[11px] font-bold hover:bg-slate-800 transition-colors">
                                Cari
                            </button>

                            <!-- Dropdown Search Results -->
                            <div id="mapSearchResults" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl border border-slate-900/10 shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-900/[0.05] z-30"></div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" id="btnUseMyGps"
                                    class="inline-flex items-center justify-center gap-1.5 h-10 px-3.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-800 text-xs font-bold transition-colors"
                                    title="Gunakan titik GPS perangkat Anda saat ini">
                                <i class="ph-fill ph-gps-fix text-sm text-emerald-600"></i>
                                <span>GPS Saya</span>
                            </button>

                            <button type="button" id="btnToggleMapLayer"
                                    class="inline-flex items-center justify-center gap-1.5 h-10 px-3.5 rounded-xl bg-slate-900/[0.05] hover:bg-slate-900/10 text-ink text-xs font-bold transition-colors"
                                    title="Ganti tampilan Peta Jalan / Satelit">
                                <i class="ph-light ph-globe-hemisphere-east text-sm"></i>
                                <span id="mapLayerLabel">Satelit</span>
                            </button>
                        </div>
                    </div>

                    <!-- Map Canvas Enclosure -->
                    <div class="sub-tray p-2 relative">
                        <div class="relative w-full h-[360px] sm:h-[410px] rounded-xl overflow-hidden border border-slate-900/10 z-10 bg-slate-100/80">
                            <div id="adminGeofenceMap" class="w-full h-full"></div>
                            <div id="mapStatusIndicator" class="absolute inset-0 flex items-center justify-center bg-slate-100/90 transition-opacity duration-300 pointer-events-none z-20">
                                <div class="text-xs text-muted font-mono flex items-center gap-2">
                                    <span class="w-3.5 h-3.5 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></span>
                                    <span>Memuat peta interaktif...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Floating Pickup-Style Address Card -->
                        <div class="mt-2.5 px-3.5 py-2.5 rounded-xl bg-white border border-slate-900/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <span class="w-7 h-7 rounded-lg bg-emerald-500/15 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="ph-fill ph-map-pin-line text-sm"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-muted">Titik Pin Peta Aktif</div>
                                    <div id="reverseGeocodeText" class="text-xs font-bold text-ink truncate">
                                        Geser pin hijau atau klik pada peta untuk memuat alamat jalan...
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="btnCenterPin"
                                    class="text-xs font-bold text-accent hover:underline shrink-0 self-end sm:self-center">
                                Pusatkan Pin
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Right (5 cols): Coordinate & Geofence Parameter Form -->
                <div class="lg:col-span-5">
                    <form id="formUpdateLokasi" onsubmit="saveLokasiConfig(event)" class="sub-tray p-5 space-y-4">

                        <div class="space-y-1.5">
                            <label for="lokasiNamaInput" class="block text-xs font-bold uppercase tracking-wider text-muted">
                                Nama Kantor / Titik Presensi
                            </label>
                            <input type="text" id="lokasiNamaInput" name="nama_lokasi" required
                                   value="{{ $lokasi->nama_lokasi }}"
                                   placeholder="Contoh: Kantor Pusat Jakarta"
                                   class="haptic-input h-10 text-xs font-bold">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="lokasiLatInput" class="block text-xs font-bold uppercase tracking-wider text-muted">
                                    Latitude
                                </label>
                                <input type="number" step="any" id="lokasiLatInput" name="latitude" required
                                       value="{{ $lokasi->latitude }}"
                                       class="haptic-input h-10 text-xs font-mono tabular-nums">
                            </div>

                            <div class="space-y-1.5">
                                <label for="lokasiLngInput" class="block text-xs font-bold uppercase tracking-wider text-muted">
                                    Longitude
                                </label>
                                <input type="number" step="any" id="lokasiLngInput" name="longitude" required
                                       value="{{ $lokasi->longitude }}"
                                       class="haptic-input h-10 text-xs font-mono tabular-nums">
                            </div>
                        </div>

                        <div class="space-y-2 p-3.5 rounded-xl bg-white border border-slate-900/[0.05]">
                            <div class="flex items-center justify-between">
                                <label for="lokasiRadiusInput" class="text-xs font-bold text-ink">
                                    Radius Geofence Haversine
                                </label>
                                <div class="flex items-center gap-1.5">
                                    <input type="number" id="lokasiRadiusInput" name="radius_meters" min="10" max="5000" required
                                           value="{{ $lokasi->radius_meters }}"
                                           class="w-20 h-8 px-2 rounded-lg border border-slate-900/15 font-mono text-xs font-extrabold text-emerald-700 text-right tabular-nums">
                                    <span class="text-xs font-bold text-muted">m</span>
                                </div>
                            </div>
                            <input type="range" id="lokasiRadiusSlider" min="10" max="1000" step="5"
                                   value="{{ min(1000, max(10, $lokasi->radius_meters)) }}"
                                   class="w-full accent-emerald-600 cursor-pointer">
                            <div class="flex justify-between text-[10px] font-mono text-muted">
                                <span>10m (Ketat)</span>
                                <span>100m (Ideal Gedung)</span>
                                <span>500m+ (Kawasan)</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="lokasiQrTokenInput" class="block text-xs font-bold uppercase tracking-wider text-muted">
                                    Token QR Statis Kantor
                                </label>
                                <button type="button" onclick="generateNewQrTokenField()" class="text-[11px] font-bold text-accent hover:underline">
                                    + Acak Token Baru
                                </button>
                            </div>
                            <input type="text" id="lokasiQrTokenInput" name="qr_token" required
                                   value="{{ $lokasi->qr_token }}"
                                   class="haptic-input h-10 text-xs font-mono font-bold">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="lokasiAllowedIpsInput" class="block text-xs font-bold uppercase tracking-wider text-muted">
                                    Daftar IP Wi-Fi Kantor (Pisahkan Koma)
                                </label>
                                <button type="button" onclick="appendCurrentClientIp(@json(request()->ip()))" class="text-[11px] font-bold text-accent hover:underline">
                                    + IP Saya ({{ request()->ip() }})
                                </button>
                            </div>
                            <input type="text" id="lokasiAllowedIpsInput" name="allowed_ips"
                                   value="{{ implode(', ', $lokasi->allowed_ips ?? []) }}"
                                   placeholder="103.14.88.19, 127.0.0.1"
                                   class="haptic-input h-10 text-xs font-mono">
                        </div>

                        <div class="pt-1">
                            <button type="submit" id="btnSaveLokasi" class="btn-island-primary w-full">
                                <span id="btnSaveLokasiLabel">Simpan Titik Lokasi &amp; Geofence</span>
                                <span class="icon-nest">
                                    <i class="ph-light ph-floppy-disk text-base"></i>
                                </span>
                            </button>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </section>

    <!-- =========================================================
         ROW 2: LIVE ATTENDANCE AUDIT TABLE + APPROVAL & DEVICE RAIL
         ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left (8 cols): Live Verification Feed & Team Coverage Matrix -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Card 1: Real-time Verification Stream -->
            <section class="bezel-shell reveal-item">
                <div class="bezel-core p-6 sm:p-7 space-y-5">

                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="eyebrow-tag">Audit Kehadiran Real-Time</span>
                            <h2 class="text-xl font-extrabold text-ink tracking-tight mt-1">Log Verifikasi 8 Lapisan Hari Ini</h2>
                        </div>
                        <span id="adminRowCount" class="font-mono text-xs text-muted bg-slate-900/[0.04] px-3 py-1.5 rounded-full">
                            5 dari 45 catatan
                        </span>
                    </div>

                    <!-- Filter Pill Strip & Search -->
                    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3">
                        <div class="w-full xl:w-auto overflow-x-auto no-scrollbar scroll-smooth -mx-1 px-1 py-1" style="-webkit-overflow-scrolling: touch; touch-action: pan-x;">
                            <div class="inline-flex items-center gap-1.5 p-1.5 rounded-full bg-slate-900/[0.04] min-w-max whitespace-nowrap" role="tablist">
                                <button type="button" onclick="filterAdminLogs('semua', this)" class="admin-tab-btn island-link active py-2 px-3.5 text-xs font-semibold shrink-0 cursor-pointer">
                                    Semua (5)
                                </button>
                                <button type="button" onclick="filterAdminLogs('hadir', this)" class="admin-tab-btn island-link py-2 px-3.5 text-xs font-semibold shrink-0 cursor-pointer">
                                    Hadir (2)
                                </button>
                                <button type="button" onclick="filterAdminLogs('terlambat', this)" class="admin-tab-btn island-link py-2 px-3.5 text-xs font-semibold shrink-0 cursor-pointer">
                                    Telat (1)
                                </button>
                                <button type="button" onclick="filterAdminLogs('izin', this)" class="admin-tab-btn island-link py-2 px-3.5 text-xs font-semibold shrink-0 cursor-pointer">
                                    Izin (1)
                                </button>
                                <button type="button" onclick="filterAdminLogs('ditolak', this)" class="admin-tab-btn island-link py-2 px-3.5 text-xs font-semibold shrink-0 cursor-pointer">
                                    Ditolak (1)
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="relative flex-1 sm:w-56">
                                <input type="search" id="searchEmployeeInput" oninput="searchAdminLogs(this.value)"
                                       placeholder="Cari nama atau NIK..."
                                       class="haptic-input h-10 pl-9 text-xs">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-muted pointer-events-none">
                                    <i class="ph-light ph-magnifying-glass text-sm"></i>
                                </span>
                            </div>

                            <select id="divisionFilterSelect" onchange="filterAdminDivision(this.value)" class="haptic-input h-10 w-full sm:w-auto py-0 pr-7 text-xs font-semibold">
                                <option value="semua">Semua Divisi</option>
                                <option value="IT Development">IT Development</option>
                                <option value="Desain Produk">Desain Produk</option>
                                <option value="Keuangan">Keuangan</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tactile Employee Rows -->
                    <div class="space-y-3" id="adminAttendanceTable">

                        <!-- Row 1: Ahmad Fauzi -->
                        <div class="admin-log-row sub-tray p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-white transition-all duration-500 ease-spring"
                             data-status="hadir" data-division="IT Development" data-search="ahmad fauzi pkl-2026-081">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-obsidian text-emerald-400 font-bold text-xs flex items-center justify-center shrink-0">
                                    AF
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-extrabold text-ink">Ahmad Fauzi</span>
                                        <span class="badge-pill badge-emerald">Tepat Waktu</span>
                                    </div>
                                    <div class="text-xs text-muted mt-0.5">
                                        IT Development · <span class="font-mono">PKL-2026-081</span> · Samsung A54
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-5 pt-2.5 sm:pt-0 border-t sm:border-t-0 border-slate-900/[0.05]">
                                <div class="text-right">
                                    <div class="font-mono text-xs font-bold text-ink tabular-nums">07:52 WIB · 18 m</div>
                                    <div class="font-mono text-[11px] text-muted tabular-nums">IP 103.14.88.19</div>
                                </div>
                                <button type="button" onclick="showToast('Audit Log #ATT-081', 'Seluruh 8 lapisan validasi Ahmad Fauzi lolos pemeriksaan.', true)"
                                        class="btn-island-secondary py-1.5 pl-3.5 pr-1.5 text-xs">
                                    <span>Audit</span>
                                    <span class="icon-nest !w-7 !h-7">
                                        <i class="ph-light ph-shield-check text-xs"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Row 2: Nadya Putri -->
                        <div class="admin-log-row sub-tray p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-white transition-all duration-500 ease-spring"
                             data-status="hadir" data-division="IT Development" data-search="nadya putri pkl-2026-085">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-obsidian text-emerald-400 font-bold text-xs flex items-center justify-center shrink-0">
                                    NP
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-extrabold text-ink">Nadya Putri</span>
                                        <span class="badge-pill badge-emerald">Tepat Waktu</span>
                                    </div>
                                    <div class="text-xs text-muted mt-0.5">
                                        IT Development · <span class="font-mono">PKL-2026-085</span> · iPhone 13
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-5 pt-2.5 sm:pt-0 border-t sm:border-t-0 border-slate-900/[0.05]">
                                <div class="text-right">
                                    <div class="font-mono text-xs font-bold text-ink tabular-nums">07:49 WIB · 14 m</div>
                                    <div class="font-mono text-[11px] text-muted tabular-nums">IP 103.14.88.19</div>
                                </div>
                                <button type="button" onclick="showToast('Audit Log #ATT-085', 'Seluruh 8 lapisan validasi Nadya Putri lolos pemeriksaan.', true)"
                                        class="btn-island-secondary py-1.5 pl-3.5 pr-1.5 text-xs">
                                    <span>Audit</span>
                                    <span class="icon-nest !w-7 !h-7">
                                        <i class="ph-light ph-shield-check text-xs"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Row 3: Rian Pratama (Terlambat) -->
                        <div class="admin-log-row sub-tray p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-white transition-all duration-500 ease-spring"
                             data-status="terlambat" data-division="Desain Produk" data-search="rian pratama pkl-2026-092">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-amber-500/15 text-amber-800 font-bold text-xs flex items-center justify-center shrink-0">
                                    RP
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-extrabold text-ink">Rian Pratama</span>
                                        <span class="badge-pill badge-amber">Telat 22m</span>
                                    </div>
                                    <div class="text-xs text-muted mt-0.5">
                                        Desain Produk · <span class="font-mono">PKL-2026-092</span> · Redmi Note 12
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-5 pt-2.5 sm:pt-0 border-t sm:border-t-0 border-slate-900/[0.05]">
                                <div class="text-right">
                                    <div class="font-mono text-xs font-bold text-amber-800 tabular-nums">08:22 WIB · 34 m</div>
                                    <div class="font-mono text-[11px] text-muted tabular-nums">IP 103.14.88.19</div>
                                </div>
                                <button type="button" onclick="showToast('Catatan Keterlambatan', 'Rian Pratama masuk pukul 08:22 WIB (melewati toleransi 08:15).', false)"
                                        class="btn-island-secondary py-1.5 pl-3.5 pr-1.5 text-xs">
                                    <span>Audit</span>
                                    <span class="icon-nest !w-7 !h-7">
                                        <i class="ph-light ph-clock-Afternoon text-xs"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Row 4: Siti Nurhaliza (Izin) -->
                        <div class="admin-log-row sub-tray p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-white transition-all duration-500 ease-spring"
                             data-status="izin" data-division="Keuangan" data-search="siti nurhaliza pkl-2026-044">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-slate-900/5 text-ink font-bold text-xs flex items-center justify-center shrink-0">
                                    SN
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-extrabold text-ink">Siti Nurhaliza</span>
                                        <span id="rowSitiStatusPill" class="badge-pill badge-amber">Menunggu HRD</span>
                                    </div>
                                    <div class="text-xs text-muted mt-0.5">
                                        Keuangan · <span class="font-mono">PKL-2026-044</span> · Surat Dokter (2 Hari)
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-5 pt-2.5 sm:pt-0 border-t sm:border-t-0 border-slate-900/[0.05]">
                                <div class="text-right">
                                    <div class="font-mono text-xs font-bold text-ink tabular-nums">07:40 WIB</div>
                                    <div class="text-[11px] text-muted">1–2 Nov 2026</div>
                                </div>
                                <button type="button" onclick="document.getElementById('leaveApprovalCard').scrollIntoView({behavior: 'smooth'})"
                                        class="btn-island-secondary py-1.5 pl-3.5 pr-1.5 text-xs">
                                    <span>Tinjau</span>
                                    <span class="icon-nest !w-7 !h-7">
                                        <i class="ph-light ph-arrow-right text-xs"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Row 5: Dimas Aditya (Ditolak Server) -->
                        <div class="admin-log-row sub-tray p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-rose-500/20 bg-rose-500/[0.03] hover:bg-white transition-all duration-500 ease-spring"
                             data-status="ditolak" data-division="Marketing" data-search="dimas aditya pkl-2026-054">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-rose-500/15 text-rose-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    DA
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-extrabold text-ink">Dimas Aditya</span>
                                        <span class="badge-pill badge-rose">Ditolak Server</span>
                                    </div>
                                    <div class="text-xs text-rose-800 mt-0.5">
                                        Marketing · <span class="font-mono">PKL-2026-054</span> · Luar Radius &amp; IP Seluler
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-5 pt-2.5 sm:pt-0 border-t sm:border-t-0 border-slate-900/[0.05]">
                                <div class="text-right">
                                    <div class="font-mono text-xs font-bold text-rose-700 tabular-nums">07:59 WIB · 420 m</div>
                                    <div class="font-mono text-[11px] text-rose-600 tabular-nums">IP 180.244.12.91</div>
                                </div>
                                <button type="button" onclick="showToast('Anomali Lapisan 4 & 6', 'Ditolak otomatis: Jarak 420 m (>100 m) dan bukan IP Wi-Fi kantor.', false)"
                                        class="btn-island-secondary py-1.5 pl-3.5 pr-1.5 text-xs">
                                    <span>Blokir</span>
                                    <span class="icon-nest !w-7 !h-7 !bg-rose-500/15 !text-rose-700">
                                        <i class="ph-light ph-warning text-xs"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Empty State -->
                    <div id="adminEmptyState" class="hidden sub-tray py-10 text-center">
                        <p class="text-sm font-bold text-ink">Tidak ada catatan kehadiran yang cocok dengan filter ini.</p>
                        <p class="text-xs text-muted mt-1">Ubah kata kunci pencarian atau pilih tab Semua.</p>
                    </div>

                </div>
            </section>

        </div>

        <!-- Right (4 cols): Approval Action Queue & Hardware Reset -->
        <aside class="lg:col-span-4 space-y-6">

            <!-- Leave Approval Card -->
            <section id="leaveApprovalCard" class="bezel-shell reveal-item">
                <div class="bezel-core p-6 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-900/[0.06]">
                        <div>
                            <span class="eyebrow-tag">Keputusan Manajer</span>
                            <h2 class="text-base font-extrabold text-ink mt-1">Antrean Persetujuan Izin</h2>
                        </div>
                        <span id="approvalQueueBadge" class="badge-pill {{ $pendingLeaves->isEmpty() ? 'badge-emerald' : 'badge-amber' }}">{{ $pendingLeaves->isEmpty() ? 'Semua Selesai' : $pendingLeaves->count().' Menunggu' }}</span>
                    </div>

                    <div id="approvalList" class="space-y-3.5">
                        @forelse($pendingLeaves as $leave)
                            <div id="leaveCard-{{ $leave->id }}" class="approval-item sub-tray p-4 space-y-3.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="text-sm font-extrabold text-ink">{{ $leave->user->name }}</div>
                                        <div class="text-xs text-muted">{{ $leave->user->division }} · {{ $leave->jenis }} ({{ $leave->durationDays() }} Hari)</div>
                                    </div>
                                    <span class="font-mono text-xs text-muted">{{ $leave->created_at->timezone(config('attendance.timezone'))->format('d M H:i') }} WIB</span>
                                </div>

                                <div class="font-mono text-[11px] text-muted">{{ $leave->tgl_mulai->translatedFormat('j M Y') }} – {{ $leave->tgl_selesai->translatedFormat('j M Y') }}</div>

                                <p class="text-xs text-ink bg-white p-3 rounded-xl border border-slate-900/[0.05] leading-relaxed">"{{ $leave->alasan }}"</p>

                                @if($leave->foto_bukti)
                                    <a href="{{ route('admin.izin.bukti', $leave) }}" target="_blank" rel="noopener"
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-900/[0.06] flex items-center justify-between text-xs hover:border-slate-900/20 transition-colors">
                                        <span class="flex items-center gap-2 font-bold text-accent truncate">
                                            <i class="ph-light ph-file-image text-base"></i>
                                            <span>{{ basename($leave->foto_bukti) }}</span>
                                        </span>
                                        <span class="font-mono text-muted shrink-0">Buka</span>
                                    </a>
                                @endif

                                <div class="leave-actions grid grid-cols-2 gap-2.5 pt-1">
                                    <button type="button" onclick="decideLeave({{ $leave->id }}, true)" class="btn-island-primary w-full py-1.5 pl-4 pr-1.5 text-xs">
                                        <span>Setujui</span>
                                        <span class="icon-nest !w-7 !h-7">
                                            <i class="ph-light ph-check text-xs"></i>
                                        </span>
                                    </button>
                                    <button type="button" onclick="decideLeave({{ $leave->id }}, false)" class="btn-island-secondary w-full py-1.5 pl-4 pr-1.5 text-xs">
                                        <span>Tolak</span>
                                        <span class="icon-nest !w-7 !h-7 !bg-rose-500/10 !text-rose-700">
                                            <i class="ph-light ph-x text-xs"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div id="approvalEmpty" class="sub-tray p-4 text-xs font-semibold text-emerald-700">Tidak ada pengajuan izin yang menunggu keputusan.</div>
                        @endforelse
                    </div>
                </div>
            </section>
            <!-- Reset Device Binding Card -->
            <section class="bezel-shell reveal-item">
                <div class="bezel-core p-6 space-y-4">
                    <div class="pb-3 border-b border-slate-900/[0.06]">
                        <span class="eyebrow-tag">Otoritas Perangkat</span>
                        <h2 class="text-base font-extrabold text-ink mt-1">Reset Device Binding</h2>
                        <p class="text-xs text-muted mt-0.5">Buka kunci hardware jika karyawan mengganti ponsel baru.</p>
                    </div>

                    <form onsubmit="handleDeviceReset(event)" class="space-y-3.5">
                        <div class="space-y-1.5">
                            <label for="resetDeviceNik" class="block text-xs font-bold uppercase tracking-wider text-muted">NIK / Nama Karyawan</label>
                            <input type="text" id="resetDeviceNik" required placeholder="Contoh: PKL-2026-081" class="haptic-input h-10 text-xs">
                        </div>

                        <div class="space-y-1.5">
                            <label for="resetDeviceReason" class="block text-xs font-bold uppercase tracking-wider text-muted">Alasan Otorisasi</label>
                            <select id="resetDeviceReason" class="haptic-input h-10 text-xs py-0">
                                <option value="Pergantian perangkat baru">Pergantian perangkat baru</option>
                                <option value="Reset browser / sistem operasi">Reset browser / sistem operasi</option>
                                <option value="Kehilangan perangkat lama">Kehilangan perangkat lama</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-island-secondary w-full">
                            <span>Reset Kunci Perangkat</span>
                            <span class="icon-nest">
                                <i class="ph-light ph-key text-sm"></i>
                            </span>
                        </button>
                    </form>
                </div>
            </section>

        </aside>

    </div>

    <!-- Modal Kelola QR Statis (Double-Bezel Dialog - Mobile Bottom Sheet Ergonomics) -->
    <div id="modalQr" role="dialog" aria-modal="true" aria-labelledby="modalQrTitle" class="fixed inset-0 z-50 hidden bg-obsidian/60 backdrop-blur-md items-end sm:items-center justify-center p-3 sm:p-4">
        <div class="bezel-shell w-full max-w-md max-h-[90dvh] overflow-y-auto" id="modalQrBox">
            <div class="bezel-core p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-900/[0.06]">
                    <div>
                        <span class="eyebrow-tag">Kriptografi Lokasi</span>
                        <h2 id="modalQrTitle" class="text-lg font-extrabold text-ink mt-1">QR Statis Kantor Pusat</h2>
                    </div>
                    <button type="button" onclick="closeQrModal()" class="w-8 h-8 rounded-full bg-slate-900/5 hover:bg-slate-900/10 flex items-center justify-center text-muted">
                        <i class="ph-light ph-x text-base"></i>
                    </button>
                </div>

                <div class="sub-tray p-6 text-center space-y-4">
                    <!-- Standard ISO/IEC 18004 2D QR Code Matrix Container -->
                    <div class="w-52 h-52 bg-white border border-slate-900/10 rounded-2xl mx-auto flex flex-col items-center justify-center p-3 shadow-lg">
                        <div id="modalQrCodeContainer" class="flex items-center justify-center [&_img]:mx-auto [&_canvas]:mx-auto"></div>
                    </div>
                    <div>
                        <div id="modalQrOfficeName" class="text-sm font-extrabold text-ink">{{ $lokasi->nama_lokasi }}</div>
                        <div id="activeQrTokenText" class="font-mono text-xs text-muted mt-0.5 font-bold">Token: {{ $lokasi->qr_token }}</div>
                        <p class="text-[11px] text-muted mt-1">Pindai kode QR matriks di atas menggunakan pemindai kamera karyawan.</p>
                    </div>
                    <span class="badge-pill badge-emerald">Aktif &amp; Terenkripsi</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="printQrSheet()" class="btn-island-secondary w-full">
                        <span>Cetak / PDF</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-printer text-sm"></i>
                        </span>
                    </button>
                    <button type="button" onclick="rotateQrToken()" class="btn-island-primary w-full">
                        <span>Rotasi Token</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-arrows-clockwise text-sm"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Immediate Fast-Path Script for Modal Ergonomics -->
    <script>
        function openQrModal() {
            const m = document.getElementById('modalQr');
            if (m) {
                m.classList.remove('hidden');
                m.style.display = 'flex';
            }
            if (typeof window.renderModalQr === 'function') {
                const tokenInput = document.getElementById('lokasiQrTokenInput');
                window.renderModalQr(tokenInput ? tokenInput.value : @json($lokasi->qr_token));
            }
        }
        function closeQrModal() {
            const m = document.getElementById('modalQr');
            if (m) {
                m.classList.add('hidden');
                m.style.display = 'none';
            }
        }
    </script>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
<style>
    #modalQr:not(.hidden) {
        display: flex !important;
    }
    #modalQr.hidden {
        display: none !important;
    }
    .gojek-pin-marker {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .gojek-pin-core {
        width: 36px;
        height: 36px;
        border-radius: 9999px 9999px 9999px 4px;
        transform: rotate(-45deg);
        background: #0B0F14;
        border: 3px solid #10B981;
        box-shadow: 0 10px 20px rgba(11, 15, 20, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .gojek-pin-dot {
        width: 12px;
        height: 12px;
        border-radius: 9999px;
        background: #34D399;
    }
</style>
@endpush

@push('scripts')
<script>
    window.HADIR_ADMIN_CONFIG = {
        updateLokasiUrl: @json(route('admin.lokasi.update')),
        leaveDecideUrl: @json(route('admin.izin.decide', ['izin' => '__ID__'])),
        csrfToken: @json(csrf_token()),
        qrToken: @json($lokasi->qr_token),
        officeName: @json($lokasi->nama_lokasi),
        latitude: {{ (float) $lokasi->latitude }},
        longitude: {{ (float) $lokasi->longitude }},
        radiusMeters: {{ (int) $lokasi->radius_meters }}
    };
</script>
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('vendor/qrcode/qrcode.min.js') }}"></script>
<script src="{{ asset('js/admin-dashboard.js') }}?v={{ time() }}"></script>
@endpush

@extends('layouts.app')

@section('title', 'Presensi Hari Ini')

@section('content')
<div class="space-y-6">

    <!-- =========================================================
         ROW 1: LIVE SHIFT STATUS + KPI METRIC PODS
         ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- Left (7 cols): Clean Shift & Attendance Core -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 sm:p-7 flex flex-col justify-between gap-6 reveal-item">

            <!-- Top Row: Shift Info & Status Badge -->
            <div class="flex flex-wrap items-center justify-between gap-2.5">
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 bg-slate-100/90 px-3 py-1.5 rounded-lg border border-slate-200/70">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>SHIFT PAGI · 08:00 — 17:00 WIB</span>
                </div>
                @if ($today === null)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/70">
                        <i class="ph-bold ph-hourglass-medium text-xs text-amber-500"></i>
                        <span>Belum Presensi</span>
                    </span>
                @elseif ($today->isLate())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200/80">
                        <i class="ph-bold ph-clock-countdown text-xs text-amber-600"></i>
                        <span>Terlambat {{ $today->late_minutes }} Menit</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                        <i class="ph-bold ph-check-circle text-xs text-emerald-600"></i>
                        <span>Hadir Tepat Waktu</span>
                    </span>
                @endif
            </div>

            <!-- Center Row: Clock & Geofence Distance Card -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center py-1">
                <div class="sm:col-span-7 space-y-2 text-center sm:text-left">
                    <div class="text-xs font-medium text-slate-500 flex items-center justify-center sm:justify-start gap-1.5">
                        <i class="ph-bold ph-calendar-blank text-slate-400"></i>
                        <span id="currentDate">Minggu, 1 November 2026</span>
                    </div>
                    <div id="liveClock" class="text-3xl sm:text-4xl font-bold font-mono tracking-tight text-slate-900 tabular-nums">
                        08:14:25
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed max-w-md mx-auto sm:mx-0">
                        @if ($today === null)
                            Belum ada presensi masuk hari ini. Buka pemindai QR di kantor untuk memvalidasi kehadiran.
                        @else
                            Presensi masuk tercatat pukul <strong class="text-slate-900 font-mono">{{ $today->masukLocal()->format('H:i') }} WIB</strong>@if ($today->isLate()) (terlambat {{ $today->late_minutes }} menit)@else (tepat waktu)@endif.
                        @endif
                        Sesi absen pulang dibuka pukul <strong class="text-slate-900 font-mono">{{ config('attendance.checkout_opens_at') }} WIB</strong>.
                    </p>
                </div>

                <!-- Clean Geofence Distance Box -->
                <div class="sm:col-span-5 flex items-center justify-center">
                    <div class="w-full sm:w-auto min-w-[150px] bg-slate-50 border border-slate-200/70 rounded-xl p-4 text-center">
                        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jarak Kantor</div>
                        <div class="font-mono text-2xl font-bold text-slate-900 tabular-nums my-1">
                            {{ $today?->latestDistance() !== null ? number_format($today->latestDistance(), 0, ',', '.').' m' : '— m' }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Maks. Radius ≤ {{ number_format(config('attendance.radius_meters'), 0, ',', '.') }}m
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-200/60">
                            @if ($today?->latestDistance() !== null && $today->latestDistance() <= config('attendance.radius_meters'))
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                                    <i class="ph-bold ph-check text-xs"></i> Dalam Radius
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600">
                                    <i class="ph-bold ph-navigation-arrow text-xs text-slate-400"></i> GPS Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Check-in / Check-out Sub-Trays + Action Buttons -->
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-slate-50/80 border border-slate-200/70 rounded-xl p-3.5 flex sm:block items-center justify-between">
                        <div>
                            <div class="flex items-center gap-1.5 text-xs font-medium text-slate-600">
                                <span>Presensi Masuk</span>
                                @if ($today !== null)
                                    <i class="ph-bold ph-check-circle text-emerald-600 text-sm"></i>
                                @else
                                    <i class="ph-bold ph-hourglass text-amber-500 text-xs"></i>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $today !== null ? 'Tercatat · '.number_format($today->masuk_distance, 0, ',', '.').' m dari kantor' : 'Mulai '.config('attendance.shift_start').' WIB' }}</div>
                        </div>
                        <div class="font-mono text-base font-bold text-slate-900 sm:mt-1 tabular-nums">{{ $today !== null ? $today->masukLocal()->format('H:i:s').' WIB' : '--:-- WIB' }}</div>
                    </div>

                    <div class="bg-slate-50/80 border border-slate-200/70 rounded-xl p-3.5 flex sm:block items-center justify-between">
                        <div>
                            <div class="flex items-center gap-1.5 text-xs font-medium text-slate-600">
                                <span>Presensi Pulang</span>
                                @if ($today?->pulang_at !== null)
                                    <i class="ph-bold ph-check-circle text-emerald-600 text-sm"></i>
                                @else
                                    <i class="ph-bold ph-hourglass text-amber-500 text-xs"></i>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $today?->pulang_at !== null ? 'Tercatat · '.number_format($today->pulang_distance, 0, ',', '.').' m dari kantor' : 'Aktif mulai '.config('attendance.checkout_opens_at').' WIB' }}</div>
                        </div>
                        <div class="font-mono text-base font-bold text-slate-900 sm:mt-1 tabular-nums">{{ $today?->pulang_at !== null ? $today->pulangLocal()->format('H:i:s').' WIB' : '--:-- WIB' }}</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 pt-2 border-t border-slate-100">
                    <a href="{{ route('karyawan.scan') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white text-xs sm:text-sm font-semibold transition-all shadow-xs flex-1">
                        <i class="ph-bold ph-qr-code text-base text-emerald-400"></i>
                        <span>Buka Pemindai QR Presensi</span>
                    </a>

                    <a href="{{ route('karyawan.izin') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 active:scale-[0.98] border border-slate-200 text-slate-700 text-xs sm:text-sm font-medium transition-all shadow-2xs">
                        <i class="ph-bold ph-file-plus text-base text-slate-500"></i>
                        <span>Ajukan Izin / Cuti</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Right (5 cols): Clean 2x2 Metric Grid -->
        <div class="lg:col-span-5 grid grid-cols-2 gap-4">

            <!-- KPI Pod 1 -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs reveal-item">
                <div class="text-xs font-semibold text-slate-500">Kehadiran Bulan Ini</div>
                <div class="my-2">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-bold text-slate-900 font-mono tabular-nums tracking-tight">{{ $presentDays }}</span>
                        <span class="text-xs font-medium text-slate-400 tabular-nums">/ {{ $workingDays }} hari</span>
                    </div>
                    <div class="w-full h-1.5 rounded-full bg-slate-100 mt-2.5 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $workingDays > 0 ? min(100, round($presentDays / $workingDays * 100)) : 0 }}%"></div>
                    </div>
                </div>
                <div class="text-[11px] font-medium text-emerald-700">{{ number_format($workingDays > 0 ? $presentDays / $workingDays * 100 : 0, 1, ',', '.') }}% Rasio Hadir · {{ $monthShort }}</div>
            </div>

            <!-- KPI Pod 2 -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs reveal-item">
                <div class="text-xs font-semibold text-slate-500">Keterlambatan</div>
                <div class="my-2">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-bold text-slate-900 font-mono tabular-nums tracking-tight">{{ $lateRecords->count() }}</span>
                        <span class="text-xs font-medium text-slate-400">sesi</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Toleransi shift {{ config('attendance.late_tolerance') }} WIB</div>
                </div>
                <div class="text-[11px] font-medium text-amber-700">
                    @if ($lateRecords->isNotEmpty())
                        {{ $lateRecords->first()->attendance_date->locale('id')->translatedFormat('j M') }} · Masuk {{ $lateRecords->first()->masukLocal()->format('H:i') }}
                    @else
                        Tidak ada keterlambatan
                    @endif
                </div>
            </div>

            <!-- KPI Pod 3 -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs reveal-item">
                <div class="text-xs font-semibold text-slate-500">Kuota Cuti</div>
                <div class="my-2">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-bold text-slate-900 font-mono tabular-nums tracking-tight">{{ $remainingAnnualLeave }}</span>
                        <span class="text-xs font-medium text-slate-400">hari sisa</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Alokasi {{ \App\Models\PengajuanIzin::ANNUAL_LEAVE_QUOTA }} hari / tahun</div>
                </div>
                <div class="text-[11px] font-medium text-slate-600">{{ $usedAnnualDays > 0 ? 'Terpakai '.$usedAnnualDays.' hari tahun ini' : 'Belum ada cuti terpakai' }}</div>
            </div>

            <!-- KPI Pod 4 -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-xs reveal-item">
                <div class="text-xs font-semibold text-slate-500">Akurasi GPS</div>
                <div class="my-2">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-bold text-slate-900 font-mono tabular-nums tracking-tight">{{ $latestRecord !== null ? '±'.number_format($latestRecord->pulang_accuracy ?? $latestRecord->masuk_accuracy, 1, ',', '.') : '—' }}</span>
                        <span class="text-xs font-medium text-slate-400">meter</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Batas presisi ≤ {{ number_format(config('attendance.max_accuracy_meters'), 1, ',', '.') }} m</div>
                </div>
                <div class="text-[11px] font-medium text-emerald-700">{{ $latestRecord !== null ? 'Presisi GPS terverifikasi' : 'Belum ada data GPS' }}</div>
            </div>

        </div>

    </div>

    <!-- =========================================================
         ROW 2: 8-LAYER TELEMETRY LEDGER + 7-DAY ARRIVAL RHYTHM & ACTIVITY RAIL
         ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left (8 cols): 7-Day Arrival Rhythm & 8-Layer Security Matrix -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Arrival Rhythm Visual Chart -->
            <section class="bezel-shell reveal-item">
                <div class="bezel-core p-6 sm:p-7 space-y-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <span class="eyebrow-tag">Analisis Kedisiplinan</span>
                            <h2 class="text-lg font-extrabold text-ink tracking-tight mt-1.5">
                                Rata-rata kedatangan pukul 07:54 WIB (7 Hari Kerja)
                            </h2>
                            <p class="text-xs text-muted mt-0.5">
                                Garis horizontal putus-putus menandai batas toleransi keterlambatan pukul 08:15 WIB.
                            </p>
                        </div>
                        <div class="flex items-center gap-3 text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 text-ink">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                                <span>Tepat Waktu</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-amber-800">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span>Lewat 08:15</span>
                            </span>
                        </div>
                    </div>

                    <div class="sub-tray p-4 sm:p-5 overflow-x-auto no-scrollbar">
                        <div class="min-w-[520px]">
                        <svg viewBox="0 0 680 185" class="w-full h-44 overflow-visible" role="img" aria-label="Grafik jam masuk 7 hari terakhir">
                            <defs>
                                <linearGradient id="barTealGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#0D9488" />
                                    <stop offset="100%" stop-color="#0F766E" />
                                </linearGradient>
                                <linearGradient id="barAmberGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#F59E0B" />
                                    <stop offset="100%" stop-color="#D97706" />
                                </linearGradient>
                            </defs>

                            <line x1="56" y1="24" x2="664" y2="24" stroke="rgba(15,20,25,0.06)" stroke-dasharray="3 3" stroke-width="1" />
                            <line x1="56" y1="68" x2="664" y2="68" stroke="#D97706" stroke-dasharray="5 4" stroke-width="1.2" />
                            <line x1="56" y1="112" x2="664" y2="112" stroke="rgba(15,20,25,0.06)" stroke-dasharray="3 3" stroke-width="1" />
                            <line x1="56" y1="154" x2="664" y2="154" stroke="rgba(15,20,25,0.1)" stroke-width="1" />

                            <text x="0" y="28" fill="#53606E" font-size="11.5" font-family="JetBrains Mono, monospace">08:30</text>
                            <text x="0" y="72" fill="#B45309" font-size="11.5" font-weight="600" font-family="JetBrains Mono, monospace">08:15</text>
                            <text x="0" y="116" fill="#53606E" font-size="11.5" font-family="JetBrains Mono, monospace">07:55</text>
                            <text x="0" y="158" fill="#53606E" font-size="11.5" font-family="JetBrains Mono, monospace">07:35</text>

                            <!-- 23 Okt -->
                            <rect x="82" y="100" width="42" height="54" rx="8" fill="url(#barTealGrad)" />
                            <text x="103" y="92" text-anchor="middle" fill="#0F1419" font-size="11.5" font-weight="600" font-family="JetBrains Mono, monospace">07:50</text>
                            <text x="103" y="174" text-anchor="middle" fill="#53606E" font-size="11.5" font-weight="600">23 Okt</text>

                            <!-- 26 Okt -->
                            <rect x="168" y="103" width="42" height="51" rx="8" fill="url(#barTealGrad)" />
                            <text x="189" y="95" text-anchor="middle" fill="#0F1419" font-size="11.5" font-weight="600" font-family="JetBrains Mono, monospace">07:49</text>
                            <text x="189" y="174" text-anchor="middle" fill="#53606E" font-size="11.5" font-weight="600">26 Okt</text>

                            <!-- 27 Okt -->
                            <rect x="254" y="90" width="42" height="64" rx="8" fill="url(#barTealGrad)" />
                            <text x="275" y="82" text-anchor="middle" fill="#0F1419" font-size="11.5" font-weight="600" font-family="JetBrains Mono, monospace">07:55</text>
                            <text x="275" y="174" text-anchor="middle" fill="#53606E" font-size="11.5" font-weight="600">27 Okt</text>

                            <!-- 29 Okt -->
                            <rect x="340" y="98" width="42" height="56" rx="8" fill="url(#barTealGrad)" />
                            <text x="361" y="90" text-anchor="middle" fill="#0F1419" font-size="11.5" font-weight="600" font-family="JetBrains Mono, monospace">07:51</text>
                            <text x="361" y="174" text-anchor="middle" fill="#53606E" font-size="11.5" font-weight="600">29 Okt</text>

                            <!-- 30 Okt (Terlambat) -->
                            <rect x="426" y="40" width="42" height="114" rx="8" fill="url(#barAmberGrad)" />
                            <text x="447" y="32" text-anchor="middle" fill="#92400E" font-size="11.5" font-weight="700" font-family="JetBrains Mono, monospace">08:24</text>
                            <text x="447" y="174" text-anchor="middle" fill="#92400E" font-size="11.5" font-weight="700">30 Okt</text>

                            <!-- 31 Okt -->
                            <rect x="512" y="106" width="42" height="48" rx="8" fill="url(#barTealGrad)" />
                            <text x="533" y="98" text-anchor="middle" fill="#0F1419" font-size="11.5" font-weight="600" font-family="JetBrains Mono, monospace">07:48</text>
                            <text x="533" y="174" text-anchor="middle" fill="#53606E" font-size="11.5" font-weight="600">31 Okt</text>

                            <!-- 1 Nov -->
                            <rect x="598" y="96" width="42" height="58" rx="8" fill="#0B0F14" />
                            <text x="619" y="88" text-anchor="middle" fill="#0F1419" font-size="11.5" font-weight="700" font-family="JetBrains Mono, monospace">07:52</text>
                            <text x="619" y="174" text-anchor="middle" fill="#0F1419" font-size="11.5" font-weight="800">Hari Ini</text>
                        </svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8-Layer Verification Ledger -->
            <section class="bezel-shell reveal-item">
                <div class="bezel-core p-6 sm:p-7 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="eyebrow-tag">Audit Telemetri Server</span>
                            <h2 class="text-lg font-extrabold text-ink tracking-tight mt-1">
                                Bukti Pemeriksaan 8 Lapisan Keamanan
                            </h2>
                        </div>
                        <span class="font-mono text-xs text-muted bg-slate-900/[0.04] px-3 py-1.5 rounded-full">
                            LOG #ATT-20261101-081
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="sub-tray p-3.5 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-ink">1. Token QR Lokasi</div>
                                <div class="font-mono text-xs text-muted mt-0.5">LOC-PST-99A</div>
                            </div>
                            <span class="badge-pill badge-emerald">Cocok</span>
                        </div>

                        <div class="sub-tray p-3.5 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-ink">2. Device Binding</div>
                                <div class="font-mono text-xs text-muted mt-0.5">dev-a54-98a3</div>
                            </div>
                            <span class="badge-pill badge-emerald">Terikat</span>
                        </div>

                        <div class="sub-tray p-3.5 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-ink">3. IP Publik Wi-Fi Kantor</div>
                                <div class="font-mono text-xs text-muted mt-0.5 tabular-nums">103.14.88.19</div>
                            </div>
                            <span class="badge-pill badge-emerald">Subnet Sah</span>
                        </div>

                        <div class="sub-tray p-3.5 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-ink">4. Jarak Haversine</div>
                                <div class="font-mono text-xs text-muted mt-0.5 tabular-nums">18,0 m (≤ 100 m)</div>
                            </div>
                            <span class="badge-pill badge-emerald">Dalam Radius</span>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <!-- Right (4 cols): Recent Activity & Bound Hardware Card -->
        <aside class="lg:col-span-4 space-y-6">

            <!-- Activity Feed -->
            <section class="bezel-shell reveal-item">
                <div class="bezel-core p-6 space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="eyebrow-tag">Jurnal Presensi</span>
                            <h2 class="text-base font-extrabold text-ink tracking-tight mt-1">Aktivitas Terakhir</h2>
                        </div>
                        <span class="font-mono text-xs text-muted">4 Log</span>
                    </div>

                    <div class="space-y-2.5">
                        <div class="sub-tray p-3.5 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold text-ink">Absen Masuk (Hari Ini)</div>
                                <div class="font-mono text-[11px] text-muted mt-0.5">1 Nov · 07:52 WIB · 18 m</div>
                            </div>
                            <span class="badge-pill badge-emerald">Valid</span>
                        </div>

                        <div class="sub-tray p-3.5 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold text-ink">Absen Pulang</div>
                                <div class="font-mono text-[11px] text-muted mt-0.5">31 Okt · 17:04 WIB · 9j 16m</div>
                            </div>
                            <span class="badge-pill badge-neutral">Selesai</span>
                        </div>

                        <div class="sub-tray p-3.5 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold text-ink">Absen Masuk (Telat)</div>
                                <div class="font-mono text-[11px] text-muted mt-0.5">30 Okt · 08:24 WIB · +24m</div>
                            </div>
                            <span class="badge-pill badge-amber">Telat</span>
                        </div>

                        <div class="sub-tray p-3.5 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold text-ink">Cuti Tahunan</div>
                                <div class="font-mono text-[11px] text-muted mt-0.5">28 Okt · 1 Hari · HRD</div>
                            </div>
                            <span class="badge-pill badge-emerald">Disetujui</span>
                        </div>
                    </div>

                    <a href="{{ route('karyawan.riwayat') }}" class="btn-island-secondary w-full">
                        <span>Lihat Seluruh Riwayat (22)</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-arrow-right text-sm"></i>
                        </span>
                    </a>
                </div>
            </section>

            <!-- Bound Device Identity -->
            <section class="bezel-shell reveal-item">
                <div class="bezel-core p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-2xl bg-obsidian text-emerald-400 flex items-center justify-center">
                                <i class="ph-light ph-device-mobile-camera text-lg"></i>
                            </span>
                            <div>
                                <h2 class="text-sm font-extrabold text-ink">Perangkat Terikat</h2>
                                <p class="text-[11px] text-muted">Kunci Hardware Aktif</p>
                            </div>
                        </div>
                        <span class="badge-pill badge-emerald">Terkunci</span>
                    </div>

                    <div class="sub-tray p-3.5 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-muted">Perangkat</span>
                            <span class="font-bold text-ink">Samsung Galaxy A54</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Device Hash</span>
                            <span class="font-mono text-ink">dev-a54-98a3</span>
                        </div>
                    </div>
                </div>
            </section>

        </aside>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function updateLiveClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
        const dateEl = document.getElementById('currentDate');
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();
</script>
@endpush

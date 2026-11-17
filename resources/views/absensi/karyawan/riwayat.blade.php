@extends('absensi.layouts.app')

@section('title', 'Riwayat Kehadiran')

@section('content')
<div class="space-y-6">

    <!-- Top 4-Pod Bento Summary Strip -->
    <section aria-label="Ringkasan Kehadiran Periode Berjalan" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bezel-shell reveal-item">
            <div class="bezel-core p-5">
                <span class="text-xs font-bold text-muted">Total Hari Hadir</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold font-mono text-ink tabular-nums">{{ $records->count() }}</span>
                    <span class="text-xs font-semibold text-muted">hari</span>
                </div>
                <div class="mt-2 text-[11px] font-semibold text-emerald-700">{{ $records->count() - $lateRecords->count() }} Hari Tepat Waktu</div>
            </div>
        </div>

        <div class="bezel-shell reveal-item">
            <div class="bezel-core p-5">
                <span class="text-xs font-bold text-muted">Akumulasi Durasi</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold font-mono text-ink tabular-nums">{{ number_format($totalMinutes / 60, 1, ',', '.') }}</span>
                    <span class="text-xs font-semibold text-muted">jam kerja</span>
                </div>
                <div class="mt-2 text-[11px] font-semibold text-muted">Rata-rata {{ intdiv($averageMinutes, 60) }}j {{ str_pad($averageMinutes % 60, 2, '0', STR_PAD_LEFT) }}m / hari</div>
            </div>
        </div>

        <div class="bezel-shell reveal-item">
            <div class="bezel-core p-5">
                <span class="text-xs font-bold text-muted">Frekuensi Telat</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold font-mono text-ink tabular-nums">{{ $lateRecords->count() }}</span>
                    <span class="text-xs font-semibold text-muted">sesi</span>
                </div>
                <div class="mt-2 text-[11px] font-semibold text-amber-800">
                    @if ($lateRecords->isNotEmpty())
                        {{ $lateRecords->first()->attendance_date->locale('id')->translatedFormat('j M Y') }} (+{{ $lateRecords->first()->late_minutes }}m)
                    @else
                        Tidak ada keterlambatan
                    @endif
                </div>
            </div>
        </div>

        <div class="bezel-shell reveal-item">
            <div class="bezel-core p-5">
                <span class="text-xs font-bold text-muted">Cuti &amp; Izin Resmi</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold font-mono text-ink tabular-nums">{{ $totalLeaveDays }}</span>
                    <span class="text-xs font-semibold text-muted">hari</span>
                </div>
                <div class="mt-2 text-[11px] font-semibold text-muted">
                    @if ($approvedLeaves->isNotEmpty())
                        {{ $approvedLeaves->count() }} disetujui · {{ $leaveRecords->count() }} total pengajuan
                    @elseif ($leaveRecords->isNotEmpty())
                        {{ $leaveRecords->count() }} pengajuan ({{ $leaveRecords->first()->status_izin }})
                    @else
                        Belum ada pengajuan tercatat
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Main Log Ledger Enclosure -->
    <section class="bezel-shell reveal-item">
        <div class="bezel-core p-6 sm:p-8 space-y-6">

            <!-- Header & Period Filter -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-slate-900/[0.06]">
                <div>
                    <span class="eyebrow-tag">Buku Besar Presensi</span>
                    <h1 class="text-2xl font-extrabold text-ink tracking-tight mt-1.5">Riwayat Kehadiran &amp; Audit Log</h1>
                    <p class="text-xs text-muted mt-1">
                        Rekaman lengkap waktu server, durasi kerja, jarak Haversine GPS, serta riwayat perizinan kerja.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select id="monthPeriodSelect"
                            class="haptic-input h-10 w-auto py-0 pr-8 text-xs font-bold">
                        <option value="semua">Seluruh Riwayat</option>
                    </select>

                    <button type="button" onclick="showToast('Unduh Rekap PDF', 'Berkas rekap_kehadiran_ahmad_fauzi.pdf siap diunduh.', true)" class="btn-island-secondary">
                        <span>Unduh Rekap PDF</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-download-simple text-sm"></i>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Filter Island Bar & Search -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 w-full min-w-0 max-w-full">
                <div id="historyFilterScroll" class="w-full min-w-0 max-w-full overflow-x-auto no-scrollbar scroll-smooth py-1"
                     style="max-width: 100%; min-width: 0; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; touch-action: pan-x;">
                    <div class="inline-flex items-center gap-1.5 p-1.5 rounded-full bg-slate-900/[0.04] whitespace-nowrap"
                         style="width: max-content; min-width: max-content; touch-action: pan-x;">
                        <button type="button" onclick="filterHistory('semua', this)"
                                class="history-filter-chip island-link active py-2 px-4 text-xs font-semibold shrink-0 cursor-pointer"
                                style="touch-action: pan-x;">
                            Semua ({{ $records->count() + $leaveRecords->count() }})
                        </button>
                        <button type="button" onclick="filterHistory('hadir', this)"
                                class="history-filter-chip island-link py-2 px-4 text-xs font-semibold shrink-0 cursor-pointer"
                                style="touch-action: pan-x;">
                            Tepat Waktu ({{ $records->count() - $lateRecords->count() }})
                        </button>
                        <button type="button" onclick="filterHistory('telat', this)"
                                class="history-filter-chip island-link py-2 px-4 text-xs font-semibold shrink-0 cursor-pointer"
                                style="touch-action: pan-x;">
                            Terlambat ({{ $lateRecords->count() }})
                        </button>
                        <button type="button" onclick="filterHistory('izin', this)"
                                class="history-filter-chip island-link py-2 px-4 text-xs font-semibold shrink-0 cursor-pointer"
                                style="touch-action: pan-x;">
                            Izin &amp; Cuti ({{ $leaveRecords->count() }})
                        </button>
                    </div>
                </div>

                <div class="relative w-full md:w-72 shrink-0">
                    <input type="search" id="historySearchInput" oninput="searchHistoryRows(this.value)"
                           placeholder="Cari tanggal atau keterangan..."
                           class="haptic-input h-10 pl-10 text-xs">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-muted pointer-events-none">
                        <i class="ph-light ph-magnifying-glass text-base"></i>
                    </span>
                </div>
            </div>

            <!-- Tactile Log Cards / Rows -->
            <div class="space-y-3" id="historyListContainer">

                @foreach ($records as $record)
                    @php
                        $isLate = $record->isLate();
                        $dateLabel = $record->attendance_date->locale('id')->translatedFormat('l, j F Y');
                        $dateShort = $record->attendance_date->locale('id')->translatedFormat('l, j M Y');
                        $masukLabel = $record->masukLocal()->format('H:i').' WIB';
                        $pulangLabel = $record->pulang_at !== null ? $record->pulangLocal()->format('H:i').' WIB' : 'Belum tercatat';
                        $statusLabel = $isLate ? 'Terlambat '.$record->late_minutes.' Menit' : 'Hadir Tepat Waktu';
                        $distanceLabel = number_format($record->latestDistance(), 0, ',', '.').' meter (≤ '.number_format(config('attendance.radius_meters'), 0, ',', '.').' m)';
                        $duration = $record->workDurationLabel();
                    @endphp
                    <div class="history-card sub-tray p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-white transition-all duration-500 ease-spring"
                         data-status="{{ $isLate ? 'telat' : 'hadir' }}" data-search="{{ strtolower($dateShort.' '.$statusLabel) }}">
                        <div class="flex items-start gap-3.5">
                            <span class="w-10 h-10 rounded-2xl {{ $isLate ? 'bg-amber-500/15 text-amber-800' : 'bg-emerald-500/12 text-emerald-700' }} flex items-center justify-center shrink-0 mt-0.5">
                                <i class="ph-light {{ $isLate ? 'ph-clock-countdown' : 'ph-check-circle' }} text-xl"></i>
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-extrabold text-ink">{{ $dateLabel }}</span>
                                    <span class="badge-pill {{ $isLate ? 'badge-amber' : 'badge-emerald' }}">{{ $isLate ? 'Telat '.$record->late_minutes.' Menit' : 'Tepat Waktu' }}</span>
                                </div>
                                <div class="text-xs text-muted mt-0.5">Shift Pagi · @if ($duration !== null)Durasi {{ $duration }} · @endif IP {{ $record->masuk_ip }}</div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between lg:justify-end gap-4 sm:gap-6 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-900/[0.05]">
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted">Masuk</span>
                                <span class="font-mono text-sm font-bold {{ $isLate ? 'text-amber-800' : 'text-ink' }} tabular-nums">{{ $masukLabel }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted">Pulang</span>
                                <span class="font-mono text-sm {{ $record->pulang_at !== null ? 'font-bold text-ink' : 'font-medium text-muted' }} tabular-nums">{{ $record->pulang_at !== null ? $pulangLabel : '--:--' }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted">Jarak GPS</span>
                                <span class="font-mono text-sm font-bold text-emerald-700 tabular-nums">{{ number_format($record->latestDistance(), 0, ',', '.') }} m</span>
                            </div>
                            <button type="button" onclick="openDetailModal(@json($dateShort, 15), @json($masukLabel, 15), @json($pulangLabel, 15), @json($statusLabel, 15), @json($distanceLabel, 15), @json($record->masuk_ip, 15))"
                                    class="btn-island-secondary py-1.5 pl-3.5 pr-1.5 text-xs">
                                <span>Audit</span>
                                <span class="icon-nest !w-7 !h-7">
                                    <i class="ph-light ph-arrow-up-right text-xs"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach

                @foreach ($leaveRecords as $leave)
                    @php
                        $isSingleDay = $leave->tgl_mulai->isSameDay($leave->tgl_selesai);
                        $leaveDateLabel = $isSingleDay
                            ? $leave->tgl_mulai->locale('id')->translatedFormat('l, j F Y')
                            : $leave->tgl_mulai->locale('id')->translatedFormat('j M Y').' – '.$leave->tgl_selesai->locale('id')->translatedFormat('j M Y');
                        $leaveDateShort = $isSingleDay
                            ? $leave->tgl_mulai->locale('id')->translatedFormat('l, j M Y')
                            : $leave->tgl_mulai->locale('id')->translatedFormat('j M Y').' – '.$leave->tgl_selesai->locale('id')->translatedFormat('j M Y');
                        $statusBadgeClass = match($leave->status_izin) {
                            'disetujui' => 'badge-emerald',
                            'ditolak' => 'badge-rose',
                            default => 'badge-amber',
                        };
                        $statusText = match($leave->status_izin) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            default => 'Menunggu Keputusan',
                        };
                        $iconClass = match($leave->status_izin) {
                            'disetujui' => 'bg-emerald-500/12 text-emerald-700',
                            'ditolak' => 'bg-rose-500/15 text-rose-700',
                            default => 'bg-amber-500/15 text-amber-800',
                        };
                        $iconName = match($leave->status_izin) {
                            'disetujui' => 'ph-check-circle',
                            'ditolak' => 'ph-x-circle',
                            default => 'ph-hourglass-medium',
                        };
                    @endphp
                    <div class="history-card sub-tray p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-white transition-all duration-500 ease-spring"
                         data-status="izin" data-search="{{ strtolower($leaveDateShort.' '.$leave->jenis.' '.$statusText.' '.$leave->alasan) }}">
                        <div class="flex items-start gap-3.5">
                            <span class="w-10 h-10 rounded-2xl {{ $iconClass }} flex items-center justify-center shrink-0 mt-0.5">
                                <i class="ph-light {{ $iconName }} text-xl"></i>
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-extrabold text-ink">{{ $leaveDateLabel }}</span>
                                    <span class="badge-pill {{ $statusBadgeClass }}">{{ $statusText }}</span>
                                </div>
                                <div class="text-xs text-muted mt-0.5">{{ $leave->jenis }} ({{ $leave->durationDays() }} Hari) · &ldquo;{{ \Illuminate\Support\Str::limit($leave->alasan, 55) }}&rdquo;</div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between lg:justify-end gap-4 sm:gap-6 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-900/[0.05]">
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted">Kategori</span>
                                <span class="text-xs font-bold text-ink">{{ $leave->jenis }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted">Durasi</span>
                                <span class="font-mono text-sm font-bold text-ink tabular-nums">{{ $leave->durationDays() }} Hari</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted">Status</span>
                                <span class="text-xs font-bold {{ $leave->status_izin === 'disetujui' ? 'text-emerald-700' : ($leave->status_izin === 'ditolak' ? 'text-rose-700' : 'text-amber-700') }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                            @if ($leave->foto_bukti)
                                <a href="{{ route('karyawan.izin.bukti', $leave) }}" target="_blank" rel="noopener"
                                   class="btn-island-secondary py-1.5 pl-3.5 pr-2.5 text-xs">
                                    <i class="ph-light ph-file-text text-xs"></i>
                                    <span>Bukti</span>
                                </a>
                            @else
                                <a href="{{ route('karyawan.izin') }}"
                                   class="btn-island-secondary py-1.5 pl-3.5 pr-2.5 text-xs">
                                    <span>Detail</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>

            <!-- Empty State -->
            <div id="historyEmptyState" class="{{ ($records->isEmpty() && $leaveRecords->isEmpty()) ? '' : 'hidden' }} sub-tray py-10 text-center">
                <p class="text-sm font-bold text-ink">{{ ($records->isEmpty() && $leaveRecords->isEmpty()) ? 'Belum ada catatan presensi atau perizinan.' : 'Tidak ditemukan catatan pada filter ini.' }}</p>
                <p class="text-xs text-muted mt-1">{{ ($records->isEmpty() && $leaveRecords->isEmpty()) ? 'Pindai QR kantor untuk presensi pertama Anda.' : 'Pilih tab Semua atau hapus kata kunci pencarian.' }}</p>
            </div>

        </div>
    </section>

    <!-- Modal Detail Verifikasi Server (Double-Bezel Dialog - Mobile Bottom Sheet Ergonomics) -->
    <div id="detailModal" role="dialog" aria-modal="true" aria-labelledby="detailModalTitle" class="fixed inset-0 z-50 hidden bg-obsidian/60 backdrop-blur-md flex items-end sm:items-center justify-center p-3 sm:p-4">
        <div class="bezel-shell w-full max-w-md max-h-[90dvh] overflow-y-auto" id="detailModalBox">
            <div class="bezel-core p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-900/[0.06]">
                    <div>
                        <span class="eyebrow-tag">Audit Telemetri</span>
                        <h2 id="detailModalTitle" class="text-lg font-extrabold text-ink mt-1">Bukti Verifikasi Server</h2>
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-slate-900/5 hover:bg-slate-900/10 flex items-center justify-center text-muted">
                        <i class="ph-light ph-x text-base"></i>
                    </button>
                </div>

                <dl class="sub-tray p-4 space-y-2.5 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-900/[0.05]">
                        <dt class="text-muted">Tanggal Presensi</dt>
                        <dd id="modalTgl" class="font-bold text-ink">-</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-900/[0.05]">
                        <dt class="text-muted">Masuk / Pulang</dt>
                        <dd id="modalJam" class="font-mono font-bold text-ink tabular-nums">-</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-900/[0.05]">
                        <dt class="text-muted">Status Validasi</dt>
                        <dd id="modalStatus" class="font-bold text-emerald-700">-</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-900/[0.05]">
                        <dt class="text-muted">Jarak Haversine</dt>
                        <dd id="modalJarak" class="font-mono font-semibold text-ink tabular-nums">-</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-muted">IP Publik Klien</dt>
                        <dd id="modalIp" class="font-mono text-ink tabular-nums">-</dd>
                    </div>
                </dl>

                <button type="button" onclick="closeDetailModal()" class="btn-island-primary w-full">
                    <span>Tutup Rincian Log</span>
                    <span class="icon-nest">
                        <i class="ph-light ph-check text-sm"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let activeHistoryStatus = 'semua';
    let activeHistoryQuery = '';

    function updateHistoryRows() {
        const rows = document.querySelectorAll('.history-card');
        let count = 0;

        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            const text = row.getAttribute('data-search') || '';
            const statusMatch = activeHistoryStatus === 'semua' || status === activeHistoryStatus;
            const searchMatch = !activeHistoryQuery || text.includes(activeHistoryQuery.toLowerCase());

            if (statusMatch && searchMatch) {
                row.classList.remove('hidden');
                count++;
            } else {
                row.classList.add('hidden');
            }
        });

        const emptyEl = document.getElementById('historyEmptyState');
        if (emptyEl) {
            emptyEl.classList.toggle('hidden', count > 0);
        }
    }

    function filterHistory(status, btnElement) {
        activeHistoryStatus = status;
        document.querySelectorAll('.history-filter-chip').forEach(el => {
            el.classList.remove('active');
        });
        btnElement.classList.add('active');
        if (btnElement && typeof btnElement.scrollIntoView === 'function') {
            btnElement.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
        updateHistoryRows();
    }

    // Touch & pointer horizontal drag support
    const filterScroll = document.getElementById('historyFilterScroll');
    if (filterScroll) {
        let isDown = false;
        let startX = 0;
        let scrollLeft = 0;

        filterScroll.addEventListener('pointerdown', (e) => {
            isDown = true;
            startX = e.pageX - filterScroll.offsetLeft;
            scrollLeft = filterScroll.scrollLeft;
        });
        window.addEventListener('pointerup', () => { isDown = false; });
        filterScroll.addEventListener('pointermove', (e) => {
            if (!isDown) return;
            const x = e.pageX - filterScroll.offsetLeft;
            const walk = (x - startX) * 1.5;
            filterScroll.scrollLeft = scrollLeft - walk;
        });
    }

    function searchHistoryRows(query) {
        activeHistoryQuery = query.trim();
        updateHistoryRows();
    }

    function openDetailModal(tgl, masuk, pulang, status, jarak, ip) {
        document.getElementById('modalTgl').textContent = tgl;
        document.getElementById('modalJam').textContent = `${masuk} / ${pulang}`;
        document.getElementById('modalStatus').textContent = status;
        document.getElementById('modalJarak').textContent = jarak;
        document.getElementById('modalIp').textContent = ip;

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeDetailModal();
        }
    });
</script>
@endpush

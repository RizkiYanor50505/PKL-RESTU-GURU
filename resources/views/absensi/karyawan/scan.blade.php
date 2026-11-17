@extends('absensi.layouts.app')

@section('title', 'Pemindai QR Presensi')

@push('styles')
<style>
    #qrReader {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        position: absolute !important;
        inset: 0 !important;
    }
    #qrReader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 1.5rem !important;
    }
    #qrReader__scan_region {
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    #qrReader__scan_region video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 1.5rem !important;
    }
    #qr-shaded-region {
        border-color: rgba(11, 15, 20, 0.6) !important;
    }
    #qrReader__dashboard_section_csr,
    #qrReader__dashboard_section_swaplink {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Main 2-Column Asymmetrical Bento: Left Optical Scanner + Right 8-Layer Security Ledger -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- Left (6 cols): Obsidian Optical Viewfinder Enclosure -->
        <section class="lg:col-span-6 bezel-shell-dark reveal-item">
            <div class="bezel-core-dark p-6 sm:p-8 flex flex-col justify-between gap-6">

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <span class="eyebrow-tag-dark">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span>OPTICAL SCANNER · 8-LAYER GUARD</span>
                        </span>
                        <h1 class="text-2xl font-extrabold text-white tracking-tight mt-2">Pemindai QR Lokasi</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="switchCameraBtn" onclick="switchCamera()"
                                class="hidden inline-flex items-center gap-1.5 px-3 py-2 rounded-full bg-white/10 hover:bg-white/15 border border-white/10 text-xs font-semibold text-white transition-all duration-500 ease-spring"
                                title="Beralih ke kamera lain">
                            <i class="ph-light ph-camera-rotate text-base text-emerald-300"></i>
                            <span id="switchCameraLabel">Ganti</span>
                        </button>
                        <button type="button" id="toggleFlashBtn"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white/10 hover:bg-white/15 border border-white/10 text-xs font-semibold text-white transition-all duration-500 ease-spring">
                            <i class="ph-light ph-flashlight text-base text-emerald-300"></i>
                            <span id="flashLabelTxt">Flash: Mati</span>
                        </button>
                    </div>
                </div>

                <!-- Machined Camera Viewfinder Box -->
                <div class="sub-tray-dark p-6 flex flex-col items-center justify-center space-y-4">
                    <div class="relative w-full max-w-[270px] aspect-square rounded-[1.75rem] bg-obsidian/90 border border-white/15 flex flex-col items-center justify-center p-6 overflow-hidden">
                        <!-- Live Camera Feed (html5-qrcode) -->
                        <div id="qrReader" class="absolute inset-0 [&_video]:w-full [&_video]:h-full [&_video]:object-cover"></div>

                        <!-- Corner Reticles -->
                        <div class="absolute top-5 left-5 w-7 h-7 border-t-2 border-l-2 border-emerald-400 rounded-tl-lg z-10"></div>
                        <div class="absolute top-5 right-5 w-7 h-7 border-t-2 border-r-2 border-emerald-400 rounded-tr-lg z-10"></div>
                        <div class="absolute bottom-5 left-5 w-7 h-7 border-b-2 border-l-2 border-emerald-400 rounded-bl-lg z-10"></div>
                        <div class="absolute bottom-5 right-5 w-7 h-7 border-b-2 border-r-2 border-emerald-400 rounded-br-lg z-10"></div>

                        <!-- Idle State -->
                        <div id="qrIdle" class="p-5 rounded-2xl bg-white/[0.04] border border-white/10 text-center space-y-2.5 relative z-10">
                            <i class="ph-light ph-qr-code text-6xl text-emerald-400"></i>
                            <div id="qrIdleText" class="font-mono text-xs font-semibold text-emerald-300 tracking-wide">Kamera belum aktif</div>
                        </div>
                    </div>

                    <p class="text-xs text-slate-300 text-center max-w-sm leading-relaxed">
                        Arahkan kamera ke lembar QR statis kantor. Server memvalidasi token lokasi, subnet IP Wi-Fi, dan radius Haversine secara serentak.
                    </p>

                    <!-- Hardware / Shutter & Multi-Camera Tip Alert -->
                    <div id="cameraHelpNotice" class="w-full max-w-sm p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 text-xs text-slate-300 space-y-1.5 text-left">
                        <div class="flex items-center gap-1.5 font-bold text-emerald-400">
                            <i class="ph-light ph-info text-sm"></i>
                            <span>Layar kamera hitam / gelap?</span>
                        </div>
                        <ul class="text-[11px] text-slate-300 space-y-1 list-disc list-inside leading-snug">
                            <li>Buka <b>slider fisik webcam</b> di atas layar laptop (bila ada penutup lensa fisik).</li>
                            <li>Klik tombol <b class="text-emerald-300">"Ganti Kamera"</b> di kanan atas untuk berpindah dari sensor IR/virtual ke webcam RGB laptop.</li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button type="button" id="btnStartScan" class="btn-island-emerald w-full">
                        <span id="btnStartScanLabel">Nyalakan Kamera &amp; Pindai QR</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-camera text-base"></i>
                        </span>
                    </button>

                    <p id="gpsStatusTxt" class="text-xs text-slate-300 text-center font-mono tabular-nums">GPS: belum aktif</p>
                </div>

            </div>
        </section>

        <!-- Right (6 cols): 8-Layer Real-Time Diagnostics Matrix -->
        <section class="lg:col-span-6 bezel-shell reveal-item">
            <div class="bezel-core p-6 sm:p-8 flex flex-col justify-between gap-5">

                <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-slate-900/[0.06]">
                    <div>
                        <span class="eyebrow-tag">Telemetri Sensor Klien & Server</span>
                        <h2 class="text-xl font-extrabold text-ink tracking-tight mt-1.5">
                            8 Lapisan Verifikasi Keamanan
                        </h2>
                    </div>
                    <span class="badge-pill badge-emerald">
                        <i class="ph-fill ph-shield-check text-sm"></i>
                        <span>Verifikasi Server Aktif</span>
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sub-tray p-3.5 flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-ink">1. Token QR Statis</div>
                            <div id="layerQrValue" class="font-mono text-xs text-muted mt-0.5">Belum dipindai</div>
                        </div>
                        <span id="layerQrBadge" class="badge-pill badge-emerald">Menunggu</span>
                    </div>

                    <div class="sub-tray p-3.5 flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-ink">2. Sesi & Token CSRF</div>
                            <div class="font-mono text-xs text-muted mt-0.5">PKL-2026-081</div>
                        </div>
                        <span class="badge-pill badge-emerald">Sah</span>
                    </div>

                    <div class="sub-tray p-3.5 flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-ink">3. Device Binding</div>
                            <div class="font-mono text-xs text-muted mt-0.5">dev-a54-98a3</div>
                        </div>
                        <span class="badge-pill badge-emerald">Terikat</span>
                    </div>

                    <div class="sub-tray p-3.5 flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-ink">4. IP Wi-Fi Kantor</div>
                            <div class="font-mono text-xs text-muted mt-0.5 tabular-nums">103.14.88.19</div>
                        </div>
                        <span class="badge-pill badge-emerald">Cocok</span>
                    </div>

                    <div class="sub-tray p-3.5 flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-ink">5. Akurasi Sensor GPS</div>
                            <div id="layerGpsValue" class="font-mono text-xs text-muted mt-0.5 tabular-nums">Menunggu GPS</div>
                        </div>
                        <span id="layerGpsBadge" class="badge-pill badge-emerald">Menunggu</span>
                    </div>

                    <div class="sub-tray p-3.5 flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-ink">6. Jarak Haversine</div>
                            <div id="layerDistValue" class="font-mono text-xs text-muted mt-0.5 tabular-nums">Menunggu verifikasi</div>
                        </div>
                        <span id="layerDistBadge" class="badge-pill badge-emerald">Menunggu</span>
                    </div>

                    <div class="sub-tray p-3.5 flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-ink">7. Duplikasi Sesi Shift</div>
                            <div class="font-mono text-xs text-muted mt-0.5">Sesi Pulang</div>
                        </div>
                        <span class="badge-pill badge-emerald">Tersedia</span>
                    </div>

                    <div class="sub-tray p-3.5 flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-ink">8. Waktu Atomik Server</div>
                            <div class="font-mono text-xs text-muted mt-0.5">NTP Asia/Jakarta</div>
                        </div>
                        <span class="badge-pill badge-emerald">Sinkron</span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('karyawan.dashboard') }}" class="btn-island-secondary w-full">
                        <span>Kembali ke Ringkasan Beranda</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-arrow-left text-sm"></i>
                        </span>
                    </a>
                </div>

            </div>
        </section>

    </div>

    <!-- Modal Sukses Absensi (Double-Bezel Dialog - Mobile Bottom Sheet Ergonomics) -->
    <div id="modalSuccess" role="dialog" aria-modal="true" aria-labelledby="modalSuccessTitle" class="fixed inset-0 z-50 hidden bg-obsidian/60 backdrop-blur-md flex items-end sm:items-center justify-center p-3 sm:p-4">
        <div class="bezel-shell w-full max-w-md max-h-[90dvh] overflow-y-auto" id="modalSuccessBox">
            <div class="bezel-core p-6 sm:p-7 space-y-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-2xl bg-emerald-500/15 text-emerald-600 flex items-center justify-center">
                            <i class="ph-fill ph-check-circle text-2xl"></i>
                        </span>
                        <div>
                            <span class="badge-pill badge-emerald">8 Lapisan Terverifikasi</span>
                            <h2 id="modalSuccessTitle" class="text-xl font-extrabold text-ink tracking-tight mt-1">Presensi Tercatat</h2>
                        </div>
                    </div>
                    <button type="button" onclick="closeScanModals()" class="w-8 h-8 rounded-full bg-slate-900/5 hover:bg-slate-900/10 flex items-center justify-center text-muted">
                        <i class="ph-light ph-x text-base"></i>
                    </button>
                </div>

                <dl class="sub-tray p-4 space-y-2.5 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-900/[0.05]">
                        <dt class="text-muted">Karyawan</dt>
                        <dd class="font-bold text-ink">Ahmad Fauzi (PKL-2026-081)</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-900/[0.05]">
                        <dt class="text-muted">Waktu Server</dt>
                        <dd id="successTime" class="font-mono font-bold text-ink tabular-nums">-</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-900/[0.05]">
                        <dt class="text-muted">Jarak Haversine</dt>
                        <dd id="successDistance" class="font-mono font-semibold text-emerald-700 tabular-nums">-</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-muted">IP & Device Hash</dt>
                        <dd id="successIp" class="font-mono text-ink tabular-nums">-</dd>
                    </div>
                </dl>

                <a href="{{ route('karyawan.dashboard') }}" class="btn-island-primary w-full">
                    <span>Selesai & Kembali ke Beranda</span>
                    <span class="icon-nest">
                        <i class="ph-light ph-arrow-right text-sm"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Penolakan Absensi (Double-Bezel Dialog - Mobile Bottom Sheet Ergonomics) -->
    <div id="modalReject" role="dialog" aria-modal="true" aria-labelledby="modalRejectTitle" class="fixed inset-0 z-50 hidden bg-obsidian/60 backdrop-blur-md flex items-end sm:items-center justify-center p-3 sm:p-4">
        <div class="bezel-shell w-full max-w-md max-h-[90dvh] overflow-y-auto" id="modalRejectBox">
            <div class="bezel-core p-6 sm:p-7 space-y-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-2xl bg-rose-500/15 text-rose-600 flex items-center justify-center">
                            <i class="ph-fill ph-warning-octagon text-2xl"></i>
                        </span>
                        <div>
                            <span id="rejectLayerBadge" class="badge-pill badge-rose">Ditolak Server</span>
                            <h2 id="modalRejectTitle" class="text-xl font-extrabold text-ink tracking-tight mt-1">Presensi Ditolak</h2>
                        </div>
                    </div>
                    <button type="button" onclick="closeRejectModal()" class="w-8 h-8 rounded-full bg-slate-900/5 hover:bg-slate-900/10 flex items-center justify-center text-muted">
                        <i class="ph-light ph-x text-base"></i>
                    </button>
                </div>

                <div class="sub-tray p-4 space-y-2 text-xs leading-relaxed text-ink">
                    <p id="rejectDetail"></p>
                    <p id="rejectHint" class="text-muted pt-2 border-t border-slate-900/[0.06]"></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeRejectModal()" class="btn-island-primary w-full">
                        <span>Coba Ulangi</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-arrow-clockwise text-sm"></i>
                        </span>
                    </button>
                    <a href="{{ route('karyawan.dashboard') }}" class="btn-island-secondary w-full">
                        <span>Beranda</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-house text-sm"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const VERIFY_URL = @json(route('karyawan.scan.verify'));
    const CSRF_TOKEN = @json(csrf_token());

    const el = (id) => document.getElementById(id);
    const formatMeters = (value) => Number(value).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' m';

    let qrScanner = null;
    let isScanning = false;
    let isVerifying = false;
    let flashOn = false;
    let gpsWatchId = null;
    let latestPosition = null;
    let availableCameras = [];
    let currentCameraIndex = 0;

    async function loadCamerasAndSelect() {
        if (!('Html5Qrcode' in window)) return null;

        try {
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length > 0) {
                availableCameras = devices;
                const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

                let chosenIndex = 0;
                if (isMobile) {
                    const backIdx = availableCameras.findIndex(c => /back|rear|environment/i.test(c.label));
                    chosenIndex = backIdx !== -1 ? backIdx : 0;
                } else {
                    // Pada Laptop/PC: Hindari sensor IR (Windows Hello), kamera virtual (OBS/DroidCam), atau depth sensor
                    const rgbIdx = availableCameras.findIndex(c => {
                        const lbl = (c.label || '').toLowerCase();
                        const isIrOrVirtual = lbl.includes('ir') || lbl.includes('infrared') || lbl.includes('hello') || lbl.includes('virtual') || lbl.includes('depth');
                        return !isIrOrVirtual;
                    });
                    chosenIndex = rgbIdx !== -1 ? rgbIdx : 0;
                }
                currentCameraIndex = chosenIndex;

                if (availableCameras.length > 1) {
                    const switchBtn = el('switchCameraBtn');
                    if (switchBtn) {
                        switchBtn.classList.remove('hidden');
                        updateSwitchBtnLabel();
                    }
                }
                return availableCameras[currentCameraIndex].id;
            }
        } catch (e) {
            console.warn('Gagal membaca daftar kamera perangkat:', e);
        }
        return null;
    }

    function updateSwitchBtnLabel() {
        const switchLabel = el('switchCameraLabel');
        if (switchLabel && availableCameras.length > 0) {
            switchLabel.textContent = `Ganti (${currentCameraIndex + 1}/${availableCameras.length})`;
        }
    }

    async function switchCamera() {
        if (!availableCameras || availableCameras.length < 2) {
            showToast('Satu Kamera', 'Hanya terdeteksi 1 kamera aktif pada perangkat ini.', false);
            return;
        }

        currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
        updateSwitchBtnLabel();

        const newCam = availableCameras[currentCameraIndex];
        showToast('Kamera Dialihkan', newCam.label || ('Kamera ' + (currentCameraIndex + 1)), true);

        if (isScanning) {
            await stopScanner(true);
            await startScannerWithConfig(newCam.id);
        }
    }
    window.switchCamera = switchCamera;

    function setLayer(prefix, text, badgeText, isOk) {
        el('layer' + prefix + 'Value').textContent = text;
        const badge = el('layer' + prefix + 'Badge');
        badge.textContent = badgeText;
        badge.className = 'badge-pill ' + (isOk ? 'badge-emerald' : 'badge-rose');
    }

    function startGpsWatch() {
        if (!('geolocation' in navigator)) {
            el('gpsStatusTxt').textContent = 'GPS: tidak didukung browser ini';
            return;
        }
        if (gpsWatchId !== null) {
            return;
        }
        el('gpsStatusTxt').textContent = 'GPS: mencari sinyal...';
        gpsWatchId = navigator.geolocation.watchPosition(
            (position) => {
                latestPosition = position.coords;
                const accuracy = position.coords.accuracy;
                el('gpsStatusTxt').textContent = 'GPS: ±' + formatMeters(accuracy);
                setLayer('Gps', '±' + formatMeters(accuracy), accuracy <= 50 ? 'Presisi' : 'Lemah', accuracy <= 50);
            },
            (error) => {
                const reasons = {
                    1: 'izin lokasi ditolak',
                    2: 'lokasi tidak tersedia',
                    3: 'waktu habis mencari sinyal',
                };
                el('gpsStatusTxt').textContent = 'GPS: ' + (reasons[error.code] || 'gagal');
                setLayer('Gps', reasons[error.code] || 'Gagal', 'Gagal', false);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }

    function stopGpsWatch() {
        if (gpsWatchId !== null) {
            navigator.geolocation.clearWatch(gpsWatchId);
            gpsWatchId = null;
        }
    }

    async function stopScanner(keepUiScanning = false) {
        if (qrScanner && isScanning) {
            try {
                await qrScanner.stop();
            } catch (e) { /* kamera sudah berhenti */ }
        }
        isScanning = false;
        flashOn = false;
        if (el('flashLabelTxt')) el('flashLabelTxt').textContent = 'Flash: Mati';
        if (!keepUiScanning) {
            el('qrIdle').classList.remove('hidden');
            el('btnStartScanLabel').textContent = 'Nyalakan Kamera & Pindai QR';
        }
    }

    async function startScannerWithConfig(cameraConfig) {
        startGpsWatch();
        qrScanner = qrScanner || new Html5Qrcode('qrReader');

        const scanConfig = {
            fps: 12,
            qrbox: (w, h) => {
                const size = Math.floor(Math.min(w, h) * 0.78);
                return { width: size, height: size };
            },
            aspectRatio: 1.0,
        };

        try {
            await qrScanner.start(
                cameraConfig,
                scanConfig,
                onQrDecoded,
                () => {}
            );
            isScanning = true;
            el('qrIdle').classList.add('hidden');
            el('btnStartScanLabel').textContent = 'Matikan Kamera';
        } catch (error) {
            console.error('Kamera gagal dimulai:', error);
            el('qrIdleText').textContent = 'Kamera gagal dibuka';
            showToast('Kamera Gagal Dibuka', 'Pastikan izin kamera aktif atau coba ganti kamera.', false);
        }
    }

    async function startScanner() {
        if (isScanning || isVerifying) {
            return;
        }
        if (!window.isSecureContext) {
            showToast('Kamera Diblokir', 'Buka lewat HTTPS atau localhost agar browser mengizinkan kamera & GPS.', false);
            el('qrIdleText').textContent = 'Butuh HTTPS / localhost';
            return;
        }

        let selectedCameraId = null;
        if (availableCameras.length === 0) {
            selectedCameraId = await loadCamerasAndSelect();
        } else {
            selectedCameraId = availableCameras[currentCameraIndex]?.id;
        }

        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        const cameraConfig = selectedCameraId
            ? selectedCameraId
            : { facingMode: (isMobile ? 'environment' : 'user') };

        await startScannerWithConfig(cameraConfig);
    }

    async function onQrDecoded(decodedText) {
        if (isVerifying) {
            return;
        }
        isVerifying = true;
        await stopScanner();

        if (!latestPosition) {
            isVerifying = false;
            showToast('GPS Belum Siap', 'Tunggu sinyal GPS terkunci lalu pindai ulang.', false);
            return;
        }

        setLayer('Qr', decodedText, 'Diperiksa', true);

        try {
            const response = await fetch(VERIFY_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    qr_token: decodedText,
                    latitude: latestPosition.latitude,
                    longitude: latestPosition.longitude,
                    accuracy: latestPosition.accuracy,
                }),
            });
            const data = await response.json();

            if (response.ok && data.success) {
                showSuccess(decodedText, data);
            } else {
                showReject(decodedText, data);
            }
        } catch (error) {
            showToast('Server Tidak Terjangkau', 'Periksa koneksi internet lalu coba lagi.', false);
        } finally {
            isVerifying = false;
        }
    }

    function showSuccess(token, data) {
        setLayer('Qr', token, 'Aktif', true);
        setLayer('Dist', formatMeters(data.distance) + ' (≤ ' + formatMeters(data.radius) + ')', 'Valid', true);
        el('successTime').textContent = data.server_time + ' WIB';
        el('modalSuccessTitle').textContent = data.type_label;
        el('successDistance').textContent = formatMeters(data.distance) + ' (≤ ' + formatMeters(data.radius) + ')';
        el('successIp').textContent = data.ip;
        el('modalSuccess').classList.remove('hidden');
        showToast('Presensi Tercatat', 'Token QR, akurasi GPS, dan radius kantor lolos verifikasi.', true);
    }

    function showReject(token, data) {
        const isTokenFailure = data.layer === 1;
        setLayer('Qr', token, isTokenFailure ? 'Tidak Valid' : 'Aktif', !isTokenFailure);
        if (data.distance !== undefined) {
            setLayer('Dist', formatMeters(data.distance) + ' (≤ ' + formatMeters(data.radius) + ')', data.layer === 6 ? 'Di Luar' : 'Valid', data.layer !== 6);
        }

        el('rejectLayerBadge').textContent = 'Ditolak Server · Lapisan Ke-' + (data.layer ?? '?');
        el('modalRejectTitle').textContent = data.title || 'Presensi Ditolak';
        el('rejectDetail').textContent = data.layer === 6
            ? 'Koordinat GPS terdeteksi berjarak ' + formatMeters(data.distance) + ' dari titik kantor. Batas radius adalah ' + formatMeters(data.radius) + '.'
            : (data.layer === 5
                ? 'Akurasi GPS ±' + formatMeters(data.accuracy) + ' melebihi batas ' + formatMeters(data.max_accuracy) + '.'
                : (data.message || 'Data presensi tidak valid.'));
        el('rejectHint').textContent = data.message || '';
        el('modalReject').classList.remove('hidden');
        showToast('Presensi Ditolak', data.title || 'Verifikasi server gagal.', false);
    }

    function closeRejectModal() {
        el('modalReject').classList.add('hidden');
    }

    function closeScanModals() {
        el('modalSuccess').classList.add('hidden');
        el('modalReject').classList.add('hidden');
    }

    el('btnStartScan').addEventListener('click', async function () {
        if (isScanning) {
            await stopScanner();
        } else {
            await startScanner();
        }
    });

    el('toggleFlashBtn').addEventListener('click', async function () {
        if (!isScanning) {
            showToast('Kamera Belum Aktif', 'Nyalakan kamera terlebih dahulu.', false);
            return;
        }
        try {
            await qrScanner.applyVideoConstraints({ advanced: [{ torch: !flashOn }] });
            flashOn = !flashOn;
            el('flashLabelTxt').textContent = flashOn ? 'Flash: Menyala' : 'Flash: Mati';
        } catch (error) {
            showToast('Flash Tidak Tersedia', 'Perangkat atau browser ini tidak mendukung lampu kilat.', false);
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeScanModals();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadCamerasAndSelect();
    });

    window.addEventListener('pagehide', () => {
        stopGpsWatch();
        stopScanner();
    });
</script>
@endpush

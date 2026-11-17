/**
 * hadir.in - Command Center & Interactive Geofence Manager
 * Version 2.5
 */
(function (window, document) {
    'use strict';

    // =========================================================
    // GLOBAL STATE & HELPERS
    // =========================================================
    let geofenceMap = null;
    let officeMarker = null;
    let geofenceCircle = null;
    let streetLayer = null;
    let satelliteLayer = null;
    let isSatellite = false;
    let reverseGeocodeTimer = null;
    let qrGeneratorInstance = null;

    let currentStatusFilter = 'semua';
    let currentDivisionFilter = 'semua';
    let currentSearchQuery = '';

    function getConfig() {
        return window.HADIR_ADMIN_CONFIG || {
            updateLokasiUrl: '/admin/lokasi/update',
            csrfToken: '',
            qrToken: 'LOC-PST-24D5',
            officeName: 'Restu Guru Promosindo Cabang Banjar Baru',
            latitude: -3.4424391,
            longitude: 114.8328872,
            radiusMeters: 10
        };
    }

    function parseCoord(val, fallback) {
        if (val === null || val === undefined || val === '') return fallback;
        const normalized = String(val).replace(',', '.').trim();
        const num = parseFloat(normalized);
        return isNaN(num) ? fallback : num;
    }

    // =========================================================
    // TABLE FILTERING & SEARCH
    // =========================================================
    function applyAdminTableFilters() {
        const rows = document.querySelectorAll('.admin-log-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status') || '';
            const rowDivision = row.getAttribute('data-division') || '';
            const rowSearch = (row.getAttribute('data-search') || row.textContent || '').toLowerCase();

            const matchStatus = (currentStatusFilter === 'semua') || (rowStatus === currentStatusFilter);
            const matchDivision = (currentDivisionFilter === 'semua') || (rowDivision === currentDivisionFilter);
            const matchSearch = !currentSearchQuery || rowSearch.includes(currentSearchQuery);

            if (matchStatus && matchDivision && matchSearch) {
                row.classList.remove('hidden');
                row.style.display = '';
                visibleCount++;
            } else {
                row.classList.add('hidden');
                row.style.display = 'none';
            }
        });

        const countEl = document.getElementById('adminRowCount');
        if (countEl) {
            countEl.textContent = `${visibleCount} dari 45 catatan`;
        }

        const emptyState = document.getElementById('adminEmptyState');
        if (emptyState) {
            emptyState.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function filterAdminLogs(status, btn) {
        currentStatusFilter = status;
        document.querySelectorAll('.filter-pill-status').forEach(el => {
            el.classList.remove('active');
            el.setAttribute('aria-pressed', 'false');
        });
        if (btn) {
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
        }
        applyAdminTableFilters();
    }

    function filterAdminDivision(division, btn) {
        currentDivisionFilter = division;
        document.querySelectorAll('.filter-pill-division').forEach(el => {
            el.classList.remove('active');
            el.setAttribute('aria-pressed', 'false');
        });
        if (btn) {
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
        }
        applyAdminTableFilters();
    }

    function searchAdminLogs(query) {
        currentSearchQuery = (query || '').toLowerCase().trim();
        applyAdminTableFilters();
    }

    function updateLeaveCounters(pending, outcome) {
        const navBadge = document.getElementById('navLeaveBadge');
        if (navBadge) {
            if (pending > 0) {
                navBadge.textContent = pending;
            } else {
                navBadge.remove();
            }
        }

        const mobileText = document.getElementById('mobileLeaveText');
        if (mobileText) {
            mobileText.textContent = pending > 0 ? `Izin (${pending})` : 'Izin';
        }

        const mobileDot = document.getElementById('mobileLeaveDot');
        if (mobileDot) {
            if (pending > 0) {
                mobileDot.style.display = '';
            } else {
                mobileDot.remove();
            }
        }

        const sitiPill = document.getElementById('rowSitiStatusPill');
        if (sitiPill) {
            sitiPill.textContent = outcome ? 'Disetujui' : 'Ditolak';
            sitiPill.className = 'badge-pill ' + (outcome ? 'badge-emerald' : 'badge-rose');
        }

        const queueBadge = document.getElementById('approvalQueueBadge');
        if (queueBadge) {
            queueBadge.textContent = pending > 0 ? pending + ' Menunggu' : 'Semua Selesai';
            queueBadge.className = 'badge-pill ' + (pending > 0 ? 'badge-amber' : 'badge-emerald');
        }

        const kpiSub = document.getElementById('kpiLeaveSub');
        if (kpiSub) {
            kpiSub.textContent = pending + ' Menunggu Keputusan';
            kpiSub.className = 'text-[11px] font-semibold ' + (pending > 0 ? 'text-amber-800' : 'text-emerald-700');
        }

        const hero = document.getElementById('heroPendingLeave');
        if (hero) hero.textContent = pending > 0 ? pending + ' berkas izin' : 'tidak ada berkas izin';

        const list = document.getElementById('approvalList');
        if (list && pending === 0 && !document.getElementById('approvalEmpty')) {
            const empty = document.createElement('div');
            empty.id = 'approvalEmpty';
            empty.className = 'sub-tray p-4 text-xs font-semibold ' + (outcome ? 'text-emerald-700' : 'text-rose-700');
            empty.textContent = 'Tidak ada pengajuan izin yang menunggu keputusan.';
            list.appendChild(empty);
        }
    }

    async function decideLeave(id, isApproved) {
        const card = document.getElementById('leaveCard-' + id);
        if (!card) return;

        let note = '';
        if (!isApproved) {
            const answer = window.prompt('Alasan penolakan (opsional):', '');
            if (answer === null) return;
            note = answer.trim();
        }

        const buttons = card.querySelectorAll('.leave-actions button');
        buttons.forEach(btn => { btn.disabled = true; });

        try {
            const cfg = getConfig();
            const res = await fetch(cfg.leaveDecideUrl.replace('__ID__', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': cfg.csrfToken
                },
                body: JSON.stringify({
                    status: isApproved ? 'disetujui' : 'ditolak',
                    catatan_admin: note
                })
            });
            const data = await res.json();

            if (!res.ok || !data.success) {
                buttons.forEach(btn => { btn.disabled = false; });
                if (typeof window.showToast === 'function') {
                    window.showToast('Gagal Menyimpan Keputusan', data.message || 'Terjadi kesalahan pada server.', false);
                }
                return;
            }

            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                card.remove();
                updateLeaveCounters(data.pending_count, isApproved);
            }, 300);

            if (typeof window.showToast === 'function') {
                window.showToast(isApproved ? 'Izin Disetujui' : 'Izin Ditolak', data.message, isApproved);
            }
        } catch (err) {
            buttons.forEach(btn => { btn.disabled = false; });
            if (typeof window.showToast === 'function') {
                window.showToast('Kesalahan Jaringan', 'Tidak dapat menghubungi server.', false);
            }
        }
    }

    function handleDeviceReset(e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        const input = document.getElementById('resetDeviceNik');
        const target = input ? (input.value.trim() || 'Karyawan') : 'Karyawan';
        if (typeof window.showToast === 'function') {
            window.showToast('Perangkat Direset', 'Kunci perangkat untuk ' + target + ' telah dibuka.', true);
        }
        if (input) input.value = '';
    }

    // =========================================================
    // QR CODE MODAL, GENERATION & ROTATION
    // =========================================================
    function renderModalQr(token) {
        const container = document.getElementById('modalQrCodeContainer');
        if (!container) return;
        container.innerHTML = '';
        const safeToken = String(token || getConfig().qrToken || 'LOC-PST-24D5').trim();

        let rendered = false;
        if (typeof QRCode !== 'undefined') {
            try {
                qrGeneratorInstance = new QRCode(container, {
                    text: safeToken,
                    width: 176,
                    height: 176,
                    colorDark: '#0B0F14',
                    colorLight: '#FFFFFF',
                    correctLevel: QRCode.CorrectLevel.M
                });
                rendered = true;
            } catch (err) {
                console.warn('[hadir.in] QRCode JS fallback:', err);
                container.innerHTML = '';
            }
        }

        if (!rendered || !container.children.length) {
            const img = document.createElement('img');
            img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=176x176&data=' + encodeURIComponent(safeToken);
            img.alt = 'QR Code ' + safeToken;
            img.className = 'w-[176px] h-[176px] mx-auto rounded-lg shadow-sm';
            container.appendChild(img);
        }
    }

    function openQrModal() {
        const modal = document.getElementById('modalQr');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }

        const tokenInput = document.getElementById('lokasiQrTokenInput');
        const token = tokenInput ? (tokenInput.value || getConfig().qrToken) : getConfig().qrToken;
        const tokenEl = document.getElementById('activeQrTokenText');
        if (tokenEl) {
            tokenEl.textContent = 'Token: ' + token;
        }

        renderModalQr(token);
    }

    function closeQrModal() {
        const modal = document.getElementById('modalQr');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    function generateNewQrTokenField() {
        const randomHex = Math.floor(1000 + Math.random() * 9000).toString(16).toUpperCase();
        const newToken = 'LOC-PST-' + randomHex;
        const qrInput = document.getElementById('lokasiQrTokenInput');
        if (qrInput) {
            qrInput.value = newToken;
        }
        return newToken;
    }

    async function rotateQrToken() {
        const newToken = generateNewQrTokenField();
        const tokenEl = document.getElementById('activeQrTokenText');
        if (tokenEl) {
            tokenEl.textContent = 'Token: ' + newToken;
        }

        renderModalQr(newToken);

        if (typeof window.showToast === 'function') {
            window.showToast('Token QR Diperbarui', 'Token baru: ' + newToken + '. Menyimpan ke database...', true);
        }

        // Auto-save silently to backend
        try {
            const cfg = getConfig();
            const latInput = document.getElementById('lokasiLatInput');
            const lngInput = document.getElementById('lokasiLngInput');
            const radiusInput = document.getElementById('lokasiRadiusInput');
            const namaInput = document.getElementById('lokasiNamaInput');
            const ipInput = document.getElementById('lokasiAllowedIpsInput');

            const payload = {
                nama_lokasi: namaInput ? namaInput.value : cfg.officeName,
                latitude: parseCoord(latInput ? latInput.value : null, cfg.latitude),
                longitude: parseCoord(lngInput ? lngInput.value : null, cfg.longitude),
                radius_meters: parseInt(radiusInput ? radiusInput.value : cfg.radiusMeters, 10) || 10,
                qr_token: newToken,
                allowed_ips: ipInput ? ipInput.value : ''
            };

            const res = await fetch(cfg.updateLokasiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': cfg.csrfToken
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (res.ok && data.success) {
                if (typeof window.showToast === 'function') {
                    window.showToast('Token QR Tersimpan', 'Token aktif ' + newToken + ' berhasil disimpan permanen.', true);
                }
            }
        } catch (err) {
            console.warn('[hadir.in] Auto-save token deferred:', err);
        }
    }

    function printQrSheet() {
        const imgEl = document.querySelector('#modalQrCodeContainer img') || document.querySelector('#modalQrCodeContainer canvas');
        const qrSrc = imgEl ? (imgEl.src || (imgEl.toDataURL ? imgEl.toDataURL() : '')) : '';
        const officeName = document.getElementById('modalQrOfficeName')?.textContent || getConfig().officeName;
        const token = document.getElementById('lokasiQrTokenInput')?.value || getConfig().qrToken;

        const printWin = window.open('', '_blank', 'width=650,height=750');
        if (!printWin) {
            if (typeof window.showToast === 'function') {
                window.showToast('Popup Diblokir', 'Izinkan popup di browser untuk mencetak lembar QR.', false);
            } else {
                alert('Izinkan popup di browser untuk mencetak lembar QR.');
            }
            return;
        }

        const htmlContent = '<!DOCTYPE html>' +
            '<html lang="id">' +
            '<head>' +
            '<meta charset="UTF-8">' +
            '<title>Lembar QR Presensi - ' + officeName + '</title>' +
            '<style>' +
            'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 95vh; margin: 0; padding: 24px; background: #fff; color: #0F172A; }' +
            '.card { border: 2.5px dashed #0F172A; border-radius: 28px; padding: 48px 40px; max-width: 440px; text-align: center; }' +
            '.brand { font-size: 30px; font-weight: 800; letter-spacing: -0.04em; color: #059669; margin-bottom: 2px; }' +
            '.badge { display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: #ECFDF5; color: #059669; padding: 4px 12px; border-radius: 9999px; margin-bottom: 16px; border: 1px solid #A7F3D0; }' +
            '.title { font-size: 16px; font-weight: 700; color: #0F172A; margin: 0 0 4px; }' +
            '.subtitle { font-size: 13.5px; color: #64748B; margin: 0 0 24px; font-weight: 500; }' +
            '.qr-wrap { display: inline-flex; align-items: center; justify-content: center; padding: 16px; border: 1.5px solid #E2E8F0; border-radius: 20px; background: #FFF; margin-bottom: 20px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06); }' +
            '.qr-wrap img { display: block; width: 220px; height: 220px; }' +
            '.token-box { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 14px; font-weight: 700; background: #F1F5F9; color: #0F172A; padding: 8px 18px; border-radius: 9999px; display: inline-block; letter-spacing: 0.05em; border: 1px solid #CBD5E1; }' +
            '.instructions { margin-top: 24px; font-size: 11.5px; color: #64748B; line-height: 1.6; border-top: 1px solid #E2E8F0; padding-top: 18px; }' +
            '</style>' +
            '</head>' +
            '<body>' +
            '<div class="card">' +
            '<div class="brand">hadir.in</div>' +
            '<div class="badge">LEMBAR PRESENSI QR RESMI</div>' +
            '<div class="title">' + officeName + '</div>' +
            '<div class="subtitle">Validasi Multi-Faktor Geofence & Jaringan</div>' +
            '<div class="qr-wrap">' +
            (qrSrc ? '<img src="' + qrSrc + '" alt="QR Presensi" />' : '<div style="width:220px;height:220px;display:flex;align-items:center;justify-content:center;">QR Code</div>') +
            '</div>' +
            '<div>' +
            '<div class="token-box">TOKEN: ' + token + '</div>' +
            '</div>' +
            '<div class="instructions">' +
            'Buka aplikasi <b>hadir.in</b> &rarr; arahkan kamera presensi ke lembar ini.<br>' +
            'Server memverifikasi token QR statis, radius GPS Haversine, dan subnet IP Wi-Fi kantor secara serentak.' +
            '</div>' +
            '</div>' +
            '</body>' +
            '</html>';

        printWin.document.open();
        printWin.document.write(htmlContent);
        printWin.document.close();

        setTimeout(() => {
            try {
                printWin.focus();
                printWin.print();
            } catch (e) {
                console.warn('Gagal mencetak otomatis:', e);
            }
        }, 500);
    }

    function appendCurrentClientIp(ip) {
        const ipInput = document.getElementById('lokasiAllowedIpsInput');
        if (!ipInput || !ip) return;
        const list = ipInput.value.split(',').map(item => item.trim()).filter(Boolean);
        if (!list.includes(ip)) {
            list.push(ip);
            ipInput.value = list.join(', ');
        }
    }

    // =========================================================
    // INTERACTIVE GEOFENCE MAP (LEAFLET + GOJEK PIN + NOMINATIM)
    // =========================================================
    function initGeofenceMap() {
        const mapContainer = document.getElementById('adminGeofenceMap');
        if (!mapContainer) return;

        if (typeof L === 'undefined') {
            console.log('[hadir.in] Menunggu modul Leaflet dimuat...');
            if (!window._leafletFallbackAttempted) {
                window._leafletFallbackAttempted = true;
                const cdnScript = document.createElement('script');
                cdnScript.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                cdnScript.onload = function () {
                    console.log('[hadir.in] CDN Leaflet berhasil dimuat.');
                    setTimeout(initGeofenceMap, 100);
                };
                document.head.appendChild(cdnScript);
            }
            setTimeout(initGeofenceMap, 250);
            return;
        }

        if (geofenceMap) {
            geofenceMap.invalidateSize();
            return;
        }

        try {
            const cfg = getConfig();
            const latInput = document.getElementById('lokasiLatInput');
            const lngInput = document.getElementById('lokasiLngInput');
            const radiusInput = document.getElementById('lokasiRadiusInput');
            const radiusSlider = document.getElementById('lokasiRadiusSlider');

            const initialLat = parseCoord(latInput ? latInput.value : null, cfg.latitude);
            const initialLng = parseCoord(lngInput ? lngInput.value : null, cfg.longitude);
            const initialRadius = parseInt(radiusInput ? radiusInput.value : cfg.radiusMeters, 10) || 10;

            streetLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            });

            satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: '&copy; Esri World Imagery'
            });

            geofenceMap = L.map('adminGeofenceMap', {
                center: [initialLat, initialLng],
                zoom: 17,
                layers: [streetLayer],
                zoomControl: true
            });

            // Remove loading indicator
            const statusEl = document.getElementById('mapStatusIndicator');
            if (statusEl) {
                statusEl.classList.add('hidden');
                statusEl.remove();
            }

            const pinIcon = L.divIcon({
                className: 'gojek-pin-marker',
                html: '<div class="gojek-pin-core"><div class="gojek-pin-dot"></div></div>',
                iconSize: [36, 36],
                iconAnchor: [18, 34]
            });

            officeMarker = L.marker([initialLat, initialLng], {
                draggable: true,
                icon: pinIcon
            }).addTo(geofenceMap);

            geofenceCircle = L.circle([initialLat, initialLng], {
                radius: initialRadius,
                color: '#059669',
                weight: 2,
                fillColor: '#10B981',
                fillOpacity: 0.18
            }).addTo(geofenceMap);

            updateReverseGeocode(initialLat, initialLng);

            // Dragging Gojek pin
            officeMarker.on('drag', function (e) {
                const pos = e.target.getLatLng();
                geofenceCircle.setLatLng(pos);
                syncCoordInputs(pos.lat, pos.lng, false);
            });

            officeMarker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                syncCoordInputs(pos.lat, pos.lng, true);
            });

            // Clicking map moves pin
            geofenceMap.on('click', function (e) {
                setMapPinPosition(e.latlng.lat, e.latlng.lng, false);
            });

            // Typing coordinates manually
            function onManualCoordInput() {
                const lat = parseCoord(latInput ? latInput.value : null, null);
                const lng = parseCoord(lngInput ? lngInput.value : null, null);
                if (lat !== null && lng !== null && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    officeMarker.setLatLng([lat, lng]);
                    geofenceCircle.setLatLng([lat, lng]);
                    geofenceMap.panTo([lat, lng]);
                    const badge = document.getElementById('liveCoordBadgeText');
                    if (badge) badge.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
                    scheduleReverseGeocode(lat, lng);
                }
            }
            if (latInput) latInput.addEventListener('input', onManualCoordInput);
            if (lngInput) lngInput.addEventListener('input', onManualCoordInput);

            setTimeout(() => {
                if (geofenceMap) geofenceMap.invalidateSize();
            }, 300);

            // Slider & number sync
            if (radiusSlider && radiusInput) {
                radiusSlider.addEventListener('input', function () {
                    radiusInput.value = this.value;
                    applyRadiusToCircle(parseInt(this.value, 10));
                });

                radiusInput.addEventListener('input', function () {
                    const val = Math.max(10, Math.min(5000, parseInt(this.value, 10) || 100));
                    radiusSlider.value = Math.min(1000, val);
                    applyRadiusToCircle(val);
                });
            }

            // Center pin button
            const btnCenter = document.getElementById('btnCenterPin');
            if (btnCenter) {
                btnCenter.addEventListener('click', function () {
                    const pos = officeMarker.getLatLng();
                    geofenceMap.flyTo(pos, 17, { duration: 0.6 });
                });
            }

            // Layer switcher
            const btnToggleLayer = document.getElementById('btnToggleMapLayer');
            if (btnToggleLayer) {
                btnToggleLayer.addEventListener('click', function () {
                    isSatellite = !isSatellite;
                    if (isSatellite) {
                        geofenceMap.removeLayer(streetLayer);
                        geofenceMap.addLayer(satelliteLayer);
                        const lbl = document.getElementById('mapLayerLabel');
                        if (lbl) lbl.textContent = 'Peta Jalan';
                    } else {
                        geofenceMap.removeLayer(satelliteLayer);
                        geofenceMap.addLayer(streetLayer);
                        const lbl = document.getElementById('mapLayerLabel');
                        if (lbl) lbl.textContent = 'Satelit';
                    }
                });
            }

            // GPS button
            const btnGps = document.getElementById('btnUseMyGps');
            if (btnGps) {
                btnGps.addEventListener('click', function () {
                    if (!('geolocation' in navigator)) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('GPS Tidak Didukung', 'Browser Anda tidak mendukung Geolocation API.', false);
                        }
                        return;
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast('Mencari Titik GPS...', 'Mengambil koordinat presisi dari perangkat Anda.', true);
                    }
                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            setMapPinPosition(pos.coords.latitude, pos.coords.longitude, true);
                            if (typeof window.showToast === 'function') {
                                window.showToast('Titik GPS Ditemukan', 'Akurasi ±' + Math.round(pos.coords.accuracy) + ' meter.', true);
                            }
                        },
                        function () {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal Mengambil GPS', 'Pastikan izin lokasi browser diaktifkan.', false);
                            }
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                });
            }

            // Search place
            const searchInput = document.getElementById('mapSearchInput');
            const btnSearch = document.getElementById('btnSearchPlace');
            if (btnSearch && searchInput) {
                btnSearch.addEventListener('click', function () {
                    searchMapLocation(searchInput.value);
                });
                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchMapLocation(this.value);
                    }
                });
            }
        } catch (err) {
            console.error('[hadir.in] Error saat inisialisasi peta Leaflet:', err);
            const statusEl = document.getElementById('mapStatusIndicator');
            if (statusEl) {
                statusEl.classList.remove('hidden');
                statusEl.innerHTML = '<div class="p-6 text-center text-xs text-rose-600 font-mono"><b>Gagal memuat peta:</b> ' + (err.message || err) + '</div>';
            }
        }
    }

    function applyRadiusToCircle(radiusMeters) {
        if (geofenceCircle) {
            geofenceCircle.setRadius(radiusMeters);
        }
        const pill = document.getElementById('liveRadiusPill');
        if (pill) {
            pill.textContent = 'Radius: ' + radiusMeters + ' m';
        }
    }

    function syncCoordInputs(lat, lng, triggerGeocode = true) {
        const roundedLat = Number(lat.toFixed(7));
        const roundedLng = Number(lng.toFixed(7));
        const latInput = document.getElementById('lokasiLatInput');
        const lngInput = document.getElementById('lokasiLngInput');
        if (latInput) latInput.value = roundedLat;
        if (lngInput) lngInput.value = roundedLng;

        const badge = document.getElementById('liveCoordBadgeText');
        if (badge) badge.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);

        if (triggerGeocode) {
            scheduleReverseGeocode(roundedLat, roundedLng);
        }
    }

    function setMapPinPosition(lat, lng, fly = true) {
        if (!geofenceMap || !officeMarker || !geofenceCircle) return;
        officeMarker.setLatLng([lat, lng]);
        geofenceCircle.setLatLng([lat, lng]);
        if (fly) {
            geofenceMap.flyTo([lat, lng], 17, { duration: 0.7 });
        } else {
            geofenceMap.panTo([lat, lng]);
        }
        syncCoordInputs(lat, lng, true);
    }

    function scheduleReverseGeocode(lat, lng) {
        if (reverseGeocodeTimer) clearTimeout(reverseGeocodeTimer);
        reverseGeocodeTimer = setTimeout(() => updateReverseGeocode(lat, lng), 350);
    }

    async function updateReverseGeocode(lat, lng) {
        const label = document.getElementById('reverseGeocodeText');
        if (!label) return;
        label.textContent = 'Memuat alamat lokasi...';
        try {
            const res = await fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng), {
                headers: { 'Accept-Language': 'id' }
            });
            const data = await res.json();
            if (data && data.display_name) {
                label.textContent = data.display_name;
            } else {
                label.textContent = 'Titik Koordinat (' + lat.toFixed(6) + ', ' + lng.toFixed(6) + ')';
            }
        } catch (e) {
            label.textContent = 'Titik Koordinat (' + lat.toFixed(6) + ', ' + lng.toFixed(6) + ')';
        }
    }

    async function searchMapLocation(query) {
        const q = (query || '').trim();
        const resultsBox = document.getElementById('mapSearchResults');
        if (!q || !resultsBox) return;

        resultsBox.innerHTML = '<div class="p-3 text-xs text-muted">Mencari lokasi...</div>';
        resultsBox.classList.remove('hidden');

        try {
            const res = await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&q=' + encodeURIComponent(q) + '&limit=5', {
                headers: { 'Accept-Language': 'id' }
            });
            const items = await res.json();

            if (!Array.isArray(items) || items.length === 0) {
                resultsBox.innerHTML = '<div class="p-3 text-xs text-rose-700 font-semibold">Lokasi tidak ditemukan. Coba kata kunci lain.</div>';
                return;
            }

            resultsBox.innerHTML = items.map(item => {
                const safeName = (item.name || item.display_name || '').replace(/"/g, '&quot;');
                return '<button type="button" class="w-full text-left px-3.5 py-2.5 hover:bg-emerald-500/10 transition-colors flex items-start gap-2.5" ' +
                    'data-lat="' + item.lat + '" data-lng="' + item.lon + '" data-name="' + safeName + '">' +
                    '<i class="ph-fill ph-map-pin text-emerald-600 text-sm mt-0.5 shrink-0"></i>' +
                    '<span class="text-xs text-ink font-semibold leading-snug line-clamp-2">' + item.display_name + '</span>' +
                    '</button>';
            }).join('');

            resultsBox.querySelectorAll('button[data-lat]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const lat = parseFloat(this.getAttribute('data-lat'));
                    const lng = parseFloat(this.getAttribute('data-lng'));
                    setMapPinPosition(lat, lng, true);
                    resultsBox.classList.add('hidden');
                });
            });
        } catch (e) {
            resultsBox.innerHTML = '<div class="p-3 text-xs text-rose-700">Gagal menghubungi layanan pencarian peta.</div>';
        }
    }

    async function saveLokasiConfig(e) {
        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
        }

        const btnLabel = document.getElementById('btnSaveLokasiLabel');
        const originalText = btnLabel ? btnLabel.textContent : 'Simpan Titik Lokasi & Geofence';
        if (btnLabel) btnLabel.textContent = 'Menyimpan ke Server...';

        const cfg = getConfig();
        const latVal = document.getElementById('lokasiLatInput') ? document.getElementById('lokasiLatInput').value : null;
        const lngVal = document.getElementById('lokasiLngInput') ? document.getElementById('lokasiLngInput').value : null;
        const radVal = document.getElementById('lokasiRadiusInput') ? document.getElementById('lokasiRadiusInput').value : null;
        const tokenVal = document.getElementById('lokasiQrTokenInput') ? document.getElementById('lokasiQrTokenInput').value : cfg.qrToken;
        const nameVal = document.getElementById('lokasiNamaInput') ? document.getElementById('lokasiNamaInput').value : cfg.officeName;
        const ipVal = document.getElementById('lokasiAllowedIpsInput') ? document.getElementById('lokasiAllowedIpsInput').value : '';

        const payload = {
            nama_lokasi: nameVal,
            latitude: parseCoord(latVal, cfg.latitude),
            longitude: parseCoord(lngVal, cfg.longitude),
            radius_meters: parseInt(radVal, 10) || 10,
            qr_token: tokenVal,
            allowed_ips: ipVal
        };

        try {
            const response = await fetch(cfg.updateLokasiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': cfg.csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const lok = data.lokasi;
                const banner = document.getElementById('bannerOfficeLabel');
                if (banner) banner.textContent = 'EXECUTIVE HRD · ' + lok.nama_lokasi.toUpperCase() + ' (' + lok.radius_meters + 'M)';
                const modalOffice = document.getElementById('modalQrOfficeName');
                if (modalOffice) modalOffice.textContent = lok.nama_lokasi;
                const activeToken = document.getElementById('activeQrTokenText');
                if (activeToken) activeToken.textContent = 'Token: ' + lok.qr_token;
                if (typeof window.showToast === 'function') {
                    window.showToast('Lokasi Diperbarui', data.message, true);
                }
            } else {
                if (typeof window.showToast === 'function') {
                    window.showToast('Gagal Menyimpan', data.message || 'Periksa kembali input koordinat dan radius.', false);
                }
            }
        } catch (err) {
            if (typeof window.showToast === 'function') {
                window.showToast('Kesalahan Jaringan', 'Tidak dapat menghubungi server.', false);
            }
        } finally {
            if (btnLabel) btnLabel.textContent = originalText;
        }
    }

    // =========================================================
    // EXPORT GLOBALS TO WINDOW
    // =========================================================
    window.openQrModal = openQrModal;
    window.closeQrModal = closeQrModal;
    window.renderModalQr = renderModalQr;
    window.rotateQrToken = rotateQrToken;
    window.printQrSheet = printQrSheet;
    window.generateNewQrTokenField = generateNewQrTokenField;
    window.appendCurrentClientIp = appendCurrentClientIp;

    window.filterAdminLogs = filterAdminLogs;
    window.filterAdminDivision = filterAdminDivision;
    window.searchAdminLogs = searchAdminLogs;
    window.decideLeave = decideLeave;
    window.handleDeviceReset = handleDeviceReset;

    window.initGeofenceMap = initGeofenceMap;
    window.setMapPinPosition = setMapPinPosition;
    window.searchMapLocation = searchMapLocation;
    window.saveLokasiConfig = saveLokasiConfig;
    window.applyRadiusToCircle = applyRadiusToCircle;
    window.syncCoordInputs = syncCoordInputs;

    // Boot on load
    function boot() {
        initGeofenceMap();
        if (window.location.hash === '#modalQr') {
            setTimeout(openQrModal, 250);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    window.addEventListener('resize', () => {
        if (geofenceMap) geofenceMap.invalidateSize();
    });

    window.addEventListener('hashchange', () => {
        setTimeout(() => {
            if (geofenceMap) geofenceMap.invalidateSize();
        }, 150);
    });

    document.addEventListener('click', function (e) {
        const resultsBox = document.getElementById('mapSearchResults');
        const searchInput = document.getElementById('mapSearchInput');
        if (resultsBox && !resultsBox.contains(e.target) && e.target !== searchInput) {
            resultsBox.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeQrModal();
        }
    });

})(window, document);

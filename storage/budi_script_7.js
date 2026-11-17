
    const UPDATE_LOKASI_URL = "http:\/\/localhost:8000\/admin\/lokasi";
    const ADMIN_CSRF_TOKEN = "xiMqGPbZYaeNr2asrPYqPpVN8pQATg5LPAcqOYRq";

    let currentStatusFilter = 'semua';
    let currentDivisionFilter = 'semua';
    let currentSearchQuery = '';

    function applyAdminTableFilters() {
        const rows = document.querySelectorAll('.admin-log-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowDivision = row.getAttribute('data-division');
            const rowSearch = row.getAttribute('data-search') || '';

            const matchesStatus = currentStatusFilter === 'semua' || rowStatus === currentStatusFilter;
            const matchesDivision = currentDivisionFilter === 'semua' || rowDivision === currentDivisionFilter;
            const matchesSearch = !currentSearchQuery || rowSearch.includes(currentSearchQuery.toLowerCase());

            if (matchesStatus && matchesDivision && matchesSearch) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        const countEl = document.getElementById('adminRowCount');
        if (countEl) {
            countEl.textContent = `${visibleCount} dari 45 catatan`;
        }

        const emptyEl = document.getElementById('adminEmptyState');
        if (emptyEl) {
            emptyEl.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function filterAdminLogs(status, btnEl) {
        currentStatusFilter = status;
        document.querySelectorAll('.admin-tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        btnEl.classList.add('active');
        applyAdminTableFilters();
    }

    function filterAdminDivision(division) {
        currentDivisionFilter = division;
        applyAdminTableFilters();
    }

    function searchAdminLogs(query) {
        currentSearchQuery = query.trim();
        applyAdminTableFilters();
    }

    function actionLeave(isApprove) {
        const badge = document.getElementById('approvalQueueBadge');
        const actions = document.getElementById('approvalActionButtons');
        const rowPill = document.getElementById('rowSitiStatusPill');
        const kpiSub = document.getElementById('kpiLeaveSub');

        if (isApprove) {
            badge.className = 'badge-pill badge-emerald';
            badge.textContent = '0 Menunggu';
            actions.innerHTML = '<div class="col-span-2 text-xs font-bold text-emerald-700 py-2 text-center bg-white rounded-xl border border-emerald-500/20">Disetujui oleh Budi Santoso (HRD)</div>';
            if (rowPill) {
                rowPill.className = 'badge-pill badge-emerald';
                rowPill.textContent = 'Izin Disetujui';
            }
            if (kpiSub) {
                kpiSub.textContent = 'Seluruh berkas selesai ditinjau';
                kpiSub.className = 'text-[11px] font-semibold text-emerald-700';
            }
            showToast('Izin Disetujui', 'Status kehadiran Siti Nurhaliza telah diperbarui menjadi Izin Sakit.', true);
        } else {
            badge.className = 'badge-pill badge-neutral';
            badge.textContent = '0 Menunggu';
            actions.innerHTML = '<div class="col-span-2 text-xs font-bold text-rose-700 py-2 text-center bg-white rounded-xl border border-rose-500/20">Permohonan ditolak oleh HRD</div>';
            if (rowPill) {
                rowPill.className = 'badge-pill badge-rose';
                rowPill.textContent = 'Izin Ditolak';
            }
            showToast('Izin Ditolak', 'Pemberitahuan penolakan telah dikirim ke akun Siti Nurhaliza.', false);
        }
    }

    function handleDeviceReset(e) {
        e.preventDefault();
        const input = document.getElementById('resetDeviceNik');
        const target = input.value.trim() || 'Karyawan';
        showToast('Perangkat Direset', `Kunci perangkat untuk ${target} telah dibuka.`, true);
        input.value = '';
    }

    let qrGeneratorInstance = null;

    function renderModalQr(token) {
        const container = document.getElementById('modalQrCodeContainer');
        if (!container) return;
        container.innerHTML = '';

        let generated = false;
        if (typeof QRCode !== 'undefined') {
            try {
                qrGeneratorInstance = new QRCode(container, {
                    text: token,
                    width: 176,
                    height: 176,
                    colorDark: '#0B0F14',
                    colorLight: '#FFFFFF',
                    correctLevel: QRCode.CorrectLevel.M
                });
                generated = true;
            } catch (err) {
                console.warn('QRCode JS error:', err);
            }
        }

        if (!generated) {
            const img = document.createElement('img');
            img.src = `https://api.qrserver.com/v1/create-qr-code/?size=176x176&data=${encodeURIComponent(token)}`;
            img.alt = 'QR Code ' + token;
            img.className = 'w-[176px] h-[176px] mx-auto rounded-lg shadow-sm';
            img.onerror = function() {
                container.innerHTML = '<span class="text-xs text-rose-500 font-mono">Gagal memuat visual QR.</span>';
            };
            container.appendChild(img);
        }
    }

    function openQrModal() {
        const modal = document.getElementById('modalQr');
        if (modal) {
            modal.classList.remove('hidden');
            if (modal.style) modal.style.display = 'flex';
        }

        const tokenInput = document.getElementById('lokasiQrTokenInput');
        const token = tokenInput ? tokenInput.value : "LOC-PST-24D5";
        const tokenEl = document.getElementById('activeQrTokenText');
        if (tokenEl) {
            tokenEl.textContent = `Token: ${token}`;
        }
        renderModalQr(token);
    }
    window.openQrModal = openQrModal;

    function closeQrModal() {
        const modal = document.getElementById('modalQr');
        if (modal) {
            modal.classList.add('hidden');
            if (modal.style) modal.style.display = 'none';
        }
    }
    window.closeQrModal = closeQrModal;

    function printQrSheet() {
        const imgEl = document.querySelector('#modalQrCodeContainer img') || document.querySelector('#modalQrCodeContainer canvas');
        const qrSrc = imgEl ? (imgEl.src || (imgEl.toDataURL ? imgEl.toDataURL() : '')) : '';
        const officeName = document.getElementById('modalQrOfficeName')?.textContent || 'Kantor Pusat';
        const token = document.getElementById('lokasiQrTokenInput')?.value || "LOC-PST-24D5";

        const printWin = window.open('', '_blank', 'width=650,height=750');
        if (!printWin) {
            showToast('Popup Diblokir', 'Izinkan popup di browser untuk mencetak lembar QR.', false);
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
    window.printQrSheet = printQrSheet;

    function generateNewQrTokenField() {
        const randomHex = Math.floor(1000 + Math.random() * 9000).toString(16).toUpperCase();
        const newToken = `LOC-PST-${randomHex}`;
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

        if (typeof showToast === 'function') {
            showToast('Token QR Diperbarui', 'Token aktif: ' + newToken + '. Klik "Simpan Titik Lokasi & Geofence" untuk menyimpan.', true);
        }

        try {
            const latInput = document.getElementById('lokasiLatInput');
            const lngInput = document.getElementById('lokasiLngInput');
            const radiusInput = document.getElementById('lokasiRadiusInput');
            const namaInput = document.getElementById('lokasiNamaInput');
            const ipInput = document.getElementById('lokasiAllowedIpsInput');

            if (UPDATE_LOKASI_URL && ADMIN_CSRF_TOKEN) {
                await fetch(UPDATE_LOKASI_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': ADMIN_CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        nama_lokasi: namaInput ? namaInput.value : 'Kantor Pusat',
                        latitude: parseCoord(latInput ? latInput.value : null, -3.4424391),
                        longitude: parseCoord(lngInput ? lngInput.value : null, 114.8328872),
                        radius_meters: parseInt(radiusInput ? radiusInput.value : 10, 10) || 10,
                        qr_token: newToken,
                        allowed_ips: ipInput ? ipInput.value : ''
                    })
                });
            }
        } catch (err) {
            console.warn('Auto-save token silently deferred:', err);
        }
    }
    window.rotateQrToken = rotateQrToken;

    function appendCurrentClientIp(ip) {
        const ipInput = document.getElementById('lokasiAllowedIpsInput');
        if (!ipInput || !ip) return;
        const list = ipInput.value.split(',').map(item => item.trim()).filter(Boolean);
        if (!list.includes(ip)) {
            list.push(ip);
            ipInput.value = list.join(', ');
        }
    }

    function parseCoord(val, fallback) {
        if (val === null || val === undefined || val === '') return fallback;
        const normalized = String(val).replace(',', '.').trim();
        const num = parseFloat(normalized);
        return isNaN(num) ? fallback : num;
    }

    // =========================================================
    // INTERACTIVE GEOFENCE MAP (LEAFLET + GOJEK PIN + NOMINATIM)
    // =========================================================
    let geofenceMap = null;
    let officeMarker = null;
    let geofenceCircle = null;
    let streetLayer = null;
    let satelliteLayer = null;
    let isSatellite = false;
    let reverseGeocodeTimer = null;

    function initGeofenceMap() {
        const mapContainer = document.getElementById('adminGeofenceMap');
        if (!mapContainer) return;
        if (typeof L === 'undefined') {
            console.log('[hadir.in] Menunggu modul Leaflet dimuat...');
            if (!window._leafletFallbackAttempted) {
                window._leafletFallbackAttempted = true;
                const cdnScript = document.createElement('script');
                cdnScript.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                cdnScript.onload = function() {
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
            const latInput = document.getElementById('lokasiLatInput');
            const lngInput = document.getElementById('lokasiLngInput');
            const radiusInput = document.getElementById('lokasiRadiusInput');
            const radiusSlider = document.getElementById('lokasiRadiusSlider');

            const initialLat = parseCoord(latInput ? latInput.value : null, -3.4424391);
            const initialLng = parseCoord(lngInput ? lngInput.value : null, 114.8328872);
            const initialRadius = parseInt(radiusInput ? radiusInput.value : '10', 10) || 10;

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

            // Remove loading indicator once map canvas is created
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

            // Dragging the Gojek-style pin
            officeMarker.on('drag', function (e) {
                const pos = e.target.getLatLng();
                geofenceCircle.setLatLng(pos);
                syncCoordInputs(pos.lat, pos.lng, false);
            });

            officeMarker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                syncCoordInputs(pos.lat, pos.lng, true);
            });

            // Clicking anywhere on the map moves the pin there
            geofenceMap.on('click', function (e) {
                setMapPinPosition(e.latlng.lat, e.latlng.lng, false);
            });

            // Typing coordinates manually moves the pin
            function onManualCoordInput() {
                const lat = parseCoord(latInput.value, null);
                const lng = parseCoord(lngInput.value, null);
                if (lat !== null && lng !== null && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    officeMarker.setLatLng([lat, lng]);
                    geofenceCircle.setLatLng([lat, lng]);
                    geofenceMap.panTo([lat, lng]);
                    document.getElementById('liveCoordBadgeText').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    scheduleReverseGeocode(lat, lng);
                }
            }
            if (latInput) latInput.addEventListener('input', onManualCoordInput);
            if (lngInput) lngInput.addEventListener('input', onManualCoordInput);

            setTimeout(() => {
                if (geofenceMap) {
                    geofenceMap.invalidateSize();
                }
            }, 300);

            // Radius slider & number input sync
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

            // Layer switcher (Street vs Satellite)
            const btnToggleLayer = document.getElementById('btnToggleMapLayer');
            if (btnToggleLayer) {
                btnToggleLayer.addEventListener('click', function () {
                    isSatellite = !isSatellite;
                    if (isSatellite) {
                        geofenceMap.removeLayer(streetLayer);
                        geofenceMap.addLayer(satelliteLayer);
                        document.getElementById('mapLayerLabel').textContent = 'Peta Jalan';
                    } else {
                        geofenceMap.removeLayer(satelliteLayer);
                        geofenceMap.addLayer(streetLayer);
                        document.getElementById('mapLayerLabel').textContent = 'Satelit';
                    }
                });
            }

            // Use current device GPS button
            const btnGps = document.getElementById('btnUseMyGps');
            if (btnGps) {
                btnGps.addEventListener('click', function () {
                    if (!('geolocation' in navigator)) {
                        showToast('GPS Tidak Didukung', 'Browser Anda tidak mendukung Geolocation API.', false);
                        return;
                    }
                    showToast('Mencari Titik GPS...', 'Mengambil koordinat presisi dari perangkat Anda.', true);
                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            setMapPinPosition(pos.coords.latitude, pos.coords.longitude, true);
                            showToast('Titik GPS Ditemukan', `Akurasi ±${Math.round(pos.coords.accuracy)} meter.`, true);
                        },
                        function () {
                            showToast('Gagal Mengambil GPS', 'Pastikan izin lokasi browser diaktifkan.', false);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                });
            }

            // Search place button & Enter key
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
            console.error('Error saat inisialisasi peta Leaflet:', err);
            const statusEl = document.getElementById('mapStatusIndicator');
            if (statusEl) {
                statusEl.classList.remove('hidden');
                statusEl.innerHTML = '<div class="p-6 text-center text-xs text-rose-600 font-mono"><b>Gagal memuat peta:</b> ' + (err.message || err) + '</div>';
            } else if (mapContainer) {
                mapContainer.innerHTML = '<div class="p-6 text-center text-xs text-rose-600 font-mono"><b>Gagal memuat peta:</b> ' + (err.message || err) + '</div>';
            }
        }
    }
    window.initGeofenceMap = initGeofenceMap;

    function applyRadiusToCircle(radiusMeters) {
        if (geofenceCircle) {
            geofenceCircle.setRadius(radiusMeters);
        }
        const pill = document.getElementById('liveRadiusPill');
        if (pill) {
            pill.textContent = `Radius: ${radiusMeters} m`;
        }
    }

    function syncCoordInputs(lat, lng, triggerGeocode = true) {
        const roundedLat = Number(lat.toFixed(7));
        const roundedLng = Number(lng.toFixed(7));
        document.getElementById('lokasiLatInput').value = roundedLat;
        document.getElementById('lokasiLngInput').value = roundedLng;
        document.getElementById('liveCoordBadgeText').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
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
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`, {
                headers: { 'Accept-Language': 'id' }
            });
            const data = await res.json();
            if (data && data.display_name) {
                label.textContent = data.display_name;
            } else {
                label.textContent = `Titik Koordinat (${lat.toFixed(6)}, ${lng.toFixed(6)})`;
            }
        } catch (e) {
            label.textContent = `Titik Koordinat (${lat.toFixed(6)}, ${lng.toFixed(6)})`;
        }
    }

    async function searchMapLocation(query) {
        const q = (query || '').trim();
        const resultsBox = document.getElementById('mapSearchResults');
        if (!q || !resultsBox) return;

        resultsBox.innerHTML = '<div class="p-3 text-xs text-muted">Mencari lokasi...</div>';
        resultsBox.classList.remove('hidden');

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(q)}&limit=5`, {
                headers: { 'Accept-Language': 'id' }
            });
            const items = await res.json();

            if (!Array.isArray(items) || items.length === 0) {
                resultsBox.innerHTML = '<div class="p-3 text-xs text-rose-700 font-semibold">Lokasi tidak ditemukan. Coba kata kunci lain.</div>';
                return;
            }

            resultsBox.innerHTML = items.map(item => `
                <button type="button" class="w-full text-left px-3.5 py-2.5 hover:bg-emerald-500/10 transition-colors flex items-start gap-2.5"
                        data-lat="${item.lat}" data-lng="${item.lon}" data-name="${(item.name || item.display_name || '').replace(/"/g, '&quot;')}">
                    <i class="ph-fill ph-map-pin text-emerald-600 text-sm mt-0.5 shrink-0"></i>
                    <span class="text-xs text-ink font-semibold leading-snug line-clamp-2">${item.display_name}</span>
                </button>
            `).join('');

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

        const payload = {
            nama_lokasi: document.getElementById('lokasiNamaInput').value,
            latitude: parseCoord(document.getElementById('lokasiLatInput').value, -3.4424391),
            longitude: parseCoord(document.getElementById('lokasiLngInput').value, 114.8328872),
            radius_meters: parseInt(document.getElementById('lokasiRadiusInput').value, 10) || 10,
            qr_token: document.getElementById('lokasiQrTokenInput').value,
            allowed_ips: document.getElementById('lokasiAllowedIpsInput').value,
        };

        try {
            const response = await fetch(UPDATE_LOKASI_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': ADMIN_CSRF_TOKEN,
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const lok = data.lokasi;
                document.getElementById('bannerOfficeLabel').textContent = `EXECUTIVE HRD · ${lok.nama_lokasi.toUpperCase()} (${lok.radius_meters}M)`;
                document.getElementById('modalQrOfficeName').textContent = lok.nama_lokasi;
                document.getElementById('activeQrTokenText').textContent = `Token: ${lok.qr_token}`;
                showToast('Lokasi Diperbarui', data.message, true);
            } else {
                showToast('Gagal Menyimpan', data.message || 'Periksa kembali input koordinat dan radius.', false);
            }
        } catch (err) {
            showToast('Kesalahan Jaringan', 'Tidak dapat menghubungi server.', false);
        } finally {
            if (btnLabel) btnLabel.textContent = originalText;
        }
    }

    function bootAdminDashboard() {
        initGeofenceMap();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootAdminDashboard);
    } else {
        bootAdminDashboard();
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

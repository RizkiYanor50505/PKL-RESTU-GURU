
        function renderModalQr(token) {
            const container = document.getElementById('modalQrCodeContainer');
            if (!container) return;
            container.innerHTML = '';
            const safeToken = String(token || 'LOC-PST-24D5').trim();

            let rendered = false;
            if (typeof QRCode !== 'undefined') {
                try {
                    new QRCode(container, {
                        text: safeToken,
                        width: 176,
                        height: 176,
                        colorDark: '#0B0F14',
                        colorLight: '#FFFFFF',
                        correctLevel: QRCode.CorrectLevel.M
                    });
                    rendered = true;
                } catch (e) {
                    console.warn('QRCode JS error:', e);
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
        window.renderModalQr = renderModalQr;

        async function rotateQrToken() {
            const randomHex = Math.floor(1000 + Math.random() * 9000).toString(16).toUpperCase();
            const newToken = 'LOC-PST-' + randomHex;

            const qrInput = document.getElementById('lokasiQrTokenInput');
            if (qrInput) {
                qrInput.value = newToken;
            }

            const tokenEl = document.getElementById('activeQrTokenText');
            if (tokenEl) {
                tokenEl.textContent = 'Token: ' + newToken;
            }

            renderModalQr(newToken);

            if (typeof showToast === 'function') {
                showToast('Token QR Diperbarui', 'Token aktif: ' + newToken + '. Klik "Simpan Titik Lokasi & Geofence" untuk menyimpan.', true);
            }

            // Silent auto-save to database if admin update URL is available
            try {
                const updateUrl = "http:\/\/localhost:8000\/admin\/lokasi";
                const csrfToken = "xiMqGPbZYaeNr2asrPYqPpVN8pQATg5LPAcqOYRq";
                const latInput = document.getElementById('lokasiLatInput');
                const lngInput = document.getElementById('lokasiLngInput');
                const radiusInput = document.getElementById('lokasiRadiusInput');
                const namaInput = document.getElementById('lokasiNamaInput');
                const ipInput = document.getElementById('lokasiAllowedIpsInput');

                if (updateUrl && csrfToken) {
                    await fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            nama_lokasi: namaInput ? namaInput.value : 'Kantor Pusat',
                            latitude: parseFloat(latInput ? latInput.value.replace(',', '.') : -3.4424391),
                            longitude: parseFloat(lngInput ? lngInput.value.replace(',', '.') : 114.8328872),
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

        function openQrModal() {
            const modal = document.getElementById('modalQr');
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            }
            const tokenInput = document.getElementById('lokasiQrTokenInput');
            const token = tokenInput ? tokenInput.value : "LOC-PST-24D5";
            const tokenEl = document.getElementById('activeQrTokenText');
            if (tokenEl) {
                tokenEl.textContent = 'Token: ' + token;
            }
            renderModalQr(token);
        }
        window.openQrModal = openQrModal;

        function closeQrModal() {
            const modal = document.getElementById('modalQr');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
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
                if (typeof showToast === 'function') {
                    showToast('Popup Diblokir', 'Izinkan popup di browser untuk mencetak lembar QR.', false);
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
        window.printQrSheet = printQrSheet;
    
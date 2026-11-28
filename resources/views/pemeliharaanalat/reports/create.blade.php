@extends('pemeliharaanalat.layouts.app')

@section('title', 'Lapor Kendala')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.reports.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Laporan
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1>Lapor Kendala Mesin</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9">
            <form action="{{ route('pemeliharaanalat.reports.store') }}" method="POST" enctype="multipart/form-data" class="card-panel" id="form-report">
                @csrf
                <div class="panel-body p-4">
                    <div class="mb-4">
                        <label for="asset_id" class="form-label">Mesin / Aset <span class="text-danger">*</span></label>
                        <select name="asset_id" id="asset_id" class="form-select form-select-lg @error('asset_id') is-invalid @enderror" required>
                            <option value="">— Pilih mesin yang bermasalah —</option>
                            @foreach($branches as $branch)
                                <optgroup label="🏢 {{ $branch->name }} ({{ $branch->city }})">
                                    @foreach($branch->assets as $asset)
                                        <option value="{{ $asset->id }}"
                                                data-branch="{{ $branch->name }} ({{ $branch->city }})"
                                                data-code="{{ $asset->code }}"
                                                data-location="{{ $asset->location }}"
                                                data-brand="{{ $asset->brand ?? $asset->subtitle }}"
                                                data-subtitle="{{ $asset->subtitle }}"
                                                data-status="{{ $asset->status->label() }}"
                                                @selected(old('asset_id', $selectedAsset) == $asset->id)>
                                            {{ $asset->name }} ({{ $asset->code }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('asset_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        {{-- Auto-fill info mesin --}}
                        <div class="info-box mt-3 d-none" id="asset-info">
                            <div class="row g-2">
                                <div class="col-sm-6"><i class="bi bi-buildings me-1 text-primary"></i><strong>Cabang:</strong> <span id="info-branch">-</span></div>
                                <div class="col-sm-6"><i class="bi bi-upc-scan me-1"></i><strong>Kode:</strong> <span id="info-code">-</span></div>
                                <div class="col-sm-6"><i class="bi bi-geo-alt me-1"></i><strong>Lokasi:</strong> <span id="info-location">-</span></div>
                                <div class="col-sm-6"><i class="bi bi-cpu me-1"></i><strong>Merek/Tipe:</strong> <span id="info-brand">-</span></div>
                                <div class="col-sm-12"><i class="bi bi-activity me-1"></i><strong>Status saat ini:</strong> <span id="info-status">-</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reporter_name" class="form-label">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" name="reporter_name" id="reporter_name"
                                value="{{ old('reporter_name', auth()->user()?->name ?? session('auth_user.name', '')) }}"
                                class="form-control @error('reporter_name') is-invalid @enderror" maxlength="100" required>
                        @error('reporter_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="issue_description" class="form-label">Apa kendalanya? <span class="text-danger">*</span></label>
                        <textarea name="issue_description" id="issue_description" rows="5" maxlength="2000"
                                  class="form-control @error('issue_description') is-invalid @enderror"
                                  placeholder="Ceritakan dengan bahasa sehari-hari. Contoh: Hasil cetak bergaris, mesin berbunyi kasar, tinta cepat habis..."
                                  required>{{ old('issue_description') }}</textarea>
                        @error('issue_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-flex justify-content-between align-items-center mb-2">
                            <span><i class="bi bi-camera me-1 text-primary"></i> Foto Dokumentasi Kendala <span class="text-muted small fw-normal">(Opsional)</span></span>
                            <span class="badge text-bg-light border text-muted fw-normal" style="font-size: .75rem">Maks. 5 MB</span>
                        </label>

                        {{-- Input file tersembunyi untuk submit form standar --}}
                        <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none @error('photo') is-invalid @enderror">
                        {{-- Input kamera khusus HP (memicu kamera belakang secara instan) --}}
                        <input type="file" id="photo-camera-mobile" accept="image/*" capture="environment" class="d-none">

                        {{-- Tombol Aksi Pengambilan Foto --}}
                        <div id="photo-actions" class="d-flex flex-wrap gap-2 align-items-center">
                            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm" id="btn-camera-trigger">
                                <i class="bi bi-camera-fill fs-5"></i>
                                <span class="fw-bold">Ambil Foto</span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2" id="btn-gallery-trigger">
                                <i class="bi bi-images"></i>
                                <span>Pilih dari Galeri / File</span>
                            </button>
                        </div>

                        @error('photo')<div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror

                        {{-- Pratinjau Foto Setelah Dijepret / Dipilih --}}
                        <div id="photo-preview-wrapper" class="mt-3 d-none">
                            <div class="p-2 border rounded bg-light d-inline-block position-relative shadow-sm" style="max-width: 100%;">
                                <img id="photo-preview" src="#" alt="Pratinjau Foto" class="rounded border" style="max-height: 240px; max-width: 100%; object-fit: contain; display: block; background: #fff;">
                                <div class="d-flex justify-content-between align-items-center mt-2 px-1 gap-2 flex-wrap">
                                    <span id="photo-filename" class="small text-muted text-truncate" style="max-width: 220px;"></span>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btn-retake-photo" style="font-size: .75rem;">
                                            <i class="bi bi-arrow-repeat me-1"></i> Foto Ulang
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" id="btn-remove-photo" style="font-size: .75rem;">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('pemeliharaanalat.reports.index') }}" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-primary px-4" id="btn-submit-report">
                            <i class="bi bi-send me-1"></i> Kirim Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Live Camera untuk Laptop / Komputer Desktop --}}
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cameraModalLabel"><i class="bi bi-camera-fill text-primary me-2"></i>Kamera Pengambilan Foto Kendala</h5>
                    <button type="button" class="btn-close" id="btn-close-camera-x" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-3 text-center bg-dark">
                    <div class="position-relative d-inline-block w-100" style="max-height: 440px; overflow: hidden; border-radius: 8px;">
                        <video id="camera-stream" autoplay playsinline class="w-100 rounded" style="max-height: 440px; object-fit: contain; background: #000;"></video>
                        <canvas id="camera-canvas" class="d-none"></canvas>
                    </div>
                    <div id="camera-error" class="alert alert-danger d-none mt-2 text-start small"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" id="btn-close-camera" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary btn-lg px-4 d-inline-flex align-items-center gap-2" id="btn-snap-photo">
                        <i class="bi bi-camera-fill fs-5"></i>
                        <span class="fw-bold">Jepret Foto Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const select = document.getElementById('asset_id');
        const box = document.getElementById('asset-info');
        const fields = { branch: 'info-branch', code: 'info-code', location: 'info-location', brand: 'info-brand', status: 'info-status' };

        function render() {
            const option = select.options[select.selectedIndex];
            if (!option || !option.value) { box.classList.add('d-none'); return; }

            Object.entries(fields).forEach(([key, id]) => {
                document.getElementById(id).textContent = option.dataset[key] || '-';
            });
            box.classList.remove('d-none');
        }

        select.addEventListener('change', render);
        render();

        // Elemen Foto & Kamera
        const photoInput = document.getElementById('photo-input');
        const photoMobileInput = document.getElementById('photo-camera-mobile');
        const btnCameraTrigger = document.getElementById('btn-camera-trigger');
        const btnGalleryTrigger = document.getElementById('btn-gallery-trigger');
        const btnRetakePhoto = document.getElementById('btn-retake-photo');
        const btnRemovePhoto = document.getElementById('btn-remove-photo');
        const previewWrapper = document.getElementById('photo-preview-wrapper');
        const previewImg = document.getElementById('photo-preview');
        const photoFilename = document.getElementById('photo-filename');

        // Elemen Modal Webcam Laptop/Desktop
        const cameraModalEl = document.getElementById('cameraModal');
        const cameraModal = cameraModalEl ? bootstrap.Modal.getOrCreateInstance(cameraModalEl) : null;
        const videoStream = document.getElementById('camera-stream');
        const canvas = document.getElementById('camera-canvas');
        const btnSnapPhoto = document.getElementById('btn-snap-photo');
        const cameraError = document.getElementById('camera-error');
        let activeMediaStream = null;

        function isMobileDevice() {
            return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent)
                || (window.innerWidth <= 768 && navigator.maxTouchPoints > 0);
        }

        function applyFile(file) {
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                photoFilename.textContent = file.name + ' (' + sizeMb + ' MB)';
                previewWrapper.classList.remove('d-none');
            };
            reader.readAsDataURL(file);

            // Pasang ke photoInput utama lewat DataTransfer jika dari input lain/webcam
            if (photoInput.files[0] !== file) {
                try {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    photoInput.files = dt.files;
                } catch (e) {
                    console.log('DataTransfer fallback', e);
                }
            }
        }

        // 1. Trigger Kamera Langsung
        btnCameraTrigger.addEventListener('click', function () {
            if (isMobileDevice()) {
                // Di HP: langsung buka kamera belakang asli HP!
                photoMobileInput.click();
            } else {
                // Di Laptop/Desktop: buka popup modal live camera
                openWebcam();
            }
        });

        // Tangkap hasil dari kamera HP
        photoMobileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                applyFile(this.files[0]);
            }
        });

        // 2. Trigger Galeri / File Manager
        btnGalleryTrigger.addEventListener('click', function () {
            photoInput.click();
        });

        photoInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                applyFile(this.files[0]);
            }
        });

        // 3. Tombol Foto Ulang & Hapus
        btnRetakePhoto.addEventListener('click', function () {
            btnCameraTrigger.click();
        });

        btnRemovePhoto.addEventListener('click', function () {
            photoInput.value = '';
            photoMobileInput.value = '';
            previewWrapper.classList.add('d-none');
            previewImg.src = '#';
        });

        // 4. Logika Webcam Laptop/Desktop
        async function openWebcam() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                // Fallback jika browser desktop tidak mendukung WebRTC
                photoMobileInput.click();
                return;
            }

            cameraError.classList.add('d-none');
            cameraModal.show();

            try {
                activeMediaStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false
                });
                videoStream.srcObject = activeMediaStream;
            } catch (err) {
                console.error('Kamera gagal diakses:', err);
                cameraError.textContent = 'Gagal mengakses kamera webcam. Pastikan Anda mengizinkan akses kamera di browser, atau gunakan tombol "Pilih dari Galeri / File".';
                cameraError.classList.remove('d-none');
            }
        }

        function stopWebcam() {
            if (activeMediaStream) {
                activeMediaStream.getTracks().forEach(track => track.stop());
                activeMediaStream = null;
            }
            if (videoStream) {
                videoStream.srcObject = null;
            }
        }

        if (cameraModalEl) {
            cameraModalEl.addEventListener('hidden.bs.modal', stopWebcam);
        }

        // Jepret foto dari webcam
        btnSnapPhoto.addEventListener('click', function () {
            if (!videoStream.videoWidth) return;

            canvas.width = videoStream.videoWidth;
            canvas.height = videoStream.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(videoStream, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(function (blob) {
                if (!blob) return;
                const capturedFile = new File([blob], 'foto-kendala-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                applyFile(capturedFile);
                cameraModal.hide();
            }, 'image/jpeg', 0.88);
        });
    })();
</script>
@endpush

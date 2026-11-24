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
            <p>Isi singkat saja — pilih mesin, tulis kendalanya, kirim. Tim teknisi akan segera menindaklanjuti.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9">
            <form action="{{ route('pemeliharaanalat.reports.store') }}" method="POST" class="card-panel" id="form-report">
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
                        <div class="form-text">Semua mesin percetakan dianggap penting — tidak perlu memilih tingkat urgensi.</div>
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
    })();
</script>
@endpush

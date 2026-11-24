@php($isEdit = $asset->exists)

<div class="row g-3">
    <div class="col-md-6">
        <label for="branch_id" class="form-label">Cabang <span class="text-danger">*</span></label>
        <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
            <option value="">-- Pilih Cabang Penempatan --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $asset->branch_id) === (string) $branch->id)>
                    {{ $branch->name }} ({{ $branch->city }})
                </option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">Kode Aset <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" maxlength="30" value="{{ old('code', $asset->code) }}"
               class="form-control mono @error('code') is-invalid @enderror" placeholder="AST-DP-001" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="name" class="form-label">Nama Aset <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" maxlength="150" value="{{ old('name', $asset->name) }}"
               class="form-control @error('name') is-invalid @enderror" placeholder="Mesin Digital Printing 01" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="subtitle" class="form-label">Deskripsi Singkat / Tipe</label>
        <input type="text" name="subtitle" id="subtitle" maxlength="200" value="{{ old('subtitle', $asset->subtitle) }}"
               class="form-control @error('subtitle') is-invalid @enderror" placeholder="Mesin Cetak Banner & Spanduk Outdoor">
        @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="location" class="form-label">Lokasi <span class="text-danger">*</span></label>
        <input type="text" name="location" id="location" maxlength="150" value="{{ old('location', $asset->location) }}"
               list="location-options" class="form-control @error('location') is-invalid @enderror"
               placeholder="Workshop Cetak Outdoor" required>
        <datalist id="location-options">
            @foreach(\App\Models\Asset::query()->distinct()->orderBy('location')->pluck('location') as $loc)
                <option value="{{ $loc }}">
            @endforeach
        </datalist>
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="brand" class="form-label">Merek / Model</label>
        <input type="text" name="brand" id="brand" maxlength="150" value="{{ old('brand', $asset->brand) }}"
               class="form-control @error('brand') is-invalid @enderror" placeholder="Roland Soljet Pro 4 XR-640">
        @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Keterangan</label>
        <textarea name="description" id="description" rows="3" maxlength="2000"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="Catatan tambahan tentang aset ini (opsional)">{{ old('description', $asset->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if($isEdit)
        <div class="col-12">
            <div class="info-box">
                <i class="bi bi-activity me-1"></i>
                Status aset saat ini: <x-badge :enum="$asset->status" class="ms-1" />
                <span class="ms-2">Status dihitung otomatis dari laporan kendala yang aktif dan tidak diubah manual.</span>
            </div>
        </div>
    @endif
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ $isEdit ? route('pemeliharaanalat.assets.show', $asset) : route('pemeliharaanalat.assets.index') }}" class="btn btn-light border">Batal</a>
    <button type="submit" class="btn btn-primary px-4" id="btn-save-asset">
        <i class="bi bi-check-lg me-1"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Aset' }}
    </button>
</div>

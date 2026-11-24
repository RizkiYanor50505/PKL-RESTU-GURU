@php($isEdit = $branch->exists)

<div class="row g-3">
    <div class="col-md-3">
        <label for="code" class="form-label">Kode Cabang <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" maxlength="20" value="{{ old('code', $branch->code) }}"
               class="form-control mono @error('code') is-invalid @enderror" placeholder="BJB" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Contoh: BJB, BJM, MTP</div>
    </div>

    <div class="col-md-5">
        <label for="name" class="form-label">Nama Cabang <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" maxlength="100" value="{{ old('name', $branch->name) }}"
               class="form-control @error('name') is-invalid @enderror" placeholder="Cabang Banjarbaru" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="city" class="form-label">Kota <span class="text-danger">*</span></label>
        <input type="text" name="city" id="city" maxlength="100" value="{{ old('city', $branch->city) }}"
               class="form-control @error('city') is-invalid @enderror" placeholder="Banjarbaru" required>
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="address" class="form-label">Alamat Lengkap</label>
        <input type="text" name="address" id="address" maxlength="255" value="{{ old('address', $branch->address) }}"
               class="form-control @error('address') is-invalid @enderror" placeholder="Jl. A. Yani Km. 34 No. 12, Loktabat Utara">
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="phone" class="form-label">Nomor Telepon / WhatsApp</label>
        <input type="text" name="phone" id="phone" maxlength="50" value="{{ old('phone', $branch->phone) }}"
               class="form-control @error('phone') is-invalid @enderror" placeholder="(0511) 4772890">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="manager_name" class="form-label">Penanggung Jawab / Kepala</label>
        <input type="text" name="manager_name" id="manager_name" maxlength="100" value="{{ old('manager_name', $branch->manager_name) }}"
               class="form-control @error('manager_name') is-invalid @enderror" placeholder="Rahmat Hidayat">
        @error('manager_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ $isEdit ? route('pemeliharaanalat.branches.show', $branch) : route('pemeliharaanalat.branches.index') }}" class="btn btn-light border">Batal</a>
    <button type="submit" class="btn btn-primary px-4" id="btn-save-branch">
        <i class="bi bi-check-lg me-1"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Cabang' }}
    </button>
</div>

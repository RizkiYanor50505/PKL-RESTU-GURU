@php
    $isEdit = $user->exists;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" maxlength="100" value="{{ old('name', $user->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" maxlength="150" value="{{ old('email', $user->email) }}"
               class="form-control @error('email') is-invalid @enderror" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="branch_id" class="form-label">Penempatan Cabang</label>
        <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
            <option value="">— Kantor Pusat / Semua Cabang —</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $user->branch_id) === (string) $branch->id)>
                    {{ $branch->name }} ({{ $branch->city }})
                </option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Boleh dikosongkan untuk staf/admin pusat.</div>
    </div>

    <div class="col-md-6">
        <label for="jabatan" class="form-label">Jabatan</label>
        <input type="text" name="jabatan" id="jabatan" maxlength="100" value="{{ old('jabatan', $user->jabatan) }}"
               class="form-control @error('jabatan') is-invalid @enderror" placeholder="Operator Mesin Cetak">
        @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @php
        $userRoleValue = is_object($user->role) ? $user->role->value : (string) $user->role;
        if ($userRoleValue === 'manajer') { $userRoleValue = 'admin'; }
        if ($userRoleValue === 'karyawan') { $userRoleValue = 'pegawai'; }
        $userStatusValue = is_object($user->status) ? $user->status->value : ($user->is_active ? 'aktif' : 'nonaktif');
    @endphp

    <div class="col-md-6">
        <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
            @foreach($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $userRoleValue) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $userStatusValue) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @unless($isEdit)
        <div class="col-md-6">
            <label for="password" class="form-label">Kata Sandi <span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" minlength="8" autocomplete="new-password"
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Minimal 8 karakter.</div>
        </div>
        <div class="col-md-6">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" minlength="8" autocomplete="new-password"
                   class="form-control" required>
        </div>
    @endunless
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('pemeliharaanalat.users.index') }}" class="btn btn-light border">Batal</a>
    <button type="submit" class="btn btn-primary px-4" id="btn-save-user">
        <i class="bi bi-check-lg me-1"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Buat Akun' }}
    </button>
</div>

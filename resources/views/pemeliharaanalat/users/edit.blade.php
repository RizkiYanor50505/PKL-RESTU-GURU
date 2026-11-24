@extends('pemeliharaanalat.layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.users.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Kelola Pengguna
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1>Edit Pengguna</h1>
            <p>{{ $user->name }} · {{ $user->email }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <form action="{{ route('pemeliharaanalat.users.update', $user) }}" method="POST" class="card-panel">
                @csrf @method('PUT')
                <div class="panel-body p-4">
                    @include('pemeliharaanalat.users._form')
                </div>
            </form>
        </div>

        <div class="col-xl-4">
            <form action="{{ route('pemeliharaanalat.users.reset-password', $user) }}" method="POST" class="card-panel" id="form-reset-password">
                @csrf
                <div class="panel-head"><h2><i class="bi bi-key me-1 text-primary"></i> Reset Kata Sandi</h2></div>
                <div class="panel-body">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Kata sandi baru</label>
                        <input type="password" name="password" id="new_password" minlength="8" autocomplete="new-password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi kata sandi</label>
                        <input type="password" name="password_confirmation" id="new_password_confirmation" minlength="8" autocomplete="new-password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100" id="btn-reset-password">
                        <i class="bi bi-arrow-repeat me-1"></i> Reset Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('pemeliharaanalat.layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.users.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Kelola Pengguna
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1>Tambah Pengguna</h1>
            <p>Buat akun baru untuk staf cabang.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <form action="{{ route('pemeliharaanalat.users.store') }}" method="POST" class="card-panel">
                @csrf
                <div class="panel-body p-4">
                    @include('pemeliharaanalat.users._form')
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('pemeliharaanalat.layouts.app')

@section('title', 'Tambah Cabang')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.branches.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Cabang
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1>Tambah Cabang Baru</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <form action="{{ route('pemeliharaanalat.branches.store') }}" method="POST" class="card-panel">
                @csrf
                <div class="panel-body p-4">
                    @include('pemeliharaanalat.branches._form')
                </div>
            </form>
        </div>
    </div>
@endsection

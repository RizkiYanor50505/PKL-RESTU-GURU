@extends('pemeliharaanalat.layouts.app')

@section('title', 'Tambah Aset')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.assets.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Aset
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1>Tambah Aset</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <form action="{{ route('pemeliharaanalat.assets.store') }}" method="POST" class="card-panel">
                @csrf
                <div class="panel-body p-4">
                    @include('pemeliharaanalat.assets._form')
                </div>
            </form>
        </div>
    </div>
@endsection

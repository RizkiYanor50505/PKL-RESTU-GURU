@extends('pemeliharaanalat.layouts.app')

@section('title', 'Edit Aset')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.assets.show', $asset) }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Aset
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1>Edit Aset</h1>
            <p>{{ $asset->code }} · {{ $asset->name }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <form action="{{ route('pemeliharaanalat.assets.update', $asset) }}" method="POST" class="card-panel">
                @csrf @method('PUT')
                <div class="panel-body p-4">
                    @include('pemeliharaanalat.assets._form')
                </div>
            </form>
        </div>
    </div>
@endsection

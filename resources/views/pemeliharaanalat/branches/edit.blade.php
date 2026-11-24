@extends('pemeliharaanalat.layouts.app')

@section('title', 'Edit Cabang')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.branches.show', $branch) }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Cabang
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1>Edit Cabang</h1>
            <p>{{ $branch->code }} · {{ $branch->name }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <form action="{{ route('pemeliharaanalat.branches.update', $branch) }}" method="POST" class="card-panel">
                @csrf @method('PUT')
                <div class="panel-body p-4">
                    @include('pemeliharaanalat.branches._form')
                </div>
            </form>
        </div>
    </div>
@endsection

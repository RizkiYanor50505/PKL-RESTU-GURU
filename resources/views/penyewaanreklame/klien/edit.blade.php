@extends('penyewaanreklame.layouts.app')

@section('title', 'Edit Klien ' . $klien->nama_klien)

@section('content')
    <div>
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-violet-600">Dashboard</a>
            <span>/</span>
            <a href="{{ route('penyewaanreklame.klien.index') }}" class="hover:text-violet-600">Data Klien</a>
            <span>/</span>
            <span class="text-slate-700">Edit</span>
        </div>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Edit Data Klien</h1>
    </div>

    <form method="POST" action="{{ route('penyewaanreklame.klien.update', $klien) }}"
          class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        @csrf
        @method('PUT')
        @include('penyewaanreklame.klien._form')

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('penyewaanreklame.klien.show', $klien) }}"
               class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-violet-600 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection

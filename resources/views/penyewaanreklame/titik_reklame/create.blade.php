@extends('penyewaanreklame.layouts.app')

@section('title', 'Tambah Titik Reklame')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('penyewaanreklame.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('penyewaanreklame.titik-reklame.index') }}" class="hover:text-emerald-600">Titik Reklame</a>
                <span>/</span>
                <span class="text-slate-700">Tambah</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Tambah Titik Reklame Baru</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('penyewaanreklame.titik-reklame.store') }}" enctype="multipart/form-data"
          class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        @csrf
        @include('penyewaanreklame.titik_reklame._form')

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('penyewaanreklame.titik-reklame.index') }}"
               class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-obsidian px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                Simpan Titik Reklame
            </button>
        </div>
    </form>
@endsection


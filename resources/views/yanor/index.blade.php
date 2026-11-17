@extends('layouts.app')

@section('title', 'Modul Yanor · hadir.in')

@section('content')
<div class="min-h-screen bg-slate-50/50 pb-20 pt-8">
    <div class="mx-auto max-w-5xl px-4 sm:px-6">
        
        <!-- Header Wadah Modul -->
        <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-700 ring-1 ring-inset ring-purple-700/10">
                        <i class="ph ph-folder-open text-sm"></i> Wadah Projek Yanor
                    </span>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Modul & Fitur Yanor
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Ruang kerja khusus untuk integrasi fitur yang dikembangkan oleh Yanor.
                    </p>
                </div>
                <a href="{{ route('portal') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <i class="ph ph-squares-four"></i> Kembali ke Portal
                </a>
            </div>

            <!-- Petunjuk Penggunaan -->
            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-purple-100 bg-purple-50/50 p-4">
                    <div class="flex items-center gap-2 font-semibold text-purple-900">
                        <i class="ph ph-brackets-curly text-lg text-purple-600"></i>
                        <span>1. Controller</span>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-600">
                        Letakkan file Controller di:<br>
                        <code class="mt-1 block rounded bg-white px-2 py-1 font-mono text-purple-800 border border-purple-200">app/Http/Controllers/Yanor/</code>
                    </p>
                </div>

                <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
                    <div class="flex items-center gap-2 font-semibold text-emerald-900">
                        <i class="ph ph-browsers text-lg text-emerald-600"></i>
                        <span>2. Views (Tampilan)</span>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-600">
                        Letakkan file blade template di:<br>
                        <code class="mt-1 block rounded bg-white px-2 py-1 font-mono text-emerald-800 border border-emerald-200">resources/views/yanor/</code>
                    </p>
                </div>

                <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4">
                    <div class="flex items-center gap-2 font-semibold text-amber-900">
                        <i class="ph ph-arrows-split text-lg text-amber-600"></i>
                        <span>3. Routing</span>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-600">
                        Daftarkan URL / Route khusus di:<br>
                        <code class="mt-1 block rounded bg-white px-2 py-1 font-mono text-amber-800 border border-amber-200">routes/yanor.php</code>
                    </p>
                </div>
            </div>
        </div>

        <!-- Area Konten Fitur Yanor -->
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white/60 p-12 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                <i class="ph ph-code text-2xl"></i>
            </div>
            <h3 class="mt-4 text-base font-semibold text-slate-900">Wadah Siap Digunakan</h3>
            <p class="mt-1 text-sm text-slate-500 max-w-md mx-auto">
                Yanor dapat mulai memasukkan tabel, form, kartu data, atau fitur bisnisnya di halaman ini.
            </p>
        </div>

    </div>
</div>
@endsection


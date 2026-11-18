@if (session('success'))
    <div class="mb-5 flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
        <i class="ph ph-check-circle shrink-0 text-lg text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mb-5 flex items-center gap-2.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-sm">
        <i class="ph ph-warning-circle shrink-0 text-lg text-rose-600"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm">
        <div class="flex items-center gap-2 font-semibold">
            <i class="ph ph-warning text-lg text-rose-600"></i>
            <span>Terdapat kesalahan pada input:</span>
        </div>
        <ul class="mt-1.5 list-disc pl-6 text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

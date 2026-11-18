@php
    $mapsUrl = $titik->google_maps_url
        ?? ($titik->latitude && $titik->longitude
            ? "https://maps.google.com/?q={$titik->latitude},{$titik->longitude}"
            : null);
    $mapId = 'leaflet-map-' . $titik->id;
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    {{-- Kiri: Foto Fisik Papan Reklame --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
            Foto Fisik Papan Reklame
        </div>
        <div class="aspect-video bg-slate-100">
            @if ($titik->foto_papan)
                <img src="{{ asset('storage/' . $titik->foto_papan) }}" alt="{{ $titik->kode_titik }}"
                     class="h-full w-full object-cover">
            @else
                <div class="flex h-full w-full flex-col items-center justify-center p-6 text-center text-slate-400">
                    <i class="ph ph-image text-4xl"></i>
                    <span class="mt-2 text-xs">Foto fisik papan reklame belum diunggah</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Kanan: Peta Lokasi & Tautan Google Maps --}}
    <div class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                Peta Lokasi &amp; Koordinat Maps
            </span>
            @if ($mapsUrl)
                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 text-xs font-semibold text-violet-600 hover:text-violet-700">
                    <i class="ph ph-arrow-square-out text-sm"></i>
                    Buka di Google Maps
                </a>
            @endif
        </div>

        <div class="relative flex-1 bg-slate-50" style="min-height: 240px;">
            @if ($titik->latitude && $titik->longitude)
                <div id="{{ $mapId }}" class="h-full w-full" style="min-height: 240px;"></div>
            @elseif ($mapsUrl)
                <div class="flex h-full flex-col items-center justify-center p-6 text-center">
                    <i class="ph ph-map-pin text-4xl text-rose-500"></i>
                    <p class="mt-2 text-sm font-semibold text-slate-700">Tautan Google Maps Tersedia</p>
                    <p class="mt-1 text-xs text-slate-500">Koordinat lintang/bujur belum dipecah, gunakan tombol berikut untuk membuka peta langsung.</p>
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                       class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-4 py-2 text-xs font-semibold text-white hover:bg-violet-700">
                        <i class="ph ph-map-trifold text-base"></i>
                        Buka Google Maps
                    </a>
                </div>
            @else
                <div class="flex h-full flex-col items-center justify-center p-6 text-center text-slate-400">
                    <i class="ph ph-map-pin-line text-4xl"></i>
                    <p class="mt-2 text-xs">Koordinat Google Maps belum dilengkapi (baru tersedia alamat teks &amp; patokan lokasi).</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if ($titik->latitude && $titik->longitude)
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const lat = {{ $titik->latitude }};
                const lng = {{ $titik->longitude }};
                const map = L.map(@json($mapId)).setView([lat, lng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                const pinIcon = L.divIcon({
                    className: 'custom-reklame-pin',
                    html: '<div style="width:28px;height:28px;border-radius:9999px;background:#7c3aed;border:3px solid #ffffff;box-shadow:0 4px 10px rgba(0,0,0,0.25);"></div>',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                L.marker([lat, lng], { icon: pinIcon })
                    .addTo(map)
                    .bindPopup(@json($titik->kode_titik . ' — ' . $titik->wilayah))
                    .openPopup();
            });
        </script>
    @endpush
@endif


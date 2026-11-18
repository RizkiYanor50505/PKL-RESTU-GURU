@if ($errors->any())
    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
        <div class="font-semibold">Terdapat kesalahan input:</div>
        <ul class="mt-1 list-disc pl-5 text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="kode_titik" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Kode Titik <span class="text-rose-500">*</span>
        </label>
        <input id="kode_titik" name="kode_titik" type="text" required
               value="{{ old('kode_titik', $titikReklame->kode_titik) }}"
               placeholder="Contoh: RGP-BJB-001"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="kategori_space" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Kategori Space <span class="text-rose-500">*</span>
        </label>
        <select id="kategori_space" name="kategori_space" required
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="internal" @selected(old('kategori_space', $titikReklame->kategori_space) === 'internal')>
                Space Baliho Restu Guru Promosindo (Internal)
            </option>
            <option value="mitra" @selected(old('kategori_space', $titikReklame->kategori_space) === 'mitra')>
                Space Baliho Mitra Restu Guru Promosindo
            </option>
        </select>
    </div>

    <div>
        <label for="wilayah" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Wilayah <span class="text-rose-500">*</span>
        </label>
        <input id="wilayah" name="wilayah" type="text" list="wilayah-options" required
               value="{{ old('wilayah', $titikReklame->wilayah) }}"
               placeholder="Pilih atau ketik kabupaten/kota..."
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        <datalist id="wilayah-options">
            @foreach ($wilayahOptions as $opt)
                <option value="{{ $opt }}">
            @endforeach
        </datalist>
    </div>

    <div>
        <label for="status_titik" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Status Titik <span class="text-rose-500">*</span>
        </label>
        <select id="status_titik" name="status_titik" required
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="Tersedia" @selected(old('status_titik', $titikReklame->status_titik) === 'Tersedia')>Tersedia</option>
            <option value="Tersewa" @selected(old('status_titik', $titikReklame->status_titik) === 'Tersewa')>Tersewa</option>
            <option value="Maintenance" @selected(old('status_titik', $titikReklame->status_titik) === 'Maintenance')>Maintenance</option>
        </select>
    </div>

    <div class="sm:col-span-2">
        <label for="alamat_lokasi" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Alamat Lokasi &amp; Patokan <span class="text-rose-500">*</span>
        </label>
        <textarea id="alamat_lokasi" name="alamat_lokasi" rows="2" required
                  placeholder="Contoh: Jl. A. Yani Km. 34 (Patokan: Dekat Bundaran Simpang Empat)"
                  class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">{{ old('alamat_lokasi', $titikReklame->alamat_lokasi) }}</textarea>
    </div>

    <div>
        <label for="ukuran" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Ukuran Papan <span class="text-rose-500">*</span>
        </label>
        <input id="ukuran" name="ukuran" type="text" required
               value="{{ old('ukuran', $titikReklame->ukuran) }}"
               placeholder="Contoh: 4m x 8m (Vertikal)"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="spesifikasi_lampu" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Spesifikasi Lampu Penerangan
        </label>
        <input id="spesifikasi_lampu" name="spesifikasi_lampu" type="text"
               value="{{ old('spesifikasi_lampu', $titikReklame->spesifikasi_lampu) }}"
               placeholder="Contoh: Frontlite LED 4x100W / Backlite"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="latitude" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Latitude (Opsional)
        </label>
        <input id="latitude" name="latitude" type="number" step="0.00000001"
               value="{{ old('latitude', $titikReklame->latitude) }}"
               placeholder="Contoh: -3.440512"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="longitude" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Longitude (Opsional)
        </label>
        <input id="longitude" name="longitude" type="number" step="0.00000001"
               value="{{ old('longitude', $titikReklame->longitude) }}"
               placeholder="Contoh: 114.832145"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div class="sm:col-span-2">
        <label for="google_maps_url" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Link Google Maps (Opsional)
        </label>
        <input id="google_maps_url" name="google_maps_url" type="url"
               value="{{ old('google_maps_url', $titikReklame->google_maps_url) }}"
               placeholder="https://maps.google.com/?q=..."
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="no_izin" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Nomor Izin Reklame
        </label>
        <input id="no_izin" name="no_izin" type="text"
               value="{{ old('no_izin', $titikReklame->no_izin) }}"
               placeholder="Opsional"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="tgl_izin_berakhir" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                Izin Berakhir
            </label>
            <input id="tgl_izin_berakhir" name="tgl_izin_berakhir" type="date"
                   value="{{ old('tgl_izin_berakhir', optional($titikReklame->tgl_izin_berakhir)->format('Y-m-d')) }}"
                   class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        </div>
        <div>
            <label for="tgl_pajak_berakhir" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
                Pajak Berakhir
            </label>
            <input id="tgl_pajak_berakhir" name="tgl_pajak_berakhir" type="date"
                   value="{{ old('tgl_pajak_berakhir', optional($titikReklame->tgl_pajak_berakhir)->format('Y-m-d')) }}"
                   class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        </div>
    </div>

    <div class="sm:col-span-2">
        <label for="foto_papan" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Foto Fisik Papan Reklame (JPG/PNG/WEBP, maks 4MB)
        </label>
        <input id="foto_papan" name="foto_papan" type="file" accept="image/jpeg,image/png,image/webp"
               class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-obsidian file:px-3 file:py-1 file:text-xs file:font-semibold file:text-white">
        @if ($titikReklame->foto_papan)
            <div class="mt-3 flex items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                <img src="{{ asset('storage/' . $titikReklame->foto_papan) }}" alt="Foto saat ini"
                     class="h-16 w-24 rounded-md object-cover">
                <label class="inline-flex items-center gap-2 text-xs font-medium text-rose-600">
                    <input type="checkbox" name="hapus_foto" value="1" class="rounded border-slate-300 text-rose-600">
                    Hapus foto saat ini
                </label>
            </div>
        @endif
    </div>

    <div class="sm:col-span-2">
        <label for="keterangan" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Keterangan Tambahan
        </label>
        <textarea id="keterangan" name="keterangan" rows="2"
                  placeholder="Catatan arah pandang, akses lokasi, atau kondisi rangka..."
                  class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">{{ old('keterangan', $titikReklame->keterangan) }}</textarea>
    </div>
</div>


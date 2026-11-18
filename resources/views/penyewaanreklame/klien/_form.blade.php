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
        <label for="nama_klien" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Nama Perusahaan / Perorangan <span class="text-rose-500">*</span>
        </label>
        <input id="nama_klien" name="nama_klien" type="text" required
               value="{{ old('nama_klien', $klien->nama_klien) }}"
               placeholder="Contoh: PT Banua Niaga Mandiri"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="kategori_klien" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Kategori Klien <span class="text-rose-500">*</span>
        </label>
        <select id="kategori_klien" name="kategori_klien" required
                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="perusahaan" @selected(old('kategori_klien', $klien->kategori_klien) === 'perusahaan')>
                Perusahaan / Instansi (Bayar setelah Berita Acara &amp; Pajak)
            </option>
            <option value="perorangan" @selected(old('kategori_klien', $klien->kategori_klien) === 'perorangan')>
                Perorangan (Wajib bayar di awal sebelum produksi)
            </option>
        </select>
    </div>

    <div>
        <label for="nama_pic" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Nama PIC / Penanggung Jawab
        </label>
        <input id="nama_pic" name="nama_pic" type="text"
               value="{{ old('nama_pic', $klien->nama_pic) }}"
               placeholder="Contoh: Hendra Wijaya"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="no_wa" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Nomor WhatsApp / HP <span class="text-rose-500">*</span>
        </label>
        <input id="no_wa" name="no_wa" type="text" required
               value="{{ old('no_wa', $klien->no_wa) }}"
               placeholder="Contoh: 6281234567890"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Email
        </label>
        <input id="email" name="email" type="email"
               value="{{ old('email', $klien->email) }}"
               placeholder="Opsional"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div>
        <label for="npwp" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            NPWP (Untuk Faktur Pajak)
        </label>
        <input id="npwp" name="npwp" type="text"
               value="{{ old('npwp', $klien->npwp) }}"
               placeholder="Contoh: 01.234.567.8-731.000"
               class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
    </div>

    <div class="sm:col-span-2">
        <label for="alamat" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Alamat Lengkap
        </label>
        <textarea id="alamat" name="alamat" rows="2"
                  placeholder="Alamat kantor atau domisili klien..."
                  class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">{{ old('alamat', $klien->alamat) }}</textarea>
    </div>
</div>

@extends('absensi.layouts.app')

@section('title', 'Pengajuan Izin & Cuti')

@section('content')
<div class="space-y-6">

    <!-- Top Quota Bento Strip (4 Double-Bezel Pods) -->
    <section aria-label="Ringkasan Kuota Izin" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bezel-shell reveal-item">
            <div class="bezel-core p-5">
                <span class="text-xs font-bold text-muted">Sisa Cuti Tahunan</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold font-mono text-ink tabular-nums">{{ $remainingAnnualLeave }}</span>
                    <span class="text-xs font-semibold text-muted">/ {{ $annualQuota }} hari</span>
                </div>
                <div class="mt-2 text-[11px] font-semibold text-emerald-700">Aktif s/d 31 Des {{ now()->year }}</div>
            </div>
        </div>

        <div class="bezel-shell reveal-item">
            <div class="bezel-core p-5">
                <span class="text-xs font-bold text-muted">Menunggu Review</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span id="pendingCountValue" class="text-3xl font-extrabold font-mono text-ink tabular-nums">{{ $pendingCount }}</span>
                    <span class="text-xs font-semibold text-muted">berkas</span>
                </div>
                <div class="mt-2 text-[11px] font-semibold text-amber-800">SLA Keputusan ≤ 4 Jam</div>
            </div>
        </div>

        <div class="bezel-shell reveal-item">
            <div class="bezel-core p-5">
                <span class="text-xs font-bold text-muted">Telah Disetujui</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    @php($lastApproved = $requests->firstWhere('status_izin', 'disetujui'))
                <span class="text-3xl font-extrabold font-mono text-ink tabular-nums">{{ $approvedCount }}</span>
                    <span class="text-xs font-semibold text-muted">permohonan</span>
                </div>
                <div class="mt-2 text-[11px] font-semibold text-emerald-700">{{ $lastApproved ? $lastApproved->tgl_mulai->translatedFormat('j M').' · '.$lastApproved->jenis : 'Belum ada' }}</div>
            </div>
        </div>

        <div class="bezel-shell reveal-item">
            <div class="bezel-core p-5">
                <span class="text-xs font-bold text-muted">Permohonan Ditolak</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    @php($lastRejected = $requests->firstWhere('status_izin', 'ditolak'))
                <span class="text-3xl font-extrabold font-mono text-ink tabular-nums">{{ $rejectedCount }}</span>
                    <span class="text-xs font-semibold text-muted">permohonan</span>
                </div>
                <div class="mt-2 text-[11px] font-semibold text-muted">{{ $lastRejected ? $lastRejected->tgl_mulai->translatedFormat('j M').' · '.$lastRejected->jenis : 'Belum ada' }}</div>
            </div>
        </div>
    </section>

    <!-- Mobile Tab Switcher (<1024px) -->
    <div class="lg:hidden fluid-island-nav grid grid-cols-2 gap-1 p-1.5">
        <button type="button" id="tabFormBtn" onclick="switchIzinTab('form')" class="island-link active justify-center">
            <span>Formulir Baru</span>
        </button>
        <button type="button" id="tabListBtn" onclick="switchIzinTab('list')" class="island-link justify-center">
            <span>Riwayat Status ({{ $requests->count() }})</span>
        </button>
    </div>

    <!-- Main 2-Column Bento Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left (7 cols): Double-Bezel Form Enclosure -->
        <section id="sectionForm" class="lg:col-span-7 bezel-shell reveal-item">
            <div class="bezel-core p-6 sm:p-8 space-y-6">
                <div class="pb-4 border-b border-slate-900/[0.06]">
                    <span class="eyebrow-tag">Administrasi Kehadiran</span>
                    <h1 class="text-2xl font-extrabold text-ink tracking-tight mt-1.5">Formulir Pengajuan Izin & Cuti</h1>
                    <p class="text-xs text-muted mt-1">
                        Pilih kategori izin, rentang tanggal, serta unggah dokumen pendukung untuk diverifikasi oleh Manajer HRD.
                    </p>
                </div>

                <form id="leaveForm" onsubmit="submitLeaveForm(event)" class="space-y-5">

                    <!-- Kategori Izin -->
                    <fieldset>
                        <legend class="block text-xs font-bold uppercase tracking-wider text-muted mb-2.5">Kategori Permohonan</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="leave-type-card sub-tray p-4 flex items-start justify-between cursor-pointer ring-2 ring-obsidian bg-white transition-all duration-500 ease-spring" onclick="selectType(this, 'Sakit')">
                                <input type="radio" name="jenis" value="Sakit" checked class="sr-only">
                                <div>
                                    <span class="block text-sm font-extrabold text-ink">Izin Sakit</span>
                                    <span class="block text-xs text-muted mt-0.5">Wajib lampirkan surat dokter</span>
                                </div>
                                <i class="ph-fill ph-first-aid text-lg text-emerald-600"></i>
                            </label>

                            <label class="leave-type-card sub-tray p-4 flex items-start justify-between cursor-pointer hover:bg-white transition-all duration-500 ease-spring" onclick="selectType(this, 'Izin Khusus')">
                                <input type="radio" name="jenis" value="Izin Khusus" class="sr-only">
                                <div>
                                    <span class="block text-sm font-extrabold text-ink">Izin Khusus</span>
                                    <span class="block text-xs text-muted mt-0.5">Urusan keluarga / akademik</span>
                                </div>
                                <i class="ph-light ph-user-focus text-lg text-muted"></i>
                            </label>

                            <label class="leave-type-card sub-tray p-4 flex items-start justify-between cursor-pointer hover:bg-white transition-all duration-500 ease-spring" onclick="selectType(this, 'Cuti Tahunan')">
                                <input type="radio" name="jenis" value="Cuti Tahunan" class="sr-only">
                                <div>
                                    <span class="block text-sm font-extrabold text-ink">Cuti Tahunan</span>
                                    <span class="block text-xs text-muted mt-0.5">Memotong kuota cuti (Sisa 8h)</span>
                                </div>
                                <i class="ph-light ph-airplane-tilt text-lg text-muted"></i>
                            </label>

                            <label class="leave-type-card sub-tray p-4 flex items-start justify-between cursor-pointer hover:bg-white transition-all duration-500 ease-spring" onclick="selectType(this, 'Izin Terlambat')">
                                <input type="radio" name="jenis" value="Izin Terlambat" class="sr-only">
                                <div>
                                    <span class="block text-sm font-extrabold text-ink">Izin Terlambat</span>
                                    <span class="block text-xs text-muted mt-0.5">Kendala darurat perjalanan</span>
                                </div>
                                <i class="ph-light ph-clock-Afternoon text-lg text-muted"></i>
                            </label>
                        </div>
                    </fieldset>

                    <!-- Periode Tanggal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="leaveStartDate" class="block text-xs font-bold uppercase tracking-wider text-muted">Tanggal Mulai</label>
                            <input type="date" id="leaveStartDate" name="tgl_mulai" required value="{{ now()->toDateString() }}" class="haptic-input font-mono">
                        </div>
                        <div class="space-y-1.5">
                            <label for="leaveEndDate" class="block text-xs font-bold uppercase tracking-wider text-muted">Tanggal Selesai</label>
                            <input type="date" id="leaveEndDate" name="tgl_selesai" required value="{{ now()->toDateString() }}" class="haptic-input font-mono">
                        </div>
                    </div>

                    <!-- Conditional Field: Jam Estimasi Tiba -->
                    <div id="fieldJamTiba" class="hidden sub-tray p-4 space-y-1.5">
                        <label for="leaveArrivalTime" class="block text-xs font-bold uppercase tracking-wider text-muted">Estimasi Jam Tiba di Kantor</label>
                        <input type="time" id="leaveArrivalTime" name="jam_tiba" value="09:15" class="haptic-input font-mono max-w-xs">
                    </div>

                    <!-- Alasan -->
                    <div class="space-y-1.5">
                        <label for="leaveReasonInput" class="block text-xs font-bold uppercase tracking-wider text-muted">Alasan & Keterangan Lengkap</label>
                        <textarea id="leaveReasonInput" name="alasan" rows="3" required
                                  placeholder="Jelaskan alasan pengajuan secara spesifik..."
                                  class="haptic-textarea"></textarea>
                    </div>

                    <!-- Unggah Lampiran -->
                    <div class="space-y-2">
                        <label for="fotoBuktiInput" class="block text-xs font-bold uppercase tracking-wider text-muted">Lampiran Dokumen Bukti</label>
                        <div class="sub-tray p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-2xl bg-obsidian text-emerald-400 flex items-center justify-center shrink-0">
                                    <i class="ph-light ph-cloud-arrow-up text-xl"></i>
                                </span>
                                <div>
                                    <div id="fileNameTxt" class="text-sm font-bold text-ink">Belum ada berkas dipilih</div>
                                    <p id="fileConstraintHelp" class="text-xs text-muted">Format JPG, PNG, atau PDF · Maksimal 2 MB</p>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <input type="file" id="fotoBuktiInput" name="foto_bukti" accept="image/*,.pdf" aria-describedby="fileConstraintHelp" class="sr-only" onchange="previewUpload(event)">
                                <button type="button" onclick="triggerFileInput()" class="btn-island-secondary">
                                    <span>Pilih Berkas</span>
                                    <span class="icon-nest">
                                        <i class="ph-light ph-paperclip text-sm"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div id="uploadPreview" class="hidden sub-tray p-3.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img id="previewImg" src="" alt="Pratinjau bukti" class="h-12 w-12 rounded-xl object-cover border border-slate-900/10">
                                <div>
                                    <div id="previewFileName" class="text-xs font-bold text-ink">surat_dokter.jpg</div>
                                    <div class="text-[11px] font-semibold text-emerald-700">Siap dilampirkan</div>
                                </div>
                            </div>
                            <button type="button" onclick="triggerFileInput()" class="text-xs font-bold text-accent hover:underline">
                                Ganti
                            </button>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-wrap items-center justify-end gap-3">
                        <button type="submit" class="btn-island-primary w-full sm:w-auto">
                            <span>Kirim Permohonan Izin</span>
                            <span class="icon-nest">
                                <i class="ph-light ph-paper-plane-tilt text-base"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Right (5 cols): Status History Rail -->
        <aside id="sectionList" class="hidden lg:block lg:col-span-5 bezel-shell reveal-item">
            <div class="bezel-core p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-900/[0.06]">
                    <div>
                        <span class="eyebrow-tag">Log Permohonan</span>
                        <h2 class="text-lg font-extrabold text-ink tracking-tight mt-1">Riwayat Status Pengajuan</h2>
                    </div>
                    <span class="font-mono text-xs text-muted">{{ $requests->count() }} Berkas</span>
                </div>

                <div id="leaveHistoryItems" class="space-y-3.5">
                    @forelse($requests as $item)
                        @php($badge = match ($item->status_izin) {
                            'disetujui' => ['badge-emerald', 'Disetujui', 'text-emerald-700'],
                            'ditolak' => ['badge-rose', 'Ditolak', 'text-rose-700'],
                            default => ['badge-amber', 'Menunggu HRD', 'text-muted'],
                        })
                        <article class="sub-tray p-4 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-sm font-extrabold text-ink">{{ $item->jenis }} ({{ $item->durationDays() }} Hari)</span>
                                    <div class="font-mono text-xs text-muted mt-0.5">
                                        {{ $item->tgl_mulai->translatedFormat('j M Y') }}@if($item->durationDays() > 1) – {{ $item->tgl_selesai->translatedFormat('j M Y') }}@endif
                                    </div>
                                </div>
                                <span class="badge-pill {{ $badge[0] }}">{{ $badge[1] }}</span>
                            </div>
                            <p class="text-xs text-ink bg-white p-3 rounded-xl border border-slate-900/[0.05] leading-relaxed">"{{ $item->alasan }}"</p>
                            @if($item->isPending())
                                <div class="flex items-center justify-between text-[11px] text-muted font-mono">
                                    @if($item->foto_bukti)
                                        <a href="{{ route('karyawan.izin.bukti', $item) }}" target="_blank" rel="noopener" class="underline hover:text-ink">
                                            {{ basename($item->foto_bukti) }}
                                        </a>
                                    @else
                                        <span>Tanpa lampiran</span>
                                    @endif
                                    <span>{{ $item->created_at->timezone(config('attendance.timezone'))->format('j M · H:i') }} WIB</span>
                                </div>
                            @else
                                <div class="text-xs font-semibold {{ $badge[2] }}">
                                    {{ $badge[1] }} oleh {{ $item->approver?->name ?? 'HRD' }}@if($item->catatan_admin): {{ $item->catatan_admin }}@endif
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="sub-tray p-4 text-xs text-muted">Belum ada pengajuan izin. Kirim permohonan pertama Anda melalui formulir.</div>
                    @endforelse
                </div>
            </div>
        </aside>

    </div>

</div>
@endsection

@push('scripts')
<script>
    let selectedLeaveCategory = 'Sakit';

    function switchIzinTab(tabName) {
        const formSec = document.getElementById('sectionForm');
        const listSec = document.getElementById('sectionList');
        const formBtn = document.getElementById('tabFormBtn');
        const listBtn = document.getElementById('tabListBtn');

        if (tabName === 'form') {
            formSec.classList.remove('hidden');
            listSec.classList.add('hidden');
            formBtn.classList.add('active');
            listBtn.classList.remove('active');
        } else {
            formSec.classList.add('hidden');
            listSec.classList.remove('hidden');
            formBtn.classList.remove('active');
            listBtn.classList.add('active');
        }
    }

    function selectType(element, type) {
        selectedLeaveCategory = type;
        document.querySelectorAll('.leave-type-card').forEach(el => {
            el.className = 'leave-type-card sub-tray p-4 flex items-start justify-between cursor-pointer hover:bg-white transition-all duration-500 ease-spring';
        });
        element.className = 'leave-type-card sub-tray p-4 flex items-start justify-between cursor-pointer ring-2 ring-obsidian bg-white transition-all duration-500 ease-spring';

        const jamTibaField = document.getElementById('fieldJamTiba');
        if (type === 'Izin Terlambat') {
            jamTibaField.classList.remove('hidden');
        } else {
            jamTibaField.classList.add('hidden');
        }
    }

    function triggerFileInput() {
        document.getElementById('fotoBuktiInput').click();
    }

    function previewUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        document.getElementById('fileNameTxt').textContent = file.name;
        document.getElementById('previewFileName').textContent = file.name;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('uploadPreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('uploadPreview').classList.add('hidden');
        }
        showToast('Berkas Terpilih', `Lampiran ${file.name} siap dikirim.`, true);
    }

    async function submitLeaveForm(e) {
        e.preventDefault();
        const form = document.getElementById('leaveForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const data = new FormData(form);
        data.set('jenis', selectedLeaveCategory);
        if (selectedLeaveCategory !== 'Izin Terlambat') {
            data.delete('jam_tiba');
        }
        const file = document.getElementById('fotoBuktiInput').files[0];
        if (!file) {
            data.delete('foto_bukti');
        }

        submitBtn.disabled = true;
        try {
            const res = await fetch(@json(route('karyawan.izin.store')), {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                body: data,
            });
            const body = await res.json();

            if (!res.ok) {
                const firstError = body.errors ? Object.values(body.errors)[0][0] : null;
                showToast('Gagal Mengirim', firstError || body.message || 'Periksa kembali isian formulir.', false);
                submitBtn.disabled = false;
                return;
            }

            showToast('Permohonan Terkirim', body.message, true);
            setTimeout(() => window.location.reload(), 1100);
        } catch (err) {
            showToast('Kesalahan Jaringan', 'Tidak dapat menghubungi server.', false);
            submitBtn.disabled = false;
        }
    }
</script>
@endpush

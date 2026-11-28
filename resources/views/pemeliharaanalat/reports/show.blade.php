@extends('pemeliharaanalat.layouts.app')

@section('title', 'Detail Laporan')

@section('content')
    @php
        $user = auth()->user();
        $canHandle = $user?->canHandleReports() ?? (session('auth_role') === 'manajer');
        $cancelled = $report->status === \App\Enums\ReportStatus::Dibatalkan;
    @endphp

    <div class="mb-3">
        <a href="{{ route('pemeliharaanalat.reports.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Laporan
        </a>
    </div>

    <div class="page-head">
        <div>
            <h1 class="d-flex align-items-center gap-2 flex-wrap">
                <span class="mono" style="font-size:1.3rem">{{ $report->report_number }}</span>
                <x-badge :enum="$report->status" />
            </h1>
            <p>
                <span class="badge text-bg-light border text-dark me-1">
                    <i class="bi bi-buildings text-primary me-1"></i>{{ $report->branch?->name ?? $report->asset->branch?->name ?? 'Cabang' }}
                </span>
                {{ $report->asset->name }} · {{ $report->asset->location }}
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            @if($canHandle)
                <a href="{{ route('pemeliharaanalat.reports.print', $report) }}" target="_blank" rel="noopener" class="btn btn-primary" id="btn-print-spk">
                    <i class="bi bi-printer me-1"></i> Cetak SPK / Laporan Servis
                </a>
            @endif
            @if($user?->isAdmin() ?? (session('auth_role') === 'manajer'))
                <form action="{{ route('pemeliharaanalat.reports.destroy', $report) }}" method="POST"
                      onsubmit="return confirm('Hapus laporan {{ $report->report_number }}? Gunakan hanya untuk laporan uji coba atau duplikat.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" id="btn-delete-report"><i class="bi bi-trash me-1"></i> Hapus</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- ================= Kolom kiri: info + linimasa ================= --}}
        <div class="col-xl-5">
            <section class="card-panel mb-4">
                <div class="panel-head"><h2><i class="bi bi-info-circle me-1 text-primary"></i> Informasi Laporan</h2></div>
                <div class="panel-body">
                    <dl class="detail-list mb-3">
                        <dt>Cabang</dt>
                        <dd>
                            @php($br = $report->branch ?? $report->asset->branch)
                            @if($br)
                                <a href="{{ route('pemeliharaanalat.branches.show', $br) }}" class="fw-semibold text-decoration-none">
                                    <i class="bi bi-buildings text-primary me-1"></i>{{ $br->name }} ({{ $br->city }})
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>
                        <dt>Mesin</dt>
                        <dd>
                            @if($canHandle)
                                <a href="{{ route('pemeliharaanalat.assets.show', $report->asset) }}" class="fw-semibold text-decoration-none">{{ $report->asset->name }}</a>
                            @else
                                <span class="fw-semibold">{{ $report->asset->name }}</span>
                            @endif
                            <div class="cell-sub mono">{{ $report->asset->code }}</div>
                        </dd>
                        <dt>Merek / Tipe</dt><dd>{{ $report->asset->brand ?? '—' }}</dd>
                        <dt>Lokasi</dt><dd>{{ $report->asset->location }}</dd>
                        <dt>Pelapor</dt><dd>{{ $report->reporter_name }}</dd>
                        <dt>Waktu Lapor</dt><dd>{{ $report->reported_at->translatedFormat('d F Y, H:i') }} WITA</dd>
                    </dl>

                    <div class="form-label">Kendala yang dilaporkan</div>
                    <div class="issue-box">{{ $report->issue_description }}</div>

                    @if($report->hasPhoto())
                        <div class="mt-4">
                            <div class="form-label fw-bold d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-camera text-primary"></i> Foto Dokumentasi Kendala
                            </div>
                            <div class="card p-2 bg-light border shadow-sm" style="max-width: 460px;">
                                <a href="{{ $report->photoUrl() }}" target="_blank" title="Klik untuk membuka ukuran penuh">
                                    <img src="{{ $report->photoUrl() }}" alt="Foto Kendala {{ $report->report_number }}"
                                         class="img-fluid rounded border" style="max-height: 280px; width: 100%; object-fit: contain; background: #fff;">
                                </a>
                                <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                    <span class="small text-muted"><i class="bi bi-zoom-in me-1"></i> Klik foto untuk memperbesar</span>
                                    <a href="{{ $report->photoUrl() }}" download="kendala-{{ $report->report_number }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: .75rem;">
                                        <i class="bi bi-download me-1"></i> Unduh
                                    </a>
                                </div>
                            </div>
                        </div>
                    @elseif($report->photo_cleaned_at)
                        <div class="mt-4 p-3 border rounded bg-light text-muted small d-flex align-items-start gap-2">
                            <i class="bi bi-clock-history text-secondary fs-5 mt-n1"></i>
                            <div>
                                <strong>Dokumentasi Foto Telah Direset</strong><br>
                                Foto kendala awal telah dibersihkan otomatis pada {{ $report->photo_cleaned_at->translatedFormat('d F Y, H:i') }} sesuai kebijakan retensi penyimpanan 1 bulan. Riwayat penanganan & biaya tetap tersimpan permanen.
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <section class="card-panel">
                <div class="panel-head"><h2><i class="bi bi-clock-history me-1 text-primary"></i> Linimasa</h2></div>
                <div class="panel-body">
                    <ul class="timeline">
                        <li class="done">
                            <span class="dot"><i class="bi bi-megaphone"></i></span>
                            <div class="t-title">Laporan dibuat</div>
                            <div class="t-time">{{ $report->reported_at->translatedFormat('d M Y, H:i') }} · oleh {{ $report->reporter_name }}</div>
                        </li>
                        <li class="{{ $report->processed_at ? 'done' : '' }}">
                            <span class="dot"><i class="bi bi-gear-wide-connected"></i></span>
                            <div class="t-title">Sedang diproses</div>
                            <div class="t-time">
                                @if($report->processed_at)
                                    {{ $report->processed_at->translatedFormat('d M Y, H:i') }}
                                    @if($report->technician) · oleh {{ $report->technician->name }} @endif
                                @else
                                    Menunggu penanganan teknisi
                                @endif
                            </div>
                        </li>
                        @if($cancelled)
                            <li class="cancelled">
                                <span class="dot"><i class="bi bi-x-lg"></i></span>
                                <div class="t-title">Laporan dibatalkan</div>
                                <div class="t-time">{{ $report->updated_at->translatedFormat('d M Y, H:i') }}</div>
                            </li>
                        @else
                            <li class="{{ $report->resolved_at ? 'done final' : '' }}">
                                <span class="dot"><i class="bi bi-check2-circle"></i></span>
                                <div class="t-title">Selesai diperbaiki</div>
                                <div class="t-time">
                                    {{ $report->resolved_at ? $report->resolved_at->translatedFormat('d M Y, H:i') : 'Belum selesai' }}
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </section>
        </div>

        {{-- ================= Kolom kanan: penanganan ================= --}}
        <div class="col-xl-7">
            @if($canHandle)
                <form action="{{ route('pemeliharaanalat.reports.update', $report) }}" method="POST" class="card-panel" id="form-handling">
                    @csrf @method('PUT')
                    <div class="panel-head">
                        <h2><i class="bi bi-wrench-adjustable me-1 text-primary"></i> Penanganan Teknisi</h2>
                    </div>
                    <div class="panel-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status Penanganan <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->value }}" @selected(old('status', $report->status->value) === $status->value)>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="technician_id" class="form-label">Teknisi Penanggung Jawab</label>
                                <select name="technician_id" id="technician_id" class="form-select @error('technician_id') is-invalid @enderror">
                                    <option value="">— Belum ditugaskan —</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}"
                                            @selected((int) old('technician_id', $report->technician_id ?? ($user?->isTechnician() ? $user->id : null)) === $technician->id)>
                                            {{ $technician->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('technician_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="repair_notes" class="form-label">
                                    Uraian Penanganan <span class="text-danger d-none" id="notes-required">*</span>
                                </label>
                                <textarea name="repair_notes" id="repair_notes" rows="4" maxlength="3000"
                                          class="form-control @error('repair_notes') is-invalid @enderror"
                                          placeholder="Tindakan yang dilakukan, hasil pengecekan, kondisi akhir mesin...">{{ old('repair_notes', $report->repair_notes) }}</textarea>
                                @error('repair_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-7">
                                <label for="replaced_parts" class="form-label">Komponen / Suku Cadang Diganti</label>
                                <input type="text" name="replaced_parts" id="replaced_parts" maxlength="255"
                                       value="{{ old('replaced_parts', $report->replaced_parts) }}"
                                       class="form-control @error('replaced_parts') is-invalid @enderror"
                                       placeholder="Pisahkan dengan koma, mis: Damper DX5, Head Cable">
                                @error('replaced_parts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-5">
                                <label for="repair_cost" class="form-label">Biaya Servis (Realisasi)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="repair_cost" id="repair_cost" min="0" step="1000" inputmode="numeric"
                                           value="{{ old('repair_cost', $report->repair_cost !== null ? (int) $report->repair_cost : '') }}"
                                           class="form-control @error('repair_cost') is-invalid @enderror" placeholder="0">
                                    @error('repair_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
                            <div class="small text-muted"><i class="bi bi-magic me-1"></i>Status mesin akan menyesuaikan otomatis setelah disimpan.</div>
                            <button type="submit" class="btn btn-primary px-4" id="btn-save-handling">
                                <i class="bi bi-check-lg me-1"></i> Simpan Penanganan
                            </button>
                        </div>
                    </div>
                </form>
            @else
                {{-- Pegawai: tampilan baca-saja --}}
                <section class="card-panel">
                    <div class="panel-head"><h2><i class="bi bi-wrench-adjustable me-1 text-primary"></i> Penanganan</h2></div>
                    <div class="panel-body">
                        @if($report->status === \App\Enums\ReportStatus::Menunggu)
                            <div class="empty-state py-4">
                                <i class="bi bi-hourglass-split"></i>
                                Laporan Anda sudah diterima dan menunggu teknisi.
                            </div>
                        @else
                            <dl class="detail-list">
                                <dt>Teknisi</dt><dd>{{ $report->technician?->name ?? '—' }}</dd>
                                <dt>Uraian</dt><dd style="white-space:pre-line">{{ $report->repair_notes ?? '—' }}</dd>
                                <dt>Komponen diganti</dt>
                                <dd>
                                    @forelse($report->partsList() as $part)
                                        <span class="bdg bdg-secondary me-1">{{ $part }}</span>
                                    @empty —
                                    @endforelse
                                </dd>
                            </dl>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const status = document.getElementById('status');
        const notes = document.getElementById('repair_notes');
        const star = document.getElementById('notes-required');
        if (!status || !notes) return;

        function toggle() {
            const done = status.value === 'selesai';
            notes.required = done;
            star.classList.toggle('d-none', !done);
        }

        status.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush

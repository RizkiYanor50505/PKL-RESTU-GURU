<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPK Servis {{ $report->report_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 14mm 16mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #e5e9f0; font-family: 'Segoe UI', Arial, Helvetica, sans-serif; color: #111827; font-size: 11pt; line-height: 1.45; }

        .toolbar { position: sticky; top: 0; background: #0f2e22; color: #fff; padding: 10px 20px; display: flex; gap: 10px; align-items: center; justify-content: space-between; }
        .toolbar button, .toolbar a { background: #26966d; color: #fff; border: 0; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .toolbar a.ghost { background: transparent; border: 1px solid rgba(255,255,255,.35); }

        .sheet { width: 210mm; min-height: 297mm; margin: 18px auto; background: #fff; padding: 14mm 16mm; box-shadow: 0 10px 40px rgba(0,0,0,.18); }

        .kop { display: flex; align-items: center; gap: 14px; border-bottom: 3px double #0f2e22; padding-bottom: 10px; }
        .kop .logo { width: 54px; height: 54px; border-radius: 12px; background: #0f2e22; color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 20px; }
        .kop h1 { margin: 0; font-size: 16pt; color: #0f2e22; letter-spacing: .02em; }
        .kop p { margin: 0; font-size: 9.5pt; color: #4b5563; }

        .title { text-align: center; margin: 16px 0 12px; }
        .title h2 { margin: 0; font-size: 14pt; text-transform: uppercase; letter-spacing: .06em; }
        .title .no { font-size: 10.5pt; color: #374151; }

        table.info { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.info td { padding: 4px 6px; vertical-align: top; }
        table.info td.k { width: 36%; color: #4b5563; }
        table.info td.s { width: 14px; }

        h3 { font-size: 10.5pt; margin: 14px 0 6px; padding: 4px 8px; background: #0f2e22; color: #fff; text-transform: uppercase; letter-spacing: .05em; }

        .box { border: 1px solid #9ca3af; padding: 8px 10px; min-height: 22mm; white-space: pre-line; }
        .box.sm { min-height: 12mm; }

        table.grid { width: 100%; border-collapse: collapse; }
        table.grid th, table.grid td { border: 1px solid #9ca3af; padding: 6px 8px; text-align: left; vertical-align: top; }
        table.grid th { background: #f3f4f6; font-size: 10pt; }

        .sign { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 22px; text-align: center; page-break-inside: avoid; }
        .sign .role { font-size: 10pt; color: #374151; }
        .sign .space { height: 22mm; }
        .sign .name { font-weight: 700; border-top: 1px solid #111827; display: inline-block; min-width: 70%; padding-top: 3px; }

        .foot { margin-top: 18px; font-size: 8.5pt; color: #6b7280; text-align: center; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { margin: 0; box-shadow: none; width: auto; min-height: auto; padding: 0; }
            h3, .kop .logo, table.grid th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <span>Pratinjau SPK · {{ $report->report_number }}</span>
    <span style="display:flex;gap:8px">
        <a href="{{ route('pemeliharaanalat.reports.show', $report) }}" class="ghost">&larr; Kembali</a>
        <button type="button" onclick="window.print()" id="btn-print-now">🖨 Cetak / Simpan PDF</button>
    </span>
</div>

<div class="sheet">
    <div class="kop">
        <div class="logo">RGP</div>
        <div>
            <h1>RESTU GURU PROMOSINDO</h1>
            <p>{{ $report->branch ? $report->branch->name . ' (' . $report->branch->city . ')' : ($report->asset->branch ? $report->asset->branch->name . ' (' . $report->asset->branch->city . ')' : 'Percetakan & Digital Printing') }} · Percetakan, Digital Printing &amp; Promosi</p>
        </div>
    </div>

    <div class="title">
        <h2>Surat Perintah Kerja (SPK) / Laporan Servis</h2>
        <div class="no">No. {{ $report->report_number }}</div>
    </div>

    <h3>Data Mesin &amp; Penempatan</h3>
    <table class="info">
        <tr><td class="k">Cabang</td><td class="s">:</td><td><strong>{{ $report->branch?->name ?? $report->asset->branch?->name ?? '—' }} ({{ $report->branch?->city ?? $report->asset->branch?->city ?? '—' }})</strong></td></tr>
        <tr><td class="k">Nama Mesin</td><td class="s">:</td><td>{{ $report->asset->name }}</td></tr>
        <tr><td class="k">Kode Aset</td><td class="s">:</td><td>{{ $report->asset->code }}</td></tr>
        <tr><td class="k">Merek / Tipe</td><td class="s">:</td><td>{{ $report->asset->brand ?? '-' }}</td></tr>
        <tr><td class="k">Lokasi Ruangan</td><td class="s">:</td><td>{{ $report->asset->location }}</td></tr>
    </table>

    <h3>Data Laporan</h3>
    <table class="info">
        <tr><td class="k">Pelapor</td><td class="s">:</td><td>{{ $report->reporter_name }}</td></tr>
        <tr><td class="k">Tanggal Lapor</td><td class="s">:</td><td>{{ $report->reported_at->translatedFormat('d F Y, H:i') }} WITA</td></tr>
        <tr><td class="k">Status</td><td class="s">:</td><td>{{ $report->status->label() }}</td></tr>
        <tr><td class="k">Teknisi Penanggung Jawab</td><td class="s">:</td><td>{{ $report->technician?->name ?? '-' }}</td></tr>
        <tr><td class="k">Tanggal Diproses</td><td class="s">:</td><td>{{ $report->processed_at?->translatedFormat('d F Y, H:i') ?? '-' }}</td></tr>
        <tr><td class="k">Tanggal Selesai</td><td class="s">:</td><td>{{ $report->resolved_at?->translatedFormat('d F Y, H:i') ?? '-' }}</td></tr>
    </table>

    <h3>Kendala yang Dilaporkan</h3>
    <div class="box sm">
        <div>{{ $report->issue_description }}</div>
        @if($report->hasPhoto())
            <div style="margin-top: 8px;">
                <img src="{{ $report->photoUrl() }}" alt="Foto Kendala" style="max-height: 120px; border: 1px solid #ccc; border-radius: 4px; display: inline-block;">
            </div>
        @endif
    </div>

    <h3>Uraian Penanganan</h3>
    <div class="box">{{ $report->repair_notes }}</div>

    <h3>Suku Cadang &amp; Biaya</h3>
    <table class="grid">
        <thead>
            <tr><th style="width:8%">No</th><th>Komponen / Suku Cadang Diganti</th></tr>
        </thead>
        <tbody>
            @forelse($report->partsList() as $i => $part)
                <tr><td>{{ $i + 1 }}</td><td>{{ $part }}</td></tr>
            @empty
                <tr><td>-</td><td>Tidak ada penggantian komponen</td></tr>
            @endforelse
            <tr>
                <td colspan="2" style="text-align:right;font-weight:700">
                    Total Biaya Servis: {{ $report->repair_cost !== null ? rupiah($report->repair_cost) : '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="sign">
        <div>
            <div class="role">Pelapor</div>
            <div class="space"></div>
            <div class="name">{{ $report->reporter_name }}</div>
        </div>
        <div>
            <div class="role">Teknisi</div>
            <div class="space"></div>
            <div class="name">{{ $report->technician?->name ?? '..............................' }}</div>
        </div>
        <div>
            <div class="role">Mengetahui,<br>{{ $report->branch?->name ? 'Kepala ' . $report->branch->name : 'Kepala Cabang' }}</div>
            <div class="space" style="height:16mm"></div>
            <div class="name">{{ $report->branch?->manager_name ?? '..............................' }}</div>
        </div>
    </div>

    <div class="foot">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} WITA oleh {{ auth()->user()?->name ?? session('auth_user.name', 'Staf') }} · {{ $report->branch?->city ?? 'Kalimantan Selatan' }}</div>
</div>
</body>
</html>

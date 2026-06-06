<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Print QR Batch — Overdue BPKB/Sertifikat</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; background: #f5f5f5; }
    .header-bar {
        background: #1a237e; color: #fff; text-align: center;
        padding: 10px; font-size: 13px; font-weight: bold;
    }
    .grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        padding: 14px;
    }
    .label {
        background: #fff; border: 2px solid #1a237e; border-radius: 8px;
        padding: 10px; page-break-inside: avoid;
    }
    .label-header {
        background: #1a237e; color: #fff; text-align: center;
        padding: 4px 6px; border-radius: 3px; font-size: 8px;
        font-weight: bold; margin-bottom: 8px; letter-spacing: 0.05em;
    }
    .label-body { display: flex; gap: 8px; align-items: flex-start; }
    .qr-area { flex-shrink: 0; }
    .qr-area svg { display: block; }
    .info { font-size: 8px; line-height: 1.55; }
    .info .no { font-size: 9.5px; font-weight: bold; font-family: monospace; color: #1a237e; }
    .label-k { color: #666; }
    .aging-box {
        text-align: center; margin-top: 6px; padding-top: 5px;
        border-top: 1px dashed #ccc; font-size: 8px; color: #444;
    }
    .aging-val { font-weight: bold; font-size: 12px; }
    .scan-hint { text-align: center; font-size: 6.5px; color: #999; margin-top: 2px; }
    .badge-jenis {
        display: inline-block; padding: 1px 6px; border-radius: 8px;
        font-size: 7px; font-weight: bold; color: #fff;
    }
    .bpkb { background: #1565c0; }
    .sertif { background: #00838f; }
    .no-print { text-align: center; padding: 14px; background: #fff; border-bottom: 1px solid #ddd; }
    .empty { text-align: center; padding: 60px; color: #888; font-size: 14px; }
    @media print {
        body { background: #fff; }
        .no-print { display: none; }
        .grid { padding: 8px; gap: 8px; }
    }
</style>
</head>
<body>

<div class="no-print">
    <strong>{{ $qrItems->count() }} item overdue (&gt;1 hari)</strong>
    &nbsp;|&nbsp;
    <button onclick="window.print()" style="background:#1a237e;color:#fff;border:none;padding:7px 20px;border-radius:5px;cursor:pointer;font-size:13px;">
        🖨️ Print Semua QR
    </button>
    <a href="{{ route('stock.index') }}" style="margin-left:10px;font-size:13px;color:#555;">← Kembali</a>
</div>

<div class="header-bar">
    GROUP MEGA — QR LABEL STOCK JAMINAN OVERDUE &gt;1 HARI &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y H:i') }}
</div>

@if($qrItems->isEmpty())
<div class="empty">Tidak ada item overdue saat ini.</div>
@else
<div class="grid">
    @foreach($qrItems as $item)
    @php
        $p      = $item['pengajuan'];
        $isBpkb = $p->jenis_jaminan === 'BPKB';
        $d      = $isBpkb ? $p->detailBpkb : $p->detailSertifikat;
        $aging  = $p->aging_hari;
        $agingColor = $aging <= 7 ? '#856404' : ($aging <= 14 ? '#7a3e06' : ($aging <= 30 ? '#dc3545' : '#212529'));
    @endphp
    <div class="label">
        <div class="label-header">
            <span class="badge-jenis {{ $isBpkb ? 'bpkb' : 'sertif' }}">{{ $p->jenis_jaminan }}</span>
            &nbsp; STOCK PUSAT
        </div>
        <div class="label-body">
            <div class="qr-area">
                {!! $item['qrSvg'] !!}
                <div class="scan-hint">Scan untuk OK</div>
            </div>
            <div class="info">
                <div class="no">{{ $p->no_pengajuan }}</div>
                <div><span class="label-k">Cabang: </span>{{ $p->cabang?->nama_cabang }}</div>
                <div><span class="label-k">Nasabah: </span>{{ $d?->nama_nasabah }}</div>
                @if($isBpkb)
                <div><span class="label-k">No. BPKB: </span>{{ $d?->no_bpkb }}</div>
                <div><span class="label-k">Polisi: </span>{{ $d?->no_polisi }}</div>
                @else
                <div><span class="label-k">No. Sertif: </span>{{ $d?->no_sertifikat }}</div>
                @endif
                <div><span class="label-k">No. Kartu: </span>{{ $d?->no_kartu_piutang }}</div>
                <div><span class="label-k">Tgl OK: </span>{{ $p->tgl_diproses?->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="aging-box">
            Di pusat <span class="aging-val" style="color:{{ $agingColor }}">{{ $aging }} hari</span>
        </div>
    </div>
    @endforeach
</div>
@endif

</body>
</html>

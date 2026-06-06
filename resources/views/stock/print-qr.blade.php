<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>QR Label — {{ $pengajuan->no_pengajuan }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; background: #fff; }
    .label {
        width: 9cm; border: 2px solid #1a237e; border-radius: 8px;
        padding: 14px; margin: 20px auto; page-break-inside: avoid;
    }
    .label-header {
        background: #1a237e; color: #fff; text-align: center;
        padding: 6px 8px; border-radius: 4px; margin-bottom: 10px;
        font-size: 10px; font-weight: bold; letter-spacing: 0.05em;
    }
    .label-body { display: flex; gap: 12px; align-items: flex-start; }
    .qr-area { flex-shrink: 0; }
    .qr-area svg { display: block; }
    .info { font-size: 9px; line-height: 1.6; }
    .info .no { font-size: 11px; font-weight: bold; font-family: monospace; color: #1a237e; }
    .info .row { margin-bottom: 2px; }
    .info .label-k { color: #666; }
    .aging-box {
        text-align: center; margin-top: 8px; padding: 4px 0;
        border-top: 1px dashed #ccc; font-size: 9px; color: #444;
    }
    .aging-val { font-weight: bold; font-size: 14px; }
    .badge-jenis {
        display: inline-block; padding: 2px 8px; border-radius: 10px;
        font-size: 9px; font-weight: bold; color: #fff;
    }
    .bpkb { background: #1565c0; }
    .sertif { background: #00838f; }
    .scan-hint { text-align: center; font-size: 7.5px; color: #888; margin-top: 4px; }
    @media print {
        body { margin: 0; }
        .no-print { display: none; }
    }
</style>
</head>
<body>

<div class="no-print" style="text-align:center;padding:16px;">
    <button onclick="window.print()" style="background:#1a237e;color:#fff;border:none;padding:8px 24px;border-radius:6px;font-size:14px;cursor:pointer;">
        🖨️ Print Label QR
    </button>
    <a href="{{ route('stock.index') }}" style="margin-left:12px;font-size:13px;color:#555;">← Kembali</a>
</div>

@php
    $isBpkb = $pengajuan->jenis_jaminan === 'BPKB';
    $d      = $isBpkb ? $pengajuan->detailBpkb : $pengajuan->detailSertifikat;
    $aging  = $pengajuan->aging_hari;
@endphp

<div class="label">
    <div class="label-header">
        GROUP MEGA &nbsp;|&nbsp;
        <span class="badge-jenis {{ $isBpkb ? 'bpkb' : 'sertif' }}">{{ $pengajuan->jenis_jaminan }}</span>
        &nbsp;|&nbsp; STOCK PUSAT
    </div>
    <div class="label-body">
        <div class="qr-area">
            {!! $qrSvg !!}
            <div class="scan-hint">Scan untuk konfirmasi</div>
        </div>
        <div class="info">
            <div class="no">{{ $pengajuan->no_pengajuan }}</div>
            <div class="row"><span class="label-k">Cabang: </span>{{ $pengajuan->cabang?->nama_cabang }}</div>
            <div class="row"><span class="label-k">Nasabah: </span>{{ $d?->nama_nasabah }}</div>
            @if($isBpkb)
            <div class="row"><span class="label-k">No. BPKB: </span>{{ $d?->no_bpkb }}</div>
            <div class="row"><span class="label-k">No. Polisi: </span>{{ $d?->no_polisi }}</div>
            <div class="row"><span class="label-k">Merek/Tipe: </span>{{ $d?->merek_motor }} {{ $d?->tipe_motor }}</div>
            @else
            <div class="row"><span class="label-k">No. Sertifikat: </span>{{ $d?->no_sertifikat }}</div>
            @endif
            <div class="row"><span class="label-k">No. Kartu: </span>{{ $d?->no_kartu_piutang }}</div>
            <div class="row"><span class="label-k">Disetujui: </span>{{ $pengajuan->tgl_diproses?->format('d M Y') }}</div>
        </div>
    </div>
    <div class="aging-box">
        Sudah di pusat selama
        <span class="aging-val"
            style="color:{{ $aging === 0 ? '#198754' : ($aging <= 7 ? '#856404' : ($aging <= 14 ? '#7a3e06' : '#dc3545')) }}">
            {{ $aging }} hari
        </span>
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 11px; margin: 30px 40px; color: #222; }
    h2 { text-align: center; font-size: 14px; margin-bottom: 2px; }
    .subtitle { text-align: center; margin-bottom: 20px; }
    .header-line { border-top: 3px solid #1a237e; border-bottom: 1px solid #1a237e; padding: 4px 0; margin-bottom: 18px; }
    table.info { width: 100%; margin-bottom: 16px; }
    table.info td { padding: 3px 5px; vertical-align: top; }
    table.info td:first-child { width: 45%; font-weight: bold; color: #444; }
    h4 { font-size: 12px; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin: 16px 0 8px; color: #1a237e; }
    .status-box { display: inline-block; padding: 4px 16px; border-radius: 4px; font-weight: bold; font-size: 12px; }
    .MENUNGGU  { background: #fff3cd; color: #856404; }
    .DIPROSES  { background: #cfe2ff; color: #0a58ca; }
    .DISETUJUI { background: #d1e7dd; color: #0a3622; }
    .DITOLAK   { background: #f8d7da; color: #58151c; }
    .ttd { margin-top: 50px; display: flex; justify-content: space-between; }
    .ttd-box { text-align: center; width: 200px; }
    .ttd-box .line { border-top: 1px solid #333; margin-top: 50px; }
</style>
</head>
<body>
<h2>SURAT PENGAJUAN PENGAMBILAN JAMINAN</h2>
<p class="subtitle">GROUP MEGA — Sistem Pengajuan Jaminan & Reimburse</p>
<div class="header-line"></div>

<table class="info">
    <tr><td>No. Pengajuan</td><td>: <strong>{{ $pengajuan->no_pengajuan }}</strong></td></tr>
    <tr><td>Tanggal Pengajuan</td><td>: {{ $pengajuan->tgl_dibuat?->format('d M Y H:i') }}</td></tr>
    <tr><td>Asal Cabang</td><td>: {{ $pengajuan->cabang?->nama_cabang }} ({{ $pengajuan->cabang?->kode_cabang }})</td></tr>
    <tr><td>Jenis Jaminan</td><td>: <strong>{{ $pengajuan->jenis_jaminan }}</strong></td></tr>
    <tr><td>Status</td><td>: <span class="status-box {{ $pengajuan->status }}">{{ $pengajuan->status }}</span></td></tr>
    <tr><td>Dibuat Oleh</td><td>: {{ $pengajuan->pembuatnya?->nama_lengkap }}</td></tr>
</table>

@php $detail = $pengajuan->jenis_jaminan === 'BPKB' ? $pengajuan->detailBpkb : $pengajuan->detailSertifikat; @endphp

<h4>Data Nasabah</h4>
<table class="info">
    <tr><td>Nama Nasabah</td><td>: {{ $detail?->nama_nasabah }}</td></tr>
    <tr><td>No. KTP</td><td>: {{ $detail?->no_ktp }}</td></tr>
    <tr><td>No. Kartu Piutang</td><td>: {{ $detail?->no_kartu_piutang }}</td></tr>
    <tr><td>Total Pinjaman</td><td>: <strong>Rp {{ number_format($detail?->total_pinjaman ?? 0,0,',','.') }}</strong></td></tr>
</table>

@if($pengajuan->jenis_jaminan === 'BPKB')
<h4>Data Kendaraan & BPKB</h4>
<table class="info">
    <tr><td>No. Polisi</td><td>: {{ $detail?->no_polisi }}</td></tr>
    <tr><td>Merek / Tipe</td><td>: {{ $detail?->merek_motor }} / {{ $detail?->tipe_motor }}</td></tr>
    <tr><td>No. BPKB</td><td>: {{ $detail?->no_bpkb }}</td></tr>
    <tr><td>No. Mesin</td><td>: {{ $detail?->no_mesin }}</td></tr>
    <tr><td>No. Rangka</td><td>: {{ $detail?->no_rangka }}</td></tr>
</table>
@else
<h4>Data Sertifikat</h4>
<table class="info">
    <tr><td>No. Sertifikat</td><td>: {{ $detail?->no_sertifikat }}</td></tr>
</table>
@endif

@if($pengajuan->catatan_pusat)
<h4>Catatan Admin Pusat</h4>
<p>{{ $pengajuan->catatan_pusat }}</p>
@endif

<div class="ttd">
    <div class="ttd-box">
        <div>Admin Cabang</div>
        <div class="line"></div>
        <div>{{ $pengajuan->pembuatnya?->nama_lengkap }}</div>
    </div>
    <div class="ttd-box">
        <div>Mengetahui,</div>
        <div class="line"></div>
        <div>Admin Pusat</div>
    </div>
</div>
</body>
</html>

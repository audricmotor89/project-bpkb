<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; }
    h2 { text-align: center; font-size: 13px; margin-bottom: 4px; }
    .subtitle { text-align: center; font-size: 10px; color: #555; margin-bottom: 10px; }
    .summary { display: flex; gap: 12px; margin-bottom: 10px; flex-wrap: wrap; }
    .sum-box { border: 1px solid #ccc; border-radius: 4px; padding: 4px 10px; text-align: center; }
    .sum-box strong { font-size: 13px; display: block; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1a237e; color: white; padding: 4px 5px; text-align: left; }
    td { padding: 3px 5px; border-bottom: 1px solid #eee; }
    tr:nth-child(even) td { background: #f9f9f9; }
    .badge { padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
    .badge-MENUNGGU  { background: #fff3cd; color: #856404; }
    .badge-DIPROSES  { background: #cfe2ff; color: #0a58ca; }
    .badge-DISETUJUI { background: #d1e7dd; color: #0a3622; }
    .badge-DITOLAK   { background: #f8d7da; color: #58151c; }
</style>
</head>
<body>
<h2>Laporan Pengajuan Pengambilan Jaminan</h2>
<p class="subtitle">Dicetak: {{ now()->format('d M Y H:i') }}</p>

<div class="summary">
    <div class="sum-box"><strong>{{ $summary['total'] }}</strong>Total</div>
    <div class="sum-box"><strong>{{ $summary['bpkb'] }}</strong>BPKB</div>
    <div class="sum-box"><strong>{{ $summary['sertifikat'] }}</strong>Sertifikat</div>
    <div class="sum-box"><strong>{{ $summary['menunggu'] }}</strong>Menunggu</div>
    <div class="sum-box"><strong>{{ $summary['diproses'] }}</strong>Diproses</div>
    <div class="sum-box"><strong>{{ $summary['disetujui'] }}</strong>Disetujui</div>
    <div class="sum-box"><strong>{{ $summary['ditolak'] }}</strong>Ditolak</div>
    <div class="sum-box"><strong>Rp {{ number_format($summary['total_pinjaman'],0,',','.') }}</strong>Total Pinjaman</div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th><th>No. Pengajuan</th><th>Tanggal</th><th>Cabang</th><th>Jenis</th>
            <th>Nasabah</th><th>No. KTP</th><th>No. Kartu Piutang</th><th>Total Pinjaman</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $p)
        @php $detail = $p->jenis_jaminan === 'BPKB' ? $p->detailBpkb : $p->detailSertifikat; @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $p->no_pengajuan }}</td>
            <td>{{ $p->tgl_dibuat?->format('d/m/Y') }}</td>
            <td>{{ $p->cabang?->nama_cabang }}</td>
            <td>{{ $p->jenis_jaminan }}</td>
            <td>{{ $detail?->nama_nasabah }}</td>
            <td>{{ $detail?->no_ktp }}</td>
            <td>{{ $detail?->no_kartu_piutang }}</td>
            <td>Rp {{ number_format($detail?->total_pinjaman ?? 0,0,',','.') }}</td>
            <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>

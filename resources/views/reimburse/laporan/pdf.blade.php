<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; }
    h2 { text-align: center; font-size: 13px; margin-bottom: 4px; }
    .subtitle { text-align: center; font-size: 10px; color: #555; margin-bottom: 10px; }
    .summary { display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
    .sum-box { border: 1px solid #ccc; border-radius: 4px; padding: 4px 10px; text-align: center; }
    .sum-box strong { font-size: 13px; display: block; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1a237e; color: white; padding: 4px 5px; text-align: left; }
    td { padding: 3px 5px; border-bottom: 1px solid #eee; }
    tr:nth-child(even) td { background: #f9f9f9; }
    .badge { padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
    .badge-MENUNGGU  { background: #fff3cd; color: #856404; }
    .badge-DISETUJUI { background: #d1e7dd; color: #0a3622; }
    .badge-DITOLAK   { background: #f8d7da; color: #58151c; }
    .text-right { text-align: right; }
</style>
</head>
<body>
<h2>Laporan Pengajuan Reimburse</h2>
<p class="subtitle">Dicetak: {{ now()->format('d M Y H:i') }}</p>

<div class="summary">
    <div class="sum-box"><strong>{{ $summary['total'] }}</strong>Total</div>
    <div class="sum-box"><strong>{{ $summary['menunggu'] }}</strong>Menunggu</div>
    <div class="sum-box"><strong>{{ $summary['disetujui'] }}</strong>Disetujui</div>
    <div class="sum-box"><strong>{{ $summary['ditolak'] }}</strong>Ditolak</div>
    <div class="sum-box"><strong>Rp {{ number_format($summary['total_diajukan'],0,',','.') }}</strong>Total Diajukan</div>
    <div class="sum-box"><strong>Rp {{ number_format($summary['total_disetujui'],0,',','.') }}</strong>Total Dicairkan</div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>No. Reimburse</th>
            <th>Tgl Pengeluaran</th>
            <th>Cabang</th>
            <th>Pemohon</th>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th class="text-right">Nominal Diajukan</th>
            <th class="text-right">Nominal Disetujui</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $r)
        @php $kategoriLabel = \App\Models\Reimburse::labelKategori(); @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->no_reimburse }}</td>
            <td>{{ $r->tanggal_pengeluaran?->format('d/m/Y') }}</td>
            <td>{{ $r->cabang?->nama_cabang }}</td>
            <td>{{ $r->nama_pemohon }}</td>
            <td>{{ $kategoriLabel[$r->kategori] ?? $r->kategori }}</td>
            <td>{{ Str::limit($r->keterangan, 30) }}</td>
            <td class="text-right">Rp {{ number_format($r->nominal_diajukan,0,',','.') }}</td>
            <td class="text-right">{{ $r->nominal_disetujui ? 'Rp '.number_format($r->nominal_disetujui,0,',','.') : '-' }}</td>
            <td><span class="badge badge-{{ $r->status }}">{{ $r->status }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>

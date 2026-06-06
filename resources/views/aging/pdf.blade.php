<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 8.5px; margin: 0; }
    h2 { text-align: center; font-size: 13px; margin-bottom: 3px; }
    .subtitle { text-align: center; font-size: 9px; color: #555; margin-bottom: 8px; }
    .summary { display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap; }
    .sum-box { border: 1px solid #ccc; border-radius: 4px; padding: 3px 8px; text-align: center; min-width: 70px; }
    .sum-box strong { font-size: 12px; display: block; }
    .sum-box small { font-size: 7.5px; color: #555; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #1a237e; color: #fff; padding: 3px 4px; text-align: left; font-size: 8px; }
    td { padding: 2px 4px; border-bottom: 1px solid #eee; vertical-align: top; }
    tr:nth-child(even) td { background: #f9f9f9; }
    .aging-badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-weight: bold; font-size: 8px; }
    .a-0     { background:#d1e7dd; color:#0a3622; }
    .a-1_7   { background:#fff3cd; color:#856404; }
    .a-8_14  { background:#ffe5d0; color:#7a3e06; }
    .a-15_30 { background:#f8d7da; color:#58151c; }
    .a-30p   { background:#212529; color:#fff; }
    .sudah   { background:#d1e7dd; color:#0a3622; display:inline-block; padding:1px 5px; border-radius:3px; font-size:8px; }
    .belum   { background:#e2e3e5; color:#383d41; display:inline-block; padding:1px 5px; border-radius:3px; font-size:8px; }
</style>
</head>
<body>
<h2>LAPORAN AGING BPKB</h2>
<p class="subtitle">GROUP MEGA — Dicetak: {{ now()->format('d M Y H:i') }}</p>

<div class="summary">
    <div class="sum-box"><strong>{{ $summary['total'] }}</strong><small>Total BPKB</small></div>
    <div class="sum-box"><strong style="color:#dc3545;">{{ $summary['belum_diambil'] }}</strong><small>Belum Diambil</small></div>
    <div class="sum-box"><strong style="color:#198754;">{{ $summary['sudah_diambil'] }}</strong><small>Sudah Diambil</small></div>
    <div class="sum-box"><strong>{{ $summary['hari_0'] }}</strong><small>0 hari</small></div>
    <div class="sum-box"><strong>{{ $summary['hari_1_7'] }}</strong><small>1–7 hari</small></div>
    <div class="sum-box"><strong>{{ $summary['hari_8_14'] }}</strong><small>8–14 hari</small></div>
    <div class="sum-box"><strong>{{ $summary['hari_15_30'] }}</strong><small>15–30 hari</small></div>
    <div class="sum-box"><strong>{{ $summary['hari_30plus'] }}</strong><small>&gt;30 hari</small></div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>No. Pengajuan</th>
            <th>Cabang</th>
            <th>Tgl Disetujui</th>
            <th>Nama Nasabah</th>
            <th>No. BPKB</th>
            <th>No. Polisi</th>
            <th>Merek / Tipe</th>
            <th>No. Kartu Piutang</th>
            <th style="text-align:center;">Aging</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $p)
        @php
            $d     = $p->detailBpkb;
            $aging = $p->aging_hari;
            if ($p->tgl_diambil)    $cls = '';
            elseif ($aging === 0)   $cls = 'a-0';
            elseif ($aging <= 7)    $cls = 'a-1_7';
            elseif ($aging <= 14)   $cls = 'a-8_14';
            elseif ($aging <= 30)   $cls = 'a-15_30';
            else                    $cls = 'a-30p';
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $p->no_pengajuan }}</td>
            <td>{{ $p->cabang?->nama_cabang }}</td>
            <td>{{ $p->tgl_diproses?->format('d/m/Y') }}</td>
            <td>{{ $d?->nama_nasabah }}</td>
            <td>{{ $d?->no_bpkb }}</td>
            <td>{{ $d?->no_polisi }}</td>
            <td>{{ $d?->merek_motor }} {{ $d?->tipe_motor }}</td>
            <td>{{ $d?->no_kartu_piutang }}</td>
            <td style="text-align:center;">
                @if($p->tgl_diambil)
                    <span>—</span>
                @else
                    <span class="aging-badge {{ $cls }}">{{ $aging }} hari</span>
                @endif
            </td>
            <td style="text-align:center;">
                @if($p->tgl_diambil)
                    <span class="sudah">Sudah Diambil</span>
                @else
                    <span class="belum">Belum Diambil</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #222; }
    .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #1a237e; padding-bottom: 10px; }
    .header h2 { font-size: 14pt; color: #1a237e; margin-bottom: 2px; }
    .header p { font-size: 8pt; color: #555; }
    .filter-info { font-size: 8pt; color: #555; margin-bottom: 12px; background: #f8f9fa; padding: 6px 10px; border-radius: 4px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    th { background: #1a237e; color: white; padding: 5px 6px; font-size: 8pt; text-align: left; }
    td { padding: 5px 6px; font-size: 8pt; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
    tr:nth-child(even) td { background: #f9f9f9; }
    .badge-aktif { background: #198754; color: white; padding: 2px 7px; border-radius: 10px; font-size: 7pt; }
    .badge-kembali { background: #ffc107; color: #333; padding: 2px 7px; border-radius: 10px; font-size: 7pt; }
    .foto-wrap { text-align: center; }
    .foto-wrap img { max-width: 70px; max-height: 70px; border: 1px solid #ccc; border-radius: 3px; object-fit: cover; }
    .no-foto { color: #aaa; font-size: 7pt; }
    .section-title { font-size: 10pt; font-weight: bold; color: #1a237e; margin-bottom: 6px; margin-top: 16px; border-bottom: 1px solid #1a237e; padding-bottom: 3px; }
    .summary-row td { font-weight: bold; background: #e8eaf6 !important; }
    .footer { font-size: 7pt; color: #888; text-align: right; margin-top: 10px; border-top: 1px solid #eee; padding-top: 6px; }
</style>
</head>
<body>

<div class="header">
    <h2>GROUP MEGA — Laporan Ijasah Karyawan</h2>
    <p>Sistem Jaminan Kerja | Dicetak: {{ now()->format('d M Y H:i') }}</p>
</div>

<div class="filter-info">
    <strong>Filter:</strong>
    Status: {{ $filter['status'] }} &nbsp;|&nbsp;
    Cabang: {{ $filter['cabang'] }}
    @if($filter['tgl_dari']) &nbsp;|&nbsp; Dari: {{ \Carbon\Carbon::parse($filter['tgl_dari'])->format('d/m/Y') }} @endif
    @if($filter['tgl_sampai']) &nbsp;|&nbsp; Sampai: {{ \Carbon\Carbon::parse($filter['tgl_sampai'])->format('d/m/Y') }} @endif
    &nbsp;|&nbsp; Total: {{ $data->count() }} karyawan
</div>

{{-- BAGIAN 1: Ijasah Masih Tersimpan --}}
@php $aktif = $data->where('status','AKTIF'); @endphp
<div class="section-title">A. Ijasah Masih Tersimpan ({{ $aktif->count() }} karyawan)</div>

@if($aktif->count())
<table>
    <thead>
        <tr>
            <th style="width:12%">No. Jaminan</th>
            <th style="width:16%">Nama Karyawan</th>
            <th style="width:10%">NIK</th>
            <th style="width:12%">Jabatan</th>
            <th style="width:8%">Cabang</th>
            <th style="width:8%">Tgl. Masuk</th>
            <th style="width:10%">Tgl. Terima</th>
            <th style="width:12%" class="text-center">Foto Penerimaan</th>
            <th style="width:7%" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($aktif as $jk)
        @php $fotoPenerima = $jk->lampiran->where('jenis_dokumen','FOTO_PENERIMAAN')->first(); @endphp
        <tr>
            <td><strong>{{ $jk->no_jaminan }}</strong></td>
            <td>{{ $jk->nama_karyawan }}</td>
            <td>{{ $jk->no_ktp }}</td>
            <td>{{ $jk->jabatan }}</td>
            <td>{{ $jk->cabang?->kode_cabang }}</td>
            <td>{{ $jk->tgl_masuk_kerja?->format('d/m/Y') }}</td>
            <td>{{ $jk->tgl_diterima?->format('d/m/Y') ?? '-' }}</td>
            <td class="foto-wrap">
                @if($fotoPenerima && isset($fotoPenerima->base64))
                    <img src="{{ $fotoPenerima->base64 }}" alt="Foto">
                @else
                    <span class="no-foto">Tidak ada foto</span>
                @endif
            </td>
            <td class="text-center"><span class="badge-aktif">Tersimpan</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="color:#888;font-size:8pt;margin-bottom:12px;">Tidak ada data.</p>
@endif

{{-- BAGIAN 2: Ijasah Sudah Keluar --}}
@php $kembali = $data->where('status','KEMBALI'); @endphp
<div class="section-title">B. Ijasah Sudah Dikembalikan / Keluar ({{ $kembali->count() }} karyawan)</div>

@if($kembali->count())
<table>
    <thead>
        <tr>
            <th style="width:11%">No. Jaminan</th>
            <th style="width:15%">Nama Karyawan</th>
            <th style="width:9%">NIK</th>
            <th style="width:10%">Jabatan</th>
            <th style="width:6%">Cabang</th>
            <th style="width:8%">Tgl. Terima</th>
            <th style="width:10%" class="text-center">Foto Penerimaan</th>
            <th style="width:8%">Tgl. Kembali</th>
            <th style="width:10%" class="text-center">Foto Pengembalian</th>
            <th style="width:8%" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kembali as $jk)
        @php
            $fotoPenerima = $jk->lampiran->where('jenis_dokumen','FOTO_PENERIMAAN')->first();
            $fotoKembali  = $jk->lampiran->where('jenis_dokumen','FOTO_PENGEMBALIAN')->first();
        @endphp
        <tr>
            <td><strong>{{ $jk->no_jaminan }}</strong></td>
            <td>{{ $jk->nama_karyawan }}</td>
            <td>{{ $jk->no_ktp }}</td>
            <td>{{ $jk->jabatan }}</td>
            <td>{{ $jk->cabang?->kode_cabang }}</td>
            <td>{{ $jk->tgl_diterima?->format('d/m/Y') ?? '-' }}</td>
            <td class="foto-wrap">
                @if($fotoPenerima && isset($fotoPenerima->base64))
                    <img src="{{ $fotoPenerima->base64 }}" alt="Foto">
                @else
                    <span class="no-foto">Tidak ada</span>
                @endif
            </td>
            <td>{{ $jk->tgl_dikembalikan?->format('d/m/Y') ?? '-' }}</td>
            <td class="foto-wrap">
                @if($fotoKembali && isset($fotoKembali->base64))
                    <img src="{{ $fotoKembali->base64 }}" alt="Foto">
                @else
                    <span class="no-foto">Tidak ada</span>
                @endif
            </td>
            <td class="text-center"><span class="badge-kembali">Dikembalikan</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="color:#888;font-size:8pt;margin-bottom:12px;">Tidak ada data.</p>
@endif

<div class="footer">
    Group Mega — Laporan Ijasah Karyawan | {{ now()->format('d M Y H:i') }}
</div>
</body>
</html>

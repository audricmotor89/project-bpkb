<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Konfirmasi Pengambilan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
    body { background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .scan-card { max-width: 420px; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
    .scan-header { background: #1a237e; color: #fff; padding: 18px 20px; text-align: center; }
    .scan-header .brand { font-size: 0.75rem; opacity: 0.8; letter-spacing: 0.08em; }
    .scan-header h5 { font-size: 1rem; margin: 4px 0 0; font-weight: 700; }
    .info-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #f0f0f0; font-size: 0.875rem; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #888; }
    .info-val { font-weight: 600; text-align: right; max-width: 60%; }
    .aging-pill {
        display: inline-block; padding: 3px 14px; border-radius: 20px;
        font-weight: bold; font-size: 0.875rem;
    }
</style>
</head>
<body>

<div class="scan-card bg-white">
    {{-- Header --}}
    <div class="scan-header">
        <div class="brand">GROUP MEGA — SISTEM JAMINAN</div>
        <h5><i class="bi bi-qr-code-scan me-2"></i>Konfirmasi Pengambilan</h5>
    </div>

    <div class="p-4">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif
        @if(session('info'))
        <div class="alert alert-info mb-3">{{ session('info') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif

        @php
            $isBpkb = $pengajuan->jenis_jaminan === 'BPKB';
            $d      = $isBpkb ? $pengajuan->detailBpkb : $pengajuan->detailSertifikat;
            $aging  = $pengajuan->aging_hari;
            $agingStyle = $aging === 0
                ? 'background:#d1e7dd;color:#0a3622'
                : ($aging <= 7  ? 'background:#fff3cd;color:#856404'
                : ($aging <= 14 ? 'background:#ffe5d0;color:#7a3e06'
                : ($aging <= 30 ? 'background:#f8d7da;color:#58151c'
                :                 'background:#212529;color:#fff')));
        @endphp

        {{-- Badge jenis --}}
        <div class="text-center mb-3">
            <span class="badge bg-{{ $isBpkb ? 'primary' : 'info' }} fs-6 px-3">
                {{ $pengajuan->jenis_jaminan }}
            </span>
            <span class="badge bg-{{ $pengajuan->tgl_diambil ? 'success' : 'secondary' }} fs-6 px-3 ms-1">
                {{ $pengajuan->tgl_diambil ? 'SUDAH DIAMBIL' : 'BELUM DIAMBIL' }}
            </span>
        </div>

        {{-- Info item --}}
        <div class="mb-3">
            <div class="info-row">
                <span class="info-label">No. Pengajuan</span>
                <span class="info-val font-monospace">{{ $pengajuan->no_pengajuan }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cabang</span>
                <span class="info-val">{{ $pengajuan->cabang?->nama_cabang }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nama Nasabah</span>
                <span class="info-val">{{ $d?->nama_nasabah }}</span>
            </div>
            @if($isBpkb)
            <div class="info-row">
                <span class="info-label">No. BPKB</span>
                <span class="info-val font-monospace">{{ $d?->no_bpkb }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Polisi</span>
                <span class="info-val">{{ $d?->no_polisi }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Merek / Tipe</span>
                <span class="info-val">{{ $d?->merek_motor }} {{ $d?->tipe_motor }}</span>
            </div>
            @else
            <div class="info-row">
                <span class="info-label">No. Sertifikat</span>
                <span class="info-val font-monospace">{{ $d?->no_sertifikat }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">No. Kartu Piutang</span>
                <span class="info-val font-monospace">{{ $d?->no_kartu_piutang }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tgl Disetujui</span>
                <span class="info-val">{{ $pengajuan->tgl_diproses?->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Lama di Pusat</span>
                <span class="info-val">
                    <span class="aging-pill" style="{{ $agingStyle }}">{{ $aging }} hari</span>
                </span>
            </div>
        </div>

        {{-- QR kecil di tengah --}}
        <div class="text-center mb-3">
            {!! $qrSvg !!}
        </div>

        {{-- Aksi --}}
        @if($pengajuan->tgl_diambil)
            <div class="alert alert-success text-center mb-0">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <strong>Sudah diambil</strong><br>
                <small>
                    {{ $pengajuan->tgl_diambil->format('d M Y H:i') }}<br>
                    Oleh: {{ $pengajuan->pengambilnya?->nama_lengkap }}
                </small>
            </div>
        @elseif($pengajuan->status !== 'DISETUJUI')
            <div class="alert alert-warning text-center mb-0">
                Status pengajuan: <strong>{{ $pengajuan->status }}</strong><br>
                <small>Hanya yang berstatus DISETUJUI yang dapat dikonfirmasi.</small>
            </div>
        @else
            <form method="POST" action="{{ route('stock.confirm', $pengajuan->qr_token) }}">
                @csrf
                <button type="submit" class="btn btn-success w-100 py-3"
                    style="font-size:1.05rem;"
                    onclick="return confirm('Konfirmasi bahwa jaminan ini sudah diambil oleh cabang?')">
                    <i class="bi bi-check2-circle me-2 fs-5"></i>
                    OK — SUDAH DIAMBIL
                </button>
            </form>
            <div class="text-center mt-2">
                <small class="text-muted">Login sebagai: <strong>{{ auth()->user()->nama_lengkap }}</strong></small>
            </div>
        @endif
    </div>
</div>

</body>
</html>

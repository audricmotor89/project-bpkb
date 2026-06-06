<?php

namespace App\Services;

use App\Models\JaminanKerja;
use App\Models\Pengajuan;
use App\Models\Reimburse;

class NomorPengajuanService
{
    // Format: JKT01-BPKB-20260516-001
    public static function generate(string $kodeCabang, string $jenisJaminan): string
    {
        $tanggal = now()->format('Ymd');
        $prefix  = strtoupper($kodeCabang) . '-' . $jenisJaminan . '-' . $tanggal . '-';

        $last = Pengajuan::where('no_pengajuan', 'like', $prefix . '%')
            ->orderByDesc('no_pengajuan')
            ->value('no_pengajuan');

        $seq = $last ? (int) substr($last, -3) + 1 : 1;

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // Format: JKT01-REIMB-20260516-001
    public static function generateReimburse(string $kodeCabang): string
    {
        $tanggal = now()->format('Ymd');
        $prefix  = strtoupper($kodeCabang) . '-REIMB-' . $tanggal . '-';

        $last = Reimburse::where('no_reimburse', 'like', $prefix . '%')
            ->orderByDesc('no_reimburse')
            ->value('no_reimburse');

        $seq = $last ? (int) substr($last, -3) + 1 : 1;

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // Format: JKT01-JMK-20260524-001
    public static function generateJaminanKerja(string $kodeCabang): string
    {
        $tanggal = now()->format('Ymd');
        $prefix  = strtoupper($kodeCabang) . '-JMK-' . $tanggal . '-';

        $last = JaminanKerja::where('no_jaminan', 'like', $prefix . '%')
            ->orderByDesc('no_jaminan')
            ->value('no_jaminan');

        $seq = $last ? (int) substr($last, -3) + 1 : 1;

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}

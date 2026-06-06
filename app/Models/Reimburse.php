<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reimburse extends Model
{
    protected $table = 'reimburse';

    protected $fillable = [
        'no_reimburse','batch_id','cabang_id','dibuat_oleh','nama_pemohon','jabatan',
        'tanggal_pengeluaran','kategori','keterangan',
        'nominal_diajukan','nominal_disetujui',
        'status','catatan_pusat','diproses_oleh','tgl_diproses',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'tgl_diproses'        => 'datetime',
        'nominal_diajukan'    => 'decimal:2',
        'nominal_disetujui'   => 'decimal:2',
    ];

    public function cabang()      { return $this->belongsTo(Cabang::class); }
    public function pembuatnya()  { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function pemrosesnya() { return $this->belongsTo(User::class, 'diproses_oleh'); }
    public function lampiran()    { return $this->hasMany(LampiranReimburse::class); }

    public static function labelKategori(): array
    {
        return [
            'TRANSPORT'   => 'Transport',
            'MAKAN'       => 'Makan & Minum',
            'AKOMODASI'   => 'Akomodasi',
            'OPERASIONAL' => 'Operasional',
            'LAINNYA'     => 'Lainnya',
        ];
    }
}

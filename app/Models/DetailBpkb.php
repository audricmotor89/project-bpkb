<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBpkb extends Model
{
    protected $table = 'detail_bpkb';
    protected $fillable = [
        'pengajuan_id','nama_nasabah','no_ktp','no_polisi',
        'merek_motor','tipe_motor','no_bpkb','no_mesin','no_rangka',
        'total_pinjaman','no_kartu_piutang',
    ];
    protected $casts = ['total_pinjaman' => 'decimal:2'];
    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }
}

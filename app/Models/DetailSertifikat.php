<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailSertifikat extends Model
{
    protected $table = 'detail_sertifikat';
    protected $fillable = [
        'pengajuan_id','nama_nasabah','no_ktp','no_sertifikat',
        'total_pinjaman','no_kartu_piutang',
    ];
    protected $casts = ['total_pinjaman' => 'decimal:2'];
    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomentarPengajuan extends Model
{
    protected $table = 'komentar_pengajuan';
    protected $fillable = ['pengajuan_id', 'user_id', 'komentar'];

    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }
    public function user()      { return $this->belongsTo(User::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabang';

    protected $fillable = ['kode_cabang', 'nama_cabang', 'alamat', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class);
    }
}

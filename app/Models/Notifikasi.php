<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id', 'pengajuan_id', 'judul', 'pesan', 'tipe', 'url', 'dibaca',
    ];

    protected $casts = ['dibaca' => 'boolean'];

    public function user()      { return $this->belongsTo(User::class); }
    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }

    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'INFO', ?string $url = null, ?int $pengajuanId = null): void
    {
        static::create([
            'user_id'      => $userId,
            'pengajuan_id' => $pengajuanId,
            'judul'        => $judul,
            'pesan'        => $pesan,
            'tipe'         => $tipe,
            'url'          => $url,
        ]);
    }
}

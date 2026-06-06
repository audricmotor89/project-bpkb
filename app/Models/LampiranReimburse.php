<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LampiranReimburse extends Model
{
    protected $table = 'lampiran_reimburse';

    protected $fillable = [
        'reimburse_id','jenis_dokumen','nama_file_asli',
        'nama_file_storage','ukuran_file','mime_type','diupload_oleh',
    ];

    public function reimburse() { return $this->belongsTo(Reimburse::class); }
    public function uploader()  { return $this->belongsTo(User::class, 'diupload_oleh'); }

    public function getUkuranFormatAttribute(): string
    {
        $kb = $this->ukuran_file / 1024;
        return $kb >= 1024
            ? number_format($kb / 1024, 2) . ' MB'
            : number_format($kb, 1) . ' KB';
    }
}

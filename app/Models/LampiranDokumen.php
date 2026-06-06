<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LampiranDokumen extends Model
{
    protected $table = 'lampiran_dokumen';
    protected $fillable = [
        'pengajuan_id','jenis_dokumen','nama_file_asli',
        'nama_file_storage','ukuran_file','mime_type','diupload_oleh',
    ];
    public function pengajuan()   { return $this->belongsTo(Pengajuan::class); }
    public function uploader()    { return $this->belongsTo(User::class, 'diupload_oleh'); }

    public function getUkuranFormatAttribute(): string
    {
        $kb = $this->ukuran_file / 1024;
        return $kb >= 1024
            ? number_format($kb / 1024, 2) . ' MB'
            : number_format($kb, 1) . ' KB';
    }
}

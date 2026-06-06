<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LampiranJaminanKerja extends Model
{
    protected $table = 'lampiran_jaminan_kerja';

    protected $fillable = [
        'jaminan_kerja_id', 'jenis_dokumen', 'nama_file_asli',
        'nama_file_storage', 'ukuran_file', 'mime_type', 'diupload_oleh',
    ];

    public function jaminanKerja() { return $this->belongsTo(JaminanKerja::class); }
    public function uploader()     { return $this->belongsTo(User::class, 'diupload_oleh'); }

    public function getUkuranFormatAttribute(): string
    {
        $kb = $this->ukuran_file / 1024;
        return $kb >= 1024
            ? number_format($kb / 1024, 2) . ' MB'
            : number_format($kb, 1) . ' KB';
    }
}

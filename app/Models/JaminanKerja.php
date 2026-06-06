<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JaminanKerja extends Model
{
    protected $table = 'jaminan_kerja';

    protected $fillable = [
        'no_jaminan', 'cabang_id', 'nama_karyawan', 'no_ktp', 'jabatan', 'no_hp',
        'tgl_masuk_kerja', 'has_akte', 'has_bpkb', 'has_ijasah', 'catatan', 'status',
        'status_pusat', 'catatan_pusat', 'dikonfirmasi_oleh', 'tgl_dikonfirmasi',
        'dibuat_oleh', 'diterima_oleh', 'tgl_diterima',
        'dikembalikan_oleh', 'tgl_dikembalikan', 'catatan_pengembalian',
    ];

    protected $casts = [
        'tgl_masuk_kerja'   => 'date',
        'tgl_diterima'      => 'datetime',
        'tgl_dikembalikan'  => 'datetime',
        'tgl_dikonfirmasi'  => 'datetime',
        'has_akte'          => 'boolean',
        'has_bpkb'          => 'boolean',
        'has_ijasah'        => 'boolean',
    ];

    public function cabang()            { return $this->belongsTo(Cabang::class); }
    public function pembuatnya()        { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function penerimanya()       { return $this->belongsTo(User::class, 'diterima_oleh'); }
    public function pengembaliannya()   { return $this->belongsTo(User::class, 'dikembalikan_oleh'); }
    public function pengkonfirmasinya() { return $this->belongsTo(User::class, 'dikonfirmasi_oleh'); }
    public function lampiran()          { return $this->hasMany(LampiranJaminanKerja::class); }

    public function lampiranByJenis(string $jenis)
    {
        return $this->lampiran->where('jenis_dokumen', $jenis);
    }

    public function getJaminanListAttribute(): array
    {
        $list = [];
        if ($this->has_akte)   $list[] = 'Akte Kelahiran';
        if ($this->has_bpkb)   $list[] = 'BPKB';
        if ($this->has_ijasah) $list[] = 'Ijasah';
        return $list;
    }
}

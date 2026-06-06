<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->qr_token)) {
                $model->qr_token = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'no_pengajuan', 'jenis_jaminan', 'cabang_id', 'dibuat_oleh',
        'status', 'catatan_pusat', 'diproses_oleh', 'tgl_dibuat', 'tgl_diproses',
        'tgl_diambil', 'diambil_oleh', 'foto_pengambilan', 'qr_token',
    ];

    protected $casts = [
        'tgl_dibuat'   => 'datetime',
        'tgl_diproses' => 'datetime',
        'tgl_diambil'  => 'datetime',
    ];

    // Hitung aging dalam hari sejak tanggal disetujui
    public function getAgingHariAttribute(): int
    {
        if (!$this->tgl_diproses) return 0;
        return (int) now()->startOfDay()->diffInDays($this->tgl_diproses->startOfDay());
    }

    // Warna badge aging
    public function getAgingColorAttribute(): string
    {
        $hari = $this->aging_hari;
        if ($hari === 0)       return 'success';
        if ($hari <= 7)        return 'warning';
        if ($hari <= 14)       return 'orange';
        if ($hari <= 30)       return 'danger';
        return 'dark';
    }

    public function cabang()           { return $this->belongsTo(Cabang::class); }
    public function pembuatnya()       { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function pemrosesnya()      { return $this->belongsTo(User::class, 'diproses_oleh'); }
    public function pengambilnya()     { return $this->belongsTo(User::class, 'diambil_oleh'); }
    public function detailBpkb()       { return $this->hasOne(DetailBpkb::class); }
    public function detailSertifikat() { return $this->hasOne(DetailSertifikat::class); }
    public function lampiran()         { return $this->hasMany(LampiranDokumen::class); }
    public function auditLog()          { return $this->hasMany(AuditLog::class); }
    public function komentar()          { return $this->hasMany(KomentarPengajuan::class); }
}

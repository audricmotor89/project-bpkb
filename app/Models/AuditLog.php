<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';
    protected $fillable = [
        'pengajuan_id','user_id','aksi','status_lama','status_baru','keterangan','ip_address',
    ];
    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }
    public function user()      { return $this->belongsTo(User::class); }
}

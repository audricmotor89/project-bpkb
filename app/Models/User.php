<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', 'password', 'nama_lengkap', 'role', 'cabang_id',
        'email', 'no_whatsapp', 'notif_email', 'notif_whatsapp', 'aktif',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'       => 'hashed',
            'notif_email'    => 'boolean',
            'notif_whatsapp' => 'boolean',
            'aktif'          => 'boolean',
        ];
    }

    // Primary cabang (untuk default form & display)
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    // Semua cabang yang bisa diakses (many-to-many)
    public function cabangs()
    {
        return $this->belongsToMany(Cabang::class, 'user_cabang');
    }

    // Array of cabang IDs yang bisa diakses user ini
    public function cabangIds(): array
    {
        return $this->cabangs->pluck('id')->toArray();
    }

    public function getAuthIdentifierName(): string { return 'username'; }

    public function isSuperAdmin(): bool  { return $this->role === 'SUPER_ADMIN'; }
    public function isAdminPusat(): bool  { return $this->role === 'ADMIN_PUSAT'; }
    public function isAdminCabang(): bool { return $this->role === 'ADMIN_CABANG'; }
    public function hasRole(string $role): bool { return $this->role === $role; }
}

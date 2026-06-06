<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username'     => 'superadmin',
                'password'     => Hash::make('Admin@1234'),
                'nama_lengkap' => 'Super Administrator',
                'role'         => 'SUPER_ADMIN',
                'cabang_id'    => null,
                'email'        => 'superadmin@bpkb.local',
                'no_whatsapp'  => null,
                'notif_email'     => 1,
                'notif_whatsapp'  => 1,
                'aktif'        => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'username'     => 'adminpusat',
                'password'     => Hash::make('Admin@1234'),
                'nama_lengkap' => 'Admin Pusat',
                'role'         => 'ADMIN_PUSAT',
                'cabang_id'    => null,
                'email'        => 'adminpusat@bpkb.local',
                'no_whatsapp'  => null,
                'notif_email'     => 1,
                'notif_whatsapp'  => 1,
                'aktif'        => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'username'     => 'adminjkt01',
                'password'     => Hash::make('Admin@1234'),
                'nama_lengkap' => 'Admin Cabang Jakarta Pusat',
                'role'         => 'ADMIN_CABANG',
                'cabang_id'    => 1,
                'email'        => 'adminjkt01@bpkb.local',
                'no_whatsapp'  => null,
                'notif_email'     => 1,
                'notif_whatsapp'  => 1,
                'aktif'        => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}

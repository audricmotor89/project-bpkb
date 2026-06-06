<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CabangSeeder extends Seeder
{
    public function run(): void
    {
        $cabang = [
            ['kode_cabang' => 'JKT01', 'nama_cabang' => 'Jakarta Pusat', 'alamat' => 'Jl. Sudirman No. 1, Jakarta Pusat'],
            ['kode_cabang' => 'JKT02', 'nama_cabang' => 'Jakarta Selatan', 'alamat' => 'Jl. Fatmawati No. 10, Jakarta Selatan'],
            ['kode_cabang' => 'JKT03', 'nama_cabang' => 'Jakarta Utara', 'alamat' => 'Jl. Pluit Raya No. 5, Jakarta Utara'],
            ['kode_cabang' => 'JKT04', 'nama_cabang' => 'Jakarta Timur', 'alamat' => 'Jl. Matraman No. 15, Jakarta Timur'],
            ['kode_cabang' => 'JKT05', 'nama_cabang' => 'Jakarta Barat', 'alamat' => 'Jl. Daan Mogot No. 20, Jakarta Barat'],
            ['kode_cabang' => 'BDG01', 'nama_cabang' => 'Bandung Pusat', 'alamat' => 'Jl. Asia Afrika No. 8, Bandung'],
            ['kode_cabang' => 'BDG02', 'nama_cabang' => 'Bandung Selatan', 'alamat' => 'Jl. Soekarno Hatta No. 3, Bandung'],
            ['kode_cabang' => 'SBY01', 'nama_cabang' => 'Surabaya Pusat', 'alamat' => 'Jl. Pemuda No. 12, Surabaya'],
            ['kode_cabang' => 'SBY02', 'nama_cabang' => 'Surabaya Selatan', 'alamat' => 'Jl. Ahmad Yani No. 45, Surabaya'],
            ['kode_cabang' => 'MDN01', 'nama_cabang' => 'Medan', 'alamat' => 'Jl. Gatot Subroto No. 7, Medan'],
            ['kode_cabang' => 'SMG01', 'nama_cabang' => 'Semarang', 'alamat' => 'Jl. Pandanaran No. 9, Semarang'],
            ['kode_cabang' => 'YGY01', 'nama_cabang' => 'Yogyakarta', 'alamat' => 'Jl. Malioboro No. 2, Yogyakarta'],
            ['kode_cabang' => 'PLG01', 'nama_cabang' => 'Palembang', 'alamat' => 'Jl. Sudirman No. 4, Palembang'],
            ['kode_cabang' => 'PKU01', 'nama_cabang' => 'Pekanbaru', 'alamat' => 'Jl. Jenderal Sudirman No. 11, Pekanbaru'],
            ['kode_cabang' => 'BPN01', 'nama_cabang' => 'Balikpapan', 'alamat' => 'Jl. Jenderal Sudirman No. 6, Balikpapan'],
            ['kode_cabang' => 'MKS01', 'nama_cabang' => 'Makassar', 'alamat' => 'Jl. Urip Sumoharjo No. 13, Makassar'],
            ['kode_cabang' => 'DPS01', 'nama_cabang' => 'Denpasar', 'alamat' => 'Jl. Teuku Umar No. 22, Denpasar'],
            ['kode_cabang' => 'MLG01', 'nama_cabang' => 'Malang', 'alamat' => 'Jl. Jaksa Agung Suprapto No. 3, Malang'],
            ['kode_cabang' => 'BTM01', 'nama_cabang' => 'Batam', 'alamat' => 'Jl. Raja Ali Haji No. 5, Batam'],
            ['kode_cabang' => 'SOL01', 'nama_cabang' => 'Solo', 'alamat' => 'Jl. Slamet Riyadi No. 7, Solo'],
            ['kode_cabang' => 'LMP01', 'nama_cabang' => 'Bandar Lampung', 'alamat' => 'Jl. Kartini No. 8, Bandar Lampung'],
            ['kode_cabang' => 'PNK01', 'nama_cabang' => 'Pontianak', 'alamat' => 'Jl. Ahmad Yani No. 10, Pontianak'],
            ['kode_cabang' => 'PDA01', 'nama_cabang' => 'Padang', 'alamat' => 'Jl. Bagindo Aziz Chan No. 4, Padang'],
            ['kode_cabang' => 'BJM01', 'nama_cabang' => 'Banjarmasin', 'alamat' => 'Jl. Ahmad Yani No. 15, Banjarmasin'],
            ['kode_cabang' => 'AMB01', 'nama_cabang' => 'Ambon', 'alamat' => 'Jl. Pattimura No. 2, Ambon'],
            ['kode_cabang' => 'MND01', 'nama_cabang' => 'Manado', 'alamat' => 'Jl. 17 Agustus No. 6, Manado'],
            ['kode_cabang' => 'JPR01', 'nama_cabang' => 'Jayapura', 'alamat' => 'Jl. Sam Ratulangi No. 1, Jayapura'],
            ['kode_cabang' => 'CRB01', 'nama_cabang' => 'Cirebon', 'alamat' => 'Jl. Siliwangi No. 5, Cirebon'],
            ['kode_cabang' => 'BKS01', 'nama_cabang' => 'Bekasi', 'alamat' => 'Jl. Ahmad Yani No. 18, Bekasi'],
            ['kode_cabang' => 'DPK01', 'nama_cabang' => 'Depok', 'alamat' => 'Jl. Margonda Raya No. 22, Depok'],
        ];

        foreach ($cabang as $item) {
            DB::table('cabang')->insert(array_merge($item, [
                'aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jaminan_kerja', function (Blueprint $table) {
            // Ubah enum status tambah 2 status baru
            $table->enum('status', ['AKTIF','DIKIRIM_KURIR','DITERIMA_KARYAWAN','KEMBALI'])
                  ->default('AKTIF')->change();

            // Step 1: Admin Pusat serahkan ke kurir
            $table->foreignId('dikirim_kurir_oleh')->nullable()->after('tgl_dikonfirmasi')
                  ->constrained('users')->restrictOnDelete();
            $table->timestamp('tgl_dikirim_kurir')->nullable()->after('dikirim_kurir_oleh');

            // Step 2: Admin Cabang konfirmasi karyawan terima
            $table->foreignId('diterima_karyawan_oleh')->nullable()->after('tgl_dikirim_kurir')
                  ->constrained('users')->restrictOnDelete();
            $table->timestamp('tgl_diterima_karyawan')->nullable()->after('diterima_karyawan_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('jaminan_kerja', function (Blueprint $table) {
            $table->dropForeign(['dikirim_kurir_oleh']);
            $table->dropForeign(['diterima_karyawan_oleh']);
            $table->dropColumn(['dikirim_kurir_oleh','tgl_dikirim_kurir','diterima_karyawan_oleh','tgl_diterima_karyawan']);
            $table->enum('status', ['AKTIF','KEMBALI'])->default('AKTIF')->change();
        });
    }
};

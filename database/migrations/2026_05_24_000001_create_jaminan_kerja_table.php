<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jaminan_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('no_jaminan', 30)->unique();
            $table->foreignId('cabang_id')->constrained('cabang')->restrictOnDelete();
            $table->string('nama_karyawan', 100);
            $table->string('no_ktp', 20);
            $table->string('jabatan', 100);
            $table->string('no_hp', 20)->nullable();
            $table->date('tgl_masuk_kerja');
            $table->boolean('has_akte')->default(false);
            $table->boolean('has_bpkb')->default(false);
            $table->boolean('has_ijasah')->default(false);
            $table->text('catatan')->nullable();
            $table->enum('status', ['AKTIF', 'KEMBALI'])->default('AKTIF');
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->foreignId('diterima_oleh')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('tgl_diterima')->nullable();
            $table->foreignId('dikembalikan_oleh')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('tgl_dikembalikan')->nullable();
            $table->text('catatan_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jaminan_kerja');
    }
};

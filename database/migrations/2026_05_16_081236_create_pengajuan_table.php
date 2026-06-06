<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengajuan', 30)->unique();
            $table->enum('jenis_jaminan', ['BPKB', 'SERTIFIKAT']);
            $table->foreignId('cabang_id')->constrained('cabang')->restrictOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['MENUNGGU', 'DIPROSES', 'DISETUJUI', 'DITOLAK'])->default('MENUNGGU');
            $table->text('catatan_pusat')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('tgl_dibuat')->useCurrent();
            $table->timestamp('tgl_diproses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};

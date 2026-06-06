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
        Schema::create('reimburse', function (Blueprint $table) {
            $table->id();
            $table->string('no_reimburse', 30)->unique();
            $table->foreignId('cabang_id')->constrained('cabang')->restrictOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->string('nama_pemohon', 100);
            $table->string('jabatan', 100)->nullable();
            $table->date('tanggal_pengeluaran');
            $table->enum('kategori', ['TRANSPORT','MAKAN','AKOMODASI','OPERASIONAL','LAINNYA']);
            $table->text('keterangan');
            $table->decimal('nominal_diajukan', 15, 2);
            $table->decimal('nominal_disetujui', 15, 2)->nullable();
            $table->enum('status', ['MENUNGGU','DISETUJUI','DITOLAK'])->default('MENUNGGU');
            $table->text('catatan_pusat')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('tgl_diproses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reimburse');
    }
};

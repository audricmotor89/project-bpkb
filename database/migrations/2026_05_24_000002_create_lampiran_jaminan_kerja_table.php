<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lampiran_jaminan_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jaminan_kerja_id')->constrained('jaminan_kerja')->cascadeOnDelete();
            $table->enum('jenis_dokumen', ['AKTE_KELAHIRAN', 'BPKB', 'IJASAH', 'FOTO_PENERIMAAN', 'FOTO_PENGEMBALIAN', 'LAINNYA']);
            $table->string('nama_file_asli', 255);
            $table->string('nama_file_storage', 255)->unique();
            $table->unsignedBigInteger('ukuran_file');
            $table->string('mime_type', 100);
            $table->foreignId('diupload_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampiran_jaminan_kerja');
    }
};

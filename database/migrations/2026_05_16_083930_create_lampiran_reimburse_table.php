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
        Schema::create('lampiran_reimburse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimburse_id')->constrained('reimburse')->restrictOnDelete();
            $table->enum('jenis_dokumen', ['KWITANSI','STRUK','FOTO','LAINNYA']);
            $table->string('nama_file_asli', 255);
            $table->string('nama_file_storage', 255);
            $table->unsignedInteger('ukuran_file');
            $table->string('mime_type', 50);
            $table->foreignId('diupload_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran_reimburse');
    }
};

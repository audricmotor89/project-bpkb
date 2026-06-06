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
        Schema::create('detail_sertifikat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->restrictOnDelete();
            $table->string('nama_nasabah', 100);
            $table->string('no_ktp', 16);
            $table->string('no_sertifikat', 50);
            $table->decimal('total_pinjaman', 15, 2);
            $table->string('no_kartu_piutang', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_sertifikat');
    }
};

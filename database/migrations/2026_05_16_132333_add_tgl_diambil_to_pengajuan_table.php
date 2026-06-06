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
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->timestamp('tgl_diambil')->nullable()->after('tgl_diproses');
            $table->foreignId('diambil_oleh')->nullable()->after('tgl_diambil')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diambil_oleh');
            $table->dropColumn('tgl_diambil');
        });
    }
};

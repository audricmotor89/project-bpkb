<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jaminan_kerja', function (Blueprint $table) {
            $table->enum('status_pusat', ['MENUNGGU', 'DITERIMA', 'DITOLAK'])->default('MENUNGGU')->after('status');
            $table->text('catatan_pusat')->nullable()->after('status_pusat');
            $table->foreignId('dikonfirmasi_oleh')->nullable()->after('catatan_pusat')->constrained('users')->restrictOnDelete();
            $table->timestamp('tgl_dikonfirmasi')->nullable()->after('dikonfirmasi_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('jaminan_kerja', function (Blueprint $table) {
            $table->dropForeign(['dikonfirmasi_oleh']);
            $table->dropColumn(['status_pusat', 'catatan_pusat', 'dikonfirmasi_oleh', 'tgl_dikonfirmasi']);
        });
    }
};

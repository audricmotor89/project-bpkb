<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lampiran_reimburse', function (Blueprint $table) {
            $table->string('kategori_biaya')->nullable()->after('jenis_dokumen');
            $table->decimal('nominal', 15, 2)->nullable()->after('kategori_biaya');
        });
    }

    public function down(): void
    {
        Schema::table('lampiran_reimburse', function (Blueprint $table) {
            $table->dropColumn(['kategori_biaya', 'nominal']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->uuid('qr_token')->nullable()->unique()->after('diambil_oleh');
        });

        // Isi qr_token untuk data yang sudah ada
        DB::table('pengajuan')->whereNull('qr_token')->orderBy('id')->each(function ($row) {
            DB::table('pengajuan')->where('id', $row->id)->update(['qr_token' => (string) Str::uuid()]);
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_cabang', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cabang_id')->constrained('cabang')->cascadeOnDelete();
            $table->primary(['user_id', 'cabang_id']);
        });

        // Migrate existing cabang_id on users → user_cabang
        $users = DB::table('users')->whereNotNull('cabang_id')->get();
        foreach ($users as $u) {
            DB::table('user_cabang')->insertOrIgnore([
                'user_id'   => $u->id,
                'cabang_id' => $u->cabang_id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cabang');
    }
};

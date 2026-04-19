<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jemaat', function (Blueprint $table) {
            $table->string('no_identitas', 50)->nullable()->after('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
        });
    }

    public function down(): void
    {
        Schema::table('jemaat', function (Blueprint $table) {
            $table->dropColumn(['no_identitas', 'jenis_kelamin']);
        });
    }
};

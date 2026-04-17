<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jemaat', function (Blueprint $table) {
            $table->string('status_perkawinan')->nullable()->after('email');
            $table->string('kategori_jemaat')->nullable()->after('status_perkawinan');
        });
    }

    public function down(): void
    {
        Schema::table('jemaat', function (Blueprint $table) {
            $table->dropColumn(['status_perkawinan', 'kategori_jemaat']);
        });
    }
};

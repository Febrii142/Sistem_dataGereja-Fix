<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_jemaat', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });

        DB::table('kategori_jemaat')->insert([
            ['nama' => 'Umum', 'deskripsi' => 'Kategori umum jemaat', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Remaja', 'deskripsi' => 'Kategori usia remaja', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Dewasa', 'deskripsi' => 'Kategori usia dewasa', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lansia', 'deskripsi' => 'Kategori usia lansia', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_jemaat');
    }
};

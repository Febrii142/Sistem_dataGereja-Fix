<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_keluarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kepala_keluarga_id')->constrained('jemaat')->cascadeOnDelete();
            $table->foreignId('jemaat_id')->constrained('jemaat')->cascadeOnDelete();
            $table->enum('hubungan_keluarga', ['Istri', 'Suami', 'Anak', 'Orangtua', 'Saudara']);
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->timestamps();

            $table->unique(['kepala_keluarga_id', 'jemaat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_keluarga');
    }
};

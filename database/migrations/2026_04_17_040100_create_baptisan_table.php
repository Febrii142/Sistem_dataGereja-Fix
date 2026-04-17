<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baptisan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jemaat_id')->constrained('jemaat')->cascadeOnDelete();
            $table->date('tanggal_baptis');
            $table->string('tempat_baptis');
            $table->string('nama_pendeta')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique('jemaat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baptisan');
    }
};

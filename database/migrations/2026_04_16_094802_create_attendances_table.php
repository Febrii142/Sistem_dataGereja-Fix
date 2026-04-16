<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->date('service_date');
            $table->boolean('hadir')->default(true);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'service_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

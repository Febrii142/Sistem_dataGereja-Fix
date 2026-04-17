<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->primary(['role_id', 'permission_id']);

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->cascadeOnDelete();
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->cascadeOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('role');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->nullOnDelete();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->string('kontak');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('pekerjaan')->nullable();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->date('service_date');
            $table->boolean('hadir')->default(true);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'service_date']);

            $table->foreign('member_id')
                ->references('id')
                ->on('members')
                ->cascadeOnDelete();
        });

        Schema::create('jemaat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('no_telepon', 30);
            $table->string('email');
            $table->string('status_perkawinan')->nullable();
            $table->string('kategori_jemaat')->nullable();
            $table->enum('status_baptis', ['sudah', 'belum'])->default('belum');
            $table->date('tanggal_baptis')->nullable();
            $table->unsignedBigInteger('kepala_keluarga_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('kepala_keluarga_id')
                ->references('id')
                ->on('jemaat')
                ->nullOnDelete();
        });

        Schema::create('kategori_jemaat', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['type', 'name']);
        });

        Schema::create('jemaat_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('jemaat_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->primary(['jemaat_id', 'category_id']);

            $table->foreign('jemaat_id')
                ->references('id')
                ->on('jemaat')
                ->cascadeOnDelete();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete();
        });

        Schema::create('baptisan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jemaat_id')->unique();
            $table->date('tanggal_baptis');
            $table->string('tempat_baptis');
            $table->string('nama_pendeta')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('jemaat_id')
                ->references('id')
                ->on('jemaat')
                ->cascadeOnDelete();
        });

        Schema::create('anggota_keluarga', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kepala_keluarga_id');
            $table->unsignedBigInteger('jemaat_id');
            $table->enum('hubungan_keluarga', ['Istri', 'Suami', 'Anak', 'Orangtua', 'Saudara']);
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->timestamps();

            $table->unique(['kepala_keluarga_id', 'jemaat_id']);

            $table->foreign('kepala_keluarga_id')
                ->references('id')
                ->on('jemaat')
                ->cascadeOnDelete();
            $table->foreign('jemaat_id')
                ->references('id')
                ->on('jemaat')
                ->cascadeOnDelete();
        });

        Schema::create('keluarga_jemaat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jemaat_id');
            $table->string('nama');
            $table->string('hubungan');
            $table->string('no_telp', 30)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->timestamps();

            $table->foreign('jemaat_id')
                ->references('id')
                ->on('jemaat')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluarga_jemaat');
        Schema::dropIfExists('anggota_keluarga');
        Schema::dropIfExists('baptisan');
        Schema::dropIfExists('jemaat_categories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('kategori_jemaat');
        Schema::dropIfExists('jemaat');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('members');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('role_id');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('jemaat', function (Blueprint $table) {
            if (! Schema::hasColumn('jemaat', 'kelas_katekisasi')) {
                $table->string('kelas_katekisasi')->nullable()->after('status_baptis');
            }
        });

        DB::table('users')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('jemaat', function (Blueprint $table) {
            if (Schema::hasColumn('jemaat', 'kelas_katekisasi')) {
                $table->dropColumn('kelas_katekisasi');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

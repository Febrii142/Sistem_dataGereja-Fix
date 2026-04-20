<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'baptism_status')) {
                $table->enum('baptism_status', ['sudah_dibaptis', 'belum_dibaptis'])->nullable()->after('jenis_kelamin');
            }
            if (! Schema::hasColumn('members', 'baptism_date')) {
                $table->date('baptism_date')->nullable()->after('baptism_status');
            }
            if (! Schema::hasColumn('members', 'baptism_location')) {
                $table->string('baptism_location')->nullable()->after('baptism_date');
            }
            if (! Schema::hasColumn('members', 'catechism_batch')) {
                $table->string('catechism_batch')->nullable()->after('baptism_location');
            }
            if (! Schema::hasColumn('members', 'parent_guardian_name')) {
                $table->string('parent_guardian_name')->nullable()->after('catechism_batch');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'parent_guardian_name')) {
                $table->dropColumn('parent_guardian_name');
            }
            if (Schema::hasColumn('members', 'catechism_batch')) {
                $table->dropColumn('catechism_batch');
            }
            if (Schema::hasColumn('members', 'baptism_location')) {
                $table->dropColumn('baptism_location');
            }
            if (Schema::hasColumn('members', 'baptism_date')) {
                $table->dropColumn('baptism_date');
            }
            if (Schema::hasColumn('members', 'baptism_status')) {
                $table->dropColumn('baptism_status');
            }
        });
    }
};

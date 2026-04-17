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
            $table->unsignedBigInteger('role_id')->nullable()->after('role');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->nullOnDelete();
        });

        $roles = DB::table('roles')->pluck('id', 'name');

        $mapping = [
            'admin' => 'Admin',
            'pendeta' => 'Pendeta',
            'koordinator' => 'Staff',
            'user' => 'Member',
        ];

        foreach ($mapping as $legacyRole => $roleName) {
            $roleId = $roles[$roleName] ?? null;

            if ($roleId) {
                DB::table('users')->where('role', $legacyRole)->update(['role_id' => $roleId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};

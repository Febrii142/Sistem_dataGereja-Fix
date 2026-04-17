<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJemaatDashboardTables extends Migration
{
    public function up()
    {
        Schema::create('jemaat_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('jemaat_families', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jemaat_profile_id');
            $table->string('family_member_name');
            $table->string('relationship');
            $table->timestamps();

            $table->foreign('jemaat_profile_id')->references('id')->on('jemaat_profiles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jemaat_families');
        Schema::dropIfExists('jemaat_profiles');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeConferenceProfessionNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_users', function (Blueprint $table) {
            $table->unsignedInteger('conference_id')->nullable()->change();
            $table->unsignedInteger('profession_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registration_users', function (Blueprint $table) {
            $table->unsignedInteger('conference_id')->nullable(false)->change();
            $table->unsignedInteger('profession_id')->nullable(false)->change();
        });
    }
}

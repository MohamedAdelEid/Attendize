<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckInAndCheckOutColumnsToRegistrationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_users', function (Blueprint $table) {
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
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
            $table->dropColumn('check_in');
            $table->dropColumn('check_out');
        });
    }
}

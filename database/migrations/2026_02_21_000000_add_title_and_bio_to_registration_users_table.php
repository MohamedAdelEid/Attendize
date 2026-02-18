<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleAndBioToRegistrationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_users', function (Blueprint $table) {
            $table->string('title')->nullable()->after('last_name');
            $table->text('bio')->nullable()->after('title');
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
            $table->dropColumn(['title', 'bio']);
        });
    }
}

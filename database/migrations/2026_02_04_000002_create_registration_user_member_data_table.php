<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRegistrationUserMemberDataTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('registration_user_member_data')) {
            Schema::create('registration_user_member_data', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('registration_user_id');
                $table->string('field_key', 64);
                $table->text('value')->nullable();
                $table->timestamps();

                $table->foreign('registration_user_id')->references('id')->on('registration_users')->onDelete('cascade');
                $table->unique(['registration_user_id', 'field_key'], 'ru_member_data_uid_fk_unique');
            });
        } else {
            $indexExists = DB::select("SHOW INDEX FROM registration_user_member_data WHERE Key_name = 'ru_member_data_uid_fk_unique'");
            if (empty($indexExists)) {
                Schema::table('registration_user_member_data', function (Blueprint $table) {
                    $table->unique(['registration_user_id', 'field_key'], 'ru_member_data_uid_fk_unique');
                });
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('registration_user_member_data');
    }
}

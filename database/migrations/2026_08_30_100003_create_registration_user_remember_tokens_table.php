<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationUserRememberTokensTable extends Migration
{
    public function up()
    {
        Schema::create('registration_user_remember_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_user_id');
            $table->unsignedInteger('event_id');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->foreign('registration_user_id')
                ->references('id')
                ->on('registration_users')
                ->onDelete('cascade');
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('registration_user_remember_tokens');
    }
}

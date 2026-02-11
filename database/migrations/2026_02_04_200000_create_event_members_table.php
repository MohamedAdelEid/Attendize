<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventMembersTable extends Migration
{
    public function up()
    {
        Schema::create('event_members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->string('status', 20)->default('pending'); // pending, approved
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('event_member_data', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_member_id');
            $table->string('field_key', 64);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('event_member_id')->references('id')->on('event_members')->onDelete('cascade');
            $table->unique(['event_member_id', 'field_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_member_data');
        Schema::dropIfExists('event_members');
    }
}

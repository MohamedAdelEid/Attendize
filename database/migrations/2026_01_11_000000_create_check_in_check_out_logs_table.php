<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckInCheckOutLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_in_check_out_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_user_id');
            $table->unsignedInteger('event_id');
            $table->enum('action', ['check_in', 'check_out']);
            $table->timestamp('action_time');
            $table->timestamps();

            // Foreign keys
            $table->foreign('registration_user_id')->references('id')->on('registration_users')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['registration_user_id', 'action_time']);
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_in_check_out_logs');
    }
}
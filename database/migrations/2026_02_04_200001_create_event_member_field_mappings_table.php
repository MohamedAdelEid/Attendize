<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventMemberFieldMappingsTable extends Migration
{
    public function up()
    {
        Schema::create('event_member_field_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('registration_id'); // the "Members form" registration
            $table->string('member_field_key', 64); // e.g. full_name, email, membership_number
            $table->string('target_type', 32); // first_name, last_name, email, phone, dynamic_field
            $table->unsignedInteger('target_dynamic_form_field_id')->nullable(); // when target_type = dynamic_field
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->unique(['event_id', 'registration_id', 'member_field_key'], 'event_reg_member_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_member_field_mappings');
    }
}

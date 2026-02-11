<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventMemberFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('event_member_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->string('field_key', 64); // e.g. membership_number, full_name, expiration_date
            $table->string('label');
            $table->enum('type', ['text', 'number', 'date', 'datetime'])->default('text');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->unique(['event_id', 'field_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_member_fields');
    }
}

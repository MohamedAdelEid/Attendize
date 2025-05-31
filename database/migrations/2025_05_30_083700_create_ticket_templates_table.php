<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id')->unique();
            $table->string('background_image_path')->nullable(); // Path to uploaded background image/PDF
            $table->string('name_position_x')->nullable();
            $table->string('name_position_y')->nullable();
            $table->string('name_font_size')->nullable();
            $table->string('name_font_color')->nullable();
            $table->string('code_position_x')->nullable();
            $table->string('code_position_y')->nullable();
            $table->string('code_font_size')->nullable();
            $table->string('code_font_color')->nullable();
            $table->string('qr_position_x')->nullable();
            $table->string('qr_position_y')->nullable();
            $table->string('qr_size')->nullable(); // e.g., 100px
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_templates');
    }
}
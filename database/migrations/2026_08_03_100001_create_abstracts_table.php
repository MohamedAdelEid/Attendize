<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractsTable extends Migration
{
    public function up()
    {
        Schema::create('abstracts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->string('name');
            $table->string('slug');
            $table->longText('instructions')->nullable();
            $table->unsignedInteger('max_submissions_per_user')->nullable();
            $table->enum('register_condition', ['open', 'registered_only'])->default('open');
            $table->boolean('all_event_registrations')->default(false);
            $table->enum('approval_mode', ['automatic', 'manual'])->default('manual');
            $table->string('email_subject')->nullable();
            $table->longText('email_body')->nullable();
            $table->boolean('email_attach_template')->default(true);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('show_on_landing')->default(false);
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->unique(['event_id', 'slug']);
            $table->index(['event_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstracts');
    }
}

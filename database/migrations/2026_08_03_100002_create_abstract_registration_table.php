<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractRegistrationTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_registration', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('abstract_id');
            $table->unsignedInteger('registration_id');
            $table->timestamps();

            $table->foreign('abstract_id')->references('id')->on('abstracts')->onDelete('cascade');
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->unique(['abstract_id', 'registration_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_registration');
    }
}

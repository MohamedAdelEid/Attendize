<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractDynamicFormFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_dynamic_form_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('abstract_id');
            $table->string('label');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type');
            $table->text('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('abstract_id')->references('id')->on('abstracts')->onDelete('cascade');
            $table->index('abstract_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_dynamic_form_fields');
    }
}

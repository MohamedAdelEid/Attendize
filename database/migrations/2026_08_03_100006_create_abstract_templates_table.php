<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('abstract_id');
            $table->unsignedInteger('abstract_category_id');
            $table->string('template_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('abstract_id')->references('id')->on('abstracts')->onDelete('cascade');
            $table->foreign('abstract_category_id')->references('id')->on('abstract_categories')->onDelete('restrict');
            $table->index('abstract_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_templates');
    }
}

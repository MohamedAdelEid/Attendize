<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractReviewerAbstractTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_reviewer_abstract', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('abstract_reviewer_id');
            $table->unsignedInteger('abstract_id');
            $table->timestamps();

            $table->foreign('abstract_reviewer_id')->references('id')->on('abstract_reviewers')->onDelete('cascade');
            $table->foreign('abstract_id')->references('id')->on('abstracts')->onDelete('cascade');
            $table->unique(['abstract_reviewer_id', 'abstract_id'], 'abstract_reviewer_abstract_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_reviewer_abstract');
    }
}

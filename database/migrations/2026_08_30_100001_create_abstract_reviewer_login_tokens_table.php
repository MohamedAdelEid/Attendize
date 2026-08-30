<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractReviewerLoginTokensTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_reviewer_login_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('abstract_reviewer_id');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('abstract_reviewer_id')
                ->references('id')
                ->on('abstract_reviewers')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_reviewer_login_tokens');
    }
}

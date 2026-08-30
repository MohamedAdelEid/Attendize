<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractReviewersTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_reviewers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->boolean('access_all_abstracts')->default(false);
            $table->boolean('can_review')->default(true);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->unique(['event_id', 'email']);
            $table->index(['event_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_reviewers');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('abstract_id');
            $table->unsignedInteger('registration_user_id')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('authors')->nullable();
            $table->text('details')->nullable();
            $table->string('domain')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('abstract_id')->references('id')->on('abstracts')->onDelete('cascade');
            $table->foreign('registration_user_id')->references('id')->on('registration_users')->onDelete('set null');
            $table->index(['abstract_id', 'status']);
            $table->index(['abstract_id', 'email']);
            $table->index('registration_user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_submissions');
    }
}

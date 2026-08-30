<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewedByToAbstractSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::table('abstract_submissions', function (Blueprint $table) {
            $table->unsignedInteger('reviewed_by_reviewer_id')->nullable()->after('review_notes');
            $table->foreign('reviewed_by_reviewer_id')
                ->references('id')
                ->on('abstract_reviewers')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('abstract_submissions', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by_reviewer_id']);
            $table->dropColumn('reviewed_by_reviewer_id');
        });
    }
}

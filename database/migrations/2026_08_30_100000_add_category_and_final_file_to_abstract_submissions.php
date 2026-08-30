<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryAndFinalFileToAbstractSubmissions extends Migration
{
    public function up()
    {
        Schema::table('abstract_submissions', function (Blueprint $table) {
            $table->unsignedInteger('abstract_category_id')->nullable()->after('abstract_id');
            $table->string('final_file_path')->nullable()->after('file_path');
            $table->timestamp('final_submitted_at')->nullable()->after('final_file_path');

            $table->foreign('abstract_category_id')
                ->references('id')
                ->on('abstract_categories')
                ->onDelete('restrict');
            $table->index('abstract_category_id');
        });
    }

    public function down()
    {
        Schema::table('abstract_submissions', function (Blueprint $table) {
            $table->dropForeign(['abstract_category_id']);
            $table->dropColumn(['abstract_category_id', 'final_file_path', 'final_submitted_at']);
        });
    }
}

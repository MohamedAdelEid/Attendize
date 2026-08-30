<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropLegacyCategoryAndTemplateFromAbstractsTable extends Migration
{
    public function up()
    {
        Schema::table('abstracts', function (Blueprint $table) {
            if (Schema::hasColumn('abstracts', 'abstract_category_id')) {
                $table->dropForeign(['abstract_category_id']);
                $table->dropColumn('abstract_category_id');
            }
            if (Schema::hasColumn('abstracts', 'template_path')) {
                $table->dropColumn('template_path');
            }
        });
    }

    public function down()
    {
        Schema::table('abstracts', function (Blueprint $table) {
            if (!Schema::hasColumn('abstracts', 'abstract_category_id')) {
                $table->unsignedInteger('abstract_category_id')->nullable()->after('event_id');
            }
            if (!Schema::hasColumn('abstracts', 'template_path')) {
                $table->string('template_path')->nullable()->after('slug');
            }
        });
    }
}

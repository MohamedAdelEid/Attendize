<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreviewDimensionsToTicketTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('ticket_templates', function (Blueprint $table) {
            $table->integer('preview_width')->nullable();
            $table->integer('preview_height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('ticket_templates', function (Blueprint $table) {
            $table->dropColumn(['preview_width', 'preview_height']);
        });
    }
}

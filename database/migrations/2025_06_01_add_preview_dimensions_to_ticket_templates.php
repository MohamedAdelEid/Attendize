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
            $table->integer('preview_width')->nullable()->after('background_image_path');
            $table->integer('preview_height')->nullable()->after('preview_width');
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

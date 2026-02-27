<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfPageSettingsToTicketTemplates extends Migration
{
    public function up()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->string('pdf_page_size', 16)->nullable()->after('preview_height'); // a4, a5, a6, etc.
            $table->string('pdf_orientation', 16)->nullable()->after('pdf_page_size'); // portrait, landscape
        });
    }

    public function down()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->dropColumn(['pdf_page_size', 'pdf_orientation']);
        });
    }
}

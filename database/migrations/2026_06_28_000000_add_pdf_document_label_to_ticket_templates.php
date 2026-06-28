<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfDocumentLabelToTicketTemplates extends Migration
{
    public function up()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->string('pdf_document_label', 32)->default('ticket')->after('pdf_orientation');
        });
    }

    public function down()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->dropColumn('pdf_document_label');
        });
    }
}

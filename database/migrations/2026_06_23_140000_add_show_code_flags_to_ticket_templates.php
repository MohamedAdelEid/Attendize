<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowCodeFlagsToTicketTemplates extends Migration
{
    public function up()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->boolean('show_registration_code')->default(true)->after('code_font_color');
            $table->boolean('show_qr_code')->default(true)->after('qr_size');
        });
    }

    public function down()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->dropColumn(['show_registration_code', 'show_qr_code']);
        });
    }
}

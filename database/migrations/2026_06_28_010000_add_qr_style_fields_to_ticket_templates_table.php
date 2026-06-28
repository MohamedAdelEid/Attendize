<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrStyleFieldsToTicketTemplatesTable extends Migration
{
    public function up()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_templates', 'qr_padding')) {
                $table->unsignedSmallInteger('qr_padding')->default(0)->after('qr_size');
            }
            if (!Schema::hasColumn('ticket_templates', 'qr_background_color')) {
                $table->string('qr_background_color', 7)->default('#ffffff')->after('qr_padding');
            }
            if (!Schema::hasColumn('ticket_templates', 'qr_border_radius')) {
                $table->unsignedSmallInteger('qr_border_radius')->default(0)->after('qr_background_color');
            }
        });
    }

    public function down()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_templates', 'qr_border_radius')) {
                $table->dropColumn('qr_border_radius');
            }
            if (Schema::hasColumn('ticket_templates', 'qr_background_color')) {
                $table->dropColumn('qr_background_color');
            }
            if (Schema::hasColumn('ticket_templates', 'qr_padding')) {
                $table->dropColumn('qr_padding');
            }
        });
    }
}

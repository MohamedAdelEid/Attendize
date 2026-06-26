<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameWidthToTicketTemplates extends Migration
{
    public function up()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->unsignedInteger('name_width')->nullable()->after('name_position_y');
        });
    }

    public function down()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->dropColumn('name_width');
        });
    }
}

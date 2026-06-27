<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionHeaderEnabledToEventLandingPages extends Migration
{
    public function up()
    {
        Schema::table('event_landing_pages', function (Blueprint $table) {
            $table->boolean('section_header_enabled')->default(true)->after('section_footer_enabled');
        });
    }

    public function down()
    {
        Schema::table('event_landing_pages', function (Blueprint $table) {
            $table->dropColumn('section_header_enabled');
        });
    }
}

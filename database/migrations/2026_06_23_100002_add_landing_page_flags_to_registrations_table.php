<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLandingPageFlagsToRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->boolean('show_on_fees_section')->default(false)->after('is_members_form');
            $table->boolean('is_virtual_form')->default(false)->after('show_on_fees_section');
            $table->unsignedSmallInteger('fees_display_order')->default(0)->after('is_virtual_form');
            $table->string('fees_card_badge')->nullable()->after('fees_display_order');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'show_on_fees_section',
                'is_virtual_form',
                'fees_display_order',
                'fees_card_badge',
            ]);
        });
    }
}

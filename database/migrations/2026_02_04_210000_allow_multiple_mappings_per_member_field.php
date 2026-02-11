<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowMultipleMappingsPerMemberField extends Migration
{
    public function up()
    {
        Schema::table('event_member_field_mappings', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['registration_id']);
        });
        Schema::table('event_member_field_mappings', function (Blueprint $table) {
            $table->dropUnique('event_reg_member_key');
        });
        Schema::table('event_member_field_mappings', function (Blueprint $table) {
            $table->unique(
                ['event_id', 'registration_id', 'member_field_key', 'target_type', 'target_dynamic_form_field_id'],
                'event_reg_member_target_unique'
            );
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('event_member_field_mappings', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['registration_id']);
        });
        Schema::table('event_member_field_mappings', function (Blueprint $table) {
            $table->dropUnique('event_reg_member_target_unique');
        });
        Schema::table('event_member_field_mappings', function (Blueprint $table) {
            $table->unique(['event_id', 'registration_id', 'member_field_key'], 'event_reg_member_key');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrivateFormToRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->after('is_members_form');
            $table->string('private_slug', 64)->nullable()->unique()->after('is_private');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['is_private', 'private_slug']);
        });
    }
}

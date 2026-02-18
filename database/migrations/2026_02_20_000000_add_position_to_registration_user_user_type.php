<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionToRegistrationUserUserType extends Migration
{
    public function up()
    {
        if (Schema::hasTable('registration_user_user_type') && !Schema::hasColumn('registration_user_user_type', 'position')) {
            Schema::table('registration_user_user_type', function (Blueprint $table) {
                $table->unsignedInteger('position')->nullable()->after('user_type_option_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('registration_user_user_type') && Schema::hasColumn('registration_user_user_type', 'position')) {
            Schema::table('registration_user_user_type', function (Blueprint $table) {
                $table->dropColumn('position');
            });
        }
    }
}

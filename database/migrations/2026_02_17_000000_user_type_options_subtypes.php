<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Replace user_type_category (event categories link) with user_type_options:
 * each User Type has its own "sub-types" (e.g. Delegate -> user_type1, user_type2).
 */
class UserTypeOptionsSubtypes extends Migration
{
    public function up()
    {
        // Drop old user_type_category (linked to event categories)
        Schema::dropIfExists('user_type_category');

        // Sub-types/options per user type (e.g. Delegate -> "Type A", "Type B")
        Schema::create('user_type_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_type_id');
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
        });

        // Pivot: allow storing which "option" (sub-type) for each (user, user_type)
        if (Schema::hasTable('registration_user_user_type')) {
            Schema::table('registration_user_user_type', function (Blueprint $table) {
                if (!Schema::hasColumn('registration_user_user_type', 'user_type_option_id')) {
                    $table->unsignedInteger('user_type_option_id')->nullable()->after('user_type_id');
                    $table->foreign('user_type_option_id', 'ru_ut_option_fk')->references('id')->on('user_type_options')->onDelete('set null');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('registration_user_user_type', function (Blueprint $table) {
            $table->dropForeign(['user_type_option_id']);
            $table->dropColumn('user_type_option_id');
        });
        Schema::dropIfExists('user_type_options');

        // Recreate old user_type_category structure (empty)
        Schema::create('user_type_category', function (Blueprint $table) {
            $table->unsignedInteger('user_type_id');
            $table->unsignedInteger('category_id');
            $table->primary(['user_type_id', 'category_id'], 'ut_cat_primary');
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
}

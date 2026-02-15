<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUserTypeFeaturesMultiTypesCategoriesAvatar extends Migration
{
    public function up()
    {
        // Pivot: registration user can have many user types
        if (!Schema::hasTable('registration_user_user_type')) {
            Schema::create('registration_user_user_type', function (Blueprint $table) {
                $table->unsignedInteger('registration_user_id');
                $table->unsignedInteger('user_type_id');
                $table->primary(['registration_user_id', 'user_type_id'], 'reg_user_type_primary');
                $table->foreign('registration_user_id')->references('id')->on('registration_users')->onDelete('cascade');
                $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
            });
        } else {
            // Table may exist from a failed run - add primary and foreign keys if missing
            try {
                DB::statement('ALTER TABLE registration_user_user_type ADD PRIMARY KEY reg_user_type_primary (registration_user_id, user_type_id)');
            } catch (\Exception $e) { /* primary may exist */ }
            try {
                Schema::table('registration_user_user_type', function (Blueprint $t) {
                    $t->foreign('registration_user_id', 'ru_ut_reg_user_fk')->references('id')->on('registration_users')->onDelete('cascade');
                    $t->foreign('user_type_id', 'ru_ut_type_fk')->references('id')->on('user_types')->onDelete('cascade');
                });
            } catch (\Exception $e) { /* FKs may exist */ }
        }

        // Migrate existing user_type_id to pivot
        $rows = DB::table('registration_users')->whereNotNull('user_type_id')->select('id', 'user_type_id')->get();
        foreach ($rows as $row) {
            DB::table('registration_user_user_type')->insertOrIgnore([
                'registration_user_id' => $row->id,
                'user_type_id' => $row->user_type_id,
            ]);
        }

        // Drop foreign and column user_type_id from registration_users
        Schema::table('registration_users', function (Blueprint $table) {
            $table->dropForeign(['user_type_id']);
            $table->dropColumn('user_type_id');
        });

        // User type: show on landing page
        Schema::table('user_types', function (Blueprint $table) {
            $table->boolean('show_on_landing')->default(true)->after('name');
        });

        // User type categories: which categories belong to this user type
        Schema::create('user_type_category', function (Blueprint $table) {
            $table->unsignedInteger('user_type_id');
            $table->unsignedInteger('category_id');
            $table->primary(['user_type_id', 'category_id'], 'ut_cat_primary');
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        // Registration user avatar (optional)
        Schema::table('registration_users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('registration_users', function (Blueprint $table) {
            $table->unsignedInteger('user_type_id')->nullable()->after('conference_id');
        });
        $pivot = DB::table('registration_user_user_type')->orderBy('user_type_id')->get();
        $seen = [];
        foreach ($pivot as $row) {
            if (!isset($seen[$row->registration_user_id])) {
                $seen[$row->registration_user_id] = true;
                DB::table('registration_users')->where('id', $row->registration_user_id)->update(['user_type_id' => $row->user_type_id]);
            }
        }
        Schema::table('registration_users', function (Blueprint $table) {
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('set null');
        });
        Schema::dropIfExists('registration_user_user_type');

        Schema::table('user_types', function (Blueprint $table) {
            $table->dropColumn('show_on_landing');
        });
        Schema::dropIfExists('user_type_category');
        Schema::table('registration_users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
}

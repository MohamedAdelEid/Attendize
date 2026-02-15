<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Add slug to user_types (unique per event) and user_type_options (unique per user_type)
 * for public URLs: /e/{event}/committee/{user_type_slug} and .../committee/{user_type_slug}/{option_slug}
 */
class AddSlugToUserTypesAndOptions extends Migration
{
    public function up()
    {
        Schema::table('user_types', function (Blueprint $table) {
            $table->string('slug', 120)->nullable()->after('name');
        });

        foreach (\DB::table('user_types')->get() as $row) {
            $slug = Str::slug($row->name);
            $base = $slug;
            $n = 0;
            while (\DB::table('user_types')->where('event_id', $row->event_id)->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                $slug = $base . '-' . (++$n);
            }
            \DB::table('user_types')->where('id', $row->id)->update(['slug' => $slug]);
        }

        Schema::table('user_types', function (Blueprint $table) {
            $table->unique(['event_id', 'slug'], 'user_types_event_slug_unique');
        });
        \DB::statement('ALTER TABLE user_types MODIFY slug VARCHAR(120) NOT NULL');

        Schema::table('user_type_options', function (Blueprint $table) {
            $table->string('slug', 120)->nullable()->after('name');
        });

        foreach (\DB::table('user_type_options')->get() as $row) {
            $slug = Str::slug($row->name);
            $base = $slug;
            $n = 0;
            while (\DB::table('user_type_options')->where('user_type_id', $row->user_type_id)->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                $slug = $base . '-' . (++$n);
            }
            \DB::table('user_type_options')->where('id', $row->id)->update(['slug' => $slug]);
        }

        Schema::table('user_type_options', function (Blueprint $table) {
            $table->unique(['user_type_id', 'slug'], 'user_type_options_type_slug_unique');
        });
        \DB::statement('ALTER TABLE user_type_options MODIFY slug VARCHAR(120) NOT NULL');
    }

    public function down()
    {
        Schema::table('user_types', function (Blueprint $table) {
            $table->dropUnique('user_types_event_slug_unique');
            $table->dropColumn('slug');
        });
        Schema::table('user_type_options', function (Blueprint $table) {
            $table->dropUnique('user_type_options_type_slug_unique');
            $table->dropColumn('slug');
        });
    }
}

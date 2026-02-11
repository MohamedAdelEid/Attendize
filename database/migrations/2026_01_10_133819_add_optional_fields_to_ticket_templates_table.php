<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionalFieldsToTicketTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            // UserType fields
            $table->boolean('show_user_type')->default(false)->after('qr_size');
            $table->string('user_type_position_x')->nullable()->after('show_user_type');
            $table->string('user_type_position_y')->nullable()->after('user_type_position_x');
            $table->string('user_type_font_size')->nullable()->after('user_type_position_y');
            $table->string('user_type_font_color')->nullable()->after('user_type_font_size');

            // Profession fields
            $table->boolean('show_profession')->default(false)->after('user_type_font_color');
            $table->string('profession_position_x')->nullable()->after('show_profession');
            $table->string('profession_position_y')->nullable()->after('profession_position_x');
            $table->string('profession_font_size')->nullable()->after('profession_position_y');
            $table->string('profession_font_color')->nullable()->after('profession_font_size');

            // Category fields
            $table->boolean('show_category')->default(false)->after('profession_font_color');
            $table->string('category_position_x')->nullable()->after('show_category');
            $table->string('category_position_y')->nullable()->after('category_position_x');
            $table->string('category_font_size')->nullable()->after('category_position_y');
            $table->string('category_font_color')->nullable()->after('category_font_size');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            // Drop UserType fields
            $table->dropColumn([
                'show_user_type',
                'user_type_position_x',
                'user_type_position_y',
                'user_type_font_size',
                'user_type_font_color'
            ]);

            // Drop Profession fields
            $table->dropColumn([
                'show_profession',
                'profession_position_x',
                'profession_position_y',
                'profession_font_size',
                'profession_font_color'
            ]);

            // Drop Category fields
            $table->dropColumn([
                'show_category',
                'category_position_x',
                'category_position_y',
                'category_font_size',
                'category_font_color'
            ]);
        });
    }
}

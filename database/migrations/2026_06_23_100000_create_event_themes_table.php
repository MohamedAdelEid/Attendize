<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventThemesTable extends Migration
{
    public function up()
    {
        Schema::create('event_themes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->string('hero_bg_type')->default('image');
            $table->string('hero_bg_image_path')->nullable();
            $table->string('hero_bg_video_url')->nullable();
            $table->string('hero_badge_text')->nullable();
            $table->text('hero_title')->nullable();
            $table->text('hero_title_secondary')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->text('hero_buttons')->nullable();
            $table->text('hero_target_audience')->nullable();

            $table->string('logo_path')->nullable();
            $table->string('secondary_logo_path')->nullable();
            $table->string('favicon_path')->nullable();

            $table->string('color_background')->default('220 60% 8%');
            $table->string('color_foreground')->default('0 0% 98%');
            $table->string('color_primary')->default('45 70% 50%');
            $table->string('color_secondary')->default('220 50% 18%');
            $table->string('color_accent')->default('45 65% 55%');
            $table->string('color_muted')->default('220 45% 20%');
            $table->string('color_border')->default('220 40% 25%');

            $table->string('font_family')->default('Inter');
            $table->string('heading_font_family')->default('Playfair Display');
            $table->string('section_spacing')->default('default');

            $table->string('decorative_pattern_url')->nullable();
            $table->text('custom_css')->nullable();

            $table->string('og_site_name')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image_path')->nullable();

            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_themes');
    }
}

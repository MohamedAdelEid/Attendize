<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventLandingPagesTable extends Migration
{
    public function up()
    {
        Schema::create('event_landing_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id')->unique();
            $table->unsignedInteger('theme_id')->nullable();

            $table->boolean('section_hero_enabled')->default(true);
            $table->boolean('section_pricing_enabled')->default(true);
            $table->boolean('section_registration_enabled')->default(true);
            $table->boolean('section_location_enabled')->default(true);
            $table->boolean('section_footer_enabled')->default(true);

            $table->string('hero_bg_type')->nullable();
            $table->string('hero_bg_image_path')->nullable();
            $table->string('hero_bg_video_url')->nullable();
            $table->string('hero_badge_text')->nullable();
            $table->text('hero_title')->nullable();
            $table->text('hero_title_secondary')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->text('hero_buttons')->nullable();
            $table->text('hero_target_audience')->nullable();
            $table->string('hero_date_time_text')->nullable();
            $table->string('hero_venue_text')->nullable();

            $table->string('logo_path')->nullable();
            $table->string('secondary_logo_path')->nullable();

            $table->string('pricing_title')->nullable();
            $table->text('pricing_description')->nullable();
            $table->text('pricing_footer_note')->nullable();

            $table->string('registration_title')->nullable();
            $table->text('registration_description')->nullable();

            $table->string('location_title')->nullable();
            $table->string('location_venue_name')->nullable();
            $table->text('location_address')->nullable();
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_long', 10, 7)->nullable();
            $table->text('location_map_embed_url')->nullable();
            $table->string('location_google_maps_url')->nullable();
            $table->string('location_directions_url')->nullable();
            $table->string('location_whatsapp_url')->nullable();
            $table->string('location_phone')->nullable();
            $table->text('location_notes')->nullable();
            $table->string('location_date_time_text')->nullable();

            $table->string('footer_logo_path')->nullable();
            $table->text('footer_description')->nullable();
            $table->string('footer_email')->nullable();
            $table->string('footer_phone')->nullable();
            $table->string('footer_website_url')->nullable();
            $table->string('footer_location_text')->nullable();
            $table->text('footer_copyright')->nullable();
            $table->text('footer_social_links')->nullable();
            $table->text('footer_nav_links')->nullable();

            $table->text('header_nav_links')->nullable();

            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('theme_id')->references('id')->on('event_themes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_landing_pages');
    }
}

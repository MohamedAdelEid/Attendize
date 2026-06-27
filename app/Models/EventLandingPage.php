<?php

namespace App\Models;

class EventLandingPage extends MyBaseModel
{
    protected $fillable = [
        'event_id',
        'theme_id',
        'section_hero_enabled',
        'section_pricing_enabled',
        'section_registration_enabled',
        'section_location_enabled',
        'section_footer_enabled',
        'section_header_enabled',
        'hero_bg_type',
        'hero_bg_image_path',
        'hero_bg_video_url',
        'hero_badge_text',
        'hero_title',
        'hero_title_secondary',
        'hero_subtitle',
        'hero_buttons',
        'hero_target_audience',
        'hero_date_time_text',
        'hero_venue_text',
        'logo_path',
        'secondary_logo_path',
        'pricing_title',
        'pricing_description',
        'pricing_footer_note',
        'registration_title',
        'registration_description',
        'location_title',
        'location_venue_name',
        'location_address',
        'location_lat',
        'location_long',
        'location_map_embed_url',
        'location_google_maps_url',
        'location_directions_url',
        'location_whatsapp_url',
        'location_phone',
        'location_notes',
        'location_date_time_text',
        'footer_logo_path',
        'footer_description',
        'footer_email',
        'footer_phone',
        'footer_website_url',
        'footer_location_text',
        'footer_copyright',
        'footer_social_links',
        'footer_nav_links',
        'header_nav_links',
    ];

    protected $casts = [
        'section_hero_enabled' => 'boolean',
        'section_pricing_enabled' => 'boolean',
        'section_registration_enabled' => 'boolean',
        'section_location_enabled' => 'boolean',
        'section_footer_enabled' => 'boolean',
        'section_header_enabled' => 'boolean',
        'location_lat' => 'float',
        'location_long' => 'float',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function theme()
    {
        return $this->belongsTo(EventTheme::class, 'theme_id');
    }

    public function getHeroButtonsAttribute($value)
    {
        return $this->decodeJsonArray($value);
    }

    public function setHeroButtonsAttribute($value)
    {
        $this->attributes['hero_buttons'] = $this->encodeJsonArray($value);
    }

    public function getHeroTargetAudienceAttribute($value)
    {
        return $this->decodeJsonArray($value);
    }

    public function setHeroTargetAudienceAttribute($value)
    {
        $this->attributes['hero_target_audience'] = $this->encodeJsonArray($value);
    }

    public function getFooterSocialLinksAttribute($value)
    {
        return $this->decodeJsonArray($value);
    }

    public function setFooterSocialLinksAttribute($value)
    {
        $this->attributes['footer_social_links'] = $this->encodeJsonArray($value);
    }

    public function getFooterNavLinksAttribute($value)
    {
        return $this->decodeJsonArray($value);
    }

    public function setFooterNavLinksAttribute($value)
    {
        $this->attributes['footer_nav_links'] = $this->encodeJsonArray($value);
    }

    public function getHeaderNavLinksAttribute($value)
    {
        return $this->decodeJsonArray($value);
    }

    public function setHeaderNavLinksAttribute($value)
    {
        $this->attributes['header_nav_links'] = $this->encodeJsonArray($value);
    }

    protected function decodeJsonArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function encodeJsonArray($value)
    {
        if (is_string($value)) {
            return $value;
        }

        return json_encode($value ?: []);
    }
}

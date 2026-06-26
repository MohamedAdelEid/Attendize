<?php

namespace App\Models;

class EventTheme extends MyBaseModel
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'hero_bg_type',
        'hero_bg_image_path',
        'hero_bg_video_url',
        'hero_badge_text',
        'hero_title',
        'hero_title_secondary',
        'hero_subtitle',
        'hero_buttons',
        'hero_target_audience',
        'logo_path',
        'secondary_logo_path',
        'favicon_path',
        'color_background',
        'color_foreground',
        'color_primary',
        'color_secondary',
        'color_accent',
        'color_muted',
        'color_border',
        'font_family',
        'heading_font_family',
        'section_spacing',
        'decorative_pattern_url',
        'custom_css',
        'og_site_name',
        'og_title',
        'og_description',
        'og_image_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function landingPages()
    {
        return $this->hasMany(EventLandingPage::class, 'theme_id');
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

    public function getLogoUrlAttribute()
    {
        return $this->resolveAssetUrl($this->logo_path);
    }

    public function getSecondaryLogoUrlAttribute()
    {
        return $this->resolveAssetUrl($this->secondary_logo_path);
    }

    public function getHeroBgImageUrlAttribute()
    {
        return $this->resolveAssetUrl($this->hero_bg_image_path);
    }

    public function getFaviconUrlAttribute()
    {
        return $this->resolveAssetUrl($this->favicon_path);
    }

    public function getOgImageUrlAttribute()
    {
        return $this->resolveAssetUrl($this->og_image_path);
    }

    protected function resolveAssetUrl($path)
    {
        if (empty($path)) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        return asset(ltrim($path, '/'));
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

<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventLandingPage;
use App\Models\EventTheme;
use App\Models\Registration;
use Carbon\Carbon;

class EventLandingPageService
{
    /**
     * Build the fully resolved landing page configuration for an event.
     */
    public function resolve(Event $event): array
    {
        $landingPage = $event->landingPage;
        $theme = $landingPage && $landingPage->theme_id
            ? $landingPage->theme
            : null;

        return [
            'theme' => $this->resolveTheme($event, $landingPage, $theme),
            'hero' => $this->resolveHero($event, $landingPage, $theme),
            'pricing' => $this->resolvePricing($event, $landingPage),
            'registration' => $this->resolveRegistration($event, $landingPage),
            'location' => $this->resolveLocation($event, $landingPage),
            'footer' => $this->resolveFooter($event, $landingPage, $theme),
            'header' => $this->resolveHeader($event, $landingPage, $theme),
        ];
    }

    /**
     * Get or create the landing page record for an event.
     */
    public function getOrCreateLandingPage(Event $event): EventLandingPage
    {
        $landingPage = $event->landingPage;

        if ($landingPage) {
            return $landingPage;
        }

        return EventLandingPage::create([
            'event_id' => $event->id,
        ]);
    }

    public function buildFeesCards(Event $event): array
    {
        $registrations = Registration::where('event_id', $event->id)
            ->where('status', 'active')
            ->where('show_on_fees_section', true)
            ->with(['category.conferences' => function ($q) {
                $q->where('status', 'active')->with('professions');
            }])
            ->orderBy('fees_display_order')
            ->orderBy('id')
            ->get();

        if ($registrations->isEmpty()) {
            $registrations = Registration::where('event_id', $event->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->where('show_on_landing', true)
                        ->orWhere('is_members_form', true)
                        ->orWhere('is_virtual_form', true);
                })
                ->with(['category.conferences' => function ($q) {
                    $q->where('status', 'active')->with('professions');
                }])
                ->orderBy('fees_display_order')
                ->orderBy('id')
                ->get();
        }

        $currency = $event->currency ? ($event->currency->code ?? 'SAR') : 'SAR';
        $cards = [];

        foreach ($registrations as $registration) {
            if (!$registration->category) {
                continue;
            }

            $tiers = [];
            $priceGroups = [];

            foreach ($registration->category->conferences as $conference) {
                $price = (float) $conference->getPriceForCategory($registration->category_id);
                $priceKey = number_format($price, 2, '.', '');

                $professionNames = $conference->professions->pluck('name')->filter()->values();
                $labels = $professionNames->isNotEmpty()
                    ? $professionNames->all()
                    : [$conference->name];

                if (!isset($priceGroups[$priceKey])) {
                    $priceGroups[$priceKey] = [
                        'labels' => [],
                        'price' => $price,
                    ];
                }

                $priceGroups[$priceKey]['labels'] = array_merge($priceGroups[$priceKey]['labels'], $labels);
            }

            krsort($priceGroups, SORT_NUMERIC);

            foreach ($priceGroups as $group) {
                $uniqueLabels = array_values(array_unique(array_filter($group['labels'])));
                $tiers[] = [
                    'label' => !empty($uniqueLabels) ? implode(', ', $uniqueLabels) : 'Registration',
                    'price' => $group['price'],
                    'price_formatted' => number_format($group['price'], 0),
                    'currency' => $currency,
                ];
            }

            if (empty($tiers)) {
                continue;
            }

            $cards[] = [
                'registration_id' => $registration->id,
                'title' => $registration->category->name,
                'description' => $registration->category->description,
                'badge' => $registration->fees_card_badge,
                'is_highlighted' => !empty($registration->fees_card_badge),
                'is_virtual' => (bool) $registration->is_virtual_form,
                'tiers' => $tiers,
            ];
        }

        return $cards;
    }

    protected function resolveTheme(Event $event, ?EventLandingPage $landingPage, ?EventTheme $theme): array
    {
        return [
            'colors' => [
                'background' => $this->pick($landingPage, $theme, 'color_background', '220 60% 8%'),
                'foreground' => $this->pick($landingPage, $theme, 'color_foreground', '0 0% 98%'),
                'primary' => $this->pick($landingPage, $theme, 'color_primary', '45 70% 50%'),
                'secondary' => $this->pick($landingPage, $theme, 'color_secondary', '220 50% 18%'),
                'accent' => $this->pick($landingPage, $theme, 'color_accent', '45 65% 55%'),
                'muted' => $this->pick($landingPage, $theme, 'color_muted', '220 45% 20%'),
                'border' => $this->pick($landingPage, $theme, 'color_border', '220 40% 25%'),
            ],
            'fonts' => [
                'body' => $this->pick($landingPage, $theme, 'font_family', 'Inter'),
                'heading' => $this->pick($landingPage, $theme, 'heading_font_family', 'Playfair Display'),
            ],
            'section_spacing' => $this->pick($landingPage, $theme, 'section_spacing', 'default'),
            'decorative_pattern_url' => $this->pick($landingPage, $theme, 'decorative_pattern_url'),
            'custom_css' => $this->pick($landingPage, $theme, 'custom_css'),
            'og' => [
                'site_name' => $this->pick($landingPage, $theme, 'og_site_name', $event->title),
                'title' => $this->pick($landingPage, $theme, 'og_title', $event->title),
                'description' => $this->pick($landingPage, $theme, 'og_description', strip_tags($event->description ?? '')),
                'image_url' => $this->assetUrl(
                    $this->pick($landingPage, $theme, 'og_image_path')
                    ?: ($event->bg_image_path ?? null)
                ),
            ],
            'favicon_url' => $this->assetUrl($this->pick($landingPage, $theme, 'favicon_path')),
        ];
    }

    protected function resolveHero(Event $event, ?EventLandingPage $landingPage, ?EventTheme $theme): array
    {
        $enabled = $landingPage ? $landingPage->section_hero_enabled : true;
        $bgType = $this->pick($landingPage, $theme, 'hero_bg_type', 'image');
        $bgImage = $this->pick($landingPage, $theme, 'hero_bg_image_path')
            ?: $event->bg_image_path
            ?: 'assets/images/hero-bg.jpg';

        $buttons = $this->pick($landingPage, $theme, 'hero_buttons', []);
        if (empty($buttons)) {
            $buttons = [[
                'text' => 'Register Now',
                'url' => '#registration',
                'visible' => true,
                'style' => 'primary',
                'scroll_target' => 'registration',
            ]];
        }

        $audience = $this->pick($landingPage, $theme, 'hero_target_audience', []);

        return [
            'enabled' => $enabled,
            'bg_type' => $bgType,
            'bg_image_url' => $this->assetUrl($bgImage),
            'bg_video_url' => $this->pick($landingPage, $theme, 'hero_bg_video_url'),
            'badge_text' => $this->pick($landingPage, $theme, 'hero_badge_text'),
            'title' => $this->pick($landingPage, $theme, 'hero_title', $event->title),
            'title_secondary' => $this->pick($landingPage, $theme, 'hero_title_secondary'),
            'subtitle' => $this->pick($landingPage, $theme, 'hero_subtitle', strip_tags($event->description ?? '')),
            'date_time_text' => $this->pick($landingPage, $theme, 'hero_date_time_text')
                ?: $this->formatEventDateTime($event),
            'venue_text' => $this->pick($landingPage, $theme, 'hero_venue_text')
                ?: ($event->venue_name ?: $event->venue_name_full),
            'buttons' => array_values(array_filter($buttons, function ($btn) {
                return !isset($btn['visible']) || $btn['visible'];
            })),
            'target_audience' => $audience,
        ];
    }

    protected function resolvePricing(Event $event, ?EventLandingPage $landingPage): array
    {
        return [
            'enabled' => $landingPage ? $landingPage->section_pricing_enabled : true,
            'title' => $landingPage && $landingPage->pricing_title
                ? $landingPage->pricing_title
                : 'Registration Fees',
            'description' => $landingPage && $landingPage->pricing_description
                ? $landingPage->pricing_description
                : 'Choose your registration category.',
            'footer_note' => $landingPage ? $landingPage->pricing_footer_note : null,
            'cards' => $this->buildFeesCards($event),
        ];
    }

    protected function resolveRegistration(Event $event, ?EventLandingPage $landingPage): array
    {
        return [
            'enabled' => $landingPage ? $landingPage->section_registration_enabled : true,
            'title' => $landingPage && $landingPage->registration_title
                ? $landingPage->registration_title
                : 'Registration',
            'description' => $landingPage && $landingPage->registration_description
                ? $landingPage->registration_description
                : 'Register now to attend ' . $event->title,
        ];
    }

    protected function resolveLocation(Event $event, ?EventLandingPage $landingPage): array
    {
        $venue = ($landingPage && $landingPage->location_venue_name)
            ? $landingPage->location_venue_name
            : ($event->venue_name ?: $event->venue_name_full);

        $address = ($landingPage && $landingPage->location_address)
            ? $landingPage->location_address
            : $event->map_address;

        $lat = ($landingPage && $landingPage->location_lat)
            ? $landingPage->location_lat
            : $event->location_lat;

        $lng = ($landingPage && $landingPage->location_long)
            ? $landingPage->location_long
            : $event->location_long;

        $mapEmbed = ($landingPage && $landingPage->location_map_embed_url)
            ? $landingPage->location_map_embed_url
            : $this->buildMapEmbedUrl($lat, $lng, $venue);

        return [
            'enabled' => $landingPage ? $landingPage->section_location_enabled : true,
            'title' => ($landingPage && $landingPage->location_title)
                ? $landingPage->location_title
                : 'Venue Location',
            'venue_name' => $venue,
            'address' => $address,
            'lat' => $lat,
            'long' => $lng,
            'map_embed_url' => $mapEmbed,
            'google_maps_url' => $landingPage ? $landingPage->location_google_maps_url : null,
            'directions_url' => $landingPage ? $landingPage->location_directions_url : null,
            'whatsapp_url' => $landingPage ? $landingPage->location_whatsapp_url : null,
            'phone' => $landingPage ? $landingPage->location_phone : null,
            'notes' => $landingPage ? $landingPage->location_notes : null,
            'date_time_text' => ($landingPage && $landingPage->location_date_time_text)
                ? $landingPage->location_date_time_text
                : $this->formatEventDateTime($event),
        ];
    }

    protected function resolveFooter(Event $event, ?EventLandingPage $landingPage, ?EventTheme $theme): array
    {
        $organiser = $event->organiser;

        return [
            'enabled' => $landingPage ? $landingPage->section_footer_enabled : true,
            'logo_url' => $this->assetUrl(
                ($landingPage && $landingPage->footer_logo_path)
                    ? $landingPage->footer_logo_path
                    : $this->pick($landingPage, $theme, 'logo_path')
            ),
            'description' => ($landingPage && $landingPage->footer_description)
                ? $landingPage->footer_description
                : strip_tags($event->description ?? ''),
            'email' => ($landingPage && $landingPage->footer_email)
                ? $landingPage->footer_email
                : ($organiser ? $organiser->email : null),
            'phone' => $landingPage ? $landingPage->footer_phone : null,
            'website_url' => $landingPage ? $landingPage->footer_website_url : null,
            'location_text' => ($landingPage && $landingPage->footer_location_text)
                ? $landingPage->footer_location_text
                : $event->location,
            'copyright' => ($landingPage && $landingPage->footer_copyright)
                ? $landingPage->footer_copyright
                : '© ' . date('Y') . ' ' . ($organiser ? $organiser->name : $event->title) . '. All Rights Reserved.',
            'social_links' => ($landingPage && !empty($landingPage->footer_social_links))
                ? $landingPage->footer_social_links
                : [],
            'nav_links' => ($landingPage && !empty($landingPage->footer_nav_links))
                ? $landingPage->footer_nav_links
                : [],
        ];
    }

    protected function resolveHeader(Event $event, ?EventLandingPage $landingPage, ?EventTheme $theme): array
    {
        return [
            'logo_url' => $this->assetUrl(
                ($landingPage && $landingPage->logo_path)
                    ? $landingPage->logo_path
                    : $this->pick($landingPage, $theme, 'logo_path')
            ),
            'secondary_logo_url' => $this->assetUrl(
                ($landingPage && $landingPage->secondary_logo_path)
                    ? $landingPage->secondary_logo_path
                    : $this->pick($landingPage, $theme, 'secondary_logo_path')
            ),
            'nav_links' => ($landingPage && !empty($landingPage->header_nav_links))
                ? $landingPage->header_nav_links
                : [],
        ];
    }

    protected function pick(?EventLandingPage $landingPage, ?EventTheme $theme, string $key, $default = null)
    {
        if ($landingPage && isset($landingPage->$key) && $landingPage->$key !== null && $landingPage->$key !== '') {
            return $landingPage->$key;
        }

        if ($theme && isset($theme->$key) && $theme->$key !== null && $theme->$key !== '') {
            return $theme->$key;
        }

        return $default;
    }

    protected function assetUrl($path)
    {
        if (empty($path)) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    protected function formatEventDateTime(Event $event): ?string
    {
        if (!$event->start_date) {
            return null;
        }

        $start = Carbon::parse($event->start_date);
        $text = $start->format('F j, Y');

        if ($event->end_date) {
            $end = Carbon::parse($event->end_date);
            if ($start->isSameDay($end)) {
                $text .= ' ' . $start->format('g:i a') . ' - ' . $end->format('g:i a');
            } else {
                $text .= ' - ' . $end->format('F j, Y g:i a');
            }
        }

        return $text;
    }

    protected function buildMapEmbedUrl($lat, $lng, $venue): ?string
    {
        if ($lat && $lng) {
            return 'https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d3000!2d' . $lng . '!3d' . $lat . '!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2s!4v1!5m2!1sen!2s';
        }

        if ($venue) {
            return 'https://www.google.com/maps?q=' . urlencode($venue) . '&output=embed';
        }

        return null;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTheme;
use App\Services\EventLandingPageService;
use Illuminate\Http\Request;
use Image;
use Validator;

class EventLandingPageController extends MyBaseController
{
    protected $landingPageService;

    public function __construct(EventLandingPageService $landingPageService)
    {
        $this->landingPageService = $landingPageService;
    }

    public function show($event_id, $tab = 'general')
    {
        $event = Event::scope()->findOrFail($event_id);
        $landingPage = $this->landingPageService->getOrCreateLandingPage($event);
        $landingPage->load('theme');
        $themes = EventTheme::where('account_id', $event->account_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $resolved = $this->landingPageService->resolve($event);

        return view('ManageEvent.LandingPage.Settings', compact(
            'event',
            'landingPage',
            'themes',
            'resolved',
            'tab'
        ));
    }

    public function update(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $landingPage = $this->landingPageService->getOrCreateLandingPage($event);

        $validator = Validator::make($request->all(), [
            'theme_id' => 'nullable|exists:event_themes,id',
            'logo' => 'nullable|image|max:4000',
            'secondary_logo' => 'nullable|image|max:4000',
            'hero_bg_image' => 'nullable|image|max:8000',
            'footer_logo' => 'nullable|image|max:4000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $section = $request->input('section', 'general');

        if ($section === 'general') {
            $landingPage->theme_id = $request->input('theme_id') ?: null;
            $landingPage->section_hero_enabled = $request->boolean('section_hero_enabled');
            $landingPage->section_pricing_enabled = $request->boolean('section_pricing_enabled');
            $landingPage->section_registration_enabled = $request->boolean('section_registration_enabled');
            $landingPage->section_location_enabled = $request->boolean('section_location_enabled');
            $landingPage->section_footer_enabled = $request->boolean('section_footer_enabled');
            $landingPage->section_header_enabled = $request->boolean('section_header_enabled');
        }

        if ($section === 'hero' || $section === 'general') {
            $landingPage->hero_bg_type = $request->input('hero_bg_type');
            $landingPage->hero_bg_video_url = $request->input('hero_bg_video_url');
            $landingPage->hero_badge_text = $request->input('hero_badge_text');
            $landingPage->hero_title = $request->input('hero_title');
            $landingPage->hero_title_secondary = $request->input('hero_title_secondary');
            $landingPage->hero_subtitle = $request->input('hero_subtitle');
            $landingPage->hero_date_time_text = $request->input('hero_date_time_text');
            $landingPage->hero_venue_text = $request->input('hero_venue_text');
            $landingPage->hero_buttons = $this->parseHeroButtons($request);
            $landingPage->hero_target_audience = $this->parseLines($request->input('hero_target_audience'));
        }

        if ($section === 'pricing' || $section === 'general') {
            $landingPage->pricing_title = $request->input('pricing_title');
            $landingPage->pricing_description = $request->input('pricing_description');
            $landingPage->pricing_footer_note = $request->input('pricing_footer_note');
        }

        if ($section === 'registration' || $section === 'general') {
            $landingPage->registration_title = $request->input('registration_title');
            $landingPage->registration_description = $request->input('registration_description');
        }

        if ($section === 'location' || $section === 'general') {
            $landingPage->location_title = $request->input('location_title');
            $landingPage->location_venue_name = $request->input('location_venue_name');
            $landingPage->location_address = $request->input('location_address');
            $landingPage->location_lat = $request->input('location_lat');
            $landingPage->location_long = $request->input('location_long');
            $landingPage->location_map_embed_url = $request->input('location_map_embed_url');
            $landingPage->location_google_maps_url = $request->input('location_google_maps_url');
            $landingPage->location_directions_url = $request->input('location_directions_url');
            $landingPage->location_whatsapp_url = $request->input('location_whatsapp_url');
            $landingPage->location_phone = $request->input('location_phone');
            $landingPage->location_notes = $request->input('location_notes');
            $landingPage->location_date_time_text = $request->input('location_date_time_text');
        }

        if ($section === 'footer' || $section === 'general') {
            $landingPage->footer_description = $request->input('footer_description');
            $landingPage->footer_email = $request->input('footer_email');
            $landingPage->footer_phone = $request->input('footer_phone');
            $landingPage->footer_website_url = $request->input('footer_website_url');
            $landingPage->footer_location_text = $request->input('footer_location_text');
            $landingPage->footer_copyright = $request->input('footer_copyright');
            $landingPage->footer_social_links = $this->parseSocialLinks($request);
            $landingPage->footer_nav_links = $this->parseNavLinks($request, 'footer_nav');
            $landingPage->header_nav_links = $this->parseNavLinks($request, 'header_nav');
        }

        $this->handleImageUpload($request, 'logo', $landingPage, 'logo_path', 'landing_logo');
        $this->handleImageUpload($request, 'secondary_logo', $landingPage, 'secondary_logo_path', 'landing_secondary_logo');
        $this->handleImageUpload($request, 'hero_bg_image', $landingPage, 'hero_bg_image_path', 'landing_hero_bg');
        $this->handleImageUpload($request, 'footer_logo', $landingPage, 'footer_logo_path', 'landing_footer_logo');

        $landingPage->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Landing page settings saved successfully.',
        ]);
    }

    protected function parseHeroButtons(Request $request)
    {
        $texts = $request->input('hero_button_text', []);
        $urls = $request->input('hero_button_url', []);
        $visible = $request->input('hero_button_visible', []);
        $styles = $request->input('hero_button_style', []);
        $scrollTargets = $request->input('hero_button_scroll', []);

        $buttons = [];
        foreach ($texts as $i => $text) {
            $text = trim($text);
            if ($text === '') {
                continue;
            }
            $buttons[] = [
                'text' => $text,
                'url' => $urls[$i] ?? '#registration',
                'visible' => isset($visible[$i]),
                'style' => $styles[$i] ?? 'primary',
                'scroll_target' => $scrollTargets[$i] ?? null,
            ];
        }

        return $buttons;
    }

    protected function parseSocialLinks(Request $request)
    {
        $platforms = $request->input('social_platform', []);
        $urls = $request->input('social_url', []);
        $links = [];

        foreach ($platforms as $i => $platform) {
            $platform = trim($platform);
            $url = trim($urls[$i] ?? '');
            if ($platform && $url) {
                $links[] = ['platform' => $platform, 'url' => $url];
            }
        }

        return $links;
    }

    protected function parseNavLinks(Request $request, $prefix)
    {
        $labels = $request->input($prefix . '_label', []);
        $urls = $request->input($prefix . '_url', []);
        $links = [];

        foreach ($labels as $i => $label) {
            $label = trim($label);
            $url = trim($urls[$i] ?? '');
            if ($label && $url) {
                $links[] = ['label' => $label, 'url' => $url];
            }
        }

        return $links;
    }

    protected function parseLines($value)
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (empty($value)) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $value);

        return array_values(array_filter(array_map('trim', $lines)));
    }

    protected function handleImageUpload(Request $request, $field, $model, $attribute, $prefix)
    {
        if (!$request->hasFile($field)) {
            return;
        }

        $path = public_path() . '/' . config('attendize.event_images_path');
        $filename = $prefix . '-' . md5($model->id ?: uniqid()) . '.' . strtolower($request->file($field)->getClientOriginalExtension());
        $fileFullPath = $path . '/' . $filename;

        $request->file($field)->move($path, $filename);

        $img = Image::make($fileFullPath);
        $img->resize(2000, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($fileFullPath, 80);

        $model->$attribute = config('attendize.event_images_path') . '/' . $filename;
        \Storage::put(config('attendize.event_images_path') . '/' . $filename, file_get_contents($fileFullPath));
    }
}

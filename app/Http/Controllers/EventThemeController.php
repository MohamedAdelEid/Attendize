<?php

namespace App\Http\Controllers;

use App\Models\EventTheme;
use App\Services\EventLandingPageService;
use Illuminate\Http\Request;
use Image;
use Validator;

class EventThemeController extends MyBaseController
{
    protected $landingPageService;

    public function __construct(EventLandingPageService $landingPageService)
    {
        $this->landingPageService = $landingPageService;
    }

    public function index($event_id)
    {
        $event = $this->getEvent($event_id);
        $themes = EventTheme::where('account_id', $event->account_id)
            ->orderBy('name')
            ->get();

        return view('ManageEvent.LandingPage.Themes', compact('event', 'themes'));
    }

    public function create($event_id)
    {
        $event = $this->getEvent($event_id);
        $theme = new EventTheme();

        return view('ManageEvent.LandingPage.Modals.CreateTheme', compact('event', 'theme'));
    }

    public function store(Request $request, $event_id)
    {
        $event = $this->getEvent($event_id);
        $validator = $this->validateTheme($request);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $theme = new EventTheme();
        $theme->account_id = $event->account_id;
        $theme->user_id = auth()->id();
        $this->fillThemeFromRequest($theme, $request);
        $theme->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Theme created successfully.',
            'id' => $theme->id,
        ]);
    }

    public function edit($event_id, $theme_id)
    {
        $event = $this->getEvent($event_id);
        $theme = $this->getTheme($event, $theme_id);

        return view('ManageEvent.LandingPage.Modals.EditTheme', compact('event', 'theme'));
    }

    public function update(Request $request, $event_id, $theme_id)
    {
        $event = $this->getEvent($event_id);
        $theme = $this->getTheme($event, $theme_id);
        $validator = $this->validateTheme($request);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $this->fillThemeFromRequest($theme, $request);
        $theme->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Theme updated successfully.',
        ]);
    }

    public function destroy($event_id, $theme_id)
    {
        $event = $this->getEvent($event_id);
        $theme = $this->getTheme($event, $theme_id);

        if ($theme->landingPages()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This theme is in use by one or more events and cannot be deleted.',
            ]);
        }

        $theme->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Theme deleted successfully.',
        ]);
    }

    protected function getEvent($event_id)
    {
        return \App\Models\Event::scope()->findOrFail($event_id);
    }

    protected function getTheme($event, $theme_id)
    {
        return EventTheme::where('account_id', $event->account_id)->findOrFail($theme_id);
    }

    protected function validateTheme(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:255',
            'hero_bg_type' => 'nullable|in:image,video',
            'logo' => 'nullable|image|max:4000',
            'secondary_logo' => 'nullable|image|max:4000',
            'hero_bg_image' => 'nullable|image|max:8000',
            'favicon' => 'nullable|image|max:1000',
            'og_image' => 'nullable|image|max:4000',
        ]);
    }

    protected function fillThemeFromRequest(EventTheme $theme, Request $request)
    {
        $theme->name = $request->input('name');
        $theme->description = $request->input('description');
        $theme->is_active = $request->boolean('is_active', true);

        $theme->hero_bg_type = $request->input('hero_bg_type', 'image');
        $theme->hero_bg_video_url = $request->input('hero_bg_video_url');
        $theme->hero_badge_text = $request->input('hero_badge_text');
        $theme->hero_title = $request->input('hero_title');
        $theme->hero_title_secondary = $request->input('hero_title_secondary');
        $theme->hero_subtitle = $request->input('hero_subtitle');
        $theme->hero_buttons = $this->parseJsonField($request->input('hero_buttons'));
        $theme->hero_target_audience = $this->parseLinesField($request->input('hero_target_audience'));

        $theme->color_background = $request->input('color_background', '220 60% 8%');
        $theme->color_foreground = $request->input('color_foreground', '0 0% 98%');
        $theme->color_primary = $request->input('color_primary', '45 70% 50%');
        $theme->color_secondary = $request->input('color_secondary', '220 50% 18%');
        $theme->color_accent = $request->input('color_accent', '45 65% 55%');
        $theme->color_muted = $request->input('color_muted', '220 45% 20%');
        $theme->color_border = $request->input('color_border', '220 40% 25%');

        $theme->font_family = $request->input('font_family', 'Inter');
        $theme->heading_font_family = $request->input('heading_font_family', 'Playfair Display');
        $theme->section_spacing = $request->input('section_spacing', 'default');
        $theme->decorative_pattern_url = $request->input('decorative_pattern_url');
        $theme->custom_css = $request->input('custom_css');

        $theme->og_site_name = $request->input('og_site_name');
        $theme->og_title = $request->input('og_title');
        $theme->og_description = $request->input('og_description');

        $this->handleImageUpload($request, 'logo', $theme, 'logo_path', 'theme_logo');
        $this->handleImageUpload($request, 'secondary_logo', $theme, 'secondary_logo_path', 'theme_secondary_logo');
        $this->handleImageUpload($request, 'hero_bg_image', $theme, 'hero_bg_image_path', 'theme_hero_bg');
        $this->handleImageUpload($request, 'favicon', $theme, 'favicon_path', 'theme_favicon');
        $this->handleImageUpload($request, 'og_image', $theme, 'og_image_path', 'theme_og');
    }

    protected function parseJsonField($value)
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

    protected function parseLinesField($value)
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

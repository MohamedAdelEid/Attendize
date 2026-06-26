<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Name *</label>
            <input type="text" name="name" class="form-control" value="{{ $theme->name ?? '' }}" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="2">{{ $theme->description ?? '' }}</textarea>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" {{ !isset($theme->id) || $theme->is_active ? 'checked' : '' }}> Active</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Logo</label>
            @if(!empty($theme->logo_path))
                <p><img src="{{ asset($theme->logo_path) }}" style="max-height:50px"></p>
            @endif
            <input type="file" name="logo" class="form-control" accept="image/*">
        </div>
        <div class="form-group">
            <label>Secondary Logo</label>
            @if(!empty($theme->secondary_logo_path))
                <p><img src="{{ asset($theme->secondary_logo_path) }}" style="max-height:50px"></p>
            @endif
            <input type="file" name="secondary_logo" class="form-control" accept="image/*">
        </div>
    </div>
</div>

<h5>Colors (HSL format: e.g. "45 70% 50%")</h5>
<div class="row">
    <div class="col-md-4"><div class="form-group"><label>Primary</label><input type="text" name="color_primary" class="form-control" value="{{ $theme->color_primary ?? '45 70% 50%' }}"></div></div>
    <div class="col-md-4"><div class="form-group"><label>Secondary</label><input type="text" name="color_secondary" class="form-control" value="{{ $theme->color_secondary ?? '220 50% 18%' }}"></div></div>
    <div class="col-md-4"><div class="form-group"><label>Accent</label><input type="text" name="color_accent" class="form-control" value="{{ $theme->color_accent ?? '45 65% 55%' }}"></div></div>
    <div class="col-md-4"><div class="form-group"><label>Background</label><input type="text" name="color_background" class="form-control" value="{{ $theme->color_background ?? '220 60% 8%' }}"></div></div>
    <div class="col-md-4"><div class="form-group"><label>Foreground</label><input type="text" name="color_foreground" class="form-control" value="{{ $theme->color_foreground ?? '0 0% 98%' }}"></div></div>
    <div class="col-md-4"><div class="form-group"><label>Border</label><input type="text" name="color_border" class="form-control" value="{{ $theme->color_border ?? '220 40% 25%' }}"></div></div>
</div>

<h5>Typography</h5>
<div class="row">
    <div class="col-md-6"><div class="form-group"><label>Body Font</label><input type="text" name="font_family" class="form-control" value="{{ $theme->font_family ?? 'Inter' }}"></div></div>
    <div class="col-md-6"><div class="form-group"><label>Heading Font</label><input type="text" name="heading_font_family" class="form-control" value="{{ $theme->heading_font_family ?? 'Playfair Display' }}"></div></div>
</div>

<h5>Hero Defaults</h5>
<div class="form-group">
    <label>Hero Background Image</label>
    @if(!empty($theme->hero_bg_image_path))
        <p><img src="{{ asset($theme->hero_bg_image_path) }}" style="max-height:80px"></p>
    @endif
    <input type="file" name="hero_bg_image" class="form-control" accept="image/*">
</div>
<div class="form-group">
    <label>Hero Badge Text</label>
    <input type="text" name="hero_badge_text" class="form-control" value="{{ $theme->hero_badge_text ?? '' }}">
</div>
<div class="form-group">
    <label>Hero Title</label>
    <textarea name="hero_title" class="form-control" rows="2">{{ $theme->hero_title ?? '' }}</textarea>
</div>
<div class="form-group">
    <label>Hero Subtitle</label>
    <textarea name="hero_subtitle" class="form-control" rows="2">{{ $theme->hero_subtitle ?? '' }}</textarea>
</div>

<div class="form-group">
    <label>Custom CSS</label>
    <textarea name="custom_css" class="form-control" rows="3" placeholder="Optional custom styles">{{ $theme->custom_css ?? '' }}</textarea>
</div>

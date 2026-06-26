<div class="tab-pane {{ $tab == 'hero' ? 'active' : '' }}" id="hero">
    {!! Form::open(['url' => route('postEventLandingPage', ['event_id' => $event->id]), 'class' => 'ajax', 'files' => true]) !!}
    <input type="hidden" name="section" value="hero">

    <div class="form-group">
        <label>Hero Background Type</label>
        <select name="hero_bg_type" class="form-control">
            <option value="image" {{ ($landingPage->hero_bg_type ?: 'image') == 'image' ? 'selected' : '' }}>Image</option>
            <option value="video" {{ $landingPage->hero_bg_type == 'video' ? 'selected' : '' }}>Video</option>
        </select>
    </div>

    <div class="form-group">
        <label>Hero Background Image</label>
        @if($landingPage->hero_bg_image_path)
            <p><img src="{{ asset($landingPage->hero_bg_image_path) }}" style="max-height:120px" alt="Hero BG"></p>
        @elseif($event->bg_image_path)
            <p class="text-muted">Falls back to event image: <img src="{{ asset($event->bg_image_path) }}" style="max-height:60px"></p>
        @endif
        <input type="file" name="hero_bg_image" class="form-control" accept="image/*">
    </div>

    <div class="form-group">
        <label>Hero Background Video URL</label>
        <input type="url" name="hero_bg_video_url" class="form-control" value="{{ $landingPage->hero_bg_video_url }}" placeholder="https://...">
    </div>

    <div class="form-group">
        <label>Badge Text</label>
        <input type="text" name="hero_badge_text" class="form-control" value="{{ $landingPage->hero_badge_text }}" placeholder="e.g. Specialized Scientific Seminar">
    </div>

    <div class="form-group">
        <label>Hero Title</label>
        <textarea name="hero_title" class="form-control" rows="2" placeholder="Leave empty to use event title">{{ $landingPage->hero_title }}</textarea>
    </div>

    <div class="form-group">
        <label>Hero Title (Secondary / Highlight)</label>
        <input type="text" name="hero_title_secondary" class="form-control" value="{{ $landingPage->hero_title_secondary }}">
    </div>

    <div class="form-group">
        <label>Hero Subtitle</label>
        <textarea name="hero_subtitle" class="form-control" rows="2">{{ $landingPage->hero_subtitle }}</textarea>
    </div>

    <div class="form-group">
        <label>Date & Time Text</label>
        <input type="text" name="hero_date_time_text" class="form-control" value="{{ $landingPage->hero_date_time_text }}" placeholder="Leave empty to auto-format from event dates">
    </div>

    <div class="form-group">
        <label>Venue Text</label>
        <input type="text" name="hero_venue_text" class="form-control" value="{{ $landingPage->hero_venue_text }}" placeholder="Leave empty to use event venue">
    </div>

    <div class="form-group">
        <label>Target Audience (one per line)</label>
        <textarea name="hero_target_audience" class="form-control" rows="4" placeholder="Consultants&#10;Specialists&#10;Residents">{{ is_array($landingPage->hero_target_audience) ? implode("\n", $landingPage->hero_target_audience) : '' }}</textarea>
    </div>

    <h5>Hero Buttons</h5>
    @php $heroButtons = $landingPage->hero_buttons ?: [['text' => 'Register Now', 'url' => '#registration', 'visible' => true, 'style' => 'primary', 'scroll_target' => 'registration']]; @endphp
    @foreach($heroButtons as $i => $btn)
    <div class="well well-sm">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="hero_button_text[]" class="form-control" value="{{ $btn['text'] ?? '' }}" placeholder="Button text">
            </div>
            <div class="col-md-3">
                <input type="text" name="hero_button_url[]" class="form-control" value="{{ $btn['url'] ?? '' }}" placeholder="URL or #section">
            </div>
            <div class="col-md-2">
                <input type="text" name="hero_button_scroll[]" class="form-control" value="{{ $btn['scroll_target'] ?? '' }}" placeholder="Scroll target ID">
            </div>
            <div class="col-md-2">
                <select name="hero_button_style[]" class="form-control">
                    <option value="primary" {{ ($btn['style'] ?? '') == 'primary' ? 'selected' : '' }}>Primary</option>
                    <option value="secondary" {{ ($btn['style'] ?? '') == 'secondary' ? 'selected' : '' }}>Secondary</option>
                </select>
            </div>
            <div class="col-md-2">
                <label><input type="checkbox" name="hero_button_visible[{{ $i }}]" value="1" {{ !isset($btn['visible']) || $btn['visible'] ? 'checked' : '' }}> Visible</label>
            </div>
        </div>
    </div>
    @endforeach
    <div class="well well-sm">
        <div class="row">
            <div class="col-md-3"><input type="text" name="hero_button_text[]" class="form-control" placeholder="New button text"></div>
            <div class="col-md-3"><input type="text" name="hero_button_url[]" class="form-control" placeholder="#registration"></div>
            <div class="col-md-2"><input type="text" name="hero_button_scroll[]" class="form-control" placeholder="registration"></div>
            <div class="col-md-2"><select name="hero_button_style[]" class="form-control"><option value="primary">Primary</option><option value="secondary">Secondary</option></select></div>
            <div class="col-md-2"><label><input type="checkbox" name="hero_button_visible[99]" value="1" checked> Visible</label></div>
        </div>
    </div>

    <button type="submit" class="btn btn-success">Save Hero Settings</button>
    {!! Form::close() !!}
</div>

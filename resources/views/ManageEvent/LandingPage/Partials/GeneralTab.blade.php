<div class="tab-pane {{ ($tab == 'general' || !$tab) ? 'active' : '' }}" id="general">
    {!! Form::open(['url' => route('postEventLandingPage', ['event_id' => $event->id]), 'class' => 'ajax', 'files' => true]) !!}
    <input type="hidden" name="section" value="general">

    <h4>Section Visibility</h4>
    <div class="row">
        <div class="col-md-4">
            <input type="hidden" name="section_hero_enabled" value="0">
            <label><input type="checkbox" name="section_hero_enabled" value="1" {{ $landingPage->section_hero_enabled ? 'checked' : '' }}> Hero</label>
        </div>
        <div class="col-md-4">
            <input type="hidden" name="section_pricing_enabled" value="0">
            <label><input type="checkbox" name="section_pricing_enabled" value="1" {{ $landingPage->section_pricing_enabled ? 'checked' : '' }}> Registration Fees</label>
        </div>
        <div class="col-md-4">
            <input type="hidden" name="section_registration_enabled" value="0">
            <label><input type="checkbox" name="section_registration_enabled" value="1" {{ $landingPage->section_registration_enabled ? 'checked' : '' }}> Registration Form</label>
        </div>
        <div class="col-md-4">
            <input type="hidden" name="section_location_enabled" value="0">
            <label><input type="checkbox" name="section_location_enabled" value="1" {{ $landingPage->section_location_enabled ? 'checked' : '' }}> Location</label>
        </div>
        <div class="col-md-4">
            <input type="hidden" name="section_footer_enabled" value="0">
            <label><input type="checkbox" name="section_footer_enabled" value="1" {{ $landingPage->section_footer_enabled ? 'checked' : '' }}> Footer</label>
        </div>
    </div>

    <hr>
    <h4>Theme</h4>
    <div class="form-group">
        <label>Select Theme</label>
        <select name="theme_id" class="form-control">
            <option value="">— No theme (use event overrides & defaults) —</option>
            @foreach($themes as $theme)
                <option value="{{ $theme->id }}" {{ $landingPage->theme_id == $theme->id ? 'selected' : '' }}>{{ $theme->name }}</option>
            @endforeach
        </select>
        <p class="help-block">Themes define colors, fonts, hero defaults, and logos. Event-specific overrides in other tabs take priority.</p>
    </div>

    <div class="form-group">
        <label>Header Logo</label>
        @if($landingPage->logo_path)
            <p><img src="{{ asset($landingPage->logo_path) }}" style="max-height:60px" alt="Logo"></p>
        @endif
        <input type="file" name="logo" class="form-control" accept="image/*">
    </div>

    <div class="form-group">
        <label>Secondary Logo</label>
        @if($landingPage->secondary_logo_path)
            <p><img src="{{ asset($landingPage->secondary_logo_path) }}" style="max-height:60px" alt="Secondary Logo"></p>
        @endif
        <input type="file" name="secondary_logo" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-success">Save General Settings</button>
    {!! Form::close() !!}
</div>

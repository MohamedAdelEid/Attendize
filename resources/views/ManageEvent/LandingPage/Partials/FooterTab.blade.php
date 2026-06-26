<div class="tab-pane {{ $tab == 'footer' ? 'active' : '' }}" id="footer">
  {!! Form::open(['url' => route('postEventLandingPage', ['event_id' => $event->id]), 'class' => 'ajax', 'files' => true]) !!}
  <input type="hidden" name="section" value="footer">

  <div class="form-group">
    <label>Footer Logo</label>
    @if($landingPage->footer_logo_path)
      <p><img src="{{ asset($landingPage->footer_logo_path) }}" style="max-height:60px" alt="Footer Logo"></p>
    @endif
    <input type="file" name="footer_logo" class="form-control" accept="image/*">
  </div>

  <div class="form-group">
    <label>Description</label>
    <textarea name="footer_description" class="form-control" rows="3">{{ $landingPage->footer_description }}</textarea>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="footer_email" class="form-control" value="{{ $landingPage->footer_email }}">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label>Phone</label>
        <input type="text" name="footer_phone" class="form-control" value="{{ $landingPage->footer_phone }}">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label>Website URL</label>
    <input type="url" name="footer_website_url" class="form-control" value="{{ $landingPage->footer_website_url }}">
  </div>

  <div class="form-group">
    <label>Location Text</label>
    <input type="text" name="footer_location_text" class="form-control" value="{{ $landingPage->footer_location_text }}">
  </div>

  <div class="form-group">
    <label>Copyright Text</label>
    <input type="text" name="footer_copyright" class="form-control" value="{{ $landingPage->footer_copyright }}" placeholder="Leave empty for auto-generated">
  </div>

  <h5>Social Media Links</h5>
  @php $socialLinks = $landingPage->footer_social_links ?: []; @endphp
  @foreach(array_merge($socialLinks, [['platform' => '', 'url' => '']]) as $i => $link)
  <div class="row" style="margin-bottom:8px">
    <div class="col-md-4"><input type="text" name="social_platform[]" class="form-control" value="{{ $link['platform'] ?? '' }}" placeholder="Platform (e.g. Twitter)"></div>
    <div class="col-md-8"><input type="url" name="social_url[]" class="form-control" value="{{ $link['url'] ?? '' }}" placeholder="https://..."></div>
  </div>
  @endforeach

  <h5>Footer Navigation Links</h5>
  @php $footerNav = $landingPage->footer_nav_links ?: []; @endphp
  @foreach(array_merge($footerNav, [['label' => '', 'url' => '']]) as $i => $link)
  <div class="row" style="margin-bottom:8px">
    <div class="col-md-4"><input type="text" name="footer_nav_label[]" class="form-control" value="{{ $link['label'] ?? '' }}" placeholder="Label"></div>
    <div class="col-md-8"><input type="text" name="footer_nav_url[]" class="form-control" value="{{ $link['url'] ?? '' }}" placeholder="URL"></div>
  </div>
  @endforeach

  <button type="submit" class="btn btn-success">Save Footer Settings</button>
  {!! Form::close() !!}
</div>

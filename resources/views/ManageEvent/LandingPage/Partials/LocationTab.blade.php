<div class="tab-pane {{ $tab == 'location' ? 'active' : '' }}" id="location">
  {!! Form::open(['url' => route('postEventLandingPage', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
  <input type="hidden" name="section" value="location">

  <div class="alert alert-info">
    Event location fields from General settings are used as fallbacks when left empty here.
    Current event venue: <strong>{{ $event->venue_name ?: $event->venue_name_full ?: '—' }}</strong>
  </div>

  <div class="form-group">
    <label>Section Title</label>
    <input type="text" name="location_title" class="form-control" value="{{ $landingPage->location_title }}" placeholder="Venue Location">
  </div>

  <div class="form-group">
    <label>Venue Name</label>
    <input type="text" name="location_venue_name" class="form-control" value="{{ $landingPage->location_venue_name }}">
  </div>

  <div class="form-group">
    <label>Address</label>
    <textarea name="location_address" class="form-control" rows="2">{{ $landingPage->location_address }}</textarea>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label>Latitude</label>
        <input type="text" name="location_lat" class="form-control" value="{{ $landingPage->location_lat ?: $event->location_lat }}">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label>Longitude</label>
        <input type="text" name="location_long" class="form-control" value="{{ $landingPage->location_long ?: $event->location_long }}">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label>Map Embed URL (iframe src)</label>
    <textarea name="location_map_embed_url" class="form-control" rows="2" placeholder="Google Maps embed URL">{{ $landingPage->location_map_embed_url }}</textarea>
  </div>

  <div class="form-group">
    <label>Google Maps URL</label>
    <input type="url" name="location_google_maps_url" class="form-control" value="{{ $landingPage->location_google_maps_url }}">
  </div>

  <div class="form-group">
    <label>Get Directions URL</label>
    <input type="url" name="location_directions_url" class="form-control" value="{{ $landingPage->location_directions_url }}">
  </div>

  <div class="form-group">
    <label>WhatsApp URL</label>
    <input type="url" name="location_whatsapp_url" class="form-control" value="{{ $landingPage->location_whatsapp_url }}" placeholder="https://wa.me/966...">
  </div>

  <div class="form-group">
    <label>Phone Number</label>
    <input type="text" name="location_phone" class="form-control" value="{{ $landingPage->location_phone }}">
  </div>

  <div class="form-group">
    <label>Date & Time Text</label>
    <input type="text" name="location_date_time_text" class="form-control" value="{{ $landingPage->location_date_time_text }}" placeholder="Leave empty to use event dates">
  </div>

  <div class="form-group">
    <label>Additional Notes</label>
    <textarea name="location_notes" class="form-control" rows="3">{{ $landingPage->location_notes }}</textarea>
  </div>

  <button type="submit" class="btn btn-success">Save Location Section</button>
  {!! Form::close() !!}
</div>

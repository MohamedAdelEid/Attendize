<div class="tab-pane {{ $tab == 'registration' ? 'active' : '' }}" id="registration">
  {!! Form::open(['url' => route('postEventLandingPage', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
  <input type="hidden" name="section" value="registration">

  <div class="alert alert-info">
    Registration forms, tabs, and fields are managed in
    <a href="{{ route('showEventRegistration', ['event_id' => $event->id]) }}">Registration Settings</a>.
    Use flags: <em>Show on Landing</em>, <em>Members Form</em>, <em>Virtual Form</em>.
  </div>

  <div class="form-group">
    <label>Section Title</label>
    <input type="text" name="registration_title" class="form-control" value="{{ $landingPage->registration_title }}" placeholder="Registration">
  </div>

  <div class="form-group">
    <label>Section Description</label>
    <textarea name="registration_description" class="form-control" rows="2">{{ $landingPage->registration_description }}</textarea>
  </div>

  <button type="submit" class="btn btn-success">Save Registration Section</button>
  {!! Form::close() !!}
</div>

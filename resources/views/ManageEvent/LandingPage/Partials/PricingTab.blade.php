<div class="tab-pane {{ $tab == 'pricing' ? 'active' : '' }}" id="pricing">
  <div class="alert alert-warning">
    <strong>Data source:</strong> Fee cards are built from Registration Forms marked "Show on Fees Section".
    Prices come from Categories → Conferences. Configure forms in
    <a href="{{ route('showEventRegistration', ['event_id' => $event->id]) }}">Registration</a> and
    <a href="{{ route('showEventRegistrationCategories', ['event_id' => $event->id]) }}">Categories</a>.
  </div>

  {!! Form::open(['url' => route('postEventLandingPage', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
  <input type="hidden" name="section" value="pricing">

  <div class="form-group">
    <label>Section Title</label>
    <input type="text" name="pricing_title" class="form-control" value="{{ $landingPage->pricing_title }}" placeholder="Registration Fees">
  </div>

  <div class="form-group">
    <label>Section Description</label>
    <textarea name="pricing_description" class="form-control" rows="2">{{ $landingPage->pricing_description }}</textarea>
  </div>

  <div class="form-group">
    <label>Footer Note</label>
    <textarea name="pricing_footer_note" class="form-control" rows="2" placeholder="e.g. All prices include materials and certificate">{{ $landingPage->pricing_footer_note }}</textarea>
  </div>

  <button type="submit" class="btn btn-success">Save Pricing Section</button>
  {!! Form::close() !!}

  <hr>
  <h4>Current Fee Cards Preview</h4>
  @if(!empty($resolved['pricing']['cards']))
    <table class="table table-bordered">
      <thead><tr><th>Card</th><th>Tiers</th><th>Source</th></tr></thead>
      <tbody>
        @foreach($resolved['pricing']['cards'] as $card)
        <tr>
          <td><strong>{{ $card['title'] }}</strong>@if($card['badge']) <span class="label label-success">{{ $card['badge'] }}</span>@endif</td>
          <td>
            @foreach($card['tiers'] as $tier)
              {{ $tier['label'] }}: {{ $tier['price_formatted'] }} {{ $tier['currency'] }}<br>
            @endforeach
          </td>
          <td>Registration #{{ $card['registration_id'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p class="text-muted">No fee cards yet. Enable "Show on Fees Section" on registration forms, or ensure forms have show_on_landing / is_members_form / is_virtual_form flags.</p>
  @endif
</div>

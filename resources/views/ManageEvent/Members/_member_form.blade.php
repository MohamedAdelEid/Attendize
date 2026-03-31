@php
    $memberData = isset($member) ? $member->getDataByKey() : collect();
@endphp

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ $formTitle ?? 'Member' }}</h3>
    </div>
    <div class="panel-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                @php $currentStatus = old('status', isset($member) ? $member->status : 'approved'); @endphp
                <option value="approved" {{ $currentStatus === 'approved' ? 'selected' : '' }}>approved</option>
                <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>pending</option>
                <option value="rejected" {{ $currentStatus === 'rejected' ? 'selected' : '' }}>rejected</option>
            </select>
        </div>

        @forelse($fields as $field)
            @php
                $inputName = 'member_data[' . $field->field_key . ']';
                $inputValue = old('member_data.' . $field->field_key, $memberData->get($field->field_key));
            @endphp
            <div class="form-group">
                <label>
                    {{ $field->label }}
                    <small class="text-muted">({{ $field->field_key }})</small>
                    @if($field->is_required)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                @if($field->type === 'date')
                    <input type="date" name="{{ $inputName }}" value="{{ $inputValue }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                @elseif($field->type === 'datetime')
                    <input type="datetime-local" name="{{ $inputName }}" value="{{ $inputValue }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                @elseif($field->type === 'number')
                    <input type="number" step="any" name="{{ $inputName }}" value="{{ $inputValue }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                @else
                    <input type="text" name="{{ $inputName }}" value="{{ $inputValue }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                @endif
            </div>
        @empty
            <div class="alert alert-warning">No member fields configured yet. Add fields first from Members &gt; Member Fields.</div>
        @endforelse
    </div>
</div>

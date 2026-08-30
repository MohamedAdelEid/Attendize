@if($abstract->templates->filter(function ($t) { return optional($t->category)->is_active; })->count())
<div>
    <label class="field-label">@lang('Abstract.category') <span class="text-red-500">*</span></label>
    <select name="abstract_category_id" class="field-input" required>
        <option value="">@lang('Abstract.select_category')</option>
        @foreach($abstract->templates as $tpl)
            @if(optional($tpl->category)->is_active)
                <option value="{{ $tpl->abstract_category_id }}" {{ (string) old('abstract_category_id') === (string) $tpl->abstract_category_id ? 'selected' : '' }}>
                    {{ $tpl->category->name }}
                </option>
            @endif
        @endforeach
    </select>
</div>
@endif

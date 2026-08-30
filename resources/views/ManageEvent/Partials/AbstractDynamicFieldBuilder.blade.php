{{-- Shared dynamic field panel template for Abstract forms --}}
@php
    $fieldTypes = $fieldTypes ?? \App\Models\AbstractDynamicFormField::getFieldTypes();
@endphp
<div id="abstract-field-template" style="display: none;">
    <div class="panel panel-default dynamic-field" style="margin-bottom: 10px;">
        <div class="panel-heading" style="padding: 8px 12px;">
            <div class="row">
                <div class="col-xs-8">
                    <span class="drag-handle"><i class="ico-arrow-move"></i></span>
                    <span class="position-badge badge">#<span class="position-number">1</span></span>
                    <strong class="field-title">New Field</strong>
                </div>
                <div class="col-xs-4 text-right">
                    <button type="button" class="btn btn-xs btn-default move-up-btn"><i class="ico-arrow-up"></i></button>
                    <button type="button" class="btn btn-xs btn-default move-down-btn"><i class="ico-arrow-down"></i></button>
                    <button type="button" class="btn btn-xs btn-danger remove-field-btn"><i class="ico-remove"></i></button>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <input type="hidden" name="dynamic_fields[{INDEX}][position]" class="field-position" value="1">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label required">Label</label>
                        <input type="text" name="dynamic_fields[{INDEX}][label]" class="form-control field-label" placeholder="Field label" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label required">Type</label>
                        <select name="dynamic_fields[{INDEX}][type]" class="form-control field-type">
                            @foreach($fieldTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Description / Help text</label>
                        <input type="text" name="dynamic_fields[{INDEX}][description]" class="form-control" placeholder="Optional help text">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group field-options" style="display: none;">
                        <label class="control-label required">Options</label>
                        <textarea name="dynamic_fields[{INDEX}][options]" class="form-control" rows="3"
                            placeholder="Enter one option per line"></textarea>
                    </div>
                </div>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="dynamic_fields[{INDEX}][is_required]" value="1">
                    This field is required
                </label>
            </div>
        </div>
    </div>
</div>

<div role="dialog" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-eye"></i>
                    Submission Details
                </h3>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%;">@lang('Abstract.full_name')</th>
                        <td>{{ $submission->display_name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.email')</th>
                        <td>{{ $submission->email }}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.phone')</th>
                        <td>{{ $submission->phone ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.authors')</th>
                        <td>{{ $submission->authors ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.details')</th>
                        <td>{!! nl2br(e($submission->details ?: '—')) !!}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.domain')</th>
                        <td>{{ $submission->domain ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.abstract')</th>
                        <td>{{ optional($submission->abstractDefinition)->name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.category')</th>
                        <td>{{ optional($submission->category)->name ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.status')</th>
                        <td>
                            <span class="label label-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.submitted_at')</th>
                        <td>{{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '—' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('Abstract.file_upload')</th>
                        <td>
                            @if($submission->file_path)
                                <a href="{{ $submission->file_url }}" target="_blank" class="btn btn-sm btn-default">
                                    <i class="ico-download"></i> @lang('Abstract.download_file')
                                </a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @if($submission->final_file_path)
                    <tr>
                        <th>@lang('Abstract.final_submission')</th>
                        <td>
                            <a href="{{ $submission->final_file_url }}" target="_blank" class="btn btn-sm btn-success">
                                <i class="ico-download"></i> @lang('Abstract.download_final_file')
                            </a>
                            @if($submission->final_submitted_at)
                                <span class="text-muted">({{ $submission->final_submitted_at->format('Y-m-d H:i') }})</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @foreach($submission->formFieldValues as $value)
                        <tr>
                            <th>{{ optional($value->field)->label ?: 'Field' }}</th>
                            <td>
                                @if(optional($value->field)->type === 'file' && $value->value)
                                    <a href="{{ asset('storage/' . ltrim($value->value, '/')) }}" target="_blank">Download</a>
                                @else
                                    {{ $value->value ?: '—' }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($submission->review_notes)
                        <tr>
                            <th>@lang('Abstract.review_notes')</th>
                            <td>{{ $submission->review_notes }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="modal-footer">
                {!! Form::button(trans('basic.cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) !!}
            </div>
        </div>
    </div>
</div>

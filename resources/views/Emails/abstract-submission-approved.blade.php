<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $emailSubject ?? 'Abstract Submission' }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #222;">{{ $event->title }}</h2>
        <p style="margin: 5px 0 0; color: #666;">{{ $abstract->name }}</p>
        @if(optional($submission->category)->name)
            <p style="margin: 5px 0 0; color: #888; font-size: 13px;">@lang('Abstract.category'): {{ $submission->category->name }}</p>
        @endif
    </div>

    <div>
        {!! $emailBody !!}
    </div>

    @if(!empty($finalUploadUrl))
    <div style="margin: 28px 0; text-align: center;">
        <a href="{{ $finalUploadUrl }}"
           style="display: inline-block; padding: 14px 28px; background: #111827; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px;">
            @lang('Abstract.upload_final_submission')
        </a>
        <p style="margin: 12px 0 0; font-size: 12px; color: #9ca3af;">
            @lang('Abstract.upload_final_submission_help')
        </p>
    </div>
    @endif

    <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee; font-size: 12px; color: #999;">
        <p>@lang('Abstract.approval_email_footer', ['event' => $event->title])</p>
    </div>
</body>
</html>

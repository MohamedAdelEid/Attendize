<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ trans('AttendeePortal.login_code_subject', ['event' => $event->title]) }}</title>
</head>
<body style="margin:0;padding:0;background:#f9fafb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;padding:40px 16px;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:16px;border:1px solid #e5e7eb;">
                <tr>
                    <td style="padding:32px;text-align:center;">
                        <div style="width:48px;height:48px;background:#4f46e5;border-radius:12px;display:inline-block;line-height:48px;color:#fff;font-size:20px;">&#128274;</div>
                        <h1 style="margin:16px 0 8px;font-size:20px;font-weight:700;color:#111827;">@lang('AttendeePortal.login_code_heading')</h1>
                        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;">{{ $event->title }}</p>
                        <p style="margin:0 0 8px;font-size:14px;color:#374151;">@lang('AttendeePortal.login_code_greeting', ['name' => $user->full_name])</p>
                        <p style="margin:0 0 24px;font-size:13px;color:#6b7280;">@lang('AttendeePortal.login_code_body')</p>
                        <div style="display:inline-block;padding:16px 32px;background:#f3f4f6;border-radius:12px;font-size:32px;font-weight:700;letter-spacing:8px;color:#111827;">
                            {{ $code }}
                        </div>
                        <p style="margin:24px 0 0;font-size:12px;color:#9ca3af;">@lang('AttendeePortal.login_code_expires')</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

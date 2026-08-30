<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ trans('Abstract.reviewer_invitation_subject', ['event' => $event->title]) }}</title>
</head>
<body style="margin:0;padding:0;background:#f9fafb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;padding:40px 16px;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;">
                <tr>
                    <td style="padding:32px 32px 24px;text-align:center;border-bottom:1px solid #f3f4f6;">
                        <div style="width:48px;height:48px;background:#111827;border-radius:12px;display:inline-block;line-height:48px;color:#fff;font-size:20px;">&#128196;</div>
                        <h1 style="margin:16px 0 4px;font-size:20px;font-weight:700;color:#111827;">@lang('Abstract.reviewer_portal')</h1>
                        <p style="margin:0;font-size:14px;color:#6b7280;">{{ $event->title }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0 0 16px;font-size:15px;color:#374151;line-height:1.6;">
                            @lang('Abstract.reviewer_invitation_greeting', ['name' => $reviewer->name])
                        </p>
                        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;line-height:1.6;">
                            @lang('Abstract.reviewer_invitation_body')
                        </p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:16px 20px;font-size:13px;color:#374151;">
                                    <strong>@lang('Abstract.email'):</strong> {{ $reviewer->email }}<br>
                                    <strong>@lang('Abstract.reviewer_password'):</strong> {{ $plainPassword }}
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="padding-bottom:12px;">
                                    <a href="{{ $oneTimeLoginUrl }}"
                                       style="display:inline-block;padding:14px 28px;background:#111827;color:#ffffff;text-decoration:none;border-radius:10px;font-size:14px;font-weight:600;">
                                        @lang('Abstract.reviewer_one_time_login')
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <a href="{{ $portalUrl }}"
                                       style="display:inline-block;padding:12px 24px;color:#374151;text-decoration:none;font-size:13px;">
                                        @lang('Abstract.reviewer_go_to_login')
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 32px;background:#f9fafb;border-top:1px solid #f3f4f6;text-align:center;">
                        <p style="margin:0;font-size:12px;color:#9ca3af;">@lang('Abstract.reviewer_invitation_footer')</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

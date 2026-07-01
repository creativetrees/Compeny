@php $brand = \App\Models\SiteSetting::current()->brand_name ?? 'Creative Trees Group'; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New project brief</title>
</head>
<body style="margin:0;background:#f2f2f0;font-family:ui-monospace,'SFMono-Regular',Menlo,monospace;color:#0a0a0a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f2f2f0;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #e6e6e3;">
                    <tr>
                        <td style="padding:24px 28px;border-bottom:1px solid #e6e6e3;">
                            <div style="font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:#6f6f6c;">● {{ $brand }}</div>
                            <div style="font-size:20px;font-weight:700;text-transform:uppercase;letter-spacing:-.01em;margin-top:8px;">New project brief</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 28px;font-family:Inter,Arial,sans-serif;">
                            @php
                                $rows = [
                                    'Name' => $lead->name,
                                    'Email' => $lead->email,
                                    'Company' => $lead->company,
                                    'Phone' => $lead->phone,
                                    'Budget' => $lead->budget,
                                    'Interest' => $lead->service_interest,
                                ];
                            @endphp
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                @foreach ($rows as $label => $value)
                                    @if ($value)
                                        <tr>
                                            <td style="padding:8px 0;width:90px;vertical-align:top;font-family:ui-monospace,monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#a9a9a5;">{{ $label }}</td>
                                            <td style="padding:8px 0;font-size:14px;color:#0a0a0a;">{{ $value }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>

                            <div style="margin-top:16px;padding-top:16px;border-top:1px solid #e6e6e3;">
                                <div style="font-family:ui-monospace,monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#a9a9a5;margin-bottom:8px;">Message</div>
                                <div style="font-size:14px;line-height:1.6;white-space:pre-line;">{!! nl2br(e($lead->message)) !!}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px;border-top:1px solid #e6e6e3;font-family:ui-monospace,monospace;font-size:11px;color:#6f6f6c;">
                            Received {{ $lead->created_at?->format('d M Y, H:i') }} · via website
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

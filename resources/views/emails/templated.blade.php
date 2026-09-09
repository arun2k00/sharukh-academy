<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ config('academy.name') }}</title>
</head>
<body style="margin:0;background:#f7f7f6;font-family:Georgia,'Times New Roman',serif;color:#1a1a18;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:520px;background:#ffffff;border:1px solid #ececeb;">
                    <tr>
                        <td style="padding:28px 32px 0;">
                            <span style="display:inline-block;border:1px solid #1b4331;color:#1b4331;width:28px;height:28px;line-height:28px;text-align:center;font-size:15px;">O</span>
                            <span style="font-size:13px;letter-spacing:0.08em;color:#6b6b65;margin-left:8px;text-transform:uppercase;">{{ config('academy.name') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px 8px;font-size:15px;line-height:1.7;color:#2c2c29;white-space:pre-line;">{{ $body }}</td>
                    </tr>
                    @if ($url)
                        <tr>
                            <td style="padding:8px 32px 28px;">
                                <a href="{{ $url }}" style="display:inline-block;background:#1b4331;color:#ffffff;padding:10px 20px;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:14px;">Open in {{ config('academy.name') }}</a>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

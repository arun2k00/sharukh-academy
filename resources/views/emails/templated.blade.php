@php
    $settings = app(\App\Services\SettingsRepository::class);
    // The header band is dark, so the full-colour mark would be unreadable on
    // it; `logo_email_path` is the white cut of the same artwork.
    $logoUrl = $settings->imageUrl('logo_email_path') ?? $settings->imageUrl('logo_path');
    $academyName = $settings->get('academy_name') ?: config('academy.name');
    $buttonLabel ??= 'Open in '.$academyName;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $academyName }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Helvetica,Arial,sans-serif;color:#171d26;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(11,33,54,0.10);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#0d539b;padding:28px 36px;">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $academyName }}" height="34" style="display:block;height:34px;max-height:34px;width:auto;border:0;">
                            @else
                                <span style="font-family:Georgia,'Times New Roman',serif;font-size:20px;font-weight:600;color:#ffffff;letter-spacing:0.01em;">{{ $academyName }}</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px 36px 8px;">
                            <div style="font-size:15.5px;line-height:1.7;color:#2a303a;white-space:pre-line;">{{ $body }}</div>
                        </td>
                    </tr>

                    @if ($url)
                        <tr>
                            <td style="padding:16px 36px 8px;">
                                <table role="presentation" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="border-radius:6px;background-color:#0d539b;">
                                            <a href="{{ $url }}"
                                               style="display:inline-block;padding:12px 28px;font-family:Helvetica,Arial,sans-serif;font-size:14.5px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:6px;">
                                                {{ $buttonLabel }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:36px 36px 32px;">
                            <hr style="border:none;border-top:1px solid #e5edf5;margin:0 0 20px;">
                            <p style="margin:0 0 6px;font-size:12.5px;line-height:1.6;color:#677181;">
                                Need help? Write to
                                <a href="mailto:{{ config('academy.support_email') }}" style="color:#0d539b;text-decoration:none;">{{ config('academy.support_email') }}</a>
                                @if (config('academy.support_phone'))
                                    or call {{ config('academy.support_phone') }}.
                                @endif
                            </p>
                            <p style="margin:0;font-size:12px;color:#8792a2;">&copy; {{ now()->year }} {{ $academyName }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

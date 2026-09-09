<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | Values that rarely change and are safe to cache. Editable copy such as the
    | contact address or social links lives in the `settings` table instead, so
    | an administrator can change it without touching the environment file.
    |
    */

    'name' => env('APP_NAME', 'Edunova'),

    'currency' => env('ACADEMY_CURRENCY', 'INR'),

    'currency_symbol' => env('ACADEMY_CURRENCY_SYMBOL', '₹'),

    'support_email' => env('ACADEMY_SUPPORT_EMAIL', 'support@edunova.com'),

    'support_phone' => env('ACADEMY_SUPPORT_PHONE', '+91 00000 00000'),

    /*
    |--------------------------------------------------------------------------
    | Learning rules
    |--------------------------------------------------------------------------
    |
    | `certificate_threshold` is the default completion percentage a student must
    | reach before a certificate is issued. Individual courses may override it.
    |
    */

    'certificate_threshold' => (int) env('ACADEMY_CERTIFICATE_THRESHOLD', 100),

    /*
    | Require a verified email address before the learning area opens. Leave it
    | off until outgoing mail is confirmed working, otherwise new students are
    | locked out by an email that never arrives. Run `php artisan route:clear`
    | after changing it.
    */
    'require_email_verification' => (bool) env('ACADEMY_REQUIRE_EMAIL_VERIFICATION', false),

    /*
    | Minutes before a live class starts that the Zoom join button unlocks.
    */
    'live_join_window' => (int) env('ACADEMY_LIVE_JOIN_WINDOW', 15),

    /*
    | Watched percentage at which a video lesson may be auto-completed.
    */
    'video_completion_percent' => 90,

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    |
    | The "manual" driver processes nothing. Paid courses record a pending order
    | and wait for an administrator to confirm the enrolment. Swap the driver
    | once real gateway credentials exist.
    |
    */

    'payments' => [
        'driver' => env('PAYMENT_DRIVER', 'manual'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    |
    | Resource files are stored on the private disk and streamed by a controller
    | that checks enrolment, so nothing is reachable by guessing a URL.
    |
    */

    'uploads' => [
        'image_max_kb' => 4096,
        'image_mimes' => ['jpg', 'jpeg', 'png', 'webp'],

        'resource_max_kb' => 20480,
        'resource_mimes' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'txt', 'zip', 'jpg', 'jpeg', 'png', 'webp'],

        // Certificate artwork is composited at native resolution for print
        // and social-share quality, so it gets a larger allowance and only
        // the two raster formats GD can read for this purpose.
        'certificate_max_kb' => 10240,
        'certificate_mimes' => ['png', 'jpg', 'jpeg'],
    ],

];

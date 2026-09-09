<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
|
| This is the only scheduled task the academy needs. It requires a single
| cron entry pointed at `php artisan schedule:run` (see DEPLOYMENT.md) —
| no permanent queue worker or daemon of any kind.
*/
Schedule::command('academy:send-reminders')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

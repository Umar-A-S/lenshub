<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── WA Reminder otomatis setiap 2 menit ─────────────────────────────
// (cron harus jalan: * * * * * php artisan schedule:run)
Schedule::command('rental:send-reminders')->everyTwoMinutes();

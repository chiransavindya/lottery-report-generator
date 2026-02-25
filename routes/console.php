<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automatic cleanup of old PDF reports (older than 7 days)
// Runs daily at 2 AM
Schedule::command('reports:cleanup-old --days=7')->dailyAt('02:00');

// Also cleanup processed XML files that might have failed deletion
Schedule::command('uploads:cleanup')->dailyAt('03:00');

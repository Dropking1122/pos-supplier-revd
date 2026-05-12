<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Jadwal Backup Otomatis ──────────────────────────────────────────────────
// Baca pengaturan dari file JSON. Dievaluasi setiap menit oleh schedule:work.
$scheduleFile = storage_path('app/backup_schedule.json');
if (file_exists($scheduleFile)) {
    $cfg = json_decode(file_get_contents($scheduleFile), true) ?? [];

    if (!empty($cfg['enabled'])) {
        $time            = $cfg['time'] ?? '00:00';
        [$hour, $minute] = array_map('intval', explode(':', $time));
        $cron            = "{$minute} {$hour} * * *";

        Schedule::command('app:backup-database')
            ->cron($cron)
            ->appendOutputTo(storage_path('logs/backup.log'))
            ->runInBackground();
    }
}

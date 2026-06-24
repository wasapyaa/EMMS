<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\Student;
use Illuminate\Support\Facades\Schedule;

Artisan::command('ranking:snapshot', function () {
    Student::saveDailyRankingSnapshot();
    $this->info('Daily ranking snapshot saved successfully.');
})->purpose('Save daily student ranking snapshot');

Schedule::command('ranking:snapshot')->dailyAt('23:59');

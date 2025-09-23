<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Carbon\Carbon;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $today = Carbon::now()->format('Ymd');
        $schedule->command('sales:generate-report ' .  $today . ' ' . $today . ' ' . $today . ' 29991')->timezone('Europe/Istanbul')->dailyAt('23:00');
        $schedule->command('arrivalconf:generate-report')->timezone('Europe/Istanbul')->dailyAt('23:02');
        $schedule->command('goodsman:generate-report')->timezone('Europe/Istanbul')->dailyAt('23:04');
        $schedule->command('files:process')->timezone('Europe/Istanbul')->dailyAt('23:06');

    })
    ->create();


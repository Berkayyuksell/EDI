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

        $schedule->command('sales:generate-report ' .  $today . ' ' . $today . ' ' . $today . ' 29991')->dailyAt('15:07')->timezone('Europe/Istanbul');
        //$schedule->command('goodsman:generate-report  ' . $today . ' ' . $today . ' ' . $today . ' 29991')->everyMinute();
        //$schedule->command('missean:generate-report  ' .  $today . ' ' . $today . ' ' . $today . ' 29991')->everyMinute();
        //$schedule->command('stockcount:generate-report  ' .  $today . ' ' . $today . ' ' . $today . ' 29991')->everyMinute();
        //$schedule->command('files:process')->everyMinute();
    })
    ->create();

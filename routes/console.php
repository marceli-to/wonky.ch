<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Process queued jobs every minute
Schedule::command('queue:work --stop-when-empty --max-time=60')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

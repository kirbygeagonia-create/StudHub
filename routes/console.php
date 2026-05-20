<?php

use App\Domain\Lends\Jobs\SendReturnReminders;
use App\Domain\Search\Jobs\SendDailyDigest;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SendReturnReminders)->dailyAt('09:00');
Schedule::job(new SendDailyDigest)->dailyAt('07:00');
Schedule::command('studhub:expire-requests')->dailyAt('03:00');

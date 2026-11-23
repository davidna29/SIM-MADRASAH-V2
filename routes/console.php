<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('berita:publish-terjadwal')->everyMinute();
Schedule::command('api:prune-idempotency')->daily();

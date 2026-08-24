<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('berita:publish-terjadwal')->everyMinute();

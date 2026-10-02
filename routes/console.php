<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('peminjaman:selesaikan')->everyFifteenMinutes();

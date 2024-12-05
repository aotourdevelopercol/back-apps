<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->everyMinute();


// comandos por minutos 
//Schedule::command('app:time-command')->everyMinute();
Schedule::command('app:consulta-documentos-vehiculos')->everyMinute();

// comandos cada dos horas 
// $schedule->command('app:')->everyTwoHours();

// Comando para una hora en especifico 
// $schedule->command('app:')->hourlyAt(15);

// Comando para dias en especificos y una hora exacta se ejecuta el lunes a las 8am
//$schedule->command('app:')->weeklyOn(1, '08:00');

// Comandos por horas
Schedule::command('app:command-per-hours')->hourly();

// Comandos por dias 
Schedule::command('app:commanf-per-day')->dailyAt('14:00');

// Comandos por mes
Schedule::command('app:commanf-per-month')->monthly();

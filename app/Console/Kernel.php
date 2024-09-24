<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*
        Laravel Scheduler does not seem to work very well in Azure,
        so I opted to fire each individual event as a Cron Triggered WebJob
        */
        
        /*
        $schedule->command('spec2kapp:test_scheduler')
            ->everyMinute()
            ->when(function(){
                return env('APP_ENV') == 'dev';
        });
        
        $schedule->command('spec2kapp:update_notifications_and_piece_parts')
            ->dailyAt('09.30')
            ->timezone('Europe/London')
            ->when(function(){
                return env('APP_ENV') == 'dev';
        });
        
        $schedule->command('spec2kapp:update_notifications_and_piece_parts')
            ->dailyAt('13.30')
            ->timezone('Europe/London')
            ->when(function(){
                return env('APP_ENV') == 'dev';
        });
        
        $schedule->command('spec2kapp:update_notifications_and_piece_parts')
            ->dailyAt('17.30')
            ->timezone('Europe/London')
            ->when(function(){
                return env('APP_ENV') == 'dev';
        });
        
        $schedule->command('spec2kapp:update_notifications_and_piece_parts')
            ->dailyAt('21.30')
            ->timezone('Europe/London')
            ->when(function(){
                return env('APP_ENV') == 'dev';
        });
        
        $schedule->command('spec2kapp:update_notifications_and_piece_parts')
            ->dailyAt('01.30')
            ->timezone('Europe/London')
            ->when(function(){
                return env('APP_ENV') == 'dev';
        });
        
        $schedule->command('spec2kapp:update_notifications_and_piece_parts')
            ->dailyAt('05.30')
            ->timezone('Europe/London')
            ->when(function(){
                return env('APP_ENV') == 'dev';
        });
        */
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

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
        Commands\SendRemindMail::class,
        Commands\DeleteTokens::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('send_remind_mail:cron')
                 ->dailyAt('7:00')
                 ->sendOutputTo(storage_path('logs/send_remind_mail.log'), true);
        
        $schedule->command('delete_tokens:cron')
                 ->daily()
                 ->sendOutputTo(storage_path('logs/delete_tokens.log'), true);
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

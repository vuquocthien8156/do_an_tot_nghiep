<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendNotification;
use App\Console\Commands\SyncDB411;
use App\Console\Commands\SyncDB411Auto;

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
    protected function schedule(Schedule $schedule) {
        $schedule->command(SendNotification::class, ['--admin-allcustomer'])->everyMinute();
        $schedule->command(SendNotification::class, ['--admin-customer'])->everyMinute();
        $schedule->command(SendNotification::class, ['--admin-customer-unit'])->everyMinute();
        $schedule->command(SendNotification::class, ['--appointment'])->hourly();
        $schedule->command(SendNotification::class, ['--birthday-customer'])->dailyAt('7:00')->timezone('Asia/Ho_Chi_Minh');
        //$schedule->command(SyncDB411::class, ['--sync-all'])->hourly();
        $schedule->command(SyncDB411Auto::class, ['--sync-auto-all'])->everyFiveMinutes();
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

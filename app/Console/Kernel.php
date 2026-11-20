<?php

namespace App\Console;

use App\Console\Commands\sendEmail;
use App\Models\advice_note\advice_notes;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Notifications\CreatedEmails;
use Illuminate\Support\Carbon;
use App\Models\Notifications\BulkSms;
use App\Traits\NotificationTrait;
use App\Console\Commands\BadgesBackup;

class Kernel extends ConsoleKernel
{
    use NotificationTrait;
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        \Laravelista\LumenVendorPublish\VendorPublishCommand::class,
        Commands\cloneJob::class,
        commands\sendEmail::class,
        commands\BulkSmsCron::class,
        commands\PaymentBalanceNotification::class,
        commands\AutomaticActionCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // schedule to send emails to user
        // if (CreatedEmails::where('emailStatus','PENDING')->exists()) {
        //     $schedule->command('Email:Send')->cron('* * * * *');
        // }
        // end of email schedules

        // sending Bulk Sms
        $messages = BulkSms::where('sms_status', 0)->get();
        foreach ($messages as $value) {
            $sent_on = $value->sms_sent_on;
            $today = date('Y-m-d');

            if ($sent_on <= $today) {
                $schedule->command('send:sms')->cron('* * * * *');
            }
        }
        // end sending bulk sms

        #**************** RUN BACKUP *****************#
        $schedule->command('backup:run')->daily()->at('23:59');
        #*******************END BACKUP RUN************#

        #************* SEND PAYMENT BALANCE NOTIFICATION TO USER *************#
        $schedule->command('balance:notification')->daily()->at('10:00');
        #************* END SEND PAYMENT BALANCE NOTIFICATION TO USER *********#

        #---------- CANCEL EXPIRED INVOICES -----#
        $schedule->command('Clear:Invoices')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

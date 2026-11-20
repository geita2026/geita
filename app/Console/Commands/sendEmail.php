<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notifications\CreatedEmails;
use Illuminate\Support\Facades\Mail;


class sendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Email:Send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send emails to users from created email table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // $emails = CreatedEmails::where('emailStatus', 'PENDING')->get();
            // foreach ($emails as $email) {
            //     $email_data = [
            //         'fromName' =>'TanTrade',
            //         'emailTo' => $email->emailTo,
            //         'name' => $email->receiverName,
            //         'emailContent' => $email->emailContent,
            //         'regardsName' => $email->regardsName,
            //         'emailSubject' => $email->emailSubject,
            //         'emailLink' => $email->emailLink,
            //         'linkLable' => $email->linkLable,
            //     ];
            //     Mail::send('mails.NotificationEmail', $email_data, function ($message) use ($email_data) {
            //         $message
            //             ->to($email_data['emailTo'])
            //             ->priority(1)
            //             ->subject($email_data['emailSubject']);
            //     });
            //     $email->delete();
            // }
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}

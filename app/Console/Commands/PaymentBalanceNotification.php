<?php

namespace App\Console\Commands;

use App\Models\advice_note\advice_notes;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Traits\NotificationTrait;


class PaymentBalanceNotification extends Command
{
    use NotificationTrait;
 
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminding message to customer abou his or her balance';

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

        #***************** NOTIFY CUSTOMER ABOUT BALANCE *************#
        $adviceNotes = advice_notes::leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
            ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')->where('event_status', 1)
            ->where('control_number', '!=', null)->where('balance', '>', 1)->where('advice_note_status', 'APPROVED')
            ->where('advice_note_deadline', '>', date('Y-m-d'))->get();

        foreach ($adviceNotes as $value) {
            if ($value->new_deadline != null) {
                $fromDate = Carbon::parse(date('Y-m-d h:i:s', strtotime($value->new_deadline)));
            } else {
                $fromDate = Carbon::parse(date('Y-m-d h:i:s', strtotime($value->advice_note_deadline)));
            }
            $toDate = Carbon::parse(date('Y-m-d h:i:s'));


            $days = $toDate->diffInDays($fromDate);

            $deadline = $fromDate;
            $phone_number = $value->companyPhone;
            $control_number = $value->control_number;
            if ($days == 7 || $days == 3 || $days == 0) {
                // return $days;
                $sms_content = 'We are kindly remind you to complete your payment for 49th DITF,
Balance: ' . number_format($value->balance, 2) . ' ' . $value->currency . ',
Contro Number: ' . $control_number . ',
Pay Before ' . date('d-m-Y', strtotime($deadline)) . ' 23:59,
TSRN:' . $value->TSRN;
                $sent_on = date('Y-m-d');
                $customer_name = $value->companyName;
                $d1 = date('Y-m-d', strtotime($value->notification_date));
                $d2 = date('Y-m-d');

                if ($d1 != $d2) // chech if not same date
                {
                    if (config('global.APP_STATUS') == 1) {
                        $this->SaveSendSmsJobs($phone_number, $sms_content, $sent_on, $customer_name); // saving message to jobs
                    }
                    advice_notes::find($value->advice_note_id)->update(['notification_date' => date('Y-m-d h:i:s')]);
                }
            }
        }
        #***************** END NOTIFY USER ***************************#
    }
}

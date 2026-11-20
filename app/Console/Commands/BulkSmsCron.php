<?php
namespace App\Console\Commands;

use App\Models\advice_note\service_charges;
use App\Models\booking\booked_pavilions;
use Illuminate\Console\Command;
use App\Models\Notifications\BulkSms;
use App\Traits\NotificationTrait;
use Illuminate\Support\Carbon;

class BulkSmsCron extends Command
{
    use NotificationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This comand sends Bulk sms to specified user in system';

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

          #*********************** COMMAND EXCUTING EVERY 1 MIN
        $messages=BulkSms::where('sms_status',0)->get();
        foreach ($messages as $key)
        {
        $recipients=$key->phone_number;
        $datetime = date('Y-m-d H:i:s');
        $sentOn=date('Y-m-d',strtotime($key->sms_sent_on));
        $today=date('Y-m-d');

        if ($sentOn<=$today)
        {
# sms to be sent
$sms_content =$key->sms_body;
#end of sms to be sent

     $this->Notification($recipients,$sms_content,$datetime); // send sms methode
     $sms=BulkSms::find($key->sms_id);
     $sms->delete(); // after sennding sms change status to 1;
    }

    }
    #*********** END OF COMMANDS *************#
}
}

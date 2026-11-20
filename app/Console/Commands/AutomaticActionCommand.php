<?php

namespace App\Console\Commands;

use App\Models\advice_note\advice_notes;
use Illuminate\Console\Command;
use App\Traits\AutomaticActionTraits;

class AutomaticActionCommand extends Command
{
    use AutomaticActionTraits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Clear:Invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel All Expired Invoices from the system.';

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
        #------ AUTOCANCELATION OF INVOICES -----#
        $ExpiredInvoices = advice_notes::leftJoin('companies', 'advice_notes.company_id', 'companies.companyID')
            ->orderBy('advice_note_deadline', 'DESC')->where('advice_note_status', '!=', 'CANCELED')
            ->where('advice_note_deadline', '<', date('Y-m-d'))
            ->where('balance', '>', 1)->where('amount_paid', Null)
            ->get();
        foreach ($ExpiredInvoices as  $value) {
            if (config('global.APP_STATUS') == 1) {
                $id = $value->advice_note_id;
                $status = $this->AutocancelationOfExpiredInvoices($id);
                if ($status == 0) {
                    continue;
                }
            }
        }
        #------ END AUTOCANCELATION OF INVOICES -----#
    }
}

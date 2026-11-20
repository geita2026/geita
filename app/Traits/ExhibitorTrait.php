<?php
namespace App\Traits;
use App\Models\exhibition\exhibitors;
use Illuminate\Support\Str;
use App\Models\space\booths;
use App\Models\advice_note\advice_notes;
use App\Models\advice_note\service_charges;
use App\Models\space\pavilions;
use App\Models\booking\booked_booths;
use App\Models\booking\booked_pavilions;
use App\Models\booking\booked_sponsorship_packages;
use App\Models\companies;
use App\Models\settings\events;

trait ExhibitorTrait{


    #-------------- REGISTER EXHIBITORS ---------#
     function RegisterCompanyAsExhibitor($company_id,$event_id)
    {
            try {
                $exist=exhibitors::where('exhibitor_id',$company_id)->where('exhibitor_event',$event_id)->first();
            if(!$exist)  #----CHECK IF DOES NOT EXISTS -----#
            {
                $participationId=Str::uuid();
                exhibitors::create([
                    'participation_id'=>$participationId,
                    'exhibitor_id'=>$company_id,
                    'exhibitor_event'=>session('liveEvent'),
                    ]);
                    #------MAKE ASSOCIATE COMPANIES EXHIBITORS TOO
                    $companies=companies::where('associate_company',$company_id)->get();
                    foreach ($companies as $key) {
                        exhibitors::create([
                            'participation_id'=>Str::uuid(),
                            'exhibitor_id'=>$key->companyID,
                            'exhibitor_event'=>session('liveEvent'),
                            ]);
                    }
                    #------ END ----------#
                    return $participationId;
            }
            $participationId=$exist->participation_id;
            return $participationId;
            } catch (\Throwable $th) {
                return '0';
            }
    }

    #-----------------END REGISTER EXHIBITOR ------#



    #------------- CLEAR BOOKING DETAILS WHEN CANCELED -------#
    function RemoveAdviceNoteFromList($id){
        try {
            $adviceNote=advice_notes::find($id);//return advice note
            booked_pavilions::where('advice_note_id',$id)->delete();
            service_charges::where('advice_note_id',$id)->delete();
            booked_sponsorship_packages::where('advice_note_id',$id)->delete();
            $event_id=$adviceNote->event;
            $company_id=$adviceNote->company_id;
             $invoiceCount=advice_notes::where('company_id',$company_id)
             ->where('event',$event_id)->count();
             
             $liveEvent=events::where('event_status',1)->first()->event_id;
             if ($invoiceCount==1 && $liveEvent==$event_id) {
                exhibitors::where('exhibitor_event',$event_id)->where('exhibitor_id',$company_id)->delete();
             }
           } catch (\Throwable $th) {
            return 0;
           }
    }

}

//

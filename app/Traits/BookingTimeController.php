<?php
namespace App\Traits;

use App\Models\advice_note\service_charges;
use App\Models\booking\booked_pavilions;
use Illuminate\Support\Facades\Auth;

trait BookingTimeController{

    public function BookingTimeControl(){
        booked_pavilions::where('userID',Auth::user()->user_uuid)
        ->where('advice_note_id',Null)->delete();
        service_charges::where('user_id',Auth::user()->user_uuid)
        ->where('advice_note_id',Null)->delete();
        
    }
}
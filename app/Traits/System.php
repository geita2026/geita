<?php
namespace App\Traits;
use App\Models\setting\DefaultSettings;

trait System{

    public function checkApplicationStatus(){
       
        $defaultSettings = DefaultSettings::first();
        if($defaultSettings->applicationStatus =="CLOSED"){
            return false;
        }
        else{
            return true;
        }
    }
}
#

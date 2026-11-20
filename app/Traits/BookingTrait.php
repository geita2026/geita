<?php

namespace App\Traits;

use App\Models\space\additional_services;
use App\Models\advice_note\service_charges;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\booking\booked_booths;

/**
 *
 */
trait BookingTrait
{

    private function AddMandatoryServices($space_size, $currency, $company_id, $event, $boothCounter)
    {
        $mandatory_services = additional_services::where('service_priolity', 'Mandatory')
            ->where('service_event', $event)->where('type_of_currency', $currency)->get(); // services must be of the same currency
        foreach ($mandatory_services as $item) {
            #********* service quantity deteminer ********#
            if ($item->relateToSpaceSize == 0) {
                if ($item->serviceName == 'Garbage collection') {
                    $service_quantity = 1;
                } else {
                    $service_quantity = 2;
                }
            } else {
                if ($boothCounter >= 4) {
                    $service_quantity = $boothCounter*2;
                } else {
                    if ($space_size < 10) {
                        $service_quantity = 2;
                    } elseif ($space_size <= 36) {
                        $service_quantity = 3;
                    } elseif ($space_size <= 50) {
                        $service_quantity = 4;
                    } elseif ($space_size <= 80) {
                        $service_quantity = 6;
                    } elseif ($space_size <= 100) {
                        $service_quantity = 8;
                    } elseif ($space_size <= 150) {
                        $service_quantity = 10;
                    } elseif ($space_size < 200) {
                        $service_quantity = 12;
                    } elseif ($space_size >= 200) {
                        $service_quantity = 16;
                    }
                }
            }



            #********* service quantity deteminer ********#
            service_charges::create([
                'service_charge_id' => Str::uuid(),
                'service_id' => $item->serviceID,
                'company_id' => $company_id,
                'user_id' => Auth::user()->user_uuid,
                'exhibition_year' => $event,
                'service_quantity' => $service_quantity,
            ]);
        }
    }
}

//

<?php
namespace App\Traits;
use App\Models\Vistors\GroundVisitor;
use App\Models\Vistors\SiteVistor;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

trait VistorsTrait{

    function Vistors($ip){
        try {
           
                $data = Location::get($ip);
            SiteVistor::create([
                'vistor_id'=>Str::uuid(),
                'vistor_ip_address'=>$ip,
                'country'=>$data->countryName,
                'city'=>$data->cityName,
                'region'=>$data->regionName,
                'timezone'=>$data->timezone,
                'coordinates'=>$data->latitude.','.$data->longitude,
                'page_visted'=>url()->current(),
            ]);
          
        } catch (\Throwable $th) {
            return redirect()->route('index');
        }
    }

    public function groundCount($countType){
        $exists=GroundVisitor::where('countDate',date('d-m-Y'))
        ->where('countType',$countType)->first();
        if ($exists) {
            $exists->update([
                'totalCount'=>$exists->totalCount+1,
            ]);
        }
        else {
            GroundVisitor::create([
                'countId'=>Str::uuid(),
                'countType'=>$countType,
                'totalCount'=>1,
                'countDate'=>date('d-m-Y'),
            ]);
        }
    }
}
#

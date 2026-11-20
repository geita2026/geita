<?php
namespace App\Traits;

use App\Models\Collections\Income;

trait CollectionSettingTraits{

    function SaveNewGfsCode($gfs_code,$income_name){

        try {
            Income::create([
                'gfs_code'=>$gfs_code,
                'income_name'=>$income_name,
            ]);
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}
#

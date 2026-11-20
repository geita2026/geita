<?php

namespace App\Imports;
use App\Models\companies;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Auth;

class CompanyImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        foreach($row as $data)
        {
            $check=companies::where('companyName',$row[0])->first();
            if(empty($row[0]))
            {
                continue;
            }

            elseif(!empty($check))
            {
                
                continue;
            }

            else
            {
                return new companies([
                    'userID'=>Auth::user()->user_uuid,
                    'companyID'=>Str::uuid(),
                    'registrationID'=>'T-'.random_int(100,999).'-'.random_int(100,999).'-'.substr(date('Y'),-3),
                    'companyName'=>$row[0],
                    'companyEmail'=>$row[1],
                    'companyPhone'=>$row[2],
                    'companyCategory'=>$row[3],
                    'companyLocation'=>$row[4],
                    'status'=>0,
                    'password'=>hash::make(12345678),
                ]);
            }
        }
    }


    public function startRow(): int
    {
        return 2;
    }
}

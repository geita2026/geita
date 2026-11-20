<?php

namespace App\Imports;
use App\Models\Printing\FacialName as PrintingFacialName;
use App\Models\setting\Country;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;

class ImportFacialNames implements ToModel,WithStartRow
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

            $check=PrintingFacialName::where('company_name',$row[0])->first();
            if(empty($row[0]))
            {
                continue; // if row does'nt have data
            }

            elseif(!empty($check))
            {

                continue; // if company exist in database.
            }

            else
            {
                return new PrintingFacialName([
                    'company_id'=>Str::uuid(),
                    'company_name'=>$row[0],
                    'group_id'=>session('group_id'),
                ]);
            }
        }
    }


    public function startRow(): int
    {
        return 2;
    }

    }

<?php

namespace App\Imports;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;


class UsersImport implements ToModel, WithStartRow
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
            $perm=DB::table('permissions')->get();
            foreach($perm as $item)
            {
                if(empty($row[0]) OR $item->name==$row[0] )
                {
                    continue;
                }
             else
               {
                return new Permission([

                    'name'=>$row[0],
                    // 'guard_name'=>$row[1],
                ]);
            }

          }


    }

    }

    public function startRow(): int
    {
        return 2;
    }
}

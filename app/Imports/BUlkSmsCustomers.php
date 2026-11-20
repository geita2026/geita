<?php

namespace App\Imports;

use App\Models\Notifications\BulkSmsReceivers as NotificationsBulkSmsReceivers;
use CreateBulkSmsReceiversTable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;
use LengthException;

class BUlkSmsCustomers implements ToModel,WithStartRow
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
            $check=NotificationsBulkSmsReceivers::where('phone_number',$row[1])->where('customer_group_name',session('customer_group_name'))
            ->first();

            if(empty($row[0]))
            {
                continue; // if row does'nt have customer name
            }
            elseif(empty($row[1]))
            {
                continue; // if row does'nt have phone number
            }
            elseif(strlen($row[1])!=12)
            {
                continue;
            }
            elseif(!empty($check))
            {

                continue; // if company exist in database.
            }



            else
            {
                return new NotificationsBulkSmsReceivers([
                    'receiver_id'=>Str::uuid(),
                    'customer_name'=>$row[0],
                    'phone_number'=>$row[1],
                    'customer_group_name'=>session('customer_group_name'),
                ]);
            }
        }
    }


    public function startRow(): int
    {
        return 2;
    }
}

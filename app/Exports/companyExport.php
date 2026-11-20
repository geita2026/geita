<?php

namespace App\Exports;

use App\Models\companies;
use App\Models\company_details;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class companyExport implements FromQuery,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return DB::table('companies')->select('companyID','companyName','companyPhone','companyEmail','companyLocation')->orderBy('companyName','ASC');
    }

    public function headings(): array
    {
        return ["CompanyID","Company_name", "company_phone","company_email","company_location"];
    }

    public function map($row): array
    {
        return
        [
            $row->companyID,
            $row->companyName,
            $row->companyPhone,
            $row->companyEmail,
            $row->companyLocation,
        ];
    }

}

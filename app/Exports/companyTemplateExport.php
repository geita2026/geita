<?php

namespace App\Exports;
use App\Models\space\company_registration_templates;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;


class companyTemplateExport implements FromQuery, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return company_registration_templates::all();
    // }

    public function query()
    {
        return company_registration_templates::get();
    }

    public function headings(): array
    {
        return ["Company_name", "company_email", "company_phone","company_category","company_location"];
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $ids = [
        '55a7245f-6f24-45e1-bfa3-c271084a641e',
        '61e8007b-05d7-4d31-9464-910ab5e8bd52',
        '6bf76eb2-9cc1-4255-86f3-4ec2cfca13e6',
        '7d857e75-fcca-424f-91e6-82a4c3b9b630',
        'd9c23786-74fc-4e6f-b31c-a356c3ae21a9',
    ];

    public function up(): void
    {
        DB::table('level_two_menuses')->whereIn('level_two_menu_id', $this->ids)->delete();
    }

    public function down(): void
    {
        DB::table('level_two_menuses')->insert([
            ['level_two_menu_id'=>'55a7245f-6f24-45e1-bfa3-c271084a641e','level_two_common_name'=>'Facial Name Companies','level_two_menu_status'=>1,'level_two_icon_name'=>'icon-vcard','level_two_menu_link'=>'facial_name_companies.index','level_two_gate_name'=>'VIEW_FACIAL_NAME_COMPANY_SUBMENU','upper_level_id'=>'d2d25103-5eb2-4a2a-9a20-23ca4654851f','level_two_menu_created_at'=>'2026-11-24 07:30:17','level_two_menu_updated_at'=>null],
            ['level_two_menu_id'=>'61e8007b-05d7-4d31-9464-910ab5e8bd52','level_two_common_name'=>'Booths','level_two_menu_status'=>1,'level_two_icon_name'=>'icon-map5','level_two_menu_link'=>'booths.index','level_two_gate_name'=>'VIEW_BOOTH_SUBMENU','upper_level_id'=>'425494bd-ee2e-4b98-a8e7-b44e35895359','level_two_menu_created_at'=>'2026-11-24 07:30:15','level_two_menu_updated_at'=>null],
            ['level_two_menu_id'=>'6bf76eb2-9cc1-4255-86f3-4ec2cfca13e6','level_two_common_name'=>'Site Visitors','level_two_menu_status'=>1,'level_two_icon_name'=>'icon-stats-growth','level_two_menu_link'=>'vistors.index','level_two_gate_name'=>'VIEW_SITE_VISITOR_SUBMENU','upper_level_id'=>'8ab88bf8-a668-4485-a162-fe10d1406ed4','level_two_menu_created_at'=>'2026-11-24 07:30:19','level_two_menu_updated_at'=>null],
            ['level_two_menu_id'=>'7d857e75-fcca-424f-91e6-82a4c3b9b630','level_two_common_name'=>'Facial Name Groups','level_two_menu_status'=>1,'level_two_icon_name'=>'icon-vcard','level_two_menu_link'=>'facial_name_groups.index','level_two_gate_name'=>'VIEW_FACIAL_NAME_GROUP_SUBMENU','upper_level_id'=>'d2d25103-5eb2-4a2a-9a20-23ca4654851f','level_two_menu_created_at'=>'2026-11-24 07:30:17','level_two_menu_updated_at'=>null],
            ['level_two_menu_id'=>'d9c23786-74fc-4e6f-b31c-a356c3ae21a9','level_two_common_name'=>'Exhibitor Judging','level_two_menu_status'=>1,'level_two_icon_name'=>'icon-calendar3','level_two_menu_link'=>'exhibitor_judging.index','level_two_gate_name'=>'VIEW_EXHIBITOR_JUDGING_SUBMENU','upper_level_id'=>'9b202026-40ba-4f66-b993-2ef93be872d2','level_two_menu_created_at'=>'2026-11-24 07:30:15','level_two_menu_updated_at'=>null],
        ]);
    }
};
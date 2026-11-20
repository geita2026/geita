<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\settings\events;
use App\Models\space\additional_services;
use App\Models\Stickers\CarSticker;
use Illuminate\Http\Request;

class BackUpIntergrationController extends Controller
{
   public function getBadges(Request $request)
   {
      $badges = CompanyMember::orderBy('member_added_at', 'DESC')->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
         ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
         ->where('backupStatus', 0)->where('badge_status',1)
         ->where('event',events::where('event_status',1)->first()->event_id)
         ->limit(1)->get();

      $totaltBadges = CompanyMember::orderBy('member_added_at', 'DESC')->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
         ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
         ->where('backupStatus', 0)->count();
      return response()->json([
         'status' => 1,
         'badges' => $badges,
         'totaltBadges' => $totaltBadges,
      ]);
   }
   # + - + - + - + - + - + - + #

   public function badgeBackup($member_id)
   {
      $exist = CompanyMember::find($member_id);
      if ($exist) {
         $exist->update([
            'backupStatus' => 1,
         ]);
         return response()->json([
            'status' => 1,
            'message' => 'Badge Backed up successfully!',
         ]);
      } else {
         return response()->json([
            'status' => 0,
            'message' => 'Badge does not exist!',
         ]);
      }
   }

   #*********** STICKETS BACKUP *************#
   public function StickersBackup(){
      try {
      $stickers=CarSticker::leftJoin('companies', 'car_stickers.company_id', '=', 'companies.companyID')
      ->where('event_id',events::where('event_status',1)->first()->event_id)
      ->where('backupStatus',0)->where('sticker_status',1)->limit(1)->get();
      $services=additional_services::get();
      
      return response()->json([
         'status'=>1,
         'stickers'=>$stickers,
         'services'=>$services,
      ]);

      } catch (\Throwable $th) {
         return response()->json([
            'status'=>0,
            'message'=>'Internal Server Error',
         ]);
      }
   }

   public function notifyStickerBackUp($sticker_id){
      $exist = CarSticker::find($sticker_id);
      if ($exist) {
         $exist->update([
            'backupStatus' => 1,
         ]);
         return response()->json([
            'status' => 1,
            'message' => 'Badge Backed up successfully!',
         ]);
      } else {
         return response()->json([
            'status' => 0,
            'message' => 'Badge does not exist!',
         ]);
      }
   }
}

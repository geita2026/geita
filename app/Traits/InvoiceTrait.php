<?php
namespace App\Traits;
use App\Models\advice_note\particulars;
use Illuminate\Support\Str;
use App\Models\advice_note\service_charges;
use App\Models\booking\booked_sponsorship_packages;
use App\Models\advice_note\advice_notes;
use App\Models\Collections\Income;
use App\Models\space\booths;
use App\Models\space\pavilions;
use Illuminate\Support\Facades\Auth;

trait InvoiceTrait{

    public function CreateParticulars($id){

       try {
        // check if contain vat
        $checking=advice_notes::where('advice_note_id',$id)->get();
        foreach ($checking as $value) { $include_vat=$value->vat; }

        $booked_pavilions = pavilions::leftJoin('booked_pavilions', 'pavilions.pavilionID', '=', 'booked_pavilions.pavilionID')
        ->leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
        ->where('advice_note_id', $id)->get();
         
         $booked_booths=booths::leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
         ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
         ->where('advice_note_id', $id)->get();
         $gfs_code = null;
         if ($booked_pavilions->isNotEmpty() || $booked_booths->isNotEmpty()) {
             $gfs_code = Income::where('is_space_fee', 1)->value('gfs_code');
             if ($gfs_code === null) {
                 return 0;
             }
         }
         //  space return booked booths details



         //inserting returned details into database particulars as particular.
         $normal=0; $foreign_conner=0; $foreign_normal=0; $raw_space=0;
         $NormalDescription=array(); $NormalForeignDescription=array(); $ConnerForeignDescription=array(); $RawDescription=array(); // data in array
         foreach ($booked_booths as $item)
         {

             if($item->category_name=='Local Normal-Booth'){
                 $normal=$normal+$item->boothSize;
                 $normal_cost=$item->category_cost;
                 $normal_currency=$item->type_of_currency;
                 array_push($NormalDescription,$item->boothNo);
                 $Ncategory=$item->category_name;
             }


             if($item->category_name=='Foreign Normal-Booth'){
                 $foreign_normal=$foreign_normal+$item->boothSize;
                 $normal_foreign_cost=$item->category_cost;
                 $normal_foreign_currency=$item->type_of_currency;
                 array_push($NormalForeignDescription,$item->boothNo);
                 $NFcategory=$item->category_name;
             }

             if($item->category_name=='Foreign Corner-Booth'){
                 $foreign_conner=$foreign_conner+$item->boothSize;
                 $conner_foreign_cost=$item->category_cost;
                 $conner_foreign_currency=$item->type_of_currency;
                 array_push($ConnerForeignDescription,$item->boothNo);
                 $CFcategory=$item->category_name;
             }

             if($item->category_name=='Raw Space'){
                $raw_space=$raw_space+$item->boothSize;
                $raw_foreign_cost=$item->category_cost;
                $raw_foreign_currency=$item->type_of_currency;
                array_push($RawDescription,$item->boothNo);
                $RFcategory=$item->category_name;
             }

             elseif($item->category_name!='Local Normal-Booth' AND $item->category_name!='Foreign Normal-Booth' AND $item->category_name!='Foreign Corner-Booth')
             {
                particulars::create([
                    'particular_id'=>Str::uuid(),
                    'advice_note_id'=>$id,
                    'particular_name'=>'Space Fee',
                    'particular_quantity'=>$item->boothSize,
                    'cost_for_one_particular'=>$item->category_cost,
                    'type_of_currency'=>$item->type_of_currency,
                    'particular_description'=>$item->boothNo,
                    'particular_category'=>$item->category_name,
                    'particular_type'=>'Space Fee',
                    'contain_vat'=>$include_vat,
                    'gfs_code'=>$gfs_code,

                ]);

             }
         }

         if($normal>0) // for all normal booths ( from local shared pavilions)
         {
             particulars::create([
                 'particular_id'=>Str::uuid(),
                 'advice_note_id'=>$id,
                 'particular_name'=>'Space Fee',
                 'particular_quantity'=>$normal,
                 'cost_for_one_particular'=>$normal_cost,
                 'type_of_currency'=>$normal_currency,
                 'particular_description'=>implode(', ',$NormalDescription), // creating sentence and insert it into database.
                 'particular_category'=>$Ncategory,
                 'particular_type'=>'Space Fee',
                 'contain_vat'=>$include_vat,
                 'gfs_code'=>$gfs_code,
             ]);
         }
         // end normal

         if($foreign_normal>0) // for all normal booths ( from local shared pavilions)
         {
             particulars::create([
                 'particular_id'=>Str::uuid(),
                 'advice_note_id'=>$id,
                 'particular_name'=>'Space Fee',
                 'particular_quantity'=>$foreign_normal,
                 'cost_for_one_particular'=>$normal_foreign_cost,
                 'type_of_currency'=>$normal_foreign_currency,
                 'particular_description'=>implode(', ',$NormalForeignDescription), // creating sentence and insert it into database.
                 'particular_category'=>$NFcategory,
                 'particular_type'=>'Space Fee',
                 'contain_vat'=>$include_vat,
                 'gfs_code'=>$gfs_code,
             ]);
         }

         if($foreign_conner>0) // for all normal booths ( from local shared pavilions)
         {
             particulars::create([
                 'particular_id'=>Str::uuid(),
                 'advice_note_id'=>$id,
                 'particular_name'=>'Space Fee',
                 'particular_quantity'=>$foreign_conner,
                 'cost_for_one_particular'=>$conner_foreign_cost,
                 'type_of_currency'=>$conner_foreign_currency,
                 'particular_description'=>implode(', ',$ConnerForeignDescription), // creating sentence and insert it into database.
                 'particular_category'=>$CFcategory,
                 'particular_type'=>'Space Fee',
                 'contain_vat'=>$include_vat,
                 'gfs_code'=>$gfs_code,
             ]);
         }

         if($raw_space>0)
         {
            particulars::create([
                'particular_id'=>Str::uuid(),
                'advice_note_id'=>$id,
                'particular_name'=>'Space Fee',
                'particular_quantity'=>$raw_space,
                'cost_for_one_particular'=>$raw_foreign_cost,
                'type_of_currency'=>$raw_foreign_currency,
                'particular_description'=>implode(', ',$RawDescription), // creating sentence and insert it into database.
                'particular_category'=>$RFcategory,
                'particular_type'=>'Space Fee',
                'contain_vat'=>$include_vat,
                'gfs_code'=>$gfs_code,
            ]);
         }

         foreach ($booked_pavilions as $value) {
            particulars::create([
                'particular_id'=>Str::uuid(),
                'advice_note_id'=>$id,
                'particular_name'=>'Space Fee',
                'particular_quantity'=>$value->pavilion_size,
                'cost_for_one_particular'=>$value->cost_one_msq,
                'type_of_currency'=>$value->type_of_currency,
                'particular_description'=>$value->pavilionName,
                'particular_category'=>$value->category_name,
                'particular_type'=>'Space Fee',
                'contain_vat'=>$include_vat,
                'gfs_code'=>$gfs_code,
            ]);
         }


         //services offered / booked
         $services=service_charges::leftJoin('additional_services','service_charges.service_id','=','additional_services.serviceID')
         ->where('advice_note_id',$id)->get(); //return booked services in service charge table


         #adding service particulars to database one after another.
         foreach ($services as $item) {
             particulars::create([
             'particular_id'=>Str::uuid(),
             'advice_note_id'=>$id,
             'particular_name'=>$item->serviceName,
             'particular_quantity'=>$item->service_quantity,
             'cost_for_one_particular'=>$item->serviceCost,
             'type_of_currency'=>$item->type_of_currency,
             'particular_category'=>'Additional Service',
             'particular_type'=>'Additional Service Fee',
             'contain_vat'=>$include_vat,
             'gfs_code'=>$item->gfs_code,
             ]);
         }

         // end services


         // for sponsorships
         $package=booked_sponsorship_packages::leftJoin('advice_notes','booked_sponsorship_packages.advice_note_id','=','advice_notes.advice_note_id')
         ->leftJoin('sponsorship_packages','booked_sponsorship_packages.package_id','=','sponsorship_packages.package_id')
         ->where('booked_sponsorship_packages.advice_note_id',$id)->get();

         foreach ($package as $value) {
               particulars::create([
                 'particular_id'=>Str::uuid(),
                 'advice_note_id'=>$id,
                 'particular_name'=>$value->package_name,
                 'particular_quantity'=>1,
                 'cost_for_one_particular'=>$value->customer_amount,
                 'type_of_currency'=>$value->type_of_currency,
                 'particular_category'=>'Sponsorship Fee',
                 'particular_type'=>'Sponsorship Fee',
                 'contain_vat'=>$include_vat,
                 'gfs_code'=>$value->gfs_code,
                 ]);
         }
         // end for sponsorships

         $adviceNote=advice_notes::find($id);//return advice note
         $updated=$adviceNote->update([
             'advice_note_status'=>'UNDER_REVIEW',// allow advice note to be seen by accountant.
             'user_id'=>Auth::user()->user_uuid,
         ]);
         return 1;
       } catch (\Throwable $th) {
        return 0;
       }
    }



    public function SetDeadline($id){
        //dea
        $data=advice_notes::leftJoin('events','advice_notes.event','=','events.event_id')->where('advice_note_id',$id)->get();
        foreach ($data as $value) {
            // deadline calculation will be here
        }
        $deadline='2023-03-31';

        advice_notes::find($id)->update(['advice_note_deadline'=>$deadline, 'advice_note_status'=>'UNDER_REVIEW']);

    }




}

//

<?php

namespace App\Http\Controllers\messaging;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\companies;
use App\Models\company_details;
use App\Models\exhibition\exhibitors;
use App\Models\Notifications\BulkSms;
use App\Models\Notifications\BulkSmsReceivers;
use App\Models\Notifications\CustomerGroups;
use App\Traits\NotificationTrait;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class bulkSmsController extends Controller
{
    use NotificationTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if($request->user()->cannot('VIEW_BULK_SMS_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_BULK_SMS_SUBMENU'));
        }

        $draft_messages=BulkSms::where('sms_status',0)->get(); //return draft messages

        $sent_messages=BulkSms::where('sms_status',1)->get();// return sent msgs

        $groups=CustomerGroups::get(['group_id','group_name']); // return all customer names
        $individuals=BulkSmsReceivers::get(['receiver_id','customer_name']); // return all customers in bulk sms castodian
        return view('Messaging.bulk_sms',compact('groups','draft_messages','sent_messages','individuals'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('CREATE_BULK_SMS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_BULK_SMS'));
        }
        try {
        $sms_content=$request->sms_body;
        $sent_on=$request->sms_sent_on;

        #--------INDIVIDUAL SMS -----#
        if ($request->group_name=='Single') {
            $phone_number=$request->phone_number;
            $customer_name=$request->customer_name;
            $this->SaveSendSmsJobs($phone_number,$sms_content,$sent_on,$customer_name);
            return back()->with('success','Message will be sent as per date set!');
        }
        #------END INDIVIDUAL SMS -----#


        #--------CURRENT EXHIBITORS SMS -----#
        if($request->group_name=='CurrentExhibitors')
        {
            $exhibitors=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
            ->where('exhibitor_event',session('liveEvent'))
            ->get();

            foreach ($exhibitors as $key => $value)
            {
                if(Str::length($value->companyPhone)==12){
                $phone_number=$value->companyPhone;
                $customer_name=$value->companyName;
                $this->SaveSendSmsJobs($phone_number,$sms_content,$sent_on,$customer_name);
                }
            }
        }
        #--------END CURRENT EXHIBITORS SMS -----#


        #--------CURRENT EXHIBITORS SMS -----#
        if($request->group_name=='AllExhibitors')
        {  
         $exhibitors=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
            ->where('companyPhone','!=',Null)
            ->get();
            foreach ($exhibitors as $key => $value)
            {
                if(!empty($value->companyPhone)){
                $phone_number=$value->companyPhone;
                $customer_name=$value->companyName;
                $this->SaveSendSmsJobs($phone_number,$sms_content,$sent_on,$customer_name);
                }

            }
        }
        #--------END CURRENT EXHIBITORS SMS -----#


        if($request->group_name=='All Customers')
        {
           $customers=BulkSmsReceivers::get();
           foreach ($customers as $key) {
            $phone_number=$key->phone_number;
            $customer_name=$key->customer_name;
            $this->SaveSendSmsJobs($phone_number,$sms_content,$sent_on,$customer_name); // saving messages to SMSJobTable
        }
        }
        else
        {
            $customers=BulkSmsReceivers::where('customer_group_name',$request->group_name)->get();
            foreach ($customers as $key) {
                $phone_number=$key->phone_number;
                $customer_name=$key->customer_name;
                $this->SaveSendSmsJobs($phone_number,$sms_content,$sent_on,$customer_name); // saving messages to SMSJobTable
            }
        }
        return back()->with('success','messages set successfully, System will send them as time to be sent reaches');
      } catch (\Throwable $th) {
        //throw $th;
        return back()->with('fail','Something went wrong!');
      }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->user()->cannot('UPDATE_BULK_SMS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_BULK_SMS'));
        }
        $request->validate([
            'companyPhone'=>'required|starts_with:255|digits:12',
            'personalPhone'=>'required|starts_with:255|digits:12'
        ]);

        try {
            $company=companies::find($id);
        $company->update([
            'companyPhone'=>$request->companyPhone,
        ]);

        $company_details=company_details::get()->where('companyID',$id);
        foreach ($company_details as $key) {
           $detail=company_details::find($key->companyDetailID);
           $detail->update([
            'personalPhone'=>$request->personalPhone,
           ]);
        }
        return back()->with('success','Phone Number updated successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, please try again after some time.');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('DELETE_BULK_SMS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_BULK_SMS'));
        }

        try {
            $message=BulkSms::where('sms_status',$request->source)->get();
            foreach ($message as $key => $value) {
                $id=$value->sms_id;
                BulkSms::find($id)->delete();
            }
            return back()->with('success','Message deleted successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, please try again after some time.');
        }

    }

}

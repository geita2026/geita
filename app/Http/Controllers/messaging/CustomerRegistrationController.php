<?php

namespace App\Http\Controllers\messaging;

use App\Http\Controllers\Controller;
use App\Imports\BUlkSmsCustomers;
use App\Models\Notifications\BulkSmsReceivers;
use App\Models\Notifications\CustomerGroups;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerRegistrationController extends Controller
{
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
        if($request->user()->cannot('View Sms Customer',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        if($request->group_name)
        {
            $group_id=$request->group_name;
        }
        else
        {
            $group_id= session('customer_group_name');
        }
        $receivers=BulkSmsReceivers::leftJoin('customer_groups','bulk_sms_receivers.customer_group_name','customer_groups.group_id')
        ->where('customer_group_name',$group_id)->get();
        $groups=CustomerGroups::get();
        return view('Messaging.registrations',compact('receivers','groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Sms Customer',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
            $request->validate([
                'file'=>'file',
                'customer_group'=>'required'
            ]);
            session(['customer_group_name'=>$request->customer_group]);
            // return session('customer_group_name');
            // return session('group_id');
            Excel::import(new BUlkSmsCustomers,
            $request->file('file')->store('files'));
            return back()->with('success','Customers uploaded successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong! ERROR: '.$th->getMessage());
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
        if($request->user()->cannot('Update Sms Customer',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $request->validate([
            'customer_name'=>'required',
            'phone_number'=>'digits:12',
        ]);

       try {
        $customer=BulkSmsReceivers::find($id);
        $customer->update([
            'customer_name'=>$request->customer_name,
            'phone_number'=>$request->phone_number,
        ]);
        return back()->with('success',$request->customer_name.' Updated Successfully!');
       } catch (\Throwable $th) {
        //throw $th;
        return back()->with('fail','Somthing went wrong, ERROR: '.$th->getMessage());
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
        if($request->user()->cannot('Delete Sms Customer',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
            $customer=BulkSmsReceivers::find($id);
            $customer->delete();
            return back()->with('success','Data Deleted Successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Failed to delete data!');
        }
    }
}

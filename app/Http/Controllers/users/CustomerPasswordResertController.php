<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Models\settings\events;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerPasswordResertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('Resert Customer Password',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
    $event=events::where('event_status',1)->first();
    $users=User::orderBy('first_name','ASC')->leftJoin('roles','users.role','=','roles.id')
    ->leftJoin('exhibitors','users.user_uuid','=','exhibitors.exhibitor_id')->where('exhibitor_event',$event->event_id)
    ->where('roles.name', 'Customer')->where('roles.guard_name', 'web')->get(['users.id','first_name','middle_name','last_name','name','email','user_uuid','user_ip_address','role',
    'phone_number','account_status']);
    return view('users.CustomerResertPassword',compact('users'));

    }


}

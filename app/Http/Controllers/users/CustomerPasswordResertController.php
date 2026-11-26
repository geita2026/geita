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
        if($request->user()->cannot('RESET_CUSTOMER_PASSWORD_RESET',User::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('RESET_CUSTOMER_PASSWORD_RESET'));
        }
        $event = events::where('event_status', 1)->first();
        $users = User::orderBy('users.first_name')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->leftJoin('exhibitors', 'users.user_uuid', '=', 'exhibitors.exhibitor_id')
            ->where('exhibitors.exhibitor_event', $event->event_id)
            ->where('roles.name', 'Customer')
            ->where('roles.guard_name', 'web')
            ->get([
                'users.id', 'users.first_name', 'users.middle_name', 'users.last_name',
                'users.name', 'users.email', 'users.user_uuid', 'users.user_ip_address',
                'users.role', 'users.phone_number', 'users.account_status',
            ]);

        return view('users.CustomerResertPassword', compact('users'));

    }


}

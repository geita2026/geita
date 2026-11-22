<?php

namespace App\Http\Controllers\controls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Google\Service\ServiceControl\AuditLog;
use Spatie\Permission\Models\Permission;

class userAuditController extends Controller
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
        //
        if($request->user()->cannot('VIEW_AUDIT_SUBMENU',Permission::class))
        {
           return back()->with('fail',$this->permissionDeniedMessage('VIEW_AUDIT_SUBMENU'));
        }
        $data=DB::table('audits')->orderBy('created_at','DESC')->leftJoin('users','audits.user_id','=','users.id')
        ->paginate(10,['first_name','middle_name','last_name','audits.id','auditable_type','audits.created_at','event']);

        return view('audit.user-audit',compact('data'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('VIEW_AUDIT_SUBMENU',Permission::class))
        {
           return back()->with('fail',$this->permissionDeniedMessage('VIEW_AUDIT_SUBMENU'));
        }


        $data=DB::table('audits')->leftJoin('users','audits.user_id','=','users.id')->where('audits.id',$id)->get();

        return view('audit.user-audit-more',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if($request->user()->cannot('VIEW_AUDIT_SUBMENU',Permission::class))
        {
           return back()->with('fail',$this->permissionDeniedMessage('VIEW_AUDIT_SUBMENU'));
        }
        //


        $request->validate([
            'user_name'=>'required'
        ]);

        $data=User::leftJoin('audits','users.id','=','audits.user_id')->orderBy('created_at','DESC')
        ->limit(1000)->orderBy('audits.created_at','DESC')
        ->where('users.id',$request->user_name)->get();

        $users=User::get();
        //  return $data;

        return view('audit.individual_logs',compact('data','users'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($request->user()->cannot('DELETE_AUDIT',Permission::class))
        {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_AUDIT'));
        }
        try {
            DB::delete("delete from audits where id ='$id'");
            return back()->with('success','User activity deleted Successfully');
        }
        catch (\Throwable $th)
        {
            return back()->with('fail','Something went wrong!');
        }
    }
}


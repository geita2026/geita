<?php

namespace App\Http\Controllers\information;

use App\Http\Controllers\Controller;
use App\Models\Information\FeedBack;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class helpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('View FeedBack',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $account_type=Auth::user()->account_type;

    //    Internal Users
    if ($account_type==1) {
        $feedback=FeedBack::leftJoin('users','feed_backs.asked_by','=','users.user_uuid')->get();
        $faq=FeedBack::where('is_faq',1)->get();
        return view('information.help-feedback',compact('feedback','faq'));
        }
    // internal users

    // external users
        elseif ($account_type==2) {
            $feedback=FeedBack::leftJoin('users','feed_backs.attended_by','=','users.user_uuid')
        ->where('asked_by',Auth::user()->user_uuid)->get();
        $faq=FeedBack::where('is_faq',1)->get();
        return view('information.help-feedback',compact('feedback','faq'));
        }
    // external users

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create FeedBack',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $request->validate([
            'customer_comment'=>'required'
        ]);

       try {
        FeedBack::create([
            'feedback_id'=>Str::uuid(),
            'asked_by'=>Auth::user()->user_uuid,
            'customer_comment'=>$request->customer_comment,
        ]);
        return back()->with('success','Your feedback submitted successfully!');
       } catch (\Throwable $th) {
        return back()->with('fail','Somthing went wrong!');
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
        if($request->user()->cannot('Update FeedBack',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

      try {
        FeedBack::find($id)->update([
            'answer'=>$request->answer,
            'attended_by'=>Auth::user()->user_uuid,
            'is_faq'=>$request->is_faq,
          ]);
          return back()->with('success','Comment replied Successfully!');
      } catch (\Throwable $th) {
        return back()->with('fail','Something went wrong!');
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
        if($request->user()->cannot('Delete FeedBack',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        try {
            FeedBack::find($id)->delete();
            return back()->with('success','Comment removed successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong');
        }
    }
}

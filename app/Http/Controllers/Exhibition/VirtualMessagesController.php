<?php

namespace App\Http\Controllers\Exhibition;
use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\exhibition\VirtualMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VirtualMessagesController extends Controller
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
        if($request->user()->cannot('VIEW_VIRTUAL_MESSAGES',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_VIRTUAL_MESSAGES'));
        }

        $messages=VirtualMessages::orderBy('message_sent_on','DESC')->where('message_from',Auth::user()->user_uuid)->get();

        $companies=companies::get();

        return view('Exhibition.Messaging.virtualMessages',compact('messages','companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('SEND_VIRTUAL_MESSAGES',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('SEND_VIRTUAL_MESSAGES'));
        }

        try {
            $message_to=session('message_to');
            $message_from=Auth::user()->user_uuid;
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
                return response()->json(['status'=>1,'success'=>'Message sent']);
            }

        } catch (\Throwable $th) {
            return response()->json(['status'=>0,'fail'=>'Something went wrong!'.$th->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\exhibition\VirtualMessages  $virtualMessages
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('VIEW_VIRTUAL_MESSAGE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_VIRTUAL_MESSAGE'));
        }
       try {
        session(['message_to'=>$id]);
        $messages=VirtualMessages::orderBy('message_sent_on','DESC')->where('message_to',$id)->get();
        $company=companies::where('companyID',session('message_to'))->get();
        $companies=companies::get();

        return view('Exhibition.Messaging.virtualMessages',compact('messages','companies','company'));
       } catch (\Throwable $th) {
        return back()->with('success','Something went wrong!');
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\exhibition\VirtualMessages  $virtualMessages
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request ,$id)
    {
        if($request->user()->cannot('DELETE_VIRTUAL_MESSAGES',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_VIRTUAL_MESSAGES'));
        }
       try
       {

       $message=VirtualMessages::find($id);
       $message->message_file;
       File::delete(public_path('VirtualImages/').$message->message_file); // delete file existing
       $message->delete();
        return back()->with('success','Message deleted successfully!');
       } catch (\Throwable $th) {
        return back()->with('fail','Something went wrong!');
       }
    }
}

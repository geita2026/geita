<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\setting\ApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RegisterApiRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->user()->cannot('View All Api Requests', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $api_requests = ApiRequest::get();
        return view('settings.api_requests', compact('api_requests'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create Api Requests', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {
            $token = str::random(80);
            $validator = Validator::make($request->all(), [
                'request_name' => 'required|unique:api_requests,request_name',
                'client_email' => 'required|unique:api_requests,client_email'
            ]);

            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            ApiRequest::create([
                'client_id' =>$request->clientId ?? Str::uuid(),
                'request_name' => $request->request_name,
                'client_email' => $request->client_email,
                'access_token' => $token,
            ]);

            #*********** SEND MAIL TO USER ***************#
            $data = [
                'serviceProvider' => $request->request_name,
                'access_token' => $token,
                'client_email' => $request->client_email,
                'subject' => 'SABASABA ONLINE TICKET APIs'
            ];
            // Mail::send('mails.requestRegistration', $data, function ($message) use ($data) {
            //     $message->to(
            //         $data['client_email'],
            //         $data['serviceProvider']
            //     )
            //         ->priority(1)
            //         ->subject(
            //             $data['subject']
            //         );
            // });

            return back()->with('success', 'API resquest registered successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
        if ($request->user()->cannot('Update Api Requests', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        try {
            $token = str::random(40);
            ApiRequest::find($id)->update([
                'request_name' => $request->request_name,
                'client_email' => $request->client_email,
                'access_token' => $token,
            ]);

            return back()->with('success', 'Request Apdated successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Delete Api Requests', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        try {
            ApiRequest::find($id)->delete();
            return back()->with('success', 'Request deleted successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!');
        }
    }
}
